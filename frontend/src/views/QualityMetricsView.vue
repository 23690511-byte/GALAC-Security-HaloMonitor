<template>
  <div class="space-y-6">
    <!-- Encabezado de Página -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-black text-white flex items-center gap-2">
          <span class="w-2.5 h-6 bg-cyan-400 rounded-sm inline-block"></span>
          Estándares y Métricas de Calidad del Software
        </h1>
        <p class="text-xs text-slate-400 mt-1 font-mono">
          Implementación CMMI &bull; Modelo MoProSoft (NMX-I-059-NYCE) &bull; Aseguramiento ISO/IEC 25010
        </p>
      </div>

      <div class="flex items-center gap-2">
        <span class="px-3 py-1 bg-purple-500/10 border border-purple-500/30 text-purple-300 text-xs font-mono rounded-full">
          CMMI Level 3 &bull; MoProSoft Nivel 2
        </span>
      </div>
    </div>

    <!-- Comparativa CMMI vs MoProSoft (Documento PDF 3) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Tarjeta CMMI -->
      <div class="cyber-card p-6 border-purple-500/30">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-base font-bold text-white flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-purple-400"></span>
            Modelo CMMI (Capability Maturity Model)
          </h2>
          <span class="text-xs font-mono text-purple-400 bg-purple-950/60 px-2 py-0.5 rounded border border-purple-800">
            Nivel 3: Definido
          </span>
        </div>
        <p class="text-xs text-slate-300 leading-relaxed">
          Marco de mejores prácticas internacionales para evaluar la capacidad y madurez de procesos. HaloMonitor aplica control cuantitativo de defectos, planificación de sprints de 10 semanas y gestión de configuración.
        </p>

        <!-- Niveles de Madurez CMMI -->
        <div class="mt-4 space-y-1.5 text-xs">
          <div class="p-2 rounded bg-slate-900/60 border border-slate-800 flex justify-between items-center">
            <span class="text-slate-400">Nivel 1: Inicial (Reactivo)</span>
            <span class="text-slate-500 text-[10px]">Superado</span>
          </div>
          <div class="p-2 rounded bg-slate-900/60 border border-slate-800 flex justify-between items-center">
            <span class="text-slate-300">Nivel 2: Gestionado (Planificación y Requisitos)</span>
            <span class="text-emerald-400 text-[10px] font-bold">&check; 100% Implementado</span>
          </div>
          <div class="p-2 rounded bg-purple-950/40 border border-purple-500/40 flex justify-between items-center">
            <span class="text-purple-300 font-bold">Nivel 3: Definido (Procesos Estándar y Métricas)</span>
            <span class="text-purple-400 text-[10px] font-bold">&check; Activo en HaloMonitor</span>
          </div>
          <div class="p-2 rounded bg-slate-900/60 border border-slate-800 flex justify-between items-center opacity-60">
            <span class="text-slate-400">Nivel 4: Cuantitativo &bull; Nivel 5: Optimización</span>
            <span class="text-slate-500 text-[10px]">Fase Futura SaaS</span>
          </div>
        </div>
      </div>

      <!-- Tarjeta MoProSoft -->
      <div class="cyber-card p-6 border-cyan-500/30">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-base font-bold text-white flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
            Modelo MoProSoft (Industria Mexicana)
          </h2>
          <span class="text-xs font-mono text-cyan-400 bg-cyan-950/60 px-2 py-0.5 rounded border border-cyan-800">
            NMX-I-059-NYCE
          </span>
        </div>
        <p class="text-xs text-slate-300 leading-relaxed">
          Estándar nacional adaptado a PyMEs desarrollado por la Secretaría de Economía. Organiza los procesos en tres categorías de responsabilidad: Alta Dirección, Gerencia y Operación.
        </p>

        <!-- Categorías de Procesos MoProSoft -->
        <div class="mt-4 space-y-2 text-xs">
          <div class="p-2.5 rounded bg-slate-900/60 border border-cyan-500/20">
            <div class="flex justify-between items-center font-bold text-cyan-300 mb-1">
              <span>1. Alta Dirección (DIR)</span>
              <span class="text-slate-400 text-[10px]">Planificación Estratégica</span>
            </div>
            <p class="text-[11px] text-slate-400">
              Presupuesto EDT ($90,299 MXN) y delimitación de alcance institucional por GALAC Security.
            </p>
          </div>

          <div class="p-2.5 rounded bg-slate-900/60 border border-cyan-500/20">
            <div class="flex justify-between items-center font-bold text-blue-300 mb-1">
              <span>2. Gerencia (GER)</span>
              <span class="text-slate-400 text-[10px]">Gestión de Procesos y Proyectos</span>
            </div>
            <p class="text-[11px] text-slate-400">
              Metodología Scrum en 6 Sprints, roles asignados y control de riesgos de software.
            </p>
          </div>

          <div class="p-2.5 rounded bg-slate-900/60 border border-cyan-500/20">
            <div class="flex justify-between items-center font-bold text-emerald-300 mb-1">
              <span>3. Operación (OPE)</span>
              <span class="text-slate-400 text-[10px]">Desarrollo y Mantenimiento</span>
            </div>
            <p class="text-[11px] text-slate-400">
              Desarrollo Full Stack (Vue 3 + PHP PDO + MySQL) y trazabilidad inmutable con auditoría SQL.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Matriz de Pruebas Unitarias y de Calidad (PDF 1 - Sección 8.2) -->
    <div class="cyber-card p-6">
      <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-800">
        <div>
          <h2 class="text-base font-bold text-white flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Matriz de Verificación y Pruebas del Sistema (ISO/IEC 25010)
          </h2>
          <p class="text-xs text-slate-400">Evidencias documentadas del proyecto final de software</p>
        </div>
        <span class="text-xs font-mono text-emerald-400 bg-emerald-950/60 px-2.5 py-1 rounded border border-emerald-800">
          6/6 Pruebas Superadas (100%)
        </span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-300">
          <thead class="bg-slate-950/80 uppercase text-slate-400 font-semibold border-b border-slate-800">
            <tr>
              <th class="p-3">ID</th>
              <th class="p-3">Tipo</th>
              <th class="p-3">Funcionalidad Evaluada</th>
              <th class="p-3">Resultado Esperado</th>
              <th class="p-3">Resultado Obtenido</th>
              <th class="p-3 text-center">Estado</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800">
            <tr v-for="test in testMatrix" :key="test.id" class="hover:bg-slate-900/40">
              <td class="p-3 font-mono text-cyan-400 font-bold">{{ test.id }}</td>
              <td class="p-3">
                <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700 font-mono text-[11px]">
                  {{ test.tipo }}
                </span>
              </td>
              <td class="p-3 font-semibold text-white">{{ test.funcionalidad }}</td>
              <td class="p-3 text-slate-400">{{ test.esperado }}</td>
              <td class="p-3 text-cyan-300">{{ test.obtenido }}</td>
              <td class="p-3 text-center">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-bold border border-emerald-500/40 text-[11px]">
                  &check; Correcto
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Ficha Técnica del Proyecto (Equipo GALAC Security) -->
    <div class="cyber-card p-5 border-slate-700/60 text-xs text-slate-400 space-y-3">
      <h3 class="text-sm font-bold text-white uppercase tracking-wider text-cyan-400">
        Información del Proyecto y Equipo de Desarrollo
      </h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pt-1">
        <div>
          <span class="text-slate-500 block">Institución:</span>
          <span class="text-slate-200 font-medium">Instituto Tecnológico de Ciudad Valles</span>
        </div>
        <div>
          <span class="text-slate-500 block">Asignatura:</span>
          <span class="text-slate-200 font-medium">Desarrollo de Aplicaciones Web</span>
        </div>
        <div>
          <span class="text-slate-500 block">Docente Titular:</span>
          <span class="text-slate-200 font-medium">Dr. Nitgard Zapata Garay</span>
        </div>
        <div>
          <span class="text-slate-500 block">Presupuesto Estimado:</span>
          <span class="text-cyan-400 font-mono font-bold">$90,299.00 MXN (10 Semanas)</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const testMatrix = [
  {
    id: 'P01',
    tipo: 'Seguridad',
    funcionalidad: 'Autenticación con usuario válido',
    esperado: 'Inicio exitoso y redirección',
    obtenido: 'Acceso concedido al Dashboard'
  },
  {
    id: 'P02',
    tipo: 'Seguridad',
    funcionalidad: 'Login con contraseña inválida',
    esperado: 'Rechazo y mensaje de error',
    obtenido: "Mensaje 'Credenciales inválidas'"
  },
  {
    id: 'P03',
    tipo: 'Funcional',
    funcionalidad: 'Alta y persistencia de incidente',
    esperado: 'Registro almacenado en BD',
    obtenido: 'Incidente visible en tabla y folio generado'
  },
  {
    id: 'P04',
    tipo: 'Funcional',
    funcionalidad: 'Cálculo de KPIs en Dashboard',
    esperado: 'Contadores actualizados',
    obtenido: 'Métricas de abiertos/críticos al día'
  },
  {
    id: 'P05',
    tipo: 'Validación',
    funcionalidad: 'Envío con campos vacíos',
    esperado: 'Errores de validación visibles (HTTP 422)',
    obtenido: 'No se inserta registro inválido'
  },
  {
    id: 'P06',
    tipo: 'Despliegue',
    funcionalidad: 'Levantamiento con Docker Compose',
    esperado: 'Stack sincronizado en puertos 8080 y 8000',
    obtenido: 'Frontend, Backend y BD activos'
  }
];
</script>
