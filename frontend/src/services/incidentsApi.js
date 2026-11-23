/**
 * HaloMonitor - Servicio de Gestión de Incidentes
 */
import { apiFetch } from './http';

export const incidentsApi = {
  async getIncidents(params = {}) {
    const query = new URLSearchParams(params).toString();
    const endpoint = `/incidents/index.php${query ? `?${query}` : ''}`;
    return apiFetch(endpoint, { method: 'GET' });
  },

  async getIncidentDetail(id) {
    return apiFetch(`/incidents/detail.php?id=${encodeURIComponent(id)}`, { method: 'GET' });
  },

  async createIncident(payload) {
    return apiFetch('/incidents/create.php', {
      method: 'POST',
      body: payload
    });
  },

  async updateIncident(payload) {
    return apiFetch('/incidents/update.php', {
      method: 'POST',
      body: payload
    });
  },

  async getCatalogs() {
    return apiFetch('/users/index.php', { method: 'GET' });
  }
};
