<template>
  <div v-if="isOpen" class="modal-backdrop" @click.self="onCancel">
    <div class="modal-dialog">
      <!-- Encabezado del Modal -->
      <div class="modal-head">
        <h3 class="modal-title">
          <svg class="modal-title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span>{{ title }}</span>
        </h3>
        <button @click="onCancel" class="close-btn">&times;</button>
      </div>

      <!-- Cuerpo del Modal -->
      <div class="modal-body">
        <p v-if="description" class="modal-desc">{{ description }}</p>

        <!-- Selector de Nuevo Estado (si aplica) -->
        <div v-if="statusOptions && statusOptions.length > 0" class="field-group">
          <label class="field-label">Nuevo Estado de la Solicitud:</label>
          <select v-model="formData.newStatus" class="saas-select">
            <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">
              {{ opt.label }}
            </option>
          </select>
        </div>

        <!-- Asignación de Analista (si aplica y si tiene permiso) -->
        <div v-if="analystOptions && analystOptions.length > 0 && canAssign" class="field-group">
          <label class="field-label">Asignar Operador / Analista:</label>
          <select v-model="formData.assignedId" class="saas-select">
            <option :value="null">-- Sin asignar --</option>
            <option v-for="an in analystOptions" :key="an.id" :value="an.id">
              {{ an.nombre }} ({{ an.email }})
            </option>
          </select>
        </div>

        <!-- Selector de Fecha/Hora Futura (SLA) -->
        <div v-if="requireFutureDate" class="field-group">
          <label class="field-label text-blue">Fecha y Hora Límite de Atención (SLA Futuro):</label>
          <input 
            type="datetime-local" 
            v-model="formData.futureDate" 
            :min="minDate"
            class="saas-input font-mono"
            required
          />
          <span v-if="dateError" class="field-error">{{ dateError }}</span>
        </div>

        <!-- Captura de Motivo Obligatorio (Auditoría) -->
        <div v-if="requireReason" class="field-group">
          <label class="field-label text-amber">Motivo / Justificación de la Acción (Obligatorio):</label>
          <textarea 
            v-model="formData.reason" 
            rows="3" 
            placeholder="Describa el motivo del cambio para la bitácora inmutable..." 
            class="saas-textarea"
            required
          ></textarea>
          <span v-if="reasonError" class="field-error">{{ reasonError }}</span>
        </div>
      </div>

      <!-- Pie del Modal -->
      <div class="modal-foot">
        <button type="button" @click="onCancel" class="btn-secondary">Cancelar</button>
        <button 
          type="button" 
          @click="onConfirm" 
          :disabled="isSubmitting"
          class="btn-primary"
        >
          <span v-if="isSubmitting">&#9696; Procesando...</span>
          <span v-else>{{ confirmText }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';

const props = defineProps({
  isOpen: { type: Boolean, default: false },
  title: { type: String, default: 'Confirmar Acción' },
  description: { type: String, default: '' },
  confirmText: { type: String, default: 'Confirmar Transacción' },
  requireReason: { type: Boolean, default: true },
  requireFutureDate: { type: Boolean, default: false },
  statusOptions: { type: Array, default: () => [] },
  analystOptions: { type: Array, default: () => [] },
  initialStatus: { type: String, default: '' },
  initialAssigned: { type: [Number, String], default: null },
  canAssign: { type: Boolean, default: true },
  isSubmitting: { type: Boolean, default: false }
});

const emit = defineEmits(['confirm', 'cancel']);

const formData = ref({
  reason: '',
  futureDate: '',
  newStatus: '',
  assignedId: null
});

const reasonError = ref('');
const dateError = ref('');

const minDate = computed(() => {
  const now = new Date();
  now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
  return now.toISOString().slice(0, 16);
});

watch(() => props.isOpen, (open) => {
  if (open) {
    formData.value = {
      reason: '',
      futureDate: '',
      newStatus: props.initialStatus || (props.statusOptions[0]?.value || ''),
      assignedId: props.initialAssigned || null
    };
    reasonError.value = '';
    dateError.value = '';
  }
});

function onCancel() {
  emit('cancel');
}

function onConfirm() {
  reasonError.value = '';
  dateError.value = '';

  if (props.requireReason && !formData.value.reason.trim()) {
    reasonError.value = 'El motivo de auditoría es obligatorio para la trazabilidad.';
    return;
  }

  if (props.requireFutureDate) {
    if (!formData.value.futureDate) {
      dateError.value = 'Debe indicar una fecha y hora límite.';
      return;
    }
    const selected = new Date(formData.value.futureDate).getTime();
    if (selected <= Date.now()) {
      dateError.value = 'La fecha debe ser estrictamente en el futuro.';
      return;
    }
  }

  emit('confirm', { ...formData.value });
}
</script>

<style scoped>
.modal-head {
  padding: 0.85rem 1.15rem;
  border-bottom: 1px solid rgba(148, 163, 184, 0.12);
  background: #0f172a;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-top-left-radius: 14px;
  border-top-right-radius: 14px;
}

.modal-title {
  display: flex;
  align-items: center;
  gap: 0.45rem;
  font-size: 0.85rem;
  font-weight: 700;
  color: #38bdf8;
}

.modal-title-icon {
  width: 16px !important;
  height: 16px !important;
  max-width: 16px !important;
  max-height: 16px !important;
}

.close-btn {
  background: none;
  border: none;
  color: #94a3b8;
  font-size: 1.25rem;
  cursor: pointer;
  line-height: 1;
}

.close-btn:hover { color: #ffffff; }

.modal-body {
  padding: 1.15rem;
  display: flex;
  flex-direction: column;
  gap: 0.85rem;
}

.modal-desc {
  font-size: 0.8rem;
  color: #cbd5e1;
  line-height: 1.4;
}

.field-group {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.field-label {
  font-size: 0.725rem;
  font-weight: 600;
  color: #94a3b8;
  text-transform: uppercase;
}

.text-blue { color: #38bdf8; }
.text-amber { color: #fbbf24; }

.field-error {
  font-size: 0.7rem;
  color: #f87171;
}

.modal-foot {
  padding: 0.85rem 1.15rem;
  border-top: 1px solid rgba(148, 163, 184, 0.12);
  background: #0f172a;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 0.5rem;
  border-bottom-left-radius: 14px;
  border-bottom-right-radius: 14px;
}
</style>
