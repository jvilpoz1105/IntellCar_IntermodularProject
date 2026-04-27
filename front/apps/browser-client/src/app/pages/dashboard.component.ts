import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterOutlet } from '@angular/router';
import { SidebarComponent } from '../shared/components/sidebar.component';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule, SidebarComponent],
  template: `
    <div class="flex h-screen bg-slate-900 text-slate-100">
      <!-- Sidebar -->
      <app-sidebar></app-sidebar>

      <!-- Main Content -->
      <main class="flex-1 overflow-hidden flex flex-col">
        <!-- Header -->
        <header class="bg-slate-800/50 backdrop-blur-xl border-b border-slate-700/50 px-8 py-6">
          <div class="flex items-center justify-between">
            <h1 class="text-2xl font-black bg-gradient-to-r from-green-400 to-cyan-400 bg-clip-text text-transparent">
              Dashboard
            </h1>
            <div class="flex items-center gap-4">
              <button class="p-2 rounded-lg hover:bg-slate-700 transition">
                🔔
              </button>
              <button class="p-2 rounded-lg hover:bg-slate-700 transition">
                ⚙️
              </button>
            </div>
          </div>
        </header>

        <!-- Content Area -->
        <div class="flex-1 overflow-auto">
          <div class="p-8">
            <!-- Welcome Card -->
            <div class="mb-8 p-6 bg-gradient-to-br from-green-500/10 to-cyan-500/10 border border-green-500/30 rounded-xl">
              <h2 class="text-xl font-bold text-slate-100 mb-2">¡Bienvenido a tu Dashboard!</h2>
              <p class="text-slate-400">Desde aquí puedes acceder a todas las funcionalidades de IntellCar. Navega por el menú lateral para explorar.</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
              <div class="stat-card">
                <div class="flex items-center justify-between mb-4">
                  <span class="text-3xl">🏎️</span>
                  <span class="text-xs font-bold text-green-400">+12%</span>
                </div>
                <p class="text-slate-400 text-sm">Vehículos</p>
                <p class="text-2xl font-bold">24</p>
              </div>

              <div class="stat-card">
                <div class="flex items-center justify-between mb-4">
                  <span class="text-3xl">👥</span>
                  <span class="text-xs font-bold text-cyan-400">+5%</span>
                </div>
                <p class="text-slate-400 text-sm">Seguidores</p>
                <p class="text-2xl font-bold">1,234</p>
              </div>

              <div class="stat-card">
                <div class="flex items-center justify-between mb-4">
                  <span class="text-3xl">📅</span>
                  <span class="text-xs font-bold text-purple-400">+3</span>
                </div>
                <p class="text-slate-400 text-sm">Eventos</p>
                <p class="text-2xl font-bold">8</p>
              </div>

              <div class="stat-card">
                <div class="flex items-center justify-between mb-4">
                  <span class="text-3xl">⭐</span>
                  <span class="text-xs font-bold text-yellow-400">4.8/5</span>
                </div>
                <p class="text-slate-400 text-sm">Valoración</p>
                <p class="text-2xl font-bold">98%</p>
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-slate-800/50 backdrop-blur border border-slate-700/50 rounded-xl p-6">
              <h3 class="text-lg font-bold mb-4">Acciones Rápidas</h3>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button class="group p-4 bg-slate-700/30 hover:bg-slate-600/50 border border-slate-600 rounded-lg transition text-left">
                  <p class="text-2xl mb-2">📸</p>
                  <p class="font-semibold group-hover:text-green-400 transition">Nuevo Post</p>
                  <p class="text-sm text-slate-400">Comparte tu contenido</p>
                </button>

                <button class="group p-4 bg-slate-700/30 hover:bg-slate-600/50 border border-slate-600 rounded-lg transition text-left">
                  <p class="text-2xl mb-2">🚗</p>
                  <p class="font-semibold group-hover:text-blue-400 transition">Vender Coche</p>
                  <p class="text-sm text-slate-400">Publica tu vehículo</p>
                </button>

                <button class="group p-4 bg-slate-700/30 hover:bg-slate-600/50 border border-slate-600 rounded-lg transition text-left">
                  <p class="text-2xl mb-2">📅</p>
                  <p class="font-semibold group-hover:text-purple-400 transition">Crear Evento</p>
                  <p class="text-sm text-slate-400">Organiza una quedada</p>
                </button>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <style>
      .stat-card {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.8), rgba(15, 23, 42, 0.8));
        border: 1px solid rgba(71, 85, 105, 0.3);
        backdrop-filter: blur(8px);
        padding: 1.5rem;
        border-radius: 0.75rem;
        transition: all 0.3s ease;
      }

      .stat-card:hover {
        border-color: rgba(34, 197, 94, 0.5);
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(34, 197, 94, 0.1);
      }

      ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
      }

      ::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, 0.5);
      }

      ::-webkit-scrollbar-thumb {
        background: rgba(71, 85, 105, 0.5);
        border-radius: 4px;
      }

      ::-webkit-scrollbar-thumb:hover {
        background: rgba(71, 85, 105, 0.8);
      }
    </style>
  `
})
export class DashboardComponent {}
