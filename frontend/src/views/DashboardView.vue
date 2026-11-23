<template>
  <div class="space-y-6">
    <!-- Encabezado de Página -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-black text-white flex items-center gap-2">
          <span class="w-2.5 h-6 bg-cyan-400 rounded-sm inline-block"></span>
          Panel Principal de Monitoreo (Dashboard)
        </h1>
        <p class="text-xs text-slate-400 mt-1 font-mono">
          Consola Centralizada SOC &bull; Monitoreo en Tiempo Real de Activos PyME
        </p>
      </div>

      <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-mono">
          <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
          Sistemas en Línea
        </span>
        <button @click="loadData" class="btn-cyber-secondary text-xs py-1.5 px-3" title="Actualizar métricas">
          <svg class="w-3.5 h-3.5 text-cyan-400" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Actualizar
        </button>
      </div>
    </div>

    <!-- 1) Barra Superior con Cuatro Tarjetas de KPI (Mockup Oficial Figura 2.2) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- KPI 1: Alertas Activas (247) -->
      <div class="cyber-card p-5 border-cyan-500/30 relative overflow-hidden group">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-cyan-500/10 rounded-full blur-xl group-hover:bg-cyan-500/20 transition-all"></div>
        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Alertas Activas</span>
          <span class="p-2 rounded-lg bg-cyan-500/15 text-cyan-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
          </span>
        </div>
        <div class="mt-4">
          <div class="text-3xl font-black text-white font-mono tracking-tight">
            {{ kpis.alertas_activas }}
          </div>
          <span class="text-[11px] text-cyan-400/80 font-mono flex items-center gap-1 mt-1">
            <span>&uarr; 12% vs. periodo previo</span>
          </span>
        </div>
      </div>

      <!-- KPI 2: Incidentes Crítico (10) -->
      <div class="cyber-card p-5 border-rose-500/30 relative overflow-hidden group">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-rose-500/10 rounded-full blur-xl group-hover:bg-rose-500/20 transition-all"></div>
        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Incidentes Crítico</span>
          <span class="p-2 rounded-lg bg-rose-500/15 text-rose-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
          </span>
        </div>
        <div class="mt-4">
          <div class="text-3xl font-black text-rose-400 font-mono tracking-tight">
            {{ kpis.incidentes_criticos }}
          </div>
          <span class="text-[11px] text-rose-300/80 font-mono flex items-center gap-1 mt-1">
            <span>Atención Inmediata Prioritaria</span>
          </span>
        </div>
      </div>

      <!-- KPI 3: Tiempo Resp. (8.2 min) -->
      <div class="cyber-card p-5 border-blue-500/30 relative overflow-hidden group">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-blue-500/10 rounded-full blur-xl group-hover:bg-blue-500/20 transition-all"></div>
        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Tiempo Resp.</span>
          <span class="p-2 rounded-lg bg-blue-500/15 text-blue-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </span>
        </div>
        <div class="mt-4">
          <div class="text-3xl font-black text-blue-400 font-mono tracking-tight">
            {{ kpis.tiempo_respuesta_min }} <span class="text-lg text-slate-400">min</span>
          </div>
          <span class="text-[11px] text-emerald-400 font-mono flex items-center gap-1 mt-1">
            <span>&bull; Cumplimiento SLA 94.8%</span>
          </span>
        </div>
      </div>

      <!-- KPI 4: Sistemas (1,284) -->
      <div class="cyber-card p-5 border-emerald-500/30 relative overflow-hidden group">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl group-hover:bg-emerald-500/20 transition-all"></div>
        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Sistemas</span>
          <span class="p-2 rounded-lg bg-emerald-500/15 text-emerald-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
            </svg>
          </span>
        </div>
        <div class="mt-4">
          <div class="text-3xl font-black text-emerald-400 font-mono tracking-tight">
            {{ kpis.sistemas_monitoreados.toLocaleString() }}
          </div>
          <span class="text-[11px] text-slate-400 font-mono flex items-center gap-1 mt-1">
            <span>Infraestructura PyME Segura</span>
          </span>
        </div>
      </div>
    </div>

    <!-- 2) Gráfica Central de Tendencias de Alertas & 3) Panel Lateral de Últimos Incidentes -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Gráfica Central: Tendencia de Alertas (Últimas 24h) -->
      <div class="lg:col-span-2 cyber-card p-6 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h2 class="text-base font-bold text-white flex items-center gap-2">
              <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
              </svg>
              Tendencia de Alertas (Últimas 24h)
            </h2>
            <p class="text-xs text-slate-400">Variación horaria de eventos detectados por sensores de intrusión</p>
          </div>
          <span class="text-xs text-cyan-400 bg-cyan-950/60 px-2.5 py-1 rounded border border-cyan-800/50 font-mono">
            Pico máx: 78 eventos
          </span>
        </div>

        <!-- Renderizado de Gráfica SVG Reactiva con estilo Cian Neon idéntico al Boceto -->
        <div class="relative w-full h-64 bg-slate-950/60 rounded-xl p-4 border border-slate-800/80 flex flex-col justify-between">
          <!-- Guías Horizontales -->
          <div class="absolute inset-x-4 top-8 border-b border-slate-800/60 text-[10px] text-slate-600 font-mono">80</div>
          <div class="absolute inset-x-4 top-24 border-b border-slate-800/60 text-[10px] text-slate-600 font-mono">50</div>
          <div class="absolute inset-x-4 top-40 border-b border-slate-800/60 text-[10px] text-slate-600 font-mono">20</div>

          <!-- Polilínea SVG -->
          <svg class="w-full h-44 overflow-visible" viewBox="0 0 700 160" preserveAspectRatio="none">
            <defs>
              <linearGradient id="cyanLineGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                <stop offset="0%" stop-color="#00f2fe" stop-opacity="0.35" />
                <stop offset="100%" stop-color="#00f2fe" stop-opacity="0.0" />
              </linearGradient>
            </defs>
            <!-- Área bajo la curva -->
            <polygon 
              points="0,160 0,136 100,144 200,112 300,24 400,70 500,122 600,36 700,84 700,160" 
              fill="url(#cyanLineGrad)" 
            />
            <!-- Línea de tendencia (Mockup exacto) -->
            <polyline 
              points="0,136 100,144 200,112 300,24 400,70 500,122 600,36 700,84" 
              fill="none" 
              stroke="#00f2fe" 
              stroke-width="3.5" 
              stroke-linecap="round" 
              stroke-linejoin="round"
              class="drop-shadow-[0_0_8px_rgba(0,242,254,0.7)]"
            />
            <!-- Puntos de datos interactivos -->
            <circle cx="300" cy="24" r="5" fill="#ffffff" stroke="#00f2fe" stroke-width="3" />
            <circle cx="500" cy="122" r="4" fill="#00f2fe" />
            <circle cx="600" cy="36" r="4" fill="#00f2fe" />
          </svg>

          <!-- Eje X (Horas) -->
          <div class="flex justify-between text-[11px] text-slate-400 font-mono pt-2 border-t border-slate-800">
            <span v-for="pt in tendencia" :key="pt.hora">{{ pt.hora }}</span>
          </div>
        </div>
      </div>

      <!-- Panel Lateral Derecho: Últimos Incidentes (Mockup Oficial Figura 2.2) -->
      <div class="cyber-card p-6 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-800">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
              <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Últimos Incidentes
            </h2>
            <router-link to="/incidents" class="text-xs text-cyan-400 hover:underline">
              Ver todos &rarr;
            </router-link>
          </div>

          <!-- Lista de Incidentes Rápidos -->
          <div class="space-y-3">
            <div 
              v-for="inc in ultimosIncidentes" 
              :key="inc.id"
              class="p-3 rounded-lg bg-slate-900/70 border border-slate-800/80 hover:border-cyan-500/30 transition-all cursor-pointer"
              @click="$router.push('/incidents')"
            >
              <div class="flex items-start justify-between gap-2">
                <span class="text-xs font-mono text-cyan-300 font-semibold">{{ inc.hora_formato || '10:42 AM' }}</span>
                <span :class="['severity-badge text-[10px]', `severity-${inc.gravedad.toLowerCase()}`]">
                  [{{ inc.gravedad }}]
                </span>
              </div>
              <p class="text-sm font-medium text-white mt-1 leading-snug">
                {{ inc.titulo }}
              </p>
              <div class="flex items-center justify-between mt-2 pt-1.5 border-t border-slate-800/50 text-[11px] text-slate-400">
                <span class="font-mono text-slate-500">{{ inc.folio }}</span>
                <RequestStatusBadge :status="inc.estado" />
              </div>
            </div>
          </div>
        </div>

        <div class="mt-4 pt-3 border-t border-slate-800/80">
          <router-link to="/incidents" class="w-full btn-cyber-primary py-2 text-xs text-center justify-center">
            Gestionar Ciclo de Vida de Incidentes
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { dashboardApi } from '../services/dashboardApi';
import RequestStatusBadge from '../components/RequestStatusBadge.vue';

const loading = ref(false);

const kpis = ref({
  alertas_activas: 247,
  incidentes_criticos: 10,
  tiempo_respuesta_min: 8.2,
  sistemas_monitoreados: 1284
});

const ultimosIncidentes = ref([
  { id: 1, folio: 'INC-2026-001', titulo: 'Malware Detectado en Endpoint Contabilidad', gravedad: 'Critical', estado: 'Open', hora_formato: '10:42 AM' },
  { id: 2, folio: 'INC-2026-002', titulo: 'Intento Login Fallido Recurrente (Brute Force)', gravedad: 'Medium', estado: 'InProgress', hora_formato: '10:35 AM' },
  { id: 3, folio: 'INC-2026-003', titulo: 'Puerto Inseguro Escaneado (Puerto 22 y 3389)', gravedad: 'Low', estado: 'Resolved', hora_formato: '10:18 AM' }
]);

const tendencia = ref([
  { hora: '00:00' }, { hora: '03:00' }, { hora: '06:00' }, { hora: '09:00' },
  { hora: '12:00' }, { hora: '15:00' }, { hora: '18:00' }, { hora: '21:00' }
]);

async function loadData() {
  loading.value = true;
  try {
    const res = await dashboardApi.getStats();
    if (res?.ok && res?.data) {
      if (res.data.kpis) kpis.value = res.data.kpis;
      if (res.data.ultimos_incidentes?.length) {
        ultimosIncidentes.value = res.data.ultimos_incidentes;
      }
    }
  } catch (e) {
    console.warn('Dashboard stats fallback:', e);
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  loadData();
});
</script>
