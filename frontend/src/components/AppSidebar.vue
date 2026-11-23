<template>
  <aside class="w-60 bg-[#091224] border-r border-sky-950/80 flex flex-col shrink-0 h-screen sticky top-0 select-none z-30">
    <!-- Brand / Logotipo Oficial GALAC Security (Mockup Figura 2.2) -->
    <div class="p-4 border-b border-sky-950/80 flex items-center gap-3">
      <!-- Escudo Cibernético Compacto -->
      <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-cyan-500/20 to-blue-600/30 border border-cyan-400/50 flex items-center justify-center p-1.5 shadow-[0_0_12px_rgba(0,242,254,0.35)] shrink-0">
        <svg class="w-full h-full text-cyan-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
          <rect x="9" y="11" width="6" height="5" rx="1" fill="currentColor" fill-opacity="0.3" stroke="currentColor" stroke-width="1.5"/>
          <path d="M10 11V9a2 2 0 0 1 4 0v2" stroke="currentColor" stroke-width="1.5"/>
        </svg>
      </div>

      <div class="leading-none">
        <div class="text-[10px] tracking-[0.25em] text-cyan-400 font-extrabold uppercase">GALAC</div>
        <div class="text-sm font-black text-white tracking-wide mt-0.5">SECURITY</div>
      </div>
    </div>

    <!-- Menú de Navegación Vertical (Mockup Oficial Figura 2.2) -->
    <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
      <!-- 1. Dashboard -->
      <router-link 
        to="/dashboard" 
        class="sidebar-item"
        active-class="sidebar-item-active"
      >
        <svg class="w-4 h-4 shrink-0 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
        <span>Dashboard</span>
      </router-link>

      <!-- 2. Gestión de Incidentes -->
      <router-link 
        to="/incidents" 
        class="sidebar-item"
        active-class="sidebar-item-active"
      >
        <svg class="w-4 h-4 shrink-0 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span>Gestión de Incidentes</span>
      </router-link>

      <!-- 3. Alertas Internas (Solo Admin y Analista) -->
      <router-link 
        v-if="hasRole(['Administrador', 'Analista'])"
        to="/alerts" 
        class="sidebar-item"
        active-class="sidebar-item-active"
      >
        <svg class="w-4 h-4 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <span class="flex-1">Alertas Internas</span>
        <span v-if="unreadAlertsCount > 0" class="ml-auto px-1.5 py-0.2 bg-rose-500/90 text-white text-[10px] font-bold rounded-full">
          {{ unreadAlertsCount }}
        </span>
      </router-link>

      <!-- 4. Reportes e Historial -->
      <router-link 
        to="/reports" 
        class="sidebar-item"
        active-class="sidebar-item-active"
      >
        <svg class="w-4 h-4 shrink-0 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <span>Reportes e Historial</span>
      </router-link>

      <!-- 5. Métricas de Calidad (PDF 3) -->
      <router-link 
        to="/quality" 
        class="sidebar-item"
        active-class="sidebar-item-active"
      >
        <svg class="w-4 h-4 shrink-0 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
        </svg>
        <span>Métricas de Calidad</span>
      </router-link>
    </nav>

    <!-- Pie de Sidebar: Perfil y Logout -->
    <div class="p-3 border-t border-sky-950/80 bg-[#070e1c]">
      <div class="flex items-center justify-between gap-2">
        <div class="truncate">
          <div class="text-xs font-semibold text-white truncate leading-tight">{{ user?.nombre || 'Usuario' }}</div>
          <div class="flex items-center gap-1 mt-0.5">
            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded uppercase" :class="roleBadgeClass">
              {{ user?.rol || 'Operador' }}
            </span>
          </div>
        </div>

        <button 
          @click="handleLogout" 
          class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded transition-colors"
          title="Cerrar sesión"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

onMounted(async () => {
  if (hasRole(['Administrador', 'Analista'])) {
    try {
      const res = await alertsApi.getAlerts({ leida: 0 });
      if (res?.ok) {
        unreadAlertsCount.value = res.data.unread_count || 0;
      }
    } catch {
      // Valor por defecto
    }
  }
});

const roleBadgeClass = computed(() => {
  const r = (user.value?.rol || '').toLowerCase();
  if (r.includes('admin')) return 'bg-purple-500/20 text-purple-300 border border-purple-500/40';
  if (r.includes('analista')) return 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/40';
  if (r.includes('auditor')) return 'bg-amber-500/20 text-amber-300 border border-amber-500/40';
  return 'bg-slate-800 text-slate-300';
});

async function handleLogout() {
  await logout();
  router.push({ name: 'Login' });
}
</script>

<style scoped>
.sidebar-item {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.55rem 0.75rem;
  font-size: 0.815rem;
  font-weight: 500;
  color: #94a3b8;
  border-radius: 6px;
  text-decoration: none;
  transition: all 0.15s ease;
}

.sidebar-item:hover {
  color: #00f2fe;
  background: rgba(0, 242, 254, 0.08);
}

.sidebar-item-active {
  color: #00f2fe;
  background: rgba(0, 242, 254, 0.15);
  font-weight: 600;
  border-left: 3px solid #00f2fe;
}
</style>
