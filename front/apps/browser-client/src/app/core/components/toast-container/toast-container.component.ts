import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ToastService, Toast } from '../../services/toast.service';

@Component({
  selector: 'app-toast-container',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 w-full max-w-[380px] pointer-events-none p-4 md:p-0">
      @for (toast of toastService.toasts(); track toast.id) {
        <div 
          [class]="toastClasses(toast)"
          class="pointer-events-auto flex items-start gap-3 p-4 rounded-xl border backdrop-blur-md shadow-2xl transition-all duration-300 animate-slide-in hover:scale-[1.02]"
        >
          <!-- SVG Icon based on type -->
          <div class="shrink-0 mt-0.5">
            @switch (toast.type) {
              @case ('success') {
                <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
              }
              @case ('error') {
                <svg class="w-5 h-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
              }
              @case ('warning') {
                <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                </svg>
              }
              @case ('info') {
                <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 111.063.852l-.708 2.836a.75.75 0 001.063.852l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                </svg>
              }
            }
          </div>

          <!-- Message Body -->
          <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold leading-tight text-white uppercase tracking-wider mb-0.5">
              {{ toast.type === 'success' ? 'Éxito' : toast.type === 'error' ? 'Error de Seguridad' : toast.type === 'warning' ? 'Advertencia de IA' : 'Información' }}
            </p>
            <p class="text-[11.5px] leading-relaxed text-slate-300">
              {{ toast.message }}
            </p>
          </div>

          <!-- Close Button -->
          <button 
            (click)="toastService.dismiss(toast.id)" 
            class="shrink-0 p-1 hover:bg-white/10 rounded-lg text-slate-400 hover:text-white transition-colors"
          >
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      }
    </div>
  `,
  styles: [`
    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateX(40px) scale(0.95);
      }
      to {
        opacity: 1;
        transform: translateX(0) scale(1);
      }
    }
    .animate-slide-in {
      animation: slideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
  `]
})
export class ToastContainerComponent {
  readonly toastService = inject(ToastService);

  toastClasses(toast: Toast): string {
    const base = 'bg-slate-950/90 border-slate-800 shadow-[0_8px_30px_rgb(0,0,0,0.12)]';
    switch (toast.type) {
      case 'success':
        return `${base} border-l-[3.5px] border-l-emerald-500`;
      case 'error':
        return `${base} border-l-[3.5px] border-l-red-500`;
      case 'warning':
        return `${base} border-l-[3.5px] border-l-amber-500`;
      case 'info':
        return `${base} border-l-[3.5px] border-l-blue-500`;
      default:
        return base;
    }
  }
}
