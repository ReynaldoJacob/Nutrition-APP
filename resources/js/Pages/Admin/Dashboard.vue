<template>
    <AppLayout>
        <div class="p-8 max-w-7xl mx-auto">

            <!-- Header -->
            <div class="mb-10">
                <h1 class="text-4xl font-extrabold text-on-surface tracking-tight font-headline">Panel de Administración</h1>
                <p class="text-on-surface-variant mt-1">Vista general de la plataforma</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-10">
                <div class="bg-surface-container-lowest rounded-3xl p-6 shadow-sm border-b-4 border-primary/20">
                    <div class="bg-primary/10 p-2 rounded-xl w-fit mb-4">
                        <span class="material-symbols-outlined text-primary">medical_services</span>
                    </div>
                    <p class="text-on-surface-variant text-xs font-bold uppercase mb-1">Total Nutriólogos</p>
                    <span class="text-4xl font-extrabold font-headline">{{ stats.totalNutritionists }}</span>
                </div>
                <div class="bg-surface-container-lowest rounded-3xl p-6 shadow-sm border-b-4 border-secondary/20">
                    <div class="bg-secondary/10 p-2 rounded-xl w-fit mb-4">
                        <span class="material-symbols-outlined text-secondary">check_circle</span>
                    </div>
                    <p class="text-on-surface-variant text-xs font-bold uppercase mb-1">Nutriólogos Activos</p>
                    <span class="text-4xl font-extrabold font-headline">{{ stats.activeNutritionists }}</span>
                </div>
                <div class="bg-surface-container-lowest rounded-3xl p-6 shadow-sm border-b-4 border-tertiary/20">
                    <div class="bg-tertiary/10 p-2 rounded-xl w-fit mb-4">
                        <span class="material-symbols-outlined text-tertiary">group</span>
                    </div>
                    <p class="text-on-surface-variant text-xs font-bold uppercase mb-1">Total Pacientes</p>
                    <span class="text-4xl font-extrabold font-headline">{{ stats.totalPatients }}</span>
                </div>
                <div class="bg-surface-container-lowest rounded-3xl p-6 shadow-sm border-b-4 border-primary/10">
                    <div class="bg-primary/10 p-2 rounded-xl w-fit mb-4">
                        <span class="material-symbols-outlined text-primary">verified</span>
                    </div>
                    <p class="text-on-surface-variant text-xs font-bold uppercase mb-1">Verificados</p>
                    <span class="text-4xl font-extrabold font-headline">{{ stats.verifiedNutritionists }}</span>
                </div>
            </div>

            <!-- Últimos nutriólogos registrados -->
            <div class="bg-white rounded-3xl p-8 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-extrabold text-on-surface font-headline">Últimos Nutriólogos Registrados</h2>
                    <Link
                        :href="route('admin.nutriologos')"
                        class="text-sm font-bold text-primary hover:underline flex items-center gap-1"
                    >
                        Ver todos
                        <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </Link>
                </div>

                <div v-if="!recentNutritionists.length" class="flex flex-col items-center py-12 gap-3 text-on-surface-variant">
                    <span class="material-symbols-outlined text-5xl opacity-30">medical_services</span>
                    <p class="font-medium">Aún no hay nutriólogos registrados</p>
                </div>

                <div v-else class="space-y-3">
                    <div
                        v-for="nutri in recentNutritionists"
                        :key="nutri.id"
                        class="flex items-center gap-4 p-4 rounded-2xl hover:bg-surface-container-low/50 transition-colors"
                    >
                        <img
                            :alt="nutri.name"
                            :src="nutri.avatar ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(nutri.name)}&background=c9f0e3&color=004933&size=64`"
                            class="w-12 h-12 rounded-2xl object-cover shrink-0"
                        />
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-on-surface truncate">{{ nutri.name }}</p>
                            <p class="text-xs text-on-surface-variant truncate">{{ nutri.email }}</p>
                        </div>
                        <span class="text-xs text-on-surface-variant hidden sm:block">{{ nutri.specialization }}</span>
                        <div class="flex items-center gap-2 shrink-0">
                            <span
                                v-if="nutri.is_verified"
                                class="text-xs font-bold px-2 py-0.5 rounded-full bg-primary-container text-on-primary-container flex items-center gap-1"
                            >
                                <span class="material-symbols-outlined text-xs" style="font-variation-settings:'FILL' 1">verified</span>
                                Verificado
                            </span>
                            <span
                                class="text-xs font-bold px-2 py-0.5 rounded-full"
                                :class="nutri.is_active ? 'bg-secondary-container text-on-secondary-container' : 'bg-surface-container-high text-on-surface-variant'"
                            >{{ nutri.is_active ? 'Activo' : 'Inactivo' }}</span>
                        </div>
                        <span class="text-xs text-on-surface-variant hidden md:block shrink-0">{{ nutri.memberSince }}</span>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    stats: {
        type: Object,
        default: () => ({
            totalNutritionists:   0,
            activeNutritionists:  0,
            totalPatients:        0,
            verifiedNutritionists: 0,
        }),
    },
    recentNutritionists: {
        type: Array,
        default: () => [],
    },
});
</script>
