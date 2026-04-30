import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../core/services/auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  template: `
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-black flex items-center justify-center p-4 relative overflow-hidden">
      <!-- Fondo animado con gradientes neon -->
      <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-green-500/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s"></div>
      </div>

      <!-- Card principal -->
      <div class="relative w-full max-w-md">
        <div class="absolute -inset-1 bg-gradient-to-r from-green-500 to-purple-600 rounded-2xl blur opacity-20 group-hover:opacity-100 transition duration-1000"></div>
        
        <div class="relative bg-slate-900/80 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-8 shadow-2xl">
          <!-- Header -->
          <div class="text-center mb-8">
            <h1 class="text-4xl font-black tracking-tight">
              <span class="bg-gradient-to-r from-green-400 to-cyan-400 bg-clip-text text-transparent">IntellCar</span>
            </h1>
            <p class="text-slate-400 mt-2 text-sm">Donde vive la pasión del motor</p>
          </div>

          <!-- Form -->
          <form [formGroup]="loginForm" (ngSubmit)="onSubmit()" class="space-y-5">
            <!-- Email Input -->
            <div class="space-y-2">
              <label for="email" class="block text-sm font-medium text-slate-200">Email</label>
              <input
                id="email"
                type="email"
                formControlName="email"
                placeholder="tu@email.com"
                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500/50 focus:border-green-500 text-slate-100 placeholder-slate-500 transition"
              />
              <p *ngIf="email.invalid && email.touched" class="text-xs text-red-400">Email requerido</p>
            </div>

            <!-- Password Input -->
            <div class="space-y-2">
              <label for="password" class="block text-sm font-medium text-slate-200">Contraseña</label>
              <input
                id="password"
                type="password"
                formControlName="password"
                placeholder="••••••••"
                class="w-full px-4 py-3 bg-slate-800/50 border border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500/50 focus:border-green-500 text-slate-100 placeholder-slate-500 transition"
              />
              <p *ngIf="password.invalid && password.touched" class="text-xs text-red-400">Contraseña requerida</p>
            </div>

            <!-- Error Message -->
            <div *ngIf="errorMessage" class="p-3 bg-red-500/20 border border-red-500/50 rounded-lg text-red-300 text-sm">
              {{ errorMessage }}
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              [disabled]="!loginForm.valid || loading"
              class="w-full py-3 px-4 bg-gradient-to-r from-green-500 to-cyan-500 hover:from-green-400 hover:to-cyan-400 disabled:opacity-50 disabled:cursor-not-allowed text-slate-900 font-bold rounded-lg transition duration-200 shadow-lg shadow-green-500/50"
            >
              <span *ngIf="!loading">Iniciar Sesión</span>
              <span *ngIf="loading" class="flex items-center justify-center gap-2">
                <span class="w-4 h-4 border-2 border-transparent border-t-slate-900 rounded-full animate-spin"></span>
                Autenticando...
              </span>
            </button>
          </form>

          <!-- Demo Users -->
          <div class="mt-8 pt-6 border-t border-slate-700">
            <p class="text-xs text-slate-400 text-center mb-3">Demo - Usuarios disponibles:</p>
            <div class="space-y-2 text-xs">
              <p class="text-slate-400"><span class="text-green-400">admin@intellcar.com</span> / password123</p>
              <p class="text-slate-400"><span class="text-green-400">carlos@ferrari.com</span> / password123</p>
              <p class="text-slate-400"><span class="text-green-400">maria.gonzalez@email.com</span> / password123</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  `,
  styles: [`
    :host {
      display: block;
      width: 100%;
      height: 100%;
    }
  `]
})
export class LoginComponent {
  private fb = inject(FormBuilder);
  private authService = inject(AuthService);
  private router = inject(Router);

  loginForm: FormGroup;
  loading = false;
  errorMessage = '';

  constructor() {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', Validators.required]
    });
  }

  get email() {
    return this.loginForm.get('email')!;
  }

  get password() {
    return this.loginForm.get('password')!;
  }

  onSubmit(): void {
    if (this.loginForm.invalid) return;

    this.loading = true;
    this.errorMessage = '';

    const { email, password } = this.loginForm.value;
    this.authService.login(email, password).subscribe({
      next: () => {
        this.loading = false;
        this.router.navigate(['/dashboard']);
      },
      error: (error) => {
        this.loading = false;
        this.errorMessage = error.message || 'Email o contraseña incorrectos';
      }
    });
  }
}
