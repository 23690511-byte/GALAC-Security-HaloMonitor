<template>
  <div class="login-wrapper">
    <!-- PANEL IZQUIERDO: Visual & Branding (Idéntico a Imagen de Referencia) -->
    <div class="visual-panel">
      <div class="visual-container">
        <!-- Encabezado de Marca HaloMonitor -->
        <div class="brand-top-row">
          <div class="halo-logo-badge">
            <svg class="halo-logo-svg" viewBox="0 0 24 24" fill="none">
              <path d="M12 2L4 6v6c0 5.55 3.84 10.74 8 12 4.16-1.26 8-6.45 8-12V6l-8-4z" stroke="#38bdf8" stroke-width="2" fill="rgba(56, 189, 248, 0.12)" stroke-linejoin="round"/>
              <rect x="9" y="11" width="6" height="5" rx="1.5" fill="#38bdf8"/>
              <path d="M10 11V9a2 2 0 114 0v2" stroke="#38bdf8" stroke-width="1.6"/>
            </svg>
            <span class="halo-brand-text">HaloMonitor</span>
          </div>
          
          <h1 class="visual-main-title">
            Gestión Transaccional<br />de Ciberseguridad
          </h1>
          <p class="visual-main-subtitle">
            Monitoreo, registro, alertas y trazabilidad de incidentes en tiempo real para PyMEs
          </p>
        </div>

        <!-- Ilustración Central 3D Isométrica con Badges Flotantes (Idéntico a Imagen) -->
        <div class="visual-graphics-area">
          <!-- Gráfico Isométrico: Nube + Candado + Servidores -->
          <div class="isometric-scene">
            <!-- Nube con Candado Luminoso -->
            <div class="cloud-shield-unit">
              <div class="cloud-shape">
                <svg class="cloud-svg" viewBox="0 0 100 70" fill="none">
                  <path d="M25 55h52a18 18 0 005-35.3A22 22 0 0040 18a17.9 17.9 0 00-18 17 18 18 0 003 20z" fill="url(#cloudGrad)" stroke="#38bdf8" stroke-width="1.5" />
                  <defs>
                    <linearGradient id="cloudGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                      <stop offset="0%" stop-color="#1e3a8a" stop-opacity="0.9" />
                      <stop offset="100%" stop-color="#0284c7" stop-opacity="0.8" />
                    </linearGradient>
                  </defs>
                </svg>
                <!-- Candado resplandeciente en la nube -->
                <div class="glowing-cloud-lock">
                  <svg viewBox="0 0 24 24" fill="none" class="lock-shield-svg">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" fill="#0284c7" stroke="#38bdf8" stroke-width="1.8"/>
                    <rect x="9" y="11" width="6" height="5" rx="1" fill="#ffffff" />
                    <path d="M10 11V9a2 2 0 014 0v2" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"/>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Racks de Servidores Isométricos -->
            <div class="servers-isometric-stack">
              <div class="isometric-rack rack-1">
                <div class="rack-top"></div>
                <div class="rack-front">
                  <div class="rack-slot"><span class="led led-blue"></span><span class="slot-bar"></span></div>
                  <div class="rack-slot"><span class="led led-cyan"></span><span class="slot-bar"></span></div>
                  <div class="rack-slot"><span class="led led-green"></span><span class="slot-bar"></span></div>
                </div>
              </div>

              <div class="isometric-rack rack-2">
                <div class="rack-top"></div>
                <div class="rack-front">
                  <div class="rack-slot"><span class="led led-cyan"></span><span class="slot-bar"></span></div>
                  <div class="rack-slot"><span class="led led-green"></span><span class="slot-bar"></span></div>
                  <div class="rack-slot"><span class="led led-blue"></span><span class="slot-bar"></span></div>
                </div>
              </div>

              <div class="isometric-rack rack-3">
                <div class="rack-top"></div>
                <div class="rack-front">
                  <div class="rack-slot"><span class="led led-green"></span><span class="slot-bar"></span></div>
                  <div class="rack-slot"><span class="led led-blue"></span><span class="slot-bar"></span></div>
                  <div class="rack-slot"><span class="led led-cyan"></span><span class="slot-bar"></span></div>
                </div>
              </div>
            </div>

            <!-- Trazas de Circuitos que conectan con los Badges -->
            <svg class="circuit-traces-svg" viewBox="0 0 160 160" fill="none">
              <path d="M20 50 H70 V25 H150" stroke="#38bdf8" stroke-width="1.5" stroke-dasharray="2 3" opacity="0.6"/>
              <path d="M30 80 H85 V80 H150" stroke="#38bdf8" stroke-width="1.5" opacity="0.75"/>
              <path d="M20 110 H70 V135 H150" stroke="#38bdf8" stroke-width="1.5" stroke-dasharray="2 3" opacity="0.6"/>
              <circle cx="150" cy="25" r="3" fill="#fbbf24"/>
              <circle cx="150" cy="80" r="3" fill="#f87171"/>
              <circle cx="150" cy="135" r="3" fill="#34d399"/>
            </svg>
          </div>

          <!-- Badges Flotantes de Estado (Idénticos a la Imagen de Referencia) -->
          <div class="reference-floating-cards">
            <!-- 1. EVENTO DETECTADO (Ámbar / Dorado) -->
            <div class="ref-floating-card card-amber">
              <div class="ref-pill pill-evento">
                <span class="ref-dot dot-yellow"></span>
                <span>EVENTO DETECTADO</span>
              </div>
              <div class="ref-lines">
                <div class="ref-line line-long"></div>
                <div class="ref-line line-mid"></div>
              </div>
            </div>

            <!-- 2. INCIDENTE CRÍTICO (Rojo / Carmesí) -->
            <div class="ref-floating-card card-red">
              <div class="ref-pill pill-critico">
                <span class="ref-dot dot-red"></span>
                <span>INCIDENTE CRÍTICO</span>
              </div>
              <div class="ref-lines">
                <div class="ref-line line-long"></div>
                <div class="ref-line line-mid"></div>
              </div>
            </div>

            <!-- 3. SISTEMA SEGURO (Verde / Esmeralda) -->
            <div class="ref-floating-card card-green">
              <div class="ref-pill pill-seguro">
                <span class="ref-dot dot-green"></span>
                <span>SISTEMA SEGURO</span>
              </div>
              <div class="ref-lines">
                <div class="ref-line line-long"></div>
                <div class="ref-line line-short"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pie Institucional -->
        <div class="visual-bottom-meta">
          <span>Dr. Nitgard Zapata Garay &bull; IT Ciudad Valles &bull; 7mo Semestre</span>
        </div>
      </div>
    </div>

    <!-- PANEL DERECHO: Tarjeta de Iniciar Sesión (Idéntica a Imagen de Referencia) -->
    <div class="form-panel">
      <div class="login-glass-card">
        <!-- Logotipo Centrado de GALAC SECURITY (Escudo Padlock + Circuitos) -->
        <div class="galac-logo-block">
          <div class="galac-emblem-wrap">
            <svg class="galac-emblem-svg" viewBox="0 0 100 100" fill="none">
              <!-- Órbita de Electrón / Nodo -->
              <ellipse cx="50" cy="50" rx="42" ry="20" stroke="#00f2fe" stroke-width="1.8" transform="rotate(-28 50 50)" opacity="0.8"/>
              <circle cx="86" cy="34" r="3.5" fill="#00f2fe" filter="drop-shadow(0 0 5px #00f2fe)"/>
              
              <!-- Trazas de Circuito Izquierdas -->
              <path d="M12 42 H28 L35 48" stroke="#38bdf8" stroke-width="2" stroke-linecap="round"/>
              <circle cx="12" cy="42" r="2.5" fill="#38bdf8"/>
              <path d="M15 56 H30 L38 52" stroke="#38bdf8" stroke-width="2" stroke-linecap="round"/>
              <circle cx="15" cy="56" r="2.5" fill="#38bdf8"/>
              <path d="M18 70 H34 L42 64" stroke="#38bdf8" stroke-width="2" stroke-linecap="round"/>
              <circle cx="18" cy="70" r="2.5" fill="#38bdf8"/>

              <!-- Escudo Principal -->
              <path d="M50 16 L74 27 V52 C74 68 62 81 50 86 C38 81 26 68 26 52 V27 L50 16 Z" fill="url(#shieldGrad)" stroke="#38bdf8" stroke-width="2.5" stroke-linejoin="round"/>
              <defs>
                <linearGradient id="shieldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#0369a1" />
                  <stop offset="50%" stop-color="#0f172a" />
                  <stop offset="100%" stop-color="#0284c7" />
                </linearGradient>
              </defs>

              <!-- Arco de candado -->
              <path d="M42 45 V37 A8 8 0 0 1 58 37 V45" stroke="#38bdf8" stroke-width="3" stroke-linecap="round"/>
              <circle cx="50" cy="33" r="2" fill="#ffffff" filter="drop-shadow(0 0 4px #ffffff)"/>
              
              <!-- Cuerpo del candado -->
              <rect x="38" y="44" width="24" height="20" rx="3.5" fill="#082f49" stroke="#38bdf8" stroke-width="2"/>
              <!-- Ojo de cerradura -->
              <circle cx="50" cy="52" r="2.5" fill="#ffffff"/>
              <path d="M48.5 52 L47.5 60 H52.5 L51.5 52 Z" fill="#ffffff"/>
            </svg>
          </div>
          <div class="galac-wordmark">
            <span class="galac-title">GALAC</span>
            <span class="galac-subtitle">SECURITY</span>
          </div>
        </div>

        <!-- Encabezado de la Tarjeta -->
        <div class="card-headings-group">
          <h2 class="card-main-title">Iniciar sesión</h2>
          <p class="card-main-subtitle">Ingresa tus credenciales para acceder a la plataforma</p>
        </div>

        <!-- Alerta de Error -->
        <div v-if="errorMessage" class="error-alert-banner">
          <span>{{ errorMessage }}</span>
        </div>

        <!-- Formulario de Autenticación -->
        <form @submit.prevent="handleSubmit" class="login-inputs-form">
          <!-- Campo 1: Correo electrónico -->
          <div class="input-form-field">
            <label class="field-label">Correo electrónico</label>
            <div class="field-input-wrapper">
              <svg class="field-left-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              <input 
                type="email" 
                v-model="email" 
                placeholder="analista@halomonitor.com" 
                required 
                class="field-native-input"
              />
            </div>
          </div>

          <!-- Campo 2: Contraseña -->
          <div class="input-form-field">
            <label class="field-label">Contraseña</label>
            <div class="field-input-wrapper">
              <svg class="field-left-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke-width="1.8"/>
                <path d="M7 11V7a5 5 0 0110 0v4" stroke-width="1.8"/>
              </svg>
              <input 
                :type="showPassword ? 'text' : 'password'" 
                v-model="password" 
                placeholder="••••••••" 
                required 
                class="field-native-input"
              />
              <button type="button" @click="showPassword = !showPassword" class="eye-reveal-btn" title="Mostrar/Ocultar contraseña">
                <svg v-if="!showPassword" class="eye-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg v-else class="eye-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Fila de Recordar Sesión -->
          <div class="remember-row">
            <label class="remember-label">
              <input type="checkbox" v-model="rememberMe" class="remember-checkbox" />
              <span>Recordar sesión</span>
            </label>
          </div>

          <!-- Botón de Entrar al sistema (Idéntico a Imagen de Referencia) -->
          <button type="submit" :disabled="isLoading" class="login-action-btn">
            <span v-if="isLoading">Verificando credenciales...</span>
            <span v-else class="btn-inner-content">
              <span>Entrar al sistema</span>
              <svg class="btn-arrow-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
              </svg>
            </span>
          </button>
        </form>

        <!-- Accesos Demo (Idéntico a la segunda imagen de referencia + Cliente) -->
        <div class="demo-logins-block">
          <span class="demo-legend-title">Ingresar como usuario demo:</span>
          <div class="demo-pills-row">
            <!-- Administrador (Púrpura) -->
            <button 
              type="button" 
              @click="setRole('admin@halomonitor.com', 'Admin123!')" 
              class="role-pill-btn pill-admin"
              title="Acceso total como Administrador"
            >
              Administrador
            </button>

            <!-- Analista (Azul) -->
            <button 
              type="button" 
              @click="setRole('analista@halomonitor.com', 'Analista123!')" 
              class="role-pill-btn pill-analista"
              title="Acceso operativo como Analista SOC"
            >
              Analista
            </button>

            <!-- Auditor (Ámbar) -->
            <button 
              type="button" 
              @click="setRole('auditor@halomonitor.com', 'Auditor123!')" 
              class="role-pill-btn pill-auditor"
              title="Acceso solo lectura como Auditor"
            >
              Auditor
            </button>

            <!-- Cliente (Verde) -->
            <button 
              type="button" 
              @click="setRole('cliente@pyme.com', 'Cliente123!')" 
              class="role-pill-btn pill-cliente"
              title="Acceso cliente PyME: reporte de incidentes propios"
            >
              Cliente
            </button>
          </div>
        </div>
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

const email = ref('analista@halomonitor.com');
const password = ref('Analista123!');
const rememberMe = ref(true);
const showPassword = ref(false);
const errorMessage = ref('');

function setRole(userEmail, userPass) {
  email.value = userEmail;
  password.value = userPass;
  errorMessage.value = '';
}

async function handleSubmit() {
  errorMessage.value = '';
  try {
    const res = await login(email.value, password.value);
    const userRole = res?.user?.rol || '';
    
    // Enrutamiento realista según el rol:
    if (userRole === 'Cliente') {
      router.push('/incidents');
    } else {
      const redirect = route.query.redirect || '/dashboard';
      router.push(redirect);
    }
  } catch (err) {
    errorMessage.value = err.message || 'Credenciales inválidas. Verifique su correo o contraseña.';
  }
}
</script>

<style scoped>
.login-wrapper {
  display: flex;
  width: 100vw;
  height: 100vh;
  overflow: hidden;
  background-color: #060b17;
  font-family: var(--font-main);
}

/* =====================================================================
   PANEL IZQUIERDO: Visual & Branding (Idéntico a imagen de referencia)
   ===================================================================== */
.visual-panel {
  flex: 1.15;
  background: radial-gradient(circle at 20% 30%, #0d1e40 0%, #071026 60%, #050b18 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 3.5rem;
  position: relative;
  overflow: hidden;
  border-right: 1px solid rgba(56, 189, 248, 0.12);
}

.visual-container {
  max-width: 540px;
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 2.2rem;
}

.brand-top-row {
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
}

.halo-logo-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.55rem;
}

.halo-logo-svg {
  width: 26px !important;
  height: 26px !important;
  max-width: 26px !important;
  max-height: 26px !important;
}

.halo-brand-text {
  font-size: 1.25rem;
  font-weight: 800;
  color: #ffffff;
  letter-spacing: -0.01em;
}

.visual-main-title {
  font-size: 2.35rem;
  font-weight: 800;
  line-height: 1.15;
  color: #ffffff;
  letter-spacing: -0.02em;
}

.visual-main-subtitle {
  font-size: 0.95rem;
  color: #94a3b8;
  line-height: 1.5;
  font-weight: 400;
  max-width: 460px;
}

/* Ilustración Isométrica Central */
.visual-graphics-area {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1.5rem;
  min-height: 220px;
  position: relative;
}

.isometric-scene {
  flex: 1;
  position: relative;
  height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.cloud-shield-unit {
  position: absolute;
  top: 10px;
  left: 20px;
  z-index: 5;
}

.cloud-shape {
  position: relative;
  width: 90px;
  height: 60px;
}

.cloud-svg {
  width: 90px !important;
  height: 60px !important;
  filter: drop-shadow(0 8px 20px rgba(2, 132, 199, 0.4));
}

.glowing-cloud-lock {
  position: absolute;
  top: 15px;
  left: 30px;
}

.lock-shield-svg {
  width: 28px !important;
  height: 28px !important;
  filter: drop-shadow(0 0 8px #38bdf8);
}

.servers-isometric-stack {
  position: absolute;
  bottom: 10px;
  left: 30px;
  display: flex;
  gap: 8px;
  z-index: 4;
}

.isometric-rack {
  width: 38px;
  background: #0f172a;
  border: 1px solid #38bdf8;
  border-radius: 6px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.7);
  display: flex;
  flex-direction: column;
}

.rack-1 { height: 75px; }
.rack-2 { height: 95px; }
.rack-3 { height: 65px; }

.rack-top {
  height: 6px;
  background: #0284c7;
  border-radius: 5px 5px 0 0;
}

.rack-front {
  padding: 4px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.rack-slot {
  height: 12px;
  background: #1e293b;
  border-radius: 2px;
  display: flex;
  align-items: center;
  gap: 3px;
  padding: 0 3px;
}

.slot-bar {
  flex: 1;
  height: 2px;
  background: #334155;
  border-radius: 1px;
}

.led {
  width: 4px;
  height: 4px;
  border-radius: 50%;
}

.led-blue { background: #38bdf8; box-shadow: 0 0 4px #38bdf8; }
.led-cyan { background: #00f2fe; box-shadow: 0 0 4px #00f2fe; }
.led-green { background: #10b981; box-shadow: 0 0 4px #10b981; }

.circuit-traces-svg {
  position: absolute;
  top: 20px;
  right: -10px;
  width: 140px !important;
  height: 160px !important;
  pointer-events: none;
}

/* Badges Flotantes de Estado */
.reference-floating-cards {
  flex: 1.25;
  display: flex;
  flex-direction: column;
  gap: 0.85rem;
  z-index: 6;
}

.ref-floating-card {
  background: rgba(15, 23, 42, 0.65);
  backdrop-filter: blur(14px);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 10px;
  padding: 0.65rem 0.85rem;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  transition: all 0.2s ease;
}

.ref-floating-card:hover {
  transform: translateX(3px);
  border-color: rgba(56, 189, 248, 0.4);
}

.card-amber { border-left: 3px solid #fbbf24; }
.card-red { border-left: 3px solid #f87171; }
.card-green { border-left: 3px solid #34d399; }

.ref-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.675rem;
  font-weight: 800;
  letter-spacing: 0.05em;
  padding: 0.25rem 0.6rem;
  border-radius: 9999px;
  text-transform: uppercase;
}

.pill-evento { background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); }
.pill-critico { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.4); }
.pill-seguro { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); }

.ref-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
}

.dot-yellow { background: #fbbf24; box-shadow: 0 0 6px #fbbf24; }
.dot-red { background: #f87171; box-shadow: 0 0 6px #f87171; }
.dot-green { background: #34d399; box-shadow: 0 0 6px #34d399; }

.ref-lines {
  display: flex;
  flex-direction: column;
  gap: 3px;
  align-items: flex-end;
}

.ref-line {
  height: 4px;
  background: rgba(148, 163, 184, 0.25);
  border-radius: 2px;
}

.line-long { width: 55px; }
.line-mid { width: 40px; }
.line-short { width: 25px; }

.visual-bottom-meta {
  font-size: 0.725rem;
  color: #64748b;
  font-family: var(--font-mono);
}

/* =====================================================================
   PANEL DERECHO: Formulario de Login (Idéntico a imagen de referencia)
   ===================================================================== */
.form-panel {
  flex: 0.85;
  background-color: #060b17;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2.5rem;
}

.login-glass-card {
  width: 100%;
  max-width: 410px;
  background: rgba(12, 21, 39, 0.88);
  border: 1px solid rgba(56, 189, 248, 0.22);
  border-radius: 16px;
  padding: 2.25rem 2rem;
  box-shadow: 0 25px 60px rgba(0, 0, 0, 0.65);
  backdrop-filter: blur(16px);
}

.galac-logo-block {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.4rem;
  margin-bottom: 1.25rem;
}

.galac-emblem-wrap {
  width: 68px;
  height: 68px;
}

.galac-emblem-svg {
  width: 68px !important;
  height: 68px !important;
  max-width: 68px !important;
  max-height: 68px !important;
}

.galac-wordmark {
  display: flex;
  flex-direction: column;
  align-items: center;
  line-height: 1;
}

.galac-title {
  font-size: 1.2rem;
  font-weight: 800;
  color: #ffffff;
  letter-spacing: 0.15em;
}

.galac-subtitle {
  font-size: 0.65rem;
  font-weight: 700;
  color: #38bdf8;
  letter-spacing: 0.25em;
  margin-top: 2px;
}

.card-headings-group {
  text-align: center;
  margin-bottom: 1.25rem;
}

.card-main-title {
  font-size: 1.4rem;
  font-weight: 800;
  color: #ffffff;
  letter-spacing: -0.01em;
}

.card-main-subtitle {
  font-size: 0.8rem;
  color: #94a3b8;
  margin-top: 0.25rem;
}

.error-alert-banner {
  margin-bottom: 1rem;
  padding: 0.55rem 0.75rem;
  background: rgba(239, 68, 68, 0.15);
  border: 1px solid rgba(239, 68, 68, 0.35);
  color: #fca5a5;
  border-radius: 8px;
  font-size: 0.75rem;
  text-align: center;
}

.login-inputs-form {
  display: flex;
  flex-direction: column;
  gap: 0.95rem;
}

.input-form-field {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.field-label {
  font-size: 0.775rem;
  font-weight: 600;
  color: #cbd5e1;
}

.field-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.field-left-icon {
  position: absolute;
  left: 0.85rem;
  width: 16px !important;
  height: 16px !important;
  color: #64748b;
  pointer-events: none;
}

.field-native-input {
  width: 100%;
  padding: 0.65rem 0.85rem 0.65rem 2.4rem;
  background: #091224;
  border: 1px solid rgba(148, 163, 184, 0.22);
  border-radius: 8px;
  font-size: 0.825rem;
  color: #ffffff;
  outline: none;
  transition: all 0.18s ease;
}

.field-native-input:focus {
  background: #0d1a33;
  border-color: #38bdf8;
  box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.18);
}

.field-native-input::placeholder {
  color: #475569;
}

.eye-reveal-btn {
  position: absolute;
  right: 0.75rem;
  background: none;
  border: none;
  cursor: pointer;
  color: #64748b;
  display: flex;
  align-items: center;
  padding: 0.25rem;
}

.eye-reveal-btn:hover {
  color: #94a3b8;
}

.eye-svg {
  width: 16px !important;
  height: 16px !important;
}

.remember-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 0.15rem;
}

.remember-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.775rem;
  color: #94a3b8;
  cursor: pointer;
}

.remember-checkbox {
  width: 14px;
  height: 14px;
  accent-color: #2563eb;
  cursor: pointer;
}

.login-action-btn {
  width: 100%;
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #ffffff;
  border: none;
  border-radius: 8px;
  padding: 0.75rem;
  font-size: 0.875rem;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 4px 18px rgba(37, 99, 235, 0.45);
  transition: all 0.18s ease;
  margin-top: 0.4rem;
}

.login-action-btn:hover {
  background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
  transform: translateY(-1px);
}

.btn-inner-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-arrow-svg {
  width: 16px !important;
  height: 16px !important;
}

/* Sección de Botones Demo (Idéntica a imagen de referencia) */
.demo-logins-block {
  margin-top: 1.5rem;
  padding-top: 1.25rem;
  border-top: 1px solid rgba(148, 163, 184, 0.12);
  text-align: center;
}

.demo-legend-title {
  display: block;
  font-size: 0.75rem;
  font-weight: 600;
  color: #94a3b8;
  margin-bottom: 0.65rem;
}

.demo-pills-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.45rem;
}

.role-pill-btn {
  padding: 0.45rem 0.35rem;
  font-size: 0.725rem;
  font-weight: 700;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.15s ease;
  text-align: center;
  border: 1px solid transparent;
}

/* Colores Idénticos a la Imagen de Referencia */
.pill-admin {
  background: #f3e8ff;
  color: #7e22ce;
  border-color: #e9d5ff;
}

.pill-analista {
  background: #e0f2fe;
  color: #0369a1;
  border-color: #bae6fd;
}

.pill-auditor {
  background: #fef3c7;
  color: #b45309;
  border-color: #fde68a;
}

.pill-cliente {
  background: #dcfce7;
  color: #15803d;
  border-color: #bbf7d0;
}

.role-pill-btn:hover {
  transform: translateY(-1px);
  filter: brightness(0.96);
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
}

@media (max-width: 960px) {
  .visual-panel { display: none; }
  .form-panel { flex: 1; }
}
</style>