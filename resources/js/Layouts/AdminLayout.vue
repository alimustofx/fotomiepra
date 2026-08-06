<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

// Helper untuk mengecek tab aktif
const isActive = (routeName) => {
  return route().current(routeName);
};
</script>

<template>
  <!-- Menambahkan overflow-x-hidden di sini agar tidak ada scroll ke samping (horizontal) yang bocor -->
  <div class="min-h-screen bg-paper text-ink-navy font-sans antialiased flex flex-col overflow-x-hidden">
    
    <!-- Top Header Bar -->
    <header class="bg-ink-navy text-paper-card px-4 md:px-8 py-3 flex justify-between items-center shadow-sm">
      <div class="flex items-center space-x-3">
        <span class="font-mono text-xs px-2 py-0.5 bg-paper/20 rounded text-paper tracking-wider">ADMIN</span>
        <h1 class="text-base font-bold tracking-tight">ARSIP SISWA — PANEL KELOLA</h1>
      </div>
      
      <div class="flex items-center space-x-4 text-xs font-mono">
        <span class="text-paper/80 hidden sm:inline">{{ page.props.auth.user.name }}</span>
        <Link
          :href="route('logout')"
          method="post"
          as="button"
          class="text-stamp-red hover:underline font-semibold"
        >
          [ Keluar ]
        </Link>
      </div>
    </header>

    <!-- Folder Tab Navigation (Scroll dihilangkan) -->
    <div class="max-w-7xl w-full mx-auto px-4 md:px-8 pt-6">
      <nav class="flex space-x-1 border-b border-ledger-line flex-wrap sm:flex-nowrap">
        <!-- Tab 1: Ringkasan / Dashboard -->
        <Link
          :href="route('dashboard')"
          :class="[
            'px-5 py-2.5 font-sans font-medium text-sm rounded-t-lg border-t border-x transition-all duration-150 whitespace-nowrap flex items-center space-x-2',
            isActive('dashboard')
              ? 'bg-paper-card border-ledger-line text-ink-navy -mb-px font-semibold shadow-xs'
              : 'bg-paper/60 border-transparent text-ink-faded hover:text-ink-navy hover:bg-paper'
          ]"
        >
          <span>📁 Ringkasan Arsip</span>
        </Link>

        <!-- Tab 2: Data Foto Siswa -->
        <Link
        :href="route('photos.index')"
        :class="[
            'px-5 py-2.5 font-sans font-medium text-sm rounded-t-lg border-t border-x transition-all duration-150 whitespace-nowrap flex items-center space-x-2',
            isActive('photos.index')
            ? 'bg-paper-card border-ledger-line text-ink-navy -mb-px font-semibold shadow-xs'
            : 'bg-paper/60 border-transparent text-ink-faded hover:text-ink-navy hover:bg-paper'
        ]"
        >
        <span>🗂️ Data Foto</span>
        </Link>

        <!-- Tab 3: Impor Excel -->
        <Link
        :href="route('photos.import')"
        :class="[
            'px-5 py-2.5 font-sans font-medium text-sm rounded-t-lg border-t border-x transition-all duration-150 whitespace-nowrap flex items-center space-x-2',
            isActive('photos.import*')
            ? 'bg-paper-card border-ledger-line text-ink-navy -mb-px font-semibold shadow-xs'
            : 'bg-paper/60 border-transparent text-ink-faded hover:text-ink-navy hover:bg-paper'
        ]"
        >
        <span>📥 Impor Excel</span>
        </Link>
      </nav>
    </div>

    <!-- Main Content Folder Area (Scroll internal dihilangkan) -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 md:px-8 pb-12">
      <div class="bg-paper-card border-x border-b border-ledger-line rounded-b-lg p-6 md:p-8 shadow-sm">
        <slot />
      </div>
    </main>
  </div>
</template>