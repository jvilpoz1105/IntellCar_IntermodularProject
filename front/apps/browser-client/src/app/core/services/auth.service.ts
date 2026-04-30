import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, Observable, throwError } from 'rxjs';
import { tap, catchError } from 'rxjs/operators';
import { API_CONFIG } from '../config/api.config';

export interface User {
  user_id: number;
  email_address: string;
  user_name: string;
  phone?: string;
  address?: string;
  paddock_id?: number;
}

export interface LoginRequest {
  email_address: string;
  user_password: string;
}

export interface AuthResponse {
  token: string;
  user?: User;
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private http = inject(HttpClient);
  private currentUser$ = new BehaviorSubject<User | null>(null);
  private isAuthenticated$ = new BehaviorSubject<boolean>(false);

  constructor() {
    this.loadUserFromStorage();
  }

  private loadUserFromStorage(): void {
    const token = localStorage.getItem('authToken');
    if (token) {
      this.isAuthenticated$.next(true);
      this.loadCurrentUser();
    }
  }

  private loadCurrentUser(): void {
    const url = `${API_CONFIG.BASE_URL}/auth/me`;
    this.http.get<User>(url).subscribe({
      next: (user) => {
        this.currentUser$.next(user);
      },
      error: (error) => {
        console.error('Error loading user:', error);
        this.logout();
      }
    });
  }

  login(email: string, password: string): Observable<AuthResponse> {
    const url = `${API_CONFIG.BASE_URL}/auth/login`;
    const body: LoginRequest = {
      email_address: email,
      user_password: password
    };

    return this.http.post<AuthResponse>(url, body).pipe(
      tap((response) => {
        // Guardar el token
        if (response.token) {
          localStorage.setItem('authToken', response.token);
          this.isAuthenticated$.next(true);
          
          // Cargar los datos del usuario
          this.loadCurrentUser();
        }
      }),
      catchError((error) => {
        console.error('Error de autenticación:', error);
        const errorMessage = error.error?.message || 'Error en la autenticación';
        return throwError(() => new Error(errorMessage));
      })
    );
  }

  logout(): void {
    const url = `${API_CONFIG.BASE_URL}/auth/logout`;
    
    this.http.post(url, {}).subscribe({
      next: () => {
        this.clearAuth();
      },
      error: (error) => {
        console.error('Error en logout:', error);
        this.clearAuth();
      }
    });
  }

  private clearAuth(): void {
    localStorage.removeItem('authToken');
    this.currentUser$.next(null);
    this.isAuthenticated$.next(false);
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

  getToken(): string | null {
    return localStorage.getItem('authToken');
  }
}

