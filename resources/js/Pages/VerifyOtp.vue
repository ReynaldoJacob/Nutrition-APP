<template>
    <div class="bg-background min-h-screen flex flex-col">
        <header class="fixed top-0 w-full z-50 bg-surface-container-lowest/80 backdrop-blur-md shadow-sm">
            <div class="flex justify-between items-center px-6 py-4 max-w-7xl mx-auto">
                <div class="text-xl font-bold text-primary tracking-tight italic">Clinical Sanctuary</div>
                <Link :href="route('login')" class="text-sm font-semibold text-primary hover:underline">Volver a login</Link>
            </div>
        </header>

        <main class="flex-grow flex items-center justify-center pt-24 pb-12 px-4 relative overflow-hidden bg-gradient-to-br from-surface via-primary-container/20 to-surface-container">
            <div class="absolute top-[-10%] right-[-5%] w-[500px] h-[500px] bg-primary-container/30 rounded-full blur-[120px] -z-10"></div>
            <div class="absolute bottom-[-10%] left-[-5%] w-[400px] h-[400px] bg-secondary-container/40 rounded-full blur-[100px] -z-10"></div>

            <div class="w-full max-w-lg">
                <div class="glass-card p-8 md:p-10 rounded-[2rem] shadow-sm border border-white/40">
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-container rounded-2xl mb-6">
                            <span class="material-symbols-outlined text-on-primary-container text-3xl">verified_user</span>
                        </div>
                        <h1 class="text-3xl font-extrabold text-on-surface tracking-tight mb-2 font-headline">Verifica tu cuenta</h1>
                        <p class="text-on-surface-variant text-sm font-medium">Ingresa el código OTP de 6 dígitos enviado a <strong>{{ form.email }}</strong>.</p>
                    </div>

                    <form class="space-y-6" @submit.prevent="submitOtp">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-on-secondary-fixed-variant ml-1">Correo electrónico</label>
                            <input
                                v-model="form.email"
                                type="email"
                                class="w-full bg-surface-container-high border-none rounded-xl px-4 py-4 text-on-surface focus:ring-2 focus:ring-primary transition-all"
                                autocomplete="email"
                            />
                            <p v-if="errors.email" class="text-xs text-error font-medium ml-1">{{ errors.email }}</p>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-on-secondary-fixed-variant ml-1">Código OTP</label>
                            <input
                                v-model="form.otp"
                                inputmode="numeric"
                                maxlength="6"
                                placeholder="000000"
                                class="w-full bg-primary-container/30 border-none rounded-xl px-4 py-4 text-on-primary-container text-3xl font-extrabold tracking-[0.30em] text-center focus:ring-2 focus:ring-primary transition-all"
                            />
                            <p class="text-[11px] text-on-surface-variant ml-1">Este código expira en {{ expiryMinutes }} minutos.</p>
                            <p v-if="errors.otp" class="text-xs text-error font-medium ml-1">{{ errors.otp }}</p>
                        </div>

                        <div
                            v-if="page.props.flash?.success"
                            class="flex items-center gap-2 p-3 bg-primary-container/50 rounded-xl text-on-primary-container text-sm font-medium"
                        >
                            <span class="material-symbols-outlined" style="font-size:18px">check_circle</span>
                            {{ page.props.flash.success }}
                        </div>

                        <div
                            v-if="page.props.flash?.warning"
                            class="flex items-center gap-2 p-3 bg-secondary-container/70 rounded-xl text-on-secondary-container text-sm font-medium"
                        >
                            <span class="material-symbols-outlined" style="font-size:18px">warning</span>
                            {{ page.props.flash.warning }}
                        </div>

                        <button
                            :disabled="loading"
                            class="w-full py-4 bg-gradient-to-r from-primary to-primary-dim text-on-primary font-headline font-bold text-lg rounded-xl shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all disabled:opacity-60"
                            type="submit"
                        >
                            {{ loading ? 'Verificando...' : 'Verificar cuenta' }}
                        </button>
                    </form>

                    <button
                        :disabled="resending"
                        class="w-full mt-4 py-3 rounded-xl border border-outline-variant text-on-surface-variant font-semibold hover:bg-surface-container-low transition-colors disabled:opacity-60"
                        @click="resendOtp"
                    >
                        {{ resending ? 'Reenviando...' : 'Reenviar código OTP' }}
                    </button>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { onMounted, reactive, ref } from 'vue';

const props = defineProps({
    email: { type: String, default: '' },
    expiryMinutes: { type: Number, default: 10 },
});

const page = usePage();
const loading = ref(false);
const resending = ref(false);

const form = reactive({
    email: props.email,
    otp: '',
});

const errors = reactive({
    email: '',
    otp: '',
});

onMounted(() => {
    document.documentElement.classList.remove('dark');
    document.body.classList.remove('theme-blue', 'theme-purple', 'theme-rose', 'theme-amber');
    localStorage.setItem('theme-mode', 'light');
});

function clearErrors() {
    errors.email = '';
    errors.otp = '';
}

function submitOtp() {
    clearErrors();
    loading.value = true;

    router.post(route('register.verify.submit'), form, {
        preserveScroll: true,
        onError: (err) => {
            if (err.email) errors.email = err.email;
            if (err.otp) errors.otp = err.otp;
        },
        onFinish: () => {
            loading.value = false;
        },
    });
}

function resendOtp() {
    clearErrors();
    resending.value = true;

    router.post(route('register.verify.resend'), { email: form.email }, {
        preserveScroll: true,
        onError: (err) => {
            if (err.email) errors.email = err.email;
            if (err.otp) errors.otp = err.otp;
        },
        onFinish: () => {
            resending.value = false;
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
