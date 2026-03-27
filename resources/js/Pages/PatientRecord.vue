<template>
    <AppLayout>
        <div class="p-8 max-w-7xl mx-auto">

            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="px-3 py-1 rounded-full bg-primary-container text-on-primary-container text-xs font-bold uppercase tracking-wider">
                            {{ patient.status }}
                        </span>
                        <span class="text-on-surface-variant font-medium text-sm">#{{ patient.id }}</span>
                    </div>
                    <h1 class="text-4xl font-extrabold text-on-surface tracking-tight font-headline">{{ patient.name }}</h1>
                    <p class="text-on-surface-variant text-sm mt-1">Paciente desde {{ patient.memberSince }}</p>
                </div>
                <div class="flex gap-3">
                    <button class="px-6 py-2.5 rounded-xl text-primary font-bold hover:bg-primary-container/30 transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">edit</span>
                        Editar Perfil
                    </button>
                    <Link
                        :href="route('calendario')"
                        class="px-6 py-2.5 rounded-xl bg-primary text-on-primary font-bold shadow-sm hover:shadow-md transition-all flex items-center gap-2 active:scale-95"
                    >
                        <span class="material-symbols-outlined text-lg">add</span>
                        Nueva Consulta
                    </Link>
                </div>
            </div>

            <!-- Bento Grid -->
            <div class="grid grid-cols-12 gap-6">

                <!-- ── Columna izquierda ── -->
                <div class="col-span-12 lg:col-span-4 space-y-6">

                    <!-- Resumen del paciente -->
                    <div class="bg-surface-container-lowest rounded-3xl p-8 shadow-sm">
                        <div class="relative w-32 h-32 mx-auto mb-6">
                            <img
                                :alt="'Foto de ' + patient.name"
                                :src="patient.avatar ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(patient.name)}&background=7af9c7&color=004933&size=128`"
                                class="w-full h-full object-cover rounded-3xl shadow-inner"
                            />
                            <div class="absolute -bottom-2 -right-2 bg-primary p-2 rounded-xl text-on-primary">
                                <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1">verified</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div v-if="patient.age" class="flex justify-between items-center py-2 border-b border-surface-container">
                                <span class="text-on-surface-variant text-sm">Edad</span>
                                <span class="font-bold">{{ patient.age }} años</span>
                            </div>
                            <div v-if="patient.height" class="flex justify-between items-center py-2 border-b border-surface-container">
                                <span class="text-on-surface-variant text-sm">Estatura</span>
                                <span class="font-bold">{{ patient.height }} cm</span>
                            </div>
                            <div v-if="patient.gender" class="flex justify-between items-center py-2 border-b border-surface-container">
                                <span class="text-on-surface-variant text-sm">Sexo</span>
                                <span class="font-bold capitalize">{{ patient.gender }}</span>
                            </div>
                            <div v-if="latest?.bmi" class="flex justify-between items-center py-2 border-b border-surface-container">
                                <span class="text-on-surface-variant text-sm">IMC</span>
                                <span class="font-bold">{{ latest.bmi }}</span>
                            </div>
                            <div class="py-2">
                                <span class="text-on-surface-variant text-sm block mb-1">Objetivo</span>
                                <span class="font-bold text-primary">{{ patient.goal }}</span>
                            </div>
                            <div class="pt-4 space-y-3">
                                <div class="flex items-center gap-3 text-sm">
                                    <span class="material-symbols-outlined text-primary text-lg">mail</span>
                                    <span class="truncate">{{ patient.email }}</span>
                                </div>
                                <div v-if="patient.phone" class="flex items-center gap-3 text-sm">
                                    <span class="material-symbols-outlined text-primary text-lg">call</span>
                                    <span>{{ patient.phone }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notas del perfil -->
                    <div v-if="patient.notes" class="bg-surface-container-low rounded-3xl p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="material-symbols-outlined text-on-surface-variant text-lg">sticky_note_2</span>
                            <h3 class="font-bold text-on-surface text-sm">Notas del Perfil</h3>
                        </div>
                        <p class="text-sm text-on-surface-variant leading-relaxed">{{ patient.notes }}</p>
                    </div>

                </div>

                <!-- ── Columna derecha ── -->
                <div class="col-span-12 lg:col-span-8 space-y-6">

                    <!-- Métricas actuales -->
                    <div class="grid grid-cols-3 gap-4">
                        <!-- Peso -->
                        <div class="bg-surface-container-lowest p-6 rounded-3xl shadow-sm border-b-4 border-primary/20">
                            <div class="flex justify-between items-start mb-4">
                                <div class="bg-primary/10 p-2 rounded-xl">
                                    <span class="material-symbols-outlined text-primary">scale</span>
                                </div>
                                <span v-if="trends.weightDiff !== null && trends.weightDiff !== undefined"
                                    class="flex items-center text-xs font-bold gap-1 px-2 py-0.5 rounded-full"
                                    :class="trends.weightDiff <= 0 ? 'text-primary bg-primary-container/30' : 'text-error bg-error-container/20'"
                                >
                                    <span class="material-symbols-outlined text-xs">{{ trends.weightDiff <= 0 ? 'trending_down' : 'trending_up' }}</span>
                                    {{ trends.weightDiff > 0 ? '+' : '' }}{{ trends.weightDiff }}kg
                                </span>
                            </div>
                            <p class="text-on-surface-variant text-xs font-bold uppercase mb-1">Peso</p>
                            <div class="flex items-baseline gap-1">
                                <span class="text-3xl font-extrabold font-headline">{{ latest?.weight ?? '—' }}</span>
                                <span v-if="latest?.weight" class="text-on-surface-variant font-medium">kg</span>
                            </div>
                        </div>
                        <!-- Grasa -->
                        <div class="bg-surface-container-lowest p-6 rounded-3xl shadow-sm border-b-4 border-secondary/20">
                            <div class="flex justify-between items-start mb-4">
                                <div class="bg-secondary/10 p-2 rounded-xl">
                                    <span class="material-symbols-outlined text-secondary">monitor_weight</span>
                                </div>
                                <span v-if="trends.bodyFatDiff !== null && trends.bodyFatDiff !== undefined"
                                    class="flex items-center text-xs font-bold gap-1 px-2 py-0.5 rounded-full"
                                    :class="trends.bodyFatDiff <= 0 ? 'text-primary bg-primary-container/30' : 'text-error bg-error-container/20'"
                                >
                                    <span class="material-symbols-outlined text-xs">{{ trends.bodyFatDiff <= 0 ? 'trending_down' : 'trending_up' }}</span>
                                    {{ trends.bodyFatDiff > 0 ? '+' : '' }}{{ trends.bodyFatDiff }}%
                                </span>
                            </div>
                            <p class="text-on-surface-variant text-xs font-bold uppercase mb-1">% Grasa Corporal</p>
                            <div class="flex items-baseline gap-1">
                                <span class="text-3xl font-extrabold font-headline">{{ latest?.fat ?? '—' }}</span>
                                <span v-if="latest?.fat" class="text-on-surface-variant font-medium">%</span>
                            </div>
                        </div>
                        <!-- Masa magra -->
                        <div class="bg-surface-container-lowest p-6 rounded-3xl shadow-sm border-b-4 border-tertiary/20">
                            <div class="flex justify-between items-start mb-4">
                                <div class="bg-tertiary/10 p-2 rounded-xl">
                                    <span class="material-symbols-outlined text-tertiary">fitness_center</span>
                                </div>
                                <span v-if="trends.muscleDiff !== null && trends.muscleDiff !== undefined"
                                    class="flex items-center text-xs font-bold gap-1 px-2 py-0.5 rounded-full"
                                    :class="trends.muscleDiff >= 0 ? 'text-primary bg-primary-container/30' : 'text-error bg-error-container/20'"
                                >
                                    <span class="material-symbols-outlined text-xs">{{ trends.muscleDiff >= 0 ? 'trending_up' : 'trending_down' }}</span>
                                    {{ trends.muscleDiff > 0 ? '+' : '' }}{{ trends.muscleDiff }}kg
                                </span>
                            </div>
                            <p class="text-on-surface-variant text-xs font-bold uppercase mb-1">Masa Magra</p>
                            <div class="flex items-baseline gap-1">
                                <span class="text-3xl font-extrabold font-headline">{{ latest?.muscle ?? '—' }}</span>
                                <span v-if="latest?.muscle" class="text-on-surface-variant font-medium">kg</span>
                            </div>
                        </div>
                    </div>

                    <!-- Historial Clínico Timeline -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm">
                        <div class="flex justify-between items-center mb-8">
                            <h2 class="text-2xl font-extrabold text-on-surface font-headline">Historial Clínico</h2>
                            <span class="text-xs text-on-surface-variant font-medium">{{ timeline.length }} consulta{{ timeline.length !== 1 ? 's' : '' }}</span>
                        </div>

                        <!-- Sin consultas -->
                        <div v-if="!timeline.length" class="flex flex-col items-center py-12 gap-3 text-on-surface-variant">
                            <span class="material-symbols-outlined text-5xl opacity-30">history</span>
                            <p class="font-medium">Aún no hay consultas registradas</p>
                        </div>

                        <!-- Timeline -->
                        <div v-else class="relative">
                            <div class="absolute left-[19px] top-2 bottom-0 w-0.5 bg-surface-container"></div>
                            <div class="space-y-8">
                                <div
                                    v-for="(record, i) in timeline"
                                    :key="record.id"
                                    class="relative pl-12"
                                >
                                    <div class="absolute left-0 top-1 w-10 h-10 rounded-full flex items-center justify-center z-10"
                                        :class="i === 0 ? 'bg-primary-fixed' : 'bg-secondary-container'"
                                    >
                                        <span class="material-symbols-outlined text-lg"
                                            :class="i === 0 ? 'text-on-primary-fixed' : 'text-on-secondary-container'"
                                            :style="i === 0 ? `font-variation-settings:'FILL' 1` : ''"
                                        >{{ i === 0 ? 'check_circle' : 'history_edu' }}</span>
                                    </div>
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-2 mb-2">
                                        <h4 class="font-bold text-base">Consulta de Seguimiento</h4>
                                        <span class="text-sm font-bold text-on-surface-variant bg-surface-container-low px-3 py-1 rounded-full">{{ record.date }}</span>
                                    </div>
                                    <div class="bg-surface-container-low/50 rounded-2xl p-4 space-y-3">
                                        <div class="flex flex-wrap gap-3 text-xs font-bold">
                                            <span v-if="record.weight" class="flex items-center gap-1 bg-white px-3 py-1 rounded-lg border border-surface-container">
                                                <span class="material-symbols-outlined text-primary" style="font-size:14px">scale</span>
                                                {{ record.weight }} kg
                                            </span>
                                            <span v-if="record.fat" class="flex items-center gap-1 bg-white px-3 py-1 rounded-lg border border-surface-container">
                                                <span class="material-symbols-outlined text-secondary" style="font-size:14px">percent</span>
                                                {{ record.fat }}% grasa
                                            </span>
                                            <span v-if="record.muscle" class="flex items-center gap-1 bg-white px-3 py-1 rounded-lg border border-surface-container">
                                                <span class="material-symbols-outlined text-tertiary" style="font-size:14px">fitness_center</span>
                                                {{ record.muscle }} kg masa magra
                                            </span>
                                            <span v-if="record.bmi" class="flex items-center gap-1 bg-white px-3 py-1 rounded-lg border border-surface-container">
                                                IMC {{ record.bmi }}
                                            </span>
                                        </div>
                                        <p v-if="record.summary" class="text-sm text-on-surface-variant leading-relaxed">{{ record.summary }}</p>
                                        <p v-else class="text-xs text-outline italic">Sin resumen de consulta</p>
                                    </div>
                                </div>

                                <!-- Próxima cita (placeholder) -->
                                <div class="relative pl-12 opacity-40">
                                    <div class="absolute left-0 top-1 w-10 h-10 rounded-full bg-surface-container flex items-center justify-center z-10">
                                        <span class="material-symbols-outlined text-outline text-lg">calendar_today</span>
                                    </div>
                                    <h4 class="font-bold text-base">Próximo Seguimiento</h4>
                                    <p class="text-xs text-on-surface-variant mt-1">Por programar</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfica de evolución -->
                    <div v-if="chartData.length" class="bg-surface-container-lowest rounded-3xl p-8 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                            <h2 class="text-2xl font-extrabold text-on-surface font-headline">Evolución de Composición Corporal</h2>
                            <div class="flex bg-surface-container-low p-1 rounded-xl gap-1">
                                <button
                                    v-for="tab in ['Peso', 'Grasa', 'Masa Magra']"
                                    :key="tab"
                                    class="px-4 py-1.5 text-xs font-bold rounded-lg transition-colors"
                                    :class="activeTab === tab ? 'bg-white text-primary shadow-sm' : 'text-on-surface-variant hover:text-on-surface'"
                                    @click="activeTab = tab"
                                >{{ tab }}</button>
                            </div>
                        </div>

                        <!-- Barras -->
                        <div class="relative h-56 w-full flex items-end justify-between gap-2 px-2">
                            <div class="absolute inset-0 flex flex-col justify-between py-2 pointer-events-none">
                                <div class="border-t border-surface-container w-full"></div>
                                <div class="border-t border-surface-container w-full"></div>
                                <div class="border-t border-surface-container w-full"></div>
                                <div class="border-t border-surface-container w-full"></div>
                            </div>
                            <div
                                v-for="(bar, i) in chartData"
                                :key="i"
                                class="flex-1 flex flex-col justify-end items-center group relative h-full"
                            >
                                <div
                                    class="w-full max-w-[44px] rounded-t-xl transition-all duration-300 cursor-pointer relative"
                                    :class="i === chartData.length - 1 ? 'bg-primary shadow-lg shadow-primary/20' : 'bg-primary/20 hover:bg-primary/30'"
                                    :style="{ height: barHeight(bar) + '%' }"
                                >
                                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap text-[10px] font-bold px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity"
                                        :class="i === chartData.length - 1 ? 'bg-primary text-on-primary' : 'bg-on-surface text-white'"
                                    >{{ barValue(bar) }}</div>
                                </div>
                                <span class="text-[10px] font-bold mt-3 uppercase"
                                    :class="i === chartData.length - 1 ? 'text-primary' : 'text-on-surface-variant'"
                                >{{ bar.label }}</span>
                            </div>
                        </div>

                        <!-- Resumen tendencia -->
                        <div v-if="chartSummary" class="mt-8 pt-6 border-t border-surface-container flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                :class="chartSummary.positive ? 'bg-primary-container text-primary' : 'bg-error-container/20 text-error'"
                            >
                                <span class="material-symbols-outlined">{{ chartSummary.icon }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-on-surface">{{ chartSummary.title }}</p>
                                <p class="text-xs text-on-surface-variant">{{ chartSummary.subtitle }}</p>
                            </div>
                        </div>
                    </div>

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
    patient:   { type: Object, required: true },
    latest:    { type: Object, default: null },
    trends:    { type: Object, default: () => ({}) },
    timeline:  { type: Array,  default: () => [] },
    chartData: { type: Array,  default: () => [] },
});

const activeTab = ref('Peso');

function barValue(bar) {
    if (activeTab.value === 'Peso')       return bar.weight ? bar.weight + ' kg' : '—';
    if (activeTab.value === 'Grasa')      return bar.fat    ? bar.fat    + '%'    : '—';
    if (activeTab.value === 'Masa Magra') return bar.muscle ? bar.muscle + ' kg'  : '—';
}

function barHeight(bar) {
    const key = activeTab.value === 'Peso' ? 'weight'
        : activeTab.value === 'Grasa' ? 'fat' : 'muscle';
    const values = props.chartData.map(b => parseFloat(b[key]) || 0).filter(v => v > 0);
    if (!values.length) return 8;
    const max = Math.max(...values);
    const val = parseFloat(bar[key]) || 0;
    return max ? Math.max(8, Math.round((val / max) * 85)) : 8;
}

const chartSummary = computed(() => {
    const key = activeTab.value === 'Peso' ? 'weight'
        : activeTab.value === 'Grasa' ? 'fat' : 'muscle';
    const values = props.chartData.map(b => parseFloat(b[key])).filter(v => !isNaN(v));
    if (values.length < 2) return null;
    const diff = values[values.length - 1] - values[0];
    const pct  = ((diff / values[0]) * 100).toFixed(1);
    const unit = key === 'fat' ? '%' : 'kg';
    // Para peso y grasa, bajar es positivo; para masa magra, subir es positivo
    const isPositive = key !== 'muscle' ? diff <= 0 : diff >= 0;
    return {
        positive: isPositive,
        icon:     key !== 'muscle' ? (diff <= 0 ? 'trending_down' : 'trending_up') : (diff >= 0 ? 'trending_up' : 'trending_down'),
        title:    isPositive ? 'Tendencia favorable' : 'Tendencia desfavorable',
        subtitle: `Variación de ${diff > 0 ? '+' : ''}${diff.toFixed(1)}${unit} (${pct}%) en los últimos ${values.length} registros`,
    };
});
</script>
