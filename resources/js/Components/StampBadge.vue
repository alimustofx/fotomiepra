<template>
  <div 
    class="stamp-badge inline-block border-4 border-stamp-red text-stamp-red font-mono font-bold tracking-widest px-3 py-1 text-sm uppercase select-none pointer-events-none rounded opacity-90 shadow-sm"
    :style="stampStyle"
  >
    DITEMUKAN
  </div>
</template>

<script setup>
import { computed } from 'vue';

// Rotasi acak khas cap fisik (antara -10deg sampai -6deg)
const randomRotation = computed(() => {
  const deg = -(Math.floor(Math.random() * 5) + 6);
  return `${deg}deg`;
});

const stampStyle = computed(() => ({
  transform: `rotate(${randomRotation.value})`,
}));
</script>

<style scoped>
.stamp-badge {
  animation: stamp-pop 180ms cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

@keyframes stamp-pop {
  0% {
    opacity: 0;
    transform: scale(1.4) rotate(0deg);
  }
  100% {
    opacity: 0.9;
  }
}

@media (prefers-reduced-motion: reduce) {
  .stamp-badge {
    animation: none !important;
  }
}
</style>