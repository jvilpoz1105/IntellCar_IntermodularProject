import { Injectable, signal, computed } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { UploadedMedia } from './publish.types';
import { firstValueFrom } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class PublishService {
  // Configuración de la API (Usando tu IP de EC2)
  private readonly API_BASE = 'http://18.205.229.197/api';
  private mediaItems = signal<UploadedMedia[]>([]);

  // Selectors
  readonly items = computed(() => this.mediaItems());
  readonly isUploading = computed(() => this.mediaItems().some(i => i.status === 'uploading' || i.status === 'analyzing'));
  readonly hasValidMedia = computed(() => this.mediaItems().some(i => i.status === 'success'));

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

      // 4. Actualizar con los resultados de la IA
      this.mediaItems.update(prev => prev.map(i => 
        i.id === item.id 
          ? { 
              ...i, 
              status: 'success', 
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
