import { Injectable, signal, computed } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment';
import { UploadedMedia } from './publish.types';
import { firstValueFrom } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class PublishService {
  private readonly API_BASE = environment.apiUrl;
  private mediaItems = signal<UploadedMedia[]>([]);

  // Selectors
  readonly items = computed(() => this.mediaItems());
  readonly isUploading = computed(() =>
    this.mediaItems().some(i => i.status === 'uploading' || i.status === 'analyzing')
  );
  readonly hasValidMedia = computed(() =>
    this.mediaItems().some(i => i.status === 'success')
  );
  /** Bloquea el submit si alguna imagen fue rechazada por Rekognition */
  readonly hasRejectedMedia = computed(() =>
    this.mediaItems().some(i => i.status === 'error')
  );
  /** URLs S3 de imágenes aprobadas para enviar al backend */
  readonly approvedMediaUrls = computed(() =>
    this.mediaItems()
      .filter(i => i.status === 'success' && i.s3Url)
      .map(i => i.s3Url!)
  );

  constructor(private http: HttpClient) {}

  async addFiles(files: FileList) {
    const newItems: UploadedMedia[] = Array.from(files).map(file => ({
      id: Math.random().toString(36).substring(2, 9),
      url: URL.createObjectURL(file),
      status: 'pending' as const
    }));

    this.mediaItems.update(prev => [...prev, ...newItems]);

    for (let i = 0; i < newItems.length; i++) {
      this.processRealUpload(newItems[i], Array.from(files)[i]);
    }
  }

  private async processRealUpload(item: UploadedMedia, file: File) {
    try {
      // 1. Obtener URL firmada de S3 desde nuestra API
      this.updateStatus(item.id, 'uploading');
      const presigned: any = await firstValueFrom(
        this.http.post(`${this.API_BASE}/media/presigned`, {
          filename: file.name,
          content_type: file.type
        })
      );

      // 2. Subir el archivo directamente a S3
      await firstValueFrom(
        this.http.put(presigned.upload_url, file, {
          headers: { 'Content-Type': file.type }
        })
      );

      // 3. Notificar a la API para que analice con Rekognition
      this.updateStatus(item.id, 'analyzing');
      const analysis: any = await firstValueFrom(
        this.http.post(`${this.API_BASE}/media/analyze`, { key: presigned.key })
      );

      // 4. Actualizar con los resultados — guardar s3Url y s3Key
      this.mediaItems.update(prev => prev.map(i =>
        i.id === item.id
          ? {
              ...i,
              status: 'success',
              s3Key: presigned.key,
              s3Url: presigned.public_url ?? presigned.upload_url?.split('?')[0],
              labels: analysis.all_labels?.map((l: any) => l.name)
            }
          : i
      ));

    } catch (error: any) {
      this.updateStatus(item.id, 'error');
      console.error('Error en el flujo de subida:', error);
    }
  }

  private updateStatus(id: string, status: UploadedMedia['status']) {
    this.mediaItems.update(prev => prev.map(i =>
      i.id === id ? { ...i, status } : i
    ));
  }

  removeMedia(id: string) {
    this.mediaItems.update(prev => prev.filter(i => i.id !== id));
  }

  reset() {
    this.mediaItems.set([]);
  }
}
