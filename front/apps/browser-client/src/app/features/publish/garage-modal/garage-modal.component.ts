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

@Component({
  selector: 'app-garage-modal',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, MediaCarouselComponent, FilePickerComponent, SmartFieldComponent],
  template: `
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-in fade-in duration-300">
      <div class="bg-slate-900 border border-slate-800 w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row max-h-[88vh] animate-in zoom-in-95 duration-300">

        <!-- Left: Foto del coche -->
        <div class="w-full md:w-[36%] bg-slate-950 p-6 flex flex-col gap-5 border-r border-slate-800 shrink-0">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Foto del coche</h3>
            <span class="text-[10px] px-2 py-1 bg-emerald-500/10 text-emerald-400 rounded-full border border-emerald-500/20 font-bold uppercase tracking-wider">AI Verified</span>
          </div>
          <app-media-carousel [items]="publishService.items()" (deleteMedia)="publishService.removeMedia($event)" />
          <app-file-picker (filesSelected)="publishService.addFiles($event)" />

          @if (publishService.hasRejectedMedia()) {
            <div class="flex items-start gap-2 p-3 bg-red-500/10 border border-red-500/30 rounded-xl">
              <svg class="w-4 h-4 text-red-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
              <p class="text-[11px] text-red-400"><strong>Foto rechazada.</strong> Elimínala antes de continuar.</p>
            </div>
          } @else if (publishService.hasValidMedia()) {
            <div class="flex items-center gap-2 p-3 bg-emerald-500/10 border border-emerald-500/30 rounded-xl">
              <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
              <p class="text-[11px] text-emerald-400">Foto verificada por IA ✓</p>
            </div>
          } @else {
            <div class="p-3 bg-emerald-500/5 rounded-xl border border-emerald-500/10">
              <p class="text-[11px] text-slate-400 leading-relaxed">Sube una foto de tu coche para mostrarla en tu garaje. Solo se usará la primera imagen.</p>
            </div>
          }
        </div>

        <!-- Right: Form -->
        <div class="w-full md:w-[64%] p-6 overflow-y-auto flex flex-col">
          <div class="flex items-center justify-between mb-5">
            <div>
              <h2 class="text-xl font-bold text-white">Añadir al Garaje</h2>
              <p class="text-slate-400 text-xs mt-0.5">Registra uno de tus coches en tu perfil.</p>
            </div>
            <button id="garage-modal-close" (click)="handleClose()" class="p-2 hover:bg-slate-800 rounded-full text-slate-400 transition-colors">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
          </div>

          <form [formGroup]="form" (ngSubmit)="onSubmit()" class="flex flex-col gap-5 flex-1">

            <!-- Apodo personalizado -->
            <app-smart-field
              id="garage-field-nickname"
              label="Nombre o apodo del coche"
              placeholder="Ej: Mi M3, La bestia, Pitufina..."
              [control]="getControl('car_nickname')"
              [enableNlp]="true"
              (nlpStatus)="handleNlpStatus('car_nickname', $event)"
            />

            <!-- Vehículo: Marca → Modelo → Motor -->
            <div>
              <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Vehículo *</p>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <app-smart-field
                  id="garage-field-make"
                  label="Marca"
                  type="select"
                  [options]="catalog.makeOptions()"
                  [control]="getControl('make_id_ui')"
                  [status]="catalog.loadingMakes() ? 'thinking' : 'idle'"
                />
                <app-smart-field
                  id="garage-field-model"
                  label="Modelo *"
                  type="select"
                  [options]="catalog.modelOptions()"
                  [control]="getControl('model_id')"
                  [status]="catalog.loadingModels() ? 'thinking' : 'idle'"
                />
                <app-smart-field
                  id="garage-field-engine"
                  label="Motor"
                  type="select"
                  [options]="catalog.engineOptions()"
                  [control]="getControl('motor_id')"
                  [status]="catalog.loadingEngines() ? 'thinking' : 'idle'"
                />
              </div>
            </div>

            <!-- Descripción -->
            <app-smart-field
              id="garage-field-description"
              label="Descripción / Historia"
              placeholder="Cuéntanos algo de este coche, modificaciones, historia..."
              [control]="getControl('description')"
              [enableNlp]="true"
              (nlpStatus)="handleNlpStatus('description', $event)"
            />

            <!-- ¿Coche actual? -->
            <div class="flex items-center gap-3 p-4 bg-slate-800/50 rounded-xl border border-slate-700/50">
              <button
                type="button"
                id="garage-is-current"
                (click)="toggleCurrentCar()"
                [class]="isCurrentCar()
                  ? 'w-11 h-6 rounded-full bg-emerald-500 relative transition-all duration-300 shrink-0'
                  : 'w-11 h-6 rounded-full bg-slate-600 relative transition-all duration-300 shrink-0'"
              >
                <span [class]="isCurrentCar()
                  ? 'absolute top-0.5 left-5 w-5 h-5 bg-white rounded-full shadow transition-all duration-300'
                  : 'absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-all duration-300'">
                </span>
              </button>
              <div>
                <p class="text-sm font-medium text-slate-200">¿Es tu coche actual?</p>
                <p class="text-xs text-slate-500">Aparecerá destacado en tu perfil</p>
              </div>
            </div>

            <!-- Submit bar -->
            <div class="flex items-center justify-between gap-3 mt-auto pt-5 border-t border-slate-800">
              <div class="text-[11px] text-slate-500 flex-1">
                @if (publishService.hasRejectedMedia()) { <span class="text-red-400">⚠ Elimina la foto rechazada</span> }
                @else if (hasNlpError()) { <span class="text-red-400">⚠ Corrige los errores de contenido detectados por IA</span> }
                @else if (form.invalid) { <span>Selecciona al menos un modelo (*)</span> }
                @else if (submitSuccess()) { <span class="text-emerald-400">✓ ¡Coche añadido al garaje!</span> }
                @else if (submitError()) { <span class="text-red-400">{{ submitError() }}</span> }
              </div>
              <div class="flex items-center gap-3 shrink-0">
                <button type="button" (click)="handleClose()" class="px-4 py-2 text-sm text-slate-400 hover:text-white transition-colors">Cancelar</button>
                <button id="garage-submit-btn" type="submit" [disabled]="!canSubmit"
                  class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 disabled:bg-slate-800 disabled:text-slate-600 disabled:cursor-not-allowed text-white font-bold rounded-xl transition-all active:scale-95 flex items-center gap-2 text-sm">
                  @if (isSubmitting()) {
                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Guardando...
                  } @else {
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
                    Añadir al Garaje
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
export class GarageModalComponent implements OnInit {
  @Output() close  = new EventEmitter<void>();
  @Output() added  = new EventEmitter<any>();

  private fb      = inject(FormBuilder);
  private http    = inject(HttpClient);
  readonly publishService = inject(PublishService);
  readonly catalog        = inject(CatalogService);
  private readonly API_BASE = environment.apiUrl;

  isSubmitting  = signal(false);
  submitSuccess = signal(false);
  submitError   = signal<string | null>(null);
  isCurrentCar  = signal(false);
  
  nlpErrors     = signal<Record<string, boolean>>({});

  form: FormGroup = this.fb.group({
    car_nickname: [''],
    make_id_ui:   [''],   // solo visual
    model_id:     ['', Validators.required],
    motor_id:     [''],
    description:  [''],
    is_current_car: [false],
  });

  ngOnInit(): void {
    this.catalog.loadMakes();
    this.form.get('make_id_ui')!.valueChanges.subscribe(makeId => {
      this.form.patchValue({ model_id: '', motor_id: '' });
      this.catalog.loadModels(makeId);
    });
  }

  getControl(name: string): FormControl { return this.form.get(name) as FormControl; }

  toggleCurrentCar(): void {
    const next = !this.isCurrentCar();
    this.isCurrentCar.set(next);
    this.form.patchValue({ is_current_car: next });
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
      // Solo usamos la primera foto aprobada como foto del coche
      const photoUrl = this.publishService.approvedMediaUrls()[0] ?? null;
      const payload = {
        model_id:       v.model_id,
        motor_id:       v.motor_id || null,
        car_nickname:   v.car_nickname || null,
        description:    v.description || null,
        is_current_car: v.is_current_car ?? false,
        photo_url:      photoUrl,
      };
      const result = await this.http.post(`${this.API_BASE}/profile/garage`, payload).toPromise();
      this.submitSuccess.set(true);
      this.added.emit(result);
      setTimeout(() => this.handleClose(), 1500);
    } catch (err: any) {
      const msg = err?.error?.message
        ?? (err?.error?.errors ? Object.values(err.error.errors).flat().join(', ') : 'Error al guardar.');
      this.submitError.set(msg as string);
    } finally {
      this.isSubmitting.set(false);
    }
  }

  handleClose(): void {
    this.publishService.reset();
    this.catalog.reset();
    this.isCurrentCar.set(false);
    this.submitSuccess.set(false);
    this.submitError.set(null);
    this.nlpErrors.set({});
    this.close.emit();
  }
}
