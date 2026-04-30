import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { forkJoin, of } from 'rxjs';
import { catchError, filter, switchMap, take } from 'rxjs/operators';
import { SidebarComponent } from '../shared/components/sidebar.component';
import { API_CONFIG } from '../core/config/api.config';
import { AuthService, User } from '../core/services/auth.service';

interface PaginatedResponse<T> {
  data: T[];
  total: number;
}

interface AppUserDetail {
  user_id: number;
  user_name: string;
  posts?: unknown[];
  garage?: unknown[];
}

interface CarAdvert {
  ad_id: number;
  ad_title: string;
  price: number;
  city?: string;
}

interface EventKdd {
  event_id: number;
  title: string;
  event_date: string;
  city?: string;
}

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule, SidebarComponent],
  template: `
    <div class="flex h-screen bg-slate-900 text-slate-100">
      <app-sidebar></app-sidebar>

      <main class="flex-1 overflow-hidden flex flex-col">
        <header class="bg-slate-800/50 backdrop-blur-xl border-b border-slate-700/50 px-8 py-6">
          <div class="flex items-center justify-between">
            <h1 class="text-2xl font-black bg-gradient-to-r from-green-400 to-cyan-400 bg-clip-text text-transparent">
              Dashboard
            </h1>
            <div class="text-sm text-slate-400">
              {{ currentUserName }}
            </div>
          </div>
        </header>

        <div class="flex-1 overflow-auto">
          <div class="p-8">
            <div class="mb-8 p-6 bg-gradient-to-br from-green-500/10 to-cyan-500/10 border border-green-500/30 rounded-xl">
              <h2 class="text-xl font-bold text-slate-100 mb-2">Datos reales desde tu base de datos</h2>
              <p class="text-slate-400">Este panel consume tu API Laravel en tiempo real.</p>
            </div>

            <div *ngIf="loading" class="mb-8 p-4 rounded-lg border border-slate-700 bg-slate-800/40 text-slate-300">
              Cargando datos del dashboard...
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
              <div class="stat-card">
                <div class="flex items-center justify-between mb-4">
                  <span class="text-3xl">🚗</span>
                </div>
                <p class="text-slate-400 text-sm">Anuncios activos</p>
                <p class="text-2xl font-bold">{{ stats.advertsTotal }}</p>
              </div>

              <div class="stat-card">
                <div class="flex items-center justify-between mb-4">
                  <span class="text-3xl">📝</span>
                </div>
                <p class="text-slate-400 text-sm">Mis publicaciones</p>
                <p class="text-2xl font-bold">{{ stats.myPosts }}</p>
                <p *ngIf="!endpointStatus.user" class="text-xs text-red-300 mt-1">No se pudo leer /users/id</p>
              </div>

              <div class="stat-card">
                <div class="flex items-center justify-between mb-4">
                  <span class="text-3xl">📅</span>
                </div>
                <p class="text-slate-400 text-sm">Eventos próximos</p>
                <p class="text-2xl font-bold">{{ stats.eventsTotal }}</p>
                <p *ngIf="!endpointStatus.events" class="text-xs text-amber-300 mt-1">Endpoint /events no disponible</p>
              </div>

              <div class="stat-card">
                <div class="flex items-center justify-between mb-4">
                  <span class="text-3xl">🛠️</span>
                </div>
                <p class="text-slate-400 text-sm">Coches en mi garaje</p>
                <p class="text-2xl font-bold">{{ stats.garageTotal }}</p>
              </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <section class="bg-slate-800/50 backdrop-blur border border-slate-700/50 rounded-xl p-6">
                <h3 class="text-lg font-bold mb-4">Últimos anuncios</h3>
                <div *ngIf="recentAdverts.length === 0" class="text-slate-400 text-sm">Sin datos de anuncios.</div>
                <div *ngFor="let advert of recentAdverts" class="py-3 border-b border-slate-700/50 last:border-b-0">
                  <p class="font-semibold">{{ advert.ad_title }}</p>
                  <p class="text-sm text-slate-400">{{ advert.city || 'Ciudad no disponible' }}</p>
                  <p class="text-sm text-green-400 font-bold">{{ advert.price | number:'1.0-0' }} €</p>
                </div>
              </section>

              <section class="bg-slate-800/50 backdrop-blur border border-slate-700/50 rounded-xl p-6">
                <h3 class="text-lg font-bold mb-4">Próximos eventos</h3>
                <div *ngIf="upcomingEvents.length === 0" class="text-slate-400 text-sm">Sin datos de eventos.</div>
                <div *ngFor="let event of upcomingEvents" class="py-3 border-b border-slate-700/50 last:border-b-0">
                  <p class="font-semibold">{{ event.title }}</p>
                  <p class="text-sm text-slate-400">{{ event.city || 'Ubicación no disponible' }}</p>
                  <p class="text-sm text-cyan-400">{{ event.event_date | date:'short' }}</p>
                </div>
              </section>
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
    </style>
  `,
})
export class DashboardComponent implements OnInit {
  private http = inject(HttpClient);
  private authService = inject(AuthService);

  loading = true;
  currentUserName = 'Usuario';

  stats = {
    advertsTotal: 0,
    myPosts: 0,
    eventsTotal: 0,
    garageTotal: 0,
  };

  recentAdverts: CarAdvert[] = [];
  upcomingEvents: EventKdd[] = [];

  endpointStatus = {
    user: true,
    events: true,
  };

  ngOnInit(): void {
    this.authService
      .getCurrentUser()
      .pipe(
        filter((user): user is User => !!user),
        take(1),
        switchMap((user) => {
          this.currentUserName = user.user_name || 'Usuario';

          const userRequest = this.http
            .get<AppUserDetail>(`${API_CONFIG.BASE_URL}/users/${user.user_id}`)
            .pipe(catchError(() => of(null)));

          const advertsRequest = this.http
            .get<PaginatedResponse<CarAdvert>>(`${API_CONFIG.BASE_URL}/adverts`)
            .pipe(catchError(() => of({ data: [], total: 0 } as PaginatedResponse<CarAdvert>)));

          const eventsRequest = this.http
            .get<PaginatedResponse<EventKdd>>(`${API_CONFIG.BASE_URL}/events`)
            .pipe(catchError(() => of(null)));

          return forkJoin({
            userDetail: userRequest,
            adverts: advertsRequest,
            events: eventsRequest,
          });
        })
      )
      .subscribe(({ userDetail, adverts, events }) => {
        this.endpointStatus.user = !!userDetail;
        this.endpointStatus.events = !!events;

        this.stats.advertsTotal = adverts.total ?? 0;
        this.stats.myPosts = userDetail?.posts?.length ?? 0;
        this.stats.garageTotal = userDetail?.garage?.length ?? 0;
        this.stats.eventsTotal = events?.total ?? 0;

        this.recentAdverts = (adverts.data ?? []).slice(0, 3);
        this.upcomingEvents = (events?.data ?? []).slice(0, 3);

        this.loading = false;
      });
  }
}
