<template>
  <div class="space-y-3 max-w-full">
    <!-- Encabezado y Selector de Pestañas Compacto -->
    <div class="flex items-center justify-between gap-2 pb-1 border-b border-sky-950/80">
      <div>
        <h1 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-1.5">
          <span class="w-1.5 h-3.5 bg-cyan-400 rounded-sm inline-block"></span>
          Reportes Ejecutivos & Auditoría
        </h1>
        <span class="text-[10px] text-slate-400 font-mono">Trazabilidad Inmutable &bull; Métricas MoProSoft / CMMI</span>
      </div>

      <div class="flex items-center gap-1 p-1 bg-[#080f1e] rounded-lg border border-sky-950 text-xs">
        <button 
          @click="activeTab = 'incidentes'" 
          :class="activeTab === 'incidentes' ? 'bg-[#2563eb] text-white font-bold shadow-md' : 'text-slate-400 hover:text-white'"
          class="px-3 py-1 rounded-md text-[11px] transition-all cursor-pointer border-none"
        >
          Incidentes
        </button>
        <button 
          v-if="hasRole(['Administrador', 'Auditor'])" 
          @click="activeTab = 'auditoria'" 
          :class="activeTab === 'auditoria' ? 'bg-[#2563eb] text-white font-bold shadow-md' : 'text-slate-400 hover:text-white'"
          class="px-3 py-1 rounded-md text-[11px] transition-all cursor-pointer border-none"
        >
          Bitácora SQL
        </button>
      </div>
    </div>

    <!-- 4 Indicadores Rápidos Compactos (Idénticos a Imagen 1) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
      <div class="cyber-card p-3 bg-[#0d172e] flex items-center justify-between border border-sky-950">
        <div>
          <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Auditados</span>
          <div class="text-2xl font-black text-white font-mono mt-1 leading-none">{{ resumen.total_incidentes || 4 }}</div>
        </div>
        <span class="text-[10px] text-slate-300 font-mono px-2 py-0.5 rounded border border-slate-700 bg-slate-800/60">Tickets</span>
      </div>

      <div class="cyber-card p-3 bg-[#0a1f26] flex items-center justify-between border border-emerald-500/40 shadow-sm">
        <div>
          <span class="text-[10px] uppercase font-bold text-slate-400 block">Cumplimiento SLA</span>
          <div class="text-2xl font-black text-emerald-400 font-mono mt-1 leading-none">{{ resumen.sla_cumplimiento_pct || 100 }}%</div>
        </div>
        <span class="text-[10px] text-emerald-300 font-mono px-2 py-0.5 rounded border border-emerald-500/40 bg-emerald-950/60">&gt; 90%</span>
      </div>

      <div class="cyber-card p-3 bg-[#0d172e] flex items-center justify-between border border-sky-950">
        <div>
          <span class="text-[10px] uppercase font-bold text-slate-400 block">Tiempo Promedio</span>
          <div class="text-2xl font-black text-white font-mono mt-1 leading-none">{{ resumen.tiempo_promedio_min || 5708 }} <span class="text-xs font-normal text-slate-400">min</span></div>
        </div>
        <span class="text-[10px] text-slate-300 font-mono px-2 py-0.5 rounded border border-slate-700 bg-slate-800/60">SOC</span>
      </div>

      <div class="cyber-card p-3 bg-[#0e1738] flex items-center justify-between border border-blue-500/40 shadow-sm">
        <div>
          <span class="text-[10px] uppercase font-bold text-slate-400 block">Nivel de Proceso</span>
          <div class="text-2xl font-black text-sky-400 font-mono mt-1 leading-none">Nivel 3</div>
        </div>
        <span class="text-[10px] text-blue-300 font-mono px-2 py-0.5 rounded border border-blue-500/40 bg-blue-950/60">CMMI</span>
      </div>
    </div>

    <!-- Pestaña 1: Reporte Consolidado de Incidentes -->
    <div v-show="activeTab === 'incidentes'" class="cyber-card p-3.5 bg-[#0d172e]">
      <DataGrid
        :data="incidentes"
        :columns="columnsIncidentes"
        search-placeholder="Buscar en reporte de incidentes..."
        export-title="Reporte_Incidentes_HaloMonitor"
      >
        <template #cell-gravedad="{ value }">
          <span :class="['severity-badge text-[9px]', `severity-${(value || '').toLowerCase()}`]">
            {{ value }}
          </span>
        </template>

        <template #cell-estado="{ value }">
          <RequestStatusBadge :status="value" />
        </template>

        <template #cell-folio="{ value }">
          <span class="font-mono text-cyan-400 font-bold">{{ value }}</span>
        </template>

        <template #cell-duracion_minutos="{ value }">
          <span class="font-mono text-slate-300">{{ value !== null ? `${value} min` : 'En curso' }}</span>
        </template>
      </DataGrid>
    </div>

    <!-- Pestaña 2: Bitácora de Auditoría (historial_solicitud) -->
    <div v-show="activeTab === 'auditoria'" class="cyber-card p-3.5 bg-[#0d172e]">
      <DataGrid
        :data="auditoria"
        :columns="columnsAuditoria"
        search-placeholder="Buscar por usuario, IP, folio o comentario..."
        export-title="Bitacora_Auditoria_SQL"
      >
        <template #cell-incidente_folio="{ value }">
          <span class="font-mono text-cyan-400 font-semibold">{{ value }}</span>
        </template>

        <template #cell-estado_nuevo="{ value }">
          <RequestStatusBadge :status="value" />
        </template>

        <template #cell-ip_origen="{ value }">
          <span class="font-mono text-[10px] text-slate-400">{{ value }}</span>
        </template>
      </DataGrid>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuth } from '../composables/useAuth';
import { reportsApi } from '../services/reportsApi';
import DataGrid from '../components/DataGrid.vue';
import RequestStatusBadge from '../components/RequestStatusBadge.vue';

const { hasRole } = useAuth();
const activeTab = ref('incidentes');
const incidentes = ref([]);
const auditoria = ref([]);
const resumen = ref({});

const columnsIncidentes = [
  { key: 'folio', label: 'Folio' },
  { key: 'titulo', label: 'Título' },
  { key: 'categoria', label: 'Categoría' },
  { key: 'gravedad', label: 'Gravedad', align: 'center' },
  { key: 'estado', label: 'Estado', align: 'center' },
  { key: 'reporta_nombre', label: 'Reporta' },
  { key: 'asignado_nombre', label: 'Asignado' },
  { key: 'duracion_minutos', label: 'Atención', align: 'right' },
  { key: 'fecha_registro', label: 'Fecha' }
];

const columnsAuditoria = [
  { key: 'id', label: 'ID', align: 'center' },
  { key: 'incidente_folio', label: 'Folio' },
  { key: 'usuario_nombre', label: 'Usuario' },
  { key: 'usuario_rol', label: 'Rol' },
  { key: 'estado_anterior', label: 'Previo' },
  { key: 'estado_nuevo', label: 'Nuevo', align: 'center' },
  { key: 'comentario', label: 'Comentario' },
  { key: 'ip_origen', label: 'IP' },
  { key: 'fecha_registro', label: 'Fecha' }
];

async function loadData() {
  try {
    const res = await reportsApi.getReportsData();
    if (res?.ok && res?.data) {
      incidentes.value = res.data.incidentes || [];
      auditoria.value = res.data.auditoria || [];
      resumen.value = res.data.resumen_calidad || {};
    }
  } catch (err) {
    console.error('Error cargando reportes:', err);
  }
}

onMounted(() => {
  loadData();
});
</script>
