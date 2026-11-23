/**
 * HaloMonitor - Servicio de Alertas Internas
 */
import { apiFetch } from './http';

export const alertsApi = {
  async getAlerts(params = {}) {
    const query = new URLSearchParams(params).toString();
    const endpoint = `/alerts/index.php${query ? `?${query}` : ''}`;
    return apiFetch(endpoint, { method: 'GET' });
  },

  async markAsRead(id) {
    return apiFetch('/alerts/read.php', {
      method: 'POST',
      body: { id }
    });
  },

  async markAllAsRead() {
    return apiFetch('/alerts/read.php', {
      method: 'POST',
      body: { all: true }
    });
  },

  async getRules() {
    return apiFetch('/alerts/rules.php', { method: 'GET' });
  },

  async createRule(payload) {
    return apiFetch('/alerts/rules.php', {
      method: 'POST',
      body: payload
    });
  }
};
