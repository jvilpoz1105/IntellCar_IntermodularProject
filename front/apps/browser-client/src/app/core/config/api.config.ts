import { environment } from '../../../environments/environment';

export const API_CONFIG = {
  // Ahora la URL de la API se obtiene dinámicamente según el entorno
  BASE_URL: environment.apiUrl,
};
