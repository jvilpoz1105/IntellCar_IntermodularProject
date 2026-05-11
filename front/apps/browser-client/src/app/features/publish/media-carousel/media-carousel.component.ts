import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MediaItemComponent, MediaStatus } from '../media-item/media-item.component';

export interface UploadedMedia {
  id: string;
  url: string | null;
  status: MediaStatus;
  labels?: string[];
}

@Component({
  selector: 'app-media-carousel',
  standalone: true,
  imports: [CommonModule, MediaItemComponent],
  template: `
    <div class="relative w-full overflow-hidden">
      <div class="flex gap-3 overflow-x-auto pb-4 px-1 snap-x scrollbar-hide">
        @for (item of items; track item.id) {
          <div class="flex-shrink-0 w-32 snap-start">
             <app-media-item 
               [imageUrl]="item.url" 
               [status]="item.status"
               [deleteCallback]="getDeleteCallback(item.id)"
             />
             
             <!-- AI Detected Labels (Mini) -->
             @if (item.status === 'success' && item.labels && item.labels.length > 0) {
               <div class="mt-2 flex flex-wrap gap-1 overflow-hidden h-5">
                  @for (label of item.labels.slice(0, 2); track label) {
                    <span class="text-[9px] px-1.5 py-0.5 bg-slate-800 text-slate-400 rounded-full border border-slate-700 whitespace-nowrap">
                      {{ label }}
                    </span>
                  }
               </div>
             }
          </div>
        }

        <!-- Placeholder if empty -->
        @if (items.length === 0) {
          <div class="w-full h-32 flex items-center justify-center border-2 border-dashed border-slate-800 rounded-xl bg-slate-900/30">
            <span class="text-xs text-slate-600 italic">No hay imágenes seleccionadas</span>
          </div>
        }
      </div>
    </div>
  `,
  styles: [`
    :host { display: block; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
  `]
})
export class MediaCarouselComponent {
  @Input() items: UploadedMedia[] = [];
  @Output() deleteMedia = new EventEmitter<string>();

  getDeleteCallback(id: string) {
    return () => this.deleteMedia.emit(id);
  }
}
