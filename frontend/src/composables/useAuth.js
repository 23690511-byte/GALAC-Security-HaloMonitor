/**
 * HaloMonitor - Composable useAuth()
 * Gestiona estado reactivo del usuario, persistencia de sesión y roles RBAC
 */
import { ref, computed } from 'vue';
import { authApi } from '../services/authApi';

const currentUser = ref(null);
const isLoading = ref(false);
const authInitialized = ref(false);

export function useAuth() {
  const isAuthenticated = computed(() => !!currentUser.value);
  const user = computed(() => currentUser.value);
  const userRole = computed(() => currentUser.value?.rol || '');

  async function checkAuth() {
    if (authInitialized.value && currentUser.value) {
      return currentUser.value;
    }
    isLoading.value = true;
    try {
      const res = await authApi.me();
      if (res?.ok && res?.data?.user) {
        currentUser.value = res.data.user;
      } else {
        currentUser.value = null;
      }
    } catch {
      currentUser.value = null;
    } finally {
      isLoading.value = false;
      authInitialized.value = true;
    }
    return currentUser.value;
  }

  async function login(email, password) {
    isLoading.value = true;
    try {
      const res = await authApi.login(email, password);
      if (res?.ok && res?.data?.user) {
        currentUser.value = res.data.user;
        authInitialized.value = true;
        return res;
      }
      throw new Error(res?.message || 'Error en inicio de sesión');
    } finally {
      isLoading.value = false;
    }
  }

  async function logout() {
    try {
      await authApi.logout();
    } catch (e) {
      console.warn('Error en logout:', e);
    } finally {
      currentUser.value = null;
    }
  }

  function hasRole(allowedRoles) {
    if (!currentUser.value) return false;
    if (!allowedRoles || allowedRoles.length === 0) return true;
    return allowedRoles.some(r => r.toLowerCase() === (currentUser.value.rol || '').toLowerCase());
  }

  return {
    user,
    userRole,
    isAuthenticated,
    isLoading,
    authInitialized,
    checkAuth,
    login,
    logout,
    hasRole
  };
}
