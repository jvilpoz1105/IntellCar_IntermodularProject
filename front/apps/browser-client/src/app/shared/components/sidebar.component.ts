import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
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
          <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-400 to-cyan-400 flex items-center justify-center font-black text-slate-900">
            ⚡
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
          <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-lg">
            {{ currentUser?.avatar }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-slate-100 truncate">{{ currentUser?.name }}</p>
            <p class="text-xs text-slate-400 truncate">{{ currentUser?.email }}</p>
          </div>
        </div>
        <span class="mt-2 inline-block px-2 py-1 text-xs font-bold bg-gradient-to-r from-green-500/30 to-cyan-500/30 text-green-300 rounded border border-green-500/30">
          {{ profileLabel }}
        </span>
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
          <span class="text-xl group-hover:scale-110 transition-transform">{{ item.icon }}</span>
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
          <span>⚙️</span>
          <span>Mi Perfil</span>
        </button>
        <button
          (click)="onLogout()"
          class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-400 hover:text-red-300 rounded-lg hover:bg-red-500/10 transition-all"
        >
          <span>🚪</span>
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

  currentUser: User | null = null;

  navItems: NavItem[] = [
    { label: 'Dashboard', route: '/dashboard', icon: '📊', color: 'hover:text-green-400' },
    { label: 'Marketplace', route: '/dashboard/marketplace', icon: '🏪', color: 'hover:text-blue-400' },
    { label: 'Eventos', route: '/dashboard/events', icon: '📅', color: 'hover:text-yellow-400' },
    { label: 'Universo', route: '/dashboard/universe', icon: '🌌', color: 'hover:text-purple-400' },
    { label: 'Mi Garaje', route: '/dashboard/garage', icon: '🛠️', color: 'hover:text-orange-400' }
  ];

  get profileLabel(): string {
    const labels: { [key: string]: string } = {
      admin: '👨‍💼 Admin',
      professional: '🏢 Profesional',
      individual: '👤 Individual',
      tuner: '🔧 Tuner',
      press: '📰 Prensa'
    };
    return labels[this.currentUser?.profile || 'individual'] || 'Usuario';
  }

  constructor() {
    this.authService.getCurrentUser().subscribe((user) => {
      this.currentUser = user;
    });
  }

  onProfile(): void {
    this.router.navigate(['/dashboard/profile']);
  }

  onLogout(): void {
    this.authService.logout();
    this.router.navigate(['/login']);
  }
}
