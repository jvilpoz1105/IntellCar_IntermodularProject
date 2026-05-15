import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient, HttpParams } from '@angular/common/http';
import { AuthService, User } from '../core/services/auth.service';
import { API_CONFIG } from '../core/config/api.config';

interface PaginatedResponse<T> {
  data: T[];
  total: number;
}

interface AdvertMedia {
  media_id: number;
  media_url: string;
  media_type: 'image' | 'video';
}

interface AdvertModel {
  model_name?: string;
  make?: {
    make_name?: string;
  };
}

interface MarketplaceAdvert {
  ad_id: number;
  ad_title: string;
  ad_type?: string;
  price: number;
  city?: string;
  region?: string;
  model?: AdvertModel;
  seller?: {
    user_name?: string;
  };
  media?: AdvertMedia[];
}

interface UniversePost {
  post_id: number;
  title?: string;
  content: string;
  created_at?: string;
  author?: {
    user_name?: string;
  };
  media?: Array<{
    media_id: number;
    media_url: string;
    media_type: 'image' | 'video';
  }>;
  likes?: unknown[];
  comments?: unknown[];
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
  imports: [CommonModule],
  template: `
    <div class="p-8">
      <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">Marketplace</h2>
        <div class="flex gap-2">
          <button
            class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold transition"
          >
            + Crear anuncio
          </button>
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

      <div *ngIf="loading" class="rounded-lg border border-slate-700 bg-slate-800/50 p-4 text-slate-300 mb-4">
        Cargando anuncios...
      </div>

      <div *ngIf="errorMessage" class="rounded-lg border border-red-500/40 bg-red-500/10 p-4 text-red-200 mb-4">
        {{ errorMessage }}
      </div>

      <div class="mb-4 text-sm text-slate-400">
        Total anuncios: <span class="text-slate-100 font-semibold">{{ total }}</span>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <article *ngFor="let advert of adverts" class="bg-slate-800/50 rounded-lg overflow-hidden border border-slate-700 hover:border-cyan-500/50 transition">
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
              {{ advert.model?.make?.make_name || 'Marca' }} • {{ advert.model?.model_name || 'Modelo' }}
            </p>
            <p class="text-sm text-slate-400">{{ advert.city || 'Ciudad' }}, {{ advert.region || 'Región' }}</p>
            <p class="text-sm text-slate-400">Vendedor: {{ advert.seller?.user_name || 'Usuario' }}</p>
            <div class="mt-3 flex items-center justify-between">
              <span class="text-xs px-2 py-1 rounded bg-slate-700 text-slate-200 uppercase">{{ advert.ad_type || 'used' }}</span>
              <p class="text-lg text-green-400 font-bold">{{ advert.price | number:'1.0-0' }} €</p>
            </div>
          </div>
        </article>
      </div>
    </div>
  `,
})
export class MarketplaceComponent implements OnInit {
  private http = inject(HttpClient);

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

  getCover(advert: MarketplaceAdvert): string | null {
    const image = advert.media?.find((m) => m.media_type === 'image') || advert.media?.[0];
    return image?.media_url ?? null;
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
      .get<PaginatedResponse<MarketplaceAdvert>>(`${API_CONFIG.BASE_URL}/adverts`, { params })
      .subscribe({
        next: (response) => {
          this.adverts = response.data ?? [];
          this.total = response.total ?? this.adverts.length;
          this.loading = false;
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
  imports: [CommonModule],
  template: `
    <div class="p-8">
      <div class="mb-6 flex items-center justify-between">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-yellow-400 to-orange-400 bg-clip-text text-transparent">Eventos</h2>
        <button
          class="px-4 py-2 rounded-lg bg-yellow-600 hover:bg-yellow-500 text-white text-sm font-semibold transition"
        >
          + Crear evento
        </button>
      </div>
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
  `,
})
export class EventsComponent {}

@Component({
  selector: 'app-universe',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="p-8">
      <div class="mb-6 flex items-center justify-between">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">El Universo</h2>
        <div class="flex gap-2">
          <button
            class="px-4 py-2 rounded-lg bg-purple-600 hover:bg-purple-500 text-white text-sm font-semibold transition"
          >
            + Crear post
          </button>
          <button
            (click)="reload()"
            class="px-3 py-2 rounded-lg border bg-slate-800/60 border-slate-700 text-slate-200 text-sm hover:bg-slate-700/60"
          >
            Recargar
          </button>
        </div>
      </div>

      <div *ngIf="loading" class="rounded-lg border border-slate-700 bg-slate-800/50 p-4 text-slate-300 mb-4">
        Cargando publicaciones...
      </div>

      <div *ngIf="errorMessage" class="rounded-lg border border-red-500/40 bg-red-500/10 p-4 text-red-200 mb-4">
        {{ errorMessage }}
      </div>

      <div class="mb-4 text-sm text-slate-400">
        Total publicaciones: <span class="text-slate-100 font-semibold">{{ total }}</span>
      </div>

      <div class="space-y-4">
        <article *ngFor="let post of posts" class="bg-slate-800/50 p-6 rounded-lg border border-slate-700 hover:border-purple-500/50 transition">
          <div class="flex items-start gap-4">
            <span class="text-3xl">🧑‍🔧</span>
            <div class="flex-1">
              <p class="font-bold">{{ post.author?.user_name || 'Usuario' }}</p>
              <p *ngIf="post.title" class="text-sm text-slate-300 mt-1">{{ post.title }}</p>
              <p class="text-slate-400 mt-2">{{ post.content }}</p>

              <img
                *ngIf="getFirstPostImage(post) as imageUrl"
                [src]="imageUrl"
                alt="post media"
                class="mt-3 w-full max-h-72 object-cover rounded-lg border border-slate-700"
              />

              <div class="flex gap-4 mt-3 text-slate-400 text-sm">
                <span>❤️ {{ post.likes?.length || 0 }}</span>
                <span>💬 {{ post.comments?.length || 0 }}</span>
                <span *ngIf="post.created_at">🕒 {{ post.created_at | date:'short' }}</span>
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

  getFirstPostImage(post: UniversePost): string | null {
    const image = post.media?.find((m) => m.media_type === 'image') || post.media?.[0];
    return image?.media_url ?? null;
  }

  private loadPosts(): void {
    this.loading = true;
    this.errorMessage = '';

    this.http.get<PaginatedResponse<UniversePost>>(`${API_CONFIG.BASE_URL}/posts`).subscribe({
      next: (response) => {
        this.posts = response.data ?? [];
        this.total = response.total ?? this.posts.length;
        this.loading = false;
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
  imports: [CommonModule],
  template: `
    <div class="p-8">
      <h2 class="text-3xl font-bold mb-4 bg-gradient-to-r from-orange-400 to-red-400 bg-clip-text text-transparent">Mi Garaje</h2>
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
  `,
})
export class GarageComponent {}

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="p-8">
      <div class="mb-6 flex items-center justify-between">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-green-400 to-cyan-400 bg-clip-text text-transparent">Mi Perfil</h2>
        <button (click)="reload()" class="px-3 py-2 rounded-lg border bg-slate-800/60 border-slate-700 text-slate-200 text-sm hover:bg-slate-700/60">
          Recargar
        </button>
      </div>

      <div *ngIf="loading" class="rounded-lg border border-slate-700 bg-slate-800/50 p-4 text-slate-300 mb-4">
        Cargando perfil...
      </div>

      <div *ngIf="errorMessage" class="rounded-lg border border-red-500/40 bg-red-500/10 p-4 text-red-200 mb-4">
        {{ errorMessage }}
      </div>

      <div *ngIf="profile" class="max-w-4xl grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-slate-800/50 p-6 rounded-lg border border-slate-700">
          <h3 class="font-bold mb-4 text-lg">Información Personal</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
  profile: ProfileDetail | null = null;

  ngOnInit(): void {
    this.loadProfile();
  }

  reload(): void {
    this.loadProfile();
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
