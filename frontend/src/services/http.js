/**
 * HaloMonitor - Cliente HTTP Centralizado (apiFetch)
 * Requisitos:
 * - Función reutilizable apiFetch()
 * - Gestión automática de headers JSON
 * - Envío de credenciales/sesión (credentials: 'include')
 * - Captura centralizada de errores HTTP con payload estructurado
 */

// Detecta si corre bajo el proxy de Vite o puerto directo
const BASE_URL = import.meta.env.VITE_API_BASE_URL || '/backend/api';

export async function apiFetch(endpoint, options = {}) {
  const url = endpoint.startsWith('http') 
    ? endpoint 
    : `${BASE_URL}${endpoint.startsWith('/') ? '' : '/'}${endpoint}`;

  const defaultHeaders = {
    'Accept': 'application/json',
    ...(options.body && !(options.body instanceof FormData) 
        ? { 'Content-Type': 'application/json' } 
        : {})
  };

  const config = {
    ...options,
    credentials: 'include', // Manejo de cookies de sesión PHP
    headers: {
      ...defaultHeaders,
      ...options.headers
    }
  };

  if (config.body && typeof config.body === 'object' && !(config.body instanceof FormData)) {
    config.body = JSON.stringify(config.body);
  }

  try {
    const response = await fetch(url, config);

    // Leer respuesta como JSON si es posible
    let data = null;
    const contentType = response.headers.get('content-type');
    if (contentType && contentType.includes('application/json')) {
      data = await response.json();
    } else {
      const text = await response.text();
      data = { ok: response.ok, message: text || response.statusText, data: null };
    }

    if (!response.ok) {
      const error = new Error(data?.message || `Error HTTP ${response.status}: ${response.statusText}`);
      error.status = response.status;
      error.data = data;
      error.isHttpError = true;
      throw error;
    }

    return data;
  } catch (err) {
    // Si ya es un error estructurado, relanzarlo
    if (err.isHttpError) {
      throw err;
    }

    // Errores de red o conexión
    const networkError = new Error(err.message || 'Error de conexión con el servidor de HaloMonitor');
    networkError.status = 0;
    networkError.data = { ok: false, message: networkError.message, data: null };
    throw networkError;
  }
}
