<template>
  <div class="space-y-3 max-w-full">
    <!-- Encabezado de Página Compacto -->
    <div class="flex items-center justify-between gap-2 pb-1 border-b border-sky-950/80">
      <div>
        <h1 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-1.5">
          <span class="w-1.5 h-3.5 bg-cyan-400 rounded-sm inline-block"></span>
          Motor de Alertas Internas (SOC Alarms)
        </h1>
        <span class="text-[10px] text-slate-400 font-mono">Disparo Automático ante Criticidad High/Critical</span>
      </div>

      <div class="flex items-center gap-2">
        <button 
          v-if="alerts.some(a => !a.leida)" 
          @click="markAllRead" 
          class="btn-cyber-secondary text-xs py-1 px-2.5"
        >
          Marcar Todas Leídas
        </button>

        <button 
          v-if="hasRole(['Administrador'])" 
          @click="showRuleModal = true" 
          class="btn-cyber-primary text-xs py-1 px-2.5"
        >
          + Nueva Regla
        </button>
      </div>
    </div>

    <!-- Filtros de Severidad en una Sola Fila -->
    <div class="flex items-center gap-1.5 text-xs">
      <span class="text-[11px] text-slate-400 uppercase font-semibold mr-1">Filtrar:</span>
      <button 
        @click="filterSeverity = ''" 
        :class="filterSeverity === '' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-400/50' : 'bg-[#080f1e] text-slate-400 border-sky-950'"
        class="py-0.5 px-2.5 rounded border text-[11px] transition-colors"
      >
        Todas ({{ alerts.length }})
      </button>
      <button 
        @click="filterSeverity = 'Critical'" 
        :class="filterSeverity === 'Critical' ? 'bg-rose-500/30 text-rose-300 border-rose-500/60' : 'bg-[#080f1e] text-slate-400 border-sky-950'"
        class="py-0.5 px-2.5 rounded border text-[11px] transition-colors"
      >
        Critical
      </button>
      <button 
        @click="filterSeverity = 'High'" 
        :class="filterSeverity === 'High' ? 'bg-orange-500/30 text-orange-300 border-orange-500/60' : 'bg-[#080f1e] text-slate-400 border-sky-950'"
        class="py-0.5 px-2.5 rounded border text-[11px] transition-colors"
      >
        High
      </button>
      <button 
        @click="filterSeverity = 'Medium'" 
        :class="filterSeverity === 'Medium' ? 'bg-amber-500/30 text-amber-300 border-amber-500/60' : 'bg-[#080f1e] text-slate-400 border-sky-950'"
        class="py-0.5 px-2.5 rounded border text-[11px] transition-colors"
      >
        Medium
      </button>
    </div>

    <!-- Lista Compacta de Alertas -->
    <div class="space-y-1.5">
      <div v-if="filteredAlerts.length === 0" class="cyber-card p-6 text-center text-slate-400 text-xs">
        No hay alertas que coincidan con el filtro seleccionado.
      </div>

      <div 
        v-for="al in filteredAlerts" 
        :key="al.id"
        class="cyber-card p-2.5 bg-[#0d172e] flex items-center justify-between gap-3 transition-colors"
        :class="{ 'border-rose-500/40 bg-rose-950/15': al.gravedad === 'Critical' && !al.leida, 'opacity-70': al.leida }"
      >
        <div class="flex items-center gap-2.5 truncate">
          <span :class="['severity-badge text-[9px] shrink-0', `severity-${al.gravedad.toLowerCase()}`]">
            {{ al.gravedad }}
          </span>

          <span v-if="!al.leida" class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 shrink-0">
            NUEVA
          </span>

          <span class="text-xs font-bold text-white truncate">{{ al.titulo }}</span>
          <span class="text-xs text-slate-300 hidden md:inline truncate">&bull; {{ al.mensaje }}</span>
        </div>

        <div class="flex items-center gap-2 shrink-0 text-xs">
          <span class="text-[10px] text-slate-400 font-mono hidden sm:inline">{{ al.fecha_emision }}</span>
          <button 
            v-if="!al.leida" 
            @click="markRead(al.id)" 
            class="btn-cyber-primary py-0.5 px-2 text-[10px]"
          >
            Leída
          </button>
          <span v-else class="text-[10px] text-slate-500 font-mono">&check;</span>
        </div>
      </div>
    </div>

    <!-- Reglas de Alertas Vigentes (Tabla Compacta) -->
    <div class="cyber-card p-3.5 bg-[#0d172e] mt-4">
      <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-2 flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
        </svg>
        Reglas Automáticas de Notificación
      </h3>
      <table class="w-full text-left text-[11px] text-slate-300">
        <thead class="bg-[#080f1e] text-[10px] uppercase text-slate-400 border-b border-sky-950">
          <tr>
            <th class="py-1 px-2.5">ID</th>
            <th class="py-1 px-2.5">Nombre Regla</th>
            <th class="py-1 px-2.5">Criterio Mínimo</th>
            <th class="py-1 px-2.5">Estado</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-sky-950/60 bg-[#091224]">
          <tr v-for="r in rules" :key="r.id">
            <td class="py-1 px-2.5 font-mono text-cyan-400">{{ r.id }}</td>
            <td class="py-1 px-2.5 font-semibold text-white">{{ r.nombre }}</td>
            <td class="py-1 px-2.5">
              <span :class="['severity-badge text-[9px]', `severity-${r.criterio_gravedad.toLowerCase()}`]">
                {{ r.criterio_gravedad }}
              </span>
            </td>
            <td class="py-1 px-2.5 text-emerald-400 font-semibold">&check; Activa</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- MODAL Crear Regla -->
    <div v-if="showRuleModal" class="modal-overlay" @click.self="showRuleModal = false">
      <div class="modal-content max-w-sm p-0">
        <div class="flex items-center justify-between p-3 border-b border-sky-950/80 bg-[#080f1e] rounded-t-lg">
          <h3 class="text-xs font-bold text-cyan-400 uppercase tracking-wider">Nueva Regla de Alerta</h3>
          <button @click="showRuleModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form @submit.prevent="submitRule" class="p-4 space-y-3 text-xs">
          <div>
            <label class="block font-semibold text-slate-300 uppercase mb-0.5 text-[11px]">Nombre de la Regla:</label>
            <input type="text" v-model="newRule.nombre" placeholder="Ej. Detección Ransomware" required class="cyber-input text-xs py-1"/>
          </div>
          <div>
            <label class="block font-semibold text-slate-300 uppercase mb-0.5 text-[11px]">Gravedad Mínima:</label>
            <select v-model="newRule.criterio_gravedad" class="cyber-select text-xs py-1">
              <option value="Critical">Critical</option>
              <option value="High">High</option>
              <option value="Medium">Medium</option>
            </select>
          </div>
          <div class="flex justify-end gap-2 pt-2 border-t border-sky-950/80">
            <button type="button" @click="showRuleModal = false" class="btn-cyber-secondary text-xs">Cancelar</button>
            <button type="submit" class="btn-cyber-primary text-xs">Guardar</button>
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

const newRule = ref({ nombre: '', criterio_gravedad: 'High' });

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
    if (alRes?.ok) alerts.value = alRes.data.alertas || [];
    if (ruleRes?.ok) rules.value = ruleRes.data || [];
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
