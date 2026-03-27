<template>
    <div class="bg-surface text-on-surface min-h-screen flex flex-col">
        <header class="fixed top-0 w-full z-50 bg-surface-container-lowest/85 backdrop-blur-md shadow-sm">
            <div class="flex justify-between items-center px-6 py-4 max-w-7xl mx-auto">
                <div class="text-2xl font-bold tracking-tight text-primary font-headline">Metabole</div>
                <nav class="hidden md:flex gap-8 items-center">
                    <a class="text-primary font-semibold" href="#">Planes</a>
                    <a class="text-on-surface-variant hover:text-primary transition-colors" href="#">Caracteristicas</a>
                    <a class="text-on-surface-variant hover:text-primary transition-colors" href="#">Historias de exito</a>
                </nav>
                <div class="flex items-center gap-4">
                    <Link :href="route('login')" class="px-6 py-2.5 rounded-xl text-primary font-semibold hover:bg-primary/10 transition-all">
                        Iniciar sesion
                    </Link>
                </div>
            </div>
            <div class="bg-surface-variant h-px w-full"></div>
        </header>

        <main class="flex-grow pt-32 pb-20 px-6 bg-[radial-gradient(circle_at_top,#d9f8e9_0%,rgba(247,249,251,0.9)_36%,rgb(var(--color-surface))_70%)]">
            <div class="max-w-4xl mx-auto text-center mb-16">
                <span class="inline-block px-4 py-1.5 mb-6 rounded-full bg-primary-container text-on-primary-container text-sm font-semibold tracking-wide">
                    TARIFAS TRANSPARENTES
                </span>
                <h1 class="text-4xl md:text-6xl font-extrabold text-on-surface tracking-tight mb-6 leading-tight font-headline">
                    Elige el plan que potencie tu <span class="text-primary">practica clinica</span>
                </h1>
                <p class="text-lg text-on-surface-variant max-w-2xl mx-auto">
                    Desde consultorios independientes hasta clinicas de alto rendimiento. Encuentra las herramientas necesarias para transformar la salud de tus pacientes.
                </p>
            </div>

            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
                <article
                    v-for="plan in plans"
                    :key="plan.key"
                    :class="cardClass(plan)"
                >
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-on-surface mb-2 font-headline">{{ plan.name }}</h3>
                        <div class="flex items-baseline gap-1 mb-4">
                            <span class="text-4xl font-extrabold text-on-surface">${{ plan.price }}</span>
                            <span class="text-on-surface-variant font-medium">/ {{ plan.period }}</span>
                        </div>
                        <p class="text-on-surface-variant text-sm leading-relaxed">{{ plan.description }}</p>
                    </div>

                    <div class="space-y-4 mb-10 flex-grow">
                        <div v-for="feature in plan.features" :key="feature" class="flex items-center gap-3 text-on-surface">
                            <span class="material-symbols-outlined text-primary text-xl" :style="plan.isRecommended ? recommendedIconStyle : null">check_circle</span>
                            <span :class="['text-sm', plan.isRecommended ? 'font-bold' : 'font-medium']">{{ feature }}</span>
                        </div>
                    </div>

                    <Link
                        :href="route('register', { plan: plan.key })"
                        :class="buttonClass(plan)"
                    >
                        {{ plan.cta }}
                    </Link>

                    <div v-if="plan.badge" class="absolute -top-4 left-1/2 -translate-x-1/2 bg-primary text-on-primary px-6 py-1 rounded-full text-xs font-bold tracking-widest uppercase">
                        {{ plan.badge }}
                    </div>
                </article>
            </div>
        </main>

        <footer class="bg-surface-container-low w-full py-8 mt-auto border-t border-surface-variant/70 text-sm">
            <div class="flex flex-col md:flex-row justify-between items-center px-8 max-w-7xl mx-auto gap-4">
                <div class="font-headline font-bold text-on-surface">Metabole</div>
                <div class="text-on-surface-variant">© 2026 Metabole. El Santuario Clinico.</div>
                <div class="flex gap-6">
                    <a class="text-on-surface-variant hover:text-on-surface transition-all" href="#">Privacidad</a>
                    <a class="text-on-surface-variant hover:text-on-surface transition-all" href="#">Terminos</a>
                    <a class="text-on-surface-variant hover:text-on-surface transition-all" href="#">Soporte</a>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    plans: {
        type: Array,
        required: true,
    },
    currency: {
        type: String,
        default: 'USD',
    },
    selectedPlan: {
        type: String,
        default: 'free',
    },
});

const recommendedIconStyle = "font-variation-settings: 'FILL' 1;";

function cardClass(plan) {
    if (plan.isRecommended) {
        return 'relative bg-surface-container-lowest rounded-2xl p-8 flex flex-col border-2 border-primary shadow-xl scale-100 md:scale-105 z-10';
    }

    return 'relative bg-surface-container-lowest rounded-2xl p-8 flex flex-col transition-all duration-300 hover:-translate-y-1';
}

function buttonClass(plan) {
    if (plan.cta_type === 'primary') {
        return 'w-full py-4 rounded-xl bg-gradient-to-r from-primary to-primary-dim text-on-primary font-bold shadow-lg shadow-primary/20 transition-all duration-200 active:scale-95 text-center';
    }

    if (plan.cta_type === 'outline') {
        return 'w-full py-4 rounded-xl border-2 border-primary text-primary font-bold transition-all duration-200 active:scale-95 hover:bg-primary/10 text-center';
    }

    return 'w-full py-4 rounded-xl bg-surface-container-high text-on-surface font-bold transition-all duration-200 active:scale-95 hover:bg-primary hover:text-on-primary text-center';
}
</script>
