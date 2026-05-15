import { Injectable, signal, computed, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment';
import { VehicleMake, VehicleModel, VehicleEngine, VehiclePaddock } from './publish.types';

@Injectable({
  providedIn: 'root'
})
export class CatalogService {
  private readonly API_BASE = environment.apiUrl;
  private http = inject(HttpClient);

  // --- State signals ---
  readonly makes    = signal<VehicleMake[]>([]);
  readonly models   = signal<VehicleModel[]>([]);
  readonly engines  = signal<VehicleEngine[]>([]);
  readonly paddocks = signal<VehiclePaddock[]>([]);

  readonly loadingMakes    = signal(false);
  readonly loadingModels   = signal(false);
  readonly loadingEngines  = signal(false);
  readonly loadingPaddocks = signal(false);

  // --- Computed options for SmartField selects ---
  readonly makeOptions = computed(() => [
    { label: 'Selecciona una marca...', value: '' },
    ...this.makes().map(m => ({ label: m.make_name, value: String(m.make_id) }))
  ]);

  readonly modelOptions = computed(() => [
    { label: this.models().length ? 'Selecciona un modelo...' : 'Primero selecciona una marca', value: '' },
    ...this.models().map(m => ({ label: m.model_name, value: String(m.model_id) }))
  ]);

  readonly engineOptions = computed(() => [
    { label: this.engines().length ? 'Selecciona un motor...' : 'Primero selecciona una marca', value: '' },
    ...this.engines().map(e => ({
      label: e.fuel_type ? `${e.engine_name} (${e.fuel_type})` : e.engine_name,
      value: String(e.engine_id)
    }))
  ]);

  readonly paddockOptions = computed(() =>
    this.paddocks().map(p => ({ label: p.paddock_name, value: p.paddock_id }))
  );

  // --- Loaders ---
  loadMakes(): void {
    if (this.makes().length > 0) return; // Cache simple
    this.loadingMakes.set(true);
    this.http.get<VehicleMake[]>(`${this.API_BASE}/makes`).subscribe({
      next: data => { this.makes.set(data); this.loadingMakes.set(false); },
      error: () => this.loadingMakes.set(false)
    });
  }

  loadModels(makeId: number | string): void {
    this.models.set([]);
    this.engines.set([]);
    if (!makeId) return;
    this.loadingModels.set(true);
    this.http.get<VehicleModel[]>(`${this.API_BASE}/makes/${makeId}/models`).subscribe({
      next: data => { this.models.set(data); this.loadingModels.set(false); },
      error: () => this.loadingModels.set(false)
    });
    // Cargar motores también al cambiar la marca
    this.loadEngines(makeId);
  }

  loadEngines(makeId: number | string): void {
    if (!makeId) { this.engines.set([]); return; }
    this.loadingEngines.set(true);
    this.http.get<VehicleEngine[]>(`${this.API_BASE}/makes/${makeId}/engines`).subscribe({
      next: data => { this.engines.set(data); this.loadingEngines.set(false); },
      error: () => this.loadingEngines.set(false)
    });
  }

  loadPaddocks(): void {
    if (this.paddocks().length > 0) return; // Cache simple
    this.loadingPaddocks.set(true);
    this.http.get<VehiclePaddock[]>(`${this.API_BASE}/paddocks`).subscribe({
      next: data => { this.paddocks.set(data); this.loadingPaddocks.set(false); },
      error: () => this.loadingPaddocks.set(false)
    });
  }

  reset(): void {
    this.models.set([]);
    this.engines.set([]);
  }
}
