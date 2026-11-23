<template>
  <div class="space-y-3 max-w-full">
    <!-- Encabezado Compacto -->
    <div class="flex items-center justify-between gap-2 pb-1 border-b border-sky-950/80">
      <div>
        <h1 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-1.5">
          <span class="w-1.5 h-3.5 bg-cyan-400 rounded-sm inline-block"></span>
          Estándares de Calidad CMMI & MoProSoft
        </h1>
        <span class="text-[10px] text-slate-400 font-mono">Conformidad NMX-I-059-NYCE & Modelo de Calidad ISO/IEC 25010</span>
      </div>

      <span class="px-2 py-0.5 bg-purple-500/10 border border-purple-500/30 text-purple-300 text-[10px] font-mono rounded">
        CMMI L3 &bull; MoProSoft N2
      </span>
    </div>

    <!-- Comparativa CMMI y MoProSoft en 2 Columnas Compactas -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <!-- CMMI -->
      <div class="cyber-card p-3 bg-[#0d172e] border-purple-500/30">
        <div class="flex items-center justify-between mb-1.5">
          <span class="text-xs font-bold text-white flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-purple-400"></span>
            Modelo CMMI (v2.0)
          </span>
          <span class="text-[10px] font-mono text-purple-300 bg-purple-950/60 px-1.5 py-0.2 rounded border border-purple-800">
            Nivel 3: Definido
          </span>
        </div>
        <p class="text-[11px] text-slate-300 leading-normal mb-2">
          HaloMonitor implementa gestión cuantitativa de incidencias, trazabilidad ACID, congelamiento de datos de contrato y procesos estandarizados.
        </p>

        <div class="space-y-1 text-[11px]">
          <div class="p-1 px-2 rounded bg-[#080f1e] border border-sky-950 flex justify-between items-center">
            <span class="text-slate-400">Nivel 2: Gestionado (Requisitos & Sprints)</span>
            <span class="text-emerald-400 font-bold text-[10px]">&check; Cumplido</span>
          </div>
          <div class="p-1 px-2 rounded bg-purple-950/30 border border-purple-500/40 flex justify-between items-center">
            <span class="text-purple-200 font-bold">Nivel 3: Definido (Estándares & Métricas)</span>
            <span class="text-purple-300 font-bold text-[10px]">&check; Activo</span>
          </div>
        </div>
      </div>

      <!-- MoProSoft -->
      <div class="cyber-card p-3 bg-[#0d172e] border-cyan-500/30">
        <div class="flex items-center justify-between mb-1.5">
          <span class="text-xs font-bold text-white flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
            MoProSoft (México NMX-I-059)
          </span>
          <span class="text-[10px] font-mono text-cyan-300 bg-cyan-950/60 px-1.5 py-0.2 rounded border border-cyan-800">
            PyME Software
          </span>
        </div>
        <p class="text-[11px] text-slate-300 leading-normal mb-2">
          Estructura de procesos orientada a empresas mexicanas en tres niveles de responsabilidad:
        </p>

        <div class="space-y-1 text-[11px]">
          <div class="p-1 px-2 rounded bg-[#080f1e] border border-sky-950 flex justify-between items-center">
            <span class="text-cyan-300 font-medium">1. Alta Dirección (DIR):</span>
            <span class="text-slate-400 text-[10px]">EDT $90,299 MXN (10 Semanas)</span>
          </div>
          <div class="p-1 px-2 rounded bg-[#080f1e] border border-sky-950 flex justify-between items-center">
            <span class="text-sky-300 font-medium">2. Gerencia (GER):</span>
            <span class="text-slate-400 text-[10px]">Scrum, RBAC, Control de Calidad</span>
          </div>
          <div class="p-1 px-2 rounded bg-[#080f1e] border border-sky-950 flex justify-between items-center">
            <span class="text-emerald-300 font-medium">3. Operación (OPE):</span>
            <span class="text-slate-400 text-[10px]">Vue 3 + PHP PDO + MySQL</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Matriz de Pruebas ISO 25010 (PDF 1) -->
    <div class="cyber-card p-3.5 bg-[#0d172e]">
      <div class="flex items-center justify-between mb-2">
        <h2 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Matriz de Pruebas y Evidencias de Software (ISO/IEC 25010)
        </h2>
        <span class="text-[10px] font-mono text-emerald-400 bg-emerald-950/60 px-2 py-0.2 rounded border border-emerald-800">
          6/6 Superadas (100%)
        </span>
      </div>

      <table class="w-full text-left text-[11px] text-slate-300">
        <thead class="bg-[#080f1e] text-[10px] uppercase text-slate-400 border-b border-sky-950">
          <tr>
            <th class="py-1 px-2.5">ID</th>
            <th class="py-1 px-2.5">Tipo</th>
            <th class="py-1 px-2.5">Funcionalidad</th>
            <th class="py-1 px-2.5">Resultado</th>
            <th class="py-1 px-2.5 text-center">Estado</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-sky-950/60 bg-[#091224]">
          <tr v-for="t in testMatrix" :key="t.id">
            <td class="py-1 px-2.5 font-mono text-cyan-400 font-bold">{{ t.id }}</td>
            <td class="py-1 px-2.5 font-mono text-[10px] text-slate-400">{{ t.tipo }}</td>
            <td class="py-1 px-2.5 font-medium text-white">{{ t.funcionalidad }}</td>
            <td class="py-1 px-2.5 text-slate-300">{{ t.obtenido }}</td>
            <td class="py-1 px-2.5 text-center">
              <span class="px-1.5 py-0.2 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-bold">&check; Correcto</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
const testMatrix = [
  { id: 'P01', tipo: 'Seguridad', funcionalidad: 'Autenticación usuario válido', obtenido: 'Acceso concedido al Dashboard' },
  { id: 'P02', tipo: 'Seguridad', funcionalidad: 'Login credencial inválida', obtenido: "Rechazo 'Credenciales inválidas'" },
  { id: 'P03', tipo: 'Funcional', funcionalidad: 'Alta incidente con ACID', obtenido: 'Folio generado e historial inmutable' },
  { id: 'P04', tipo: 'Funcional', funcionalidad: 'Cálculo KPIs Dashboard', obtenido: 'Métricas de abiertos/críticos al día' },
  { id: 'P05', tipo: 'Validación', funcionalidad: 'Envío campos vacíos / fechas pasadas', obtenido: 'Errores HTTP 422 controlados' },
  { id: 'P06', tipo: 'Despliegue', funcionalidad: 'Levantamiento Docker Compose', obtenido: 'Frontend 8080 y Backend 8000 sincronizados' }
];
</script>
