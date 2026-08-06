<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  stats: Object,
});

// Hitung nilai maksimum untuk menentukan panjang bar chart
const maxClassCount = computed(() => {
  if (!props.stats.classDistribution.length) return 0;
  return Math.max(...props.stats.classDistribution.map(item => item.total));
});

// Format tanggal
const formatDate = (dateString) => {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(date);
};
</script>

<template>
  <Head title="Ringkasan Dashboard — Arsip Siswa" />

  <AdminLayout>
    <div class="space-y-8 animate-[fadeUp_0.5s_ease-out_both] pb-10">
      
      <!-- Header Section -->
      <div class="border-b-2 border-ledger-line pb-5 flex flex-col md:flex-row md:items-end justify-between gap-4 relative">
        <div>
          <!-- Badge Kecil -->
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-ink-navy text-paper-card text-[10px] font-mono font-bold uppercase tracking-widest rounded-sm mb-3 shadow-sm">
            Panel Pengawasan
          </span>
          <h2 class="text-2xl md:text-3xl font-black text-ink-navy tracking-tight font-sans">
            Ringkasan Arsip
          </h2>
          <p class="text-xs md:text-sm text-ink-faded mt-1.5 font-mono">
            Gambaran umum data foto siswa dan entri tercatat di dalam sistem.
          </p>
        </div>
        
        <div class="text-left md:text-right">
          <p class="text-[10px] font-mono text-ink-faded uppercase tracking-widest">Waktu Sistem</p>
          <p class="text-sm font-mono font-bold text-ink-navy">{{ new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}</p>
        </div>
      </div>

      <!-- Stat Cards Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        
        <!-- Card 1: Total Arsip -->
        <div class="relative bg-paper-card p-6 rounded-xl border-2 border-ledger-line shadow-[4px_4px_0px_0px_#1F3A52] hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_#1F3A52] transition-all duration-200">
          <div class="absolute -top-2 left-6 w-12 h-4 bg-amber-200/60 border border-amber-300/60 rotate-[-4deg] z-10 backdrop-blur-[1px]" aria-hidden="true"></div>
          <span class="text-[10px] font-mono font-bold uppercase text-ink-faded tracking-widest block mb-1">Total Arsip Foto</span>
          <p class="text-4xl font-mono font-black text-ink-navy mt-2">
            {{ stats.totalPhotos.toLocaleString('id-ID') }}
          </p>
        </div>

        <!-- Card 2: Kelas Tercatat -->
        <div class="relative bg-paper-card p-6 rounded-xl border-2 border-ledger-line shadow-[4px_4px_0px_0px_#1F3A52] hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_#1F3A52] transition-all duration-200">
          <div class="absolute -top-2 right-8 w-14 h-4 bg-amber-200/60 border border-amber-300/60 rotate-[6deg] z-10 backdrop-blur-[1px]" aria-hidden="true"></div>
          <span class="text-[10px] font-mono font-bold uppercase text-ink-faded tracking-widest block mb-1">Kelas Tercatat</span>
          <p class="text-4xl font-mono font-black text-ink-navy mt-2">
            {{ stats.totalClasses }}
          </p>
        </div>

        <!-- Card 3: Impor Terakhir -->
        <div class="relative bg-paper-card p-6 rounded-xl border-2 border-ledger-line shadow-[4px_4px_0px_0px_#1F3A52] hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_#1F3A52] transition-all duration-200 flex flex-col justify-center">
          <div class="absolute -top-2 left-1/2 -translate-x-1/2 w-10 h-4 bg-amber-200/60 border border-amber-300/60 rotate-[-2deg] z-10 backdrop-blur-[1px]" aria-hidden="true"></div>
          <span class="text-[10px] font-mono font-bold uppercase text-ink-faded tracking-widest block mb-1">Impor Terakhir</span>
          <div class="mt-2">
            <template v-if="stats.latestImport">
              <p class="text-xl font-mono font-black text-ink-navy leading-none">{{ stats.latestImport.success_rows }} <span class="text-sm font-semibold">Entri</span></p>
              <p class="text-[11px] font-mono text-ink-faded mt-1.5 bg-paper inline-block px-1.5 py-0.5 rounded border border-ledger-line/50">{{ formatDate(stats.latestImport.created_at) }}</p>
            </template>
            <template v-else>
              <span class="text-ink-faded/70 font-mono text-xs uppercase tracking-wider">Belum ada riwayat</span>
            </template>
          </div>
        </div>
      </div>

      <!-- Area Bawah: Dua Kolom -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Bar Chart per Kelas -->
        <div class="bg-paper-card p-6 md:p-8 rounded-xl border-2 border-ledger-line shadow-sm relative">
          <!-- Aksen Garis Kertas Milimeter (Grafik) -->
          <div class="absolute inset-0 opacity-[0.03] pointer-events-none bg-[linear-gradient(to_right,#1f3a52_1px,transparent_1px),linear-gradient(to_bottom,#1f3a52_1px,transparent_1px)] bg-[size:12px_12px] rounded-xl" aria-hidden="true"></div>
          
          <h3 class="text-sm font-mono font-bold text-ink-navy uppercase tracking-wider mb-6 pb-2 border-b-2 border-ledger-line relative z-10">
            📊 Distribusi per Kelas
          </h3>

          <div v-if="stats.classDistribution.length > 0" class="space-y-4 relative z-10">
            <div class="grid grid-cols-1 gap-3.5">
              <div
                v-for="item in stats.classDistribution"
                :key="item.kelas"
                class="flex items-center space-x-3 text-xs group"
              >
                <span class="w-20 sm:w-24 font-mono font-bold text-ink-navy truncate text-right">
                  {{ item.kelas }}
                </span>
                
                <!-- Trek Bar -->
                <div class="flex-1 bg-paper h-7 rounded-r-md border-y-2 border-r-2 border-ledger-line overflow-hidden relative flex items-center shadow-inner">
                  <!-- Garis Putus-putus pembantu di dalam bar -->
                  <div class="absolute inset-0 bg-[linear-gradient(to_right,#1f3a5220_1px,transparent_1px)] bg-[size:20%_100%]" aria-hidden="true"></div>
                  
                  <!-- Bar Fill -->
                  <div
                    class="h-full transition-all duration-700 ease-out flex items-center justify-end px-2"
                    :class="item.total === maxClassCount ? 'bg-stamp-red' : 'bg-ink-navy'"
                    :style="{ width: `${(item.total / maxClassCount) * 100}%` }"
                  >
                    <!-- Teks Jumlah di dalam bar (Muncul jika bar cukup panjang) -->
                    <span v-if="(item.total / maxClassCount) > 0.15" class="font-mono text-[11px] font-bold text-paper-card drop-shadow-sm">
                      {{ item.total }}
                    </span>
                  </div>
                  
                  <!-- Teks Jumlah di luar bar (Jika bar terlalu pendek) -->
                  <span v-if="(item.total / maxClassCount) <= 0.15" class="absolute left-2 font-mono text-[11px] font-bold text-ink-navy drop-shadow-sm" :style="{ left: `calc(${(item.total / maxClassCount) * 100}% + 8px)`}">
                    {{ item.total }}
                  </span>
                </div>
              </div>
            </div>
            <p class="text-[10px] text-ink-faded font-mono mt-4 pt-4 border-t border-dashed border-ledger-line/50">
              * Bar <span class="text-stamp-red font-bold">MERAH</span> menandai kelas dengan entri terbanyak.
            </p>
          </div>

          <div v-else class="text-center py-10 text-xs text-ink-faded font-mono uppercase tracking-wider relative z-10 border-2 border-dashed border-ledger-line/50 rounded-lg">
            [ Data Grafik Kosong ]
          </div>
        </div>

        <!-- Tabel Entri Terbaru -->
        <div class="bg-paper-card p-6 md:p-8 rounded-xl border-2 border-ledger-line shadow-sm relative">
          <!-- Aksen Lubang Kertas Binder di kiri -->
          <div class="absolute top-8 -left-3 flex flex-col gap-12" aria-hidden="true">
            <div class="w-5 h-5 rounded-full bg-paper border-2 border-ledger-line shadow-inner"></div>
            <div class="w-5 h-5 rounded-full bg-paper border-2 border-ledger-line shadow-inner"></div>
            <div class="w-5 h-5 rounded-full bg-paper border-2 border-ledger-line shadow-inner"></div>
          </div>

          <h3 class="text-sm font-mono font-bold text-ink-navy uppercase tracking-wider mb-6 pb-2 border-b-2 border-ledger-line pl-4">
            📝 Log Entri Terbaru
          </h3>

          <div class="overflow-x-auto pl-4">
            <table v-if="stats.latestEntries.length > 0" class="w-full text-left text-xs border-collapse">
              <thead>
                <tr class="border-b-2 border-ledger-line text-ink-navy font-mono font-bold uppercase tracking-wider text-[10px]">
                  <th class="py-3 px-2">#</th>
                  <th class="py-3 px-2">NIS</th>
                  <th class="py-3 px-2">Nama Siswa</th>
                  <th class="py-3 px-2">KLS</th>
                  <th class="py-3 px-2 text-right">Waktu</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-dashed divide-ledger-line/50 font-mono">
                <tr
                  v-for="(student, index) in stats.latestEntries"
                  :key="student.id"
                  class="hover:bg-ink-navy/[0.03] transition-colors group"
                >
                  <td class="py-3 px-2 text-ink-faded/70 text-[10px]">
                    {{ String(index + 1).padStart(2, '0') }}
                  </td>
                  <td class="py-3 px-2 font-bold text-ink-navy">
                    {{ student.nis }}
                  </td>
                  <td class="py-3 px-2 font-sans font-semibold text-ink-navy uppercase truncate max-w-[120px]">
                    {{ student.nama }}
                  </td>
                  <td class="py-3 px-2 text-ink-faded font-bold">
                    {{ student.kelas }}
                  </td>
                  <td class="py-3 px-2 text-ink-faded/80 text-right text-[10px] tracking-tight group-hover:text-ink-navy transition-colors">
                    {{ formatDate(student.created_at) }}
                  </td>
                </tr>
              </tbody>
            </table>

            <div v-else class="text-center py-10 text-xs text-ink-faded font-mono uppercase tracking-wider border-2 border-dashed border-ledger-line/50 rounded-lg">
              [ Belum Ada Entri Tercatat ]
            </div>
          </div>
        </div>

      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
@keyframes fadeUp {
  0% { transform: translateY(15px); opacity: 0; }
  100% { transform: translateY(0); opacity: 1; }
}

@media (prefers-reduced-motion: reduce) {
  .animate-\[fadeUp_0\.5s_ease-out_both\] {
    animation: none !important;
    transition: opacity 0.2s linear !important;
    transform: none !important;
  }
}
</style>