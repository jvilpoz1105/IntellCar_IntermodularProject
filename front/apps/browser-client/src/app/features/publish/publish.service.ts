import { Injectable, signal, computed } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { UploadedMedia } from './publish.types';
import { firstValueFrom } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class PublishService {
  private mediaItems = signal<UploadedMedia[]>([]);

  // Selectors
  readonly items = computed(() => this.mediaItems());
  readonly isUploading = computed(() => this.mediaItems().some(i => i.status === 'uploading' || i.status === 'analyzing'));
  readonly hasValidMedia = computed(() => this.mediaItems().some(i => i.status === 'success'));

  constructor(private http: HttpClient) {}

  async addFiles(files: FileList) {
    const newItems: UploadedMedia[] = Array.from(files).map(file => ({
      id: Math.random().toString(36).substring(2, 9),
      url: URL.createObjectURL(file), // Local preview
      status: 'pending' as const
    }));

    this.mediaItems.update(prev => [...prev, ...newItems]);

    // Process each file
    for (let i = 0; i < newItems.length; i++) {
      this.processUpload(newItems[i], Array.from(files)[i]);
    }
  }

  private async processUpload(item: UploadedMedia, file: File) {
    // 1. Upload to S3 (Simulated or via existing service)
    this.updateStatus(item.id, 'uploading');
    
    try {
      // AQUÍ IRÍA LA LLAMADA AL S3UploadService
      // Por ahora simulamos un tiempo de subida
      await new Promise(resolve => setTimeout(resolve, 1500));
      
      // 2. Analyze with AI
      this.updateStatus(item.id, 'analyzing');
      
      // Llamada real a la API de Laravel
      const response: any = await firstValueFrom(
        this.http.post('/api/media/analyze', { key: 'fake-key-' + item.id })
      );

      this.mediaItems.update(prev => prev.map(i => 
        i.id === item.id 
          ? { ...i, status: 'success', labels: response.labels?.map((l: any) => l.Name) } 
          : i
      ));

    } catch (error: any) {
      this.updateStatus(item.id, 'error');
      console.error('Error processing media:', error);
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
