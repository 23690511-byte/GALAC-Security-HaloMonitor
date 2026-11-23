/**
 * HaloMonitor - Servicio del Dashboard y Métricas
 */
import { apiFetch } from './http';

export const dashboardApi = {
  async getStats() {
    return apiFetch('/dashboard/stats.php', { method: 'GET' });
  }
};
