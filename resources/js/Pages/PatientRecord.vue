<template>
    <AppLayout>
        <div class="p-8 max-w-7xl mx-auto space-y-8">

            <!-- Hero Patient Section -->
            <section class="flex flex-col md:flex-row gap-8 items-start">
                <div class="flex-1 space-y-2">
                    <div class="flex items-center gap-4">
                        <h1 class="text-4xl font-extrabold text-on-surface tracking-tight font-headline">
                            Valeria Montes de Oca
                        </h1>
                        <span class="bg-primary-container text-on-primary-container px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                            Activo
                        </span>
                    </div>
                    <p class="text-on-surface-variant font-body text-lg">
                        Paciente desde Marzo 2023 • ID: #VN-2904
                    </p>
                    <div class="flex flex-wrap gap-2 pt-4">
                        <div class="bg-secondary-container text-on-secondary-container px-4 py-1 rounded-full text-sm font-medium flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">warning</span>
                            Alergia: Nueces y Mariscos
                        </div>
                        <div class="bg-surface-container-high text-on-surface-variant px-4 py-1 rounded-full text-sm font-medium">
                            Plan: Déficit Calórico Moderado
                        </div>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button class="flex items-center gap-2 bg-surface-container-lowest border border-outline-variant/20 px-5 py-2.5 rounded-xl text-sm font-bold text-on-surface hover:bg-surface-container-low transition-all">
                        <span class="material-symbols-outlined text-lg">edit</span>
                        Editar información
                    </button>
                    <button class="flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-primary/10 hover:shadow-primary/20 transition-all">
                        <span class="material-symbols-outlined text-lg">picture_as_pdf</span>
                        Generar reporte
                    </button>
                </div>
            </section>

            <!-- Stats & Bento Grid -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

                <!-- Vital Stats -->
                <div class="md:col-span-8 grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/10">
                        <p class="text-on-surface-variant text-sm font-medium mb-1">Peso Actual</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-on-surface font-headline">64.5</span>
                            <span class="text-on-surface-variant font-bold">kg</span>
                        </div>
                        <div class="mt-4 flex items-center gap-1 text-primary text-xs font-bold">
                            <span class="material-symbols-outlined text-sm">arrow_downward</span>
                            1.2kg vs mes pasado
                        </div>
                    </div>
                    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/10">
                        <p class="text-on-surface-variant text-sm font-medium mb-1">IMC</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-on-surface font-headline">22.8</span>
                        </div>
                        <div class="mt-4 flex items-center gap-1 text-primary text-xs font-bold">
                            <span class="px-2 py-0.5 bg-primary-container rounded-md">Rango Saludable</span>
                        </div>
                    </div>
                    <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/10">
                        <p class="text-on-surface-variant text-sm font-medium mb-1">% Grasa Corporal</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-on-surface font-headline">24.2</span>
                            <span class="text-on-surface-variant font-bold">%</span>
                        </div>
                        <div class="mt-4 flex items-center gap-1 text-primary text-xs font-bold">
                            <span class="material-symbols-outlined text-sm">trending_down</span>
                            -0.8% tendencia
                        </div>
                    </div>
                </div>

                <!-- Goals -->
                <div class="md:col-span-4 bg-primary-container/30 p-8 rounded-xl">
                    <h3 class="text-xl font-bold text-on-primary-container mb-4 font-headline">Objetivos Actuales</h3>
                    <ul class="space-y-4">
                        <li v-for="goal in goals" :key="goal.title" class="flex gap-3">
                            <span
                                class="material-symbols-outlined shrink-0"
                                :class="goal.done ? 'text-primary' : 'text-primary/40'"
                                :style="goal.done ? `font-variation-settings: 'FILL' 1` : ''"
                            >{{ goal.done ? 'check_circle' : 'radio_button_unchecked' }}</span>
                            <div>
                                <p class="text-sm font-bold text-on-primary-container">{{ goal.title }}</p>
                                <p class="text-xs text-on-primary-fixed-variant">{{ goal.description }}</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Weight History Chart -->
                <div class="md:col-span-8 bg-surface-container-lowest p-8 rounded-xl border border-outline-variant/10">
                    <div class="flex justify-between items-center mb-8">
                        <h3 class="text-xl font-bold text-on-surface font-headline">Historial de Peso y Medidas</h3>
                        <div class="flex gap-2">
                            <button
                                v-for="range in ['6 Meses', '1 Año']"
                                :key="range"
                                :class="[
                                    'px-3 py-1 text-xs font-bold rounded-lg transition-colors',
                                    selectedRange === range
                                        ? 'bg-surface-container-high'
                                        : 'font-medium hover:bg-surface-container-high',
                                ]"
                                @click="selectedRange = range"
                            >{{ range }}</button>
                        </div>
                    </div>
                    <div class="h-64 flex items-end justify-between gap-4 px-2">
                        <div
                            v-for="bar in chartBars"
                            :key="bar.label"
                            class="flex flex-col items-center gap-2 flex-1"
                        >
                            <div
                                class="w-full rounded-t-lg"
                                :class="bar.active ? 'bg-primary' : 'bg-primary-fixed-dim'"
                                :style="{ height: bar.height + '%' }"
                            ></div>
                            <span
                                class="text-[10px] font-bold"
                                :class="bar.active ? 'text-on-surface' : 'text-on-surface-variant'"
                            >{{ bar.label }}</span>
                        </div>
                    </div>
                </div>

                <!-- Meal Plan -->
                <div class="md:col-span-4 bg-surface-container-lowest p-8 rounded-xl border border-outline-variant/10">
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="text-xl font-bold text-on-surface font-headline">Plan Alimenticio</h3>
                        <span class="material-symbols-outlined text-primary">restaurant</span>
                    </div>
                    <div class="space-y-4">
                        <div
                            v-for="meal in meals"
                            :key="meal.type"
                            class="p-4 bg-surface-container-low rounded-xl"
                        >
                            <p class="text-xs font-bold text-primary mb-1 uppercase tracking-wider">{{ meal.type }}</p>
                            <p class="text-sm font-semibold text-on-surface">{{ meal.description }}</p>
                        </div>
                        <button class="w-full py-3 text-center text-sm font-bold text-primary hover:underline transition-all">
                            Ver plan completo
                        </button>
                    </div>
                </div>

            </div>

            <!-- Notes & Observations -->
            <section class="bg-surface-container-lowest p-8 rounded-xl border border-outline-variant/10">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-on-surface font-headline">Notas y Observaciones</h3>
                    <button class="text-primary font-bold text-sm hover:underline">Añadir nota</button>
                </div>
                <div class="space-y-4">
                    <div
                        v-for="(note, i) in notes"
                        :key="i"
                        :class="[
                            'pl-4 py-2 border-l-4',
                            i === 0 ? 'border-primary' : 'border-outline-variant/30',
                        ]"
                    >
                        <p class="text-sm text-on-surface-variant italic mb-1">{{ note.date }}</p>
                        <p class="text-on-surface font-medium">{{ note.text }}</p>
                    </div>
                </div>
            </section>

        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const selectedRange = ref('6 Meses');

const goals = [
    { title: 'Mantener Hidratación',  description: 'Mínimo 2.5L de agua al día',   done: true  },
    { title: 'Reducción de Sodio',    description: 'Bajo 2000mg diarios',          done: false },
    { title: 'Incremento de Proteína',description: '1.6g por kg de peso',          done: false },
];

const chartBars = [
    { label: 'ENE', height: 90, active: false },
    { label: 'FEB', height: 85, active: false },
    { label: 'MAR', height: 82, active: false },
    { label: 'ABR', height: 78, active: false },
    { label: 'MAY', height: 75, active: true  },
];

const meals = [
    { type: 'Desayuno', description: 'Avena nocturna con chía y frutos rojos' },
    { type: 'Comida',   description: 'Salmón a la plancha con espárragos y quinoa' },
    { type: 'Cena',     description: 'Ensalada de espinacas con pollo y aderezo tahini' },
];

const notes = [
    {
        date: '15 de Mayo, 2024',
        text: 'La paciente reporta mayor energía durante las mañanas. Ha sido constante con el consumo de agua. Sugiero incrementar la intensidad de ejercicio aeróbico.',
    },
    {
        date: '02 de Mayo, 2024',
        text: 'Ajuste de porciones en la cena para mejorar la calidad del sueño. Refiere menos inflamación abdominal.',
    },
];
</script>
