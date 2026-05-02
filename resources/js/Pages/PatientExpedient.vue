<template>
    <AppLayout>
        <div class="flex-1 p-6 md:p-10">
            <!-- Breadcrumbs -->
            <nav class="flex items-center space-x-2 text-on-surface-variant text-sm mb-8 font-medium">
                <Link href="#" class="hover:text-primary transition-colors">Pacientes</Link>
                <span class="material-symbols-outlined text-base">chevron_right</span>
                <Link :href="route('pacientes.show', patient.id)" class="hover:text-primary transition-colors">{{ patient.name }}</Link>
                <span class="material-symbols-outlined text-base">chevron_right</span>
                <span class="text-on-surface font-bold">Historial de Citas</span>
            </nav>

            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
                <div>
                    <h1 class="text-4xl font-extrabold text-on-surface tracking-tight mb-2 font-headline">Historial de Consultas</h1>
                    <p class="text-on-surface-variant flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">person</span>
                        Paciente: {{ patient.name }} • ID: #{{ patient.id }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex bg-surface-container-high rounded-full p-1">
                        <button
                            @click="viewMode = 'list'"
                            :class="viewMode === 'list' ? 'bg-surface-container-lowest text-on-surface shadow-sm' : 'text-on-surface-variant'"
                            class="px-6 py-2 font-bold rounded-full text-sm transition-all"
                        >
                            Lista
                        </button>
                        <button
                            @click="viewMode = 'calendar'"
                            :class="viewMode === 'calendar' ? 'bg-surface-container-lowest text-on-surface shadow-sm' : 'text-on-surface-variant'"
                            class="px-6 py-2 font-medium rounded-full text-sm transition-all"
                        >
                            Calendario
                        </button>
                    </div>
                    <button class="p-2 bg-surface-container-lowest text-primary rounded-xl border border-primary/10 shadow-sm flex items-center gap-2 px-4 hover:bg-primary-container/20 transition-all">
                        <span class="material-symbols-outlined">download</span>
                        <span class="text-sm font-bold">Exportar PDF</span>
                    </button>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="bg-surface-container-low rounded-3xl p-6 mb-8 flex flex-col md:flex-row gap-6 items-end">
                <div class="flex-1 space-y-2">
                    <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider ml-1">Tipo de Consulta</label>
                    <div class="relative">
                        <select
                            v-model="filters.type"
                            class="w-full bg-surface-container-lowest border-none rounded-xl py-3 pl-4 pr-10 text-sm font-medium focus:ring-2 focus:ring-primary appearance-none"
                        >
                            <option value="">Todos los tipos</option>
                            <option value="initial">Evaluación Inicial</option>
                            <option value="followup">Seguimiento</option>
                            <option value="adjustment">Ajuste de Dieta</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant">expand_more</span>
                    </div>
                </div>
                <div class="flex-1 space-y-2">
                    <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider ml-1">Rango de Fecha</label>
                    <div class="relative">
                        <input
                            v-model="filters.dateRange"
                            class="w-full bg-surface-container-lowest border-none rounded-xl py-3 pl-10 pr-4 text-sm font-medium focus:ring-2 focus:ring-primary"
                            placeholder="Últimos 6 meses"
                            type="text"
                        />
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">calendar_today</span>
                    </div>
                </div>
                <button
                    @click="applyFilters"
                    class="h-12 px-8 bg-on-surface text-surface rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-on-surface/90 transition-all"
                >
                    <span class="material-symbols-outlined">filter_list</span>
                    <span>Filtrar</span>
                </button>
            </div>

            <!-- Appointments Content - List View -->
            <div v-if="viewMode === 'list'" class="space-y-4">
                <div
                    v-for="consultation in paginatedConsultations"
                    :key="consultation.id"
                    class="group bg-surface-container-lowest hover:bg-white p-1 rounded-[2rem] transition-all duration-300 shadow-sm hover:shadow-md border border-transparent hover:border-primary/5"
                >
                    <div class="flex flex-col md:flex-row md:items-center p-6 gap-6">
                        <!-- Date Card -->
                        <div class="flex items-center gap-6 md:w-1/4">
                            <div class="w-16 h-16 rounded-2xl bg-primary/10 flex flex-col items-center justify-center text-primary border border-primary/20">
                                <span class="text-xs font-bold uppercase">{{ formatDateMonth(consultation.date) }}</span>
                                <span class="text-2xl font-extrabold leading-none">{{ formatDateDay(consultation.date) }}</span>
                            </div>
                            <div>
                                <span
                                    :class="getTypeColor(consultation.type)"
                                    class="px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-full mb-1 inline-block"
                                >
                                    {{ getTypeLabel(consultation.type) }}
                                </span>
                                <p class="font-bold text-on-surface">{{ formatTime(consultation.date) }}</p>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1">
                            <h3 class="font-bold text-lg mb-1">{{ consultation.topic }}</h3>
                            <p class="text-on-surface-variant text-sm line-clamp-2 leading-relaxed">
                                {{ consultation.summary }}
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between md:justify-end gap-6 md:w-1/4">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-primary" :class="consultation.completed ? '' : 'animate-pulse'"></span>
                                <span class="text-xs font-bold text-primary uppercase tracking-widest">{{ consultation.completed ? 'Completada' : 'Pendiente' }}</span>
                            </div>
                            <button
                                class="w-10 h-10 rounded-full flex items-center justify-center hover:bg-surface-container-high text-on-surface-variant hover:text-primary transition-all"
                            >
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar View (Placeholder) -->
            <div v-else class="bg-surface-container-lowest rounded-3xl p-8 text-center">
                <span class="material-symbols-outlined text-6xl text-surface-dim opacity-30 block mb-4">calendar_month</span>
                <p class="text-on-surface-variant">Vista de calendario en desarrollo</p>
            </div>

            <!-- Pagination -->
            <div class="mt-12 flex items-center justify-between border-t border-surface-container-high pt-8">
                <p class="text-on-surface-variant text-sm">
                    Mostrando {{ (currentPage - 1) * itemsPerPage + 1 }} -
                    {{ Math.min(currentPage * itemsPerPage, filteredConsultations.length) }}
                    de {{ filteredConsultations.length }} consultas realizadas
                </p>
                <div class="flex items-center gap-2">
                    <button
                        @click="currentPage--"
                        :disabled="currentPage === 1"
                        class="w-10 h-10 rounded-xl bg-surface-container-low flex items-center justify-center text-on-surface-variant disabled:opacity-30 hover:opacity-80 transition-all"
                    >
                        <span class="material-symbols-outlined">chevron_left</span>
                    </button>
                    <button
                        v-for="page in totalPages"
                        :key="page"
                        @click="currentPage = page"
                        :class="currentPage === page ? 'bg-primary text-on-primary' : 'hover:bg-surface-container-low'"
                        class="w-10 h-10 rounded-xl flex items-center justify-center font-medium transition-all"
                    >
                        {{ page }}
                    </button>
                    <button
                        @click="currentPage++"
                        :disabled="currentPage === totalPages"
                        class="w-10 h-10 rounded-xl bg-surface-container-low flex items-center justify-center text-on-surface-variant disabled:opacity-30 hover:opacity-80 transition-all"
                    >
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    patient: { type: Object, required: true },
    consultations: { type: Array, default: () => [] },
});

const viewMode = ref('list');
const currentPage = ref(1);
const itemsPerPage = 10;

const filters = ref({
    type: '',
    dateRange: '',
});

const filteredConsultations = computed(() => {
    let result = [...props.consultations];

    if (filters.value.type) {
        result = result.filter(c => c.type === filters.value.type);
    }

    return result.sort((a, b) => new Date(b.date) - new Date(a.date));
});

const totalPages = computed(() => {
    return Math.ceil(filteredConsultations.value.length / itemsPerPage);
});

const paginatedConsultations = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredConsultations.value.slice(start, end);
});

function formatDateMonth(date) {
    const months = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
    return months[new Date(date).getMonth()];
}

function formatDateDay(date) {
    return new Date(date).getDate();
}

function formatTime(date) {
    return new Date(date).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
}

function getTypeLabel(type) {
    const labels = {
        initial: 'Evaluación Inicial',
        followup: 'Seguimiento',
        adjustment: 'Ajuste de Dieta',
    };
    return labels[type] || type;
}

function getTypeColor(type) {
    const colors = {
        initial: 'bg-primary-container text-on-primary-container',
        followup: 'bg-secondary-container text-on-secondary-container',
        adjustment: 'bg-tertiary-container text-on-tertiary-container',
    };
    return colors[type] || 'bg-surface-container text-on-surface';
}

function applyFilters() {
    currentPage.value = 1;
}
</script>
