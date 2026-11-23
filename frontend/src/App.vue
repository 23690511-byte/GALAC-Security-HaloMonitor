<template>
  <div class="min-h-screen flex flex-col bg-[#070d19] text-slate-100 font-sans">
    <!-- Header de la Aplicación -->
    <AppHeader />

    <!-- Contenido Principal con Router View -->
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <router-view />
    </main>

    <!-- Footer Corporativo -->
    <footer class="border-t border-cyan-500/15 bg-slate-950/80 py-4 text-xs text-slate-500 font-mono">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2">
        <div class="flex items-center gap-2">
          <span class="text-cyan-400 font-bold">HaloMonitor v3.0</span>
          <span>&bull;</span>
          <span>GALAC Security Platform</span>
          <span>&bull;</span>
          <span class="text-slate-400">IT Ciudad Valles</span>
        </div>
        <div>
          <span>Desarrollo de Aplicaciones Web &bull; Dr. Nitgard Zapata Garay</span>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import AppHeader from './components/AppHeader.vue';
</script>
