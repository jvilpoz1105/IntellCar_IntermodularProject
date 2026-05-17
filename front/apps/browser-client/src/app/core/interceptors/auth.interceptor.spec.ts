import { TestBed } from '@angular/core/testing';
import { provideHttpClient, withInterceptors, HttpClient } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { authInterceptor } from './auth.interceptor';

describe('authInterceptor', () => {
  let httpTesting: HttpTestingController;
  let httpClient: HttpClient;

  beforeEach(() => {
    localStorage.clear();

    TestBed.configureTestingModule({
      providers: [
        provideHttpClient(withInterceptors([authInterceptor])),
        provideHttpClientTesting(),
      ],
    });

    httpTesting = TestBed.inject(HttpTestingController);
    httpClient = TestBed.inject(HttpClient);
  });

  afterEach(() => {
    httpTesting.verify();
    localStorage.clear();
  });

  it('should add Authorization header when a token is stored', () => {
    localStorage.setItem('authToken', 'my-token');

    httpClient.get('/api/cars').subscribe();

    const req = httpTesting.expectOne('/api/cars');
    expect(req.request.headers.get('Authorization')).toBe('Bearer my-token');
    req.flush({});
  });

  it('should NOT add Authorization header when no token is stored', () => {
    httpClient.get('/api/cars').subscribe();

    const req = httpTesting.expectOne('/api/cars');
    expect(req.request.headers.get('Authorization')).toBeNull();
    req.flush({});
  });

  it('should NOT add Authorization header for login requests', () => {
    localStorage.setItem('authToken', 'my-token');

    httpClient.post('/api/auth/login', {}).subscribe();

    const req = httpTesting.expectOne('/api/auth/login');
    expect(req.request.headers.get('Authorization')).toBeNull();
    req.flush({});
  });

  it('should NOT add Authorization header for register requests', () => {
    localStorage.setItem('authToken', 'my-token');

    httpClient.post('/api/auth/register', {}).subscribe();

    const req = httpTesting.expectOne('/api/auth/register');
    expect(req.request.headers.get('Authorization')).toBeNull();
    req.flush({});
  });

  it('should add Content-Type application/json for authenticated requests', () => {
    localStorage.setItem('authToken', 'my-token');

    httpClient.get('/api/cars').subscribe();

    const req = httpTesting.expectOne('/api/cars');
    expect(req.request.headers.get('Content-Type')).toBe('application/json');
    req.flush({});
  });
});
