<template>
    <AppLayout>
        <main class="p-8 min-h-screen">

            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
                <div>
                    <nav class="flex text-xs text-on-surface-variant mb-2 space-x-2 items-center">
                        <span>Directorio</span>
                        <span class="material-symbols-outlined" style="font-size:14px">chevron_right</span>
                        <span class="text-primary font-bold">Gestión de Pacientes</span>
                    </nav>
                    <h1 class="text-4xl font-extrabold text-on-surface tracking-tight font-headline">Gestión de Pacientes</h1>
                    <p class="text-on-surface-variant mt-1 font-body">Supervisa y organiza el progreso de tus pacientes activos e históricos.</p>
                </div>
                <button
                    class="self-start shrink-0 bg-primary text-on-primary px-6 py-3 rounded-xl font-bold inline-flex items-center gap-2 shadow-lg hover:opacity-90 transition-opacity active:scale-95"
                    @click="showNewPatientModal = true"
                >
                    <span class="material-symbols-outlined">person_add</span>
                    Añadir Nuevo Paciente
                </button>
            </div>

            <!-- Filters & Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-8">

                <!-- Filter Card -->
                <div class="md:col-span-8 bg-white rounded-xl p-6 shadow-sm flex flex-wrap items-center gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-outline px-1">Estado</label>
                        <select
                            v-model="filterStatus"
                            class="bg-surface-container-low border-none rounded-lg text-sm font-medium focus:ring-1 focus:ring-primary py-2 px-3 min-w-[160px]"
                        >
                            <option value="">Todos los estados</option>
                            <option value="Activo">Activos</option>
                            <option value="Inactivo">Inactivos</option>
                            <option value="En Pausa">En Pausa</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-outline px-1">Última Consulta</label>
                        <select
                            v-model="filterDate"
                            class="bg-surface-container-low border-none rounded-lg text-sm font-medium focus:ring-1 focus:ring-primary py-2 px-3 min-w-[160px]"
                        >
                            <option value="">Cualquier fecha</option>
                            <option value="week">Esta semana</option>
                            <option value="month">Este mes</option>
                            <option value="old">Hace +3 meses</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2 mt-5">
                        <button class="p-2 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors">
                            <span class="material-symbols-outlined">filter_list</span>
                        </button>
                        <button class="text-sm font-semibold text-primary hover:underline px-2" @click="clearFilters">
                            Limpiar filtros
                        </button>
                    </div>
                </div>

                <!-- Total Patients Card -->
                <div class="md:col-span-4 bg-primary-container rounded-xl p-6 flex items-center justify-between border-l-4 border-primary">
                    <div>
                        <p class="text-on-primary-container text-xs font-bold uppercase tracking-widest opacity-80">Pacientes Totales</p>
                        <p class="text-3xl font-extrabold text-on-primary-container font-headline">{{ patients.length }}</p>
                    </div>
                    <div class="bg-white/40 p-3 rounded-full">
                        <span class="material-symbols-outlined text-primary" style="font-size:30px">groups</span>
                    </div>
                </div>
            </div>

            <!-- Patient Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-surface-container-low">
                            <th class="px-8 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-widest">Paciente</th>
                            <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-widest">ID</th>
                            <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-widest">Última Consulta</th>
                            <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-widest">Objetivo</th>
                            <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-widest">Estado</th>
                            <th class="px-8 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-widest text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="patient in filteredPatients"
                            :key="patient.profileId"
                            class="border-t border-surface-container-high hover:bg-surface-bright transition-colors"
                        >
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <img
                                        :alt="'Retrato de ' + patient.name"
                                        :src="patient.avatar ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(patient.name)}&background=7af9c7&color=004933`"
                                        class="w-10 h-10 rounded-full object-cover shrink-0"
                                    />
                                    <div>
                                        <p class="font-bold text-on-surface text-sm">{{ patient.name }}</p>
                                        <p class="text-xs text-on-surface-variant">{{ patient.email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-sm font-medium text-secondary">{{ patient.id }}</td>
                            <td class="px-6 py-5">
                                <p class="text-sm font-medium text-on-surface">{{ patient.lastVisit }}</p>
                                <p class="text-xs text-outline">{{ patient.daysAgo }}</p>
                            </td>
                            <td class="px-6 py-5">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold"
                                    :class="goalClass(patient.goal)"
                                >{{ patient.goal }}</span>
                            </td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center gap-2 text-xs font-bold" :class="patient.status === 'Activo' ? 'text-primary' : 'text-on-surface-variant'">
                                    <span class="w-2 h-2 rounded-full" :class="patient.status === 'Activo' ? 'bg-primary' : 'bg-outline-variant'"></span>
                                    {{ patient.status }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <button class="p-2 text-outline hover:text-primary transition-colors rounded-lg hover:bg-surface-container-high">
                                    <span class="material-symbols-outlined">more_vert</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="px-8 py-5 border-t border-surface-container-high flex items-center justify-between">
                    <p class="text-xs font-medium text-on-surface-variant">
                        Mostrando {{ filteredPatients.length }} de {{ patients.length }} pacientes
                    </p>
                    <div class="flex items-center gap-2">
                        <button
                            class="p-1.5 border border-outline-variant rounded-lg text-outline hover:bg-surface-container-high transition-colors disabled:opacity-40"
                            :disabled="currentPage === 1"
                            @click="currentPage--"
                        >
                            <span class="material-symbols-outlined" style="font-size:16px">chevron_left</span>
                        </button>
                        <template v-for="page in visiblePages" :key="page">
                            <span v-if="page === '...'" class="px-1 text-xs text-on-surface-variant">...</span>
                            <button
                                v-else
                                class="px-3 py-1 rounded-lg text-xs font-bold transition-colors"
                                :class="page === currentPage ? 'bg-primary text-on-primary' : 'hover:bg-surface-container-high text-on-surface'"
                                @click="currentPage = page"
                            >{{ page }}</button>
                        </template>
                        <button
                            class="p-1.5 border border-outline-variant rounded-lg text-outline hover:bg-surface-container-high transition-colors"
                            @click="currentPage++"
                        >
                            <span class="material-symbols-outlined" style="font-size:16px">chevron_right</span>
                        </button>
                    </div>
                </div>
            </div>

        </main>

        <!-- FAB Mobile -->
        <button
            class="md:hidden fixed bottom-6 right-6 w-14 h-14 bg-primary text-on-primary rounded-full shadow-2xl flex items-center justify-center z-50"
            @click="showNewPatientModal = true"
        >
            <span class="material-symbols-outlined">add</span>
        </button>

        <!-- Modal Nuevo Paciente -->
        <NewPatientModal
            :show="showNewPatientModal"
            @close="showNewPatientModal = false"
            @saved="onPatientSaved"
        />
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import NewPatientModal from '@/Components/NewPatientModal.vue';

const props = defineProps({
    patients: {
        type: Array,
        default: () => [],
    },
});

const filterStatus        = ref('');
const filterDate          = ref('');
const currentPage         = ref(1);
const showNewPatientModal = ref(false);

function onPatientSaved() {
    router.reload();
}

const goalClasses = {
    'Pérdida de Peso': 'bg-secondary-container text-on-secondary-container',
    'Hipertrofia':     'bg-tertiary-container text-on-tertiary-container',
    'Mantenimiento':   'bg-surface-container-high text-on-surface-variant',
    'Diabetes Tipo II':'bg-error-container text-on-error-container',
};

function goalClass(goal) {
    return goalClasses[goal] ?? 'bg-surface-container-high text-on-surface-variant';
}

const filteredPatients = computed(() => {
    return props.patients.filter(p => {
        if (filterStatus.value && p.status !== filterStatus.value) return false;
        return true;
    });
});

const totalPages = computed(() => Math.max(1, Math.ceil(props.patients.length / 10)));

const visiblePages = computed(() => {
    if (totalPages.value <= 5) return Array.from({ length: totalPages.value }, (_, i) => i + 1);
    return [1, 2, 3, '...', totalPages.value];
});

function clearFilters() {
    filterStatus.value = '';
    filterDate.value = '';
}
</script>
