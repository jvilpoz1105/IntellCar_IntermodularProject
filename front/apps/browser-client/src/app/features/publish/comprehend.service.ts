import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment';
import { Observable } from 'rxjs';

export interface ComprehendResponse {
  is_valid: boolean;
  status: 'success' | 'warning' | 'error';
  warning?: string;
  error?: string;
}

@Injectable({
  providedIn: 'root'
})
export class ComprehendService {
  private readonly API_BASE = environment.apiUrl;

  constructor(private http: HttpClient) {}

  analyze(text: string): Observable<ComprehendResponse> {
    return this.http.post<ComprehendResponse>(`${this.API_BASE}/text/analyze`, { text });
  }
}
