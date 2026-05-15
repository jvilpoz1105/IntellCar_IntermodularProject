import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'relativeTime',
  standalone: true,
})
export class RelativeTimePipe implements PipeTransform {
  transform(value: string | Date | null | undefined): string {
    if (!value) return '';

    const date = typeof value === 'string' ? new Date(value) : value;
    const now = new Date();
    const diffMs = now.getTime() - date.getTime();

    if (isNaN(diffMs) || diffMs < 0) return '';

    const diffSecs = Math.floor(diffMs / 1000);
    const diffMins = Math.floor(diffSecs / 60);
    const diffHours = Math.floor(diffMins / 60);
    const diffDays = Math.floor(diffHours / 24);
    const diffWeeks = Math.floor(diffDays / 7);
    const diffMonths = Math.floor(diffDays / 30);
    const diffYears = Math.floor(diffDays / 365);

    if (diffSecs < 60) return 'hace un momento';
    if (diffMins < 60) return diffMins === 1 ? 'hace 1 minuto' : `hace ${diffMins} minutos`;
    if (diffHours < 24) return diffHours === 1 ? 'hace 1 hora' : `hace ${diffHours} horas`;
    if (diffDays < 7) return diffDays === 1 ? 'hace 1 día' : `hace ${diffDays} días`;
    if (diffWeeks < 5) return diffWeeks === 1 ? 'hace 1 semana' : `hace ${diffWeeks} semanas`;
    if (diffMonths < 12) return diffMonths === 1 ? 'hace 1 mes' : `hace ${diffMonths} meses`;
    return diffYears === 1 ? 'hace 1 año' : `hace ${diffYears} años`;
  }
}
