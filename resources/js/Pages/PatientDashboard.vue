<template>
    <PatientLayout>
        <!-- Hero Welcome & Specialist Card -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12 items-end">
            <div class="lg:col-span-8">
                <span class="text-primary font-bold uppercase tracking-[0.2em] text-xs mb-2 block">Bienvenida de nuevo</span>
                <h1 class="text-5xl font-extrabold text-on-surface mb-4 tracking-tight">¡Hola, {{ patient.user.first_name }}!</h1>
                <p class="text-on-surface-variant text-lg max-w-xl font-medium leading-relaxed">
                    Hoy es un excelente día para nutrir tu metabolismo. Tienes {{ contentUpdates }} nuevas actualizaciones en tu muro de bienestar.
                </p>
            </div>
            <div class="lg:col-span-4">
                <div class="bg-surface-container-lowest p-6 rounded-3xl shadow-sm border border-emerald-50/50 flex flex-col gap-4 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-primary-container/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                    <div class="flex items-center gap-4 relative z-10">
                        <img
                            :src="nutritionist.avatar || 'https://via.placeholder.com/64'"
                            :alt="nutritionist.full_name"
                            class="w-16 h-16 rounded-2xl object-cover shadow-md"
                        />
                        <div>
                            <h3 class="font-headline font-extrabold text-on-surface">{{ nutritionist.full_name }}</h3>
                            <p class="text-xs text-primary font-bold">Tu Nutrióloga</p>
                        </div>
                    </div>
                    <button class="w-full bg-secondary-container text-on-secondary-container py-3 rounded-xl font-bold text-sm flex items-center justify-center gap-2 hover:bg-secondary-container/80 transition-colors">
                        <span class="material-symbols-outlined text-lg">chat_bubble</span>
                        Enviar Mensaje
                    </button>
                </div>
            </div>
        </section>

        <!-- Main Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-20 md:mb-0">
            <!-- Center Content: Muro de Bienestar -->
            <div class="lg:col-span-8 space-y-8">
                <div class="flex justify-between items-center mb-2">
                    <h2 class="text-2xl font-extrabold text-on-surface flex items-center gap-2">
                        <span class="w-2 h-8 bg-primary rounded-full"></span>
                        Muro de Bienestar
                    </h2>
                    <button class="text-primary font-bold text-sm hover:underline">Ver todo</button>
                </div>

                <!-- Feed Bento Cards -->
                <div v-if="hasWallContent" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Sección de Recetas -->
                    <div v-if="recipes.length > 0" class="md:col-span-2 bg-surface-container-lowest rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition-shadow flex flex-col md:flex-row border border-emerald-50">
                        <div class="md:w-1/2 h-64 md:h-auto relative">
                            <img
                                :src="recipes[0].image_url"
                                :alt="recipes[0].title"
                                class="w-full h-full object-cover"
                            />
                            <div class="absolute top-4 left-4 bg-primary text-on-primary px-3 py-1 rounded-full text-xs font-bold">
                                Receta Saludable
                            </div>
                        </div>
                        <div class="md:w-1/2 p-8 flex flex-col justify-center">
                            <h3 class="text-2xl font-bold mb-3 text-on-surface">{{ recipes[0].title }}</h3>
                            <p class="text-on-surface-variant mb-6 text-sm leading-relaxed">{{ recipes[0].description }}</p>
                            <div class="flex items-center gap-4">
                                <span class="flex items-center gap-1 text-xs font-bold text-secondary">
                                    <span class="material-symbols-outlined text-sm">schedule</span>
                                    {{ recipes[0].reading_time }} min lectura
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Consejo Médico -->
                    <div v-if="medicalAdvice.length > 0" class="bg-primary-container/30 p-8 rounded-3xl border border-primary-container/50 relative overflow-hidden group hover:shadow-md transition-shadow cursor-pointer">
                        <span class="material-symbols-outlined text-primary text-4xl mb-4 opacity-50 group-hover:scale-110 transition-transform block">psychology</span>
                        <div class="absolute top-6 right-6 bg-white/50 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider text-primary">Consejo Médico</div>
                        <h3 class="text-xl font-bold text-on-primary-container mb-3">{{ medicalAdvice[0].title }}</h3>
                        <p class="text-on-primary-container/80 text-sm leading-relaxed mb-4">{{ medicalAdvice[0].content }}</p>
                        <a href="#" class="text-primary font-bold text-sm flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                            Leer más <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>

                    <!-- Hábito Diario -->
                    <div v-if="dailyHabits.length > 0" class="bg-surface-container-lowest p-8 rounded-3xl shadow-sm border border-emerald-50 flex flex-col justify-between hover:shadow-md transition-shadow">
                        <div>
                            <div class="w-12 h-12 bg-secondary-container rounded-2xl flex items-center justify-center text-secondary mb-4">
                                <span class="material-symbols-outlined">water_drop</span>
                            </div>
                            <h3 class="text-xl font-bold text-on-surface mb-2">{{ dailyHabits[0].title }}</h3>
                            <p class="text-on-surface-variant text-sm leading-relaxed">{{ dailyHabits[0].description }}</p>
                        </div>
                        <div class="mt-6 flex items-center gap-2">
                            <div class="flex -space-x-2">
                                <div class="w-6 h-6 rounded-full bg-emerald-200 border-2 border-white"></div>
                                <div class="w-6 h-6 rounded-full bg-emerald-300 border-2 border-white"></div>
                                <div class="w-6 h-6 rounded-full bg-emerald-400 border-2 border-white"></div>
                            </div>
                            <span class="text-[10px] font-bold text-on-surface-variant">Recomendado por tu equipo nutricional</span>
                        </div>
                    </div>
                </div>

                <div v-else class="bg-surface-container-lowest p-8 rounded-3xl border border-emerald-50 shadow-sm">
                    <h3 class="text-xl font-bold text-on-surface mb-2">Aun no hay publicaciones en tu muro</h3>
                    <p class="text-on-surface-variant text-sm leading-relaxed">
                        Tu nutriologa todavia no ha publicado recetas, consejos o notas para ti. Cuando publique contenido, aparecera aqui automaticamente.
                    </p>
                </div>
            </div>

            <!-- Sidebar: Progress & Info -->
            <div class="lg:col-span-4 space-y-8">
                <!-- Tu Progreso Card -->
                <div class="bg-surface-container-lowest p-8 rounded-3xl shadow-sm border border-emerald-50">
                    <h3 class="text-lg font-extrabold text-on-surface mb-6 flex items-center justify-between">
                        Tu Progreso
                        <span class="material-symbols-outlined text-on-surface-variant">trending_up</span>
                    </h3>
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-on-surface-variant uppercase">Peso</p>
                                <p class="text-2xl font-black text-on-surface">
                                    {{ patient.measurements.current_weight }}<span class="text-sm font-bold ml-1">kg</span>
                                </p>
                            </div>
                            <div class="bg-emerald-50 text-primary px-2 py-1 rounded-lg flex items-center gap-1 text-xs font-bold">
                                <span class="material-symbols-outlined text-xs">arrow_downward</span>
                                {{ patient.measurements.weight_change }}kg
                            </div>
                        </div>
                        <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
                            <div class="bg-primary h-full rounded-full" :style="{ width: progressPercentage + '%' }"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-surface-container-low p-4 rounded-2xl">
                                <p class="text-[10px] font-bold text-on-surface-variant uppercase mb-1">Grasa</p>
                                <p class="text-xl font-bold text-on-surface">{{ patient.measurements.body_fat }}%</p>
                                <p class="text-[10px] text-emerald-600 font-bold flex items-center">
                                    <span class="material-symbols-outlined text-[10px]">arrow_downward</span>
                                    {{ patient.measurements.body_fat_change }}%
                                </p>
                            </div>
                            <div class="bg-surface-container-low p-4 rounded-2xl">
                                <p class="text-[10px] font-bold text-on-surface-variant uppercase mb-1">Músculo</p>
                                <p class="text-xl font-bold text-on-surface">{{ patient.measurements.muscle_mass }}<span class="text-xs ml-1">kg</span></p>
                                <p class="text-[10px] text-emerald-600 font-bold flex items-center">
                                    <span class="material-symbols-outlined text-[10px]">arrow_upward</span>
                                    {{ patient.measurements.muscle_change }}kg
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Próxima Cita Card -->
                <div v-if="nextAppointment" class="bg-secondary-dim text-white p-8 rounded-3xl shadow-lg relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[120px]" style="font-variation-settings: 'FILL' 1;">calendar_today</span>
                    </div>
                    <p class="text-secondary-fixed text-xs font-bold uppercase tracking-widest mb-4">Próxima Cita</p>
                    <h3 class="text-xl font-bold mb-1">{{ formatDate(nextAppointment.scheduled_at) }}</h3>
                    <p class="text-secondary-fixed/80 font-medium mb-8">{{ formatTime(nextAppointment.scheduled_at) }}</p>
                    <button class="w-full bg-white text-secondary-dim py-3 rounded-xl font-bold text-sm hover:bg-secondary-fixed transition-colors">
                        Ver Detalles
                    </button>
                </div>

                <!-- Plan Activo Card -->
                <div class="bg-primary p-1 rounded-3xl">
                    <Link
                        href="/paciente/plan"
                        class="block bg-surface-container-lowest p-7 rounded-[1.4rem] group cursor-pointer hover:bg-surface-container transition-colors"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-primary-container rounded-2xl flex items-center justify-center text-primary group-hover:rotate-12 transition-transform">
                                    <span class="material-symbols-outlined">restaurant_menu</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-on-surface">Plan Activo</h4>
                                    <p class="text-xs text-on-surface-variant">Ver mi Dieta de Hoy</p>
                                </div>
                            </div>
                            <span class="material-symbols-outlined text-primary">chevron_right</span>
                        </div>
                    </Link>
                </div>
            </div>
        </div>

        <!-- FAB for Quick Action -->
        <div class="fixed bottom-24 md:bottom-8 right-8 z-40">
            <button class="w-16 h-16 bg-primary text-on-primary rounded-full shadow-xl shadow-primary/20 flex items-center justify-center hover:scale-110 active:scale-95 transition-all group">
                <span class="material-symbols-outlined text-3xl group-hover:rotate-90 transition-transform">add</span>
            </button>
        </div>
    </PatientLayout>
</template>

<script setup>
import { computed } from 'vue';
import PatientLayout from '@/Layouts/PatientLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    patient: {
        type: Object,
        required: true,
    },
    nutritionist: {
        type: Object,
        required: true,
    },
    recipes: {
        type: Array,
        default: () => [],
    },
    medicalAdvice: {
        type: Array,
        default: () => [],
    },
    dailyHabits: {
        type: Array,
        default: () => [],
    },
    nextAppointment: {
        type: Object,
        default: null,
    },
    contentUpdates: {
        type: Number,
        default: 0,
    },
    activePatients: {
        type: Number,
        default: 0,
    },
});

const hasWallContent = computed(() => {
    return props.recipes.length > 0 || props.medicalAdvice.length > 0 || props.dailyHabits.length > 0;
});

const progressPercentage = computed(() => {
    const target = props.patient.measurements.target_weight || 0;
    const current = props.patient.measurements.current_weight || 0;
    const initial = props.patient.measurements.initial_weight || current;

    if (target === 0) return 75;

    const totalDistance = Math.abs(initial - target);
    const remainingDistance = Math.abs(current - target);

    return totalDistance === 0 ? 100 : Math.max(0, Math.min(100, ((totalDistance - remainingDistance) / totalDistance) * 100));
});

const formatDate = (isoString) => {
    if (!isoString) return '';
    const date = new Date(isoString);
    return date.toLocaleDateString('es-ES', { weekday: 'long', month: 'long', day: 'numeric' });
};

const formatTime = (isoString) => {
    if (!isoString) return '';
    const date = new Date(isoString);
    return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
};
</script>
