<template>
  <span :class="['status-pill', pillClass]">
    <span class="status-indicator-dot"></span>
    <span>{{ displayLabel }}</span>
  </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  status: {
    type: String,
    required: true
  }
});

const normalized = computed(() => (props.status || '').toLowerCase().trim());

const pillClass = computed(() => {
  const s = normalized.value;
  if (['open', 'pendiente', 'nuevo', 'solicitada'].includes(s)) return 'status-solicitada';
  if (['inprogress', 'en proceso', 'autorizado', 'autorizada', 'asignado'].includes(s)) return 'status-autorizada';
  if (['resolved', 'resuelto', 'completado', 'completada'].includes(s)) return 'status-completada';
  if (['closed', 'cerrado', 'rechazado', 'cancelado', 'cancelada'].includes(s)) return 'status-cancelada';
  return 'status-solicitada';
});

const displayLabel = computed(() => {
  const s = normalized.value;
  if (['open', 'pendiente', 'nuevo', 'solicitada'].includes(s)) return 'SOLICITADA';
  if (['inprogress', 'en proceso', 'autorizado', 'autorizada', 'asignado'].includes(s)) return 'AUTORIZADA';
  if (['resolved', 'resuelto', 'completado', 'completada'].includes(s)) return 'COMPLETADA';
  if (['closed', 'cerrado', 'rechazado', 'cancelado', 'cancelada'].includes(s)) return 'CERRADA';
  return props.status.toUpperCase();
});
</script>

<style scoped>
.status-indicator-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: currentColor;
}
</style>
