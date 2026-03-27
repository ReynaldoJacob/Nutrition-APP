<template>
    <div class="bg-background min-h-screen flex flex-col">
        <header class="fixed top-0 w-full z-50 bg-surface-container-lowest/80 backdrop-blur-md shadow-sm">
            <div class="flex justify-between items-center px-6 py-4 max-w-7xl mx-auto">
                <div class="text-xl font-bold text-primary tracking-tight italic">
                    Clinical Sanctuary
                </div>
                <nav class="hidden md:flex gap-8 items-center font-headline text-sm font-semibold tracking-tight">
                    <a class="text-on-surface-variant hover:text-primary transition-colors" href="#">Plataforma</a>
                    <a class="text-on-surface-variant hover:text-primary transition-colors" href="#">Especialistas</a>
                    <a class="text-primary font-bold scale-95 active:opacity-80 transition-all" href="#">Support</a>
                </nav>
            </div>
        </header>

        <main class="flex-grow flex items-center justify-center pt-24 pb-12 px-4 relative overflow-hidden bg-gradient-to-br from-surface via-primary-container/20 to-surface-container">
            <div class="absolute top-[-10%] right-[-5%] w-[500px] h-[500px] bg-primary-container/30 rounded-full blur-[120px] -z-10"></div>
            <div class="absolute bottom-[-10%] left-[-5%] w-[400px] h-[400px] bg-secondary-container/40 rounded-full blur-[100px] -z-10"></div>

            <div class="w-full max-w-xl">
                <div class="glass-card p-8 md:p-12 rounded-[2rem] shadow-sm border border-white/40">
                    <div class="text-center mb-10">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-container rounded-2xl mb-6">
                            <span class="material-symbols-outlined text-on-primary-container text-3xl">medical_services</span>
                        </div>
                        <h1 class="text-3xl font-extrabold text-on-surface tracking-tight mb-2 font-headline">Únete a la Red</h1>
                        <p class="text-on-surface-variant text-sm font-medium">Crea tu perfil profesional en Clinical Sanctuary</p>
                    </div>

                    <form class="space-y-6" @submit.prevent="submit">
                        <div
                            v-if="page.props.flash?.warning"
                            class="flex items-center gap-2 p-3 bg-secondary-container/70 rounded-xl text-on-secondary-container text-sm font-medium"
                        >
                            <span class="material-symbols-outlined" style="font-size:18px">warning</span>
                            {{ page.props.flash.warning }}
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-on-secondary-fixed-variant ml-1">Nombre Completo</label>
                            <div class="relative group">
                                <input
                                    v-model="form.name"
                                    class="w-full bg-surface-container-high border-none rounded-xl px-4 py-4 text-on-surface placeholder:text-outline/60 focus:ring-2 focus:ring-primary transition-all"
                                    placeholder="Dr. Jane Smith"
                                    type="text"
                                    autocomplete="name"
                                />
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline/40">person</span>
                            </div>
                            <p v-if="errors.name" class="text-xs text-error font-medium ml-1">{{ errors.name }}</p>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-on-secondary-fixed-variant ml-1">Correo Electrónico</label>
                            <div class="relative group">
                                <input
                                    v-model="form.email"
                                    class="w-full bg-surface-container-high border-none rounded-xl px-4 py-4 text-on-surface placeholder:text-outline/60 focus:ring-2 focus:ring-primary transition-all"
                                    placeholder="doctor@clinicalsanctuary.com"
                                    type="email"
                                    autocomplete="email"
                                />
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline/40">mail</span>
                            </div>
                            <p v-if="errors.email" class="text-xs text-error font-medium ml-1">{{ errors.email }}</p>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-on-secondary-fixed-variant ml-1">Especialidad / Cédula Profesional</label>
                            <div class="relative group">
                                <input
                                    v-model="form.professional_info"
                                    class="w-full bg-surface-container-high border-none rounded-xl px-4 py-4 text-on-surface placeholder:text-outline/60 focus:ring-2 focus:ring-primary transition-all"
                                    placeholder="Nutrición Clínica / ID: 987654321"
                                    type="text"
                                />
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline/40">workspace_premium</span>
                            </div>
                            <p class="text-[11px] text-on-surface-variant ml-1">Tip: puedes usar "Especialidad / Cédula" o solo "Cédula".</p>
                            <p v-if="errors.professional_info" class="text-xs text-error font-medium ml-1">{{ errors.professional_info }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-on-secondary-fixed-variant ml-1">Contraseña</label>
                                <input
                                    v-model="form.password"
                                    class="w-full bg-surface-container-high border-none rounded-xl px-4 py-4 text-on-surface placeholder:text-outline/60 focus:ring-2 focus:ring-primary transition-all"
                                    placeholder="••••••••"
                                    type="password"
                                    autocomplete="new-password"
                                />
                                <p v-if="errors.password" class="text-xs text-error font-medium ml-1">{{ errors.password }}</p>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-on-secondary-fixed-variant ml-1">Confirmar Contraseña</label>
                                <input
                                    v-model="form.password_confirmation"
                                    class="w-full bg-surface-container-high border-none rounded-xl px-4 py-4 text-on-surface placeholder:text-outline/60 focus:ring-2 focus:ring-primary transition-all"
                                    placeholder="••••••••"
                                    type="password"
                                    autocomplete="new-password"
                                />
                            </div>
                        </div>

                        <div class="flex items-start gap-3 py-2">
                            <input
                                id="terms"
                                v-model="form.terms"
                                class="mt-1 rounded border-outline-variant text-primary focus:ring-primary"
                                type="checkbox"
                            />
                            <label for="terms" class="text-[11px] leading-relaxed text-on-surface-variant">
                                Al registrarme, acepto los <span class="text-primary font-semibold">Términos de Servicio</span> y los <span class="text-primary font-semibold">Estándares Clínicos</span> de Clinical Sanctuary.
                            </label>
                        </div>
                        <p v-if="errors.terms" class="text-xs text-error font-medium">{{ errors.terms }}</p>

                        <button
                            :disabled="loading"
                            class="w-full py-4 bg-gradient-to-r from-primary to-primary-dim text-on-primary font-headline font-bold text-lg rounded-xl shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all disabled:opacity-60"
                            type="submit"
                        >
                            {{ loading ? 'Creando cuenta...' : 'Crear Cuenta' }}
                        </button>
                    </form>

                    <div class="mt-10 pt-8 border-t border-surface-variant text-center">
                        <p class="text-sm text-on-surface-variant font-medium">
                            ¿Ya tienes una cuenta?
                            <Link :href="route('login')" class="text-primary font-bold ml-1 hover:underline">Inicia sesión</Link>
                        </p>
                    </div>
                </div>

                <div class="mt-8 flex justify-center gap-8 opacity-50 grayscale hover:grayscale-0 hover:opacity-100 transition-all">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">verified_user</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest">HIPAA Compliant</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">encrypted</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest">AES-256 Secure</span>
                    </div>
                </div>
            </div>
        </main>

        <footer class="w-full py-8 mt-auto bg-surface-container-low">
            <div class="flex flex-col md:flex-row justify-between items-center px-8 gap-4 max-w-7xl mx-auto">
                <p class="text-xs text-on-surface-variant">
                    © 2026 Clinical Sanctuary. Professional Nutritionist Portal.
                </p>
                <nav class="flex gap-6">
                    <a class="text-xs text-on-surface-variant hover:text-primary transition-all" href="#">Privacy Policy</a>
                    <a class="text-xs text-on-surface-variant hover:text-primary transition-all" href="#">Terms of Service</a>
                    <a class="text-xs text-on-surface-variant hover:text-primary transition-all" href="#">Clinical Standards</a>
                </nav>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const loading = ref(false);
const page = usePage();

const form = reactive({
    name: '',
    email: '',
    professional_info: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const errors = reactive({
    name: '',
    email: '',
    professional_info: '',
    password: '',
    terms: '',
});

onMounted(() => {
    document.documentElement.classList.remove('dark');
    document.body.classList.remove('theme-blue', 'theme-purple', 'theme-rose', 'theme-amber');
    localStorage.setItem('theme-mode', 'light');
});

function clearErrors() {
    errors.name = '';
    errors.email = '';
    errors.professional_info = '';
    errors.password = '';
    errors.terms = '';
}

function submit() {
    clearErrors();
    loading.value = true;

    router.post(route('register.store'), form, {
        preserveScroll: true,
        onError: (err) => {
            if (err.name) errors.name = err.name;
            if (err.email) errors.email = err.email;
            if (err.professional_info) errors.professional_info = err.professional_info;
            if (err.password) errors.password = err.password;
            if (err.terms) errors.terms = err.terms;
        },
        onFinish: () => {
            loading.value = false;
        },
    });
}
</script>

<style scoped>
.glass-card {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
}
</style>
