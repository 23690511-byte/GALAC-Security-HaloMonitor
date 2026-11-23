<template>
  <div class="datagrid-container space-y-2.5">
    <!-- Barra Superior Compacta: Búsqueda y Exportaciones en una sola línea -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2">
      <!-- Caja de Búsqueda Compacta -->
      <div class="relative flex-1 max-w-sm">
        <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-slate-400">
          <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </span>
        <input 
          type="text" 
          v-model="searchQuery" 
          :placeholder="searchPlaceholder" 
          class="cyber-input pl-8 text-xs py-1"
        />
        <button 
          v-if="searchQuery" 
          @click="searchQuery = ''" 
          class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-white text-xs"
        >
          &times;
        </button>
      </div>

      <!-- Controles de Exportación y Registros -->
      <div class="flex items-center gap-1.5 flex-wrap justify-end">
        <div class="flex items-center gap-1 text-[11px] text-slate-400">
          <span>Ver:</span>
          <select v-model.number="pageSize" class="cyber-select text-[11px] py-0.5 px-1.5 w-auto">
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
          class="btn-cyber-secondary text-[11px] py-1 px-2.5"
          title="Descargar datos filtrados en Excel (CSV UTF-8)"
        >
          <svg class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 20V4h7v5h5v11H6z"/>
            <path d="M8 12l2.5 3.5L8 19h1.8l1.6-2.5 1.6 2.5h1.8L12.3 15.5 14.8 12H13l-1.6 2.5L9.8 12z"/>
          </svg>
          Excel
        </button>

        <!-- Botón Exportar a PDF -->
        <button 
          type="button" 
          @click="exportToPdf" 
          class="btn-cyber-secondary text-[11px] py-1 px-2.5"
          title="Generar reporte PDF"
        >
          <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
          </svg>
          PDF
        </button>

        <!-- Slot para Acciones Globales (ej. botón Crear) -->
        <slot name="header-actions"></slot>
      </div>
    </div>

    <!-- Tabla Ultracompacta Responsiva -->
    <div class="overflow-x-auto rounded border border-sky-950/80 bg-[#080f1e]">
      <table class="w-full text-left text-xs text-slate-200 divide-y divide-sky-950/80">
        <thead class="bg-[#0b1428] text-[10px] uppercase tracking-wider text-cyan-400 font-semibold select-none">
          <tr>
            <th 
              v-for="col in columns" 
              :key="col.key" 
              class="py-2 px-3"
              :class="col.align === 'center' ? 'text-center' : col.align === 'right' ? 'text-right' : 'text-left'"
            >
              {{ col.label }}
            </th>
            <th v-if="$slots.actions" class="py-2 px-3 text-right">
              Acciones
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-sky-950/60 bg-[#091224] text-[11px]">
          <tr v-if="paginatedData.length === 0">
            <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="text-center py-6 text-slate-400">
              No se encontraron registros que coincidan con la búsqueda.
            </td>
          </tr>

          <tr 
            v-for="(row, rowIdx) in paginatedData" 
            :key="row.id || rowIdx"
            class="hover:bg-[#0f1d38] transition-colors"
          >
            <td 
              v-for="col in columns" 
              :key="col.key"
              class="py-1.5 px-3 whitespace-nowrap"
              :class="col.align === 'center' ? 'text-center' : col.align === 'right' ? 'text-right' : 'text-left'"
            >
              <!-- Slot Dinámico por Columna: #cell-${col.key} -->
              <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
                {{ formatCellValue(row[col.key], col) }}
              </slot>
            </td>

            <td v-if="$slots.actions" class="py-1.5 px-3 text-right whitespace-nowrap">
              <slot name="actions" :row="row"></slot>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Barra Inferior Compacta: Contador y Paginador -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] text-slate-400 pt-1">
      <div>
        Mostrando <span class="text-cyan-400 font-semibold">{{ startIndex + 1 }}</span> a 
        <span class="text-cyan-400 font-semibold">{{ endIndex }}</span> de 
        <span class="text-white font-semibold">{{ filteredData.length }}</span> registros
        <span v-if="filteredData.length !== data.length" class="text-slate-500">
          (filtrados de {{ data.length }})
        </span>
      </div>

      <!-- Controles de Paginación -->
      <div class="flex items-center gap-1">
        <button 
          @click="currentPage = 1" 
          :disabled="currentPage === 1"
          class="btn-cyber-secondary py-0.5 px-2 text-[10px]"
          title="Primera página"
        >
          &laquo;
        </button>
        <button 
          @click="currentPage--" 
          :disabled="currentPage === 1"
          class="btn-cyber-secondary py-0.5 px-2 text-[10px]"
          title="Anterior"
        >
          &lsaquo;
        </button>

        <span class="px-2 py-0.5 bg-[#080f1e] border border-sky-950 rounded text-cyan-300 font-mono text-[10px]">
          {{ currentPage }} / {{ totalPages || 1 }}
        </span>

        <button 
          @click="currentPage++" 
          :disabled="currentPage >= totalPages"
          class="btn-cyber-secondary py-0.5 px-2 text-[10px]"
          title="Siguiente"
        >
          &rsaquo;
        </button>
        <button 
          @click="currentPage = totalPages" 
          :disabled="currentPage >= totalPages"
          class="btn-cyber-secondary py-0.5 px-2 text-[10px]"
          title="Última"
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
    default: () => []
  },
  searchPlaceholder: {
    type: String,
    default: 'Buscar registros...'
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

watch([searchQuery, pageSize], () => {
  currentPage.value = 1;
});

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

function exportToExcel() {
  const dataset = filteredData.value;
  if (!dataset.length) {
    alert('No hay registros filtrados para exportar.');
    return;
  }

  const headers = props.columns.map(c => `"${c.label.replace(/"/g, '""')}"`).join(',');
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

  const tableHeaders = props.columns.map(c => `<th style="border: 1px solid #ddd; padding: 6px; background: #0c1527; color: #00f2fe; text-align: left; font-size: 11px;">${c.label}</th>`).join('');

  const tableRows = dataset.map(row => {
    const cells = props.columns.map(c => {
      let v = row[c.key];
      if (v === null || v === undefined) v = '-';
      if (typeof v === 'object') v = JSON.stringify(v);
      return `<td style="border: 1px solid #ddd; padding: 6px; font-size: 10px;">${String(v)}</td>`;
    }).join('');
    return `<tr>${cells}</tr>`;
  }).join('');

  printWindow.document.write(`
    <!DOCTYPE html>
    <html>
      <head>
        <title>${props.exportTitle} - HaloMonitor</title>
        <style>
          body { font-family: Arial, sans-serif; margin: 20px; color: #111; }
          .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #00f2fe; padding-bottom: 8px; margin-bottom: 12px; }
          .title { font-size: 18px; font-weight: bold; color: #0c1527; }
          table { width: 100%; border-collapse: collapse; }
        </style>
      </head>
      <body>
        <div class="header">
          <div>
            <div class="title">HaloMonitor | GALAC Security</div>
            <div style="font-size: 12px; color: #444;">${props.exportTitle}</div>
          </div>
          <div style="font-size: 11px; color: #666;">
            <div>Fecha: ${new Date().toLocaleString()}</div>
            <div>Registros: ${dataset.length}</div>
          </div>
        </div>
        <table>
          <thead><tr>${tableHeaders}</tr></thead>
          <tbody>${tableRows}</tbody>
        </table>
        <script>window.onload = function() { window.print(); }<\/script>
      </body>
    </html>
  `);
  printWindow.document.close();
}
</script>
