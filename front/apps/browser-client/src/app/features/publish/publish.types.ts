export type MediaStatus = 'pending' | 'uploading' | 'analyzing' | 'success' | 'error';

export interface UploadedMedia {
  id: string;
  url: string | null;
  status: MediaStatus;
  labels?: string[];
}
