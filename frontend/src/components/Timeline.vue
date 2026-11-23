<template>
  <div class="timeline-wrapper">
    <div v-if="!items || items.length === 0" class="empty-timeline">
      No hay registros históricos en la bitácora.
    </div>

    <div v-else class="timeline-track">
      <div v-for="(item, idx) in items" :key="item.id || idx" class="timeline-entry">
        <div class="timeline-dot">
          <div class="timeline-dot-inner"></div>
        </div>

        <div class="timeline-box">
          <div class="timeline-meta">
            <div class="user-block">
              <span class="user-txt">{{ item.usuario_nombre || 'Sistema' }}</span>
              <span v-if="item.usuario_rol" class="role-pill">{{ item.usuario_rol }}</span>
            </div>
            <span class="date-txt">{{ item.fecha_registro }}</span>
          </div>

          <div class="transition-row">
            <span v-if="item.estado_anterior" class="prev-state">
              {{ item.estado_anterior }} &rarr;
            </span>
            <span class="next-state">{{ item.estado_nuevo }}</span>
          </div>

          <p class="comment-box">{{ item.comentario }}</p>

          <!-- Snapshot Inmutable Congelado -->
          <div v-if="item.datos_congelados && Object.keys(item.datos_congelados).length" class="frozen-snapshot">
            <span class="snapshot-tag">&bull; Snapshot de Integridad Congelado:</span>
            <div class="snapshot-items">
              <span v-for="(val, key) in item.datos_congelados" :key="key" class="snapshot-item">
                <span class="text-dim">{{ key }}:</span> {{ val }}
              </span>
            </div>
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
.empty-timeline {
  text-align: center;
  padding: 1rem;
  color: #64748b;
  font-size: 0.775rem;
}

.timeline-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.35rem;
}

.user-block {
  display: flex;
  align-items: center;
  gap: 0.4rem;
}

.user-txt {
  font-size: 0.775rem;
  font-weight: 700;
  color: #38bdf8;
}

.role-pill {
  font-size: 0.65rem;
  background: #1e293b;
  color: #94a3b8;
  padding: 0.1rem 0.4rem;
  border-radius: 4px;
  font-weight: 600;
}

.date-txt {
  font-size: 0.685rem;
  color: #94a3b8;
  font-family: var(--font-mono);
}

.transition-row {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.725rem;
  margin-bottom: 0.35rem;
}

.prev-state {
  color: #94a3b8;
  text-decoration: line-through;
}

.next-state {
  color: #60a5fa;
  font-weight: 700;
}

.comment-box {
  font-size: 0.775rem;
  color: #e2e8f0;
  background: #0f172a;
  border: 1px solid rgba(148, 163, 184, 0.1);
  padding: 0.5rem 0.65rem;
  border-radius: 6px;
  line-height: 1.4;
}

.frozen-snapshot {
  margin-top: 0.5rem;
  background: #060b17;
  border: 1px solid rgba(59, 130, 246, 0.2);
  border-radius: 4px;
  padding: 0.4rem 0.6rem;
  font-size: 0.685rem;
  font-family: var(--font-mono);
}

.snapshot-tag {
  color: #38bdf8;
  font-weight: 600;
  display: block;
  margin-bottom: 0.2rem;
}

.snapshot-items {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.text-dim { color: #64748b; }
</style>
