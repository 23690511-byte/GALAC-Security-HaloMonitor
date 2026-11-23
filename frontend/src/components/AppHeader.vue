<template>
  <header class="border-b border-cyan-500/20 bg-slate-950/80 backdrop-blur-md sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <!-- Logo & Brand (GALAC Security - HaloMonitor) -->
        <div class="flex items-center gap-3">
          <router-link to="/dashboard" class="flex items-center gap-2.5 group">
            <!-- Icono Escudo Cibernético con Candado y Trazas (Mockup) -->
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-500/20 to-blue-600/30 border border-cyan-400/50 flex items-center justify-center p-1.5 shadow-[0_0_15px_rgba(0,242,254,0.3)] group-hover:shadow-[0_0_20px_rgba(0,242,254,0.6)] transition-all">
              <svg class="w-full h-full text-cyan-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                <rect x="9" y="11" width="6" height="5" rx="1" fill="currentColor" fill-opacity="0.3" stroke="currentColor" stroke-width="1.5"/>
                <path d="M10 11V9a2 2 0 0 1 4 0v2" stroke="currentColor" stroke-width="1.5"/>
              </svg>
            </div>
            <div>
              <span class="text-xs uppercase tracking-widest text-cyan-400 font-bold block leading-none">GALAC Security</span>
              <span class="text-base font-extrabold text-white tracking-wide leading-tight">Halo<span class="text-cyan-400">Monitor</span></span>
            </div>
          </router-link>
        </div>

        <!-- Enlaces de Navegación con Renderizado Condicional por Rol -->
        <nav v-if="isAuthenticated" class="hidden md:flex items-center space-x-1 lg:space-x-2">
          <!-- Dashboard: Todos los roles (Admin, Analista, Auditor) -->
          <router-link 
            to="/dashboard" 
            class="nav-link"
            active-class="nav-link-active"
          >
            Dashboard
          </router-link>

          <!-- Gestión de Incidentes: Todos los roles -->
          <router-link 
            to="/incidents" 
            class="nav-link"
            active-class="nav-link-active"
          >
            Gestión de Incidentes
          </router-link>

          <!-- Alertas Internas: Exclusivo Administrador y Analista -->
          <router-link 
            v-if="hasRole(['Administrador', 'Analista'])"
            to="/alerts" 
            class="nav-link relative"
            active-class="nav-link-active"
          >
            Alertas Internas
            <span v-if="unreadAlertsCount > 0" class="ml-1.5 px-1.5 py-0.2 bg-rose-500/90 text-white text-[10px] font-bold rounded-full">
              {{ unreadAlertsCount }}
            </span>
          </router-link>

          <!-- Reportes e Historial: Todos los roles -->
          <router-link 
            to="/reports" 
            class="nav-link"
            active-class="nav-link-active"
          >
            Reportes e Historial
          </router-link>

          <!-- Calidad CMMI / MoProSoft (PDF 3) -->
          <router-link 
            to="/quality" 
            class="nav-link"
            active-class="nav-link-active"
          >
            Métricas de Calidad
          </router-link>
        </nav>

        <!-- Perfil de Usuario y Acciones -->
        <div v-if="isAuthenticated" class="flex items-center gap-3">
          <!-- Tarjeta de Usuario con Rol RBAC -->
          <div class="hidden sm:flex flex-col text-right">
            <span class="text-xs font-semibold text-white leading-tight">{{ user?.nombre }}</span>
            <div class="flex items-center justify-end gap-1.5 mt-0.5">
              <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider" :class="roleBadgeClass">
                {{ user?.rol }}
              </span>
            </div>
          </div>

          <!-- Botón de Cierre de Sesión -->
          <button 
            @click="handleLogout" 
            class="btn-cyber-secondary text-xs py-1.5 px-2.5 text-slate-300 hover:text-white"
            title="Cerrar sesión segura"
          >
            <svg class="w-4 h-4 text-slate-400 group-hover:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            <span class="hidden sm:inline">Salir</span>
          </button>
        </div>

        <div v-else>
          <router-link to="/login" class="btn-cyber-primary text-xs py-1.5 px-4">
            Iniciar Sesión
          </router-link>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '../composables/useAuth';
import { alertsApi } from '../services/alertsApi';

const router = useRouter();
const { user, isAuthenticated, hasRole, logout } = useAuth();
const unreadAlertsCount = ref(3);

onMounted(async () => {
  if (isAuthenticated.value && hasRole(['Administrador', 'Analista'])) {
    try {
      const res = await alertsApi.getAlerts({ leida: 0 });
      if (res?.ok) {
        unreadAlertsCount.value = res.data.unread_count || 0;
      }
    } catch {
      // Usar valor por defecto
    }
  }
});

const roleBadgeClass = computed(() => {
  const r = (user.value?.rol || '').toLowerCase();
  if (r.includes('admin')) return 'bg-purple-500/20 text-purple-300 border border-purple-500/30';
  if (r.includes('analista')) return 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30';
  if (r.includes('auditor')) return 'bg-amber-500/20 text-amber-300 border border-amber-500/30';
  return 'bg-slate-700 text-slate-300';
});

async function handleLogout() {
  await logout();
  router.push({ name: 'Login' });
}
</script>

<style scoped>
.nav-link {
  color: #94a3b8;
  font-size: 0.825rem;
  font-weight: 500;
  padding: 0.5rem 0.85rem;
  border-radius: 6px;
  transition: all 0.2s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
}

.nav-link:hover {
  color: #00f2fe;
  background: rgba(0, 242, 254, 0.08);
}

.nav-link-active {
  color: #00f2fe;
  background: rgba(0, 242, 254, 0.14);
  font-weight: 600;
  border-bottom: 2px solid #00f2fe;
}
</style>
