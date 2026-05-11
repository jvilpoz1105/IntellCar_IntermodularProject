import { Component, Input, Output, EventEmitter, inject, signal, computed } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { PublishService } from '../publish.service';
import { MediaCarouselComponent } from '../media-carousel/media-carousel.component';
import { FilePickerComponent } from '../file-picker/file-picker.component';
import { SmartFieldComponent } from '../smart-field/smart-field.component';

@Component({
  selector: 'app-publish-modal',
  standalone: true,
  imports: [
    CommonModule, 
    ReactiveFormsModule, 
    MediaCarouselComponent, 
    FilePickerComponent, 
    SmartFieldComponent
  ],
  template: `
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-in fade-in duration-300">
      <div class="bg-slate-900 border border-slate-800 w-full max-w-5xl rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row h-[90vh] md:h-auto max-h-[850px] animate-in zoom-in-95 duration-300">
        
        <!-- Left Side: Media Engine -->
        <div class="w-full md:w-[40%] bg-slate-950 p-6 flex flex-col gap-6 border-r border-slate-800">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-white tracking-tight">Multimedia</h3>
            <span class="text-[10px] px-2 py-1 bg-blue-500/10 text-blue-400 rounded-full border border-blue-500/20 font-bold uppercase tracking-wider">AI Verified</span>
          </div>

          <app-media-carousel 
            [items]="publishService.items()" 
            (deleteMedia)="publishService.removeMedia($event)"
          />

          <app-file-picker (filesSelected)="publishService.addFiles($event)" />

          <div class="mt-auto p-4 bg-blue-500/5 rounded-xl border border-blue-500/10">
            <p class="text-[11px] text-slate-400 leading-relaxed">
              <strong class="text-blue-400">Tip:</strong> Nuestra IA analizará tus fotos automáticamente para detectar marca, modelo y posibles infracciones de seguridad.
            </p>
          </div>
        </div>

        <!-- Right Side: Smart Form -->
        <div class="w-full md:w-[60%] p-8 overflow-y-auto flex flex-col gap-8">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-2xl font-bold text-white tracking-tight">
                {{ type === 'market' ? 'Publicar Anuncio' : type === 'social' ? 'Crear Post' : 'Nuevo Evento' }}
              </h2>
              <p class="text-slate-400 text-sm mt-1">Completa los detalles para tu nueva publicación.</p>
            </div>
            <button (click)="handleClose()" class="p-2 hover:bg-slate-800 rounded-full text-slate-400 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
          </div>

          <form [formGroup]="form" class="flex flex-col gap-6">
            <!-- Dynamic Fields Based on Type -->
            @if (type === 'market') {
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <app-smart-field 
                  label="Título del anuncio" 
                  placeholder="Ej: BMW M3 E46 2004" 
                  [control]="getControl('ad_title')"
                />
                <app-smart-field 
                  label="Precio (€)" 
                  type="number" 
                  [control]="getControl('price')"
                />
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <app-smart-field 
                  label="Tipo de Anuncio" 
                  type="select" 
                  [options]="[
                    {label: 'Usado', value: 'used'},
                    {label: 'Nuevo', value: 'new'},
                    {label: 'KM0', value: 'km0'},
                    {label: 'Renting', value: 'renting'}
                  ]"
                  [control]="getControl('ad_type')"
                />
                <app-smart-field 
                  label="Kilómetros" 
                  type="number" 
                  [control]="getControl('kilometers')"
                />
                <app-smart-field 
                  label="Año" 
                  type="number" 
                  [control]="getControl('year_manufacture')"
                />
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <app-smart-field 
                  label="Marca" 
                  type="select" 
                  [options]="[{label: 'Selecciona...', value: ''}, {label: 'BMW', value: '1'}]"
                  [control]="getControl('make')"
                  [status]="aiStatus('make')"
                />
                <app-smart-field 
                  label="Modelo" 
                  type="select" 
                  [options]="[{label: 'Selecciona...', value: ''}, {label: 'M3', value: '1'}]"
                  [control]="getControl('model_id')"
                  [status]="aiStatus('model_id')"
                />
                <app-smart-field 
                  label="Color" 
                  [control]="getControl('car_color')"
                  [status]="aiStatus('car_color')"
                />
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <app-smart-field 
                  label="Región / Provincia" 
                  [control]="getControl('region')"
                />
                <app-smart-field 
                  label="Ciudad" 
                  [control]="getControl('city')"
                />
              </div>

              <app-smart-field 
                label="Detalles adicionales" 
                placeholder="Estado del motor, equipamiento..." 
                [control]="getControl('ad_details')"
              />
            }

            @if (type === 'social') {
               <app-smart-field 
                  label="¿En qué estás pensando?" 
                  placeholder="Cuéntale algo a la comunidad..." 
                  [control]="getControl('content')"
                />
            }

            <div class="flex items-center justify-end gap-3 mt-4 pt-6 border-t border-slate-800">
               <button 
                (click)="handleClose()"
                class="px-4 py-2 text-sm font-medium text-slate-400 hover:text-white transition-colors"
               >
                 Cancelar
               </button>
               <button 
                [disabled]="!form.valid || publishService.isUploading() || !publishService.hasValidMedia()"
                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 disabled:bg-slate-800 disabled:text-slate-600 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-900/20 active:scale-95 flex items-center gap-2"
               >
                 @if (publishService.isUploading()) {
                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Analizando...
                 } @else {
                    Publicar Ahora
                 }
               </button>
            </div>
          </form>
        </div>

      </div>
    </div>
  `,
  styles: [`
    :host { display: block; }
  `]
})
export class PublishModalComponent {
  @Input() type: 'market' | 'social' | 'kdd' = 'market';
  @Output() close = new EventEmitter<void>();
  
  private fb = inject(FormBuilder);
  publishService = inject(PublishService);

  form: FormGroup = this.fb.group({
    ad_title: ['', Validators.required],
    ad_type: ['used', Validators.required],
    ad_details: [''],
    price: ['', [Validators.required, Validators.min(0)]],
    kilometers: [''],
    car_color: [''],
    year_manufacture: [''],
    region: ['', Validators.required],
    city: ['', Validators.required],
    make: [''], // Campo para filtrado o visual
    model_id: ['', Validators.required],
    engine_id: ['1'], // Por ahora fijo o seleccionable
    content: [''],
  });

  getControl(name: string) {
    return this.form.get(name) as any;
  }

  aiStatus(field: string) {
    // Lógica para devolver 'thinking' | 'success' | 'idle' basado en el servicio
    if (this.publishService.isUploading()) return 'thinking';
    if (this.publishService.hasValidMedia()) return 'success';
    return 'idle';
  }

  handleClose() {
    this.publishService.reset();
    this.close.emit();
  }
}
