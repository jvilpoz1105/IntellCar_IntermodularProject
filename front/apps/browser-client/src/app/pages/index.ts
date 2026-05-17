import { Component, OnInit, inject, ElementRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient, HttpParams } from '@angular/common/http';
import { AuthService, User } from '../core/services/auth.service';
import { API_CONFIG } from '../core/config/api.config';
import gsap from 'gsap';
import { RelativeTimePipe } from '../shared/pipes/relative-time.pipe';
import { PublishModalComponent } from '../features/publish/publish-modal/publish-modal.component';
import { SocialModalComponent } from '../features/publish/social-modal/social-modal.component';
import { KddModalComponent } from '../features/publish/kdd-modal/kdd-modal.component';
import { GarageModalComponent } from '../features/publish/garage-modal/garage-modal.component';
import { signal } from '@angular/core';
import { toast } from 'ngx-sonner';

interface PaginatedResponse<T> {
  data: T[];
  total?: number;
  meta?: {
    total: number;
    current_page: number;
    last_page: number;
    per_page: number;
  };
}

interface AdvertMedia {
  media_id: number;
  media_url: string;
  media_type: 'image' | 'video';
}

interface MarketplaceAdvert {
  advert_id: number;
  ad_title: string;
  price: number;
  city?: string;
  region?: string;
  make_name?: string;
  model_name?: string;
  media?: AdvertMedia | null;
}

interface UniversePost {
  post_id: number;
  title?: string;
  content_excerpt?: string;
  created_at?: string;
  author?: {
    user_id?: number;
    username?: string;
    profile_picture?: string;
  };
  media?: AdvertMedia | null;
}

interface EventKdd {
  event_id: number;
  title: string;
  event_date: string;
  city?: string;
  is_attending?: boolean;
  attendees_count?: number;
  max_participants?: number;
}

interface Paddock {
  paddock_id: number;
  paddock_name: string;
  paddock_description?: string;
}

interface ProfileDetail {
  user_id: number;
  user_name: string;
  email_address: string;
  contact_email?: string;
  address?: string;
  phone?: string;
  user_tag?: string;
  registration_date?: string;
  paddock_id?: number | null;
  paddock?: {
    paddock_id?: number;
    paddock_name?: string;
  };
  posts?: unknown[];
  garage?: unknown[];
}

@Component({
  selector: 'app-marketplace',
  standalone: true,
  imports: [CommonModule, PublishModalComponent],
  template: `
    <div class="p-8">
      <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">Marketplace</h2>
        <div class="flex gap-2">
          <button
            (click)="showPublishModal.set(true)"
            class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold transition"
          >
            + Crear anuncio
          </button>
          
          <!-- Modal de Publicación -->
          @if (showPublishModal()) {
            <app-publish-modal 
              type="market" 
              (close)="showPublishModal.set(false)"
              (published)="onPublished()"
            />
          }
          <button
            (click)="setVisibleOnly(true)"
            class="px-3 py-2 rounded-lg border text-sm"
            [ngClass]="visibleOnly ? 'bg-cyan-500/20 border-cyan-400 text-cyan-200' : 'bg-slate-800/60 border-slate-700 text-slate-300'"
          >
            Solo visibles
          </button>
          <button
            (click)="setVisibleOnly(false)"
            class="px-3 py-2 rounded-lg border text-sm"
            [ngClass]="!visibleOnly ? 'bg-cyan-500/20 border-cyan-400 text-cyan-200' : 'bg-slate-800/60 border-slate-700 text-slate-300'"
          >
            Todos
          </button>
          <button
            (click)="reload()"
            class="px-3 py-2 rounded-lg border bg-slate-800/60 border-slate-700 text-slate-200 text-sm hover:bg-slate-700/60"
          >
            Recargar
          </button>
        </div>
      </div>

      <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-3">
        <button
          *ngFor="let type of adTypes"
          (click)="setType(type.value)"
          class="px-3 py-2 rounded-lg border text-sm"
          [ngClass]="selectedType === type.value ? 'bg-blue-500/20 border-blue-400 text-blue-200' : 'bg-slate-800/60 border-slate-700 text-slate-300'"
        >
          {{ type.label }}
        </button>
      </div>

      <!-- Skeleton loader -->
      <div *ngIf="loading" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <div *ngFor="let i of [1,2,3,4,5,6]" class="bg-slate-800/50 rounded-lg overflow-hidden border border-slate-700 animate-pulse">
          <div class="h-48 bg-slate-700/60"></div>
          <div class="p-4 space-y-3">
            <div class="h-4 bg-slate-700/60 rounded w-3/4"></div>
            <div class="h-3 bg-slate-700/60 rounded w-1/2"></div>
            <div class="h-3 bg-slate-700/60 rounded w-2/3"></div>
            <div class="h-6 bg-slate-700/60 rounded w-1/3 mt-4"></div>
          </div>
        </div>
      </div>

      <div *ngIf="errorMessage" class="rounded-lg border border-red-500/40 bg-red-500/10 p-4 text-red-200 mb-4">
        {{ errorMessage }}
      </div>

      <div *ngIf="!loading" class="mb-4 text-sm text-slate-400">
        Total anuncios: <span class="text-slate-100 font-semibold">{{ total }}</span>
      </div>

      <!-- Empty state -->
      <div *ngIf="!loading && adverts.length === 0 && !errorMessage" class="flex flex-col items-center justify-center py-20 text-slate-400">
        <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" class="mb-4 text-slate-600"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" x2="21" y1="6" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        <p class="text-lg font-semibold text-slate-300">No hay anuncios disponibles</p>
        <p class="text-sm mt-1">Prueba a cambiar los filtros o vuelve más tarde.</p>
      </div>

      <div *ngIf="!loading && adverts.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <article *ngFor="let advert of adverts" (click)="openDetail(advert.advert_id)" class="advert-card cursor-pointer bg-slate-800/50 rounded-lg overflow-hidden border border-slate-700 hover:border-cyan-500/50 transition hover:scale-[1.02] transition-transform">
          <div class="h-48 bg-slate-900">
            <img
              *ngIf="getCover(advert) as imageUrl; else noImage"
              [src]="imageUrl"
              [alt]="advert.ad_title"
              class="h-full w-full object-cover"
            />
            <ng-template #noImage>
              <div class="h-full w-full flex items-center justify-center text-slate-500 text-sm">Sin imagen</div>
            </ng-template>
          </div>
          <div class="p-4">
            <p class="font-bold text-slate-100">{{ advert.ad_title }}</p>
            <p class="text-sm text-slate-400">
              {{ advert.make_name || 'Marca' }} • {{ advert.model_name || 'Modelo' }}
            </p>
            <p class="text-sm text-slate-400">{{ advert.city || 'Ciudad' }}, {{ advert.region || 'Región' }}</p>
            <div class="mt-3 flex items-center justify-between">
              <span class="inline-block px-3 py-1.5 bg-green-900/70 border border-green-600/50 rounded-lg text-green-300 text-xl font-black tracking-tight">
                {{ advert.price | number:'1.0-0' }} €
              </span>
            </div>
          </div>
        </article>
      </div>

      <!-- Marketplace Detail Modal -->
      <div *ngIf="selected || detailLoading" class="fixed inset-0 z-50 flex items-center justify-center p-4" (click)="closeDetail()">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
        <div class="relative z-10 w-full max-w-2xl max-h-[90vh] overflow-y-auto bg-slate-900 rounded-xl border border-slate-700 shadow-2xl" (click)="$event.stopPropagation()">
          <div *ngIf="detailLoading" class="flex items-center justify-center p-16">
            <svg class="animate-spin text-cyan-400" xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v8z" class="opacity-75"/></svg>
          </div>
          <ng-container *ngIf="selected && !detailLoading">
            <div class="relative h-64 bg-slate-950 rounded-t-xl overflow-hidden">
              <img *ngIf="getFirstMedia(selected.media) as img; else noDetailImg" [src]="img" [alt]="selected.ad_title" class="h-full w-full object-cover"/>
              <ng-template #noDetailImg><div class="h-full flex items-center justify-center text-slate-600 text-sm">Sin imágenes</div></ng-template>
              <button (click)="closeDetail()" class="absolute top-3 right-3 w-8 h-8 rounded-full bg-black/60 hover:bg-black/80 text-white flex items-center justify-center text-sm font-bold transition">✕</button>
            </div>
            <div class="p-6 space-y-4">
              <div class="flex items-start justify-between gap-4 flex-wrap">
                <h3 class="text-xl font-bold text-slate-100">{{ selected.ad_title }}</h3>
                <span class="px-3 py-1.5 bg-green-900/70 border border-green-600/50 rounded-lg text-green-300 text-xl font-black tracking-tight flex-shrink-0">{{ selected.price | number:'1.0-0' }} €</span>
              </div>
              <div class="flex flex-wrap gap-2">
                <span *ngIf="selected.model?.make?.make_name" class="px-2 py-1 bg-slate-800 border border-slate-700 rounded text-xs text-slate-300">{{ selected.model.make.make_name }} {{ selected.model?.model_name }}</span>
                <span *ngIf="selected.year_manufacture" class="px-2 py-1 bg-slate-800 border border-slate-700 rounded text-xs text-slate-300">{{ selected.year_manufacture }}</span>
                <span *ngIf="selected.kilometers" class="px-2 py-1 bg-slate-800 border border-slate-700 rounded text-xs text-slate-300">{{ selected.kilometers | number }} km</span>
                <span *ngIf="selected.car_color" class="px-2 py-1 bg-slate-800 border border-slate-700 rounded text-xs text-slate-300 capitalize">{{ selected.car_color }}</span>
                <span *ngIf="selected.ad_type" class="px-2 py-1 bg-blue-900/60 border border-blue-700/50 rounded text-xs text-blue-300 uppercase">{{ selected.ad_type }}</span>
              </div>
              <p *ngIf="selected.city || selected.region" class="text-slate-400 text-sm">📍 {{ selected.city }}{{ (selected.city && selected.region) ? ', ' : '' }}{{ selected.region }}</p>
              <div *ngIf="selected.engine" class="bg-slate-800/60 rounded-lg p-4 text-sm space-y-3">
                <p class="font-semibold text-slate-200">Motor</p>
                <!-- car_engine fields -->
                <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-slate-300">
                  <span *ngIf="selected.engine.engine_name" class="col-span-2 font-medium text-slate-100">{{ selected.engine.engine_name }}</span>
                  <span *ngIf="selected.engine.engine_description" class="col-span-2 text-slate-400 italic">{{ selected.engine.engine_description }}</span>
                  <span *ngIf="selected.engine.fuel_type" class="text-slate-400">Combustible:</span>
                  <span *ngIf="selected.engine.fuel_type" class="capitalize">{{ selected.engine.fuel_type }}</span>
                </div>
                <!-- engine_spec table -->
                <div *ngIf="selected.engine.specs && selected.engine.specs.length > 0">
                  <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2">Especificaciones técnicas</p>
                  <table class="w-full text-xs border-collapse">
                    <thead>
                      <tr class="border-b border-slate-700">
                        <th class="text-left py-1 pr-3 text-slate-400 font-medium">Característica</th>
                        <th class="text-left py-1 pr-3 text-slate-400 font-medium">Valor</th>
                        <th class="text-left py-1 text-slate-400 font-medium">Unidad</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr *ngFor="let spec of selected.engine.specs" class="border-b border-slate-700/40 hover:bg-slate-700/30">
                        <td class="py-1 pr-3 text-slate-300">{{ spec.sp_key }}</td>
                        <td class="py-1 pr-3 text-slate-100 font-medium">{{ spec.sp_value }}</td>
                        <td class="py-1 text-slate-400">{{ spec.measurement_unit || '—' }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <p *ngIf="!selected.engine.specs || selected.engine.specs.length === 0" class="text-slate-500 text-xs italic">Sin especificaciones técnicas registradas.</p>
              </div>
              <div *ngIf="selected.ad_details" class="bg-slate-800/40 rounded-lg p-4">
                <p class="text-sm font-semibold text-slate-300 mb-2">Descripción</p>
                <p class="text-slate-400 text-sm whitespace-pre-wrap">{{ selected.ad_details }}</p>
              </div>
              <div *ngIf="selected.seller" class="border-t border-slate-800 pt-4">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-3">Vendedor</p>
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-slate-700 border border-slate-600 flex items-center justify-center text-slate-300 font-bold text-sm flex-shrink-0">
                    {{ selected.seller.user_name?.charAt(0)?.toUpperCase() }}
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-100 text-sm">{{ selected.seller.user_name }}</p>
                    <div class="mt-1 space-y-1">
                      <a *ngIf="selected.seller.contact_email || selected.seller.email_address"
                         [href]="'mailto:' + (selected.seller.contact_email || selected.seller.email_address)"
                         class="flex items-center gap-1.5 text-xs text-cyan-400 hover:text-cyan-300 transition truncate"
                         (click)="$event.stopPropagation()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        {{ selected.seller.contact_email || selected.seller.email_address }}
                      </a>
                      <a *ngIf="selected.seller.phone"
                         [href]="'tel:' + selected.seller.phone"
                         class="flex items-center gap-1.5 text-xs text-cyan-400 hover:text-cyan-300 transition"
                         (click)="$event.stopPropagation()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.56 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        {{ selected.seller.phone }}
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </ng-container>
        </div>
      </div>
    </div>
  `,
})
export class MarketplaceComponent implements OnInit {
  private http = inject(HttpClient);
  private el = inject(ElementRef);

  showPublishModal = signal(false);
  loading = true;
  total = 0;
  errorMessage = '';
  visibleOnly = true;
  selectedType = '';
  adverts: MarketplaceAdvert[] = [];

  adTypes = [
    { label: 'Todos los tipos', value: '' },
    { label: 'Nuevo', value: 'new' },
    { label: 'Km 0', value: 'km0' },
    { label: 'Usado', value: 'used' },
    { label: 'Renting', value: 'renting' },
  ];

  ngOnInit(): void {
    this.loadAdverts();
  }

  setVisibleOnly(value: boolean): void {
    this.visibleOnly = value;
    this.loadAdverts();
  }

  setType(value: string): void {
    this.selectedType = value;
    this.loadAdverts();
  }

  reload(): void {
    this.loadAdverts();
  }

  onPublished(): void {
    this.showPublishModal.set(false);
    this.loadAdverts();
  }

  getCover(advert: MarketplaceAdvert): string | null {
    return advert.media?.media_url ?? null;
  }

  selected: any = null;
  detailLoading = false;

  openDetail(id: number): void {
    this.selected = null;
    this.detailLoading = true;
    this.http.get<any>(`${API_CONFIG.BASE_URL}/market/${id}`).subscribe({
      next: (data) => { this.selected = (data as any).data ?? data; this.detailLoading = false; },
      error: () => { this.detailLoading = false; },
    });
  }

  closeDetail(): void {
    this.selected = null;
    this.detailLoading = false;
  }

  getFirstMedia(media: any): string | null {
    if (!media) return null;
    if (Array.isArray(media)) return media[0]?.media_url ?? null;
    return media.media_url ?? null;
  }

  private loadAdverts(): void {
    this.loading = true;
    this.errorMessage = '';

    const parts: string[] = [];
    if (this.selectedType) {
      parts.push(`type[eq]=${encodeURIComponent(this.selectedType)}`);
    }
    const params = parts.length ? new HttpParams({ fromString: parts.join('&') }) : new HttpParams();

    this.http
      .get<PaginatedResponse<MarketplaceAdvert>>(`${API_CONFIG.BASE_URL}/market`, { params })
      .subscribe({
        next: (response) => {
          this.adverts = response.data ?? [];
          this.total = response.meta?.total ?? response.total ?? this.adverts.length;
          this.loading = false;
          setTimeout(() => {
            gsap.from(this.el.nativeElement.querySelectorAll('.advert-card'), {
              y: 30, opacity: 0, duration: 0.45, stagger: 0.07, ease: 'power2.out', clearProps: 'all'
            });
          }, 0);
        },
        error: () => {
          this.errorMessage = 'No se pudieron cargar los anuncios del marketplace.';
          this.adverts = [];
          this.total = 0;
          this.loading = false;
        },
      });
  }
}

@Component({
  selector: 'app-events',
  standalone: true,
  imports: [CommonModule, KddModalComponent],
  template: `
    <div class="p-8">
      <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-yellow-400 to-orange-400 bg-clip-text text-transparent">Eventos</h2>
        <div class="flex gap-2 flex-wrap">
          <button 
            (click)="showKddModal.set(true)"
            class="px-4 py-2 rounded-lg bg-yellow-600 hover:bg-yellow-500 text-white text-sm font-semibold transition">
            + Crear evento
          </button>

          <!-- Modal de Eventos -->
          @if (showKddModal()) {
            <app-kdd-modal 
              (close)="showKddModal.set(false)"
              (published)="onPublished()"
            />
          }
          <button (click)="reload()" class="px-3 py-2 rounded-lg border bg-slate-800/60 border-slate-700 text-slate-200 text-sm hover:bg-slate-700/60">
            Recargar
          </button>
        </div>
      </div>

      <!-- Filtros -->
      <div class="mb-6 flex gap-2">
        <button
          (click)="setFilter(false)"
          class="px-4 py-2 rounded-lg border text-sm font-semibold transition"
          [ngClass]="!mineOnly ? 'bg-yellow-500/20 border-yellow-400 text-yellow-200' : 'bg-slate-800/60 border-slate-700 text-slate-300'"
        >
          Todos los eventos
        </button>
        <button
          (click)="setFilter(true)"
          class="px-4 py-2 rounded-lg border text-sm font-semibold transition"
          [ngClass]="mineOnly ? 'bg-yellow-500/20 border-yellow-400 text-yellow-200' : 'bg-slate-800/60 border-slate-700 text-slate-300'"
        >
          Mis eventos
        </button>
      </div>

      <!-- Skeleton -->
      <div *ngIf="loading" class="space-y-4">
        <div *ngFor="let i of [1,2,3,4]" class="bg-slate-800/50 p-6 rounded-lg border border-slate-700 animate-pulse">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-slate-700/60 flex-shrink-0"></div>
            <div class="flex-1 space-y-2">
              <div class="h-4 bg-slate-700/60 rounded w-1/2"></div>
              <div class="h-3 bg-slate-700/60 rounded w-1/3"></div>
              <div class="h-3 bg-slate-700/60 rounded w-1/4"></div>
            </div>
          </div>
        </div>
      </div>

      <div *ngIf="errorMessage" class="rounded-lg border border-red-500/40 bg-red-500/10 p-4 text-red-200 mb-4">
        {{ errorMessage }}
      </div>

      <!-- Empty state -->
      <div *ngIf="!loading && events.length === 0 && !errorMessage" class="flex flex-col items-center justify-center py-20 text-slate-400">
        <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" class="mb-4 text-slate-600"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
        <p class="text-lg font-semibold text-slate-300">{{ mineOnly ? 'No estás inscrito en ningún evento' : 'No hay eventos próximos' }}</p>
        <p class="text-sm mt-1">{{ mineOnly ? 'Únete a un evento para verlo aquí.' : '¡Crea el primero y organiza tu próximo meet!' }}</p>
      </div>

      <div *ngIf="!loading && events.length > 0" class="space-y-4">
        <div *ngFor="let event of events" (click)="openDetail(event.event_id)" class="event-card cursor-pointer bg-slate-800/50 p-6 rounded-lg border border-slate-700 hover:border-yellow-500/50 transition hover:scale-[1.02] transition-transform">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-yellow-500/20 border border-yellow-500/40 flex items-center justify-center text-yellow-400 flex-shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
            </div>
            <div class="flex-1">
              <p class="font-bold text-lg">{{ event.title || 'Evento sin nombre' }}</p>
              <p class="text-slate-400">{{ event.city || 'Ubicación no disponible' }}</p>
              <p class="text-sm text-yellow-400 mt-1">{{ event.event_date | date:'fullDate' }}</p>
              <p class="text-xs text-slate-500 mt-1">
                {{ event.attendees_count ?? 0 }} participante{{ (event.attendees_count ?? 0) !== 1 ? 's' : '' }}
                <span *ngIf="event.max_participants && event.max_participants > 0"> / {{ event.max_participants }} max</span>
              </p>
            </div>
            <button
              (click)="$event.stopPropagation(); toggleJoin(event)"
              [disabled]="joiningId === event.event_id"
              class="px-4 py-2 rounded-lg transition flex-shrink-0 font-semibold text-sm disabled:opacity-50 disabled:cursor-not-allowed"
              [ngClass]="event.is_attending
                ? 'bg-red-500/20 hover:bg-red-500/40 text-red-300 border border-red-500/40'
                : 'bg-yellow-500/20 hover:bg-yellow-500/40 text-yellow-300 border border-yellow-500/40'"
            >
              <span *ngIf="joiningId !== event.event_id">{{ event.is_attending ? 'Abandonar' : 'Unirse' }}</span>
              <span *ngIf="joiningId === event.event_id" class="flex items-center gap-1">
                <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v8z" class="opacity-75"/></svg>
                ...
              </span>
            </button>
          </div>
        </div>
      </div>

      <!-- Events Detail Modal -->
      <div *ngIf="selected || detailLoading" class="fixed inset-0 z-50 flex items-center justify-center p-4" (click)="closeDetail()">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
        <div class="relative z-10 w-full max-w-2xl max-h-[90vh] overflow-y-auto bg-slate-900 rounded-xl border border-slate-700 shadow-2xl" (click)="$event.stopPropagation()">
          <div *ngIf="detailLoading" class="flex items-center justify-center p-16">
            <svg class="animate-spin text-yellow-400" xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v8z" class="opacity-75"/></svg>
          </div>
          <ng-container *ngIf="selected && !detailLoading">
            <div class="p-6 space-y-4">
              <div class="flex items-start justify-between gap-4">
                <h3 class="text-2xl font-bold text-slate-100">{{ selected.title }}</h3>
                <button (click)="closeDetail()" class="flex-shrink-0 w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center text-sm font-bold transition">✕</button>
              </div>
              <div class="flex flex-wrap gap-2 text-sm">
                <span class="px-2 py-1 bg-yellow-900/40 border border-yellow-700/50 rounded text-yellow-300">📅 {{ selected.event_date | date:'fullDate' }}</span>
                <span *ngIf="selected.city || selected.location_name" class="px-2 py-1 bg-slate-800 border border-slate-700 rounded text-slate-300">📍 {{ selected.location_name || selected.city }}</span>
                <span *ngIf="selected.address" class="px-2 py-1 bg-slate-800 border border-slate-700 rounded text-slate-400 text-xs">{{ selected.address }}</span>
              </div>
              <div class="flex gap-4 text-sm text-slate-400">
                <span *ngIf="selected.paddock?.paddock_name">Paddock: <span class="text-slate-200">{{ selected.paddock.paddock_name }}</span></span>
                <span *ngIf="selected.max_participants > 0">Plazas: <span class="text-slate-200">{{ selected.attendees?.length ?? 0 }} / {{ selected.max_participants }}</span></span>
              </div>
              <div *ngIf="selected.event_description" class="bg-slate-800/40 rounded-lg p-4">
                <p class="text-sm font-semibold text-slate-300 mb-2">Descripción</p>
                <p class="text-slate-400 text-sm whitespace-pre-wrap">{{ selected.event_description }}</p>
              </div>
              <div *ngIf="selected.attendees?.length" class="space-y-2">
                <p class="text-sm font-semibold text-slate-300">Participantes ({{ selected.attendees.length }})</p>
                <div class="flex flex-wrap gap-2">
                  <span *ngFor="let att of selected.attendees.slice(0, 12)" class="px-2 py-1 bg-slate-800 border border-slate-700 rounded text-xs text-slate-300">{{ att.user_name }}</span>
                  <span *ngIf="selected.attendees.length > 12" class="px-2 py-1 bg-slate-800 border border-slate-700 rounded text-xs text-slate-500">+{{ selected.attendees.length - 12 }} más</span>
                </div>
              </div>
              <p *ngIf="selected.creator?.user_name" class="text-xs text-slate-500 pt-2 border-t border-slate-800">Organizado por: <span class="text-slate-300">{{ selected.creator.user_name }}</span></p>
            </div>
          </ng-container>
        </div>
      </div>
    </div>
  `,
})
export class EventsComponent implements OnInit {
  private http = inject(HttpClient);
  private el = inject(ElementRef);

  showKddModal = signal(false);
  loading = true;
  errorMessage = '';
  mineOnly = false;
  joiningId: number | null = null;
  selected: any = null;
  detailLoading = false;
  events: EventKdd[] = [];

  ngOnInit(): void {
    this.loadEvents();
  }

  setFilter(mineOnly: boolean): void {
    this.mineOnly = mineOnly;
    this.loadEvents();
  }

  reload(): void {
    this.loadEvents();
  }

  onPublished(): void {
    this.showKddModal.set(false);
    this.loadEvents();
  }

  openDetail(id: number): void {
    this.selected = null;
    this.detailLoading = true;
    this.http.get<any>(`${API_CONFIG.BASE_URL}/kdds/${id}`).subscribe({
      next: (data) => { this.selected = (data as any).data ?? data; this.detailLoading = false; },
      error: () => { this.detailLoading = false; },
    });
  }

  closeDetail(): void {
    this.selected = null;
    this.detailLoading = false;
  }

  toggleJoin(event: EventKdd): void {
    if (this.joiningId !== null) return;
    this.joiningId = event.event_id;

    const req = event.is_attending
      ? this.http.delete<{ message: string; attendees_count: number }>(`${API_CONFIG.BASE_URL}/kdds/${event.event_id}/join`)
      : this.http.post<{ message: string; attendees_count: number }>(`${API_CONFIG.BASE_URL}/kdds/${event.event_id}/join`, {});

    req.subscribe({
      next: (res) => {
        const wasAttending = event.is_attending;
        event.is_attending = !wasAttending;
        event.attendees_count = res.attendees_count;
        this.joiningId = null;
        if (wasAttending) {
          toast.info('Has abandonado el evento.');
        } else {
          toast.success('¡Te has unido al evento!');
        }
        if (this.mineOnly && !event.is_attending) {
          this.events = this.events.filter(e => e.event_id !== event.event_id);
        }
      },
      error: () => {
        this.joiningId = null;
        toast.error('No se pudo procesar la solicitud.');
      },
    });
  }

  private loadEvents(): void {
    this.loading = true;
    this.errorMessage = '';

    let params = new HttpParams();
    if (this.mineOnly) {
      params = params.set('mine', '1');
    }

    this.http.get<PaginatedResponse<EventKdd>>(`${API_CONFIG.BASE_URL}/kdds`, { params }).subscribe({
      next: (response) => {
        this.events = response.data ?? [];
        this.loading = false;
        setTimeout(() => {
          gsap.from(this.el.nativeElement.querySelectorAll('.event-card'), {
            y: 25, opacity: 0, duration: 0.4, stagger: 0.09, ease: 'power2.out', clearProps: 'all'
          });
        }, 0);
      },
      error: () => {
        this.errorMessage = 'No se pudieron cargar los eventos.';
        this.events = [];
        this.loading = false;
      },
    });
  }
}

@Component({
  selector: 'app-universe',
  standalone: true,
  imports: [CommonModule, FormsModule, RelativeTimePipe, SocialModalComponent],
  template: `
    <div class="p-8">
      <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">El Universo</h2>
        <div class="flex gap-2">
          <button (click)="showSocialModal.set(true)" class="px-4 py-2 rounded-lg bg-purple-600 hover:bg-purple-500 text-white text-sm font-semibold transition">
            + Crear post
          </button>
          <button (click)="reload()" class="px-3 py-2 rounded-lg border bg-slate-800/60 border-slate-700 text-slate-200 text-sm hover:bg-slate-700/60">
            Recargar
          </button>
        </div>
      </div>

      <!-- Modal Social -->
      @if (showSocialModal()) {
        <app-social-modal 
          (close)="showSocialModal.set(false)"
          (published)="onPublished()"
        />
      }

      <!-- Filtros -->
      <div class="mb-6 flex gap-2">
        <button
          (click)="setFilter('all')"
          class="px-4 py-2 rounded-lg border text-sm font-semibold transition"
          [ngClass]="postFilter === 'all' ? 'bg-purple-500/20 border-purple-400 text-purple-200' : 'bg-slate-800/60 border-slate-700 text-slate-300'"
        >
          Todos los posts
        </button>
        <button
          (click)="setFilter('mine')"
          class="px-4 py-2 rounded-lg border text-sm font-semibold transition"
          [ngClass]="postFilter === 'mine' ? 'bg-purple-500/20 border-purple-400 text-purple-200' : 'bg-slate-800/60 border-slate-700 text-slate-300'"
        >
          Mis posts
        </button>
        <button
          (click)="setFilter('following')"
          class="px-4 py-2 rounded-lg border text-sm font-semibold transition"
          [ngClass]="postFilter === 'following' ? 'bg-purple-500/20 border-purple-400 text-purple-200' : 'bg-slate-800/60 border-slate-700 text-slate-300'"
        >
          Siguiendo
        </button>
      </div>

      <!-- Skeleton loader -->
      <div *ngIf="loading" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <div *ngFor="let i of [1,2,3,4,5,6]" class="bg-slate-800/50 rounded-lg overflow-hidden border border-slate-700 animate-pulse">
          <div class="h-48 bg-slate-700/60"></div>
          <div class="p-4 space-y-3">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-full bg-slate-700/60"></div>
              <div class="h-3 bg-slate-700/60 rounded w-1/3"></div>
            </div>
            <div class="h-4 bg-slate-700/60 rounded w-3/4"></div>
            <div class="h-3 bg-slate-700/60 rounded w-full"></div>
            <div class="h-3 bg-slate-700/60 rounded w-2/3"></div>
          </div>
        </div>
      </div>

      <div *ngIf="errorMessage" class="rounded-lg border border-red-500/40 bg-red-500/10 p-4 text-red-200 mb-4">
        {{ errorMessage }}
      </div>

      <div *ngIf="!loading && !errorMessage" class="mb-4 text-sm text-slate-400">
        Total publicaciones: <span class="text-slate-100 font-semibold">{{ total }}</span>
      </div>

      <!-- Empty state -->
      <div *ngIf="!loading && posts.length === 0 && !errorMessage" class="flex flex-col items-center justify-center py-20 text-slate-400">
        <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" class="mb-4 text-slate-600"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
        <p class="text-lg font-semibold text-slate-300">{{ postFilter === 'mine' ? 'Aún no has publicado nada' : postFilter === 'following' ? 'No sigues a nadie todavía' : 'No hay publicaciones todavía' }}</p>
        <p class="text-sm mt-1">{{ postFilter === 'mine' ? 'Crea tu primer post y compártelo con la comunidad.' : postFilter === 'following' ? 'Sigue a otros usuarios para ver sus posts aquí.' : '¡Sé el primero en compartir algo!' }}</p>
      </div>

      <!-- Grid de tarjetas -->
      <div *ngIf="!loading && posts.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <article *ngFor="let post of posts" (click)="openDetail(post.post_id)" class="post-card cursor-pointer bg-slate-800/50 rounded-lg overflow-hidden border border-slate-700 hover:border-purple-500/50 transition hover:scale-[1.02]">
          <!-- Imagen o placeholder -->
          <div class="h-48 bg-slate-900 relative">
            <img
              *ngIf="getMedia(post) as imgUrl; else noMedia"
              [src]="imgUrl"
              [alt]="post.title || 'Post'"
              class="h-full w-full object-cover"
            />
            <ng-template #noMedia>
              <div class="h-full w-full flex items-center justify-center bg-gradient-to-br from-purple-900/40 to-pink-900/40">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="text-purple-500/60"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
              </div>
            </ng-template>
          </div>

          <!-- Contenido -->
          <div class="p-4">
            <!-- Autor -->
            <div class="flex items-center gap-2 mb-3">
              <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-xs flex-shrink-0 uppercase select-none">
                {{ getInitial(post.author?.username) }}
              </div>
              <span class="text-sm font-semibold text-slate-200">{{ post.author?.username || 'Usuario' }}</span>
              <span *ngIf="post.created_at" class="ml-auto text-xs text-slate-500">{{ post.created_at | relativeTime }}</span>
            </div>

            <!-- Título y excerpt -->
            <p *ngIf="post.title" class="font-bold text-slate-100 mb-1 line-clamp-1">{{ post.title }}</p>
            <p class="text-sm text-slate-400 line-clamp-3">{{ post.content_excerpt }}</p>
          </div>
        </article>
      </div>

      <!-- Universe Detail Modal -->
      <div *ngIf="selected || detailLoading" class="fixed inset-0 z-50 flex items-center justify-center p-4" (click)="closeDetail()">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
        <div class="relative z-10 w-full max-w-2xl max-h-[90vh] overflow-y-auto bg-slate-900 rounded-xl border border-slate-700 shadow-2xl" (click)="$event.stopPropagation()">
          <div *ngIf="detailLoading" class="flex items-center justify-center p-16">
            <svg class="animate-spin text-purple-400" xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v8z" class="opacity-75"/></svg>
          </div>
          <ng-container *ngIf="selected && !detailLoading">
            <div *ngIf="getFirstMedia(selected.media)" class="relative h-56 bg-slate-950 rounded-t-xl overflow-hidden">
              <img [src]="getFirstMedia(selected.media)" [alt]="selected.title || 'Post'" class="h-full w-full object-cover"/>
            </div>
            <div class="p-6 space-y-4">
              <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 uppercase">{{ getInitial(selected.author?.user_name) }}</div>
                  <div>
                    <p class="font-semibold text-slate-200">{{ selected.author?.user_name || 'Usuario' }}</p>
                    <p class="text-xs text-slate-500">{{ selected.created_at | date:'medium' }}</p>
                  </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                  <button
                    *ngIf="selected.author?.user_id"
                    (click)="toggleFollow()"
                    [disabled]="followLoading"
                    class="px-3 py-1.5 rounded-lg border text-xs font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed"
                    [ngClass]="selected.author?.is_following
                      ? 'bg-purple-500/20 border-purple-500/60 text-purple-200 hover:bg-red-500/10 hover:border-red-500/40 hover:text-red-300'
                      : 'bg-slate-800 border-slate-600 text-slate-300 hover:bg-purple-500/20 hover:border-purple-500/50 hover:text-purple-200'"
                  >
                    {{ followLoading ? '...' : (selected.author?.is_following ? 'Siguiendo ✓' : '+ Seguir') }}
                  </button>
                  <button (click)="closeDetail()" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center text-sm font-bold transition">✕</button>
                </div>
              </div>
              <h3 *ngIf="selected.title" class="text-xl font-bold text-slate-100">{{ selected.title }}</h3>
              <p class="text-slate-300 text-sm whitespace-pre-wrap leading-relaxed">{{ selected.content }}</p>
              <div class="flex items-center gap-3 pt-2 border-t border-slate-800">
                <button
                  (click)="toggleLike()"
                  [disabled]="likeLoading"
                  class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-sm font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed"
                  [ngClass]="selected.is_liked
                    ? 'bg-pink-500/20 border-pink-500/60 text-pink-300'
                    : 'bg-slate-800/60 border-slate-700 text-slate-400 hover:border-pink-500/40 hover:text-pink-300'"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" [attr.fill]="selected.is_liked ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                  {{ selected.likes_count ?? selected.likes?.length ?? 0 }}
                </button>
                <span class="flex items-center gap-1.5 text-sm text-slate-500">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                  {{ selected.comments?.length ?? 0 }} comentario{{ (selected.comments?.length ?? 0) !== 1 ? 's' : '' }}
                </span>
              </div>
              <div class="space-y-3">
                <p class="text-sm font-semibold text-slate-300">Comentarios</p>
                <ng-container *ngIf="selected.comments?.length; else noComments">
                  <div *ngFor="let comment of selected.comments.slice(0, 5)" class="bg-slate-800/50 rounded-lg p-3">
                    <p class="text-xs font-semibold text-slate-300 mb-1">{{ comment.user?.user_name || 'Usuario' }}</p>
                    <p class="text-xs text-slate-400">{{ comment.comment_text }}</p>
                  </div>
                  <p *ngIf="selected.comments.length > 5" class="text-xs text-slate-500">y {{ selected.comments.length - 5 }} comentarios más...</p>
                </ng-container>
                <ng-template #noComments>
                  <p class="text-xs text-slate-500 italic">Sin comentarios aún. ¡Sé el primero!</p>
                </ng-template>
                <!-- Formulario nuevo comentario -->
                <div class="flex gap-2 pt-1">
                  <textarea
                    [(ngModel)]="newComment"
                    placeholder="Escribe un comentario..."
                    rows="2"
                    class="flex-1 px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-200 text-xs placeholder-slate-500 focus:outline-none focus:border-purple-500/60 resize-none"
                    (keydown.enter)="onCommentEnter($event)"
                  ></textarea>
                  <button
                    (click)="submitComment()"
                    [disabled]="commentLoading || !newComment.trim()"
                    class="px-3 py-2 rounded-lg bg-purple-600 hover:bg-purple-500 text-white text-xs font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed flex-shrink-0 self-end"
                  >
                    <span *ngIf="!commentLoading">Enviar</span>
                    <svg *ngIf="commentLoading" class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v8z" class="opacity-75"/></svg>
                  </button>
                </div>
              </div>
            </div>
          </ng-container>
        </div>
      </div>
    </div>
  `,
})
export class UniverseComponent implements OnInit {
  private http = inject(HttpClient);
  private el = inject(ElementRef);

  showSocialModal = signal(false);
  loading = true;
  total = 0;
  errorMessage = '';
  postFilter: 'all' | 'mine' | 'following' = 'all';
  selected: any = null;
  detailLoading = false;
  likeLoading = false;
  followLoading = false;
  newComment = '';
  commentLoading = false;
  posts: UniversePost[] = [];

  ngOnInit(): void {
    this.loadPosts();
  }

  setFilter(filter: 'all' | 'mine' | 'following'): void {
    this.postFilter = filter;
    this.loadPosts();
  }

  reload(): void {
    this.loadPosts();
  }

  onPublished(): void {
    this.showSocialModal.set(false);
    this.loadPosts();
  }

  getInitial(username?: string): string {
    if (!username) return '?';
    return username.charAt(0).toUpperCase();
  }

  getMedia(post: UniversePost): string | null {
    const m = post.media;
    if (!m) return null;
    if (Array.isArray(m)) return (m[0] as AdvertMedia)?.media_url ?? null;
    return (m as AdvertMedia).media_url ?? null;
  }

  getFirstMedia(media: any): string | null {
    if (!media) return null;
    if (Array.isArray(media)) return media[0]?.media_url ?? null;
    return media.media_url ?? null;
  }

  openDetail(id: number): void {
    this.selected = null;
    this.detailLoading = true;
    this.likeLoading = false;
    this.followLoading = false;
    this.newComment = '';
    this.commentLoading = false;
    this.http.get<any>(`${API_CONFIG.BASE_URL}/social/${id}`).subscribe({
      next: (data) => { this.selected = (data as any).data ?? data; this.detailLoading = false; },
      error: () => { this.detailLoading = false; },
    });
  }

  closeDetail(): void {
    this.selected = null;
    this.detailLoading = false;
    this.likeLoading = false;
    this.followLoading = false;
    this.newComment = '';
    this.commentLoading = false;
  }

  toggleLike(): void {
    if (!this.selected || this.likeLoading) return;
    this.likeLoading = true;
    this.http.post<{ liked: boolean; likes_count: number }>(
      `${API_CONFIG.BASE_URL}/social/${this.selected.post_id}/like`, {}
    ).subscribe({
      next: (res) => {
        this.selected.is_liked = res.liked;
        this.selected.likes_count = res.likes_count;
        this.likeLoading = false;
        if (res.liked) {
          toast.success('¡Le has dado like al post!');
        } else {
          toast.info('Has quitado el like.');
        }
      },
      error: () => {
        this.likeLoading = false;
        toast.error('No se pudo procesar el like.');
      },
    });
  }

  toggleFollow(): void {
    if (!this.selected?.author?.user_id || this.followLoading) return;
    this.followLoading = true;
    this.http.post<{ following: boolean; followers_count: number }>(
      `${API_CONFIG.BASE_URL}/users/${this.selected.author.user_id}/follow`, {}
    ).subscribe({
      next: (res) => {
        this.selected.author.is_following = res.following;
        this.followLoading = false;
        if (res.following) {
          toast.success(`Ahora sigues a ${this.selected.author.user_name}.`);
        } else {
          toast.info(`Has dejado de seguir a ${this.selected.author.user_name}.`);
        }
      },
      error: () => {
        this.followLoading = false;
        toast.error('No se pudo procesar el follow.');
      },
    });
  }

  submitComment(): void {
    if (!this.selected || !this.newComment.trim() || this.commentLoading) return;
    this.commentLoading = true;
    this.http.post<{ comment: any; comments_count: number }>(
      `${API_CONFIG.BASE_URL}/social/${this.selected.post_id}/comment`,
      { comment_text: this.newComment.trim() }
    ).subscribe({
      next: (res) => {
        if (!this.selected.comments) this.selected.comments = [];
        this.selected.comments.push(res.comment);
        this.newComment = '';
        this.commentLoading = false;
        toast.success('Comentario publicado.');
      },
      error: () => {
        this.commentLoading = false;
        toast.error('No se pudo publicar el comentario.');
      },
    });
  }

  onCommentEnter(event: Event): void {
    if (!(event as KeyboardEvent).shiftKey) {
      event.preventDefault();
      this.submitComment();
    }
  }

  private loadPosts(): void {
    this.loading = true;
    this.errorMessage = '';

    let params = new HttpParams();
    if (this.postFilter === 'mine') {
      params = params.set('mine', '1');
    } else if (this.postFilter === 'following') {
      params = params.set('following', '1');
    }

    this.http.get<PaginatedResponse<UniversePost>>(`${API_CONFIG.BASE_URL}/social`, { params }).subscribe({
      next: (response) => {
        this.posts = response.data ?? [];
        this.total = response.meta?.total ?? response.total ?? this.posts.length;
        this.loading = false;
        setTimeout(() => {
          gsap.from(this.el.nativeElement.querySelectorAll('.post-card'), {
            y: 30, opacity: 0, duration: 0.45, stagger: 0.07, ease: 'power2.out', clearProps: 'all'
          });
        }, 0);
      },
      error: () => {
        this.errorMessage = 'No se pudieron cargar las publicaciones de El Universo.';
        this.posts = [];
        this.total = 0;
        this.loading = false;
      },
    });
  }
}

@Component({
  selector: 'app-garage',
  standalone: true,
  imports: [CommonModule, GarageModalComponent],
  template: `
    <div class="p-8">
      <h2 class="text-3xl font-bold mb-4 bg-gradient-to-r from-orange-400 to-red-400 bg-clip-text text-transparent">Mi Garaje</h2>
      <div class="mb-6">
        <button 
          (click)="showGarageModal.set(true)"
          class="px-6 py-3 bg-orange-500/20 hover:bg-orange-500/40 text-orange-300 rounded-lg transition font-bold">
          + Agregar Vehículo
        </button>
      </div>

      <!-- Modal Garaje -->
      @if (showGarageModal()) {
        <app-garage-modal 
          (close)="showGarageModal.set(false)"
          (published)="onPublished()"
        />
      }
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
  `,
})
export class GarageComponent {
  showGarageModal = signal(false);

  onPublished(): void {
    this.showGarageModal.set(false);
    // TODO: Recargar lista del garaje si fuera necesario
  }
}

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [CommonModule, FormsModule],
  template: `
    <div class="p-8">
      <div class="mb-6 flex items-center justify-between">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-green-400 to-cyan-400 bg-clip-text text-transparent">Mi Perfil</h2>
        <div class="flex gap-2">
          <button *ngIf="!isEditing" (click)="startEdit()" class="px-3 py-2 rounded-lg border bg-slate-800/60 border-slate-700 text-slate-200 text-sm hover:bg-slate-700/60">
            Editar perfil
          </button>
          <button *ngIf="!isEditing" (click)="reload()" class="px-3 py-2 rounded-lg border bg-slate-800/60 border-slate-700 text-slate-200 text-sm hover:bg-slate-700/60">
            Recargar
          </button>
        </div>
      </div>

      <div *ngIf="loading" class="rounded-lg border border-slate-700 bg-slate-800/50 p-4 text-slate-300 mb-4">
        Cargando perfil...
      </div>

      <div *ngIf="errorMessage" class="rounded-lg border border-red-500/40 bg-red-500/10 p-4 text-red-200 mb-4">
        {{ errorMessage }}
      </div>

      <div *ngIf="successMessage" class="rounded-lg border border-green-500/40 bg-green-500/10 p-4 text-green-200 mb-4">
        {{ successMessage }}
      </div>

      <div *ngIf="profile" class="max-w-4xl grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-slate-800/50 p-6 rounded-lg border border-slate-700">
          <h3 class="font-bold mb-4 text-lg">Información Personal</h3>

          <!-- VIEW MODE -->
          <div *ngIf="!isEditing" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="text-sm text-slate-400">Nombre</label>
              <p class="font-semibold">{{ profile.user_name }}</p>
            </div>
            <div>
              <label class="text-sm text-slate-400">Email principal</label>
              <p class="font-semibold">{{ profile.email_address }}</p>
            </div>
            <div>
              <label class="text-sm text-slate-400">Email de contacto</label>
              <p class="font-semibold">{{ profile.contact_email || 'No definido' }}</p>
            </div>
            <div>
              <label class="text-sm text-slate-400">Teléfono</label>
              <p class="font-semibold">{{ profile.phone || 'No definido' }}</p>
            </div>
            <div>
              <label class="text-sm text-slate-400">Tipo de perfil</label>
              <p class="font-semibold">{{ getUserTagLabel(profile.user_tag) }}</p>
            </div>
            <div>
              <label class="text-sm text-slate-400">Paddock</label>
              <p class="font-semibold">{{ profile.paddock?.paddock_name || 'Sin paddock' }}</p>
            </div>
            <div class="md:col-span-2">
              <label class="text-sm text-slate-400">Dirección</label>
              <p class="font-semibold">{{ profile.address || 'No definida' }}</p>
            </div>
            <div>
              <label class="text-sm text-slate-400">Miembro desde</label>
              <p class="font-semibold">{{ profile.registration_date ? (profile.registration_date | date:'mediumDate') : 'No disponible' }}</p>
            </div>
          </div>

          <!-- EDIT MODE -->
          <form *ngIf="isEditing" (ngSubmit)="saveProfile()" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="text-sm text-slate-400 block mb-1">Nombre</label>
              <input [(ngModel)]="editForm.user_name" name="user_name" type="text"
                class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-600 text-slate-100 text-sm focus:outline-none focus:border-cyan-500" />
            </div>
            <div>
              <label class="text-sm text-slate-400 block mb-1">Email principal</label>
              <p class="font-semibold py-2 text-slate-400 text-sm">{{ profile.email_address }}</p>
            </div>
            <div>
              <label class="text-sm text-slate-400 block mb-1">Email de contacto</label>
              <input [(ngModel)]="editForm.contact_email" name="contact_email" type="email"
                class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-600 text-slate-100 text-sm focus:outline-none focus:border-cyan-500" />
            </div>
            <div>
              <label class="text-sm text-slate-400 block mb-1">Teléfono</label>
              <input [(ngModel)]="editForm.phone" name="phone" type="text"
                class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-600 text-slate-100 text-sm focus:outline-none focus:border-cyan-500" />
            </div>
            <div>
              <label class="text-sm text-slate-400 block mb-1">Tipo de perfil</label>
              <p class="font-semibold py-2 text-slate-400 text-sm">{{ getUserTagLabel(profile.user_tag) }}</p>
            </div>
            <div>
              <label class="text-sm text-slate-400 block mb-1">Paddock</label>
              <div *ngIf="loadingPaddocks" class="text-xs text-slate-500 py-2">Cargando paddocks...</div>
              <select *ngIf="!loadingPaddocks" [(ngModel)]="editForm.paddock_id" name="paddock_id"
                class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-600 text-slate-100 text-sm focus:outline-none focus:border-cyan-500">
                <option [ngValue]="null">Sin paddock</option>
                <option *ngFor="let p of paddocks" [ngValue]="p.paddock_id">{{ p.paddock_name }}</option>
              </select>
            </div>
            <div class="md:col-span-2">
              <label class="text-sm text-slate-400 block mb-1">Dirección</label>
              <input [(ngModel)]="editForm.address" name="address" type="text"
                class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-600 text-slate-100 text-sm focus:outline-none focus:border-cyan-500" />
            </div>
            <div>
              <label class="text-sm text-slate-400 block mb-1">Miembro desde</label>
              <p class="font-semibold py-2 text-slate-400 text-sm">{{ profile.registration_date ? (profile.registration_date | date:'mediumDate') : 'No disponible' }}</p>
            </div>
            <div class="md:col-span-2 flex gap-3 justify-end mt-2">
              <button type="button" (click)="cancelEdit()" [disabled]="isSaving"
                class="px-4 py-2 rounded-lg border border-slate-600 text-slate-300 text-sm hover:bg-slate-700/60 disabled:opacity-50">
                Cancelar
              </button>
              <button type="submit" [disabled]="isSaving"
                class="px-4 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white text-sm font-semibold transition disabled:opacity-50">
                {{ isSaving ? 'Guardando...' : 'Guardar cambios' }}
              </button>
            </div>
          </form>
        </div>

        <div class="bg-slate-800/50 p-6 rounded-lg border border-slate-700 space-y-4">
          <h3 class="font-bold mb-2 text-lg">Resumen</h3>
          <div class="rounded-lg border border-slate-700 bg-slate-900/40 p-4">
            <p class="text-sm text-slate-400">Publicaciones</p>
            <p class="text-2xl font-bold text-slate-100">{{ profile.posts?.length || 0 }}</p>
          </div>
          <div class="rounded-lg border border-slate-700 bg-slate-900/40 p-4">
            <p class="text-sm text-slate-400">Coches en garaje</p>
            <p class="text-2xl font-bold text-slate-100">{{ profile.garage?.length || 0 }}</p>
          </div>
          <div class="rounded-lg border border-slate-700 bg-slate-900/40 p-4">
            <p class="text-sm text-slate-400">ID de usuario</p>
            <p class="text-2xl font-bold text-slate-100">#{{ profile.user_id }}</p>
          </div>
        </div>
      </div>
    </div>
  `,
})
export class ProfileComponent implements OnInit {
  private http = inject(HttpClient);
  private authService = inject(AuthService);

  loading = true;
  errorMessage = '';
  successMessage = '';
  profile: ProfileDetail | null = null;
  isEditing = false;
  isSaving = false;
  paddocks: Paddock[] = [];
  loadingPaddocks = false;
  editForm = {
    user_name: '',
    contact_email: '',
    phone: '',
    address: '',
    paddock_id: null as number | null,
  };

  ngOnInit(): void {
    this.loadProfile();
    this.loadPaddocks();
  }

  reload(): void {
    this.loadProfile();
  }

  startEdit(): void {
    if (!this.profile) return;
    this.editForm = {
      user_name: this.profile.user_name ?? '',
      contact_email: this.profile.contact_email ?? '',
      phone: this.profile.phone ?? '',
      address: this.profile.address ?? '',
      paddock_id: this.profile.paddock?.paddock_id ?? this.profile.paddock_id ?? null,
    };
    this.successMessage = '';
    this.errorMessage = '';
    this.isEditing = true;
  }

  cancelEdit(): void {
    this.isEditing = false;
    this.errorMessage = '';
  }

  saveProfile(): void {
    if (!this.profile || this.isSaving) return;
    this.isSaving = true;
    this.errorMessage = '';
    this.successMessage = '';

    const payload: Record<string, string | number | null> = {};
    if (this.editForm.user_name.trim()) payload['user_name'] = this.editForm.user_name.trim();
    if (this.editForm.contact_email.trim()) payload['contact_email'] = this.editForm.contact_email.trim();
    if (this.editForm.phone.trim()) payload['phone'] = this.editForm.phone.trim();
    if (this.editForm.address.trim()) payload['address'] = this.editForm.address.trim();
    payload['paddock_id'] = this.editForm.paddock_id;

    this.http.patch(`${API_CONFIG.BASE_URL}/users/${this.profile.user_id}`, payload).subscribe({
      next: () => {
        this.isSaving = false;
        this.isEditing = false;
        this.successMessage = 'Perfil actualizado correctamente.';
        this.authService.refreshUser();
        this.loadProfile();
      },
      error: () => {
        this.isSaving = false;
        this.errorMessage = 'No se pudieron guardar los cambios. Inténtalo de nuevo.';
      },
    });
  }

  getUserTagLabel(tag?: string): string {
    const labels: Record<string, string> = {
      admin: 'Administrador',
      pro: 'Profesional',
      indv: 'Individual',
      tuning: 'Tuner',
      press: 'Prensa',
    };

    return tag ? (labels[tag] || tag) : 'Sin definir';
  }

  private loadPaddocks(): void {
    this.loadingPaddocks = true;
    this.http.get<Paddock[]>(`${API_CONFIG.BASE_URL}/paddocks`).subscribe({
      next: (data) => {
        this.paddocks = data;
        this.loadingPaddocks = false;
      },
      error: () => {
        this.loadingPaddocks = false;
      },
    });
  }

  private loadProfile(): void {
    this.loading = true;
    this.errorMessage = '';

    this.authService.getCurrentUser().subscribe({
      next: (user) => {
        if (!user) {
          this.errorMessage = 'No hay usuario autenticado.';
          this.loading = false;
          return;
        }

        this.http.get<ProfileDetail>(`${API_CONFIG.BASE_URL}/users/${user.user_id}`).subscribe({
          next: (profile) => {
            this.profile = profile;
            this.loading = false;
          },
          error: () => {
            this.errorMessage = 'No se pudo cargar la información del perfil.';
            this.loading = false;
          },
        });
      },
      error: () => {
        this.errorMessage = 'No se pudo resolver el usuario actual.';
        this.loading = false;
      },
    });
  }
}
