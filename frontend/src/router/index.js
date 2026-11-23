/**
 * HaloMonitor - Router con Navigation Guards y RBAC
 * Requisitos:
 * - meta: { requiresAuth: true, roles: [...] }
 * - Redirección de usuarios no autenticados a /login
 * - Redirección de usuarios sin permisos a vista permitida
 */
import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '../composables/useAuth';

import LoginView from '../views/LoginView.vue';
import DashboardView from '../views/DashboardView.vue';
import IncidentsView from '../views/IncidentsView.vue';
import AlertsView from '../views/AlertsView.vue';
import ReportsView from '../views/ReportsView.vue';
import QualityMetricsView from '../views/QualityMetricsView.vue';

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: LoginView,
    meta: { requiresAuth: false }
  },
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: DashboardView,
    meta: {
      requiresAuth: true,
      roles: ['Administrador', 'Analista', 'Auditor']
    }
  },
  {
    path: '/incidents',
    name: 'Incidents',
    component: IncidentsView,
    meta: {
      requiresAuth: true,
      roles: ['Administrador', 'Analista', 'Auditor']
    }
  },
  {
    path: '/alerts',
    name: 'Alerts',
    component: AlertsView,
    meta: {
      requiresAuth: true,
      roles: ['Administrador', 'Analista']
    }
  },
  {
    path: '/reports',
    name: 'Reports',
    component: ReportsView,
    meta: {
      requiresAuth: true,
      roles: ['Administrador', 'Analista', 'Auditor']
    }
  },
  {
    path: '/quality',
    name: 'Quality',
    component: QualityMetricsView,
    meta: {
      requiresAuth: true,
      roles: ['Administrador', 'Analista', 'Auditor']
    }
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/dashboard'
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach(async (to, from, next) => {
  const auth = useAuth();

  // Asegurar que el estado de sesión esté verificado con el backend
  if (!auth.authInitialized.value) {
    await auth.checkAuth();
  }

  const isAuth = auth.isAuthenticated.value;

  // Si requiere autenticación y no está autenticado -> a Login
  if (to.meta.requiresAuth && !isAuth) {
    return next({ name: 'Login', query: { redirect: to.fullPath } });
  }

  // Si ya está autenticado e intenta ir a login -> a Dashboard
  if (to.name === 'Login' && isAuth) {
    return next({ name: 'Dashboard' });
  }

  // Validación de Roles RBAC
  if (to.meta.requiresAuth && to.meta.roles && to.meta.roles.length > 0) {
    const userRole = auth.user.value?.rol || '';
    const hasRole = to.meta.roles.some(r => r.toLowerCase() === userRole.toLowerCase());
    
    if (!hasRole) {
      alert(`Acceso Restringido: Su rol (${userRole}) no tiene permisos para acceder a esta sección.`);
      return next({ name: 'Dashboard' });
    }
  }

  next();
});

export default router;
