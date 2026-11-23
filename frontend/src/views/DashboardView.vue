<template>
  <div class="dashboard-page space-y-4">
    <!-- 1. Fila de las 4 Tarjetas de KPI (Capsulas Modernas Enterprise) -->
    <div class="grid grid-cols-4 gap-3">
      <!-- KPI 1: Alertas Activas (247) -->
      <div class="kpi-card">
        <div class="kpi-info">
          <span class="kpi-label">ALERTAS ACTIVAS</span>
          <div class="kpi-val">{{ kpis.alertas_activas }}</div>
        </div>
        <div class="kpi-icon-wrap icon-amber">
          <svg class="kpi-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
          </svg>
        </div>
      </div>

      <!-- KPI 2: Incidentes Crítico (10) -->
      <div class="kpi-card border-rose-hover">
        <div class="kpi-info">
          <span class="kpi-label">INCIDENTES CRÍTICOS</span>
          <div class="kpi-val val-rose">{{ kpis.incidentes_criticos }}</div>
        </div>
        <div class="kpi-icon-wrap icon-rose">
          <svg class="kpi-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
        </div>
      </div>

      <!-- KPI 3: Tiempo Resp. (8.2 min) -->
      <div class="kpi-card">
        <div class="kpi-info">
          <span class="kpi-label">TIEMPO PROMEDIO RESP.</span>
          <div class="kpi-val val-blue">{{ kpis.tiempo_respuesta_min }} <span class="val-unit">min</span></div>
        </div>
        <div class="kpi-icon-wrap icon-blue">
          <svg class="kpi-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
      </div>

      <!-- KPI 4: Sistemas (1,284) -->
      <div class="kpi-card">
        <div class="kpi-info">
          <span class="kpi-label">SISTEMAS MONITOREADOS</span>
          <div class="kpi-val val-emerald">{{ kpis.sistemas_monitoreados.toLocaleString() }}</div>
        </div>
        <div class="kpi-icon-wrap icon-emerald">
          <svg class="kpi-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
          </svg>
        </div>
      </div>
    </div>

    <!-- 2. Área Central (Gráfica de Tendencia 24h + Panel de Últimos Incidentes) -->
    <div class="middle-layout">
      <!-- Tarjeta Izquierda: Tendencia de Alertas (Últimas 24h) -->
      <div class="chart-card">
        <div class="card-top-row">
          <div class="card-heading">
            <svg class="heading-icon text-blue" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
            </svg>
            <span>Tendencia de Alertas (Últimas 24h)</span>
          </div>
          <span class="peak-tag">Pico: 78 eventos (09:00)</span>
        </div>

        <!-- Gráfica SVG Contenida -->
        <div class="svg-chart-container">
          <div class="chart-grid-line top-line"><span>80</span></div>
          <div class="chart-grid-line mid-line"><span>40</span></div>

          <svg class="trend-polyline-svg" viewBox="0 0 700 100" preserveAspectRatio="none">
            <defs>
              <linearGradient id="bluePolyGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.35" />
                <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.0" />
              </linearGradient>
            </defs>
            <polygon points="0,100 0,85 100,92 200,70 300,15 400,45 500,78 600,22 700,52 700,100" fill="url(#bluePolyGrad)" />
            <polyline points="0,85 100,92 200,70 300,15 400,45 500,78 600,22 700,52" fill="none" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
            <circle cx="300" cy="15" r="3.5" fill="#ffffff" stroke="#2563eb" stroke-width="2" />
            <circle cx="600" cy="22" r="3" fill="#3b82f6" />
          </svg>

          <!-- Eje de Horas -->
          <div class="chart-hours-row">
            <span v-for="pt in tendencia" :key="pt.hora">{{ pt.hora }}</span>
          </div>
        </div>
      </div>

      <!-- Tarjeta Derecha: Últimos Incidentes (Mockup Oficial) -->
      <div class="recent-incidents-card">
        <div class="card-top-row">
          <div class="card-heading">
            <svg class="heading-icon text-amber" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Últimos Incidentes</span>
          </div>
          <router-link to="/incidents" class="link-view-all">Ver todos &rarr;</router-link>
        </div>

        <div class="incidents-mini-list">
          <div 
            v-for="inc in ultimosIncidentes" 
            :key="inc.id"
            class="incident-mini-row"
            @click="$router.push('/incidents')"
          >
            <div class="mini-left">
              <span class="mini-time">{{ inc.hora_formato || '10:42 AM' }}</span>
              <span class="mini-title truncate">{{ inc.titulo }}</span>
            </div>
            <span :class="['severity-pill shrink-0', inc.gravedad.toLowerCase()]">
              [{{ inc.gravedad }}]
            </span>
          </div>
        </div>

        <div class="mini-card-action">
          <router-link to="/incidents" class="btn-primary mini-btn">
            Gestionar Solicitudes &rarr;
          </router-link>
        </div>
      </div>
    </div>

    <!-- 3. Triage Rápido de Solicitudes (Con Badges Oficiales de Captura 3) -->
    <div class="saas-card table-section">
      <div class="table-section-header">
        <div class="card-heading">
          <svg class="heading-icon text-blue" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
          <span>Control de Solicitudes en Tiempo Real</span>
        </div>
        <span class="table-badge">Transacciones ACID Activas</span>
      </div>

      <div class="table-responsive-wrapper">
        <table class="saas-table">
          <thead>
            <tr>
              <th>Folio</th>
              <th>Título de Solicitud / Incidente</th>
              <th class="text-center">Severidad</th>
              <th class="text-center">Estado del Proceso</th>
              <th class="text-right">Acción</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="inc in ultimosIncidentes" :key="inc.id">
              <td class="font-mono text-blue font-bold">{{ inc.folio }}</td>
              <td class="text-white">{{ inc.titulo }}</td>
              <td class="text-center">
                <span :class="['severity-pill', inc.gravedad.toLowerCase()]">
                  {{ inc.gravedad }}
                </span>
              </td>
              <td class="text-center">
                <RequestStatusBadge :status="inc.estado" />
              </td>
              <td class="text-right">
                <router-link to="/incidents" class="btn-secondary btn-xs">
                  Detalles
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { dashboardApi } from '../services/dashboardApi';
import RequestStatusBadge from '../components/RequestStatusBadge.vue';

const kpis = ref({
  alertas_activas: 247,
  incidentes_criticos: 10,
  tiempo_respuesta_min: 8.2,
  sistemas_monitoreados: 1284
});

const ultimosIncidentes = ref([
  { id: 1, folio: 'INC-2026-001', titulo: 'Malware Detectado en Endpoint Contabilidad', gravedad: 'Critical', estado: 'Open', hora_formato: '10:42 AM' },
  { id: 2, folio: 'INC-2026-002', titulo: 'Intento Login Fallido Recurrente (Brute Force)', gravedad: 'Medium', estado: 'InProgress', hora_formato: '10:35 AM' },
  { id: 3, folio: 'INC-2026-003', titulo: 'Puerto Inseguro Escaneado (Puerto 22 y 3389)', gravedad: 'Low', estado: 'Resolved', hora_formato: '10:18 AM' }
]);

const tendencia = ref([
  { hora: '00:00' }, { hora: '03:00' }, { hora: '06:00' }, { hora: '09:00' },
  { hora: '12:00' }, { hora: '15:00' }, { hora: '18:00' }, { hora: '21:00' }
]);

async function loadData() {
  try {
    const res = await dashboardApi.getStats();
    if (res?.ok && res?.data) {
      if (res.data.kpis) kpis.value = res.data.kpis;
      if (res.data.ultimos_incidentes?.length) {
        ultimosIncidentes.value = res.data.ultimos_incidentes;
      }
    }
  } catch (e) {
    console.warn('Dashboard stats fallback:', e);
  }
}

onMounted(() => {
  loadData();
});
</script>

<style scoped>
.dashboard-page {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

/* 1. Tarjetas de KPI */
.kpi-card {
  background: #14203d;
  border: 1px solid rgba(59, 130, 246, 0.22);
  border-radius: 12px;
  padding: 0.85rem 1rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  transition: all 0.18s ease;
}

.kpi-card:hover {
  border-color: rgba(59, 130, 246, 0.45);
  transform: translateY(-1px);
}

.border-rose-hover:hover {
  border-color: rgba(239, 68, 68, 0.45);
}

.kpi-label {
  font-size: 0.675rem;
  font-weight: 700;
  color: #94a3b8;
  letter-spacing: 0.05em;
  display: block;
}

.kpi-val {
  font-size: 1.65rem;
  font-weight: 800;
  font-family: var(--font-mono);
  color: #ffffff;
  line-height: 1.1;
  margin-top: 0.2rem;
}

.val-rose { color: #f87171; }
.val-blue { color: #60a5fa; }
.val-emerald { color: #34d399; }
.val-unit { font-size: 0.85rem; color: #94a3b8; font-weight: normal; }

.kpi-icon-wrap {
  width: 36px;
  height: 36px;
  max-width: 36px;
  max-height: 36px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.icon-amber { background: rgba(245, 158, 11, 0.14); color: #fbbf24; }
.icon-rose { background: rgba(239, 68, 68, 0.14); color: #f87171; }
.icon-blue { background: rgba(59, 130, 246, 0.14); color: #60a5fa; }
.icon-emerald { background: rgba(16, 185, 129, 0.14); color: #34d399; }

.kpi-svg {
  width: 18px !important;
  height: 18px !important;
  max-width: 18px !important;
  max-height: 18px !important;
}

/* 2. Layout Central */
.middle-layout {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 1rem;
}

@media (max-width: 900px) {
  .middle-layout { grid-template-columns: 1fr; }
}

.chart-card, .recent-incidents-card {
  background: #14203d;
  border: 1px solid rgba(59, 130, 246, 0.22);
  border-radius: 12px;
  padding: 1rem;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.card-top-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.75rem;
}

.card-heading {
  display: flex;
  align-items: center;
  gap: 0.45rem;
  font-size: 0.8rem;
  font-weight: 700;
  color: #ffffff;
}

.heading-icon {
  width: 16px !important;
  height: 16px !important;
  max-width: 16px !important;
  max-height: 16px !important;
}

.text-blue { color: #38bdf8; }
.text-amber { color: #fbbf24; }

.peak-tag {
  font-size: 0.685rem;
  font-family: var(--font-mono);
  background: rgba(59, 130, 246, 0.15);
  color: #60a5fa;
  padding: 0.15rem 0.5rem;
  border-radius: 4px;
  border: 1px solid rgba(59, 130, 246, 0.3);
}

.link-view-all {
  font-size: 0.725rem;
  color: #38bdf8;
  text-decoration: none;
}
.link-view-all:hover { text-decoration: underline; }

.svg-chart-container {
  height: 140px;
  background: #0b1329;
  border: 1px solid rgba(148, 163, 184, 0.12);
  border-radius: 8px;
  padding: 0.5rem;
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.chart-grid-line {
  position: absolute;
  left: 0.5rem;
  right: 0.5rem;
  border-bottom: 1px solid rgba(148, 163, 184, 0.08);
  font-size: 0.65rem;
  color: #64748b;
  font-family: var(--font-mono);
}
.top-line { top: 1.5rem; }
.mid-line { top: 4.5rem; }

.trend-polyline-svg {
  width: 100% !important;
  height: 90px !important;
  max-width: 100% !important;
  max-height: 90px !important;
}

.chart-hours-row {
  display: flex;
  justify-content: space-between;
  font-size: 0.685rem;
  color: #94a3b8;
  font-family: var(--font-mono);
  border-top: 1px solid rgba(148, 163, 184, 0.1);
  padding-top: 0.25rem;
}

.incidents-mini-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.incident-mini-row {
  background: #0b1329;
  border: 1px solid rgba(148, 163, 184, 0.1);
  border-radius: 6px;
  padding: 0.55rem 0.7rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.15s ease;
}

.incident-mini-row:hover {
  border-color: rgba(59, 130, 246, 0.4);
}

.mini-left {
  display: flex;
  align-items: center;
  gap: 0.45rem;
  min-width: 0;
}

.mini-time {
  font-size: 0.685rem;
  font-family: var(--font-mono);
  color: #38bdf8;
  font-weight: 600;
  flex-shrink: 0;
}

.mini-title {
  font-size: 0.775rem;
  color: #f8fafc;
}

.mini-card-action {
  margin-top: 0.75rem;
  border-top: 1px solid rgba(148, 163, 184, 0.1);
  padding-top: 0.65rem;
  display: flex;
  justify-content: flex-end;
}

.mini-btn {
  font-size: 0.75rem;
  padding: 0.4rem 0.8rem;
}

/* 3. Triage Rápido */
.table-section {
  padding: 1rem;
}

.table-section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.75rem;
}

.table-badge {
  font-size: 0.685rem;
  font-family: var(--font-mono);
  color: #94a3b8;
}

.table-responsive-wrapper {
  overflow-x: auto;
  border: 1px solid rgba(148, 163, 184, 0.12);
  border-radius: 8px;
}

.saas-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.775rem;
  text-align: left;
}

.saas-table thead tr {
  background: #0f172a;
  color: #94a3b8;
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  border-bottom: 1px solid rgba(148, 163, 184, 0.12);
}

.saas-table th, .saas-table td {
  padding: 0.55rem 0.75rem;
}

.saas-table tbody tr {
  background: #0b1329;
  border-bottom: 1px solid rgba(148, 163, 184, 0.07);
  transition: background 0.15s ease;
}

.saas-table tbody tr:hover {
  background: #14203d;
}

.text-center { text-align: center; }
.text-right { text-align: right; }
.btn-xs { font-size: 0.7rem; padding: 0.25rem 0.6rem; }
</style>
