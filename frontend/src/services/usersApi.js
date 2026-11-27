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
    return apiFetch('/users/index.php');
  },

  /**
   * Crea un nuevo usuario en la plataforma con rol asignado
   */
  async createUser(payload) {
    return apiFetch('/users/create.php', {
      method: 'POST',
      body: payload
    });
  },

  /**
   * Edita el rol, departamento y nombre de un usuario
   */
  async updateUser(payload) {
    return apiFetch('/users/update.php', {
      method: 'POST',
      body: payload
    });
  },

  /**
   * Alterna el estado activo/inactivo de un usuario
   */
  async toggleUserStatus(id) {
    return apiFetch('/users/toggle_status.php', {
      method: 'POST',
      body: { id }
    });
  }
};
