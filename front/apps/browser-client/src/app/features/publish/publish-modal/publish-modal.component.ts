import {
  Component, Input, Output, EventEmitter,
  inject, signal, computed, OnInit, effect
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
import { ToastService } from '../../../core/services/toast.service';

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
      <div class="bg-slate-900 border border-slate-800 w-full max-w-5xl rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row h-[90vh] md:h-auto max-h-[90vh] animate-in zoom-in-95 duration-300">

        <!-- ══ Left: Media Engine ══════════════════════════════════ -->
        <div class="w-full md:w-[38%] bg-slate-950 p-6 flex flex-col gap-5 border-r border-slate-800 shrink-0">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-white tracking-tight">Multimedia</h3>
            <span class="text-[10px] px-2 py-1 bg-blue-500/10 text-blue-400 rounded-full border border-blue-500/20 font-bold uppercase tracking-wider">AI Verified</span>
          </div>

          <app-media-carousel
            [items]="publishService.items()"
            (deleteMedia)="publishService.removeMedia($event)"
          />

          <app-file-picker (filesSelected)="publishService.addFiles($event)" />

          <!-- Rejected media alert -->
          @if (publishService.hasRejectedMedia()) {
            <div class="flex items-start gap-2 p-3 bg-red-500/10 border border-red-500/30 rounded-xl animate-in fade-in slide-in-from-bottom-2">
              <svg class="w-4 h-4 text-red-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
              <p class="text-[11px] text-red-400 leading-relaxed">
                <strong>Fotos rechazadas.</strong> Elimina las imágenes con error antes de publicar.
              </p>
            </div>
          } @else if (publishService.hasValidMedia()) {
            <div class="flex items-center gap-2 p-3 bg-emerald-500/10 border border-emerald-500/30 rounded-xl animate-in fade-in">
              <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
              <p class="text-[11px] text-emerald-400">{{ publishService.items().length }} foto(s) verificadas por IA</p>
            </div>
          } @else {
            <div class="p-3 bg-blue-500/5 rounded-xl border border-blue-500/10">
              <p class="text-[11px] text-slate-400 leading-relaxed">
                <strong class="text-blue-400">Tip:</strong> Nuestra IA analizará tus fotos para detectar marca, modelo y posibles infracciones.
              </p>
            </div>
          }
        </div>

        <!-- ══ Right: Smart Form ══════════════════════════════════ -->
        <div class="w-full md:w-[62%] p-6 overflow-y-auto flex flex-col">

          <!-- Header -->
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="text-xl font-bold text-white tracking-tight">
                {{ type === 'market' ? 'Publicar Anuncio' : type === 'social' ? 'Crear Post' : 'Nuevo Evento' }}
              </h2>
              <p class="text-slate-400 text-xs mt-0.5">Completa los detalles para tu nueva publicación.</p>
            </div>
            <button id="publish-modal-close" (click)="handleClose()" class="p-2 hover:bg-slate-800 rounded-full text-slate-400 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
          </div>

          <form [formGroup]="form" (ngSubmit)="onSubmit()" class="flex flex-col gap-5 flex-1">

            <!-- ── MARKET FORM ─────────────────────────────────── -->
            @if (type === 'market') {

              <!-- Título + Precio -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <app-smart-field
                  id="field-ad-title"
                  label="Título del anuncio *"
                  placeholder="Ej: BMW M3 Competition 2023"
                  [control]="getControl('ad_title')"
                  [enableNlp]="true"
                  (nlpStatus)="handleNlpStatus('ad_title', $event)"
                />
                <app-smart-field
                  id="field-price"
                  label="Precio (€) *"
                  type="number"
                  placeholder="Ej: 45000"
                  [control]="getControl('price')"
                />
              </div>

              <!-- Tipo + Km + Año -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <app-smart-field
                  id="field-ad-type"
                  label="Tipo de anuncio *"
                  type="select"
                  [options]="adTypeOptions"
                  [control]="getControl('ad_type')"
                />
                <app-smart-field
                  id="field-kilometers"
                  label="Kilómetros"
                  type="number"
                  placeholder="Ej: 50000"
                  [control]="getControl('kilometers')"
                />
                <app-smart-field
                  id="field-year"
                  label="Año de fabricación"
                  type="number"
                  placeholder="Ej: 2021"
                  [control]="getControl('year_manufacture')"
                />
              </div>

              <!-- Marca → Modelo → Motor (cascada) -->
              <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Vehículo *</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <app-smart-field
                    id="field-make"
                    label="Marca"
                    type="select"
                    [options]="catalog.makeOptions()"
                    [control]="getControl('make_id_ui')"
                    [status]="catalog.loadingMakes() ? 'thinking' : 'idle'"
                  />
                  <app-smart-field
                    id="field-model"
                    label="Modelo *"
                    type="select"
                    [options]="catalog.modelOptions()"
                    [control]="getControl('model_id')"
                    [status]="catalog.loadingModels() ? 'thinking' : 'idle'"
                  />
                  <app-smart-field
                    id="field-engine"
                    label="Motor *"
                    type="select"
                    [options]="catalog.engineOptions()"
                    [control]="getControl('engine_id')"
                    [status]="catalog.loadingEngines() ? 'thinking' : 'idle'"
                  />
                </div>
              </div>

              <!-- Color -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <app-smart-field
                  id="field-color"
                  label="Color *"
                  type="select"
                  [options]="colorOptions"
                  [control]="getControl('car_color')"
                />
              </div>

              <!-- Ubicación -->
              <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Ubicación *</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <app-smart-field
                    id="field-region"
                    label="Región / Provincia"
                    placeholder="Ej: Cataluña"
                    [control]="getControl('region')"
                    [enableNlp]="true"
                    (nlpStatus)="handleNlpStatus('region', $event)"
                  />
                  <app-smart-field
                    id="field-city"
                    label="Ciudad"
                    placeholder="Ej: Barcelona"
                    [control]="getControl('city')"
                    [enableNlp]="true"
                    (nlpStatus)="handleNlpStatus('city', $event)"
                  />
                </div>
              </div>

              <!-- Detalles adicionales -->
              <app-smart-field
                id="field-details"
                label="Detalles adicionales"
                placeholder="Estado del motor, equipamiento, historial..."
                [control]="getControl('ad_details')"
                [enableNlp]="true"
                (nlpStatus)="handleNlpStatus('ad_details', $event)"
              />

              <!-- Paddocks / Comunidades -->
              @if (catalog.paddocks().length > 0) {
                <div>
                  <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">
                    Publicar en comunidades
                  </p>
                  <div class="flex flex-wrap gap-2">
                    @for (paddock of catalog.paddocks(); track paddock.paddock_id) {
                      <button
                        type="button"
                        [id]="'paddock-' + paddock.paddock_id"
                        (click)="togglePaddock(paddock)"
                        [class]="isPaddockSelected(paddock.paddock_id)
                          ? 'px-3 py-1.5 text-xs font-medium rounded-full border bg-blue-500/20 border-blue-500/50 text-blue-300 transition-all duration-200 shadow-[0_0_10px_rgba(59,130,246,0.2)]'
                          : 'px-3 py-1.5 text-xs font-medium rounded-full border bg-slate-800/60 border-slate-700 text-slate-400 hover:border-slate-500 hover:text-slate-300 transition-all duration-200'"
                      >
                        {{ paddock.paddock_name }}
                      </button>
                    }
                  </div>
                </div>
              }
            }

            <!-- ── SOCIAL POST ─────────────────────────────────── -->
            @if (type === 'social') {
              <app-smart-field
                id="field-content"
                label="¿En qué estás pensando?"
                placeholder="Cuéntale algo a la comunidad..."
                [control]="getControl('content')"
                [enableNlp]="true"
                (nlpStatus)="handleNlpStatus('content', $event)"
              />
            }

            <!-- ── SUBMIT BAR ─────────────────────────────────── -->
            <div class="flex items-center justify-between gap-3 mt-auto pt-5 border-t border-slate-800">

              <!-- Validation hint -->
              <div class="text-[11px] text-slate-500 flex-1">
                @if (publishService.hasRejectedMedia()) {
                  <span class="text-red-400">⚠ Elimina las fotos rechazadas antes de continuar</span>
                } @else if (hasNlpError()) {
                  <span class="text-red-400">⚠ Corrige los errores de contenido detectados por IA</span>
                } @else if (form.invalid) {
                  <span>Completa los campos obligatorios (*)</span>
                } @else if (!publishService.hasValidMedia()) {
                  <span>Añade al menos una foto verificada</span>
                } @else if (isSubmitting()) {
                  <span class="text-blue-400">Publicando...</span>
                } @else if (submitSuccess()) {
                  <span class="text-emerald-400">✓ ¡Anuncio publicado!</span>
                } @else if (submitError()) {
                  <span class="text-red-400">{{ submitError() }}</span>
                }
              </div>

              <div class="flex items-center gap-3 shrink-0">
                <button
                  type="button"
                  (click)="handleClose()"
                  class="px-4 py-2 text-sm font-medium text-slate-400 hover:text-white transition-colors"
                >
                  Cancelar
                </button>
                <button
                  id="publish-submit-btn"
                  type="submit"
                  [disabled]="!canSubmit"
                  class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 disabled:bg-slate-800 disabled:text-slate-600 disabled:cursor-not-allowed text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-900/20 active:scale-95 flex items-center gap-2 text-sm"
                >
                  @if (isSubmitting()) {
                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                    </svg>
                    Publicando...
                  } @else {
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                    Publicar Ahora
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
export class PublishModalComponent implements OnInit {
  @Input() type: 'market' | 'social' | 'kdd' = 'market';
  @Output() close = new EventEmitter<void>();
  @Output() published = new EventEmitter<any>();

  private fb      = inject(FormBuilder);
  private http    = inject(HttpClient);
  readonly publishService = inject(PublishService);
  readonly catalog        = inject(CatalogService);
  private toastService    = inject(ToastService);

  private readonly API_BASE = environment.apiUrl;

  isSubmitting  = signal(false);
  submitSuccess = signal(false);
  submitError   = signal<string | null>(null);
  
  nlpErrors     = signal<Record<string, boolean>>({});

  // Paddocks seleccionados
  selectedPaddockIds = signal<number[]>([]);

  // ── Opciones estáticas ───────────────────────────────────────────
  readonly adTypeOptions = [
    { label: 'Selecciona tipo...', value: '' },
    { label: 'Usado',         value: 'used' },
    { label: 'Nuevo',         value: 'new' },
    { label: 'KM0',           value: 'km0' },
    { label: 'Renting',       value: 'renting' },
    { label: 'Leasing',       value: 'leasing' },
    { label: 'Suscripción',   value: 'supcription' },
  ];

  readonly colorOptions = [
    { label: 'Selecciona color...', value: '' },
    { label: 'Blanco',    value: 'blanco' },
    { label: 'Negro',     value: 'negro' },
    { label: 'Gris',      value: 'gris' },
    { label: 'Plata',     value: 'plata' },
    { label: 'Rojo',      value: 'rojo' },
    { label: 'Azul',      value: 'azul' },
    { label: 'Verde',     value: 'verde' },
    { label: 'Amarillo',  value: 'amarillo' },
    { label: 'Naranja',   value: 'naranja' },
    { label: 'Otro',      value: 'otro' },
  ];

  // ── Form ────────────────────────────────────────────────────────
  form: FormGroup = this.fb.group({
    // Market
    ad_title:         ['', [Validators.required, Validators.maxLength(165)]],
    ad_type:          ['used', Validators.required],
    ad_details:       [''],
    price:            ['', [Validators.required, Validators.min(0)]],
    kilometers:       [''],
    car_color:        ['', Validators.required],
    year_manufacture: [''],
    region:           ['', Validators.required],
    city:             ['', Validators.required],
    make_id_ui:       [''],    // solo visual, no se envía
    model_id:         ['', Validators.required],
    engine_id:        ['', Validators.required],
    // Social
    content:          [''],
  });

  // ── Lifecycle ────────────────────────────────────────────────────
  ngOnInit(): void {
    if (this.type === 'market') {
      this.catalog.loadMakes();
      this.catalog.loadPaddocks();

      // Cascada: cuando cambia la marca UI → cargar modelos y motores
      this.form.get('make_id_ui')!.valueChanges.subscribe(makeId => {
        this.form.patchValue({ model_id: '', engine_id: '' });
        this.catalog.loadModels(makeId);
      });
    }
  }

  // ── Helpers ──────────────────────────────────────────────────────
  getControl(name: string): FormControl {
    return this.form.get(name) as FormControl;
  }

  handleNlpStatus(field: string, status: 'valid' | 'invalid'): void {
    this.nlpErrors.update(v => ({ ...v, [field]: status === 'invalid' }));
  }

  hasNlpError = computed(() => Object.values(this.nlpErrors()).some(invalid => invalid));

  get canSubmit(): boolean {
    if (this.type === 'market') {
      return this.form.valid
        && !this.publishService.isUploading()
        && !this.publishService.hasRejectedMedia()
        && this.publishService.hasValidMedia()
        && !this.isSubmitting()
        && !this.hasNlpError();
    }
    return this.form.valid && !this.isSubmitting() && !this.hasNlpError();
  }

  togglePaddock(paddock: VehiclePaddock): void {
    this.selectedPaddockIds.update(ids =>
      ids.includes(paddock.paddock_id)
        ? ids.filter(id => id !== paddock.paddock_id)
        : [...ids, paddock.paddock_id]
    );
  }

  isPaddockSelected(id: number): boolean {
    return this.selectedPaddockIds().includes(id);
  }

  // ── Submit ───────────────────────────────────────────────────────
  async onSubmit(): Promise<void> {
    if (!this.canSubmit) return;

    this.isSubmitting.set(true);
    this.submitError.set(null);
    this.submitSuccess.set(false);

    try {
      if (this.type === 'market') {
        const payload = {
          ad_title:         this.form.value.ad_title,
          ad_type:          this.form.value.ad_type,
          ad_details:       this.form.value.ad_details || null,
          price:            this.form.value.price,
          kilometers:       this.form.value.kilometers || null,
          car_color:        this.form.value.car_color,
          year_manufacture: this.form.value.year_manufacture || null,
          region:           this.form.value.region,
          city:             this.form.value.city,
          model_id:         this.form.value.model_id,
          engine_id:        this.form.value.engine_id,
          paddock_ids:      this.selectedPaddockIds(),
          media:            this.publishService.approvedMediaUrls(),
        };

        const result = await this.http.post(`${this.API_BASE}/market`, payload).toPromise();
        this.submitSuccess.set(true);
        this.toastService.showSuccess('¡Tu anuncio se ha publicado con éxito!');
        this.published.emit(result);

        // Cerrar el modal tras 1.5s de confirmación
        setTimeout(() => this.handleClose(), 1500);

      } else if (this.type === 'social') {
        const payload = { content: this.form.value.content };
        const result = await this.http.post(`${this.API_BASE}/social`, payload).toPromise();
        this.submitSuccess.set(true);
        this.toastService.showSuccess('¡Tu post se ha publicado con éxito!');
        this.published.emit(result);
        setTimeout(() => this.handleClose(), 1500);
      }
    } catch (err: any) {
      const msg = err?.error?.message ?? err?.error?.errors
        ? Object.values(err.error.errors).flat().join(', ')
        : 'Error al publicar. Inténtalo de nuevo.';
      this.submitError.set(msg as string);
      this.toastService.showError(msg as string);
    } finally {
      this.isSubmitting.set(false);
    }
  }

  handleClose(): void {
    this.publishService.reset();
    this.catalog.reset();
    this.selectedPaddockIds.set([]);
    this.submitSuccess.set(false);
    this.submitError.set(null);
    this.nlpErrors.set({});
    this.close.emit();
  }
}
