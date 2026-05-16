import {
  Component, Output, EventEmitter,
  inject, signal, computed, OnInit
} from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators, AbstractControl, FormControl } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { PublishService } from '../publish.service';
import { CatalogService } from '../catalog.service';
import { MediaCarouselComponent } from '../media-carousel/media-carousel.component';
import { FilePickerComponent } from '../file-picker/file-picker.component';
import { SmartFieldComponent } from '../smart-field/smart-field.component';
import { environment } from '../../../../environments/environment';
import { VehiclePaddock } from '../publish.types';

@Component({
  selector: 'app-kdd-modal',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, MediaCarouselComponent, FilePickerComponent, SmartFieldComponent],
  template: `
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-in fade-in duration-300">
      <div class="bg-slate-900 border border-slate-800 w-full max-w-5xl rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row max-h-[90vh] animate-in zoom-in-95 duration-300">

        <!-- Left: Media -->
        <div class="w-full md:w-[36%] bg-slate-950 p-6 flex flex-col gap-5 border-r border-slate-800 shrink-0">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Portada</h3>
            <span class="text-[10px] px-2 py-1 bg-amber-500/10 text-amber-400 rounded-full border border-amber-500/20 font-bold uppercase tracking-wider">AI Verified</span>
          </div>
          <app-media-carousel [items]="publishService.items()" (deleteMedia)="publishService.removeMedia($event)" />
          <app-file-picker (filesSelected)="publishService.addFiles($event)" />

          @if (publishService.hasRejectedMedia()) {
            <div class="flex items-start gap-2 p-3 bg-red-500/10 border border-red-500/30 rounded-xl">
              <svg class="w-4 h-4 text-red-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
              <p class="text-[11px] text-red-400"><strong>Fotos rechazadas.</strong> Elimínalas antes de publicar.</p>
            </div>
          } @else if (publishService.hasValidMedia()) {
            <div class="flex items-center gap-2 p-3 bg-emerald-500/10 border border-emerald-500/30 rounded-xl">
              <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
              <p class="text-[11px] text-emerald-400">{{ publishService.items().length }} foto(s) verificadas</p>
            </div>
          } @else {
            <div class="p-3 bg-amber-500/5 rounded-xl border border-amber-500/10">
              <p class="text-[11px] text-slate-400 leading-relaxed">Añade una foto de portada para que los asistentes reconozcan tu evento.</p>
            </div>
          }
        </div>

        <!-- Right: Form -->
        <div class="w-full md:w-[64%] p-6 overflow-y-auto flex flex-col">
          <div class="flex items-center justify-between mb-5">
            <div>
              <h2 class="text-xl font-bold text-white">Nuevo Evento / KDD</h2>
              <p class="text-slate-400 text-xs mt-0.5">Organiza una quedada con la comunidad.</p>
            </div>
            <button id="kdd-modal-close" (click)="handleClose()" class="p-2 hover:bg-slate-800 rounded-full text-slate-400 transition-colors">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
          </div>

          <form [formGroup]="form" (ngSubmit)="onSubmit()" class="flex flex-col gap-5 flex-1">

            <!-- Título -->
            <app-smart-field id="kdd-field-title" label="Título del evento *" placeholder="Ej: Track Day Circuit de Catalunya" [control]="getControl('title')" [enableNlp]="true" (nlpStatus)="handleNlpStatus('title', $event)" />

            <!-- Descripción -->
            <app-smart-field id="kdd-field-description" label="Descripción *" placeholder="Cuéntanos de qué va el evento..." [control]="getControl('event_description')" [enableNlp]="true" (nlpStatus)="handleNlpStatus('event_description', $event)" />

            <!-- Fecha + Participantes -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <app-smart-field id="kdd-field-date" label="Fecha y hora *" type="text" placeholder="Ej: 2025-12-01 10:00" [control]="getControl('event_date')" />
              <app-smart-field id="kdd-field-max-participants" label="Plazas máximas" type="number" placeholder="Ej: 50 (vacío = ilimitado)" [control]="getControl('max_participants')" />
            </div>

            <!-- Ubicación -->
            <div>
              <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Ubicación</p>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <app-smart-field id="kdd-field-location" label="Nombre del lugar" placeholder="Ej: Circuito de Jerez" [control]="getControl('location_name')" [enableNlp]="true" (nlpStatus)="handleNlpStatus('location_name', $event)" />
                <app-smart-field id="kdd-field-address" label="Dirección" placeholder="Calle, número..." [control]="getControl('address')" [enableNlp]="true" (nlpStatus)="handleNlpStatus('address', $event)" />
                <app-smart-field id="kdd-field-city" label="Ciudad" placeholder="Ej: Jerez de la Frontera" [control]="getControl('city')" [enableNlp]="true" (nlpStatus)="handleNlpStatus('city', $event)" />
              </div>
            </div>

            <!-- Coordenadas (colapsables) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <app-smart-field id="kdd-field-lat" label="Latitud (opcional)" type="number" placeholder="Ej: 36.7083" [control]="getControl('latitude')" />
              <app-smart-field id="kdd-field-lng" label="Longitud (opcional)" type="number" placeholder="Ej: -6.0343" [control]="getControl('longitude')" />
            </div>

            <!-- Paddock (obligatorio para eventos) -->
            <div>
              <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Comunidad organizadora *</p>
              @if (catalog.loadingPaddocks()) {
                <div class="flex items-center gap-2 text-xs text-slate-500">
                  <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                  Cargando paddocks...
                </div>
              } @else {
                <div class="flex flex-wrap gap-2">
                  @for (paddock of catalog.paddocks(); track paddock.paddock_id) {
                    <button
                      type="button"
                      [id]="'kdd-paddock-' + paddock.paddock_id"
                      (click)="selectPaddock(paddock)"
                      [class]="selectedPaddockId() === paddock.paddock_id
                        ? 'px-3 py-1.5 text-xs font-medium rounded-full border bg-amber-500/20 border-amber-500/50 text-amber-300 transition-all duration-200 shadow-[0_0_10px_rgba(245,158,11,0.2)]'
                        : 'px-3 py-1.5 text-xs font-medium rounded-full border bg-slate-800/60 border-slate-700 text-slate-400 hover:border-slate-500 hover:text-slate-300 transition-all duration-200'"
                    >
                      {{ paddock.paddock_name }}
                    </button>
                  }
                </div>
                @if (getControl('paddock_id').invalid && getControl('paddock_id').touched) {
                  <p class="text-[11px] text-red-400 mt-1.5">Selecciona una comunidad organizadora</p>
                }
              }
            </div>

            <!-- Submit bar -->
            <div class="flex items-center justify-between gap-3 mt-auto pt-5 border-t border-slate-800">
              <div class="text-[11px] text-slate-500 flex-1">
                @if (publishService.hasRejectedMedia()) { <span class="text-red-400">⚠ Elimina las fotos rechazadas</span> }
                @else if (hasNlpError()) { <span class="text-red-400">⚠ Corrige los errores de contenido detectados por IA</span> }
                @else if (form.invalid) { <span>Completa los campos obligatorios (*)</span> }
                @else if (submitSuccess()) { <span class="text-emerald-400">✓ ¡Evento creado!</span> }
                @else if (submitError()) { <span class="text-red-400">{{ submitError() }}</span> }
              </div>
              <div class="flex items-center gap-3 shrink-0">
                <button type="button" (click)="handleClose()" class="px-4 py-2 text-sm text-slate-400 hover:text-white transition-colors">Cancelar</button>
                <button id="kdd-submit-btn" type="submit" [disabled]="!canSubmit"
                  class="px-5 py-2.5 bg-amber-600 hover:bg-amber-500 disabled:bg-slate-800 disabled:text-slate-600 disabled:cursor-not-allowed text-white font-bold rounded-xl transition-all active:scale-95 flex items-center gap-2 text-sm">
                  @if (isSubmitting()) {
                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Publicando...
                  } @else {
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                    Crear Evento
                  }
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  `,
  styles: [`:host { display: block; }`]
})
export class KddModalComponent implements OnInit {
  @Output() close     = new EventEmitter<void>();
  @Output() published = new EventEmitter<any>();

  private fb      = inject(FormBuilder);
  private http    = inject(HttpClient);
  readonly publishService = inject(PublishService);
  readonly catalog        = inject(CatalogService);
  private readonly API_BASE = environment.apiUrl;

  isSubmitting     = signal(false);
  submitSuccess    = signal(false);
  submitError      = signal<string | null>(null);
  selectedPaddockId = signal<number | null>(null);
  
  nlpErrors        = signal<Record<string, boolean>>({});

  form: FormGroup = this.fb.group({
    title:             ['', [Validators.required, Validators.maxLength(150)]],
    event_description: ['', Validators.required],
    event_date:        ['', Validators.required],
    location_name:     [''],
    address:           [''],
    city:              [''],
    latitude:          [''],
    longitude:         [''],
    max_participants:  [''],
    paddock_id:        ['', Validators.required],
  });

  ngOnInit(): void {
    this.catalog.loadPaddocks();
  }

  getControl(name: string): FormControl { return this.form.get(name) as FormControl; }

  selectPaddock(paddock: VehiclePaddock): void {
    const id = paddock.paddock_id;
    this.selectedPaddockId.set(id);
    this.form.patchValue({ paddock_id: id });
    this.form.get('paddock_id')!.markAsTouched();
  }

  handleNlpStatus(field: string, status: 'valid' | 'invalid'): void {
    this.nlpErrors.update(v => ({ ...v, [field]: status === 'invalid' }));
  }

  hasNlpError = computed(() => Object.values(this.nlpErrors()).some(invalid => invalid));

  get canSubmit(): boolean {
    return this.form.valid
      && !this.publishService.isUploading()
      && !this.publishService.hasRejectedMedia()
      && !this.isSubmitting()
      && !this.hasNlpError();
  }

  async onSubmit(): Promise<void> {
    this.form.markAllAsTouched();
    if (!this.canSubmit) return;
    this.isSubmitting.set(true);
    this.submitError.set(null);
    try {
      const v = this.form.value;
      const payload = {
        title:             v.title,
        event_description: v.event_description,
        event_date:        v.event_date,
        location_name:     v.location_name || null,
        address:           v.address || null,
        city:              v.city || null,
        latitude:          v.latitude || null,
        longitude:         v.longitude || null,
        max_participants:  v.max_participants || null,
        paddock_id:        v.paddock_id,
        media:             this.publishService.approvedMediaUrls(),
      };
      const result = await this.http.post(`${this.API_BASE}/kdds`, payload).toPromise();
      this.submitSuccess.set(true);
      this.published.emit(result);
      setTimeout(() => this.handleClose(), 1500);
    } catch (err: any) {
      const msg = err?.error?.message
        ?? (err?.error?.errors ? Object.values(err.error.errors).flat().join(', ') : 'Error al crear el evento.');
      this.submitError.set(msg as string);
    } finally {
      this.isSubmitting.set(false);
    }
  }

  handleClose(): void {
    this.publishService.reset();
    this.selectedPaddockId.set(null);
    this.submitSuccess.set(false);
    this.submitError.set(null);
    this.nlpErrors.set({});
    this.close.emit();
  }
}
