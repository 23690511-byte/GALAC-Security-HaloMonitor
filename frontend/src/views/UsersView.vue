<template>
  <div class="space-y-3 max-w-full">
    <!-- Barra Superior de Acciones -->
    <div class="flex items-center justify-between gap-2 pb-1 border-b border-sky-950/80">
      <div>
        <h1 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-1.5">
          <span class="w-1.5 h-3.5 bg-purple-500 rounded-sm inline-block"></span>
          Gestión de Usuarios y Roles (RBAC)
        </h1>
        <span class="text-[10px] text-slate-400 font-mono">Panel Exclusivo de Administrador &bull; Trazabilidad de Cuentas y Accesos</span>
      </div>

      <div>
        <button @click="openCreateModal" class="btn-cyber-primary text-xs py-1 px-3">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
          </svg>
          + Nuevo Usuario
        </button>
      </div>
    </div>

    <!-- Banner de Notificación -->
    <div v-if="bannerMessage" :class="bannerType === 'error' ? 'bg-rose-500/15 border-rose-500/30 text-rose-300' : 'bg-emerald-500/15 border-emerald-500/30 text-emerald-300'" class="p-2 rounded border text-xs flex items-center justify-between">
      <div class="flex items-center gap-1.5">
        <span class="font-bold">{{ bannerType === 'error' ? 'Error:' : 'Operación Exitosa:' }}</span>
        <span>{{ bannerMessage }}</span>
      </div>
      <button @click="bannerMessage = ''" class="hover:text-white">&times;</button>
    </div>

    <!-- KPIs Rápidos de Cuentas -->
    <div class="grid grid-cols-4 gap-2.5">
      <div class="p-2.5 rounded border border-purple-500/30 bg-[#0d162b] flex items-center justify-between">
        <div>
          <span class="text-[10px] text-purple-300 font-bold uppercase tracking-wider block">Total Usuarios</span>
          <span class="text-base font-black text-white font-mono">{{ users.length }}</span>
        </div>
        <span class="w-2 h-2 rounded-full bg-purple-400 animate-pulse"></span>
      </div>

      <div class="p-2.5 rounded border border-emerald-500/30 bg-[#0d162b] flex items-center justify-between">
        <div>
          <span class="text-[10px] text-emerald-300 font-bold uppercase tracking-wider block">Usuarios Activos</span>
          <span class="text-base font-black text-emerald-400 font-mono">{{ activeUsersCount }}</span>
        </div>
        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
      </div>

      <div class="p-2.5 rounded border border-blue-500/30 bg-[#0d162b] flex items-center justify-between">
        <div>
          <span class="text-[10px] text-blue-300 font-bold uppercase tracking-wider block">Clientes PyME</span>
          <span class="text-base font-black text-blue-400 font-mono">{{ clientCount }}</span>
        </div>
        <span class="w-2 h-2 rounded-full bg-blue-400"></span>
      </div>

      <div class="p-2.5 rounded border border-amber-500/30 bg-[#0d162b] flex items-center justify-between">
        <div>
          <span class="text-[10px] text-amber-300 font-bold uppercase tracking-wider block">Equipo SOC & Auditoría</span>
          <span class="text-base font-black text-amber-400 font-mono">{{ staffCount }}</span>
        </div>
        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
      </div>
    </div>

    <!-- DataGrid de Usuarios -->
    <div class="cyber-card p-3.5 bg-[#0d172e]">
      <DataGrid
        :data="users"
        :columns="columns"
        search-placeholder="Buscar por nombre, correo, rol o departamento..."
        export-title="Usuarios_HaloMonitor"
      >
        <template #cell-rol="{ value }">
          <span :class="['role-chip font-bold text-[10px] uppercase', getRoleClass(value)]">
            {{ value }}
          </span>
        </template>

        <template #cell-activo="{ value }">
          <span :class="Number(value) === 1 ? 'badge-active' : 'badge-inactive'">
            <span class="status-dot"></span>
            {{ Number(value) === 1 ? 'Activo' : 'Inactivo' }}
          </span>
        </template>

        <template #cell-creado_en="{ value }">
          <span class="font-mono text-slate-400 text-[10px]">
            {{ formatDate(value) }}
          </span>
        </template>

        <template #actions="{ row }">
          <div class="flex items-center justify-end gap-1.5">
            <button 
              @click="handleToggleStatus(row)"
              :class="Number(row.activo) === 1 ? 'btn-toggle-suspend' : 'btn-toggle-activate'"
              :title="Number(row.activo) === 1 ? 'Suspender acceso a este usuario' : 'Habilitar acceso a este usuario'"
            >
              {{ Number(row.activo) === 1 ? 'Desactivar' : 'Activar' }}
            </button>
          </div>
        </template>
      </DataGrid>
    </div>

    <!-- MODAL: Crear Nuevo Usuario -->
    <div v-if="showCreateModal" class="modal-overlay" @click.self="showCreateModal = false">
      <div class="modal-content max-w-md p-0">
        <div class="flex items-center justify-between p-3 border-b border-sky-950/80 bg-[#080f1e] rounded-t-lg">
          <h3 class="text-xs font-bold text-cyan-400 uppercase tracking-wider flex items-center gap-1.5">
            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Alta de Nuevo Usuario en Plataforma
          </h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <form @submit.prevent="submitCreateUser" class="p-4 space-y-3 text-xs">
          <!-- Nombre Completo -->
          <div>
            <label class="block font-semibold text-slate-300 uppercase mb-0.5 text-[11px]">Nombre Completo:</label>
            <input 
              type="text" 
              v-model="newUser.nombre" 
              required 
              placeholder="Ej. Juan Pérez - Contacto PyME" 
              class="w-full bg-[#080f1e] border border-sky-950 rounded p-2 text-slate-100 focus:border-cyan-400 outline-none text-xs"
            />
          </div>

          <!-- Correo Electrónico -->
          <div>
            <label class="block font-semibold text-slate-300 uppercase mb-0.5 text-[11px]">Correo Electrónico:</label>
            <input 
              type="email" 
              v-model="newUser.email" 
              required 
              placeholder="usuario@empresa.com" 
              class="w-full bg-[#080f1e] border border-sky-950 rounded p-2 text-slate-100 focus:border-cyan-400 outline-none text-xs"
            />
          </div>

          <!-- Contraseña Inicial -->
          <div>
            <label class="block font-semibold text-slate-300 uppercase mb-0.5 text-[11px]">Contraseña Inicial:</label>
            <input 
              type="password" 
              v-model="newUser.password" 
              required 
              placeholder="Mínimo 6 caracteres" 
              class="w-full bg-[#080f1e] border border-sky-950 rounded p-2 text-slate-100 focus:border-cyan-400 outline-none text-xs"
            />
          </div>

          <!-- Rol Asignado -->
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block font-semibold text-slate-300 uppercase mb-0.5 text-[11px]">Rol Asignado:</label>
              <select 
                v-model="newUser.rol_id" 
                class="w-full bg-[#080f1e] border border-sky-950 rounded p-2 text-slate-100 focus:border-cyan-400 outline-none text-xs"
              >
                <option :value="4">Cliente (PyME)</option>
                <option :value="2">Analista (SOC)</option>
                <option :value="3">Auditor (Cumplimiento)</option>
                <option :value="1">Administrador (Total)</option>
              </select>
            </div>

            <!-- Departamento -->
            <div>
              <label class="block font-semibold text-slate-300 uppercase mb-0.5 text-[11px]">Departamento:</label>
              <input 
                type="text" 
                v-model="newUser.departamento" 
                placeholder="Ej. Operaciones" 
                class="w-full bg-[#080f1e] border border-sky-950 rounded p-2 text-slate-100 focus:border-cyan-400 outline-none text-xs"
              />
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 pt-3 border-t border-sky-950/80">
            <button type="button" @click="showCreateModal = false" class="btn-cyber-secondary py-1 px-3 text-xs">
              Cancelar
            </button>
            <button type="submit" :disabled="isSubmitting" class="btn-cyber-primary py-1 px-4 text-xs font-bold">
              <span v-if="isSubmitting">Creando...</span>
              <span v-else>Guardar Usuario</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { usersApi } from '../services/usersApi';
import DataGrid from '../components/DataGrid.vue';

const users = ref([]);
const bannerMessage = ref('');
const bannerType = ref('success');
const showCreateModal = ref(false);
const isSubmitting = ref(false);

const newUser = ref({
  nombre: '',
  email: '',
  password: '',
  rol_id: 4, // Cliente por defecto
  departamento: 'Operaciones PyME'
});

const columns = [
  { key: 'id', label: 'ID', align: 'center' },
  { key: 'nombre', label: 'Nombre Completo' },
  { key: 'email', label: 'Correo Electrónico' },
  { key: 'rol', label: 'Rol' },
  { key: 'departamento', label: 'Departamento' },
  { key: 'activo', label: 'Estado', align: 'center' },
  { key: 'creado_en', label: 'Fecha de Alta' }
];

const activeUsersCount = computed(() => {
  return users.value.filter(u => Number(u.activo) === 1).length;
});

const clientCount = computed(() => {
  return users.value.filter(u => (u.rol || '').toLowerCase().includes('cliente')).length;
});

const staffCount = computed(() => {
  return users.value.filter(u => !(u.rol || '').toLowerCase().includes('cliente')).length;
});

function getRoleClass(role) {
  const r = (role || '').toLowerCase();
  if (r.includes('admin')) return 'role-admin';
  if (r.includes('analista')) return 'role-analista';
  if (r.includes('auditor')) return 'role-auditor';
  return 'role-cliente';
}

function formatDate(dateStr) {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('es-MX', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
}

async function loadUsers() {
  try {
    const res = await usersApi.getUsers();
    if (res?.ok) {
      users.value = res.data.usuarios || [];
    }
  } catch (err) {
    bannerType.value = 'error';
    bannerMessage.value = 'Error al cargar el catálogo de usuarios: ' + err.message;
  }
}

function openCreateModal() {
  newUser.value = {
    nombre: '',
    email: '',
    password: '',
    rol_id: 4,
    departamento: 'Operaciones PyME'
  };
  showCreateModal.value = true;
}

async function submitCreateUser() {
  isSubmitting.value = true;
  bannerMessage.value = '';
  try {
    const res = await usersApi.createUser(newUser.value);
    if (res?.ok) {
      bannerType.value = 'success';
      bannerMessage.value = res.message || 'Usuario creado exitosamente.';
      showCreateModal.value = false;
      await loadUsers();
    }
  } catch (err) {
    bannerType.value = 'error';
    bannerMessage.value = err.message || 'Ocurrió un error al crear el usuario.';
  } finally {
    isSubmitting.value = false;
  }
}

async function handleToggleStatus(user) {
  try {
    const res = await usersApi.toggleUserStatus(user.id);
    if (res?.ok) {
      bannerType.value = 'success';
      bannerMessage.value = res.message;
      await loadUsers();
    }
  } catch (err) {
    bannerType.value = 'error';
    bannerMessage.value = err.message || 'No se pudo cambiar el estado del usuario.';
  }
}

onMounted(async () => {
  await loadUsers();
});
</script>

<style scoped>
.role-chip {
  padding: 0.15rem 0.5rem;
  border-radius: 4px;
  display: inline-block;
  font-size: 0.7rem;
}

.role-admin { background: rgba(168, 85, 247, 0.2); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.4); }
.role-analista { background: rgba(56, 189, 248, 0.2); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.4); }
.role-auditor { background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); }
.role-cliente { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); }

.badge-active {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.15rem 0.5rem;
  border-radius: 9999px;
  background: rgba(16, 185, 129, 0.15);
  color: #34d399;
  font-size: 0.7rem;
  font-weight: 600;
}

.badge-inactive {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.15rem 0.5rem;
  border-radius: 9999px;
  background: rgba(239, 68, 68, 0.15);
  color: #f87171;
  font-size: 0.7rem;
  font-weight: 600;
}

.status-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: currentColor;
}

.btn-toggle-suspend {
  background: rgba(239, 68, 68, 0.15);
  color: #f87171;
  border: 1px solid rgba(239, 68, 68, 0.35);
  padding: 0.2rem 0.5rem;
  border-radius: 4px;
  font-size: 0.675rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-toggle-suspend:hover {
  background: rgba(239, 68, 68, 0.3);
}

.btn-toggle-activate {
  background: rgba(16, 185, 129, 0.15);
  color: #34d399;
  border: 1px solid rgba(16, 185, 129, 0.35);
  padding: 0.2rem 0.5rem;
  border-radius: 4px;
  font-size: 0.675rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-toggle-activate:hover {
  background: rgba(16, 185, 129, 0.3);
}
</style>
