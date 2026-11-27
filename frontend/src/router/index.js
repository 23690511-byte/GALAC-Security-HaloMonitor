/**
 * HaloMonitor - Router con Navigation Guards y RBAC Estricto
 * Reglas de Acceso:
 * - Administrador: Acceso Total (/dashboard, /users, /incidents, /alerts, /reports, /quality)
 * - Analista: Acceso Operativo SOC (/dashboard, /incidents, /alerts, /reports, /quality)
 * - Auditor: Acceso Solo Lectura (/dashboard, /incidents, /reports, /quality)
 * - Cliente: Acceso Portal de Incidentes (/incidents)
 */
import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '../composables/useAuth';

import LoginView from '../views/LoginView.vue';
import DashboardView from '../views/DashboardView.vue';
import UsersView from '../views/UsersView.vue';
import IncidentsView from '../views/IncidentsView.vue';
import AlertsView from '../views/AlertsView.vue';
import ReportsView from '../views/ReportsView.vue';

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: LoginView,
    meta: { requiresAuth: false }
  },
  {
    path: '/',
    redirect: (to) => {
      const auth = useAuth();
      const role = auth.user.value?.rol || '';
      return (role.toLowerCase() === 'cliente') ? '/incidents' : '/dashboard';
    }
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
    path: '/users',
    name: 'Users',
    component: UsersView,
    meta: {
      requiresAuth: true,
      roles: ['Administrador'] // Exclusivo Administrador
    }
  },
  {
    path: '/incidents',
    name: 'Incidents',
    component: IncidentsView,
    meta: {
      requiresAuth: true,
      roles: ['Administrador', 'Analista', 'Auditor', 'Cliente']
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
    path: '/:pathMatch(.*)*',
    redirect: '/incidents'
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
  const userRole = auth.user.value?.rol || '';

  // 1. Si la ruta requiere autenticación y el usuario no está autenticado -> al Login
  if (to.meta.requiresAuth && !isAuth) {
    return next({ name: 'Login', query: { redirect: to.fullPath } });
  }

  // 2. Si ya está autenticado e intenta ir a Login -> a su vista principal
  if (to.name === 'Login' && isAuth) {
    return next(userRole.toLowerCase() === 'cliente' ? { name: 'Incidents' } : { name: 'Dashboard' });
  }

  // 3. Validación de Roles RBAC
  if (to.meta.requiresAuth && to.meta.roles && to.meta.roles.length > 0) {
    const hasRole = to.meta.roles.some(r => r.toLowerCase() === userRole.toLowerCase());
    
    if (!hasRole) {
      // Redirigir a la vista permitida según el rol
      const targetRoute = userRole.toLowerCase() === 'cliente' ? 'Incidents' : 'Dashboard';
      return next({ name: targetRoute });
    }
  }

  next();
});

export default router;
