<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import LedgerCard from '@/Components/LedgerCard.vue';
import StampBadge from '@/Components/StampBadge.vue';

const props = defineProps({
  student: Object,
  hasSearched: Boolean,
  searchedNis: String,
});

const nisInput = ref(props.searchedNis || '');
const isLoading = ref(false);
const copied = ref(false);
const photoFailed = ref(false);

/* ---------------------------------------------------------------
 * OPENING LOGO (splash)
 * ------------------------------------------------------------- */
const showSplash = ref(false);
let splashTimer = null;

const finishSplash = () => {
  showSplash.value = false;
  sessionStorage.setItem('arsip_intro_done', '1');
};
const skipSplash = () => {
  clearTimeout(splashTimer);
  finishSplash();
};

onMounted(() => {
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const alreadySeen = sessionStorage.getItem('arsip_intro_done');

  if (prefersReducedMotion || alreadySeen) {
    showSplash.value = false;
    return;
  }

  showSplash.value = true;
  // Durasi splash screen sedikit dipersingkat agar lebih responsif
  splashTimer = setTimeout(finishSplash, 2000);
});
onBeforeUnmount(() => clearTimeout(splashTimer));

const handleSearch = () => {
  if (!nisInput.value.trim()) return;

  isLoading.value = true;
  
  router.post(
    '/',
    { nis: nisInput.value.trim() },
    {
      preserveState: true,
      preserveScroll: true,
      onFinish: () => {
        isLoading.value = false;
      },
    }
  );
};

const copyNis = () => {
  if (!props.student?.nis) return;
  navigator.clipboard.writeText(props.student.nis);
  copied.value = true;
  setTimeout(() => (copied.value = false), 1800);
};

const entryNumber = computed(() => {
  if (!props.student?.id) return '----';
  return String(props.student.id).padStart(4, '0');
});

const statusText = computed(() => {
  if (!props.hasSearched) return '';
  return props.student
    ? `Ditemukan. Data untuk NIS ${props.student.nis}.`
    : `Tidak ditemukan. Tidak ada catatan untuk NIS ${props.searchedNis}.`;
});
</script>

<template>
  <Head title="Arsip Siswa — SMA Negeri 1 Turen" />

  <!-- LAYAR PEMBUKA (SPLASH SCREEN) - TANPA LINGKARAN -->
  <Transition name="splash">
    <div
      v-if="showSplash"
      class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-paper text-ink-navy overflow-hidden"
    >
      <div class="relative flex flex-col items-center">
        <!-- Logo -->
        <img
          src="/images/logo-turen-seal.svg"
          alt="Lambang Sekolah"
          class="w-24 h-24 mb-6 animate-[sealIn_0.8s_cubic-bezier(0.34,1.56,0.64,1)_both]"
        />
        
        <!-- Teks -->
        <h1 class="text-xl md:text-2xl font-bold font-mono tracking-[0.2em] uppercase animate-[fadeUp_0.6s_ease-out_0.3s_both]">
          Arsip Digital
        </h1>
        <p class="text-xs text-ink-faded mt-2 font-mono uppercase tracking-widest animate-[fadeUp_0.6s_ease-out_0.5s_both]">
          Memuat Dokumen...
        </p>

        <!-- Tombol Lewati -->
        <button
          @click="skipSplash"
          class="absolute -bottom-16 text-[10px] font-mono text-ink-faded/60 uppercase tracking-widest hover:text-ink-navy transition-colors animate-[fadeUp_0.6s_ease-out_0.8s_both]"
        >
          [ Lewati Animasi ]
        </button>
      </div>
    </div>
  </Transition>

  <!-- KONTEN UTAMA -->
  <div
    class="min-h-screen bg-paper text-ink-navy font-sans antialiased flex flex-col justify-between p-4 md:p-8 relative selection:bg-ink-navy selection:text-paper-card overflow-x-hidden transition-opacity duration-700"
    :class="showSplash ? 'opacity-0 pointer-events-none' : 'opacity-100'"
  >
    <!-- Tekstur kertas ledger -->
    <div class="fixed inset-0 pointer-events-none opacity-60 bg-[repeating-linear-gradient(to_bottom,transparent,transparent_31px,#0f2a4a10_31px,#0f2a4a10_32px)]"></div>
    <div class="fixed inset-y-0 left-14 w-px bg-ink-navy/[0.06] pointer-events-none hidden md:block"></div>
    <div class="fixed top-0 inset-x-0 h-[3px] bg-gradient-to-r from-gold/0 via-gold/70 to-gold/0 pointer-events-none"></div>

    <p class="sr-only" role="status" aria-live="polite">{{ statusText }}</p>

    <!-- Header -->
    <header class="max-w-2xl mx-auto w-full pt-2 pb-6 relative z-10">
      <div class="flex items-center gap-3 mb-6 group cursor-default">
        <img
          src="/images/logo-turen-seal.svg"
          alt="Lambang SMA Negeri 1 Turen"
          class="w-11 h-11 md:w-12 md:h-12 object-contain shrink-0 drop-shadow-sm transition-transform duration-500 group-hover:rotate-12"
        />
        <div class="min-w-0">
          <span class="block text-[10px] font-mono font-bold text-gold uppercase tracking-[0.18em] truncate">
            SMA Negeri 1 Turen
          </span>
          <h1 class="text-xl md:text-2xl font-bold tracking-tight text-ink-navy leading-tight -mt-0.5">
            Arsip Foto Siswa
          </h1>
        </div>
      </div>
    </header>

    <!-- Main -->
    <main class="max-w-2xl mx-auto w-full my-auto py-6 relative z-10">
      <!-- Formulir Pencarian -->
      <section class="mb-8">
        <form @submit.prevent="handleSearch">
          <label for="nis" class="block text-[11px] font-mono font-bold text-ink-faded uppercase tracking-wider mb-2 px-1">
            No. Induk Siswa
          </label>

          <div class="relative group/form">
            <div class="flex items-stretch gap-2 transition-transform duration-200">
              <div class="relative flex-1">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 font-mono text-ink-faded/50 font-bold pointer-events-none transition-colors group-focus-within/form:text-gold" aria-hidden="true">
                  #
                </span>
                <input
                  id="nis"
                  v-model="nisInput"
                  type="text"
                  inputmode="numeric"
                  autocomplete="off"
                  placeholder="Masukkan NIS sesuai kartu pelajar"
                  class="w-full h-full bg-paper-card border-2 border-ledger-line rounded-lg pl-9 pr-4 py-3.5 font-mono text-base md:text-lg font-bold text-ink-navy placeholder:font-sans placeholder:text-sm placeholder:font-normal placeholder:text-ink-faded/50 outline-none focus:border-ink-navy focus:shadow-[4px_4px_0px_0px_#1F3A52] transition-all duration-200"
                  required
                />
              </div>

              <button
                type="submit"
                :disabled="isLoading"
                class="shrink-0 bg-ink-navy text-paper-card font-mono font-semibold text-xs uppercase tracking-wider px-5 md:px-6 rounded-lg hover:bg-ink-navy/90 hover:-translate-y-0.5 hover:shadow-[0_4px_0_0_#0f2a4a] active:translate-y-0.5 active:shadow-none transition-all disabled:opacity-50 disabled:pointer-events-none"
              >
                {{ isLoading ? 'Memeriksa…' : 'Cari' }}
              </button>
            </div>

            <!-- Loading Bar type-writer -->
            <div
              v-if="isLoading"
              class="mt-3 h-[3px] w-full rounded-full bg-ledger-line/40 overflow-hidden"
              aria-hidden="true"
            >
              <div class="h-full bg-gold rounded-full animate-[typing_0.9s_ease-in-out_infinite] motion-reduce:animate-none motion-reduce:w-full"></div>
            </div>
          </div>

          <p class="text-[11px] text-ink-faded mt-2 px-1">
            Tekan Enter atau klik Cari untuk memeriksa arsip.
          </p>
        </form>
      </section>

      <!-- Hasil Pencarian -->
      <section v-if="hasSearched">
        <!-- STATE: DITEMUKAN -->
        <div v-if="student" class="relative animate-[fadeUp_0.5s_ease-out_both]">
          <LedgerCard class="relative pt-8 p-6 md:p-8 bg-paper-card border-2 border-ledger-line shadow-md rounded-lg hover:shadow-lg transition-shadow duration-300">
            <!-- Aksen Binder Arsip -->
            <div class="absolute top-0 left-8 flex gap-6" aria-hidden="true">
              <span class="w-3 h-3 rounded-full bg-paper border border-ledger-line shadow-[inset_0_1px_2px_rgba(15,42,74,0.25)] -translate-y-1/2"></span>
              <span class="w-3 h-3 rounded-full bg-paper border border-ledger-line shadow-[inset_0_1px_2px_rgba(15,42,74,0.25)] -translate-y-1/2"></span>
            </div>

            <span class="absolute top-3 right-6 font-mono text-[11px] text-ink-faded/70" aria-hidden="true">
              No. {{ entryNumber }}
            </span>

            <!-- Stempel -->
            <Transition appear name="stamp">
              <div class="absolute -top-3 right-4 md:right-8 z-20 rotate-[-8deg]" aria-hidden="true">
                <StampBadge />
              </div>
            </Transition>

            <div class="flex flex-col md:flex-row items-center md:items-start gap-6 md:gap-8 mt-2">
              <!-- Foto Interaktif -->
              <div class="shrink-0 group/photo cursor-crosshair">
                <div class="p-2 bg-paper border-2 border-ledger-line rounded-md shadow-sm rotate-[-2deg] transition-all duration-300 ease-out group-hover/photo:rotate-0 group-hover/photo:-translate-y-2 group-hover/photo:shadow-xl">
                  
                  <!-- Pita Isolasi Aksen -->
                  <div class="absolute -top-2 -left-2 w-10 h-4 bg-amber-200/50 border border-amber-300/40 rotate-[-15deg] z-10 backdrop-blur-[1px]"></div>

                  <img
                    v-if="!photoFailed"
                    :src="student.drive_link_preview"
                    :alt="`Foto ${student.nama}`"
                    class="w-40 h-52 object-cover rounded-sm border border-ledger-line/30 bg-paper filter group-hover/photo:contrast-105 transition-all"
                    loading="lazy"
                    @error="photoFailed = true"
                  />
                  <div
                    v-else
                    class="w-40 h-52 flex flex-col items-center justify-center gap-2 rounded-sm border border-dashed border-ledger-line/60 bg-paper text-center px-3"
                  >
                    <span class="font-mono text-[10px] text-ink-faded uppercase tracking-wide">Foto tidak bisa dimuat</span>
                  </div>
                  <p class="mt-1.5 text-center text-[10px] font-mono text-ink-faded uppercase tracking-widest border-t border-dashed border-ledger-line/50 pt-1">
                    Pas Foto Resmi
                  </p>
                </div>
              </div>

              <!-- Detail Siswa -->
              <div class="flex-1 w-full space-y-4">
                <div class="border-b-2 border-ledger-line pb-3 flex items-start justify-between gap-3">
                  <div>
                    <span class="text-[10px] font-mono font-bold text-gold uppercase tracking-wider block mb-1">
                      Lembar Arsip Individu
                    </span>
                    <h2 class="text-xl md:text-2xl font-bold text-ink-navy tracking-tight mt-0.5">
                      {{ student.nama }}
                    </h2>
                  </div>

                  <button
                    @click="copyNis"
                    type="button"
                    :aria-label="copied ? 'NIS tersalin' : 'Salin NIS'"
                    class="font-mono text-[11px] px-2.5 py-1.5 bg-paper border border-ledger-line rounded-md hover:bg-paper-card active:scale-95 transition-all text-ink-navy flex items-center gap-1.5 shrink-0"
                  >
                    <svg v-if="!copied" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                      <rect x="9" y="9" width="12" height="12" rx="1.5"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/>
                    </svg>
                    <svg v-else width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                      <path d="M20 6L9 17l-5-5"/>
                    </svg>
                    <span>{{ copied ? 'Tersalin' : 'Salin NIS' }}</span>
                  </button>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div class="bg-paper p-3 rounded-md border border-ledger-line/60 shadow-[inset_0_1px_3px_rgba(0,0,0,0.02)]">
                    <span class="text-[10px] font-mono text-ink-faded uppercase tracking-wider block mb-1">Kelas</span>
                    <span class="text-base font-semibold text-ink-navy">{{ student.kelas }}</span>
                  </div>
                  <div class="bg-paper p-3 rounded-md border border-ledger-line/60 shadow-[inset_0_1px_3px_rgba(0,0,0,0.02)]">
                    <span class="text-[10px] font-mono text-ink-faded uppercase tracking-wider block mb-1">Nomor Induk</span>
                    <span class="text-base font-semibold text-ink-navy font-mono">{{ student.nis }}</span>
                  </div>
                </div>

                <a
                  :href="student.drive_link_original"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center justify-center w-full gap-2 px-6 py-3 mt-1 border-2 border-ink-navy text-ink-navy font-mono font-bold text-xs uppercase tracking-wider rounded-lg hover:bg-ink-navy hover:text-paper-card hover:-translate-y-0.5 hover:shadow-[0_4px_0_0_#0f2a4a] active:translate-y-0.5 active:shadow-none transition-all"
                >
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M12 3v12m0 0l-4-4m4 4l4-4M4 21h16"/>
                  </svg>
                  <span>Unduh Resolusi Tinggi</span>
                </a>
              </div>
            </div>
          </LedgerCard>
        </div>

        <!-- STATE: TIDAK DITEMUKAN -->
        <LedgerCard v-else class="p-6 md:p-8 bg-paper-card border-2 border-ledger-line rounded-lg animate-[fadeUp_0.4s_ease-out_both]">
          <div class="flex flex-col md:flex-row items-center gap-6">
            <div class="shrink-0 w-32 h-32 md:w-40 md:h-52 rounded-md border-2 border-dashed border-ledger-line/70 flex items-center justify-center bg-paper shadow-inner" aria-hidden="true">
              <span class="font-mono text-4xl text-ink-faded/30">?</span>
            </div>
            <div class="flex-1 text-center md:text-left">
              <span class="text-[10px] font-mono font-bold text-stamp-red uppercase tracking-wider block mb-1">
                Laporan Sistem
              </span>
              <h3 class="text-lg font-bold text-ink-navy">
                Berkas Tidak Ditemukan
              </h3>
              <p class="text-sm text-ink-faded mt-2 leading-relaxed">
                Pencarian untuk NIS 
                <span class="font-mono font-bold text-ink-navy bg-paper px-1 rounded">{{ searchedNis }}</span>
                tidak membuahkan hasil. Silakan periksa kembali angka yang dimasukkan atau hubungi Tata Usaha (TU).
              </p>
            </div>
          </div>
        </LedgerCard>
      </section>
    </main>

    <!-- Footer -->
    <footer class="max-w-2xl mx-auto w-full pt-6 border-t border-ledger-line/60 text-center text-[11px] font-mono text-ink-faded/80 relative z-10">
      <p>&copy; {{ new Date().getFullYear() }} Tata Usaha SMA Negeri 1 Turen </p>
    </footer>
  </div>
</template>

<style scoped>
@keyframes typing {
  0% { width: 0%; }
  50% { width: 100%; }
  100% { width: 0%; }
}

.stamp-enter-active {
  animation: stampIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
@keyframes stampIn {
  0% { transform: scale(1.6) rotate(-20deg); opacity: 0; }
  100% { transform: scale(1) rotate(-8deg); opacity: 1; }
}

/* Animasi untuk konten */
@keyframes fadeUp {
  0% { transform: translateY(15px); opacity: 0; }
  100% { transform: translateY(0); opacity: 1; }
}

/* Animasi Splash Screen (Tanpa lingkaran/riak) */
@keyframes sealIn {
  0% { transform: scale(0.6); opacity: 0; filter: blur(2px); }
  60% { transform: scale(1.05); opacity: 1; filter: blur(0px); }
  100% { transform: scale(1); opacity: 1; }
}

.splash-leave-active {
  transition: opacity 0.5s cubic-bezier(0.4, 0, 0.2, 1), transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
.splash-leave-to {
  opacity: 0;
  transform: scale(1.05);
}

@media (prefers-reduced-motion: reduce) {
  .stamp-enter-active,
  .splash-leave-active,
  .animate-\[fadeUp_0\.4s_ease-out_both\],
  .animate-\[fadeUp_0\.5s_ease-out_both\],
  .animate-\[fadeUp_0\.6s_ease-out_0\.3s_both\],
  .animate-\[fadeUp_0\.6s_ease-out_0\.5s_both\],
  .animate-\[fadeUp_0\.6s_ease-out_0\.8s_both\] {
    animation: none !important;
    transition: opacity 0.2s linear !important;
    transform: none !important;
  }
}
</style>