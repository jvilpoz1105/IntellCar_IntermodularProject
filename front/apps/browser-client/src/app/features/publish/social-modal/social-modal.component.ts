import {
  Component, Output, EventEmitter,
  inject, signal, computed, OnInit
} from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators, AbstractControl } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { PublishService } from '../publish.service';
import { CatalogService } from '../catalog.service';
import { MediaCarouselComponent } from '../media-carousel/media-carousel.component';
import { FilePickerComponent } from '../file-picker/file-picker.component';
import { SmartFieldComponent } from '../smart-field/smart-field.component';
import { environment } from '../../../../environments/environment';

@Component({
  selector: 'app-social-modal',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, MediaCarouselComponent, FilePickerComponent, SmartFieldComponent],
  template: `
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-in fade-in duration-300">
      <div class="bg-slate-900 border border-slate-800 w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row max-h-[88vh] animate-in zoom-in-95 duration-300">

        <!-- Left: Media -->
        <div class="w-full md:w-[38%] bg-slate-950 p-6 flex flex-col gap-5 border-r border-slate-800 shrink-0">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Multimedia</h3>
            <span class="text-[10px] px-2 py-1 bg-violet-500/10 text-violet-400 rounded-full border border-violet-500/20 font-bold uppercase tracking-wider">Opcional</span>
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
            <div class="p-3 bg-violet-500/5 rounded-xl border border-violet-500/10">
              <p class="text-[11px] text-slate-400">Las fotos son opcionales en los posts.</p>
            </div>
          }
        </div>

        <!-- Right: Form -->
        <div class="w-full md:w-[62%] p-6 overflow-y-auto flex flex-col">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="text-xl font-bold text-white">Nuevo Post</h2>
              <p class="text-slate-400 text-xs mt-0.5">Comparte algo con la comunidad de IntellCar.</p>
            </div>
            <button id="social-modal-close" (click)="handleClose()" class="p-2 hover:bg-slate-800 rounded-full text-slate-400 transition-colors">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
          </div>

          <form [formGroup]="form" (ngSubmit)="onSubmit()" class="flex flex-col gap-5 flex-1">
            <app-smart-field id="social-field-title" label="Título (opcional)" placeholder="Ej: Mi primer day en circuito..." [control]="getControl('title')" [enableNlp]="true" (nlpStatus)="handleNlpStatus('title', $event)" />
            <app-smart-field id="social-field-content" label="¿Qué quieres compartir? *" placeholder="Cuéntale algo a la comunidad..." [control]="getControl('content')" [enableNlp]="true" (nlpStatus)="handleNlpStatus('content', $event)" />

            <div>
              <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Vehículo relacionado (opcional)</p>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <app-smart-field id="social-field-make" label="Marca" type="select" [options]="catalog.makeOptions()" [control]="getControl('make_id_ui')" [status]="catalog.loadingMakes() ? 'thinking' : 'idle'" />
                <app-smart-field id="social-field-model" label="Modelo" type="select" [options]="catalog.modelOptions()" [control]="getControl('model_id')" [status]="catalog.loadingModels() ? 'thinking' : 'idle'" />
                <app-smart-field id="social-field-engine" label="Motor" type="select" [options]="catalog.engineOptions()" [control]="getControl('engine_id')" [status]="catalog.loadingEngines() ? 'thinking' : 'idle'" />
              </div>
            </div>

            <!-- Submit bar -->
            <div class="flex items-center justify-between gap-3 mt-auto pt-5 border-t border-slate-800">
              <div class="text-[11px] text-slate-500 flex-1">
                @if (publishService.hasRejectedMedia()) { <span class="text-red-400">⚠ Elimina las fotos rechazadas</span> }
                @else if (hasNlpError()) { <span class="text-red-400">⚠ Corrige los errores de contenido detectados por IA</span> }
                @else if (form.invalid) { <span>El contenido del post es obligatorio</span> }
                @else if (submitSuccess()) { <span class="text-emerald-400">✓ ¡Post publicado!</span> }
                @else if (submitError()) { <span class="text-red-400">{{ submitError() }}</span> }
              </div>
              <div class="flex items-center gap-3 shrink-0">
                <button type="button" (click)="handleClose()" class="px-4 py-2 text-sm text-slate-400 hover:text-white transition-colors">Cancelar</button>
                <button id="social-submit-btn" type="submit" [disabled]="!canSubmit"
                  class="px-5 py-2.5 bg-violet-600 hover:bg-violet-500 disabled:bg-slate-800 disabled:text-slate-600 disabled:cursor-not-allowed text-white font-bold rounded-xl transition-all active:scale-95 flex items-center gap-2 text-sm">
                  @if (isSubmitting()) {
                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Publicando...
                  } @else {
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                    Publicar Post
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
export class SocialModalComponent implements OnInit {
  @Output() close     = new EventEmitter<void>();
  @Output() published = new EventEmitter<any>();

  private fb      = inject(FormBuilder);
  private http    = inject(HttpClient);
  readonly publishService = inject(PublishService);
  readonly catalog        = inject(CatalogService);
  private readonly API_BASE = environment.apiUrl;

  isSubmitting  = signal(false);
  submitSuccess = signal(false);
  submitError   = signal<string | null>(null);
  
  nlpErrors     = signal<Record<string, boolean>>({});

  form: FormGroup = this.fb.group({
    title:      [''],
    content:    ['', Validators.required],
    make_id_ui: [''],
    model_id:   [''],
    engine_id:  [''],
  });

  ngOnInit(): void {
    this.catalog.loadMakes();
    this.form.get('make_id_ui')!.valueChanges.subscribe(makeId => {
      this.form.patchValue({ model_id: '', engine_id: '' });
      this.catalog.loadModels(makeId);
    });
  }

  getControl(name: string): AbstractControl { return this.form.get(name)!; }

  handleNlpStatus(field: string, status: 'valid' | 'invalid'): void {
    this.nlpErrors.update(v => ({ ...v, [field]: status === 'invalid' }));
  }

  hasNlpError = computed(() => Object.values(this.nlpErrors()).some(invalid => invalid));

  get canSubmit(): boolean {
    return this.form.valid && !this.publishService.isUploading() && !this.publishService.hasRejectedMedia() && !this.isSubmitting() && !this.hasNlpError();
  }

  async onSubmit(): Promise<void> {
    if (!this.canSubmit) return;
    this.isSubmitting.set(true);
    this.submitError.set(null);
    try {
      const payload = {
        title:     this.form.value.title || null,
        content:   this.form.value.content,
        model_id:  this.form.value.model_id || null,
        engine_id: this.form.value.engine_id || null,
        media:     this.publishService.approvedMediaUrls(),
      };
      const result = await this.http.post(`${this.API_BASE}/social`, payload).toPromise();
      this.submitSuccess.set(true);
      this.published.emit(result);
      setTimeout(() => this.handleClose(), 1500);
    } catch (err: any) {
      this.submitError.set(err?.error?.message ?? 'Error al publicar.');
    } finally {
      this.isSubmitting.set(false);
    }
  }

  handleClose(): void {
    this.publishService.reset();
    this.catalog.reset();
    this.submitSuccess.set(false);
    this.submitError.set(null);
    this.nlpErrors.set({});
    this.close.emit();
  }
}
