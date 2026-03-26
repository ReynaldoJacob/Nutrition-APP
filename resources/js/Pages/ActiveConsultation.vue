<template>
    <div class="bg-surface font-body text-on-surface antialiased min-h-screen">

        <!-- Top Navigation Bar -->
        <header class="fixed top-0 w-full flex items-center justify-between px-6 py-3 bg-white/80 backdrop-blur-md z-50 shadow-sm">
            <div class="flex items-center gap-6">
                <div class="text-primary font-extrabold tracking-tight font-headline text-lg">Clinical Sanctuary</div>

                <!-- Indicador de consulta activa -->
                <div class="flex items-center gap-2 bg-primary-container/30 px-3 py-1.5 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    <span class="text-xs font-semibold text-primary">En Consulta · {{ elapsed }}</span>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <!-- Info del paciente -->
                <div class="flex items-center gap-3 border-r border-surface-container pr-4">
                    <div class="text-right">
                        <div class="font-headline font-bold text-sm text-on-surface">{{ patient.name }}</div>
                        <div class="text-[10px] text-on-surface-variant uppercase tracking-wider">{{ patient.code }}</div>
                    </div>
                    <img
                        :alt="patient.name"
                        :src="patient.avatar ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(patient.name)}&background=7af9c7&color=004933`"
                        class="w-10 h-10 rounded-full object-cover ring-2 ring-primary/20"
                    />
                </div>

                <button
                    @click="openFinish"
                    class="bg-primary text-on-primary px-5 py-2 rounded-xl font-headline font-bold text-sm hover:opacity-90 active:scale-95 transition-all shadow-lg shadow-primary/10 flex items-center gap-2"
                >
                    <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                    Finalizar Consulta
                </button>
            </div>
        </header>

        <div class="flex h-screen pt-[57px] overflow-hidden">

            <!-- ─── Left Sidebar: Visita Anterior ─────────────────────────────── -->
            <aside class="w-80 flex flex-col bg-surface-container overflow-y-auto hide-scrollbar shrink-0">
                <div class="p-6 space-y-6">
                    <div class="flex items-center gap-2 text-on-surface-variant">
                        <span class="material-symbols-outlined text-lg">history</span>
                        <h2 class="font-headline font-bold text-sm uppercase tracking-widest">Visita Anterior</h2>
                    </div>

                    <!-- Sin visita previa -->
                    <div v-if="!previousVisit" class="bg-surface-container-lowest p-5 rounded-2xl text-center text-on-surface-variant space-y-2">
                        <span class="material-symbols-outlined text-3xl opacity-30">event_note</span>
                        <p class="text-xs font-medium">No hay registros de visitas anteriores.</p>
                    </div>

                    <!-- Con visita previa -->
                    <div v-else class="space-y-4">
                        <div class="bg-surface-container-lowest p-5 rounded-2xl space-y-4">
                            <div class="text-xs text-on-surface-variant">{{ formatDate(previousVisit.date) }}</div>

                            <div class="grid grid-cols-2 gap-4">
                                <div v-if="previousVisit.weight" class="space-y-1">
                                    <span class="text-[10px] text-on-surface-variant uppercase font-bold">Peso</span>
                                    <div class="font-headline font-bold text-lg text-on-surface">
                                        {{ previousVisit.weight }} <span class="text-xs font-normal">kg</span>
                                    </div>
                                </div>
                                <div v-if="previousVisit.bodyFat" class="space-y-1">
                                    <span class="text-[10px] text-on-surface-variant uppercase font-bold">Grasa Corp.</span>
                                    <div class="font-headline font-bold text-lg text-on-surface">
                                        {{ previousVisit.bodyFat }} <span class="text-xs font-normal">%</span>
                                    </div>
                                </div>
                                <div v-if="previousVisit.muscleMass" class="space-y-1">
                                    <span class="text-[10px] text-on-surface-variant uppercase font-bold">Masa Musc.</span>
                                    <div class="font-headline font-bold text-lg text-on-surface">
                                        {{ previousVisit.muscleMass }} <span class="text-xs font-normal">kg</span>
                                    </div>
                                </div>
                                <div v-if="previousVisit.bmi" class="space-y-1">
                                    <span class="text-[10px] text-on-surface-variant uppercase font-bold">IMC</span>
                                    <div class="font-headline font-bold text-lg text-on-surface">
                                        {{ previousVisit.bmi }}
                                    </div>
                                </div>
                            </div>

                            <div v-if="previousVisit.waist || previousVisit.hip || previousVisit.bloodPressure" class="pt-4 border-t border-surface-variant space-y-2">
                                <span class="text-[10px] text-on-surface-variant uppercase font-bold tracking-wider">Medidas</span>
                                <div class="space-y-1.5">
                                    <div v-if="previousVisit.waist" class="flex justify-between text-xs">
                                        <span class="text-on-surface-variant">Cintura</span>
                                        <span class="font-bold">{{ previousVisit.waist }} cm</span>
                                    </div>
                                    <div v-if="previousVisit.hip" class="flex justify-between text-xs">
                                        <span class="text-on-surface-variant">Cadera</span>
                                        <span class="font-bold">{{ previousVisit.hip }} cm</span>
                                    </div>
                                    <div v-if="previousVisit.bloodPressure" class="flex justify-between text-xs">
                                        <span class="text-on-surface-variant">Presión Art.</span>
                                        <span class="font-bold">{{ previousVisit.bloodPressure }}</span>
                                    </div>
                                </div>
                            </div>

                            <div v-if="previousVisit.summary" class="pt-4 border-t border-surface-variant space-y-2">
                                <span class="text-[10px] text-on-surface-variant uppercase font-bold tracking-wider">Notas Clínicas</span>
                                <p class="text-sm leading-relaxed text-on-surface-variant">{{ previousVisit.summary }}</p>
                            </div>
                        </div>

                        <!-- Contexto del paciente -->
                        <div v-if="patient.allergies?.length || patient.medicalConditions?.length" class="bg-error-container/20 p-4 rounded-2xl border border-error/10 space-y-3">
                            <span class="text-[10px] font-bold uppercase text-error tracking-wider">Alertas Clínicas</span>
                            <div v-if="patient.allergies?.length" class="space-y-1">
                                <p class="text-[10px] text-on-surface-variant font-bold">ALERGIAS</p>
                                <p class="text-xs text-on-surface-variant">{{ patient.allergies.join(', ') }}</p>
                            </div>
                            <div v-if="patient.medicalConditions?.length" class="space-y-1">
                                <p class="text-[10px] text-on-surface-variant font-bold">CONDICIONES</p>
                                <p class="text-xs text-on-surface-variant">{{ patient.medicalConditions.join(', ') }}</p>
                            </div>
                        </div>

                        <!-- Objetivo -->
                        <div v-if="patient.goalWeight" class="bg-primary-container/20 p-5 rounded-2xl border border-primary/10">
                            <h3 class="font-headline font-bold text-sm text-primary mb-2">Meta del Paciente</h3>
                            <div class="flex items-baseline gap-1">
                                <span class="font-headline font-extrabold text-2xl text-on-surface">{{ patient.goalWeight }}</span>
                                <span class="text-sm text-on-surface-variant">kg objetivo</span>
                            </div>
                            <div v-if="patient.currentWeight" class="mt-2 text-xs text-on-surface-variant">
                                Faltan {{ Math.abs(patient.currentWeight - patient.goalWeight).toFixed(1) }} kg para la meta
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- ─── Main: Formulario de mediciones ────────────────────────────── -->
            <main class="flex-1 bg-surface overflow-y-auto hide-scrollbar p-8">
                <div class="max-w-4xl mx-auto space-y-10">
                    <header>
                        <h1 class="font-headline font-extrabold text-4xl text-on-surface tracking-tight">Evaluación Activa</h1>
                        <p class="text-on-surface-variant mt-2">
                            {{ appointment.typeLabel }} · {{ formatDateFull(appointment.scheduledAt) }}
                        </p>
                    </header>

                    <!-- Tarjetas principales -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                        <!-- Peso -->
                        <div class="bg-surface-container-lowest p-8 rounded-[2.5rem] shadow-sm flex flex-col justify-between min-h-[220px]">
                            <div class="flex justify-between items-start">
                                <div>
                                    <label class="text-xs font-bold text-on-surface-variant uppercase tracking-[0.2em]">Peso Corporal</label>
                                    <div class="mt-2 flex items-baseline gap-2">
                                        <input
                                            v-model="form.weight"
                                            class="w-36 bg-transparent border-none p-0 font-headline font-extrabold text-6xl text-on-surface focus:ring-0 placeholder:text-surface-dim"
                                            placeholder="00.0"
                                            type="number"
                                            min="1" max="500" step="0.1"
                                        />
                                        <span class="font-headline font-bold text-2xl text-on-surface-variant">kg</span>
                                    </div>
                                </div>
                                <div class="bg-primary-container p-3 rounded-2xl">
                                    <span class="material-symbols-outlined text-primary text-3xl">scale</span>
                                </div>
                            </div>
                            <div v-if="previousVisit?.weight" class="mt-4 text-sm font-medium flex items-center gap-1"
                                :class="weightDiff < 0 ? 'text-primary' : weightDiff > 0 ? 'text-error' : 'text-on-surface-variant'"
                            >
                                <span class="material-symbols-outlined text-sm">{{ weightDiff <= 0 ? 'trending_down' : 'trending_up' }}</span>
                                Último registro: {{ previousVisit.weight }} kg
                                <span v-if="form.weight && weightDiff !== 0">
                                    ({{ weightDiff > 0 ? '+' : '' }}{{ weightDiff.toFixed(1) }} kg)
                                </span>
                            </div>
                        </div>

                        <!-- Grasa Corporal -->
                        <div class="bg-surface-container-lowest p-8 rounded-[2.5rem] shadow-sm flex flex-col justify-between min-h-[220px]">
                            <div class="flex justify-between items-start">
                                <div>
                                    <label class="text-xs font-bold text-on-surface-variant uppercase tracking-[0.2em]">Grasa Corporal %</label>
                                    <div class="mt-2 flex items-baseline gap-2">
                                        <input
                                            v-model="form.body_fat"
                                            class="w-36 bg-transparent border-none p-0 font-headline font-extrabold text-6xl text-on-surface focus:ring-0 placeholder:text-surface-dim"
                                            placeholder="00.0"
                                            type="number"
                                            min="1" max="100" step="0.1"
                                        />
                                        <span class="font-headline font-bold text-2xl text-on-surface-variant">%</span>
                                    </div>
                                </div>
                                <div class="bg-secondary-container p-3 rounded-2xl">
                                    <span class="material-symbols-outlined text-on-secondary-container text-3xl">fitness_center</span>
                                </div>
                            </div>
                            <div class="mt-4 text-sm text-on-surface-variant font-medium">
                                Método: Plicometría / Bioimpedancia
                            </div>
                        </div>

                        <!-- Masa Muscular -->
                        <div class="bg-surface-container-lowest p-8 rounded-[2.5rem] shadow-sm flex flex-col justify-between min-h-[220px]">
                            <div class="flex justify-between items-start">
                                <div>
                                    <label class="text-xs font-bold text-on-surface-variant uppercase tracking-[0.2em]">Masa Muscular</label>
                                    <div class="mt-2 flex items-baseline gap-2">
                                        <input
                                            v-model="form.muscle_mass"
                                            class="w-36 bg-transparent border-none p-0 font-headline font-extrabold text-6xl text-on-surface focus:ring-0 placeholder:text-surface-dim"
                                            placeholder="00.0"
                                            type="number"
                                            min="1" max="300" step="0.1"
                                        />
                                        <span class="font-headline font-bold text-2xl text-on-surface-variant">kg</span>
                                    </div>
                                </div>
                                <div class="bg-tertiary-container p-3 rounded-2xl">
                                    <span class="material-symbols-outlined text-on-tertiary-container text-3xl">monitoring</span>
                                </div>
                            </div>
                        </div>

                        <!-- IMC (calculado) -->
                        <div class="bg-surface-container-lowest p-8 rounded-[2.5rem] shadow-sm flex flex-col justify-between min-h-[220px]">
                            <div class="flex justify-between items-start">
                                <div>
                                    <label class="text-xs font-bold text-on-surface-variant uppercase tracking-[0.2em]">IMC (BMI)</label>
                                    <div class="mt-2 flex items-baseline gap-2">
                                        <div
                                            class="font-headline font-extrabold text-6xl"
                                            :class="bmi ? bmiColor : 'text-surface-dim'"
                                        >{{ bmi ?? '--.-' }}</div>
                                    </div>
                                </div>
                                <div class="bg-surface-container-high p-3 rounded-2xl">
                                    <span class="material-symbols-outlined text-on-surface-variant text-3xl">calculate</span>
                                </div>
                            </div>
                            <div class="mt-4 text-xs font-medium" :class="bmi ? bmiColor : 'text-on-surface-variant'">
                                {{ bmiLabel }}
                            </div>
                        </div>
                    </div>

                    <!-- Signos vitales y circunferencias -->
                    <section class="space-y-6">
                        <h3 class="font-headline font-bold text-xl text-on-surface">Signos Vitales y Circunferencias</h3>
                        <div class="grid grid-cols-3 gap-6">
                            <div class="bg-surface-container-low p-5 rounded-2xl">
                                <label class="text-[10px] font-bold text-on-surface-variant uppercase block mb-2">Cintura (cm)</label>
                                <input
                                    v-model="form.waist"
                                    class="w-full bg-transparent border-b-2 border-outline-variant focus:border-primary border-t-0 border-x-0 p-0 text-xl font-headline font-bold focus:ring-0"
                                    type="number" min="30" max="300" step="0.5"
                                />
                            </div>
                            <div class="bg-surface-container-low p-5 rounded-2xl">
                                <label class="text-[10px] font-bold text-on-surface-variant uppercase block mb-2">Cadera (cm)</label>
                                <input
                                    v-model="form.hip"
                                    class="w-full bg-transparent border-b-2 border-outline-variant focus:border-primary border-t-0 border-x-0 p-0 text-xl font-headline font-bold focus:ring-0"
                                    type="number" min="30" max="300" step="0.5"
                                />
                            </div>
                            <div class="bg-surface-container-low p-5 rounded-2xl">
                                <label class="text-[10px] font-bold text-on-surface-variant uppercase block mb-2">Presión Arterial</label>
                                <input
                                    v-model="form.blood_pressure"
                                    class="w-full bg-transparent border-b-2 border-outline-variant focus:border-primary border-t-0 border-x-0 p-0 text-xl font-headline font-bold focus:ring-0"
                                    placeholder="120/80"
                                    type="text"
                                />
                            </div>
                        </div>
                    </section>
                </div>
            </main>

            <!-- ─── Right Panel: Notas y Objetivos ─────────────────────────────── -->
            <aside class="w-96 flex flex-col bg-surface border-l border-surface-container-high overflow-y-auto hide-scrollbar shrink-0">
                <div class="p-6 space-y-8">

                    <!-- Notas de la cita previa -->
                    <section v-if="appointment.notes" class="space-y-3">
                        <div class="flex items-center gap-2 text-on-surface-variant">
                            <span class="material-symbols-outlined text-lg">sticky_note_2</span>
                            <h2 class="font-headline font-bold text-sm uppercase tracking-widest">Preparación</h2>
                        </div>
                        <div class="bg-primary-container/20 rounded-2xl p-4 border border-primary/10">
                            <p class="text-sm leading-relaxed text-on-surface-variant">{{ appointment.notes }}</p>
                        </div>
                    </section>

                    <!-- Notas de la consulta -->
                    <section class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-on-surface">
                                <span class="material-symbols-outlined text-lg">edit_note</span>
                                <h2 class="font-headline font-bold text-sm uppercase tracking-widest">Notas de Consulta</h2>
                            </div>
                            <span class="text-xs text-on-surface-variant">{{ form.summary.length }}/5000</span>
                        </div>
                        <div class="relative">
                            <textarea
                                v-model="form.summary"
                                class="w-full bg-surface-container-lowest rounded-3xl border-none focus:ring-2 focus:ring-primary/20 p-5 text-sm leading-relaxed text-on-surface placeholder:text-on-surface-variant/40 shadow-sm resize-none"
                                placeholder="Comienza a escribir las observaciones clínicas..."
                                rows="12"
                                maxlength="5000"
                            ></textarea>
                        </div>
                    </section>

                    <!-- Estado rápido -->
                    <div class="pt-4">
                        <button
                            @click="openFinish"
                            class="w-full bg-on-surface text-surface-container-lowest rounded-2xl p-5 flex items-center justify-between hover:opacity-90 transition-opacity"
                        >
                            <div class="space-y-1 text-left">
                                <div class="text-[10px] font-bold uppercase opacity-60">Guardar y cerrar</div>
                                <div class="text-sm font-bold">Finalizar Consulta</div>
                            </div>
                            <span class="material-symbols-outlined text-primary-fixed" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        </button>
                    </div>
                </div>
            </aside>
        </div>

        <!-- ─── Modal: Confirmar finalización ─────────────────────────────────── -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="showFinishModal"
                    class="fixed inset-0 z-[60] flex items-center justify-center bg-on-surface/30 backdrop-blur-sm p-4"
                    @click.self="showFinishModal = false"
                >
                    <Transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="opacity-0 scale-90 translate-y-4"
                        enter-to-class="opacity-100 scale-100 translate-y-0"
                        appear
                    >
                        <div v-if="showFinishModal" class="w-full max-w-md bg-surface-container-lowest rounded-[2rem] shadow-2xl overflow-hidden">

                            <div class="px-8 pt-8 pb-6">
                                <div class="flex items-start gap-4 mb-6">
                                    <div class="w-12 h-12 rounded-2xl bg-primary-container flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-primary text-2xl" style="font-variation-settings: 'FILL' 1;">task_alt</span>
                                    </div>
                                    <div>
                                        <h3 class="font-headline text-xl font-extrabold text-on-surface">¿Finalizar la consulta?</h3>
                                        <p class="text-sm text-on-surface-variant mt-1">Se guardarán todas las mediciones y notas ingresadas.</p>
                                    </div>
                                </div>

                                <!-- Resumen de lo que se guardará -->
                                <div class="bg-surface-container-low rounded-2xl p-4 space-y-2 text-sm mb-6">
                                    <div class="flex justify-between">
                                        <span class="text-on-surface-variant">Paciente</span>
                                        <span class="font-bold">{{ patient.name }}</span>
                                    </div>
                                    <div v-if="form.weight" class="flex justify-between">
                                        <span class="text-on-surface-variant">Peso registrado</span>
                                        <span class="font-bold">{{ form.weight }} kg</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-on-surface-variant">Notas</span>
                                        <span class="font-bold">{{ form.summary ? form.summary.slice(0,30) + (form.summary.length > 30 ? '…' : '') : 'Sin notas' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-on-surface-variant">Duración</span>
                                        <span class="font-bold">{{ elapsed }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="px-8 py-6 bg-surface-container/20 flex flex-col-reverse sm:flex-row gap-3 justify-end">
                                <button
                                    type="button"
                                    @click="showFinishModal = false"
                                    :disabled="finishing"
                                    class="w-full sm:w-auto px-6 py-3.5 text-on-surface-variant font-bold rounded-2xl hover:bg-surface-container-high transition-all disabled:opacity-50"
                                >Seguir editando</button>
                                <button
                                    type="button"
                                    @click="submitFinish"
                                    :disabled="finishing"
                                    class="w-full sm:w-auto px-8 py-3.5 bg-primary text-on-primary font-bold rounded-2xl shadow-lg shadow-primary/20 hover:opacity-90 hover:scale-[1.02] active:scale-95 transition-all disabled:opacity-60 flex items-center justify-center gap-2"
                                >
                                    <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">
                                        {{ finishing ? 'hourglass_empty' : 'check_circle' }}
                                    </span>
                                    {{ finishing ? 'Guardando...' : 'Confirmar y cerrar' }}
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    appointment:   { type: Object, required: true },
    patient:       { type: Object, required: true },
    previousVisit: { type: Object, default: null },
});

// ─── Cronómetro ───────────────────────────────────────────────────────────────
const startedAt = new Date(props.appointment.startedAt);
const elapsed   = ref('00:00');
let timer = null;

function updateElapsed() {
    const diff = Math.floor((Date.now() - startedAt.getTime()) / 1000);
    const h    = Math.floor(diff / 3600);
    const m    = Math.floor((diff % 3600) / 60);
    const s    = diff % 60;
    if (h > 0) {
        elapsed.value = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    } else {
        elapsed.value = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    }
}

onMounted(() => {
    updateElapsed();
    timer = setInterval(updateElapsed, 1000);
});

onUnmounted(() => clearInterval(timer));

// ─── Formulario de mediciones ─────────────────────────────────────────────────
const form = ref({
    weight:         '',
    body_fat:       '',
    muscle_mass:    '',
    waist:          '',
    hip:            '',
    blood_pressure: '',
    summary:        '',
});

// ─── IMC calculado ────────────────────────────────────────────────────────────
const bmi = computed(() => {
    const w = parseFloat(form.value.weight);
    const h = parseFloat(props.patient.height);
    if (!w || !h) return null;
    const hm = h / 100;
    return (w / (hm * hm)).toFixed(1);
});

const bmiColor = computed(() => {
    const v = parseFloat(bmi.value);
    if (!v) return 'text-surface-dim';
    if (v < 18.5) return 'text-tertiary';
    if (v < 25)   return 'text-primary';
    if (v < 30)   return 'text-secondary';
    return 'text-error';
});

const bmiLabel = computed(() => {
    const v = parseFloat(bmi.value);
    if (!v) return 'Calculado automáticamente a partir de peso/estatura';
    if (v < 18.5) return `IMC ${bmi.value} — Bajo peso`;
    if (v < 25)   return `IMC ${bmi.value} — Peso normal ✓`;
    if (v < 30)   return `IMC ${bmi.value} — Sobrepeso`;
    return `IMC ${bmi.value} — Obesidad`;
});

// ─── Diferencia de peso ───────────────────────────────────────────────────────
const weightDiff = computed(() => {
    const current  = parseFloat(form.value.weight);
    const previous = parseFloat(props.previousVisit?.weight ?? 0);
    if (!current || !previous) return 0;
    return current - previous;
});

// ─── Modal de finalización ────────────────────────────────────────────────────
const showFinishModal = ref(false);
const finishing       = ref(false);

function openFinish() {
    showFinishModal.value = true;
}

function submitFinish() {
    finishing.value = true;
    router.post(route('citas.finish', props.appointment.id), {
        weight:         form.value.weight       || null,
        body_fat:       form.value.body_fat     || null,
        muscle_mass:    form.value.muscle_mass  || null,
        waist:          form.value.waist        || null,
        hip:            form.value.hip          || null,
        blood_pressure: form.value.blood_pressure || null,
        summary:        form.value.summary      || null,
    }, {
        onFinish: () => { finishing.value = false; },
    });
}

// ─── Helpers de fecha ─────────────────────────────────────────────────────────
function formatDate(iso) {
    return new Date(iso).toLocaleDateString('es-MX', {
        day: 'numeric', month: 'long', year: 'numeric',
    }).replace(/^\w/, c => c.toUpperCase());
}

function formatDateFull(iso) {
    return new Date(iso).toLocaleDateString('es-MX', {
        weekday: 'long', day: 'numeric', month: 'long',
    }).replace(/^\w/, c => c.toUpperCase());
}
</script>

<style scoped>
.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
