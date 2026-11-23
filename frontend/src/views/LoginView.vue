<template>
  <div class="login-wrapper">
    <!-- PANEL IZQUIERDO: Presentación Visual & Estados (Idéntico a Captura de Referencia 3) -->
    <div class="visual-panel">
      <div class="visual-content">
        <!-- Título y Subtítulo Corporativo -->
        <div class="visual-header">
          <div class="brand-badge">
            <span class="brand-dot"></span>
            <span>GALAC SECURITY &bull; HALOMONITOR</span>
          </div>
          <h1 class="visual-title">Gestión Transaccional de Servicios</h1>
          <p class="visual-subtitle">
            Control de incidentes, ciclo de vida de tickets y trazabilidad inmutable en tiempo real.
          </p>
        </div>

        <!-- Ilustración Central con Badges Flotantes de Estado (Captura 3) -->
        <div class="isometric-illustration">
          <!-- Servidor Central e Infraestructura Cloud -->
          <div class="cloud-server-graphic">
            <div class="server-rack">
              <div class="server-unit">
                <span class="server-led blink"></span>
                <span class="server-led"></span>
                <span class="server-slot"></span>
              </div>
              <div class="server-unit">
                <span class="server-led"></span>
                <span class="server-led blink"></span>
                <span class="server-slot"></span>
              </div>
              <div class="server-unit active-unit">
                <span class="server-led green-led blink"></span>
                <span class="server-led"></span>
                <span class="server-slot"></span>
              </div>
            </div>
          </div>

          <!-- Badges de Estado Flotantes con Estilo Glassmorphism (Captura 3) -->
          <div class="floating-pills-container">
            <!-- 1. SOLICITADA (Estado Open / Pendiente) -->
            <div class="floating-pill-card glass-gold">
              <div class="status-pill status-solicitada">SOLICITADA</div>
              <div class="pill-preview-lines">
                <div class="preview-line w-80"></div>
                <div class="preview-line w-60"></div>
              </div>
            </div>

            <!-- 2. AUTORIZADA (Estado InProgress / En Proceso) -->
            <div class="floating-pill-card glass-blue">
              <div class="status-pill status-autorizada">AUTORIZADA</div>
              <div class="pill-preview-lines">
                <div class="preview-line w-80"></div>
                <div class="preview-line w-50"></div>
              </div>
            </div>

            <!-- 3. COMPLETADA (Estado Resolved / Resuelto) -->
            <div class="floating-pill-card glass-green">
              <div class="status-pill status-completada">COMPLETADA</div>
              <div class="pill-preview-lines">
                <div class="preview-line w-70"></div>
                <div class="preview-line w-40"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="visual-footer">
          <span>Dr. Nitgard Zapata Garay &bull; IT Ciudad Valles &bull; 7mo Semestre</span>
        </div>
      </div>
    </div>

    <!-- PANEL DERECHO: Tarjeta de Iniciar Sesión (Idéntico a Captura 3) -->
    <div class="form-panel">
      <div class="login-card">
        <!-- Icono Escudo con Checkmark (Captura 3) -->
        <div class="card-icon-container">
          <svg class="shield-check-icon" viewBox="0 0 24 24" fill="none">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" fill="#2563eb" />
            <path d="M9 12l2 2 4-4" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </div>

        <!-- Encabezado de la Tarjeta -->
        <div class="card-header">
          <h2 class="card-title">Iniciar sesión</h2>
          <p class="card-subtitle">Ingresa tus credenciales para acceder al sistema</p>
        </div>

        <!-- Alerta de Error -->
        <div v-if="errorMessage" class="error-box">
          <span>&times; {{ errorMessage }}</span>
        </div>

        <!-- Formulario -->
        <form @submit.prevent="handleSubmit" class="login-form">
          <!-- Campo: Correo Electrónico -->
          <div class="form-group">
            <label class="form-label">Correo electrónico</label>
            <div class="input-with-icon">
              <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              <input 
                type="email" 
                v-model="email" 
                placeholder="usuario@correo.com" 
                required 
                class="form-input"
              />
            </div>
          </div>

          <!-- Campo: Contraseña -->
          <div class="form-group">
            <label class="form-label">Contraseña</label>
            <div class="input-with-icon">
              <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke-width="1.8"/>
                <path d="M7 11V7a5 5 0 0110 0v4" stroke-width="1.8"/>
              </svg>
              <input 
                :type="showPassword ? 'text' : 'password'" 
                v-model="password" 
                placeholder="••••••••••••" 
                required 
                class="form-input"
              />
              <button type="button" @click="showPassword = !showPassword" class="eye-toggle-btn">
                <svg v-if="!showPassword" class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg v-else class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Checkbox: Recordar sesión -->
          <div class="checkbox-row">
            <label class="checkbox-label">
              <input type="checkbox" v-model="rememberMe" class="form-checkbox" />
              <span>Recordar sesión</span>
            </label>
          </div>

          <!-- Botón de Entrada (Captura 3) -->
          <button type="submit" :disabled="isLoading" class="submit-btn">
            <span v-if="isLoading">Accediendo...</span>
            <span v-else class="btn-text-row">
              <span>Entrar al sistema</span>
              <svg class="arrow-right-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
              </svg>
            </span>
          </button>
        </form>

        <!-- Accesos Demo para Evaluación Inmediata -->
        <div class="demo-roles-section">
          <span class="demo-title">Ingresar como usuario demo:</span>
          <div class="demo-buttons-grid">
            <button 
              type="button" 
              @click="setRole('admin@halomonitor.com', 'Admin123!')" 
              class="demo-role-btn admin-badge"
            >
              Administrador
            </button>
            <button 
              type="button" 
              @click="setRole('analista@halomonitor.com', 'Analista123!')" 
              class="demo-role-btn analista-badge"
            >
              Analista
            </button>
            <button 
              type="button" 
              @click="setRole('auditor@halomonitor.com', 'Auditor123!')" 
              class="demo-role-btn auditor-badge"
            >
              Auditor
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

const email = ref('admin@halomonitor.com');
const password = ref('Admin123!');
const rememberMe = ref(true);
const showPassword = ref(false);
const errorMessage = ref('');

function setRole(e, p) {
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

<style scoped>
.login-wrapper {
  display: flex;
  width: 100vw;
  height: 100vh;
  overflow: hidden;
  background-color: #0b1329;
}

/* PANEL IZQUIERDO */
.visual-panel {
  flex: 1.1;
  background: linear-gradient(135deg, #172554 0%, #1d4ed8 45%, #2563eb 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  position: relative;
  overflow: hidden;
}

.visual-content {
  max-width: 520px;
  width: 100%;
  color: #ffffff;
  display: flex;
  flex-direction: column;
  gap: 2.5rem;
}

.brand-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.725rem;
  font-weight: 700;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: #bfdbfe;
  background: rgba(255, 255, 255, 0.1);
  padding: 0.35rem 0.75rem;
  border-radius: 9999px;
  margin-bottom: 0.75rem;
  border: 1px solid rgba(255, 255, 255, 0.15);
}

.brand-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #38bdf8;
}

.visual-title {
  font-size: 2.25rem;
  font-weight: 800;
  line-height: 1.15;
  color: #ffffff;
  letter-spacing: -0.02em;
}

.visual-subtitle {
  font-size: 0.95rem;
  color: #dbeafe;
  margin-top: 0.75rem;
  line-height: 1.5;
  font-weight: 400;
}

/* Ilustración Isométrica con Badges Flotantes */
.isometric-illustration {
  position: relative;
  height: 220px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1.5rem;
}

.cloud-server-graphic {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
}

.server-rack {
  width: 130px;
  background: #0f172a;
  border: 2px solid #38bdf8;
  border-radius: 12px;
  padding: 0.65rem;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.server-unit {
  background: #1e293b;
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 6px;
  padding: 0.5rem;
  display: flex;
  align-items: center;
  gap: 0.4rem;
}

.server-led {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #38bdf8;
}

.green-led {
  background: #10b981;
}

.server-slot {
  flex: 1;
  height: 4px;
  background: #334155;
  border-radius: 2px;
}

.floating-pills-container {
  flex: 1.3;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.floating-pill-card {
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.22);
  border-radius: 10px;
  padding: 0.6rem 0.85rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.pill-preview-lines {
  display: flex;
  flex-direction: column;
  gap: 3px;
  align-items: flex-end;
}

.preview-line {
  height: 4px;
  border-radius: 2px;
  background: rgba(255, 255, 255, 0.3);
}

.w-80 { width: 60px; }
.w-70 { width: 50px; }
.w-60 { width: 45px; }
.w-50 { width: 35px; }
.w-40 { width: 25px; }

.visual-footer {
  font-size: 0.75rem;
  color: #93c5fd;
  font-family: var(--font-mono);
}

/* PANEL DERECHO */
.form-panel {
  flex: 0.9;
  background-color: #0b1329;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2.5rem;
}

.login-card {
  width: 100%;
  max-width: 400px;
  background: #ffffff;
  border-radius: 16px;
  padding: 2.25rem 2rem;
  box-shadow: 0 20px 45px rgba(0, 0, 0, 0.25);
  color: #1e293b;
}

.card-icon-container {
  margin-bottom: 1rem;
}

.shield-check-icon {
  width: 44px;
  height: 44px;
  max-width: 44px;
  max-height: 44px;
}

.card-title {
  font-size: 1.45rem;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.01em;
}

.card-subtitle {
  font-size: 0.825rem;
  color: #64748b;
  margin-top: 0.25rem;
}

.error-box {
  margin-top: 1rem;
  padding: 0.55rem 0.75rem;
  background: #fef2f2;
  border: 1px solid #fecaca;
  color: #b91c1c;
  border-radius: 8px;
  font-size: 0.75rem;
}

.login-form {
  margin-top: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.form-label {
  font-size: 0.775rem;
  font-weight: 600;
  color: #334155;
}

.input-with-icon {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 0.75rem;
  width: 16px;
  height: 16px;
  color: #94a3b8;
  pointer-events: none;
}

.form-input {
  width: 100%;
  padding: 0.6rem 0.85rem 0.6rem 2.25rem;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  font-size: 0.85rem;
  color: #0f172a;
  outline: none;
  background: #f8fafc;
  transition: all 0.18s ease;
}

.form-input:focus {
  background: #ffffff;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}

.eye-toggle-btn {
  position: absolute;
  right: 0.75rem;
  background: none;
  border: none;
  cursor: pointer;
  color: #94a3b8;
  display: flex;
  align-items: center;
}

.eye-icon {
  width: 16px;
  height: 16px;
}

.checkbox-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 0.25rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.45rem;
  font-size: 0.775rem;
  color: #64748b;
  cursor: pointer;
}

.form-checkbox {
  width: 14px;
  height: 14px;
  accent-color: #2563eb;
}

.submit-btn {
  width: 100%;
  background: #2563eb;
  color: #ffffff;
  border: none;
  border-radius: 8px;
  padding: 0.7rem;
  font-size: 0.85rem;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
  transition: all 0.18s ease;
  margin-top: 0.5rem;
}

.submit-btn:hover {
  background: #1d4ed8;
  transform: translateY(-1px);
}

.btn-text-row {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.arrow-right-icon {
  width: 16px;
  height: 16px;
}

.demo-roles-section {
  margin-top: 1.5rem;
  padding-top: 1.25rem;
  border-top: 1px solid #f1f5f9;
}

.demo-title {
  display: block;
  font-size: 0.725rem;
  font-weight: 600;
  color: #64748b;
  margin-bottom: 0.5rem;
  text-align: center;
}

.demo-buttons-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.45rem;
}

.demo-role-btn {
  padding: 0.35rem 0.5rem;
  font-size: 0.725rem;
  font-weight: 700;
  border-radius: 6px;
  cursor: pointer;
  border: 1px solid transparent;
  transition: all 0.15s ease;
  text-align: center;
}

.admin-badge { background: #f3e8ff; color: #7e22ce; border-color: #e9d5ff; }
.analista-badge { background: #e0f2fe; color: #0369a1; border-color: #bae6fd; }
.auditor-badge { background: #fef3c7; color: #b45309; border-color: #fde68a; }

.demo-role-btn:hover {
  transform: translateY(-1px);
  filter: brightness(0.95);
}

@media (max-width: 900px) {
  .visual-panel { display: none; }
  .form-panel { flex: 1; }
}
</style>
