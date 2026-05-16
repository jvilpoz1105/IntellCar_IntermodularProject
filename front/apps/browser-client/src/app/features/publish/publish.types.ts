export type MediaStatus = 'pending' | 'uploading' | 'analyzing' | 'success' | 'error';

export interface UploadedMedia {
  id: string;
  url: string | null;
  s3Key?: string;       // Clave S3 para enviar al backend al publicar
  s3Url?: string;       // URL pública en S3 (para enviar al backend)
  status: MediaStatus;
  labels?: string[];
}

// --- Tipos del catálogo de vehículos ---

export interface VehicleMake {
  make_id: number;
  make_name: string;
  origin_country?: string;
  status?: string;
}

export interface VehicleModel {
  model_id: number;
  model_name: string;
  make_id: number;
}

export interface VehicleEngine {
  engine_id: number;
  engine_name: string;
  fuel_type?: string;
  make_id: number;
}

export interface VehiclePaddock {
  paddock_id: number;
  paddock_name: string;
  paddock_description?: string;
}
