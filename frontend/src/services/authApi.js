/**
 * HaloMonitor - Servicio de Autenticación
 */
import { apiFetch } from './http';

export const authApi = {
  async login(email, password) {
    return apiFetch('/auth/login.php', {
      method: 'POST',
      body: { email, password }
    });
  },

  async register(payload) {
    return apiFetch('/auth/register.php', {
      method: 'POST',
      body: payload
    });
  },

  async logout() {
    return apiFetch('/auth/logout.php', {
      method: 'POST'
    });
  },

  async me() {
    return apiFetch('/auth/me.php', {
      method: 'GET'
    });
  }
};
