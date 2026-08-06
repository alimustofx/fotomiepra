<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  logs: Object,
});

const form = useForm({
  file: null,
});

const selectedErrorLog = ref(null);

const handleFileChange = (e) => {
  form.file = e.target.files[0];
};

const submitImport = () => {
  if (!form.file) return;

  form.post(route('photos.import.store'), {
    onSuccess: () => {
      form.reset();
      // Reset input file secara manual agar UI kembali ke state awal
      const fileInput = document.getElementById('excel-file');
      if (fileInput) fileInput.value = '';
    },
  });
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(new Date(dateString));
};

const openErrorModal = (log) => {
  selectedErrorLog.value = log;
};
</script>

<template>
  <Head title="Impor Data Excel — Arsip Siswa" />

  <AdminLayout>
    <div class="space-y-8 animate-[fadeUp_0.5s_ease-out_both] pb-10">
      
      <!-- Header Area -->
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b-2 border-ledger-line pb-5">
        <div>
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-ink-navy text-paper-card text-[10px] font-mono font-bold uppercase tracking-widest rounded-sm mb-3 shadow-sm">
            Modul Pemasukan Data
          </span>
          <h2 class="text-2xl md:text-3xl font-black text-ink-navy tracking-tight font-sans">
            Impor Massal Arsip
          </h2>
          <p class="text-xs md:text-sm text-ink-faded mt-1.5 font-mono">
            Unggah dokumen Excel untuk memproses dan meregistrasi banyak data sekaligus.
          </p>
        </div>
      </div>

      <!-- Section Form Upload & Petunjuk Format -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Form Upload Box -->
        <div class="md:col-span-2 bg-paper-card p-6 md:p-8 rounded-xl border-2 border-ledger-line shadow-[4px_4px_0px_0px_#1F3A52] relative">
          <div class="absolute top-0 right-8 w-8 h-4 bg-paper border-x-2 border-b-2 border-ledger-line rounded-b-sm shadow-inner" aria-hidden="true"></div>
          
          <h3 class="text-sm font-mono font-bold text-ink-navy uppercase tracking-wider border-b-2 border-ledger-line pb-2 mb-6">
            📥 Baki Unggah Berkas (.xlsx / .csv)
          </h3>

          <form @submit.prevent="submitImport" class="space-y-6">
            <!-- Dropzone Area -->
            <div class="relative group">
              <!-- FIX: z-[100] memastikan input selalu di layer paling atas -->
              <input
                type="file"
                id="excel-file"
                accept=".xlsx, .xls, .csv"
                @change="handleFileChange"
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-[100]"
                required
              />
              
              <!-- FIX: Tambahan pointer-events-none agar visual di bawah tidak memblokir klik kursor ke input -->
              <div class="border-2 border-dashed border-ledger-line/70 rounded-lg p-10 text-center bg-paper group-hover:bg-ink-navy/[0.03] transition-colors flex flex-col items-center justify-center min-h-[200px] relative overflow-hidden pointer-events-none">
                
                <!-- Aksen Garis Blueprint -->
                <div class="absolute inset-0 opacity-[0.02] bg-[linear-gradient(to_right,#1f3a52_1px,transparent_1px),linear-gradient(to_bottom,#1f3a52_1px,transparent_1px)] bg-[size:20px_20px]" aria-hidden="true"></div>

                <div v-if="!form.file" class="space-y-3 relative z-10">
                  <div class="w-16 h-16 mx-auto bg-paper-card border-2 border-ledger-line shadow-sm flex items-center justify-center rotate-[-5deg] group-hover:rotate-0 transition-transform duration-300">
                    <span class="text-2xl text-ink-faded">📄</span>
                  </div>
                  <p class="text-xs font-mono font-bold text-ink-navy uppercase tracking-wider mt-2">
                    Jatuhkan Dokumen Di Sini
                  </p>
                  <p class="text-[10px] font-sans text-ink-faded px-8">
                    Atau klik area ini untuk mencari file secara manual dari komputer Anda. (Maks 10MB)
                  </p>
                </div>
                
                <!-- State ketika file dipilih -->
                <div v-else class="space-y-3 relative z-10 w-full px-4">
                  <div class="w-16 h-16 mx-auto bg-emerald-100 border-2 border-emerald-800 text-emerald-800 shadow-sm flex items-center justify-center">
                    <span class="text-2xl font-mono font-bold">✓</span>
                  </div>
                  <div class="bg-paper-card border-2 border-ledger-line p-3 rounded mt-4 text-left inline-block w-full max-w-sm mx-auto shadow-inner">
                    <p class="text-[10px] font-mono text-ink-faded uppercase tracking-widest mb-1 border-b border-dashed border-ledger-line/50 pb-1">File Tersorot:</p>
                    <p class="text-xs font-mono font-bold text-ink-navy truncate" :title="form.file.name">
                      {{ form.file.name }}
                    </p>
                    <p class="text-[10px] font-mono text-ink-faded mt-1">
                      Ukuran: {{ (form.file.size / 1024).toFixed(1) }} KB
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <p v-if="form.errors.file" class="text-[11px] font-mono font-bold text-stamp-red flex items-center gap-1.5 bg-stamp-red/10 border border-stamp-red/30 p-2 rounded">
              <span>⚠️</span> {{ form.errors.file }}
            </p>

            <div class="flex justify-end pt-2">
              <button
                type="submit"
                :disabled="form.processing || !form.file"
                class="bg-ink-navy text-paper-card border-2 border-ink-navy px-6 py-3 rounded-md text-[11px] font-mono font-bold uppercase tracking-[0.15em] hover:-translate-y-0.5 shadow-[4px_4px_0px_0px_#1F3A52] hover:shadow-[6px_6px_0px_0px_#1F3A52] active:translate-y-0.5 active:shadow-none transition-all disabled:opacity-50 flex items-center gap-2"
              >
                <span v-if="form.processing" class="inline-block animate-spin">⚙️</span>
                {{ form.processing ? 'Menyortir Data...' : 'Mulai Eksekusi Impor' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Box Panduan Format Kolom (Memo Style) -->
        <div class="bg-[#FDF6E3] p-6 rounded-xl border-2 border-amber-900/20 shadow-[4px_4px_0px_0px_#78350F20] relative h-fit transform rotate-[1deg]">
          <!-- Aksen Masking Tape -->
          <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-16 h-5 bg-amber-200/50 border border-amber-300/60 rotate-[-3deg] backdrop-blur-[1px] z-10" aria-hidden="true"></div>
          
          <h3 class="font-mono font-black text-amber-950 uppercase tracking-widest text-sm flex items-center gap-2 mb-4 border-b border-dashed border-amber-900/30 pb-2">
            <span>📌</span> Panduan Kolom
          </h3>
          
          <p class="text-[11px] text-amber-900/80 font-sans leading-relaxed mb-4">
            Sistem membaca baris pertama Excel sebagai <strong>Header</strong>. Pastikan penamaan kolom persis seperti berikut (huruf kecil semua):
          </p>
          
          <div class="bg-white/50 p-4 rounded border-2 border-amber-900/10 font-mono space-y-3 text-[11px]">
            <div class="flex flex-col border-b border-dashed border-amber-900/10 pb-2">
              <span class="font-bold text-amber-950 bg-amber-100 px-1 py-0.5 rounded w-fit mb-1">nis</span>
              <span class="text-amber-900/70">Nomor Induk (Wajib)</span>
            </div>
            <div class="flex flex-col border-b border-dashed border-amber-900/10 pb-2">
              <span class="font-bold text-amber-950 bg-amber-100 px-1 py-0.5 rounded w-fit mb-1">nama</span>
              <span class="text-amber-900/70">Nama Lengkap Siswa</span>
            </div>
            <div class="flex flex-col border-b border-dashed border-amber-900/10 pb-2">
              <span class="font-bold text-amber-950 bg-amber-100 px-1 py-0.5 rounded w-fit mb-1">kelas</span>
              <span class="text-amber-900/70">Contoh: 10 IPA 1</span>
            </div>
            <div class="flex flex-col">
              <span class="font-bold text-amber-950 bg-amber-100 px-1 py-0.5 rounded w-fit mb-1">drive_link</span>
              <span class="text-amber-900/70">URL G-Drive Viewer</span>
            </div>
          </div>
          
          <div class="mt-4 p-3 bg-amber-900/5 rounded border border-amber-900/10 text-[10px] font-mono text-amber-900/80 leading-relaxed">
            * <strong class="text-amber-950">Catatan Overwrite:</strong> Jika NIS sudah ada di arsip, sistem akan otomatis memperbarui datanya (Timpa data lama).
          </div>
        </div>
      </div>

      <!-- Section Tabel Histori Impor -->
      <div class="bg-paper-card p-6 md:p-8 rounded-xl border-2 border-ledger-line shadow-[4px_4px_0px_0px_#1F3A52] relative mt-8">
        <h3 class="text-sm font-mono font-bold text-ink-navy uppercase tracking-wider border-b-2 border-ledger-line pb-2 mb-6">
          📋 Buku Log Riwayat Impor
        </h3>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs border-collapse">
            <thead>
              <tr class="border-b-2 border-ledger-line text-ink-navy font-mono font-bold uppercase tracking-wider bg-paper/50 text-[10px]">
                <th class="py-3 px-4">Tgl & Waktu Masuk</th>
                <th class="py-3 px-4">Referensi Berkas</th>
                <th class="py-3 px-4">Petugas</th>
                <th class="py-3 px-4 text-center">T. Baris</th>
                <th class="py-3 px-4 text-center">Sukses</th>
                <th class="py-3 px-4 text-center">Gagal</th>
                <th class="py-3 px-4 text-right">Laporan Audit</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-dashed divide-ledger-line/60">
              <tr v-for="log in logs.data" :key="log.id" class="hover:bg-ink-navy/[0.02] transition-colors group">
                <td class="py-3 px-4 font-mono text-ink-faded/80 group-hover:text-ink-navy transition-colors">
                  {{ formatDate(log.created_at) }}
                </td>
                <td class="py-3 px-4 font-sans font-semibold text-ink-navy flex items-center gap-2">
                  <span>📄</span> {{ log.filename }}
                </td>
                <td class="py-3 px-4 font-mono text-[10px] text-ink-faded uppercase tracking-wider">
                  {{ log.user?.name || 'ADMIN-SYS' }}
                </td>
                <td class="py-3 px-4 font-mono text-center font-bold text-ink-navy bg-paper/50">
                  {{ log.total_rows }}
                </td>
                <td class="py-3 px-4 font-mono text-center font-bold text-emerald-700">
                  {{ log.success_rows }}
                </td>
                <td class="py-3 px-4 font-mono text-center font-bold" :class="log.failed_rows > 0 ? 'text-stamp-red bg-stamp-red/5' : 'text-ink-faded'">
                  {{ log.failed_rows > 0 ? log.failed_rows : '-' }}
                </td>
                <td class="py-3 px-4 text-right font-mono text-[10px] uppercase tracking-wider font-bold">
                  <button
                    v-if="log.failed_rows > 0"
                    @click="openErrorModal(log)"
                    class="text-stamp-red hover:bg-stamp-red hover:text-paper-card px-2 py-1 border border-stamp-red rounded transition-colors"
                  >
                    [Buka Log Error]
                  </button>
                  <span v-else class="text-emerald-700/70 border border-emerald-700/20 px-2 py-1 rounded bg-emerald-50">
                    [Aman]
                  </span>
                </td>
              </tr>
              <tr v-if="logs.data.length === 0">
                <td colspan="7" class="text-center py-10 text-ink-faded font-mono uppercase tracking-widest border-2 border-dashed border-ledger-line/50 m-4 rounded">
                  [ Belum ada aktivitas perekaman massal ]
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- MODAL LAPORAN ERROR (NEO-ANALOG/WARNING TICKET) -->
    <Transition name="modal">
      <div
        v-if="selectedErrorLog"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4"
      >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-ink-navy/60 backdrop-blur-sm transition-opacity" @click="selectedErrorLog = null" aria-hidden="true"></div>

        <!-- Box Dokumen Error -->
        <div class="bg-paper-card w-full max-w-xl p-6 md:p-8 border-4 border-stamp-red rounded-xl shadow-[8px_8px_0px_0px_#991B1B] relative z-10 animate-[fadeUp_0.3s_ease-out_both]">
          
          <!-- Isolasi & Stempel Peringatan -->
          <div class="absolute -top-4 left-1/2 -translate-x-1/2 w-20 h-6 bg-amber-200/60 border border-amber-300/60 rotate-[2deg] backdrop-blur-[1px]" aria-hidden="true"></div>
          <div class="absolute top-6 right-6 border-2 border-stamp-red text-stamp-red font-mono font-black uppercase text-[10px] tracking-widest px-2 py-1 rotate-[15deg] opacity-80 pointer-events-none">
            AUDIT GAGAL
          </div>

          <div class="border-b-2 border-stamp-red/30 pb-4 mb-5">
            <h3 class="font-black text-xl text-stamp-red font-sans tracking-tight">
              Laporan Anomali Data
            </h3>
            <p class="text-[11px] font-mono text-ink-faded mt-1 flex items-center gap-2">
              <span class="bg-paper px-1 border border-ledger-line rounded">File: {{ selectedErrorLog.filename }}</span>
            </p>
          </div>

          <!-- Kertas Log ala Mesin Tik -->
          <div class="bg-paper p-4 rounded-md border-2 border-stamp-red/50 relative shadow-inner">
            <div class="absolute inset-0 bg-[linear-gradient(transparent_50%,rgba(153,27,27,0.03)_50%)] bg-[length:100%_4px] pointer-events-none"></div>
            
            <p class="text-[10px] font-mono font-bold text-stamp-red mb-3 uppercase tracking-widest relative z-10">
              Detail Baris Bermasalah:
            </p>

            <div class="max-h-[40vh] overflow-y-auto space-y-1.5 pr-2 custom-scrollbar relative z-10">
              <div
                v-for="(err, idx) in selectedErrorLog.error_details"
                :key="idx"
                class="text-[11px] font-mono text-ink-navy border-l-2 border-stamp-red pl-2 py-0.5 bg-stamp-red/5"
              >
                <span class="font-bold text-stamp-red mr-1">></span> {{ err }}
              </div>
            </div>
          </div>

          <div class="mt-6 flex justify-end">
            <button
              @click="selectedErrorLog = null"
              class="px-6 py-2.5 bg-paper text-stamp-red border-2 border-stamp-red rounded-md text-[11px] font-mono font-bold uppercase tracking-wider hover:bg-stamp-red hover:text-paper-card transition-colors shadow-[4px_4px_0px_0px_#991B1B] hover:translate-y-0.5 hover:shadow-[2px_2px_0px_0px_#991B1B] active:translate-y-1 active:shadow-none"
            >
              Tutup Berkas
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </AdminLayout>
</template>

<style scoped>
/* Transisi Muncul Utama */
@keyframes fadeUp {
  0% { transform: translateY(15px); opacity: 0; }
  100% { transform: translateY(0); opacity: 1; }
}

/* Transisi Modal */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease, transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(10px);
}
.modal-leave-active .bg-paper-card {
  transform: scale(0.95);
  opacity: 0;
  transition: all 0.2s ease;
}

/* Kustomisasi Scrollbar untuk Modal Error agar terlihat klasik */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #EFE7D6; 
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #991B1B; 
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #7F1D1D; 
}

@media (prefers-reduced-motion: reduce) {
  .animate-\[fadeUp_0\.5s_ease-out_both\],
  .animate-\[fadeUp_0\.3s_ease-out_both\] {
    animation: none !important;
    transition: opacity 0.2s linear !important;
    transform: none !important;
  }
}
</style>