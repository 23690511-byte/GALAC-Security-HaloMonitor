<template>
  <div class="min-h-screen flex items-center justify-center p-4 bg-[#070d19] relative overflow-hidden">
    <!-- Efectos de Fondo Cibernético -->
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_30%,rgba(0,242,254,0.08),transparent_70%)] pointer-events-none"></div>
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-cyan-600/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
      <!-- Tarjeta Principal con Diseño del Boceto Oficial (Figura 2.1) -->
      <div class="cyber-card p-8 border-cyan-500/30 shadow-[0_0_50px_rgba(0,0,0,0.8)] backdrop-blur-xl">
        <!-- Logotipo Corporativo GALAC Security -->
        <div class="text-center mb-8">
          <div class="inline-flex items-center justify-center w-24 h-24 mb-4 rounded-2xl bg-gradient-to-br from-[#0c1527] to-[#132238] border-2 border-cyan-400/60 shadow-[0_0_25px_rgba(0,242,254,0.35)] glow-pulse">
            <!-- Emblema de Seguridad: Escudo + Candado + Trazas Cibernéticas -->
            <svg class="w-14 h-14 text-cyan-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
              <rect x="9" y="11" width="6" height="5" rx="1" fill="#00f2fe" fill-opacity="0.3" stroke="currentColor" stroke-width="1.5"/>
              <path d="M10 11V9a2 2 0 0 1 4 0v2" stroke="currentColor" stroke-width="1.5"/>
              <!-- Trazas de circuito -->
              <circle cx="5" cy="8" r="1" fill="#00f2fe" />
              <circle cx="19" cy="8" r="1" fill="#00f2fe" />
            </svg>
          </div>
          <h1 class="text-2xl font-black tracking-widest text-white uppercase">GALAC</h1>
          <span class="text-xs font-bold uppercase tracking-[0.3em] text-cyan-400 block -mt-1">SECURITY</span>
          <p class="text-xs text-slate-400 mt-2 font-mono">HaloMonitor v3.0 &bull; Acceso Seguro</p>
        </div>

        <!-- Mensaje de Error -->
        <div v-if="errorMessage" class="mb-5 p-3 rounded-lg bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs flex items-center gap-2">
          <svg class="w-4 h-4 shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
          <span>{{ errorMessage }}</span>
        </div>

        <!-- Formulario de Autenticación -->
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">
              Usuario / Correo:
            </label>
            <input 
              type="email" 
              v-model="email" 
              placeholder="admin@halomonitor.com" 
              required
              class="cyber-input font-mono"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">
              Contraseña:
            </label>
            <input 
              type="password" 
              v-model="password" 
              placeholder="••••••••••••" 
              required
              class="cyber-input font-mono"
            />
          </div>

          <button 
            type="submit" 
            :disabled="isLoading"
            class="w-full btn-cyber-primary py-2.5 mt-2 text-sm tracking-wider uppercase font-bold"
          >
            <span v-if="isLoading" class="animate-spin mr-2">&#9696;</span>
            {{ isLoading ? 'Verificando Credenciales...' : 'Iniciar Sesión' }}
          </button>
        </form>

        <!-- Accesos Rápidos de Prueba (Demo Roles) -->
        <div class="mt-8 pt-5 border-t border-slate-800/80">
          <p class="text-[11px] text-slate-400 text-center uppercase tracking-wider mb-3">
            Acceso Rápido por Rol (Demostración):
          </p>
          <div class="grid grid-cols-3 gap-2">
            <button 
              type="button" 
              @click="setCredentials('admin@halomonitor.com', 'Admin123!')"
              class="py-1.5 px-2 bg-slate-900/80 hover:bg-purple-950/40 border border-purple-500/30 rounded text-[11px] text-purple-300 transition-colors text-center font-medium"
            >
              Admin
            </button>
            <button 
              type="button" 
              @click="setCredentials('analista@halomonitor.com', 'Analista123!')"
              class="py-1.5 px-2 bg-slate-900/80 hover:bg-cyan-950/40 border border-cyan-500/30 rounded text-[11px] text-cyan-300 transition-colors text-center font-medium"
            >
              Analista
            </button>
            <button 
              type="button" 
              @click="setCredentials('auditor@halomonitor.com', 'Auditor123!')"
              class="py-1.5 px-2 bg-slate-900/80 hover:bg-amber-950/40 border border-amber-500/30 rounded text-[11px] text-amber-300 transition-colors text-center font-medium"
            >
              Auditor
            </button>
          </div>
        </div>
      </div>

      <!-- Footer Académico con Integrantes del PDF 1 -->
      <div class="text-center mt-4 text-[11px] text-slate-500 font-mono">
        GALAC Security &bull; IT Ciudad Valles &bull; 7mo Semestre
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
    errorMessage.value = err.message || 'Error al iniciar sesión. Compruebe credenciales.';
  }
}
</script>
