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
  paddock?: {
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
        <article *ngFor="let advert of adverts" class="advert-card bg-slate-800/50 rounded-lg overflow-hidden border border-slate-700 hover:border-cyan-500/50 transition hover:scale-[1.02] transition-transform">
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

  private loadAdverts(): void {
    this.loading = true;
    this.errorMessage = '';

    let params = new HttpParams();
    if (this.visibleOnly) {
      params = params.set('visible', '1');
    }
    if (this.selectedType) {
      params = params.set('ad_type', this.selectedType);
    }

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
      <div class="mb-6 flex items-center justify-between">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-yellow-400 to-orange-400 bg-clip-text text-transparent">Eventos</h2>
        <div class="flex gap-2">
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
        <p class="text-lg font-semibold text-slate-300">No hay eventos próximos</p>
        <p class="text-sm mt-1">¡Crea el primero y organiza tu próximo meet!</p>
      </div>

      <div *ngIf="!loading && events.length > 0" class="space-y-4">
        <div *ngFor="let event of events" class="event-card bg-slate-800/50 p-6 rounded-lg border border-slate-700 hover:border-yellow-500/50 transition hover:scale-[1.02] transition-transform">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-yellow-500/20 border border-yellow-500/40 flex items-center justify-center text-yellow-400 flex-shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
            </div>
            <div class="flex-1">
              <p class="font-bold text-lg">{{ event.title || 'Evento sin nombre' }}</p>
              <p class="text-slate-400">{{ event.city || 'Ubicación no disponible' }}</p>
              <p class="text-sm text-yellow-400 mt-1">{{ event.event_date | date:'fullDate' }}</p>
            </div>
            <button class="px-4 py-2 bg-yellow-500/20 hover:bg-yellow-500/40 text-yellow-300 rounded-lg transition flex-shrink-0">
              Unirse
            </button>
          </div>
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
  events: EventKdd[] = [];

  ngOnInit(): void {
    this.loadEvents();
  }

  reload(): void {
    this.loadEvents();
  }

  onPublished(): void {
    this.showKddModal.set(false);
    this.loadEvents();
  }

  private loadEvents(): void {
    this.loading = true;
    this.errorMessage = '';

    this.http.get<PaginatedResponse<EventKdd>>(`${API_CONFIG.BASE_URL}/kdds`).subscribe({
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
  imports: [CommonModule, RelativeTimePipe, SocialModalComponent],
  template: `
    <div class="p-8">
      <div class="mb-6 flex items-center justify-between">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">El Universo</h2>
        <div class="flex gap-2">
          <button
            (click)="showSocialModal.set(true)"
            class="px-4 py-2 rounded-lg bg-purple-600 hover:bg-purple-500 text-white text-sm font-semibold transition"
          >
            + Crear post
          </button>

          <!-- Modal Social -->
          @if (showSocialModal()) {
            <app-social-modal 
              (close)="showSocialModal.set(false)"
              (published)="onPublished()"
            />
          }
          <button
            (click)="reload()"
            class="px-3 py-2 rounded-lg border bg-slate-800/60 border-slate-700 text-slate-200 text-sm hover:bg-slate-700/60"
          >
            Recargar
          </button>
        </div>
      </div>

      <!-- Skeleton loader -->
      <div *ngIf="loading" class="space-y-4">
        <div *ngFor="let i of [1,2,3]" class="bg-slate-800/50 p-6 rounded-lg border border-slate-700 animate-pulse">
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-slate-700/60 flex-shrink-0"></div>
            <div class="flex-1 space-y-3">
              <div class="h-4 bg-slate-700/60 rounded w-1/4"></div>
              <div class="h-3 bg-slate-700/60 rounded w-3/4"></div>
              <div class="h-3 bg-slate-700/60 rounded w-1/2"></div>
            </div>
          </div>
        </div>
      </div>

      <div *ngIf="errorMessage" class="rounded-lg border border-red-500/40 bg-red-500/10 p-4 text-red-200 mb-4">
        {{ errorMessage }}
      </div>

      <div *ngIf="!loading" class="mb-4 text-sm text-slate-400">
        Total publicaciones: <span class="text-slate-100 font-semibold">{{ total }}</span>
      </div>

      <!-- Empty state -->
      <div *ngIf="!loading && posts.length === 0 && !errorMessage" class="flex flex-col items-center justify-center py-20 text-slate-400">
        <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" class="mb-4 text-slate-600"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
        <p class="text-lg font-semibold text-slate-300">No hay publicaciones todavía</p>
        <p class="text-sm mt-1">¡Sé el primero en compartir algo con la comunidad!</p>
      </div>

      <div *ngIf="!loading && posts.length > 0" class="space-y-4">
        <article *ngFor="let post of posts" class="post-card bg-slate-800/50 p-6 rounded-lg border border-slate-700 hover:border-purple-500/50 transition hover:scale-[1.02] transition-transform">
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 uppercase select-none">
              {{ getInitial(post.author?.username) }}
            </div>
            <div class="flex-1">
              <p class="font-bold">{{ post.author?.username || 'Usuario' }}</p>
              <p *ngIf="post.title" class="text-sm text-slate-300 mt-1">{{ post.title }}</p>
              <p class="text-slate-400 mt-2">{{ post.content_excerpt }}</p>

              <img
                *ngIf="post.media?.media_url"
                [src]="post.media?.media_url"
                alt="post media"
                class="mt-3 w-full max-h-72 object-cover rounded-lg border border-slate-700"
              />

              <div class="flex gap-4 mt-3 text-slate-500 text-xs">
                <span *ngIf="post.created_at" class="flex items-center gap-1">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                  {{ post.created_at | relativeTime }}
                </span>
              </div>
            </div>
          </div>
        </article>
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
  posts: UniversePost[] = [];

  ngOnInit(): void {
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

  private loadPosts(): void {
    this.loading = true;
    this.errorMessage = '';

    this.http.get<PaginatedResponse<UniversePost>>(`${API_CONFIG.BASE_URL}/social`).subscribe({
      next: (response) => {
        this.posts = response.data ?? [];
        this.total = response.meta?.total ?? response.total ?? this.posts.length;
        this.loading = false;
        setTimeout(() => {
          gsap.from(this.el.nativeElement.querySelectorAll('.post-card'), {
            y: 25, opacity: 0, duration: 0.4, stagger: 0.09, ease: 'power2.out', clearProps: 'all'
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
              <p class="font-semibold py-2 text-slate-400 text-sm">{{ profile.paddock?.paddock_name || 'Sin paddock' }}</p>
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
  editForm = {
    user_name: '',
    contact_email: '',
    phone: '',
    address: '',
  };

  ngOnInit(): void {
    this.loadProfile();
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

    const payload: Record<string, string> = {};
    if (this.editForm.user_name.trim()) payload['user_name'] = this.editForm.user_name.trim();
    if (this.editForm.contact_email.trim()) payload['contact_email'] = this.editForm.contact_email.trim();
    if (this.editForm.phone.trim()) payload['phone'] = this.editForm.phone.trim();
    if (this.editForm.address.trim()) payload['address'] = this.editForm.address.trim();

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
