<script setup>
import { ref, watch } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  photos: Object,
  classes: Array,
  filters: Object,
});

const search = ref(props.filters.search || '');
const selectedKelas = ref(props.filters.kelas || '');

// Form Modal Tambah / Edit
const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
  nis: '',
  nama: '',
  kelas: '',
  drive_link: '',
});

// Search & Filter Handler
const applyFilter = () => {
  router.get(
    route('photos.index'),
    { search: search.value, kelas: selectedKelas.value },
    { preserveState: true, replace: true }
  );
};

const resetFilter = () => {
  search.value = '';
  selectedKelas.value = '';
  applyFilter();
};

// Modal Operations
const openCreateModal = () => {
  isEditing.value = false;
  editingId.value = null;
  form.reset();
  form.clearErrors();
  showModal.value = true;
};

const openEditModal = (photo) => {
  isEditing.value = true;
  editingId.value = photo.id;
  form.clearErrors();
  form.nis = photo.nis;
  form.nama = photo.nama;
  form.kelas = photo.kelas;
  form.drive_link = photo.drive_link_original;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  form.reset();
};

const submitForm = () => {
  if (isEditing.value) {
    form.put(route('photos.update', editingId.value), {
      onSuccess: () => closeModal(),
    });
  } else {
    form.post(route('photos.store'), {
      onSuccess: () => closeModal(),
    });
  }
};

// ==========================================
// CUSTOM DELETE CONFIRMATION LOGIC (NEO-ANALOG)
// ==========================================
const showDeleteModal = ref(false);
const photoToDelete = ref(null);
const isDeleting = ref(false);

const confirmDelete = (photo) => {
  photoToDelete.value = photo;
  showDeleteModal.value = true;
};

const executeDelete = () => {
  if (!photoToDelete.value) return;
  
  isDeleting.value = true;
  
  router.delete(route('photos.destroy', photoToDelete.value.id), {
    preserveScroll: true,
    onFinish: () => {
      isDeleting.value = false;
    },
    onSuccess: () => {
      showDeleteModal.value = false;
      photoToDelete.value = null;
    }
  });
};

const cancelDelete = () => {
  showDeleteModal.value = false;
  photoToDelete.value = null;
};
</script>

<template>
  <Head title="Kelola Data Foto — Arsip Siswa" />

  <AdminLayout>
    <div class="space-y-8 animate-[fadeUp_0.5s_ease-out_both] pb-10">
      
      <!-- Top Action Bar -->
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b-2 border-ledger-line pb-5">
        <div>
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-ink-navy text-paper-card text-[10px] font-mono font-bold uppercase tracking-widest rounded-sm mb-3 shadow-sm">
            Tabel Direktori
          </span>
          <h2 class="text-2xl md:text-3xl font-black text-ink-navy tracking-tight font-sans">
            Data Foto Siswa
          </h2>
          <p class="text-xs md:text-sm text-ink-faded mt-1.5 font-mono">
            Kelola entri arsip foto dan tautan referensi awan (Google Drive).
          </p>
        </div>
        <button
          @click="openCreateModal"
          class="bg-ink-navy text-paper-card px-5 py-2.5 rounded text-xs font-mono font-bold uppercase tracking-wider hover:-translate-y-0.5 shadow-[4px_4px_0px_0px_#1F3A52] hover:shadow-[6px_6px_0px_0px_#1F3A52] active:translate-y-0.5 active:shadow-none transition-all flex items-center justify-center gap-2 border-2 border-ink-navy"
        >
          <span>+ Tambah Entri</span>
        </button>
      </div>

      <!-- Search & Filter Bar -->
      <div class="relative bg-paper-card p-5 rounded-xl border-2 border-ledger-line shadow-sm">
        <div class="absolute -top-3 left-6 bg-paper px-2 border-2 border-ledger-line rounded-sm text-[9px] font-mono font-bold text-ink-navy uppercase tracking-widest shadow-sm">
          KRITERIA PENCARIAN
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mt-2">
          <div class="md:col-span-5">
            <input
              v-model="search"
              @keyup.enter="applyFilter"
              type="text"
              placeholder="Cari NIS atau Nama Siswa..."
              class="w-full bg-paper border-2 border-ledger-line rounded-md text-xs font-mono px-4 py-2.5 text-ink-navy placeholder:font-sans placeholder-ink-faded/50 focus:outline-none focus:border-ink-navy focus:shadow-[2px_2px_0px_0px_#1F3A52] transition-all"
            />
          </div>
          <div class="md:col-span-4">
            <select
              v-model="selectedKelas"
              @change="applyFilter"
              class="w-full bg-paper border-2 border-ledger-line rounded-md text-xs font-mono font-bold px-4 py-2.5 text-ink-navy focus:outline-none focus:border-ink-navy focus:shadow-[2px_2px_0px_0px_#1F3A52] transition-all appearance-none cursor-pointer bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M7%2010L12%2015L17%2010%22%20stroke%3D%22%231F3A52%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E')] bg-[position:right_8px_center] bg-no-repeat"
            >
              <option value="">[ SEMUA KELAS ]</option>
              <option v-for="c in classes" :key="c" :value="c">{{ c }}</option>
            </select>
          </div>
          <div class="md:col-span-3 flex space-x-2">
            <button
              @click="applyFilter"
              class="bg-ink-navy text-paper-card px-4 py-2.5 rounded-md text-xs font-mono font-bold uppercase tracking-wider flex-1 hover:bg-ink-navy/90 active:scale-95 transition-all"
            >
              Terapkan
            </button>
            <button
              v-if="search || selectedKelas"
              @click="resetFilter"
              class="border-2 border-ledger-line text-ink-faded font-mono font-bold uppercase tracking-wider px-4 py-2.5 rounded-md text-[10px] hover:bg-ledger-line/10 active:scale-95 transition-all"
            >
              Reset
            </button>
          </div>
        </div>
      </div>

      <!-- Main Data Table -->
      <div class="bg-paper-card rounded-xl border-2 border-ledger-line overflow-hidden shadow-[4px_4px_0px_0px_#1F3A52] relative">
        <!-- Aksen garis header tabel -->
        <div class="absolute top-0 inset-x-0 h-1 bg-ink-navy z-10"></div>
        
        <div class="overflow-x-auto mt-1">
          <table class="w-full text-left text-xs border-collapse">
            <thead>
              <tr class="border-b-2 border-ledger-line text-ink-navy font-mono font-bold uppercase tracking-wider bg-paper/50">
                <th class="py-4 px-5">Pasfoto</th>
                <th class="py-4 px-5">Nomor Induk (NIS)</th>
                <th class="py-4 px-5">Nama Lengkap</th>
                <th class="py-4 px-5">Kelas</th>
                <th class="py-4 px-5">Referensi Berkas</th>
                <th class="py-4 px-5 text-right">Tindakan</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-dashed divide-ledger-line/60">
              <tr
                v-for="photo in photos.data"
                :key="photo.id"
                class="hover:bg-ink-navy/[0.02] transition-colors group"
              >
                <td class="py-3 px-5">
                  <div class="p-1 bg-paper border border-ledger-line shadow-sm inline-block rotate-[-2deg] group-hover:rotate-0 transition-transform">
                    <img
                      :src="photo.drive_link_preview"
                      :alt="photo.nama"
                      class="w-10 h-12 object-cover border border-ledger-line/50"
                      loading="lazy"
                      @error="(e) => e.target.src = 'https://placehold.co/40x48/EFE7D6/1F3A52?text=?'"
                    />
                  </div>
                </td>
                <td class="py-3 px-5 font-mono font-bold text-ink-navy">{{ photo.nis }}</td>
                <td class="py-3 px-5 font-sans font-semibold text-ink-navy">{{ photo.nama }}</td>
                <td class="py-3 px-5 font-mono font-bold text-ink-faded">{{ photo.kelas }}</td>
                <td class="py-3 px-5 font-mono text-[10px] text-ink-faded max-w-xs truncate">
                  <a
                    :href="photo.drive_link_original"
                    target="_blank"
                    class="hover:text-ink-navy hover:underline decoration-ink-navy transition-colors flex items-center gap-1.5"
                    title="Buka Dokumen Asli"
                  >
                    <span>🔗</span> {{ photo.drive_file_id || 'Buka Tautan' }}
                  </a>
                </td>
                <td class="py-3 px-5 text-right font-mono text-[11px] font-bold tracking-wider uppercase space-x-3">
                  <button
                    @click="openEditModal(photo)"
                    class="text-ink-navy/70 hover:text-ink-navy hover:underline decoration-2 underline-offset-2 transition-colors"
                  >
                    Edit
                  </button>
                  <button
                    @click="confirmDelete(photo)"
                    class="text-stamp-red/80 hover:text-stamp-red hover:underline decoration-2 underline-offset-2 transition-colors"
                  >
                    Hapus
                  </button>
                </td>
              </tr>
              <tr v-if="photos.data.length === 0">
                <td colspan="6" class="text-center py-12 text-ink-faded font-mono uppercase tracking-widest border-2 border-dashed border-ledger-line/50 m-4">
                  [ Laci Arsip Kosong ]
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="photos.links.length > 3" class="p-4 bg-paper/50 border-t-2 border-ledger-line flex flex-col sm:flex-row justify-between items-center gap-4 text-[11px] font-mono font-bold uppercase tracking-wider">
          <span class="text-ink-faded">
            Halaman: {{ photos.from || 0 }} - {{ photos.to || 0 }} dari {{ photos.total }}
          </span>
          <div class="flex flex-wrap space-x-1.5">
            <Link
              v-for="(link, i) in photos.links"
              :key="i"
              :href="link.url || '#'"
              v-html="link.label"
              :class="[
                'px-3 py-1.5 rounded-sm border-2 transition-all',
                link.active ? 'bg-ink-navy text-paper-card border-ink-navy shadow-[2px_2px_0px_0px_#1F3A52]' : 'bg-paper border-ledger-line text-ink-navy hover:bg-paper-card hover:shadow-[2px_2px_0px_0px_#1F3A52]',
                !link.url ? 'opacity-40 cursor-not-allowed hover:shadow-none' : ''
              ]"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL FORM TAMBAH / EDIT (NEO-ANALOG) -->
    <Transition name="modal">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-ink-navy/40 backdrop-blur-sm transition-opacity" @click="closeModal" aria-hidden="true"></div>

        <!-- Kartu Form -->
        <div class="bg-paper-card border-2 border-ledger-line rounded-xl w-full max-w-lg p-6 shadow-[8px_8px_0px_0px_#1F3A52] relative z-10 animate-[fadeUp_0.3s_ease-out_both]">
          
          <div class="absolute -top-3 right-6 w-12 h-4 bg-amber-200/60 border border-amber-300/60 rotate-[6deg] backdrop-blur-[1px]" aria-hidden="true"></div>

          <div class="border-b-2 border-ledger-line pb-4 mb-5 flex justify-between items-start">
            <div>
              <h3 class="font-black text-xl text-ink-navy font-sans tracking-tight">
                {{ isEditing ? 'Perbarui Entri Dokumen' : 'Registrasi Arsip Baru' }}
              </h3>
              <p class="text-[10px] font-mono text-ink-faded uppercase tracking-widest mt-1">Formulir Perekaman Data</p>
            </div>
            <button @click="closeModal" class="w-8 h-8 flex items-center justify-center border-2 border-ledger-line rounded hover:bg-stamp-red hover:border-stamp-red hover:text-paper-card text-ink-navy transition-colors font-mono font-bold">
              ✕
            </button>
          </div>

          <form @submit.prevent="submitForm" class="space-y-4 text-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block font-mono font-bold uppercase text-[10px] text-ink-faded tracking-wider mb-1.5">Nomor Induk (NIS)</label>
                <input
                  v-model="form.nis"
                  type="text"
                  placeholder="Contoh: 8888"
                  class="w-full bg-paper border-2 border-ledger-line rounded-md font-mono p-2.5 text-ink-navy focus:border-ink-navy focus:shadow-[2px_2px_0px_0px_#1F3A52] focus:outline-none transition-all"
                  required
                />
                <p v-if="form.errors.nis" class="text-stamp-red mt-1 font-mono text-[10px] font-bold">⚠️ {{ form.errors.nis }}</p>
              </div>

              <div>
                <label class="block font-mono font-bold uppercase text-[10px] text-ink-faded tracking-wider mb-1.5">Kelas</label>
                <input
                  v-model="form.kelas"
                  type="text"
                  placeholder="Contoh: 10 IPA 1"
                  class="w-full bg-paper border-2 border-ledger-line rounded-md font-mono p-2.5 text-ink-navy focus:border-ink-navy focus:shadow-[2px_2px_0px_0px_#1F3A52] focus:outline-none transition-all uppercase"
                  required
                />
                <p v-if="form.errors.kelas" class="text-stamp-red mt-1 font-mono text-[10px] font-bold">⚠️ {{ form.errors.kelas }}</p>
              </div>
            </div>

            <div>
              <label class="block font-mono font-bold uppercase text-[10px] text-ink-faded tracking-wider mb-1.5">Nama Lengkap Siswa</label>
              <input
                v-model="form.nama"
                type="text"
                class="w-full bg-paper border-2 border-ledger-line rounded-md font-sans font-medium p-2.5 text-ink-navy focus:border-ink-navy focus:shadow-[2px_2px_0px_0px_#1F3A52] focus:outline-none transition-all"
                required
              />
              <p v-if="form.errors.nama" class="text-stamp-red mt-1 font-mono text-[10px] font-bold">⚠️ {{ form.errors.nama }}</p>
            </div>

            <div>
              <label class="block font-mono font-bold uppercase text-[10px] text-ink-faded tracking-wider mb-1.5 flex items-center justify-between">
                <span>Tautan Referensi (G-Drive)</span>
                <span class="bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded border border-amber-300 text-[8px]">Viewer Only</span>
              </label>
              <input
                v-model="form.drive_link"
                type="url"
                placeholder="https://drive.google.com/file/d/..."
                class="w-full bg-paper border-2 border-ledger-line rounded-md font-mono text-xs p-2.5 text-ink-navy focus:border-ink-navy focus:shadow-[2px_2px_0px_0px_#1F3A52] focus:outline-none transition-all"
                :required="!isEditing"
              />
              <p v-if="form.errors.drive_link" class="text-stamp-red mt-1 font-mono text-[10px] font-bold">⚠️ {{ form.errors.drive_link }}</p>
            </div>

            <div class="pt-6 mt-6 border-t-2 border-dashed border-ledger-line flex justify-end gap-3">
              <button
                type="button"
                @click="closeModal"
                class="px-5 py-2.5 border-2 border-ledger-line rounded-md text-[11px] font-mono font-bold uppercase tracking-wider text-ink-faded hover:text-ink-navy hover:bg-paper transition-colors"
              >
                Batalkan
              </button>
              <button
                type="submit"
                :disabled="form.processing"
                class="px-6 py-2.5 bg-ink-navy border-2 border-ink-navy text-paper-card rounded-md text-[11px] font-mono font-bold uppercase tracking-[0.15em] hover:-translate-y-0.5 shadow-[4px_4px_0px_0px_#1F3A52] hover:shadow-[6px_6px_0px_0px_#1F3A52] active:translate-y-0.5 active:shadow-none transition-all disabled:opacity-50 disabled:pointer-events-none flex items-center gap-2"
              >
                <span v-if="form.processing" class="inline-block animate-spin">⚙️</span>
                {{ form.processing ? 'Menyimpan...' : 'Stempel & Simpan' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>

    <!-- MODAL KONFIRMASI HAPUS (NEO-ANALOG) -->
    <Transition name="modal">
      <div v-if="showDeleteModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-ink-navy/60 backdrop-blur-sm transition-opacity" @click="cancelDelete" aria-hidden="true"></div>

        <div class="bg-paper-card w-full max-w-sm p-6 border-2 border-ledger-line rounded-xl shadow-[8px_8px_0px_0px_#991B1B] relative z-10">
          
          <!-- Isolasi & Label Peringatan -->
          <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-14 h-4 bg-amber-200/60 border border-amber-300/60 rotate-[-3deg] backdrop-blur-[1px]" aria-hidden="true"></div>
          <div class="absolute -top-3 -left-2 bg-paper px-2 py-0.5 border-2 border-stamp-red rounded-sm text-[9px] font-mono font-bold text-stamp-red uppercase tracking-widest shadow-sm rotate-[-8deg]">
            PERINGATAN :: HAPUS
          </div>

          <div class="flex items-start gap-4 mt-2">
            <div class="w-10 h-10 shrink-0 bg-stamp-red/10 border-2 border-stamp-red rounded-md flex items-center justify-center text-stamp-red font-mono font-black text-xl rotate-[5deg]">
              !
            </div>
            <div>
              <h4 class="text-sm font-mono font-bold text-ink-navy uppercase tracking-wider mb-1 leading-tight">
                Hancurkan Dokumen?
              </h4>
              <p class="text-[11px] font-sans text-ink-faded leading-relaxed">
                Entri untuk NIS <span class="font-mono font-bold text-ink-navy bg-paper border border-ledger-line px-1 rounded">{{ photoToDelete?.nis }}</span> atas nama <strong class="text-ink-navy">{{ photoToDelete?.nama }}</strong> akan dihancurkan dari arsip secara permanen.
              </p>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t-2 border-dashed border-ledger-line/50">
            <button 
              @click="cancelDelete" 
              :disabled="isDeleting"
              class="px-4 py-2 text-[10px] font-mono font-bold uppercase tracking-wider text-ink-faded hover:text-ink-navy transition-colors disabled:opacity-50"
            >
              Kembalikan
            </button>
            <button 
              @click="executeDelete" 
              :disabled="isDeleting"
              class="px-4 py-2 bg-stamp-red text-paper-card text-[10px] font-mono font-bold uppercase tracking-[0.1em] rounded-md border-2 border-stamp-red hover:-translate-y-0.5 shadow-[4px_4px_0px_0px_#991B1B] hover:shadow-[6px_6px_0px_0px_#991B1B] active:translate-y-0.5 active:shadow-none transition-all disabled:opacity-50 flex items-center gap-2"
            >
              <span v-if="isDeleting" class="inline-block animate-spin">⚙️</span>
              {{ isDeleting ? 'Menghancurkan...' : 'Ya, Hancurkan' }}
            </button>
          </div>
          
        </div>
      </div>
    </Transition>

  </AdminLayout>
</template>

<style scoped>
/* Animasi Muncul ke Atas */
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

@media (prefers-reduced-motion: reduce) {
  .animate-\[fadeUp_0\.3s_ease-out_both\] {
    animation: none !important;
    transition: opacity 0.2s linear !important;
    transform: none !important;
  }
}
</style>