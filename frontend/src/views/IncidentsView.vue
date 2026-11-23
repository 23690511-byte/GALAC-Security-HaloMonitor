<template>
  <div class="space-y-6">
    <!-- Encabezado de Página -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-black text-white flex items-center gap-2">
          <span class="w-2.5 h-6 bg-cyan-400 rounded-sm inline-block"></span>
          Gestión Integral de Incidentes
        </h1>
        <p class="text-xs text-slate-400 mt-1 font-mono">
          Ciclo de Vida: Registro, Trazabilidad Inmutable, Transiciones ACID & Bloqueo Concurrente
        </p>
      </div>

      <!-- Botón de Nuevo Incidente (Solo Administrador y Analista) -->
      <div v-if="hasRole(['Administrador', 'Analista'])">
        <button @click="openCreateModal" class="btn-cyber-primary text-xs py-2 px-4 shadow-[0_0_15px_rgba(0,242,254,0.3)]">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Registrar Nuevo Incidente
        </button>
      </div>
    </div>

    <!-- Mensajes de Notificación Global -->
    <div v-if="bannerMessage" :class="bannerType === 'error' ? 'bg-rose-500/20 border-rose-500/40 text-rose-300' : 'bg-emerald-500/20 border-emerald-500/40 text-emerald-300'" class="p-3 rounded-lg border text-xs flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="font-bold">{{ bannerType === 'error' ? 'Error:' : 'Éxito:' }}</span>
        <span>{{ bannerMessage }}</span>
      </div>
      <button @click="bannerMessage = ''" class="hover:text-white">&times;</button>
    </div>

    <!-- Tabla Inteligente DataGrid (Requisito 4) -->
    <div class="cyber-card p-5">
      <DataGrid
        :data="incidents"
        :columns="columns"
        search-placeholder="Buscar incidente por folio, título, categoría o sistema..."
        export-title="Incidentes_HaloMonitor"
      >
        <!-- Slot Dinámico por Columna: Gravedad -->
        <template #cell-gravedad="{ value }">
          <span :class="['severity-badge text-xs', `severity-${(value || '').toLowerCase()}`]">
            {{ value }}
          </span>
        </template>

        <!-- Slot Dinámico por Columna: Estado -->
        <template #cell-estado="{ value }">
          <RequestStatusBadge :status="value" />
        </template>

        <!-- Slot Dinámico por Columna: Folio -->
        <template #cell-folio="{ value }">
          <span class="font-mono text-cyan-400 font-semibold">{{ value }}</span>
        </template>

        <!-- Slot Dinámico por Columna: Costo Congelado -->
        <template #cell-precio_contratado="{ value }">
          <span class="font-mono text-slate-300">
            ${{ Number(value || 0).toLocaleString('es-MX', { minimumFractionDigits: 2 }) }}
          </span>
        </template>

        <!-- Slot para Acciones por Registro -->
        <template #actions="{ row }">
          <div class="flex items-center justify-end gap-1.5">
            <!-- Botón Ver Detalle & Línea de Tiempo -->
            <button 
              @click="viewDetail(row)"
              class="btn-cyber-secondary py-1 px-2.5 text-xs text-cyan-300 hover:text-white"
              title="Ver bitácora de auditoría y línea de tiempo"
            >
              Historial
            </button>

            <!-- Botón Gestionar Estado (Solo Administrador y Analista) -->
            <button 
              v-if="hasRole(['Administrador', 'Analista'])"
              @click="openActionModal(row)"
              class="btn-cyber-primary py-1 px-2.5 text-xs"
              title="Cambiar estado o reasignar operador"
            >
              Gestionar
            </button>
          </div>
        </template>
      </DataGrid>
    </div>

    <!-- MODAL 1: Registrar Nuevo Incidente -->
    <div v-if="showCreateModal" class="modal-overlay" @click.self="showCreateModal = false">
      <div class="modal-content max-w-xl">
        <div class="flex items-center justify-between p-4 border-b border-cyan-500/20 bg-slate-900/60 rounded-t-xl">
          <h3 class="text-base font-bold text-cyan-400 flex items-center gap-2">
            <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Apertura de Incidente de Seguridad
          </h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <form @submit.prevent="submitCreateIncident" class="p-5 space-y-4 text-xs">
          <div>
            <label class="block font-semibold text-slate-300 uppercase mb-1">Título del Incidente:</label>
            <input 
              type="text" 
              v-model="createForm.titulo" 
              placeholder="Ej. Malware Detectado en Servidor Web PyME" 
              required
              class="cyber-input text-sm"
            />
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-slate-300 uppercase mb-1">Categoría:</label>
              <select v-model="createForm.categoria" class="cyber-select text-sm">
                <option value="Malware">Malware / Ransomware</option>
                <option value="Intrusión">Intento de Intrusión (Brute Force)</option>
                <option value="Escaneo">Escaneo no Autorizado</option>
                <option value="Fuga de Datos">Fuga / Exfiltración de Datos</option>
                <option value="Disponibilidad">Falla de Disponibilidad</option>
              </select>
            </div>

            <div>
              <label class="block font-semibold text-slate-300 uppercase mb-1">Severidad / Impacto:</label>
              <select v-model="createForm.gravedad" class="cyber-select text-sm">
                <option value="Low">Low (Bajo)</option>
                <option value="Medium">Medium (Medio)</option>
                <option value="High">High (Alto)</option>
                <option value="Critical">Critical (Crítico)</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-slate-300 uppercase mb-1">Activo / Sistema Afectado:</label>
              <select v-model="createForm.sistema_id" class="cyber-select text-sm">
                <option :value="null">-- Seleccione Sistema --</option>
                <option v-for="s in catalogs.sistemas" :key="s.id" :value="s.id">
                  {{ s.nombre }} ({{ s.ip_host }})
                </option>
              </select>
            </div>

            <div>
              <label class="block font-semibold text-slate-300 uppercase mb-1">Asignar Operador / Analista:</label>
              <select v-model="createForm.usuario_asignado_id" class="cyber-select text-sm">
                <option :value="null">-- Asignación Automática --</option>
                <option v-for="u in catalogs.usuarios" :key="u.id" :value="u.id">
                  {{ u.nombre }} ({{ u.rol }})
                </option>
              </select>
            </div>
          </div>

          <div>
            <label class="block font-semibold text-cyan-400 uppercase mb-1">
              Fecha y Hora Límite de Atención Futura (SLA):
            </label>
            <input 
              type="datetime-local" 
              v-model="createForm.fecha_limite_atencion" 
              :min="minFutureDate"
              class="cyber-input font-mono text-sm"
            />
            <span class="text-[10px] text-slate-500">Debe ser una fecha futura para cálculo de SLA</span>
          </div>

          <div>
            <label class="block font-semibold text-slate-300 uppercase mb-1">Descripción Técnica Detallada:</label>
            <textarea 
              v-model="createForm.descripcion" 
              rows="3" 
              placeholder="Describa el vector de ataque detectado, evidencias o bitácoras asociadas..." 
              required
              class="cyber-textarea text-sm"
            ></textarea>
          </div>

          <div class="p-3 bg-cyan-950/30 border border-cyan-800/40 rounded-lg text-slate-400 space-y-1 text-[11px]">
            <span class="text-cyan-400 font-bold block">&bull; Garantía de Trazabilidad e Inmutabilidad:</span>
            <p>Se congelarán los parámetros operativos y costos de SLA al momento del registro. Se disparará alerta automática ante severidades High/Critical.</p>
          </div>

          <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
            <button type="button" @click="showCreateModal = false" class="btn-cyber-secondary">
              Cancelar
            </button>
            <button type="submit" :disabled="isSubmitting" class="btn-cyber-primary">
              <span v-if="isSubmitting" class="animate-spin">&#9696;</span>
              Guardar con Transacción ACID
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL 2: Detalle e Historial de Auditoría con Timeline -->
    <div v-if="showDetailModal" class="modal-overlay" @click.self="showDetailModal = false">
      <div class="modal-content max-w-2xl">
        <div class="flex items-center justify-between p-4 border-b border-cyan-500/20 bg-slate-900/60 rounded-t-xl">
          <div>
            <h3 class="text-base font-bold text-white flex items-center gap-2">
              <span class="font-mono text-cyan-400">{{ selectedIncident?.folio }}</span>
              <span>- {{ selectedIncident?.titulo }}</span>
            </h3>
            <span class="text-xs text-slate-400 font-mono">
              Registrado por: {{ selectedIncident?.reporta_nombre }} &bull; {{ selectedIncident?.fecha_registro }}
            </span>
          </div>
          <button @click="showDetailModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <div class="p-5 space-y-5">
          <!-- Metadatos Clave -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-3 bg-slate-900/50 rounded-lg border border-slate-800 text-xs">
            <div>
              <span class="text-slate-400 block">Severidad:</span>
              <span :class="['severity-badge mt-1', `severity-${(selectedIncident?.gravedad || '').toLowerCase()}`]">
                {{ selectedIncident?.gravedad }}
              </span>
            </div>
            <div>
              <span class="text-slate-400 block">Estado:</span>
              <div class="mt-1">
                <RequestStatusBadge :status="selectedIncident?.estado || ''" />
              </div>
            </div>
            <div>
              <span class="text-slate-400 block">Sistema Afectado:</span>
              <span class="text-white font-medium block mt-1">
                {{ selectedIncident?.sistema_nombre || 'No especificado' }}
              </span>
            </div>
            <div>
              <span class="text-slate-400 block">Operador Asignado:</span>
              <span class="text-cyan-300 font-medium block mt-1">
                {{ selectedIncident?.asignado_nombre || 'Sin asignar' }}
              </span>
            </div>
          </div>

          <!-- Descripción -->
          <div>
            <h4 class="text-xs uppercase font-semibold text-slate-400 mb-1">Descripción del Evento:</h4>
            <p class="text-sm text-slate-200 bg-slate-900/70 p-3 rounded-lg border border-slate-800 leading-relaxed">
              {{ selectedIncident?.descripcion }}
            </p>
          </div>

          <!-- Línea de Tiempo de Auditoría (Componente Timeline.vue) -->
          <div>
            <h4 class="text-xs uppercase font-bold text-cyan-400 mb-3 flex items-center gap-2">
              <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Línea de Tiempo y Auditoría Inmutable (historial_solicitud):
            </h4>
            <Timeline :items="incidentHistory" />
          </div>
        </div>

        <div class="flex justify-end p-4 border-t border-slate-800 bg-slate-900/40 rounded-b-xl">
          <button @click="showDetailModal = false" class="btn-cyber-secondary text-xs">
            Cerrar Ventana
          </button>
        </div>
      </div>
    </div>

    <!-- MODAL 3: Cambiar Estado / Acción Configurable (RequestActionModal.vue) -->
    <RequestActionModal
      :is-open="showActionModal"
      title="Gestión de Ciclo de Vida del Incidente"
      :description="`Actualización de estado con SELECT ... FOR UPDATE para evitar condiciones de carrera en el folio ${actionTarget?.folio}.`"
      confirm-text="Aplicar Transición ACID"
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

// Columnas para el DataGrid Inteligente
const columns = [
  { key: 'folio', label: 'Folio' },
  { key: 'titulo', label: 'Título del Incidente' },
  { key: 'categoria', label: 'Categoría' },
  { key: 'gravedad', label: 'Gravedad', align: 'center' },
  { key: 'estado', label: 'Estado', align: 'center' },
  { key: 'sistema_nombre', label: 'Sistema PyME' },
  { key: 'asignado_nombre', label: 'Asignado' },
  { key: 'precio_contratado', label: 'Costo Congelado', align: 'right' },
  { key: 'fecha_registro', label: 'Fecha' }
];

// Estado de Modales
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

// Opciones de transición según la máquina de estados estricta
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
      { value: 'Closed', label: 'Cierre Definitivo de Ticket' },
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
  setTimeout(() => {
    bannerMessage.value = '';
  }, 6000);
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
