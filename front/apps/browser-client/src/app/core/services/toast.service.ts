import { Injectable, signal } from '@angular/core';

export interface Toast {
  id: string;
  type: 'success' | 'error' | 'warning' | 'info';
  message: string;
  duration?: number;
}

@Injectable({
  providedIn: 'root'
})
export class ToastService {
  private activeToasts = signal<Toast[]>([]);

  // Selector para obtener la lista de toasts activos
  readonly toasts = this.activeToasts.asReadonly();

  show(type: Toast['type'], message: string, duration = 6000) {
    const id = Math.random().toString(36).substring(2, 9);
    const newToast: Toast = { id, type, message, duration };

    // Añadir el nuevo toast a la pila
    this.activeToasts.update(prev => [...prev, newToast]);

    // Programar su auto-eliminación
    if (duration > 0) {
      setTimeout(() => {
        this.dismiss(id);
      }, duration);
    }
  }

  showSuccess(message: string, duration = 5000) {
    this.show('success', message, duration);
  }

  showError(message: string, duration = 6000) {
    this.show('error', message, duration);
  }

  showWarning(message: string, duration = 7000) {
    this.show('warning', message, duration);
  }

  showInfo(message: string, duration = 5000) {
    this.show('info', message, duration);
  }

  dismiss(id: string) {
    this.activeToasts.update(prev => prev.filter(t => t.id !== id));
  }
}
