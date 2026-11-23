<template>
  <div class="space-y-3 max-w-full">
    <!-- Barra Superior de Acciones: Título + Botón de Nuevo Incidente -->
    <div class="flex items-center justify-between gap-2 pb-1 border-b border-sky-950/80">
      <div>
        <h1 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-1.5">
          <span class="w-1.5 h-3.5 bg-cyan-400 rounded-sm inline-block"></span>
          Gestión Integral de Incidentes
        </h1>
        <span class="text-[10px] text-slate-400 font-mono">Control de Concurrencia (SELECT ... FOR UPDATE) & Transiciones ACID</span>
      </div>

      <div v-if="hasRole(['Administrador', 'Analista'])">
        <button @click="openCreateModal" class="btn-cyber-primary text-xs py-1 px-3">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Nuevo Incidente
        </button>
      </div>
    </div>

    <!-- Banner de Feedback Compacto -->
    <div v-if="bannerMessage" :class="bannerType === 'error' ? 'bg-rose-500/15 border-rose-500/30 text-rose-300' : 'bg-emerald-500/15 border-emerald-500/30 text-emerald-300'" class="p-2 rounded border text-xs flex items-center justify-between">
      <div class="flex items-center gap-1.5">
        <span class="font-bold">{{ bannerType === 'error' ? 'Error:' : 'Éxito:' }}</span>
        <span>{{ bannerMessage }}</span>
      </div>
      <button @click="bannerMessage = ''" class="hover:text-white">&times;</button>
    </div>

    <!-- DataGrid de Incidentes -->
    <div class="cyber-card p-3.5 bg-[#0d172e]">
      <DataGrid
        :data="incidents"
        :columns="columns"
        search-placeholder="Buscar incidente por folio, título o activo..."
        export-title="Incidentes_HaloMonitor"
      >
        <template #cell-gravedad="{ value }">
          <span :class="['severity-badge text-[10px]', `severity-${(value || '').toLowerCase()}`]">
            {{ value }}
          </span>
        </template>

        <template #cell-estado="{ value }">
          <RequestStatusBadge :status="value" />
        </template>

        <template #cell-folio="{ value }">
          <span class="font-mono text-cyan-400 font-bold">{{ value }}</span>
        </template>

        <template #cell-precio_contratado="{ value }">
          <span class="font-mono text-slate-300">
            ${{ Number(value || 0).toLocaleString('es-MX', { minimumFractionDigits: 2 }) }}
          </span>
        </template>

        <template #actions="{ row }">
          <div class="flex items-center justify-end gap-1">
            <button 
              @click="viewDetail(row)"
              class="btn-cyber-secondary py-0.5 px-2 text-[10px]"
              title="Ver bitácora de auditoría"
            >
              Historial
            </button>

            <button 
              v-if="hasRole(['Administrador', 'Analista'])"
              @click="openActionModal(row)"
              class="btn-cyber-primary py-0.5 px-2 text-[10px]"
              title="Cambiar estado o reasignar"
            >
              Gestionar
            </button>
          </div>
        </template>
      </DataGrid>
    </div>

    <!-- MODAL 1: Registrar Nuevo Incidente (Compacto y Optimizado) -->
    <div v-if="showCreateModal" class="modal-overlay" @click.self="showCreateModal = false">
      <div class="modal-content max-w-lg p-0">
        <div class="flex items-center justify-between p-3 border-b border-sky-950/80 bg-[#080f1e] rounded-t-lg">
          <h3 class="text-xs font-bold text-cyan-400 uppercase tracking-wider flex items-center gap-1.5">
            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Apertura de Incidente de Seguridad
          </h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <form @submit.prevent="submitCreateIncident" class="p-4 space-y-3 text-xs">
          <div>
            <label class="block font-semibold text-slate-300 uppercase mb-0.5 text-[11px]">Título del Incidente:</label>
            <input 
              type="text" 
              v-model="createForm.titulo" 
              placeholder="Ej. Malware Detectado en Endpoint" 
              required
              class="cyber-input text-xs py-1"
            />
          </div>

          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block font-semibold text-slate-300 uppercase mb-0.5 text-[11px]">Categoría:</label>
              <select v-model="createForm.categoria" class="cyber-select text-xs py-1">
                <option value="Malware">Malware / Ransomware</option>
                <option value="Intrusión">Intento de Intrusión</option>
                <option value="Escaneo">Escaneo de Puertos</option>
                <option value="Fuga de Datos">Fuga de Datos</option>
                <option value="Disponibilidad">Disponibilidad</option>
              </select>
            </div>

            <div>
              <label class="block font-semibold text-slate-300 uppercase mb-0.5 text-[11px]">Severidad:</label>
              <select v-model="createForm.gravedad" class="cyber-select text-xs py-1">
                <option value="Low">Low (Bajo)</option>
                <option value="Medium">Medium (Medio)</option>
                <option value="High">High (Alto)</option>
                <option value="Critical">Critical (Crítico)</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block font-semibold text-slate-300 uppercase mb-0.5 text-[11px]">Activo PyME:</label>
              <select v-model="createForm.sistema_id" class="cyber-select text-xs py-1">
                <option :value="null">-- Seleccione --</option>
                <option v-for="s in catalogs.sistemas" :key="s.id" :value="s.id">
                  {{ s.nombre }}
                </option>
              </select>
            </div>

            <div>
              <label class="block font-semibold text-slate-300 uppercase mb-0.5 text-[11px]">Asignar Analista:</label>
              <select v-model="createForm.usuario_asignado_id" class="cyber-select text-xs py-1">
                <option :value="null">-- Automático --</option>
                <option v-for="u in catalogs.usuarios" :key="u.id" :value="u.id">
                  {{ u.nombre }}
                </option>
              </select>
            </div>
          </div>

          <div>
            <label class="block font-semibold text-cyan-400 uppercase mb-0.5 text-[11px]">Fecha Límite SLA (Futura):</label>
            <input 
              type="datetime-local" 
              v-model="createForm.fecha_limite_atencion" 
              :min="minFutureDate"
              class="cyber-input text-xs py-1 font-mono"
            />
          </div>

          <div>
            <label class="block font-semibold text-slate-300 uppercase mb-0.5 text-[11px]">Descripción del Evento:</label>
            <textarea 
              v-model="createForm.descripcion" 
              rows="2" 
              placeholder="Detalle el vector de ataque detectado..." 
              required
              class="cyber-textarea text-xs py-1"
            ></textarea>
          </div>

          <div class="p-2 bg-cyan-950/30 border border-cyan-800/40 rounded text-[10px] text-slate-400">
            &bull; Transacción ACID: Se congelará el snapshot de integridad y se creará alerta inmediata si la severidad es High o Critical.
          </div>

          <div class="flex items-center justify-end gap-2 pt-2 border-t border-sky-950/80">
            <button type="button" @click="showCreateModal = false" class="btn-cyber-secondary text-xs">Cancelar</button>
            <button type="submit" :disabled="isSubmitting" class="btn-cyber-primary text-xs">
              <span v-if="isSubmitting" class="animate-spin mr-1">&#9696;</span>
              Guardar Ticket
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL 2: Detalle e Historial de Auditoría (Compacto) -->
    <div v-if="showDetailModal" class="modal-overlay" @click.self="showDetailModal = false">
      <div class="modal-content max-w-xl p-0">
        <div class="flex items-center justify-between p-3 border-b border-sky-950/80 bg-[#080f1e] rounded-t-lg">
          <div>
            <h3 class="text-xs font-bold text-white flex items-center gap-1.5">
              <span class="font-mono text-cyan-400">{{ selectedIncident?.folio }}</span>
              <span>- {{ selectedIncident?.titulo }}</span>
            </h3>
            <span class="text-[10px] text-slate-400 font-mono">
              Reportado por: {{ selectedIncident?.reporta_nombre }} &bull; {{ selectedIncident?.fecha_registro }}
            </span>
          </div>
          <button @click="showDetailModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <div class="p-4 space-y-3 text-xs max-h-[70vh] overflow-y-auto">
          <!-- Metadatos Clave en Barra Compacta -->
          <div class="grid grid-cols-4 gap-2 p-2 bg-[#080f1e] rounded border border-sky-950 text-[11px]">
            <div>
              <span class="text-slate-500 block text-[10px]">Gravedad:</span>
              <span :class="['severity-badge text-[9px] mt-0.5', `severity-${(selectedIncident?.gravedad || '').toLowerCase()}`]">
                {{ selectedIncident?.gravedad }}
              </span>
            </div>
            <div>
              <span class="text-slate-500 block text-[10px]">Estado:</span>
              <div class="mt-0.5">
                <RequestStatusBadge :status="selectedIncident?.estado || ''" />
              </div>
            </div>
            <div>
              <span class="text-slate-500 block text-[10px]">Activo:</span>
              <span class="text-white truncate block mt-0.5 font-medium">{{ selectedIncident?.sistema_nombre || 'N/A' }}</span>
            </div>
            <div>
              <span class="text-slate-500 block text-[10px]">Asignado:</span>
              <span class="text-cyan-300 truncate block mt-0.5 font-medium">{{ selectedIncident?.asignado_nombre || 'Sin asignar' }}</span>
            </div>
          </div>

          <div>
            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Descripción Técnica:</span>
            <p class="text-xs text-slate-300 bg-[#080f1e] p-2 rounded border border-sky-950/80 leading-relaxed">
              {{ selectedIncident?.descripcion }}
            </p>
          </div>

          <!-- Línea de Tiempo de Auditoría (historial_solicitud) -->
          <div>
            <span class="text-[10px] uppercase font-bold text-cyan-400 block mb-2">
              Línea de Tiempo y Trazabilidad Inmutable:
            </span>
            <Timeline :items="incidentHistory" />
          </div>
        </div>

        <div class="flex justify-end p-2.5 border-t border-sky-950 bg-[#080f1e] rounded-b-lg">
          <button @click="showDetailModal = false" class="btn-cyber-secondary text-xs py-0.5 px-3">Cerrar</button>
        </div>
      </div>
    </div>

    <!-- MODAL 3: Cambiar Estado / Acción Configurable -->
    <RequestActionModal
      :is-open="showActionModal"
      title="Gestión de Ciclo de Vida del Incidente"
      :description="`Actualización de estado con SELECT ... FOR UPDATE en el ticket ${actionTarget?.folio}.`"
      confirm-text="Confirmar Cambio"
      :require-reason="true"
      :status-options="validStateTransitions"
      :analyst-options="catalogs.usuarios"
      :initial-status="actionTarget?.estado"
      :initial-assigned="actionTarget?.usuario_asignado_id"
      :is-submitting="isSubmitting"
      @confirm="submitUpdateIncident"
      @cancel="showActionModal = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuth } from '../composables/useAuth';
import { incidentsApi } from '../services/incidentsApi';
import DataGrid from '../components/DataGrid.vue';
import RequestStatusBadge from '../components/RequestStatusBadge.vue';
import RequestActionModal from '../components/RequestActionModal.vue';
import Timeline from '../components/Timeline.vue';

const { hasRole } = useAuth();

const incidents = ref([]);
const catalogs = ref({ usuarios: [], sistemas: [] });
const isSubmitting = ref(false);
const bannerMessage = ref('');
const bannerType = ref('success');

const columns = [
  { key: 'folio', label: 'Folio' },
  { key: 'titulo', label: 'Título' },
  { key: 'categoria', label: 'Categoría' },
  { key: 'gravedad', label: 'Gravedad', align: 'center' },
  { key: 'estado', label: 'Estado', align: 'center' },
  { key: 'sistema_nombre', label: 'Activo' },
  { key: 'asignado_nombre', label: 'Asignado' },
  { key: 'precio_contratado', label: 'Costo Congelado', align: 'right' },
  { key: 'fecha_registro', label: 'Fecha' }
];

const showCreateModal = ref(false);
const showDetailModal = ref(false);
const showActionModal = ref(false);

const selectedIncident = ref(null);
const incidentHistory = ref([]);
const actionTarget = ref(null);

const createForm = ref({
  titulo: '',
  descripcion: '',
  categoria: 'Malware',
  gravedad: 'Medium',
  sistema_id: null,
  usuario_asignado_id: null,
  fecha_limite_atencion: '',
  costo_estimado: 3500.00
});

const minFutureDate = computed(() => {
  const now = new Date();
  now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
  return now.toISOString().slice(0, 16);
});

const validStateTransitions = computed(() => {
  const current = actionTarget.value?.estado || 'Open';
  if (current === 'Open') {
    return [
      { value: 'InProgress', label: 'Iniciar Atención (En Proceso)' },
      { value: 'Closed', label: 'Cerrar Directamente' }
    ];
  }
  if (current === 'InProgress') {
    return [
      { value: 'Resolved', label: 'Marcar como Resuelto' },
      { value: 'Open', label: 'Revertir a Abierto' },
      { value: 'Closed', label: 'Cerrar' }
    ];
  }
  if (current === 'Resolved') {
    return [
      { value: 'Closed', label: 'Cierre Definitivo' },
      { value: 'InProgress', label: 'Reabrir a En Proceso' }
    ];
  }
  return [
    { value: 'Open', label: 'Reabrir Incidente' }
  ];
});

async function loadData() {
  try {
    const [incRes, catRes] = await Promise.all([
      incidentsApi.getIncidents(),
      incidentsApi.getCatalogs()
    ]);
    if (incRes?.ok) {
      incidents.value = incRes.data.incidentes || [];
    }
    if (catRes?.ok) {
      catalogs.value = catRes.data || { usuarios: [], sistemas: [] };
    }
  } catch (err) {
    showBanner(err.message, 'error');
  }
}

function showBanner(msg, type = 'success') {
  bannerMessage.value = msg;
  bannerType.value = type;
  setTimeout(() => { bannerMessage.value = ''; }, 5000);
}

function openCreateModal() {
  const defaultFuture = new Date();
  defaultFuture.setHours(defaultFuture.getHours() + 6);
  defaultFuture.setMinutes(defaultFuture.getMinutes() - defaultFuture.getTimezoneOffset());

  createForm.value = {
    titulo: '',
    descripcion: '',
    categoria: 'Malware',
    gravedad: 'Medium',
    sistema_id: catalogs.value.sistemas[0]?.id || null,
    usuario_asignado_id: catalogs.value.usuarios[0]?.id || null,
    fecha_limite_atencion: defaultFuture.toISOString().slice(0, 16),
    costo_estimado: 3500.00
  };
  showCreateModal.value = true;
}

async function submitCreateIncident() {
  isSubmitting.value = true;
  try {
    const res = await incidentsApi.createIncident(createForm.value);
    if (res?.ok) {
      showBanner(res.message, 'success');
      showCreateModal.value = false;
      await loadData();
    }
  } catch (err) {
    showBanner(err.message, 'error');
  } finally {
    isSubmitting.value = false;
  }
}

async function viewDetail(row) {
  try {
    const res = await incidentsApi.getIncidentDetail(row.id);
    if (res?.ok) {
      selectedIncident.value = res.data.incidente;
      incidentHistory.value = res.data.historial || [];
      showDetailModal.value = true;
    }
  } catch (err) {
    showBanner(err.message, 'error');
  }
}

function openActionModal(row) {
  actionTarget.value = row;
  showActionModal.value = true;
}

async function submitUpdateIncident(payload) {
  if (!actionTarget.value) return;
  isSubmitting.value = true;
  try {
    const res = await incidentsApi.updateIncident({
      id: actionTarget.value.id,
      estado: payload.newStatus,
      usuario_asignado_id: payload.assignedId,
      comentario: payload.reason
    });
    if (res?.ok) {
      showBanner(res.message, 'success');
      showActionModal.value = false;
      await loadData();
    }
  } catch (err) {
    showBanner(err.message, 'error');
  } finally {
    isSubmitting.value = false;
  }
}

onMounted(() => {
  loadData();
});
</script>
