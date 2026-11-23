<template>
  <div v-if="isOpen" class="modal-overlay" @click.self="onCancel">
    <div class="modal-content">
      <!-- Encabezado del Modal -->
      <div class="flex items-center justify-between p-4 border-b border-cyan-500/20 bg-slate-900/60 rounded-t-xl">
        <h3 class="text-base font-bold text-cyan-400 flex items-center gap-2">
          <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          {{ title }}
        </h3>
        <button @click="onCancel" class="text-slate-400 hover:text-white p-1 rounded transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Cuerpo del Modal -->
      <div class="p-5 space-y-4 text-sm text-slate-300">
        <p v-if="description" class="text-slate-300 leading-relaxed">{{ description }}</p>

        <!-- Selector de Nuevo Estado (si aplica) -->
        <div v-if="statusOptions && statusOptions.length > 0" class="space-y-1">
          <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">
            Nuevo Estado de Gestión:
          </label>
          <select v-model="formData.newStatus" class="cyber-select">
            <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">
              {{ opt.label }}
            </option>
          </select>
        </div>

        <!-- Asignación de Analista (si aplica) -->
        <div v-if="analystOptions && analystOptions.length > 0" class="space-y-1">
          <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">
            Asignar Operador / Analista SOC:
          </label>
          <select v-model="formData.assignedId" class="cyber-select">
            <option :value="null">-- Sin asignar --</option>
            <option v-for="an in analystOptions" :key="an.id" :value="an.id">
              {{ an.nombre }} ({{ an.email }})
            </option>
          </select>
        </div>

        <!-- Selector de Fecha/Hora Futura (SLA / Compromiso) -->
        <div v-if="requireFutureDate" class="space-y-1">
          <label class="block text-xs font-semibold text-cyan-400 uppercase tracking-wider">
            Fecha / Hora Límite de Atención Futura (SLA):
          </label>
          <input 
            type="datetime-local" 
            v-model="formData.futureDate" 
            :min="minDate"
            class="cyber-input font-mono"
            required
          />
          <p v-if="dateError" class="text-xs text-rose-400 mt-1">{{ dateError }}</p>
        </div>

        <!-- Captura de Motivo Obligatorio (Auditoría) -->
        <div v-if="requireReason" class="space-y-1">
          <label class="block text-xs font-semibold text-amber-400 uppercase tracking-wider">
            Motivo / Comentario de Auditoría (Obligatorio):
          </label>
          <textarea 
            v-model="formData.reason" 
            rows="3" 
            placeholder="Describa detalladamente el motivo del cambio o resolución..." 
            class="cyber-textarea"
            required
          ></textarea>
          <p v-if="reasonError" class="text-xs text-rose-400 mt-1">{{ reasonError }}</p>
        </div>
      </div>

      <!-- Pie del Modal -->
      <div class="flex items-center justify-end gap-3 p-4 border-t border-slate-800 bg-slate-900/50 rounded-b-xl">
        <button type="button" @click="onCancel" class="btn-cyber-secondary text-sm">
          Cancelar
        </button>
        <button 
          type="button" 
          @click="onConfirm" 
          :disabled="isSubmitting"
          :class="confirmBtnClass"
        >
          <span v-if="isSubmitting" class="animate-spin inline-block mr-1">&#9696;</span>
          {{ confirmText }}
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
  confirmText: { type: String, default: 'Confirmar' },
  confirmType: { type: String, default: 'primary' }, // 'primary', 'danger'
  requireReason: { type: Boolean, default: true },
  requireFutureDate: { type: Boolean, default: false },
  statusOptions: { type: Array, default: () => [] },
  analystOptions: { type: Array, default: () => [] },
  initialStatus: { type: String, default: '' },
  initialAssigned: { type: [Number, String], default: null },
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

// Mínimo para fecha futura (ahora mismo)
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

const confirmBtnClass = computed(() => {
  return props.confirmType === 'danger' ? 'btn-cyber-danger text-sm' : 'btn-cyber-primary text-sm';
});

function onCancel() {
  emit('cancel');
}

function onConfirm() {
  reasonError.value = '';
  dateError.value = '';

  if (props.requireReason && !formData.value.reason.trim()) {
    reasonError.value = 'El motivo de auditoría es obligatorio para garantizar la trazabilidad.';
    return;
  }

  if (props.requireFutureDate) {
    if (!formData.value.futureDate) {
      dateError.value = 'Debe seleccionar una fecha y hora límite válida.';
      return;
    }
    const selectedTime = new Date(formData.value.futureDate).getTime();
    if (selectedTime <= Date.now()) {
      dateError.value = 'La fecha debe ser estrictamente en el futuro.';
      return;
    }
  }

  emit('confirm', { ...formData.value });
}
</script>
