<template>
  <div class="relative bg-paper-card border border-ledger-line rounded-lg p-6 md:p-8 shadow-sm">
    <!-- Lubang Punch (Bindex Folder Style) -->
    <div class="absolute top-3 left-1/2 -translate-x-1/2 flex space-x-12 pointer-events-none">
      <div class="w-3 h-3 rounded-full bg-paper border border-ledger-line shadow-inner"></div>
      <div class="w-3 h-3 rounded-full bg-paper border border-ledger-line shadow-inner"></div>
    </div>
    
    <slot />
  </div>
</template>