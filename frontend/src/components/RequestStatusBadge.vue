<template>
  <span :class="['status-badge', badgeClass]">
    <span class="w-1.5 h-1.5 rounded-full inline-block mr-1" :style="{ backgroundColor: dotColor }"></span>
    {{ displayLabel }}
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

const badgeClass = computed(() => {
  const s = normalized.value;
  if (['open', 'pendiente', 'nuevo'].includes(s)) return 'status-pending';
  if (['inprogress', 'en proceso', 'autorizado', 'asignado'].includes(s)) return 'status-inprogress';
  if (['resolved', 'resuelto', 'completado', 'aprobado'].includes(s)) return 'status-completed';
  if (['closed', 'cerrado', 'rechazado', 'cancelado'].includes(s)) return 'status-closed';
  return 'status-pending';
});

const dotColor = computed(() => {
  const s = normalized.value;
  if (['open', 'pendiente', 'nuevo'].includes(s)) return '#fbbf24';
  if (['inprogress', 'en proceso', 'autorizado', 'asignado'].includes(s)) return '#60a5fa';
  if (['resolved', 'resuelto', 'completado', 'aprobado'].includes(s)) return '#34d399';
  if (['closed', 'cerrado', 'rechazado', 'cancelado'].includes(s)) return '#f87171';
  return '#fbbf24';
});

const displayLabel = computed(() => {
  const map = {
    'open': 'Abierto (Pendiente)',
    'inprogress': 'En Proceso',
    'resolved': 'Resuelto',
    'closed': 'Cerrado'
  };
  return map[normalized.value] || props.status;
});
</script>
