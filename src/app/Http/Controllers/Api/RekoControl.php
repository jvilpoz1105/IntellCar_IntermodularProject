<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Make;
use App\Models\CarModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Aws\S3\S3Client;
use Aws\Rekognition\RekognitionClient;

class RekoControl extends Controller
{
    private $s3Client = null;
    private $rekognitionClient = null;
    private $bucket = null;

    private const REQUIRED_VEHICLE_LABELS = ['Car', 'Vehicle', 'Automobile', 'Motor Vehicle', 'Sports Car', 'Sedan', 'SUV', 'Truck', 'Van'];

    /**
     * Inicializa los clientes de AWS bajo demanda dentro de un try-catch
     */
    private function initAwsClients()
    {
        // Si ambos ya están inicializados, no hacemos nada
        if ($this->s3Client && $this->rekognitionClient) return;

        $this->bucket = config('services.aws.bucket', 'intellcar-media-tfg-jose');

        $options = [
            'version' => 'latest',
            'region' => config('services.aws.region'),
        ];

        // Solo usar credenciales explícitas si existen (ej. en desarrollo local).
        // En EC2 (Learner Lab), el SDK usará automáticamente el IAM Role (LabRole).
        $key = config('services.aws.key');
        $secret = config('services.aws.secret');
        
        if ($key && $secret) {
            $options['credentials'] = [
                'key'    => $key,
                'secret' => $secret,
            ];
            if ($token = config('services.aws.token')) {
                $options['credentials']['token'] = $token;
            }
        }

        // Inicializamos S3
        $this->s3Client = new S3Client($options);

        // Inicializamos Rekognition
        $this->rekognitionClient = new RekognitionClient($options);
    }

    public function presigned(Request $request): JsonResponse
    {
        try {
            $this->initAwsClients();

            $request->validate([
                'filename' => 'required|string|max:255',
                'content_type' => 'required|string|max:100',
            ]);

            $filename = $request->input('filename');
            $contentType = $request->input('content_type');

            $key = 'uploads/' . date('Y/m/d') . '/' . uniqid() . '-' . $filename;

            $command = $this->s3Client->getCommand('PutObject', [
                'Bucket'      => $this->bucket,
                'Key'         => $key,
                'ContentType' => $contentType,
            ]);

            $presignedRequest = $this->s3Client->createPresignedRequest($command, '+20 minutes');

            return response()->json([
                'upload_url' => (string) $presignedRequest->getUri(),
                'key'        => $key,
                'expires'    => now()->addMinutes(20)->toIso8601String(),
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'FALLO_DETECTADO_VERSION_6',
                'mensaje_ia' => $e->getMessage(),
                'ayuda' => 'Si ves esto, el código nuevo SÍ ha llegado al servidor.'
            ], 500);
        }
    }

    public function internalVerify(Request $request): JsonResponse
    {
        $internalToken = env('INTERNAL_API_TOKEN', 'intellcar-internal-token');
        $token = $request->header('X-Internal-Token');

        if ($token !== $internalToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $this->initAwsClients();
        // ... resto del código ...

        $request->validate([
            'key' => 'required|string',
        ]);

        $key = $request->input('key');

        try {
            $this->s3Client->headObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
            ]);

            return response()->json([
                'status' => 'verified',
                'key' => $key,
            ]);
        } catch (\Aws\S3Exception\S3Exception $e) {
            return response()->json([
                'error' => 'File not found in S3',
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function analyze(Request $request): JsonResponse
    {
        $request->validate([
            'key' => 'required|string',
        ]);

        $key = $request->input('key');

        try {
            $this->initAwsClients();
            
            $s3Uri = "s3://{$this->bucket}/{$key}";

            $moderationResult = $this->rekognitionClient->detectModerationLabels([
                'Image' => ['S3Object' => ['Bucket' => $this->bucket, 'Name' => $key]],
                'MinConfidence' => 50,
            ]);

            if (!empty($moderationResult['ModerationLabels'])) {
                return response()->json([
                    'error' => 'Content not allowed',
                    'details' => $moderationResult['ModerationLabels'],
                ], 422);
            }

            // 3. Detectar Etiquetas (Objetos, Marcas, Colores)
            $labelsResult = $this->rekognitionClient->detectLabels([
                'Image' => ['S3Object' => ['Bucket' => $this->bucket, 'Name' => $key]],
                'MaxLabels' => 50,
                'MinConfidence' => 65, // Bajamos un poco para captar más detalles
            ]);

            // 4. NUEVO: Detectar Texto (Para insignias, logos y modelos escritos)
            $textResult = $this->rekognitionClient->detectText([
                'Image' => ['S3Object' => ['Bucket' => $this->bucket, 'Name' => $key]],
            ]);

            $labels = $labelsResult['Labels'] ?? [];
            $texts = $textResult['TextDetections'] ?? [];
            
            $vehicleLabels = [];
            $colorLabels = [];
            $brandLabels = [];
            $otherLabels = [];
            $detectedTexts = [];

            // Procesar Texto Detectado
            foreach ($texts as $text) {
                if ($text['Type'] === 'WORD' && $text['Confidence'] > 80) {
                    $detectedTexts[] = $text['DetectedText'];
                }
            }

            foreach ($labels as $label) {
                $name = strtolower($label['Name']);

                if (in_array($label['Name'], self::REQUIRED_VEHICLE_LABELS)) {
                    $vehicleLabels[] = [
                        'name' => $label['Name'],
                        'confidence' => $label['Confidence'],
                    ];
                    continue;
                }

                $colorPatterns = ['red', 'blue', 'black', 'white', 'silver', 'grey', 'gray', 'green', 'yellow', 'orange', 'brown', 'gold', 'beige'];
                if (in_array($name, $colorPatterns)) {
                    $colorLabels[] = [
                        'name' => ucfirst($name),
                        'confidence' => $label['Confidence'],
                    ];
                    continue;
                }

                // Si la IA detecta una marca como etiqueta directa
                $brandLabels[] = [
                    'name' => $label['Name'],
                    'confidence' => $label['Confidence'],
                ];
            }

            if (empty($vehicleLabels)) {
                return response()->json([
                    'error' => 'No se detectó un vehículo válido en la imagen',
                    'labels_found' => array_column($labels, 'Name')
                ], 422);
            }

            $detectedMake = null;
            $detectedModel = null;

            // Intentar emparejar marca con la base de datos
            if (!empty($brandLabels)) {
                $brandName = $brandLabels[0]['name'];
                $make = Make::whereRaw('LOWER(make_name) = ?', [strtolower($brandName)])->first();

                if ($make) {
                    $detectedMake = [
                        'id' => $make->make_id,
                        'name' => $make->make_name,
                        'confidence' => $brandLabels[0]['confidence'],
                    ];

                    $modelMatches = $this->matchModelsFromLabels($labels, $make);
                    if (!empty($modelMatches)) {
                        $detectedModel = [
                            'id' => $modelMatches[0]['id'],
                            'name' => $modelMatches[0]['name'],
                            'confidence' => $modelMatches[0]['confidence'],
                        ];
                    }
                }
            }

            return response()->json([
                'vehicle_detected' => true,
                'vehicle_type' => $vehicleLabels[0]['name'] ?? 'Car',
                'color' => !empty($colorLabels) ? $colorLabels[0] : null,
                'make' => $detectedMake,
                'model' => $detectedModel,
                'detected_text' => $detectedTexts, // ¡Aquí saldrá el texto de las insignias!
                'all_labels' => array_slice(array_map(fn($l) => ['name' => $l['Name'], 'confidence' => $l['Confidence']], $labels), 0, 15),
            ]);

        } catch (\Throwable $e) {
            // Loguear el fallo de AWS Rekognition
            \Log::warning("AWS Rekognition no está disponible (usando fallback local): " . $e->getMessage());

            // --- FALLBACK LOCAL ---
            // Aprobamos la imagen automáticamente como si fuera un vehículo genérico
            return response()->json([
                'vehicle_detected' => true,
                'vehicle_type' => 'Car',
                'color' => ['name' => 'Gris', 'confidence' => 90],
                'make' => null,
                'model' => null,
                'detected_text' => [],
                'all_labels' => [
                    ['name' => 'Car', 'confidence' => 99],
                    ['name' => 'Vehicle', 'confidence' => 99]
                ],
                'warning' => '⚠️ (Modo local de imagen) El análisis de imagen por IA no está disponible, pero tu archivo ha sido pre-aprobado.'
            ]);
        }
    }

    private function matchModelsFromLabels(array $labels, Make $make): array
    {
        $labelNames = array_map(fn($l) => strtolower($l['Name']), $labels);

        $models = CarModel::where('make_id', $make->make_id)->get();

        $matches = [];
        foreach ($models as $model) {
            $modelNameLower = strtolower($model->model_name);
            foreach ($labelNames as $labelName) {
                if (str_contains($modelNameLower, $labelName) || str_contains($labelName, $modelNameLower)) {
                    $matches[] = [
                        'id' => $model->model_id,
                        'name' => $model->model_name,
                        'confidence' => 85,
                    ];
                    break;
                }
            }
        }

        return $matches;
    }
}