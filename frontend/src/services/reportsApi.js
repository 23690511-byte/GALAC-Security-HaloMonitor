/**
 * HaloMonitor - Servicio de Reportes y Auditoría CMMI / MoProSoft
 */
import { apiFetch } from './http';

export const reportsApi = {
  async getReportsData() {
    return apiFetch('/reports/index.php', { method: 'GET' });
  }
};
