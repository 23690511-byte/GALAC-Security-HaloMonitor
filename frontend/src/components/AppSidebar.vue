<template>
  <aside class="sidebar-wrapper select-none">
    <!-- Brand / Logotipo Oficial GALAC Security -->
    <div class="sidebar-brand">
      <div class="brand-shield-icon">
        <svg class="brand-svg" viewBox="0 0 24 24" fill="none">
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" fill="#2563eb" />
          <path d="M9 12l2 2 4-4" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </div>

      <div class="brand-text">
        <span class="brand-name">GALAC</span>
        <span class="brand-sub">SECURITY &bull; {{ currentPortalSubtitle }}</span>
      </div>
    </div>

    <!-- Menú de Navegación Vertical Dinámico según el Rol -->
    <nav class="sidebar-nav">
      <!-- 1. Dashboard / Panel de Monitoreo (Admin, Analista, Auditor) -->
      <router-link 
        v-if="hasRole(['Administrador', 'Analista', 'Auditor'])" 
        to="/dashboard" 
        class="nav-item" 
        active-class="nav-item-active"
      >
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
        <span>{{ hasRole(['Auditor']) ? 'Panel de Cumplimiento' : (hasRole(['Administrador']) ? 'Panel Global' : 'Dashboard SOC') }}</span>
      </router-link>

      <!-- 2. Gestión de Usuarios (EXCLUSIVO ADMINISTRADOR) -->
      <router-link 
        v-if="hasRole(['Administrador'])" 
        to="/users" 
        class="nav-item" 
        active-class="nav-item-active"
      >
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        <span>Gestión de Usuarios</span>
      </router-link>

      <!-- 3. Incidentes (Adaptado por Rol) -->
      <router-link to="/incidents" class="nav-item" active-class="nav-item-active">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span>{{ incidentMenuTitle }}</span>
      </router-link>

      <!-- 4. Alertas Internas (Administrador y Analista) -->
      <router-link 
        v-if="hasRole(['Administrador', 'Analista'])" 
        to="/alerts" 
        class="nav-item" 
        active-class="nav-item-active"
      >
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <span class="nav-label">{{ hasRole(['Administrador']) ? 'Reglas de Alertas' : 'Alertas en Tiempo Real' }}</span>
        <span v-if="unreadAlertsCount > 0" class="badge-unread">{{ unreadAlertsCount }}</span>
      </router-link>

      <!-- 5. Reportes / Bitácora (Admin, Analista, Auditor) -->
      <router-link 
        v-if="hasRole(['Administrador', 'Analista', 'Auditor'])" 
        to="/reports" 
        class="nav-item" 
        active-class="nav-item-active"
      >
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <span>{{ hasRole(['Auditor']) ? 'Reportes de Cumplimiento' : (hasRole(['Administrador']) ? 'Bitácora del Sistema' : 'Reportes Operativos') }}</span>
      </router-link>

      <!-- 6. Métricas de Calidad CMMI / MoProSoft (Auditor, Analista, Admin) -->
      <router-link 
        v-if="hasRole(['Administrador', 'Analista', 'Auditor'])" 
        to="/quality" 
        class="nav-item" 
        active-class="nav-item-active"
      >
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
        </svg>
        <span>Métricas de Calidad</span>
      </router-link>
    </nav>

    <!-- Pie del Sidebar: Perfil de Usuario & Salir -->
    <div class="sidebar-footer">
      <div class="user-chip">
        <div class="user-details truncate">
          <span class="user-name truncate">{{ user?.nombre || 'Usuario' }}</span>
          <span class="user-role" :class="roleClass">{{ user?.rol || 'Operador' }}</span>
        </div>
        <button @click="handleLogout" class="logout-btn" title="Cerrar sesión">
          <svg class="logout-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
        </button>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '../composables/useAuth';
import { alertsApi } from '../services/alertsApi';

const router = useRouter();
const { user, hasRole, logout } = useAuth();
const unreadAlertsCount = ref(3);

const currentPortalSubtitle = computed(() => {
  const r = (user.value?.rol || '').toLowerCase();
  if (r.includes('admin')) return 'ADMIN';
  if (r.includes('analista')) return 'SOC';
  if (r.includes('auditor')) return 'AUDITOR';
  return 'CLIENTE PyME';
});

const incidentMenuTitle = computed(() => {
  const r = (user.value?.rol || '').toLowerCase();
  if (r.includes('cliente')) return 'Mis Solicitudes / Tickets';
  if (r.includes('analista')) return 'Triage de Incidentes';
  if (r.includes('auditor')) return 'Consulta de Incidentes';
  return 'Supervisión de Incidentes';
});

onMounted(async () => {
  if (hasRole(['Administrador', 'Analista'])) {
    try {
      const res = await alertsApi.getAlerts({ leida: 0 });
      if (res?.ok) {
        unreadAlertsCount.value = res.data.unread_count || 0;
      }
    } catch {
      // Ignorar fallback
    }
  }
});

const roleClass = computed(() => {
  const r = (user.value?.rol || '').toLowerCase();
  if (r.includes('admin')) return 'role-admin';
  if (r.includes('analista')) return 'role-analista';
  if (r.includes('auditor')) return 'role-auditor';
  return 'role-cliente';
});

async function handleLogout() {
  await logout();
  router.push({ name: 'Login' });
}
</script>

<style scoped>
.sidebar-wrapper {
  width: 220px;
  background-color: #0f172a;
  border-right: 1px solid rgba(148, 163, 184, 0.12);
  display: flex;
  flex-direction: column;
  height: 100vh;
  position: sticky;
  top: 0;
  flex-shrink: 0;
  z-index: 30;
}

.sidebar-brand {
  padding: 1rem;
  display: flex;
  align-items: center;
  gap: 0.65rem;
  border-bottom: 1px solid rgba(148, 163, 184, 0.1);
}

.brand-shield-icon {
  width: 32px;
  height: 32px;
  max-width: 32px;
  max-height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.brand-svg {
  width: 30px !important;
  height: 30px !important;
  max-width: 30px !important;
  max-height: 30px !important;
}

.brand-text {
  display: flex;
  flex-direction: column;
  line-height: 1.1;
}

.brand-name {
  font-size: 0.95rem;
  font-weight: 800;
  color: #ffffff;
  letter-spacing: 0.05em;
}

.brand-sub {
  font-size: 0.65rem;
  font-weight: 700;
  color: #38bdf8;
  letter-spacing: 0.1em;
}

.sidebar-nav {
  flex: 1;
  padding: 0.75rem 0.65rem;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  overflow-y: auto;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.55rem 0.75rem;
  border-radius: 8px;
  color: #94a3b8;
  font-size: 0.825rem;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.15s ease;
}

.nav-item:hover {
  background: rgba(59, 130, 246, 0.1);
  color: #38bdf8;
}

.nav-item-active {
  background: #1e293b;
  color: #ffffff;
  font-weight: 600;
  border-left: 3px solid #2563eb;
}

.nav-icon {
  width: 16px !important;
  height: 16px !important;
  max-width: 16px !important;
  max-height: 16px !important;
  color: #38bdf8;
}

.nav-label {
  flex: 1;
}

.badge-unread {
  background: #ef4444;
  color: #ffffff;
  font-size: 0.65rem;
  font-weight: 700;
  padding: 0.1rem 0.45rem;
  border-radius: 9999px;
  margin-left: auto;
}

.sidebar-footer {
  padding: 0.75rem;
  border-top: 1px solid rgba(148, 163, 184, 0.1);
  background: #090f1d;
}

.user-chip {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.5rem;
}

.user-details {
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
  min-width: 0;
}

.user-name {
  font-size: 0.775rem;
  font-weight: 600;
  color: #ffffff;
}

.user-role {
  font-size: 0.65rem;
  font-weight: 700;
  padding: 0.1rem 0.4rem;
  border-radius: 4px;
  display: inline-block;
  width: fit-content;
  text-transform: uppercase;
}

.role-admin { background: rgba(168, 85, 247, 0.2); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.4); }
.role-analista { background: rgba(56, 189, 248, 0.2); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.4); }
.role-auditor { background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); }
.role-cliente { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); }

.logout-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: #94a3b8;
  padding: 0.35rem;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s ease;
}

.logout-btn:hover {
  color: #f87171;
  background: rgba(239, 68, 68, 0.15);
}

.logout-icon {
  width: 16px !important;
  height: 16px !important;
  max-width: 16px !important;
  max-height: 16px !important;
}
</style>
