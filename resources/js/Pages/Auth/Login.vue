<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Otorisasi Admin — Arsip Siswa" />

    <div class="min-h-screen bg-paper text-ink-navy font-sans antialiased flex flex-col justify-center items-center p-4 sm:p-6 relative selection:bg-ink-navy selection:text-paper-card overflow-hidden">
        
        <!-- Tekstur kertas ledger (Background Latar) -->
        <div class="fixed inset-0 pointer-events-none opacity-60 bg-[repeating-linear-gradient(to_bottom,transparent,transparent_31px,#0f2a4a10_31px,#0f2a4a10_32px)]"></div>
        <div class="fixed top-0 inset-x-0 h-[3px] bg-gradient-to-r from-gold/0 via-gold/70 to-gold/0 pointer-events-none"></div>
        <div class="fixed inset-y-0 left-10 sm:left-14 w-px bg-ink-navy/[0.04] pointer-events-none hidden md:block"></div>

        <div class="w-full max-w-md relative z-10 animate-[fadeUp_0.5s_ease-out_both]">
            
            <!-- Header Logo & Judul -->
            <div class="text-center mb-8 flex flex-col items-center group cursor-default">
                <img
                    src="/images/logo-turen-seal.svg"
                    alt="Lambang SMA Negeri 1 Turen"
                    class="w-16 h-16 md:w-20 md:h-20 object-contain drop-shadow-sm mb-4 transition-transform duration-500 group-hover:rotate-[15deg]"
                />
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-ink-navy leading-tight uppercase font-mono">
                    Portal Admin
                </h1>
                <p class="text-[11px] text-ink-faded mt-1.5 font-mono uppercase tracking-[0.2em]">
                    Otorisasi Terbatas
                </p>
            </div>

            <!-- Pesan Status (Jika ada) -->
            <div v-if="status" class="mb-4 p-3 bg-emerald-100/50 border-2 border-emerald-500/30 text-emerald-800 text-xs font-mono font-bold uppercase tracking-wider rounded-md text-center">
                {{ status }}
            </div>

            <!-- Kartu Login -->
            <div class="bg-paper-card border-2 border-ledger-line rounded-lg shadow-lg p-6 sm:p-8 relative hover:shadow-xl transition-shadow duration-300">
                
                <!-- Aksen Pita Isolasi (Kanan Atas) -->
                <div class="absolute -top-3 -right-3 w-14 h-4 bg-amber-200/50 border border-amber-300/40 rotate-[12deg] z-10 backdrop-blur-[1px]" aria-hidden="true"></div>
                
                <!-- Aksen Tag Arsip (Kiri Atas) -->
                <div class="absolute -top-3 left-6 bg-paper px-2 border-2 border-ledger-line rounded-sm text-[9px] font-mono font-bold text-ink-navy uppercase tracking-widest shadow-sm" aria-hidden="true">
                    FORM::AUTH-01
                </div>

                <form @submit.prevent="submit" class="space-y-5 mt-2">
                    
                    <!-- Input Email -->
                    <div class="group/input">
                        <label for="email" class="block text-[10px] font-mono font-bold text-ink-faded uppercase tracking-wider mb-1.5 transition-colors group-focus-within/input:text-ink-navy">
                            Surat Elektronik (Email)
                        </label>
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="admin@sman1turen.sch.id"
                            class="w-full bg-paper border-2 border-ledger-line rounded-md px-4 py-3 font-mono text-sm font-bold text-ink-navy placeholder:font-sans placeholder:font-normal placeholder:text-ink-faded/40 outline-none focus:border-ink-navy focus:shadow-[4px_4px_0px_0px_#1F3A52] transition-all duration-200"
                        />
                        <div v-if="form.errors.email" class="text-[10px] font-mono font-bold text-stamp-red mt-1.5 flex items-center gap-1">
                            <span class="text-xs">⚠️</span> {{ form.errors.email }}
                        </div>
                    </div>

                    <!-- Input Password -->
                    <div class="group/input">
                        <label for="password" class="block text-[10px] font-mono font-bold text-ink-faded uppercase tracking-wider mb-1.5 transition-colors group-focus-within/input:text-ink-navy">
                            Kata Sandi (Password)
                        </label>
                        <input
                            id="password"
                            type="password"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full bg-paper border-2 border-ledger-line rounded-md px-4 py-3 font-mono text-sm font-bold text-ink-navy placeholder:text-ink-faded/40 outline-none focus:border-ink-navy focus:shadow-[4px_4px_0px_0px_#1F3A52] transition-all duration-200"
                        />
                        <div v-if="form.errors.password" class="text-[10px] font-mono font-bold text-stamp-red mt-1.5 flex items-center gap-1">
                            <span class="text-xs">⚠️</span> {{ form.errors.password }}
                        </div>
                    </div>

                    <!-- Ingat Sesi & Lupa Password -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center cursor-pointer group">
                            <div class="relative flex items-center justify-center">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    v-model="form.remember"
                                    class="peer appearance-none w-4 h-4 bg-paper border-2 border-ledger-line rounded-sm checked:bg-ink-navy checked:border-ink-navy focus:outline-none focus:ring-2 focus:ring-ink-navy/20 transition-all cursor-pointer"
                                />
                                <svg class="absolute w-3 h-3 text-paper-card opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <span class="ml-2 text-[10px] font-mono font-bold text-ink-faded group-hover:text-ink-navy transition-colors uppercase tracking-wider">
                                Ingat Sesi Ini
                            </span>
                        </label>

                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-[10px] font-mono font-bold text-ink-faded hover:text-ink-navy underline decoration-ledger-line hover:decoration-ink-navy uppercase tracking-wider transition-colors"
                        >
                            Lupa Sandi?
                        </Link>
                    </div>

                    <!-- Tombol Submit (Mekanik) -->
                    <div class="pt-4">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-ink-navy text-paper-card font-mono font-bold text-xs md:text-sm uppercase tracking-[0.15em] py-3.5 rounded-md hover:bg-ink-navy/90 hover:-translate-y-0.5 hover:shadow-[0_4px_0_0_#0f2a4a] active:translate-y-0.5 active:shadow-none transition-all disabled:opacity-50 disabled:pointer-events-none flex items-center justify-center gap-2"
                        >
                            <span v-if="form.processing" class="inline-block animate-spin">⚙️</span>
                            {{ form.processing ? 'Memverifikasi...' : 'Masuk Sistem ↵' }}
                        </button>
                    </div>

                </form>
            </div>

            <!-- Footer Meta -->
            <div class="text-center mt-8 space-y-1">
                <p class="text-[10px] font-mono text-ink-faded/60 uppercase tracking-widest">
                    [ Properti Tata Usaha ]
                </p>
                <Link :href="route('home')" class="inline-block text-[10px] font-mono text-ink-faded hover:text-ink-navy transition-colors uppercase tracking-wider border-b border-transparent hover:border-ink-navy pb-0.5 mt-2">
                    ← Kembali ke Pencarian Publik
                </Link>
            </div>

        </div>
    </div>
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