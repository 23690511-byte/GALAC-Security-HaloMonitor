<template>
  <div class="space-y-3.5 max-w-full">
    <!-- 1. Fila de las 4 Tarjetas de KPI (Mockup Oficial Figura 2.2) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
      <!-- KPI 1: Alertas Activas (247) -->
      <div class="cyber-card p-3 bg-[#0d172e] border-cyan-500/30 flex items-center justify-between">
        <div>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Alertas Activas</span>
          <div class="text-2xl font-black text-white font-mono mt-0.5 leading-none">
            {{ kpis.alertas_activas }}
          </div>
        </div>
        <div class="p-2 rounded-lg bg-cyan-500/10 text-cyan-400">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
          </svg>
        </div>
      </div>

      <!-- KPI 2: Incidentes Crítico (10) -->
      <div class="cyber-card p-3 bg-[#0d172e] border-rose-500/30 flex items-center justify-between">
        <div>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Incidentes Crítico</span>
          <div class="text-2xl font-black text-rose-400 font-mono mt-0.5 leading-none">
            {{ kpis.incidentes_criticos }}
          </div>
        </div>
        <div class="p-2 rounded-lg bg-rose-500/10 text-rose-400">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
        </div>
      </div>

      <!-- KPI 3: Tiempo Resp. (8.2 min) -->
      <div class="cyber-card p-3 bg-[#0d172e] border-sky-500/30 flex items-center justify-between">
        <div>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tiempo Resp.</span>
          <div class="text-2xl font-black text-sky-400 font-mono mt-0.5 leading-none">
            {{ kpis.tiempo_respuesta_min }} <span class="text-xs text-slate-400 font-normal">min</span>
          </div>
        </div>
        <div class="p-2 rounded-lg bg-sky-500/10 text-sky-400">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
      </div>

      <!-- KPI 4: Sistemas (1,284) -->
      <div class="cyber-card p-3 bg-[#0d172e] border-emerald-500/30 flex items-center justify-between">
        <div>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Sistemas</span>
          <div class="text-2xl font-black text-emerald-400 font-mono mt-0.5 leading-none">
            {{ kpis.sistemas_monitoreados.toLocaleString() }}
          </div>
        </div>
        <div class="p-2 rounded-lg bg-emerald-500/10 text-emerald-400">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
          </svg>
        </div>
      </div>
    </div>

    <!-- 2. Área Central (Gráfica de Tendencia 24h + Panel Lateral de Últimos Incidentes) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
      <!-- Tarjeta Izquierda: Tendencia de Alertas (Últimas 24h) -->
      <div class="lg:col-span-2 cyber-card p-4 bg-[#0d172e]">
        <div class="flex items-center justify-between mb-2">
          <div class="flex items-center gap-2">
            <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
            </svg>
            <h2 class="text-xs font-bold text-white uppercase tracking-wider">Tendencia de Alertas (Últimas 24h)</h2>
          </div>
          <span class="text-[10px] text-cyan-400 font-mono bg-cyan-950/60 px-2 py-0.5 rounded border border-cyan-800/40">
            Pico máx: 78 eventos
          </span>
        </div>

        <!-- Gráfica SVG Compacta y Fiel al Boceto -->
        <div class="w-full h-36 bg-[#080f1e] rounded border border-sky-950/80 p-2 flex flex-col justify-between relative">
          <!-- Guías Horizontales Discretas -->
          <div class="absolute inset-x-2 top-4 border-b border-slate-800/40 text-[9px] text-slate-600 font-mono">80</div>
          <div class="absolute inset-x-2 top-14 border-b border-slate-800/40 text-[9px] text-slate-600 font-mono">40</div>

          <svg class="w-full h-24 overflow-visible" viewBox="0 0 700 110" preserveAspectRatio="none">
            <defs>
              <linearGradient id="cyanLineGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                <stop offset="0%" stop-color="#00f2fe" stop-opacity="0.3" />
                <stop offset="100%" stop-color="#00f2fe" stop-opacity="0.0" />
              </linearGradient>
            </defs>
            <polygon 
              points="0,110 0,90 100,98 200,75 300,16 400,48 500,82 600,24 700,56 700,110" 
              fill="url(#cyanLineGrad)" 
            />
            <polyline 
              points="0,90 100,98 200,75 300,16 400,48 500,82 600,24 700,56" 
              fill="none" 
              stroke="#00f2fe" 
              stroke-width="2.5" 
              stroke-linecap="round" 
              stroke-linejoin="round"
              class="drop-shadow-[0_0_6px_rgba(0,242,254,0.6)]"
            />
            <circle cx="300" cy="16" r="4" fill="#ffffff" stroke="#00f2fe" stroke-width="2" />
            <circle cx="600" cy="24" r="3.5" fill="#00f2fe" />
          </svg>

          <!-- Eje Horario -->
          <div class="flex justify-between text-[10px] text-slate-400 font-mono pt-1 border-t border-slate-800">
            <span v-for="pt in tendencia" :key="pt.hora">{{ pt.hora }}</span>
          </div>
        </div>
      </div>

      <!-- Tarjeta Derecha: Últimos Incidentes (Mockup Oficial Figura 2.2) -->
      <div class="cyber-card p-4 bg-[#0d172e] flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between mb-2 pb-1.5 border-b border-sky-950/80">
            <h2 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Últimos Incidentes
            </h2>
            <router-link to="/incidents" class="text-[11px] text-cyan-400 hover:underline">
              Ver todos &rarr;
            </router-link>
          </div>

          <!-- Items Concisos (Exactos al Boceto) -->
          <div class="space-y-1.5">
            <div 
              v-for="inc in ultimosIncidentes" 
              :key="inc.id"
              class="p-2 rounded bg-[#080f1e] border border-sky-950/80 hover:border-cyan-500/40 transition-colors flex items-center justify-between gap-2 cursor-pointer"
              @click="$router.push('/incidents')"
            >
              <div class="truncate">
                <div class="flex items-center gap-1.5">
                  <span class="text-[10px] font-mono text-cyan-300 font-semibold shrink-0">{{ inc.hora_formato || '10:42 AM' }}</span>
                  <span class="text-xs font-medium text-white truncate">{{ inc.titulo }}</span>
                </div>
              </div>
              <span :class="['severity-badge text-[9px] shrink-0', `severity-${inc.gravedad.toLowerCase()}`]">
                [{{ inc.gravedad }}]
              </span>
            </div>
          </div>
        </div>

        <div class="pt-2 mt-2 border-t border-sky-950/80 flex justify-end">
          <router-link to="/incidents" class="btn-cyber-primary text-xs py-1 px-3">
            Gestionar Incidentes &rarr;
          </router-link>
        </div>
      </div>
    </div>

    <!-- 3. Fila Inferior: Triage Rápido de Incidentes Abiertos (Compacto) -->
    <div class="cyber-card p-3.5 bg-[#0d172e]">
      <div class="flex items-center justify-between mb-2">
        <h3 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
          Atención Inmediata de Incidentes Prioritarios
        </h3>
        <span class="text-[10px] text-slate-400 font-mono">Consola SOC PyME</span>
      </div>

      <!-- Tabla Ultracompacta -->
      <div class="overflow-x-auto rounded border border-sky-950/80">
        <table class="w-full text-left text-xs text-slate-200">
          <thead class="bg-[#080f1e] text-[10px] uppercase text-slate-400 border-b border-sky-950">
            <tr>
              <th class="py-1.5 px-3">Folio</th>
              <th class="py-1.5 px-3">Título</th>
              <th class="py-1.5 px-3 text-center">Severidad</th>
              <th class="py-1.5 px-3 text-center">Estado</th>
              <th class="py-1.5 px-3 text-right">Acción</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-sky-950/60 bg-[#0b1428]">
            <tr v-for="inc in ultimosIncidentes" :key="inc.id" class="hover:bg-[#101c38]">
              <td class="py-1.5 px-3 font-mono text-cyan-400 font-semibold">{{ inc.folio }}</td>
              <td class="py-1.5 px-3 text-slate-200 truncate max-w-xs">{{ inc.titulo }}</td>
              <td class="py-1.5 px-3 text-center">
                <span :class="['severity-badge text-[9px]', `severity-${inc.gravedad.toLowerCase()}`]">
                  {{ inc.gravedad }}
                </span>
              </td>
              <td class="py-1.5 px-3 text-center">
                <RequestStatusBadge :status="inc.estado" />
              </td>
              <td class="py-1.5 px-3 text-right">
                <router-link to="/incidents" class="btn-cyber-secondary py-0.5 px-2 text-[10px]">
                  Ver
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { dashboardApi } from '../services/dashboardApi';
import RequestStatusBadge from '../components/RequestStatusBadge.vue';

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
  }
}

onMounted(() => {
  loadData();
});
</script>
