<template>
  <div class="datagrid-container space-y-4">
    <!-- Barra Superior: Búsqueda en tiempo real y Exportaciones -->
    <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
      <!-- Búsqueda en Tiempo Real -->
      <div class="relative flex-1 max-w-md">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
          <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </span>
        <input 
          type="text" 
          v-model="searchQuery" 
          :placeholder="searchPlaceholder" 
          class="cyber-input pl-9 text-sm"
        />
        <button 
          v-if="searchQuery" 
          @click="searchQuery = ''" 
          class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-white"
        >
          &times;
        </button>
      </div>

      <!-- Acciones de Exportación y Filtros Rápidos -->
      <div class="flex items-center gap-2 flex-wrap">
        <!-- Selector de Tamaño de Página -->
        <div class="flex items-center gap-1.5 text-xs text-slate-400">
          <span>Mostrar:</span>
          <select v-model.number="pageSize" class="cyber-select text-xs py-1 px-2 w-auto">
            <option :value="5">5</option>
            <option :value="10">10</option>
            <option :value="25">25</option>
            <option :value="50">50</option>
          </select>
        </div>

        <!-- Botón Exportar a Excel -->
        <button 
          type="button" 
          @click="exportToExcel" 
          class="btn-cyber-secondary text-xs py-1.5 px-3"
          title="Descargar datos filtrados en formato Excel / CSV"
        >
          <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 20V4h7v5h5v11H6z"/>
            <path d="M8 12l2.5 3.5L8 19h1.8l1.6-2.5 1.6 2.5h1.8L12.3 15.5 14.8 12H13l-1.6 2.5L9.8 12z"/>
          </svg>
          Exportar Excel
        </button>

        <!-- Botón Exportar a PDF -->
        <button 
          type="button" 
          @click="exportToPdf" 
          class="btn-cyber-secondary text-xs py-1.5 px-3"
          title="Generar e imprimir reporte PDF"
        >
          <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
          </svg>
          Exportar PDF
        </button>

        <!-- Slot Extra para Botones de Acción Global (ej. 'Crear') -->
        <slot name="header-actions"></slot>
      </div>
    </div>

    <!-- Tabla Responsiva -->
    <div class="overflow-x-auto rounded-xl border border-cyan-500/20 bg-slate-900/40">
      <table class="w-full text-left text-sm text-slate-200 divide-y divide-slate-800">
        <thead class="bg-slate-950/80 text-xs uppercase tracking-wider text-cyan-400 font-semibold">
          <tr>
            <th 
              v-for="col in columns" 
              :key="col.key" 
              class="px-4 py-3.5 select-none"
              :class="col.align === 'center' ? 'text-center' : col.align === 'right' ? 'text-right' : 'text-left'"
            >
              {{ col.label }}
            </th>
            <th v-if="$slots.actions" class="px-4 py-3.5 text-right">
              Acciones
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/60 font-normal">
          <tr v-if="paginatedData.length === 0">
            <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="text-center py-8 text-slate-400 text-sm">
              <div class="flex flex-col items-center justify-center gap-2">
                <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>No se encontraron registros que coincidan con la búsqueda.</span>
              </div>
            </td>
          </tr>

          <tr 
            v-for="(row, rowIdx) in paginatedData" 
            :key="row.id || rowIdx"
            class="hover:bg-slate-800/40 transition-colors"
          >
            <td 
              v-for="col in columns" 
              :key="col.key"
              class="px-4 py-3 whitespace-nowrap"
              :class="col.align === 'center' ? 'text-center' : col.align === 'right' ? 'text-right' : 'text-left'"
            >
              <!-- Slot dinámico por columna: #cell-${column.key} -->
              <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
                {{ formatCellValue(row[col.key], col) }}
              </slot>
            </td>

            <!-- Slot para Acciones por Registro -->
            <td v-if="$slots.actions" class="px-4 py-3 text-right whitespace-nowrap">
              <slot name="actions" :row="row"></slot>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Barra Inferior: Contador de Registros y Paginación -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-400 pt-2">
      <div>
        Mostrando <span class="text-cyan-400 font-semibold">{{ startIndex + 1 }}</span> a 
        <span class="text-cyan-400 font-semibold">{{ endIndex }}</span> de 
        <span class="text-white font-semibold">{{ filteredData.length }}</span> registros
        <span v-if="filteredData.length !== data.length" class="text-slate-500">
          (filtrados de {{ data.length }} totales)
        </span>
      </div>

      <!-- Controles de Paginación -->
      <div class="flex items-center gap-1.5">
        <button 
          @click="currentPage = 1" 
          :disabled="currentPage === 1"
          class="btn-cyber-secondary py-1 px-2 text-xs"
          title="Primera página"
        >
          &laquo;
        </button>
        <button 
          @click="currentPage--" 
          :disabled="currentPage === 1"
          class="btn-cyber-secondary py-1 px-2 text-xs"
          title="Página anterior"
        >
          &lsaquo; Anterior
        </button>

        <span class="px-3 py-1 bg-slate-900 border border-slate-700 rounded text-cyan-300 font-mono">
          {{ currentPage }} / {{ totalPages || 1 }}
        </span>

        <button 
          @click="currentPage++" 
          :disabled="currentPage >= totalPages"
          class="btn-cyber-secondary py-1 px-2 text-xs"
          title="Página siguiente"
        >
          Siguiente &rsaquo;
        </button>
        <button 
          @click="currentPage = totalPages" 
          :disabled="currentPage >= totalPages"
          class="btn-cyber-secondary py-1 px-2 text-xs"
          title="Última página"
        >
          &raquo;
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  data: {
    type: Array,
    required: true,
    default: () => []
  },
  columns: {
    type: Array,
    required: true,
    default: () => [] // [{ key: 'id', label: 'ID', align: 'left', format: fn }]
  },
  searchPlaceholder: {
    type: String,
    default: 'Buscar por cualquier campo...'
  },
  defaultPageSize: {
    type: Number,
    default: 10
  },
  exportTitle: {
    type: String,
    default: 'Reporte_HaloMonitor'
  }
});

const searchQuery = ref('');
const currentPage = ref(1);
const pageSize = ref(props.defaultPageSize);

// Reset de página al cambiar búsqueda
watch([searchQuery, pageSize], () => {
  currentPage.value = 1;
});

// Filtrado Reactivo en Tiempo Real
const filteredData = computed(() => {
  if (!searchQuery.value.trim()) {
    return props.data;
  }
  const q = searchQuery.value.toLowerCase().trim();
  return props.data.filter(row => {
    return props.columns.some(col => {
      const val = row[col.key];
      if (val === null || val === undefined) return false;
      return String(val).toLowerCase().includes(q);
    });
  });
});

// Paginación
const totalPages = computed(() => Math.ceil(filteredData.value.length / pageSize.value));

const startIndex = computed(() => {
  if (filteredData.value.length === 0) return 0;
  return (currentPage.value - 1) * pageSize.value;
});

const endIndex = computed(() => {
  return Math.min(startIndex.value + pageSize.value, filteredData.value.length);
});

const paginatedData = computed(() => {
  return filteredData.value.slice(startIndex.value, endIndex.value);
});

function formatCellValue(val, col) {
  if (col.format && typeof col.format === 'function') {
    return col.format(val);
  }
  if (val === null || val === undefined) return '-';
  return val;
}

// Exportación a Excel (CSV con formato compatible de Excel y UTF-8 BOM)
function exportToExcel() {
  const dataset = filteredData.value;
  if (!dataset.length) {
    alert('No hay registros filtrados para exportar.');
    return;
  }

  // Encabezados
  const headers = props.columns.map(c => `"${c.label.replace(/"/g, '""')}"`).join(',');

  // Filas
  const rows = dataset.map(row => {
    return props.columns.map(c => {
      let v = row[c.key];
      if (v === null || v === undefined) v = '';
      if (typeof v === 'object') v = JSON.stringify(v);
      return `"${String(v).replace(/"/g, '""')}"`;
    }).join(',');
  });

  const csvContent = '\uFEFF' + [headers, ...rows].join('\r\n');
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `${props.exportTitle}_${new Date().toISOString().slice(0, 10)}.csv`;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}

// Exportación a PDF (Ventana imprimible formateada profesionalmente)
function exportToPdf() {
  const dataset = filteredData.value;
  if (!dataset.length) {
    alert('No hay registros filtrados para exportar.');
    return;
  }

  const printWindow = window.open('', '_blank');
  if (!printWindow) {
    alert('Por favor permita ventanas emergentes para generar el PDF.');
    return;
  }

  const tableHeaders = props.columns.map(c => `<th style="border: 1px solid #ddd; padding: 8px; background: #0c1527; color: #00f2fe; text-align: left;">${c.label}</th>`).join('');

  const tableRows = dataset.map(row => {
    const cells = props.columns.map(c => {
      let v = row[c.key];
      if (v === null || v === undefined) v = '-';
      if (typeof v === 'object') v = JSON.stringify(v);
      return `<td style="border: 1px solid #ddd; padding: 8px;">${String(v)}</td>`;
    }).join('');
    return `<tr>${cells}</tr>`;
  }).join('');

  printWindow.document.write(`
    <!DOCTYPE html>
    <html>
      <head>
        <title>${props.exportTitle} - HaloMonitor</title>
        <style>
          body { font-family: Arial, sans-serif; margin: 30px; color: #111; }
          .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #00f2fe; padding-bottom: 12px; margin-bottom: 20px; }
          .title { font-size: 20px; font-weight: bold; color: #0c1527; }
          .meta { font-size: 12px; color: #666; }
          table { width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 15px; }
          @media print {
            button { display: none; }
          }
        </style>
      </head>
      <body>
        <div class="header">
          <div>
            <div class="title">HaloMonitor | GALAC Security</div>
            <div style="font-size: 13px; color: #333;">${props.exportTitle}</div>
          </div>
          <div class="meta">
            <div>Fecha de emisión: ${new Date().toLocaleString()}</div>
            <div>Registros incluidos: ${dataset.length}</div>
          </div>
        </div>
        <table>
          <thead><tr>${tableHeaders}</tr></thead>
          <tbody>${tableRows}</tbody>
        </table>
        <script>
          window.onload = function() { window.print(); }
        <\/script>
      </body>
    </html>
  `);
  printWindow.document.close();
}
</script>
