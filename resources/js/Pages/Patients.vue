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
                <button class="self-start shrink-0 bg-primary text-on-primary px-6 py-3 rounded-xl font-bold inline-flex items-center gap-2 shadow-lg hover:opacity-90 transition-opacity active:scale-95">
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
                        <p class="text-3xl font-extrabold text-on-primary-container font-headline">1,284</p>
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
                            :key="patient.id"
                            class="border-t border-surface-container-high hover:bg-surface-bright transition-colors"
                        >
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <img
                                        :alt="'Retrato de ' + patient.name"
                                        :src="patient.avatar"
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
                        Mostrando {{ filteredPatients.length }} de 1,284 pacientes
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
        <button class="md:hidden fixed bottom-6 right-6 w-14 h-14 bg-primary text-on-primary rounded-full shadow-2xl flex items-center justify-center z-50">
            <span class="material-symbols-outlined">add</span>
        </button>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const filterStatus = ref('');
const filterDate   = ref('');
const currentPage  = ref(1);
const totalPages   = 321;

const goalClasses = {
    'Pérdida de Peso': 'bg-secondary-container text-on-secondary-container',
    'Hipertrofia':     'bg-tertiary-container text-on-tertiary-container',
    'Mantenimiento':   'bg-surface-container-high text-on-surface-variant',
    'Diabetes Tipo II':'bg-error-container text-on-error-container',
};

function goalClass(goal) {
    return goalClasses[goal] ?? 'bg-surface-container-high text-on-surface-variant';
}

const patients = [
    {
        id: '#CS-2045',
        name: 'Sofía Alarcón',
        email: 'sofia.al@email.com',
        lastVisit: '12 Oct, 2023',
        daysAgo: 'Hace 2 días',
        goal: 'Pérdida de Peso',
        status: 'Activo',
        avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCTJo20gRtiwXySNrc8AYIs03A31RosBUz6_zySOrxNvfqEZ1zIY3NHxvKrf5T1vg5ZmXteh3d2DbhQI14ZXL-If9T9vMvlclcfPZ1zM_bFRBo5ewBQHL7tCXt3yTQrfO_KkjCRl6YN0ERVHvIMJk0J4ZsTGOIW3aIkaHBAxdS-5xgIVTlvC9lLcbr1P5f6hXZPz74oXYN2t4T2tWOJEm6ZjNUqdXteQiRwedvXBfWkCMPUldjmOwh-7ViDGRHYEu8SuLq37UVNRBGg',
    },
    {
        id: '#CS-1982',
        name: 'Roberto Méndez',
        email: 'r.mendez@servicios.mx',
        lastVisit: '05 Oct, 2023',
        daysAgo: 'Hace 9 días',
        goal: 'Hipertrofia',
        status: 'Activo',
        avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDyUbUY5ZhOYqJBksnU_u0Kry60AQ7XHtG6Vd-TsFEdu5lGImGU0MA7n8zuW3rO7WJoomN9Ez4UetL_9TZwTGpEgIfs_6lEKUbEOQC-BZ7_cN9eLhPshcE_GrTsMb4W70E5RV2ZdZU8DUXH6sveTyBDs0ITdOa3e8S9g5WqHOYB-bXeQXOLNB_-_u3jk4y8nojjk9tQSVbp67aoJLcbeN4U0wPgS8js5owzE-ILajei3mQkvOOO72iCevCg9M9-Kgkgs6jppYimz1f3',
    },
    {
        id: '#CS-2101',
        name: 'Elena Rivas',
        email: 'elena_r88@cloud.com',
        lastVisit: '28 Sep, 2023',
        daysAgo: 'Hace 16 días',
        goal: 'Mantenimiento',
        status: 'Inactivo',
        avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBk5VZaBWXYbmbRkvUYs3BTJXwOOz5YXMzdecmsGG_72hBj7NSWsBSr5u59SjBymfMbgHLp6xWSZyuztnGwXLBow9LYG9Vy0BzilPl8kIyaQXbZjNXwywnzymeu8ilKa1rvLHJhV_v_7uhxvvS1rKST0S51xQWSOgUl5mdwqlHarotcijRcbS3oi2fnKooEuhBiawTrNzsUyz9UrAGRLs9YuWiuz0T80HcX9TPD1Ut8yQ7IB4J3Sx6CULDsbcqP-zOloVV1NvWKxwyJ',
    },
    {
        id: '#CS-1855',
        name: 'Carlos Fuentes',
        email: 'fuentes.carlos@pro.com',
        lastVisit: '15 Sep, 2023',
        daysAgo: 'Hace 1 mes',
        goal: 'Diabetes Tipo II',
        status: 'Activo',
        avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuAOMuKnBBlX99-gYbfD4gvEfSoSoTmkClIWepqQrVqOcKNPgWJvLXV7OfR_RhA8bxAt5d7MJt4zybp1Mp7Vlacx1zXF_YlcUpdARBRU5JBuughK5h231_-EJuwCHyhKsYQdwNfWP4mfMEsrcW5fXc-KKPTdVTJzsQrAxyaCPV4zCJ7_zaCc9PkMqckg35hHLeGeMZjX32whMg1VphHzUKDIDNwMaXfy9G9l7k6kaDmq2hz0embwJcYsY34Eqb46K6FtXqmZghPsBdV7',
    },
];

const filteredPatients = computed(() => {
    return patients.filter(p => {
        if (filterStatus.value && p.status !== filterStatus.value) return false;
        return true;
    });
});

const visiblePages = computed(() => {
    return [1, 2, 3, '...', totalPages];
});

function clearFilters() {
    filterStatus.value = '';
    filterDate.value = '';
}
</script>
