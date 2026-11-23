<template>
  <div class="space-y-6">
    <!-- Encabezado de Página -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-black text-white flex items-center gap-2">
          <span class="w-2.5 h-6 bg-cyan-400 rounded-sm inline-block"></span>
          Motor de Alertas Internas (SOC Alarms)
        </h1>
        <p class="text-xs text-slate-400 mt-1 font-mono">
          Detección Oportuna &bull; Reglas Automatizadas de Notificación Visual ante Incidentes Críticos
        </p>
      </div>

      <div class="flex items-center gap-2">
        <button 
          v-if="alerts.some(a => !a.leida)" 
          @click="markAllRead" 
          class="btn-cyber-secondary text-xs py-1.5 px-3"
        >
          Marcar Todas como Leídas
        </button>

        <button 
          v-if="hasRole(['Administrador'])" 
          @click="showRuleModal = true" 
          class="btn-cyber-primary text-xs py-1.5 px-3"
        >
          + Nueva Regla de Alerta
        </button>
      </div>
    </div>

    <!-- Filtro Rápido por Severidad -->
    <div class="flex items-center gap-2 flex-wrap text-xs">
      <span class="text-slate-400 font-semibold uppercase tracking-wider">Filtrar por Severidad:</span>
      <button 
        @click="filterSeverity = ''" 
        :class="filterSeverity === '' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-400/50' : 'bg-slate-900 text-slate-400 border-slate-800'"
        class="py-1 px-3 rounded-full border transition-colors"
      >
        Todas ({{ alerts.length }})
      </button>
      <button 
        @click="filterSeverity = 'Critical'" 
        :class="filterSeverity === 'Critical' ? 'bg-rose-500/30 text-rose-300 border-rose-500/60' : 'bg-slate-900 text-slate-400 border-slate-800'"
        class="py-1 px-3 rounded-full border transition-colors"
      >
        Critical (Críticas)
      </button>
      <button 
        @click="filterSeverity = 'High'" 
        :class="filterSeverity === 'High' ? 'bg-orange-500/30 text-orange-300 border-orange-500/60' : 'bg-slate-900 text-slate-400 border-slate-800'"
        class="py-1 px-3 rounded-full border transition-colors"
      >
        High (Altas)
      </button>
      <button 
        @click="filterSeverity = 'Medium'" 
        :class="filterSeverity === 'Medium' ? 'bg-amber-500/30 text-amber-300 border-amber-500/60' : 'bg-slate-900 text-slate-400 border-slate-800'"
        class="py-1 px-3 rounded-full border transition-colors"
      >
        Medium
      </button>
      <button 
        @click="filterSeverity = 'Low'" 
        :class="filterSeverity === 'Low' ? 'bg-teal-500/30 text-teal-300 border-teal-500/60' : 'bg-slate-900 text-slate-400 border-slate-800'"
        class="py-1 px-3 rounded-full border transition-colors"
      >
        Low
      </button>
    </div>

    <!-- Listado de Tarjetas de Alertas -->
    <div class="grid grid-cols-1 gap-3">
      <div v-if="filteredAlerts.length === 0" class="cyber-card p-8 text-center text-slate-400 text-sm">
        No hay alertas que coincidan con el filtro seleccionado.
      </div>

      <div 
        v-for="al in filteredAlerts" 
        :key="al.id"
        class="cyber-card p-4 transition-all flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
        :class="{ 'border-rose-500/40 bg-rose-950/10': al.gravedad === 'Critical' && !al.leida, 'opacity-70': al.leida }"
      >
        <div class="flex items-start gap-3">
          <!-- Icono de Estado -->
          <div 
            class="p-2.5 rounded-xl shrink-0 mt-0.5"
            :class="al.gravedad === 'Critical' ? 'bg-rose-500/20 text-rose-400' : 'bg-amber-500/20 text-amber-400'"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
          </div>

          <div>
            <div class="flex items-center gap-2 flex-wrap">
              <span :class="['severity-badge text-xs', `severity-${al.gravedad.toLowerCase()}`]">
                {{ al.gravedad }}
              </span>
              <span v-if="al.incidente_folio" class="text-xs font-mono text-cyan-400 font-bold">
                {{ al.incidente_folio }}
              </span>
              <span class="text-xs text-slate-400 font-mono">
                {{ al.fecha_emision }}
              </span>
              <span v-if="!al.leida" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 animate-pulse">
                NUEVA ALERTA
              </span>
            </div>

            <h3 class="text-base font-bold text-white mt-1">{{ al.titulo }}</h3>
            <p class="text-sm text-slate-300 mt-0.5 leading-relaxed">{{ al.mensaje }}</p>
          </div>
        </div>

        <!-- Acciones de Alerta -->
        <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
          <router-link 
            v-if="al.incidente_id" 
            to="/incidents"
            class="btn-cyber-secondary py-1 px-3 text-xs"
          >
            Ver Ticket
          </router-link>

          <button 
            v-if="!al.leida" 
            @click="markRead(al.id)" 
            class="btn-cyber-primary py-1 px-3 text-xs"
          >
            Marcar Leída
          </button>
          <span v-else class="text-xs text-slate-500 flex items-center gap-1 font-mono">
            &check; Leída
          </span>
        </div>
      </div>
    </div>

    <!-- Reglas de Alertas Vigentes -->
    <div class="cyber-card p-5 mt-8">
      <h3 class="text-base font-bold text-white mb-3 flex items-center gap-2">
        <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Reglas Automáticas de Disparo de Alertas
      </h3>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-300">
          <thead class="bg-slate-950/60 uppercase text-slate-400 border-b border-slate-800">
            <tr>
              <th class="p-2.5">ID</th>
              <th class="p-2.5">Nombre de la Regla</th>
              <th class="p-2.5">Criterio de Gravedad</th>
              <th class="p-2.5">Notificación Activa</th>
              <th class="p-2.5">Creado Por</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800">
            <tr v-for="r in rules" :key="r.id">
              <td class="p-2.5 font-mono text-cyan-400">{{ r.id }}</td>
              <td class="p-2.5 font-semibold text-white">{{ r.nombre }}</td>
              <td class="p-2.5">
                <span :class="['severity-badge text-[10px]', `severity-${r.criterio_gravedad.toLowerCase()}`]">
                  {{ r.criterio_gravedad }}
                </span>
              </td>
              <td class="p-2.5 text-emerald-400 font-semibold">&check; Activa</td>
              <td class="p-2.5 text-slate-400">{{ r.creador_nombre || 'Administrador' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL Crear Regla -->
    <div v-if="showRuleModal" class="modal-overlay" @click.self="showRuleModal = false">
      <div class="modal-content max-w-md">
        <div class="flex items-center justify-between p-4 border-b border-cyan-500/20 bg-slate-900/60 rounded-t-xl">
          <h3 class="text-base font-bold text-cyan-400">Nueva Regla de Disparo de Alerta</h3>
          <button @click="showRuleModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form @submit.prevent="submitRule" class="p-5 space-y-4 text-xs">
          <div>
            <label class="block font-semibold text-slate-300 uppercase mb-1">Nombre Descriptivo de la Regla:</label>
            <input type="text" v-model="newRule.nombre" placeholder="Ej. Detección de Intentos DDoS" required class="cyber-input text-sm"/>
          </div>
          <div>
            <label class="block font-semibold text-slate-300 uppercase mb-1">Criterio Mínimo de Gravedad:</label>
            <select v-model="newRule.criterio_gravedad" class="cyber-select text-sm">
              <option value="Critical">Critical (Solo Críticos)</option>
              <option value="High">High (Altos y Críticos)</option>
              <option value="Medium">Medium</option>
            </select>
          </div>
          <div class="flex justify-end gap-2 pt-3 border-t border-slate-800">
            <button type="button" @click="showRuleModal = false" class="btn-cyber-secondary">Cancelar</button>
            <button type="submit" class="btn-cyber-primary">Guardar Regla</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuth } from '../composables/useAuth';
import { alertsApi } from '../services/alertsApi';

const { hasRole } = useAuth();

const alerts = ref([]);
const rules = ref([]);
const filterSeverity = ref('');
const showRuleModal = ref(false);

const newRule = ref({
  nombre: '',
  criterio_gravedad: 'High'
});

const filteredAlerts = computed(() => {
  if (!filterSeverity.value) return alerts.value;
  return alerts.value.filter(a => a.gravedad === filterSeverity.value);
});

async function loadAlerts() {
  try {
    const [alRes, ruleRes] = await Promise.all([
      alertsApi.getAlerts(),
      alertsApi.getRules()
    ]);
    if (alRes?.ok) {
      alerts.value = alRes.data.alertas || [];
    }
    if (ruleRes?.ok) {
      rules.value = ruleRes.data || [];
    }
  } catch (err) {
    console.error('Error cargando alertas:', err);
  }
}

async function markRead(id) {
  try {
    const res = await alertsApi.markAsRead(id);
    if (res?.ok) {
      const target = alerts.value.find(a => a.id === id);
      if (target) target.leida = 1;
    }
  } catch (err) {
    alert(err.message);
  }
}

async function markAllRead() {
  try {
    const res = await alertsApi.markAllAsRead();
    if (res?.ok) {
      alerts.value.forEach(a => a.leida = 1);
    }
  } catch (err) {
    alert(err.message);
  }
}

async function submitRule() {
  try {
    const res = await alertsApi.createRule(newRule.value);
    if (res?.ok) {
      showRuleModal.value = false;
      newRule.value = { nombre: '', criterio_gravedad: 'High' };
      await loadAlerts();
    }
  } catch (err) {
    alert(err.message);
  }
}

onMounted(() => {
  loadAlerts();
});
</script>
