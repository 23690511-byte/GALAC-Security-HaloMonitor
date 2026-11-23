<template>
  <div class="h-screen w-screen flex items-center justify-center p-4 bg-[#070d19] relative overflow-hidden select-none">
    <!-- Efecto Sutil de Fondo (sin desbordamiento) -->
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_35%,rgba(0,242,254,0.06),transparent_65%)] pointer-events-none"></div>

    <div class="w-full max-w-sm relative z-10">
      <!-- Tarjeta Compacta de Acceso Profesional (Mockup Figura 2.1) -->
      <div class="cyber-card p-6 border-cyan-500/30 bg-[#0c1527]/95 shadow-[0_0_35px_rgba(0,0,0,0.8)]">
        <!-- Emblema Corporativo GALAC Security Compacto -->
        <div class="text-center mb-5">
          <div class="inline-flex items-center justify-center w-14 h-14 mb-2 rounded-xl bg-gradient-to-br from-[#0c162b] to-[#16274a] border border-cyan-400/60 shadow-[0_0_15px_rgba(0,242,254,0.3)]">
            <svg class="w-8 h-8 text-cyan-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
              <rect x="9" y="11" width="6" height="5" rx="1" fill="#00f2fe" fill-opacity="0.3" stroke="currentColor" stroke-width="1.5"/>
              <path d="M10 11V9a2 2 0 0 1 4 0v2" stroke="currentColor" stroke-width="1.5"/>
            </svg>
          </div>
          <h1 class="text-lg font-black tracking-widest text-white uppercase leading-none">GALAC</h1>
          <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-cyan-400 block mt-0.5">SECURITY</span>
          <span class="text-[10px] text-slate-400 font-mono block mt-1">HaloMonitor &bull; Consola de Ciberseguridad</span>
        </div>

        <!-- Alerta de Error Compacta -->
        <div v-if="errorMessage" class="mb-3 p-2 rounded bg-rose-500/15 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
          <span class="truncate">{{ errorMessage }}</span>
        </div>

        <!-- Formulario Compacto -->
        <form @submit.prevent="handleSubmit" class="space-y-3">
          <div>
            <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1">
              Usuario / Correo:
            </label>
            <input 
              type="email" 
              v-model="email" 
              placeholder="admin@halomonitor.com" 
              required
              class="cyber-input text-xs py-1.5 font-mono"
            />
          </div>

          <div>
            <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1">
              Contraseña:
            </label>
            <input 
              type="password" 
              v-model="password" 
              placeholder="••••••••••••" 
              required
              class="cyber-input text-xs py-1.5 font-mono"
            />
          </div>

          <button 
            type="submit" 
            :disabled="isLoading"
            class="w-full btn-cyber-primary py-2 text-xs tracking-wider uppercase font-bold mt-1"
          >
            <span v-if="isLoading" class="animate-spin mr-1">&#9696;</span>
            {{ isLoading ? 'Verificando...' : 'Iniciar Sesión' }}
          </button>
        </form>

        <!-- Accesos Directos de 1 Clic por Rol (Compacto) -->
        <div class="mt-4 pt-3 border-t border-slate-800">
          <span class="text-[10px] text-slate-400 block text-center uppercase tracking-wider mb-2 font-mono">
            Acceso Rápido Demo:
          </span>
          <div class="grid grid-cols-3 gap-1.5">
            <button 
              type="button" 
              @click="setCredentials('admin@halomonitor.com', 'Admin123!')"
              class="py-1 px-1 bg-purple-950/30 hover:bg-purple-900/50 border border-purple-500/40 rounded text-[10px] text-purple-300 font-semibold transition-all text-center"
            >
              Admin
            </button>
            <button 
              type="button" 
              @click="setCredentials('analista@halomonitor.com', 'Analista123!')"
              class="py-1 px-1 bg-cyan-950/30 hover:bg-cyan-900/50 border border-cyan-500/40 rounded text-[10px] text-cyan-300 font-semibold transition-all text-center"
            >
              Analista
            </button>
            <button 
              type="button" 
              @click="setCredentials('auditor@halomonitor.com', 'Auditor123!')"
              class="py-1 px-1 bg-amber-950/30 hover:bg-amber-900/50 border border-amber-500/40 rounded text-[10px] text-amber-300 font-semibold transition-all text-center"
            >
              Auditor
            </button>
          </div>
        </div>
      </div>

      <!-- Pie Académico Discreto -->
      <div class="text-center mt-2.5 text-[10px] text-slate-500 font-mono">
        GALAC Security &bull; IT Ciudad Valles
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuth } from '../composables/useAuth';

const router = useRouter();
const route = useRoute();
const { login, isLoading } = useAuth();

const email = ref('admin@halomonitor.com');
const password = ref('Admin123!');
const errorMessage = ref('');

function setCredentials(e, p) {
  email.value = e;
  password.value = p;
  errorMessage.value = '';
}

async function handleSubmit() {
  errorMessage.value = '';
  try {
    await login(email.value, password.value);
    const redirect = route.query.redirect || '/dashboard';
    router.push(redirect);
  } catch (err) {
    errorMessage.value = err.message || 'Credenciales inválidas.';
  }
}
</script>
