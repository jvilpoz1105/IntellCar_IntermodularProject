import { TestBed } from '@angular/core/testing';
import { Router, ActivatedRouteSnapshot, RouterStateSnapshot } from '@angular/router';
import { AuthGuardFn } from './auth.guard';
import { AuthService } from '../services/auth.service';

describe('AuthGuardFn', () => {
  let authServiceSpy: jasmine.SpyObj<AuthService>;
  let routerSpy: jasmine.SpyObj<Router>;

  beforeEach(() => {
    authServiceSpy = jasmine.createSpyObj('AuthService', ['getIsAuthenticatedSync']);
    routerSpy = jasmine.createSpyObj('Router', ['navigate']);

    TestBed.configureTestingModule({
      providers: [
        { provide: AuthService, useValue: authServiceSpy },
        { provide: Router, useValue: routerSpy },
      ],
    });
  });

  function runGuard(): ReturnType<typeof AuthGuardFn> {
    return TestBed.runInInjectionContext(() =>
      AuthGuardFn({} as ActivatedRouteSnapshot, {} as RouterStateSnapshot)
    );
  }

  it('should allow access when user is authenticated', () => {
    authServiceSpy.getIsAuthenticatedSync.and.returnValue(true);

    const result = runGuard();

    expect(result).toBeTrue();
    expect(routerSpy.navigate).not.toHaveBeenCalled();
  });

  it('should block access and redirect to /login when not authenticated', () => {
    authServiceSpy.getIsAuthenticatedSync.and.returnValue(false);

    const result = runGuard();

    expect(result).toBeFalse();
    expect(routerSpy.navigate).toHaveBeenCalledWith(['/login']);
  });
});
