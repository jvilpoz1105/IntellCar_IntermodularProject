import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../core/services/auth.service';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  template: `
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-black flex items-center justify-center p-4 relative overflow-hidden">
      <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-green-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1.5s"></div>
      </div>

      <div class="relative w-full max-w-2xl">
        <div class="absolute -inset-1 bg-gradient-to-r from-cyan-500 to-green-500 rounded-2xl blur opacity-20 transition duration-1000"></div>

        <div class="relative bg-slate-900/85 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-8 shadow-2xl">
          <div class="text-center mb-8">
            <h1 class="text-4xl font-black tracking-tight">
              <span class="bg-gradient-to-r from-cyan-400 to-green-400 bg-clip-text text-transparent">Crear Cuenta</span>
            </h1>
            <p class="text-slate-400 mt-2 text-sm">Únete a IntellCar y empieza a mover tu garaje</p>
          </div>

          <form [formGroup]="registerForm" (ngSubmit)="onSubmit()" class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="space-y-2 md:col-span-1">
              <label for="user_name" class="block text-sm font-medium text-slate-200">Nombre</label>
              <input id="user_name" type="text" formControlName="user_name" placeholder="Tu nombre"
                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 text-slate-100 placeholder-slate-500 transition" />
            </div>

            <div class="space-y-2 md:col-span-1">
              <label for="phone" class="block text-sm font-medium text-slate-200">Teléfono</label>
              <input id="phone" type="text" formControlName="phone" placeholder="+34600000000"
                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 text-slate-100 placeholder-slate-500 transition" />
            </div>

            <div class="space-y-2 md:col-span-1">
              <label for="email_address" class="block text-sm font-medium text-slate-200">Email</label>
              <input id="email_address" type="email" formControlName="email_address" placeholder="tu@email.com"
                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 text-slate-100 placeholder-slate-500 transition" />
            </div>

            <div class="space-y-2 md:col-span-1">
              <label for="contact_email" class="block text-sm font-medium text-slate-200">Email de contacto</label>
              <input id="contact_email" type="email" formControlName="contact_email" placeholder="Opcional"
                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 text-slate-100 placeholder-slate-500 transition" />
            </div>

            <div class="space-y-2 md:col-span-1">
              <label for="user_password" class="block text-sm font-medium text-slate-200">Contraseña</label>
              <input id="user_password" type="password" formControlName="user_password" placeholder="Mínimo 6 caracteres"
                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 text-slate-100 placeholder-slate-500 transition" />
            </div>

            <div class="space-y-2 md:col-span-1">
              <label for="confirm_password" class="block text-sm font-medium text-slate-200">Confirmar contraseña</label>
              <input id="confirm_password" type="password" formControlName="confirm_password" placeholder="Repite la contraseña"
                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 text-slate-100 placeholder-slate-500 transition" />
            </div>

            <div class="space-y-2 md:col-span-2">
              <label for="address" class="block text-sm font-medium text-slate-200">Dirección</label>
              <input id="address" type="text" formControlName="address" placeholder="Opcional"
                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 text-slate-100 placeholder-slate-500 transition" />
            </div>

            <div *ngIf="errorMessage" class="md:col-span-2 p-3 bg-red-500/20 border border-red-500/50 rounded-lg text-red-300 text-sm">
              {{ errorMessage }}
            </div>

            <div *ngIf="passwordMismatch" class="md:col-span-2 p-3 bg-amber-500/20 border border-amber-500/50 rounded-lg text-amber-200 text-sm">
              Las contraseñas no coinciden.
            </div>

            <div class="md:col-span-2 flex flex-col gap-4">
              <button type="submit" [disabled]="registerForm.invalid || loading || passwordMismatch"
                class="w-full py-3 px-4 bg-gradient-to-r from-cyan-500 to-green-500 hover:from-cyan-400 hover:to-green-400 disabled:opacity-50 disabled:cursor-not-allowed text-slate-950 font-bold rounded-lg transition duration-200 shadow-lg shadow-cyan-500/40">
                <span *ngIf="!loading">Crear Cuenta</span>
                <span *ngIf="loading" class="flex items-center justify-center gap-2">
                  <span class="w-4 h-4 border-2 border-transparent border-t-slate-900 rounded-full animate-spin"></span>
                  Registrando...
                </span>
              </button>

              <div class="text-center text-sm text-slate-400">
                ¿Ya tienes cuenta?
                <a routerLink="/login" class="ml-1 font-semibold text-cyan-300 hover:text-cyan-200 transition">Inicia sesión</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  `,
})
export class RegisterComponent {
  private fb = inject(FormBuilder);
  private authService = inject(AuthService);
  private router = inject(Router);

  registerForm = this.fb.group({
    user_name: ['', [Validators.required, Validators.maxLength(90)]],
    email_address: ['', [Validators.required, Validators.email]],
    phone: ['', [Validators.required]],
    user_password: ['', [Validators.required, Validators.minLength(6)]],
    confirm_password: ['', [Validators.required]],
    contact_email: ['', [Validators.email]],
    address: [''],
  });

  loading = false;
  errorMessage = '';

  get passwordMismatch(): boolean {
    const password = this.registerForm.get('user_password')?.value;
    const confirmPassword = this.registerForm.get('confirm_password')?.value;
    return !!password && !!confirmPassword && password !== confirmPassword;
  }

  onSubmit(): void {
    if (this.registerForm.invalid || this.passwordMismatch) {
      this.registerForm.markAllAsTouched();
      return;
    }

    this.loading = true;
    this.errorMessage = '';

    const formValue = this.registerForm.getRawValue();

    this.authService.register({
      user_name: formValue.user_name || '',
      email_address: formValue.email_address || '',
      phone: formValue.phone || '',
      user_password: formValue.user_password || '',
      contact_email: formValue.contact_email || undefined,
      address: formValue.address || undefined,
      paddock_id: null,
    }).subscribe({
      next: () => {
        this.loading = false;
        this.router.navigate(['/dashboard']);
      },
      error: (error) => {
        this.loading = false;
        this.errorMessage = error.message || 'No se pudo completar el registro';
      }
    });
  }
}