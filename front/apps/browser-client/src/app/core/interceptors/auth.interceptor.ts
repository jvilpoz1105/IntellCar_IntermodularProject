import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';

export const authInterceptor: HttpInterceptorFn = (req, next) => {
  // Ignorar peticiones a AWS S3 para evitar interferir con las firmas y el tipo de contenido
  if (req.url.includes('amazonaws.com')) {
    return next(req);
  }

  const token = localStorage.getItem('authToken');

  if (token && !req.url.includes('login') && !req.url.includes('register')) {
    req = req.clone({
      setHeaders: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });
  }

  return next(req);
};

