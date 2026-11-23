<template>
  <header class="h-12 bg-[#0a1426] border-b border-sky-950/80 px-4 flex items-center justify-between shrink-0 select-none z-20">
    <!-- Título de Sección y Breadcrumb -->
    <div class="flex items-center gap-2">
      <span class="w-1.5 h-3.5 bg-cyan-400 rounded-sm inline-block"></span>
      <span class="text-xs font-bold text-white uppercase tracking-wider">{{ currentTitle }}</span>
    </div>

    <!-- Indicadores de Estado y Reloj SOC -->
    <div class="flex items-center gap-3">
      <!-- Estado de Red / Salud -->
      <div class="flex items-center gap-1.5 px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-[11px] font-mono">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
        <span>SOC Activo (Sistemas Online)</span>
      </div>

      <!-- Zona Horaria Centralizada -->
      <div class="hidden sm:flex items-center gap-1 text-[11px] text-slate-400 font-mono">
        <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>{{ currentTime }} &bull; CDMX</span>
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
    'Dashboard': 'Panel Principal de Monitoreo',
    'Incidents': 'Gestión Integral de Incidentes',
    'Alertas': 'Motor de Alertas Internas',
    'Alerts': 'Motor de Alertas Internas',
    'Reports': 'Reportes Ejecutivos & Auditoría',
    'Quality': 'Estándares de Calidad CMMI / MoProSoft'
  };
  return map[route.name] || 'Consola HaloMonitor';
});

function updateClock() {
  const now = new Date();
  currentTime.value = now.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}

onMounted(() => {
  updateClock();
  timer = setInterval(updateClock, 1000);
});

onUnmounted(() => {
  if (timer) clearInterval(timer);
});
</script>
