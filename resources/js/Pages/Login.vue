<template>
    <div class="font-body bg-surface min-h-screen flex items-center justify-center relative overflow-hidden">

        <!-- Background blobs -->
        <div class="absolute inset-0 z-0 pointer-events-none">
            <div class="absolute top-[-10%] right-[-5%] w-[600px] h-[600px] rounded-full bg-primary-container/30 blur-[120px]"></div>
            <div class="absolute bottom-[-10%] left-[-5%] w-[500px] h-[500px] rounded-full bg-secondary-container/20 blur-[100px]"></div>
        </div>

        <!-- Card -->
        <main class="relative z-10 w-full max-w-md px-6 py-12">
            <div class="glass-effect rounded-[2rem] p-8 md:p-10 shadow-sm border border-white/40">

                <!-- Header -->
                <header class="flex flex-col items-center mb-10 text-center">
                    <div class="w-16 h-16 bg-primary-container rounded-2xl flex items-center justify-center mb-6 shadow-sm">
                        <span class="material-symbols-outlined text-primary" style="font-size:36px;font-variation-settings:'FILL' 1">spa</span>
                    </div>
                    <h1 class="font-headline font-extrabold text-3xl text-on-surface tracking-tight mb-2">Clinical Sanctuary</h1>
                    <p class="text-on-surface-variant text-sm font-medium">Inicia sesión en tu espacio de bienestar</p>
                </header>

                <!-- Form -->
                <form class="space-y-6" @submit.prevent="submit">

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-widest ml-1 mb-2" for="email">
                            Correo Electrónico
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="material-symbols-outlined text-outline group-focus-within:text-primary transition-colors" style="font-size:20px">mail</span>
                            </div>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                name="email"
                                placeholder="ejemplo@clinica.com"
                                required
                                autocomplete="email"
                                class="block w-full pl-11 pr-4 py-4 bg-surface-container-high border-none rounded-xl text-on-surface focus:ring-2 focus:ring-primary/20 transition-all placeholder:text-outline/60 text-sm font-medium"
                            />
                            <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-0 h-0.5 bg-primary transition-all duration-300 group-focus-within:w-full rounded-full"></div>
                        </div>
                        <p v-if="errors.email" class="mt-1.5 ml-1 text-xs text-error font-medium">{{ errors.email }}</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-widest ml-1" for="password">
                                Contraseña
                            </label>
                            <a class="text-xs font-semibold text-primary hover:text-primary-dim transition-colors" href="#">¿La olvidaste?</a>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="material-symbols-outlined text-outline group-focus-within:text-primary transition-colors" style="font-size:20px">lock</span>
                            </div>
                            <input
                                id="password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                placeholder="••••••••"
                                required
                                autocomplete="current-password"
                                class="block w-full pl-11 pr-12 py-4 bg-surface-container-high border-none rounded-xl text-on-surface focus:ring-2 focus:ring-primary/20 transition-all placeholder:text-outline/60 text-sm font-medium"
                            />
                            <button
                                type="button"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-outline hover:text-primary transition-colors"
                                @click="showPassword = !showPassword"
                            >
                                <span class="material-symbols-outlined" style="font-size:20px">{{ showPassword ? 'visibility_off' : 'visibility' }}</span>
                            </button>
                            <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-0 h-0.5 bg-primary transition-all duration-300 group-focus-within:w-full rounded-full"></div>
                        </div>
                        <p v-if="errors.password" class="mt-1.5 ml-1 text-xs text-error font-medium">{{ errors.password }}</p>
                    </div>

                    <!-- Remember me -->
                    <div class="flex items-center gap-2 pt-1">
                        <input
                            id="remember"
                            v-model="form.remember"
                            type="checkbox"
                            class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary cursor-pointer"
                        />
                        <label class="text-xs font-medium text-on-surface-variant cursor-pointer select-none" for="remember">
                            Recordar mi sesión
                        </label>
                    </div>

                    <!-- Error general -->
                    <div v-if="errors.general" class="flex items-center gap-2 p-3 bg-error-container/20 rounded-xl text-error text-sm font-medium">
                        <span class="material-symbols-outlined" style="font-size:18px">error</span>
                        {{ errors.general }}
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full bg-primary text-on-primary font-headline font-bold py-4 rounded-xl shadow-lg hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                        <span v-if="loading" class="material-symbols-outlined animate-spin" style="font-size:20px">progress_activity</span>
                        <span v-else>Acceder al Santuario</span>
                        <span v-if="!loading" class="material-symbols-outlined" style="font-size:20px">arrow_forward</span>
                    </button>
                </form>

                <!-- Footer -->
                <footer class="mt-10 pt-8 border-t border-surface-variant/30 text-center">
                    <p class="text-sm text-on-surface-variant font-medium">
                        ¿Nuevo en la plataforma?
                        <a class="text-primary font-bold hover:underline ml-1" href="#">Crea una cuenta</a>
                    </p>
                </footer>
            </div>

            <!-- Trust badges -->
            <div class="mt-8 flex justify-center gap-6">
                <div class="flex items-center gap-2 text-outline/50">
                    <span class="material-symbols-outlined" style="font-size:16px">verified_user</span>
                    <span class="text-[10px] uppercase font-bold tracking-widest">Seguridad Médica</span>
                </div>
                <div class="flex items-center gap-2 text-outline/50">
                    <span class="material-symbols-outlined" style="font-size:16px">encrypted</span>
                    <span class="text-[10px] uppercase font-bold tracking-widest">Datos Encriptados</span>
                </div>
            </div>
        </main>

        <!-- Bottom-right badge -->
        <div class="fixed bottom-0 right-0 p-8 hidden lg:block">
            <div class="flex items-center gap-4 bg-white p-4 rounded-2xl shadow-sm border border-surface-variant/30">
                <div class="w-10 h-10 rounded-full overflow-hidden bg-surface-container shrink-0">
                    <img
                        alt="Dr. Nutrición"
                        class="w-full h-full object-cover"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCIyMLekA7TowwcmnDQG2vsOQ-RxPIKkaAix1eGPV25fm-Tc4lOeD4y8PtrDC9Yduszbc2FQMUoHH9AyqglARr9QNBpf-pj_R5TlglY-gmeEtDqP1DJ3ndEX8d1K4ht99l6Y4A192LL5n7lqBT4788EAd4EWEAegD4lzZ2ytM_VEHSIGdC6vAzcRZXrNXsc4NUoI-v_YorW_XBQ6DvSrLN0B3mDk2uS7lEWyac8ZhCAfpvnvSvO9XMRsmBTUZKsjly7xgTv6XlFcMX5"
                    />
                </div>
                <div class="pr-2">
                    <p class="text-xs font-bold text-on-surface">Asistencia Médica</p>
                    <p class="text-[10px] text-on-surface-variant">Disponible 24/7</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const showPassword = ref(false);
const loading = ref(false);

const form = reactive({
    email: '',
    password: '',
    remember: false,
});

const errors = reactive({
    email: '',
    password: '',
    general: '',
});

onMounted(() => {
    // El login siempre debe iniciar con apariencia original (sin tema de nutriólogo)
    document.documentElement.classList.remove('dark');
    document.body.classList.remove('theme-blue', 'theme-purple', 'theme-rose', 'theme-amber');
    localStorage.setItem('theme-mode', 'light');
});

function submit() {
    errors.email = '';
    errors.password = '';
    errors.general = '';
    loading.value = true;

    router.post('/login', form, {
        onError: (err) => {
            if (err.email)    errors.email    = err.email;
            if (err.password) errors.password = err.password;
            if (err.general)  errors.general  = err.general;
        },
        onFinish: () => {
            loading.value = false;
        },
    });
}
</script>

<style scoped>
.glass-effect {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
}
</style>
