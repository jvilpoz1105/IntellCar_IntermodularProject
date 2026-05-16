<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Aws\Comprehend\ComprehendClient;

class ComprehendControl extends Controller
{
    private $comprehendClient = null;

    private function initAwsClient()
    {
        if ($this->comprehendClient) return;

        $key = config('services.aws.key');
        $secret = config('services.aws.secret');
        $token = config('services.aws.token');
        $region = config('services.aws.region', 'us-east-1');

        if (!$key || !$secret) {
            throw new \Exception("Faltan las credenciales de AWS. (Key: " . ($key ? 'OK' : 'MISSING') . ", Secret: " . ($secret ? 'OK' : 'MISSING') . ")");
        }

        $credentials = [
            'key'    => $key,
            'secret' => $secret,
        ];

        if ($token) {
            $credentials['token'] = $token;
        }

        $this->comprehendClient = new ComprehendClient([
            'version' => 'latest',
            'region' => $region,
            'credentials' => $credentials,
        ]);
    }

    public function analyzeText(Request $request): JsonResponse
    {
        try {
            $this->initAwsClient();

            $request->validate([
                'text' => 'required|string|max:4800',
            ]);

            $text = $request->input('text');
            $languageCode = 'es'; // default

            // 1. Detección de Idioma
            $languageResult = $this->comprehendClient->detectDominantLanguage([
                'Text' => $text,
            ]);

            $languages = $languageResult['Languages'] ?? [];
            $warningMessage = null;

            if (!empty($languages)) {
                $dominantLanguage = $languages[0]['LanguageCode'];
                if ($dominantLanguage !== 'es' && $languages[0]['Score'] > 0.8) {
                    $warningMessage = "⚠️ El idioma principal parece ser diferente al español. Considera traducirlo para mayor alcance.";
                    $languageCode = in_array($dominantLanguage, ['en', 'es', 'fr', 'de', 'it', 'pt']) ? $dominantLanguage : 'es';
                }
            }

            // 2. Detección de PII (Personally Identifiable Information)
            try {
                $piiResult = $this->comprehendClient->detectPiiEntities([
                    'Text' => $text,
                    'LanguageCode' => $languageCode,
                ]);

                $piiEntities = $piiResult['Entities'] ?? [];
                foreach ($piiEntities as $entity) {
                    if (in_array($entity['Type'], ['EMAIL', 'PHONE', 'ADDRESS']) && $entity['Score'] > 0.8) {
                        return response()->json([
                            'is_valid' => false,
                            'error' => "🚨 Por tu seguridad y privacidad, no incluyas datos personales (" . strtolower($entity['Type']) . ").",
                            'status' => 'error'
                        ], 422);
                    }
                }
            } catch (\Throwable $e) {
                // Fallback si detectPiiEntities no está disponible o falla por idioma
                $entitiesResult = $this->comprehendClient->detectEntities([
                    'Text' => $text,
                    'LanguageCode' => $languageCode,
                ]);
                $entities = $entitiesResult['Entities'] ?? [];
                // Intentar buscar emails/teléfonos manualmente con regex básico como fallback si falla la API
                if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $text) || preg_match('/(\+34|0034|34)?[ -]*(6|7)[ -]*([0-9][ -]*){8}/', $text)) {
                    return response()->json([
                        'is_valid' => false,
                        'error' => "🚨 Se han detectado posibles datos de contacto en el texto. Por favor, elimínalos.",
                        'status' => 'error'
                    ], 422);
                }
            }

            // 3. Detección de Toxicidad (Insultos, amenazas)
            try {
                $toxicResult = $this->comprehendClient->detectToxicContent([
                    'LanguageCode' => $languageCode,
                    'TextSegments' => [
                        ['Text' => $text]
                    ]
                ]);

                $toxicity = $toxicResult['ResultList'][0]['Labels'] ?? [];
                foreach ($toxicity as $label) {
                    if (in_array($label['Name'], ['TOXICITY', 'INSULT', 'PROFANITY', 'THREAT', 'HATE_SPEECH']) && $label['Score'] > 0.6) {
                        return response()->json([
                            'is_valid' => false,
                            'error' => "🚨 Lenguaje no permitido: Se ha detectado contenido agresivo, insultos o amenazas.",
                            'status' => 'error'
                        ], 422);
                    }
                }
            } catch (\Throwable $e) {
                // Fallback a Sentiment Analysis si DetectToxicContent no está soportado en esta versión del SDK o región
                $sentimentResult = $this->comprehendClient->detectSentiment([
                    'Text' => $text,
                    'LanguageCode' => $languageCode,
                ]);

                if ($sentimentResult['Sentiment'] === 'NEGATIVE') {
                    // Verificamos el score para no bloquear por cualquier cosa negativa
                    $negativeScore = $sentimentResult['SentimentScore']['Negative'] ?? 0;
                    if ($negativeScore > 0.95) {
                        return response()->json([
                            'is_valid' => false,
                            'error' => "🚨 Tu texto parece ser fuertemente negativo u hostil. Por favor, mantén un tono respetuoso.",
                            'status' => 'error'
                        ], 422);
                    }
                }
            }

            // 4. Sentiment Analysis (Para sugerencias)
            $sentimentResult = $this->comprehendClient->detectSentiment([
                'Text' => $text,
                'LanguageCode' => $languageCode,
            ]);

            $sentiment = $sentimentResult['Sentiment'];
            if ($sentiment === 'NEGATIVE' && !$warningMessage) {
                $warningMessage = "⚠️ Tu texto tiene un tono negativo. Considera reescribirlo para resultar más atractivo y positivo.";
            }

            return response()->json([
                'is_valid' => true,
                'status' => $warningMessage ? 'warning' : 'success',
                'warning' => $warningMessage
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'FALLO_COMPREHEND',
                'mensaje_ia' => $e->getMessage(),
            ], 500);
        }
    }
}
