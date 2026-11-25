/**
 * HaloMonitor - Servicio de Gestión de Usuarios y Roles (RBAC)
 * Exclusivo para Administrador
 */
import { apiFetch } from './http';

export const usersApi = {
  /**
   * Obtiene la lista de usuarios, roles y catálogo de sistemas
   */
  async getUsers() {
    return apiFetch('/backend/api/users/index.php');
  },

  /**
   * Crea un nuevo usuario en la plataforma con rol asignado
   */
  async createUser(payload) {
    return apiFetch('/backend/api/users/create.php', {
      method: 'POST',
      body: JSON.stringify(payload)
    });
  },

  /**
   * Alterna el estado activo/inactivo de un usuario
   */
  async toggleUserStatus(id) {
    return apiFetch('/backend/api/users/toggle_status.php', {
      method: 'POST',
      body: JSON.stringify({ id })
    });
  }
};
