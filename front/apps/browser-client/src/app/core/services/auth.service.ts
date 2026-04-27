import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';

export interface User {
  id: string;
  email: string;
  name: string;
  profile: 'admin' | 'professional' | 'individual' | 'tuner' | 'press';
  avatar?: string;
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private currentUser$ = new BehaviorSubject<User | null>(null);
  private isAuthenticated$ = new BehaviorSubject<boolean>(false);

  constructor() {
    this.loadUserFromStorage();
  }

  private loadUserFromStorage(): void {
    const stored = localStorage.getItem('currentUser');
    if (stored) {
      const user = JSON.parse(stored);
      this.currentUser$.next(user);
      this.isAuthenticated$.next(true);
    }
  }

  login(email: string, password: string): Observable<User> {
    return new Observable((observer) => {
      // Simulamos delay de API
      setTimeout(() => {
        // Validación mock
        const mockUsers: { [key: string]: User } = {
          'admin@intellcar.com': {
            id: '1',
            email: 'admin@intellcar.com',
            name: 'Administrador',
            profile: 'admin',
            avatar: '👨‍💼'
          },
          'carlos@ferrari.com': {
            id: '2',
            email: 'carlos@ferrari.com',
            name: 'Carlos',
            profile: 'professional',
            avatar: '🏎️'
          },
          'maria.gonzalez@email.com': {
            id: '3',
            email: 'maria.gonzalez@email.com',
            name: 'María González',
            profile: 'individual',
            avatar: '👩‍🔧'
          },
          'juan.perez@tuning.com': {
            id: '4',
            email: 'juan.perez@tuning.com',
            name: 'Juan Pérez',
            profile: 'tuner',
            avatar: '🚗'
          },
          'laura@motorpress.com': {
            id: '5',
            email: 'laura@motorpress.com',
            name: 'Laura',
            profile: 'press',
            avatar: '📰'
          }
        };

        if (mockUsers[email] && password === 'password123') {
          const user = mockUsers[email];
          this.currentUser$.next(user);
          this.isAuthenticated$.next(true);
          localStorage.setItem('currentUser', JSON.stringify(user));
          observer.next(user);
          observer.complete();
        } else {
          observer.error('Credenciales inválidas');
        }
      }, 500);
    });
  }

  logout(): void {
    this.currentUser$.next(null);
    this.isAuthenticated$.next(false);
    localStorage.removeItem('currentUser');
  }

  getCurrentUser(): Observable<User | null> {
    return this.currentUser$.asObservable();
  }

  isAuthenticated(): Observable<boolean> {
    return this.isAuthenticated$.asObservable();
  }

  getIsAuthenticatedSync(): boolean {
    return this.isAuthenticated$.value;
  }
}
