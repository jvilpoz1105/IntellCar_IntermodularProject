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

        $key = config('services.aws.key');
        $secret = config('services.aws.secret');
        $token = config('services.aws.token');
        $region = config('services.aws.region', 'us-east-1');
        $this->bucket = config('services.aws.bucket', 'intellcar-media-tfg-jose');

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

        // Inicializamos S3
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => $region,
            'credentials' => $credentials,
        ]);

        // Inicializamos Rekognition
        $this->rekognitionClient = new RekognitionClient([
            'version' => 'latest',
            'region' => $region,
            'credentials' => $credentials,
        ]);
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

        $labelsResult = $this->rekognitionClient->detectLabels([
            'Image' => ['S3Object' => ['Bucket' => $this->bucket, 'Name' => $key]],
            'MaxLabels' => 50,
            'MinConfidence' => 70,
        ]);

        $labels = $labelsResult['Labels'] ?? [];
        $vehicleLabels = [];
        $colorLabels = [];
        $brandLabels = [];
        $otherLabels = [];

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

            $makes = Make::where('status', 'active')->pluck('make_name')->map(fn($m) => strtolower($m))->toArray();
            if (in_array($name, $makes)) {
                $brandLabels[] = [
                    'name' => $label['Name'],
                    'confidence' => $label['Confidence'],
                ];
                continue;
            }

            $otherLabels[] = [
                'name' => $label['Name'],
                'confidence' => $label['Confidence'],
            ];
        }

        if (empty($vehicleLabels)) {
            return response()->json([
                'error' => 'No vehicle detected in image',
            ], 422);
        }

        $detectedMake = null;
        $detectedModel = null;

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
            'vehicle_labels' => $vehicleLabels,
            'color' => !empty($colorLabels) ? $colorLabels[0] : null,
            'make' => $detectedMake,
            'model' => $detectedModel,
            'other_labels' => array_slice($otherLabels, 0, 10),
            'all_labels' => array_map(fn($l) => ['name' => $l['Name'], 'confidence' => $l['Confidence']], $labels),
        ]);
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