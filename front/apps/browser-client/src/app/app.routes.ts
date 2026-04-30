import { Routes } from '@angular/router';
import { LoginComponent } from './pages/login.component';
import { DashboardComponent } from './pages/dashboard.component';
import { MarketplaceComponent, EventsComponent, UniverseComponent, GarageComponent, ProfileComponent } from './pages/index';
import { AuthGuardFn } from './core/guards/auth.guard';

export const routes: Routes = [
  { path: '', redirectTo: '/dashboard', pathMatch: 'full' },
  { path: 'login', component: LoginComponent },
  {
    path: 'dashboard',
    component: DashboardComponent,
    canActivate: [AuthGuardFn],
    children: [
      { path: '', component: DashboardComponent },
      { path: 'marketplace', component: MarketplaceComponent },
      { path: 'events', component: EventsComponent },
      { path: 'universe', component: UniverseComponent },
      { path: 'garage', component: GarageComponent },
      { path: 'profile', component: ProfileComponent }
    ]
  },
  { path: '**', redirectTo: '/login' }
];
