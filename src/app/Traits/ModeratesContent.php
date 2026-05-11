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
                Log::error("Error al validar moderación para {$key}: " . $e->getMessage());
                // Si falla la conexión con AWS, por seguridad podríamos ser estrictos o permisivos.
                // Aquí dejaremos que pase o bloquearemos. Bloquearemos por seguridad.
                return false;
            }
        }

        return true;
    }
}
