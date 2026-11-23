<template>
  <div class="h-screen w-screen overflow-hidden bg-[#070d19] text-slate-100 flex flex-col font-sans">
    <!-- Si la ruta es Login, renderizar pantalla completa sin sidebar -->
    <div v-if="isLoginRoute" class="flex-1 w-full h-full overflow-hidden">
      <router-view />
    </div>

    <!-- Layout Autenticado: Sidebar Fija a la Izquierda + Contenido a la Derecha (Mockup Figura 2.2) -->
    <div v-else class="flex-1 flex overflow-hidden w-full h-full">
      <!-- Barra Lateral Fija de Navegación -->
      <AppSidebar />

      <!-- Área de Trabajo Principal -->
      <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Barra Superior Compacta (40px) -->
        <AppTopBar />

        <!-- Vista Activa con Scroll Independiente Compacto -->
        <main class="flex-1 overflow-y-auto p-4 bg-[#0a1224]/80">
          <router-view />
        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import AppSidebar from './components/AppSidebar.vue';
import AppTopBar from './components/AppTopBar.vue';

const route = useRoute();
const isLoginRoute = computed(() => route.name === 'Login');
</script>
