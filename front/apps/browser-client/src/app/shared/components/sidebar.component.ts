import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { DomSanitizer, SafeHtml } from '@angular/platform-browser';
import { AuthService, User } from '../../core/services/auth.service';

interface NavItem {
  label: string;
  route: string;
  icon: string;
  color: string;
}

@Component({
  selector: 'app-sidebar',
  standalone: true,
  imports: [CommonModule, RouterLink, RouterLinkActive],
  template: `
    <div class="w-64 bg-gradient-to-b from-slate-900/95 to-slate-800/95 backdrop-blur-xl border-r border-slate-700/50 h-screen flex flex-col shadow-2xl">
      <!-- Logo Section -->
      <div class="p-6 border-b border-slate-700/50">
        <div class="flex items-center gap-3 mb-2">
          <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-400 to-cyan-400 flex items-center justify-center text-slate-900">
            <span [innerHTML]="s(zapIcon)"></span>
          </div>
          <div>
            <h2 class="font-black text-lg bg-gradient-to-r from-green-400 to-cyan-400 bg-clip-text text-transparent">IntellCar</h2>
            <p class="text-xs text-slate-400">v0.0.1</p>
          </div>
        </div>
      </div>

      <!-- User Profile Section -->
      <div class="px-6 py-4 border-b border-slate-700/50">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 uppercase select-none">
            {{ getInitial(currentUser?.user_name) }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-slate-100 truncate">{{ currentUser?.user_name || 'Usuario' }}</p>
            <p class="text-xs text-slate-400 truncate">{{ currentUser?.email_address || 'email@mail.com' }}</p>
          </div>
        </div>
      </div>

      <!-- Navigation Items -->
      <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <a
          *ngFor="let item of navItems"
          [routerLink]="item.route"
          routerLinkActive="active"
          [routerLinkActiveOptions]="{ exact: true }"
          class="nav-item group flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:text-slate-100 transition-all duration-200 hover:bg-slate-700/50"
          [ngClass]="item.color"
        >
          <span [innerHTML]="s(item.icon)" class="flex-shrink-0 transition-transform group-hover:scale-110"></span>
          <span class="font-medium">{{ item.label }}</span>
          <span class="ml-auto w-2 h-2 rounded-full bg-gradient-to-r from-green-400 to-cyan-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
        </a>
      </nav>

      <!-- Footer -->
      <div class="border-t border-slate-700/50 p-4 space-y-2">
        <button
          (click)="onProfile()"
          class="w-full flex items-center gap-2 px-3 py-2 text-sm text-slate-300 hover:text-slate-100 rounded-lg hover:bg-slate-700/50 transition-all"
        >
          <span [innerHTML]="s(settingsIcon)" class="flex-shrink-0"></span>
          <span>Mi Perfil</span>
        </button>
        <button
          (click)="onLogout()"
          class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-400 hover:text-red-300 rounded-lg hover:bg-red-500/10 transition-all"
        >
          <span [innerHTML]="s(logoutIcon)" class="flex-shrink-0"></span>
          <span>Cerrar Sesión</span>
        </button>
      </div>
    </div>

    <style>
      .nav-item.active {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(34, 211, 238, 0.1));
        border-left: 3px solid #22c55e;
      }

      nav::-webkit-scrollbar {
        width: 6px;
      }

      nav::-webkit-scrollbar-track {
        background: transparent;
      }

      nav::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.3);
        border-radius: 3px;
      }

      nav::-webkit-scrollbar-thumb:hover {
        background: rgba(148, 163, 184, 0.5);
      }
    </style>
  `
})
export class SidebarComponent {
  private authService = inject(AuthService);
  private router = inject(Router);
  private sanitizer = inject(DomSanitizer);

  currentUser: User | null = null;

  private svg = (paths: string) =>
    `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${paths}</svg>`;

  s(html: string): SafeHtml {
    return this.sanitizer.bypassSecurityTrustHtml(html);
  }

  zapIcon = this.svg('<path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/>');
  settingsIcon = this.svg('<path d="M20 7h-9"/><path d="M14 17H5"/><circle cx="17" cy="17" r="3"/><circle cx="7" cy="7" r="3"/>');
  logoutIcon = this.svg('<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/>');

  navItems: NavItem[] = [
    { label: 'Dashboard', route: '/dashboard', icon: this.svg('<rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/>'), color: 'hover:text-green-400' },
    { label: 'Marketplace', route: '/dashboard/marketplace', icon: this.svg('<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" x2="21" y1="6" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>'), color: 'hover:text-blue-400' },
    { label: 'Eventos', route: '/dashboard/events', icon: this.svg('<rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/>'), color: 'hover:text-yellow-400' },
    { label: 'Universo', route: '/dashboard/universe', icon: this.svg('<path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/>'), color: 'hover:text-purple-400' },
    { label: 'Mi Garaje', route: '/dashboard/garage', icon: this.svg('<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>'), color: 'hover:text-orange-400' }
  ];

  constructor() {
    this.authService.getCurrentUser().subscribe((user) => {
      this.currentUser = user;
    });
  }

  getInitial(username?: string): string {
    if (!username) return '?';
    return username.charAt(0).toUpperCase();
  }

  onProfile(): void {
    this.router.navigate(['/dashboard/profile']);
  }

  onLogout(): void {
    this.authService.logout();
    this.router.navigate(['/login']);
  }
}
