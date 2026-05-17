import { TestBed } from '@angular/core/testing';
import { provideHttpClient } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { AuthService } from './auth.service';
import { API_CONFIG } from '../config/api.config';

describe('AuthService', () => {
  let service: AuthService;
  let httpTesting: HttpTestingController;

  beforeEach(() => {
    localStorage.clear();

    TestBed.configureTestingModule({
      providers: [
        provideHttpClient(),
        provideHttpClientTesting(),
      ],
    });

    service = TestBed.inject(AuthService);
    httpTesting = TestBed.inject(HttpTestingController);
  });

  afterEach(() => {
    httpTesting.verify();
    localStorage.clear();
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });

  it('getToken() should return null when localStorage has no token', () => {
    expect(service.getToken()).toBeNull();
  });

  it('getToken() should return the token stored in localStorage', () => {
    localStorage.setItem('authToken', 'test-token-123');
    expect(service.getToken()).toBe('test-token-123');
  });

  it('getIsAuthenticatedSync() should return false when no token on init', () => {
    expect(service.getIsAuthenticatedSync()).toBeFalse();
  });

  it('login() should call the login endpoint with correct credentials', () => {
    service.login('user@test.com', 'secret').subscribe();

    const req = httpTesting.expectOne(`${API_CONFIG.BASE_URL}/auth/login`);
    expect(req.request.method).toBe('POST');
    expect(req.request.body).toEqual({
      email_address: 'user@test.com',
      user_password: 'secret',
    });

    req.flush({ token: 'abc123' });
    httpTesting
      .expectOne(`${API_CONFIG.BASE_URL}/auth/me`)
      .flush({ user_id: 1, user_name: 'Test', email_address: 'user@test.com' });
  });

  it('login() should store the token and set authenticated to true', () => {
    service.login('user@test.com', 'secret').subscribe();

    httpTesting.expectOne(`${API_CONFIG.BASE_URL}/auth/login`).flush({ token: 'my-token' });
    httpTesting
      .expectOne(`${API_CONFIG.BASE_URL}/auth/me`)
      .flush({ user_id: 1, user_name: 'Test', email_address: 'user@test.com' });

    expect(localStorage.getItem('authToken')).toBe('my-token');
    expect(service.getIsAuthenticatedSync()).toBeTrue();
  });

  it('login() should propagate error message on failure', () => {
    let receivedError: Error | null = null;

    service.login('bad@test.com', 'wrong').subscribe({
      error: (err) => (receivedError = err),
    });

    httpTesting.expectOne(`${API_CONFIG.BASE_URL}/auth/login`).flush(
      { message: 'Credenciales incorrectas' },
      { status: 422, statusText: 'Unprocessable Entity' }
    );

    expect(receivedError).toBeTruthy();
    expect((receivedError as Error).message).toBe('Credenciales incorrectas');
  });

  it('logout() should call the logout endpoint', () => {
    service.logout();

    const req = httpTesting.expectOne(`${API_CONFIG.BASE_URL}/auth/logout`);
    expect(req.request.method).toBe('POST');
    req.flush({});
  });

  it('logout() should remove token from localStorage', () => {
    localStorage.setItem('authToken', 'existing-token');

    service.logout();

    httpTesting.expectOne(`${API_CONFIG.BASE_URL}/auth/logout`).flush({});

    expect(localStorage.getItem('authToken')).toBeNull();
  });
});
