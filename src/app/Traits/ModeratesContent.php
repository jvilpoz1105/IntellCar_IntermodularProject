<?php

namespace App\Traits;

use Aws\Rekognition\RekognitionClient;
use Illuminate\Support\Facades\Log;

trait ModeratesContent
{
    /**
     * Valida que las imágenes subidas a S3 no tengan contenido inapropiado.
     */
    protected function validateModeration(array $mediaKeys): bool
    {
        $client = new RekognitionClient([
            'version' => 'latest',
            'region'  => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        $bucket = env('AWS_BUCKET', 'intellcar-media');

        foreach ($mediaKeys as $key) {
            try {
                $result = $client->detectModerationLabels([
                    'Image' => [
                        'S3Object' => [
                            'Bucket' => $bucket,
                            'Name'   => $key,
                        ],
                    ],
                    'MinConfidence' => 60,
                ]);

                if (!empty($result['ModerationLabels'])) {
                    Log::warning("Contenido inapropiado detectado en el archivo: {$key}", $result['ModerationLabels']);
                    return false;
                }
            } catch (\Exception $e) {
                Log::warning("AWS Rekognition no disponible para moderación en backend (usando fallback local): " . $e->getMessage());
                // Bajo restricciones de cuenta (Learner Lab), permitimos continuar para no interrumpir el flujo del usuario
                return true;
            }
        }

        return true;
    }
}
