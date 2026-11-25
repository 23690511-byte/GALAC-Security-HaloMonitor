<template>
  <header class="topbar-wrapper select-none">
    <!-- Breadcrumb / Título -->
    <div class="topbar-left">
      <span class="indicator-bar"></span>
      <span class="topbar-title">{{ currentTitle }}</span>
    </div>

    <!-- Indicador de Red y Hora -->
    <div class="topbar-right">
      <div class="status-indicator">
        <span class="status-dot"></span>
        <span class="status-text">SOC ONLINE &bull; MONITOREO ACTIVO</span>
      </div>

      <div class="clock-display">
        <svg class="clock-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <circle cx="12" cy="12" r="10" stroke-width="1.8"/>
          <path d="M12 6v6l4 2" stroke-width="1.8"/>
        </svg>
        <span>{{ currentTime }}</span>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const currentTime = ref('');
let timer = null;

const currentTitle = computed(() => {
  const map = {
    'Dashboard': 'Dashboard & Monitoreo Transaccional',
    'Users': 'Gestión de Usuarios y Roles (RBAC)',
    'Incidents': 'Gestión Integral de Incidentes',
    'Alerts': 'Motor de Alertas Internas',
    'Reports': 'Reportes Ejecutivos & Auditoría',
    'Quality': 'Métricas CMMI & MoProSoft'
  };
  return map[route.name] || 'Consola SOC';
});

function updateClock() {
  const now = new Date();
  currentTime.value = now.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' (CDMX)';
}

onMounted(() => {
  updateClock();
  timer = setInterval(updateClock, 1000);
});

onUnmounted(() => {
  if (timer) clearInterval(timer);
});
</script>

<style scoped>
.topbar-wrapper {
  height: 44px;
  background-color: #0f172a;
  border-bottom: 1px solid rgba(148, 163, 184, 0.12);
  padding: 0 1.25rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-shrink: 0;
  z-index: 20;
}

.topbar-left {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.indicator-bar {
  width: 4px;
  height: 14px;
  border-radius: 2px;
  background: #2563eb;
}

.topbar-title {
  font-size: 0.8rem;
  font-weight: 700;
  color: #f8fafc;
  letter-spacing: 0.02em;
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.status-indicator {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.25rem 0.65rem;
  border-radius: 9999px;
  background: rgba(16, 185, 129, 0.12);
  border: 1px solid rgba(16, 185, 129, 0.3);
}

.status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #10b981;
}

.status-text {
  font-size: 0.65rem;
  font-weight: 700;
  color: #34d399;
  font-family: var(--font-mono);
}

.clock-display {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.725rem;
  color: #94a3b8;
  font-family: var(--font-mono);
}

.clock-icon {
  width: 14px !important;
  height: 14px !important;
  max-width: 14px !important;
  max-height: 14px !important;
  color: #38bdf8;
}
</style>
