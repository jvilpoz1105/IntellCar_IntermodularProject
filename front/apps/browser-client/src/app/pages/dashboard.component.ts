import { Component, OnInit, inject, ChangeDetectorRef, ElementRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { Router, RouterOutlet } from '@angular/router';
import { forkJoin, of } from 'rxjs';
import { catchError, filter, switchMap, take } from 'rxjs/operators';
import { SidebarComponent } from '../shared/components/sidebar.component';
import { API_CONFIG } from '../core/config/api.config';
import { AuthService, User } from '../core/services/auth.service';
import gsap from 'gsap';

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
  imports: [CommonModule, SidebarComponent, RouterOutlet],
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
          <ng-container *ngIf="isOverview; else childView">
          <div class="p-8">
            <div class="mb-8 p-6 bg-gradient-to-br from-green-500/10 to-cyan-500/10 border border-green-500/30 rounded-xl">
              <h2 class="text-xl font-bold text-slate-100 mb-2">¡Bienvenido de nuevo, {{ currentUserName }}!</h2>
              <p class="text-slate-400">Disfruta de tu experiencia en IntellCar.</p>
            </div>

            <div *ngIf="loading" class="mb-8">
              <!-- Skeleton stat-cards -->
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8 animate-pulse">
                <div *ngFor="let i of [1,2,3,4]" class="stat-card">
                  <div class="h-7 w-7 rounded bg-slate-700/60 mb-4"></div>
                  <div class="h-3 bg-slate-700/60 rounded w-2/3 mb-2"></div>
                  <div class="h-8 bg-slate-700/60 rounded w-1/3"></div>
                </div>
              </div>
              <!-- Skeleton lists -->
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-pulse">
                <div class="bg-slate-800/50 border border-slate-700/50 rounded-xl p-6">
                  <div class="h-5 bg-slate-700/60 rounded w-1/3 mb-4"></div>
                  <div *ngFor="let i of [1,2,3]" class="py-3 border-b border-slate-700/50 last:border-b-0 space-y-2">
                    <div class="h-4 bg-slate-700/60 rounded w-3/4"></div>
                    <div class="h-3 bg-slate-700/60 rounded w-1/2"></div>
                    <div class="h-3 bg-slate-700/60 rounded w-1/4"></div>
                  </div>
                </div>
                <div class="bg-slate-800/50 border border-slate-700/50 rounded-xl p-6">
                  <div class="h-5 bg-slate-700/60 rounded w-1/3 mb-4"></div>
                  <div *ngFor="let i of [1,2,3]" class="py-3 border-b border-slate-700/50 last:border-b-0 space-y-2">
                    <div class="h-4 bg-slate-700/60 rounded w-3/4"></div>
                    <div class="h-3 bg-slate-700/60 rounded w-1/2"></div>
                    <div class="h-3 bg-slate-700/60 rounded w-1/4"></div>
                  </div>
                </div>
              </div>
            </div>

            <div *ngIf="!loading">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
              <div class="stat-card">
                <div class="flex items-center justify-between mb-4">
                  <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400"><path d="M19 17H5v-6l2-4h10l2 4v6Z"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/></svg>
                </div>
                <p class="text-slate-400 text-sm">Anuncios activos</p>
                <p class="text-2xl font-bold">{{ displayStats.advertsTotal }}</p>
              </div>

              <div class="stat-card">
                <div class="flex items-center justify-between mb-4">
                  <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-purple-400"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                </div>
                <p class="text-slate-400 text-sm">Mis publicaciones</p>
                <p class="text-2xl font-bold">{{ displayStats.myPosts }}</p>
                <p *ngIf="!endpointStatus.user" class="text-xs text-red-300 mt-1">No se pudo leer /users/id</p>
              </div>

              <div class="stat-card">
                <div class="flex items-center justify-between mb-4">
                  <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-yellow-400"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                </div>
                <p class="text-slate-400 text-sm">Eventos próximos</p>
                <p class="text-2xl font-bold">{{ displayStats.eventsTotal }}</p>
                <p *ngIf="!endpointStatus.events" class="text-xs text-amber-300 mt-1">Endpoint /events no disponible</p>
              </div>

              <div class="stat-card">
                <div class="flex items-center justify-between mb-4">
                  <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-orange-400"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                </div>
                <p class="text-slate-400 text-sm">Coches en mi garaje</p>
                <p class="text-2xl font-bold">{{ displayStats.garageTotal }}</p>
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
          </ng-container>

          <ng-template #childView>
            <router-outlet></router-outlet>
          </ng-template>
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
  private router = inject(Router);
  private cdr = inject(ChangeDetectorRef);
  private el = inject(ElementRef);

  loading = true;
  currentUserName = 'Usuario';

  stats = { advertsTotal: 0, myPosts: 0, eventsTotal: 0, garageTotal: 0 };
  displayStats = { advertsTotal: 0, myPosts: 0, eventsTotal: 0, garageTotal: 0 };

  recentAdverts: CarAdvert[] = [];
  upcomingEvents: EventKdd[] = [];

  endpointStatus = {
    user: true,
    events: true,
  };

  get isOverview(): boolean {
    return this.router.url === '/dashboard';
  }

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
            .get<PaginatedResponse<CarAdvert>>(`${API_CONFIG.BASE_URL}/market`)
            .pipe(catchError(() => of({ data: [], total: 0 } as PaginatedResponse<CarAdvert>)));

          const eventsRequest = this.http
            .get<PaginatedResponse<EventKdd>>(`${API_CONFIG.BASE_URL}/kdds`)
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

        this.stats.advertsTotal = adverts.meta?.total ?? adverts.total ?? 0;
        this.stats.myPosts = userDetail?.posts?.length ?? 0;
        this.stats.garageTotal = userDetail?.garage?.length ?? 0;
        this.stats.eventsTotal = events?.meta?.total ?? events?.total ?? 0;

        this.recentAdverts = (adverts.data ?? []).slice(0, 3);
        this.upcomingEvents = (events?.data ?? []).slice(0, 3);

        this.loading = false;

        const target = { ...this.displayStats };
        gsap.to(target, {
          advertsTotal: this.stats.advertsTotal,
          myPosts: this.stats.myPosts,
          eventsTotal: this.stats.eventsTotal,
          garageTotal: this.stats.garageTotal,
          duration: 1.5,
          ease: 'power2.out',
          onUpdate: () => {
            this.displayStats.advertsTotal = Math.round(target.advertsTotal);
            this.displayStats.myPosts = Math.round(target.myPosts);
            this.displayStats.eventsTotal = Math.round(target.eventsTotal);
            this.displayStats.garageTotal = Math.round(target.garageTotal);
            this.cdr.detectChanges();
          }
        });

        setTimeout(() => {
          gsap.from(this.el.nativeElement.querySelectorAll('.stat-card'), {
            y: 20, opacity: 0, duration: 0.5, stagger: 0.1, ease: 'power2.out', clearProps: 'all'
          });
        }, 0);
      });
  }
}
