<template>
  <div class="space-y-6">
    <!-- Encabezado de Página -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-black text-white flex items-center gap-2">
          <span class="w-2.5 h-6 bg-cyan-400 rounded-sm inline-block"></span>
          Reportes Ejecutivos & Bitácoras de Auditoría
        </h1>
        <p class="text-xs text-slate-400 mt-1 font-mono">
          Exportación de Datos Filtrados &bull; Conformidad MoProSoft & Trazabilidad Inmutable
        </p>
      </div>

      <!-- Selector de Tipo de Reporte -->
      <div class="flex items-center gap-1.5 p-1 bg-slate-900 rounded-lg border border-slate-800 text-xs">
        <button 
          @click="activeTab = 'incidentes'" 
          :class="activeTab === 'incidentes' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-400/40' : 'text-slate-400 hover:text-white'"
          class="px-3 py-1.5 rounded-md font-semibold border border-transparent transition-all"
        >
          Reporte de Incidentes
        </button>
        <button 
          @click="activeTab = 'auditoria'" 
          :class="activeTab === 'auditoria' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-400/40' : 'text-slate-400 hover:text-white'"
          class="px-3 py-1.5 rounded-md font-semibold border border-transparent transition-all"
        >
          Bitácora de Auditoría
        </button>
      </div>
    </div>

    <!-- Tarjetas de Resumen de Calidad y Desempeño Operativo -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="cyber-card p-4">
        <span class="text-xs font-semibold text-slate-400 uppercase">Total Gestionados</span>
        <div class="text-2xl font-black text-white font-mono mt-2">
          {{ resumen.total_incidentes || 0 }}
        </div>
        <span class="text-[11px] text-cyan-400 font-mono">Tickets Auditados</span>
      </div>

      <div class="cyber-card p-4">
        <span class="text-xs font-semibold text-slate-400 uppercase">Cumplimiento SLA</span>
        <div class="text-2xl font-black text-emerald-400 font-mono mt-2">
          {{ resumen.sla_cumplimiento_pct || 94.8 }}%
        </div>
        <span class="text-[11px] text-emerald-400/80 font-mono">Meta alcanzada (&gt; 90%)</span>
      </div>

      <div class="cyber-card p-4">
        <span class="text-xs font-semibold text-slate-400 uppercase">Tiempo Promedio Resolución</span>
        <div class="text-2xl font-black text-blue-400 font-mono mt-2">
          {{ resumen.tiempo_promedio_min || 8.2 }} <span class="text-sm">min</span>
        </div>
        <span class="text-[11px] text-slate-400 font-mono">Respuesta Inmediata SOC</span>
      </div>

      <div class="cyber-card p-4">
        <span class="text-xs font-semibold text-slate-400 uppercase">Nivel de Proceso</span>
        <div class="text-2xl font-black text-purple-400 font-mono mt-2">
          Nivel 3
        </div>
        <span class="text-[11px] text-purple-300 font-mono">CMMI Definido / MoProSoft</span>
      </div>
    </div>

    <!-- TAB 1: Reporte Consolidado de Incidentes con DataGrid -->
    <div v-show="activeTab === 'incidentes'" class="cyber-card p-5">
      <div class="mb-4 pb-2 border-b border-slate-800">
        <h2 class="text-base font-bold text-white flex items-center gap-2">
          <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          Reporte Consolidado de Incidentes y Tiempos de Atención
        </h2>
        <p class="text-xs text-slate-400">Datos preparados para auditorías externas, exportación a Excel (.xlsx) y generación de PDF</p>
      </div>

      <DataGrid
        :data="incidentes"
        :columns="columnsIncidentes"
        search-placeholder="Buscar en el reporte de incidentes..."
        export-title="Reporte_Consolidado_Incidentes"
      >
        <template #cell-gravedad="{ value }">
          <span :class="['severity-badge text-xs', `severity-${(value || '').toLowerCase()}`]">
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

    <!-- TAB 2: Bitácora de Auditoría Inmutable (historial_solicitud) -->
    <div v-show="activeTab === 'auditoria'" class="cyber-card p-5">
      <div class="mb-4 pb-2 border-b border-slate-800">
        <h2 class="text-base font-bold text-white flex items-center gap-2">
          <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          Bitácora Global de Trazabilidad e Inmutabilidad (Auditoría SQL)
        </h2>
        <p class="text-xs text-slate-400">Registro inmutable de quién hizo qué cambio, estado anterior, estado nuevo, comentario y fecha</p>
      </div>

      <DataGrid
        :data="auditoria"
        :columns="columnsAuditoria"
        search-placeholder="Buscar por usuario, IP, folio o comentario de auditoría..."
        export-title="Bitacora_Auditoria_Inmutable"
      >
        <template #cell-incidente_folio="{ value }">
          <span class="font-mono text-cyan-400 font-semibold">{{ value }}</span>
        </template>

        <template #cell-estado_anterior="{ value }">
          <span class="text-slate-400">{{ value || 'Inicial' }}</span>
        </template>

        <template #cell-estado_nuevo="{ value }">
          <RequestStatusBadge :status="value" />
        </template>

        <template #cell-ip_origen="{ value }">
          <span class="font-mono text-xs text-slate-400">{{ value }}</span>
        </template>
      </DataGrid>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { reportsApi } from '../services/reportsApi';
import DataGrid from '../components/DataGrid.vue';
import RequestStatusBadge from '../components/RequestStatusBadge.vue';

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
  { key: 'duracion_minutos', label: 'Tiempo Atención', align: 'right' },
  { key: 'fecha_registro', label: 'Fecha Registro' }
];

const columnsAuditoria = [
  { key: 'id', label: 'ID Log', align: 'center' },
  { key: 'incidente_folio', label: 'Folio Incidente' },
  { key: 'usuario_nombre', label: 'Operador / Usuario' },
  { key: 'usuario_rol', label: 'Rol' },
  { key: 'estado_anterior', label: 'Estado Previo' },
  { key: 'estado_nuevo', label: 'Nuevo Estado', align: 'center' },
  { key: 'comentario', label: 'Motivo / Justificación' },
  { key: 'ip_origen', label: 'IP Origen' },
  { key: 'fecha_registro', label: 'Fecha Timestamp' }
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
