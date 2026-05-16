import { Component, Input, Output, EventEmitter, signal, computed, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule, FormControl } from '@angular/forms';
import { debounceTime, distinctUntilChanged } from 'rxjs/operators';
import { ComprehendService } from '../comprehend.service';

export type SmartFieldStatus = 'idle' | 'thinking' | 'success' | 'warning' | 'error';

@Component({
  selector: 'app-smart-field',
  standalone: true,
  imports: [CommonModule, FormsModule, ReactiveFormsModule],
  template: `
    <div class="flex flex-col gap-1.5 w-full">
      @if (label) {
        <label [for]="id" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-slate-200">
          {{ label }}
        </label>
      }
      
      <div class="relative group">
        <!-- Input Field with Dynamic Border -->
        <div 
          [class]="containerClasses()"
          class="flex h-10 w-full rounded-md border bg-slate-950 px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-slate-500 focus-within:ring-2 focus-within:ring-slate-400 focus-within:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-all duration-300"
        >
          @if (type === 'select') {
            <select 
              [id]="id"
              [formControl]="control"
              class="w-full bg-transparent border-none outline-none text-slate-100 appearance-none cursor-pointer"
            >
              @for (option of options; track option.value) {
                <option [value]="option.value" class="bg-slate-900">{{ option.label }}</option>
              }
            </select>
            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none opacity-50">
               <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
            </div>
          } @else {
            <input 
              [id]="id"
              [type]="type"
              [placeholder]="placeholder"
              [formControl]="control"
              class="w-full bg-transparent border-none outline-none text-slate-100 placeholder:text-slate-500"
            />
          }
        </div>

        <!-- AI Thinking Indicator (Subtle Pulse) -->
        @if (status === 'thinking') {
          <div class="absolute -bottom-0.5 left-0 h-0.5 bg-blue-500 animate-pulse w-full rounded-full blur-[1px]"></div>
        }
      </div>

      <!-- Feedback Messages -->
      <div class="h-4">
        @if (status === 'error' && errorMessage) {
          <span class="text-[11px] font-medium text-red-500 animate-in fade-in slide-in-from-top-1">
            {{ errorMessage }}
          </span>
        } @else if (status === 'warning' && warningMessage) {
          <span class="text-[11px] font-medium text-amber-500 animate-in fade-in slide-in-from-top-1">
            {{ warningMessage }}
          </span>
        } @else if (status === 'success' && successMessage) {
          <span class="text-[11px] font-medium text-emerald-500 animate-in fade-in slide-in-from-top-1 flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            {{ successMessage }}
          </span>
        }
      </div>
    </div>
  `,
  styles: [`
    :host { display: block; }
    select::-ms-expand { display: none; }
  `]
})
export class SmartFieldComponent implements OnInit {
  @Input() id = 'field-' + Math.random().toString(36).substring(2, 9);
  @Input() label = '';
  @Input() placeholder = '';
  @Input() type: 'text' | 'number' | 'select' | 'password' | 'email' | 'textarea' = 'text';
  @Input() control = new FormControl();
  @Input() options: { label: string, value: any }[] = [];
  @Input() status: SmartFieldStatus = 'idle';
  @Input() errorMessage = '';
  @Input() warningMessage = '';
  @Input() successMessage = '';
  
  @Input() enableNlp: boolean = false;
  @Output() nlpStatus = new EventEmitter<'valid' | 'invalid'>();

  private comprehendService = inject(ComprehendService);

  containerClasses = computed(() => {
    switch (this.status) {
      case 'thinking': return 'border-blue-500 shadow-[0_0_10px_rgba(59,130,246,0.3)]';
      case 'success': return 'border-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.2)]';
      case 'warning': return 'border-amber-500 shadow-[0_0_10px_rgba(245,158,11,0.2)]';
      case 'error': return 'border-red-500 shadow-[0_0_10px_rgba(239,68,68,0.2)]';
      default: return 'border-slate-800 focus-within:border-slate-400';
    }
  });

  ngOnInit() {
    if (this.enableNlp && (this.type === 'text' || this.type === 'textarea')) {
      this.control.valueChanges
        .pipe(
          debounceTime(800),
          distinctUntilChanged()
        )
        .subscribe((value: string) => {
          if (!value || value.trim().length === 0) {
            this.status = 'idle';
            this.errorMessage = '';
            this.warningMessage = '';
            this.nlpStatus.emit('valid');
            return;
          }

          this.status = 'thinking';
          this.comprehendService.analyze(value).subscribe({
            next: (res) => {
              this.status = res.status;
              if (res.status === 'warning') {
                this.warningMessage = res.warning || '';
                this.nlpStatus.emit('valid'); // warnings don't block
              } else if (res.status === 'success') {
                this.successMessage = 'Texto válido';
                this.nlpStatus.emit('valid');
              }
            },
            error: (err) => {
              this.status = 'error';
              this.errorMessage = err.error?.error || 'Error analizando texto';
              this.nlpStatus.emit('invalid');
            }
          });
        });
    }
  }
}
