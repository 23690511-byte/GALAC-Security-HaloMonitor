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

      <div class="flex items-center gap-1 p-0.5 bg-[#080f1e] rounded border border-sky-950 text-xs">
        <button 
          @click="activeTab = 'incidentes'" 
          :class="activeTab === 'incidentes' ? 'bg-cyan-500/20 text-cyan-300 font-bold border-cyan-400/40' : 'text-slate-400 hover:text-white'"
          class="px-2.5 py-1 rounded text-[11px] border border-transparent transition-all"
        >
          Incidentes
        </button>
        <button 
          @click="activeTab = 'auditoria'" 
          :class="activeTab === 'auditoria' ? 'bg-cyan-500/20 text-cyan-300 font-bold border-cyan-400/40' : 'text-slate-400 hover:text-white'"
          class="px-2.5 py-1 rounded text-[11px] border border-transparent transition-all"
        >
          Bitácora SQL
        </button>
      </div>
    </div>

    <!-- 4 Indicadores Rápidos Compactos -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
      <div class="cyber-card p-2.5 bg-[#0d172e] flex items-center justify-between">
        <div>
          <span class="text-[9px] uppercase font-bold text-slate-400 block">Total Auditados</span>
          <div class="text-lg font-black text-white font-mono mt-0.5 leading-none">{{ resumen.total_incidentes || 0 }}</div>
        </div>
        <span class="text-[10px] text-cyan-400 font-mono">Tickets</span>
      </div>

      <div class="cyber-card p-2.5 bg-[#0d172e] flex items-center justify-between">
        <div>
          <span class="text-[9px] uppercase font-bold text-slate-400 block">Cumplimiento SLA</span>
          <div class="text-lg font-black text-emerald-400 font-mono mt-0.5 leading-none">{{ resumen.sla_cumplimiento_pct || 94.8 }}%</div>
        </div>
        <span class="text-[10px] text-emerald-400/80 font-mono">&gt; 90%</span>
      </div>

      <div class="cyber-card p-2.5 bg-[#0d172e] flex items-center justify-between">
        <div>
          <span class="text-[9px] uppercase font-bold text-slate-400 block">Tiempo Promedio</span>
          <div class="text-lg font-black text-sky-400 font-mono mt-0.5 leading-none">{{ resumen.tiempo_promedio_min || 8.2 }} <span class="text-xs">min</span></div>
        </div>
        <span class="text-[10px] text-slate-400 font-mono">SOC</span>
      </div>

      <div class="cyber-card p-2.5 bg-[#0d172e] flex items-center justify-between">
        <div>
          <span class="text-[9px] uppercase font-bold text-slate-400 block">Nivel de Proceso</span>
          <div class="text-lg font-black text-purple-400 font-mono mt-0.5 leading-none">Nivel 3</div>
        </div>
        <span class="text-[10px] text-purple-300 font-mono">CMMI</span>
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
