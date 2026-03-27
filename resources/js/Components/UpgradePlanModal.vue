<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-on-background/30 backdrop-blur-md">
                <div
                    class="absolute inset-0"
                    @click="$emit('close')"
                ></div>

                <div class="relative w-full max-w-lg bg-surface-container-lowest rounded-[2rem] overflow-hidden shadow-2xl shadow-on-background/10 border border-white/40 z-10">
                    <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-primary-fixed to-primary"></div>

                    <div class="p-8 md:p-12 flex flex-col items-center text-center">
                        <div class="relative w-48 h-48 mb-8 flex items-center justify-center">
                            <div class="absolute inset-0 bg-primary-container/30 rounded-full blur-3xl"></div>
                            <div class="relative z-10 w-full h-full flex items-center justify-center">
                                <div class="w-32 h-32 rounded-full bg-white shadow-inner flex items-center justify-center border-4 border-primary-container/20">
                                    <div class="relative">
                                        <span class="material-symbols-outlined text-primary text-6xl" style="font-variation-settings: 'FILL' 1;">favorite</span>
                                        <div class="absolute -top-1 -right-1 bg-primary-fixed text-primary rounded-full p-1.5 border-2 border-white">
                                            <span class="material-symbols-outlined text-sm font-bold">add</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="absolute bottom-4 -left-2 w-10 h-10 bg-primary-container rounded-lg rotate-12 opacity-40"></div>
                            <div class="absolute top-8 -right-4 w-6 h-6 bg-secondary-container rounded-full opacity-60"></div>
                        </div>

                        <h2 class="font-headline text-3xl font-extrabold text-on-background mb-4 tracking-tight">
                            {{ title }}
                        </h2>
                        <p class="text-on-surface-variant font-medium leading-relaxed mb-8 px-4">
                            {{ message }}
                        </p>

                        <div class="flex flex-wrap justify-center gap-3 mb-10">
                            <div
                                v-for="item in features"
                                :key="item.label"
                                class="flex items-center gap-2 px-4 py-2 bg-secondary-container text-on-secondary-container rounded-full text-xs font-bold border border-secondary-container/50"
                            >
                                <span class="material-symbols-outlined text-sm">{{ item.icon }}</span>
                                {{ item.label }}
                            </div>
                        </div>

                        <div class="w-full space-y-4">
                            <Link
                                :href="route('plans')"
                                class="w-full py-4 bg-gradient-to-r from-primary to-primary-dim text-on-primary font-bold text-lg rounded-xl shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2"
                            >
                                {{ ctaLabel }}
                                <span class="material-symbols-outlined">arrow_forward</span>
                            </Link>
                            <button
                                type="button"
                                class="w-full py-2 text-on-surface-variant font-semibold hover:text-primary transition-colors duration-200"
                                @click="$emit('close')"
                            >
                                Tal vez mas tarde
                            </button>
                        </div>
                    </div>

                    <div class="absolute bottom-0 left-0 w-full h-1/2 opacity-[0.03] pointer-events-none overflow-hidden">
                        <svg class="w-full h-full text-primary fill-current" viewBox="0 0 100 100" aria-hidden="true">
                            <circle cx="10" cy="90" r="40"></circle>
                            <circle cx="90" cy="10" r="30"></circle>
                        </svg>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Has alcanzado el limite de tu plan',
    },
    message: {
        type: String,
        default: 'Has gestionado tus primeros 5 pacientes con exito. Para seguir transformando la salud de mas personas, mejora a un plan superior.',
    },
    ctaLabel: {
        type: String,
        default: 'Ver Planes y Mejorar',
    },
    features: {
        type: Array,
        default: () => [
            { icon: 'all_inclusive', label: 'Pacientes ilimitados' },
            { icon: 'assessment', label: 'Reportes personalizados' },
            { icon: 'verified', label: 'Soporte prioritario' },
        ],
    },
});

defineEmits(['close']);
</script>
