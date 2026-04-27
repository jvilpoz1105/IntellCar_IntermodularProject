import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-marketplace',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="p-8">
      <h2 class="text-3xl font-bold mb-4 bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">🏪 Marketplace</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div *ngFor="let i of [1,2,3,4,5,6]" class="bg-slate-800/50 rounded-lg overflow-hidden hover:scale-105 transition">
          <div class="h-48 bg-gradient-to-br from-slate-700 to-slate-900 flex items-center justify-center text-4xl">🚗</div>
          <div class="p-4">
            <p class="font-bold">Vehículo {{ i }}</p>
            <p class="text-slate-400 text-sm">Marca • Modelo</p>
            <p class="text-green-400 font-bold mt-2">45.000€</p>
          </div>
        </div>
      </div>
    </div>
  `
})
export class MarketplaceComponent {}

@Component({
  selector: 'app-events',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="p-8">
      <h2 class="text-3xl font-bold mb-4 bg-gradient-to-r from-yellow-400 to-orange-400 bg-clip-text text-transparent">📅 Eventos</h2>
      <div class="space-y-4">
        <div *ngFor="let i of [1,2,3,4]" class="bg-slate-800/50 p-6 rounded-lg border border-slate-700 hover:border-yellow-500/50 transition">
          <div class="flex items-center gap-4">
            <span class="text-4xl">🏁</span>
            <div class="flex-1">
              <p class="font-bold text-lg">Evento {{ i }} - Meet & Greet</p>
              <p class="text-slate-400">Lugar: Autódromo de Barcelona • 12 de Mayo</p>
              <p class="text-sm text-yellow-400 mt-2">{{ 25 + i * 3 }} personas confirmadas</p>
            </div>
            <button class="px-4 py-2 bg-yellow-500/20 hover:bg-yellow-500/40 text-yellow-300 rounded-lg transition">
              Unirse
            </button>
          </div>
        </div>
      </div>
    </div>
  `
})
export class EventsComponent {}

@Component({
  selector: 'app-universe',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="p-8">
      <h2 class="text-3xl font-bold mb-4 bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">🌌 El Universo</h2>
      <div class="space-y-4">
        <div *ngFor="let i of [1,2,3,4,5]" class="bg-slate-800/50 p-6 rounded-lg border border-slate-700 hover:border-purple-500/50 transition">
          <div class="flex items-start gap-4">
            <span class="text-3xl">{{ ['👨‍🔧', '👩‍🔧', '🏎️', '🚗', '⚙️'][i-1] }}</span>
            <div class="flex-1">
              <p class="font-bold">Usuario {{ i }} (@user{{ i }})</p>
              <p class="text-slate-400 mt-2">Acabo de modificar mi BMW e36 con nuevos rines de 19 pulgadas...</p>
              <div class="flex gap-3 mt-3 text-slate-400 text-sm">
                <button class="hover:text-red-400 transition">❤️ {{ 120 + i * 10 }}</button>
                <button class="hover:text-blue-400 transition">💬 {{ 20 + i * 5 }}</button>
                <button class="hover:text-green-400 transition">🔗 Compartir</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `
})
export class UniverseComponent {}

@Component({
  selector: 'app-garage',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="p-8">
      <h2 class="text-3xl font-bold mb-4 bg-gradient-to-r from-orange-400 to-red-400 bg-clip-text text-transparent">🛠️ Mi Garaje</h2>
      <div class="mb-6">
        <button class="px-6 py-3 bg-orange-500/20 hover:bg-orange-500/40 text-orange-300 rounded-lg transition font-bold">
          + Agregar Vehículo
        </button>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div *ngFor="let car of ['Mi Nissan R34', 'Subaru STI', 'Toyota Supra']" class="bg-slate-800/50 rounded-lg overflow-hidden border border-orange-500/30 hover:border-orange-500/80 transition">
          <div class="h-40 bg-gradient-to-br from-orange-600 to-red-900 flex items-center justify-center text-5xl">🏎️</div>
          <div class="p-4">
            <p class="font-bold text-lg">{{ car }}</p>
            <p class="text-slate-400 text-sm">2006 • 280 CV • Stock</p>
            <p class="text-orange-400 font-bold mt-2">⭐ 4.8/5 - 156 likes</p>
          </div>
        </div>
      </div>
    </div>
  `
})
export class GarageComponent {}

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="p-8">
      <h2 class="text-3xl font-bold mb-6 bg-gradient-to-r from-green-400 to-cyan-400 bg-clip-text text-transparent">⚙️ Mi Perfil</h2>
      <div class="max-w-2xl space-y-6">
        <div class="bg-slate-800/50 p-6 rounded-lg border border-slate-700">
          <h3 class="font-bold mb-4">Información Personal</h3>
          <div class="space-y-4">
            <div>
              <label class="text-sm text-slate-400">Nombre</label>
              <p class="font-semibold">Nombre del Usuario</p>
            </div>
            <div>
              <label class="text-sm text-slate-400">Email</label>
              <p class="font-semibold">usuario@email.com</p>
            </div>
            <div>
              <label class="text-sm text-slate-400">Tipo de Perfil</label>
              <p class="font-semibold">🏎️ Entusiasta</p>
            </div>
          </div>
        </div>

        <div class="bg-slate-800/50 p-6 rounded-lg border border-slate-700">
          <h3 class="font-bold mb-4">Configuración</h3>
          <button class="w-full text-left px-4 py-2 hover:bg-slate-700 rounded transition">
            🔔 Notificaciones
          </button>
          <button class="w-full text-left px-4 py-2 hover:bg-slate-700 rounded transition">
            🔒 Privacidad
          </button>
          <button class="w-full text-left px-4 py-2 hover:bg-slate-700 rounded transition">
            🌙 Apariencia
          </button>
        </div>
      </div>
    </div>
  `
})
export class ProfileComponent {}
