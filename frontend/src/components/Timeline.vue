<template>
  <div class="timeline-wrapper">
    <div v-if="!items || items.length === 0" class="text-center py-6 text-slate-400 text-sm">
      No hay registros históricos disponibles.
    </div>

    <div v-else class="timeline-container">
      <div v-for="(item, idx) in items" :key="item.id || idx" class="timeline-step">
        <div class="timeline-marker">
          <div class="timeline-marker-inner"></div>
        </div>

        <div class="timeline-content">
          <div class="flex flex-wrap items-center justify-between gap-2 mb-1.5">
            <div class="flex items-center gap-2">
              <span class="font-semibold text-cyan-400 text-sm">
                {{ item.usuario_nombre || 'Sistema' }}
              </span>
              <span v-if="item.usuario_rol" class="text-xs px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700">
                {{ item.usuario_rol }}
              </span>
            </div>
            <span class="text-xs text-slate-400 font-mono">
              {{ item.fecha_registro }}
            </span>
          </div>

          <div class="flex items-center gap-2 text-xs mb-2">
            <span v-if="item.estado_anterior" class="text-slate-400">
              Estado: <span class="text-slate-300 line-through">{{ item.estado_anterior }}</span> &rarr;
            </span>
            <span class="font-bold text-cyan-300">
              {{ item.estado_nuevo }}
            </span>
          </div>

          <p class="text-sm text-slate-200 bg-slate-900/60 p-2.5 rounded border border-slate-800/80 leading-relaxed">
            {{ item.comentario }}
          </p>

          <!-- Datos Congelados / Snapshot de Operación -->
          <div v-if="item.datos_congelados && Object.keys(item.datos_congelados).length" class="mt-2 text-xs text-slate-400 bg-slate-950/70 p-2 rounded font-mono border border-cyan-900/40">
            <span class="text-cyan-400 font-semibold block mb-0.5">&bull; Snapshot de Integridad Congelado:</span>
            <span v-for="(val, key) in item.datos_congelados" :key="key" class="inline-block mr-3">
              <span class="text-slate-500">{{ key }}:</span> {{ val }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  items: {
    type: Array,
    default: () => []
  }
});
</script>

<style scoped>
.timeline-wrapper {
  width: 100%;
}
</style>
