<template>
    <AppLayout>
        <div class="flex-1 flex overflow-hidden">

            <!-- Calendar Section -->
            <section class="flex-1 flex flex-col overflow-hidden p-8">

                <!-- Calendar Header -->
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-3xl font-extrabold font-headline text-on-surface tracking-tight">{{ monthLabel }}</h1>
                        <p class="text-on-surface-variant font-body mt-1">{{ weekLabel }}</p>
                    </div>

                    <!-- Navigation -->
                    <div class="flex items-center gap-4 bg-white p-1 rounded-xl shadow-sm">
                        <button
                            class="p-2 hover:bg-surface-container-low rounded-lg transition-colors"
                            @click="navigateWeek(-1)"
                        >
                            <span class="material-symbols-outlined">chevron_left</span>
                        </button>
                        <button
                            class="px-4 py-1.5 text-sm font-bold bg-surface-container-high rounded-lg text-on-surface"
                            @click="goToday"
                        >Hoy</button>
                        <button
                            class="p-2 hover:bg-surface-container-low rounded-lg transition-colors"
                            @click="navigateWeek(1)"
                        >
                            <span class="material-symbols-outlined">chevron_right</span>
                        </button>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3">
                        <button
                            @click="showModal = true"
                            class="flex items-center gap-2 bg-primary text-on-primary px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:opacity-90 active:scale-95 transition-all"
                        >
                            <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">add</span>
                            Nueva Cita
                        </button>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="flex-1 bg-white rounded-3xl shadow-sm flex flex-col border border-slate-100 overflow-hidden">

                    <!-- Day Labels Row -->
                    <div class="calendar-grid border-b border-surface-container-low bg-surface-bright/50">
                        <div class="p-4"></div>
                        <div
                            v-for="(day, idx) in weekDays"
                            :key="idx"
                            class="p-4 text-center"
                            :class="day.isToday ? 'bg-primary-container/20 border-x border-primary-container/10' : ''"
                        >
                            <span
                                class="block text-[10px] font-bold uppercase tracking-widest"
                                :class="day.isToday ? 'text-primary' : 'text-outline-variant'"
                            >{{ day.shortName }}</span>
                            <span
                                class="text-xl font-extrabold"
                                :class="day.isToday ? 'text-primary' : 'text-on-surface'"
                            >{{ day.dayNum }}</span>
                        </div>
                    </div>

                    <!-- Scrollable Grid -->
                    <div class="flex-1 overflow-y-auto no-scrollbar relative">
                        <div class="calendar-grid" :style="`min-height: ${timeSlots.length * 80}px`">

                            <!-- Time Column -->
                            <div class="bg-surface-bright/30">
                                <div
                                    v-for="slot in timeSlots"
                                    :key="slot"
                                    class="h-20 flex items-center justify-center text-[10px] font-bold text-outline-variant border-b border-surface-container-low"
                                >{{ slot }}</div>
                            </div>

                            <!-- Day Columns background -->
                            <div
                                v-for="(day, idx) in weekDays"
                                :key="'bg-' + idx"
                                class="border-r border-surface-container-low last:border-r-0"
                                :class="day.isToday ? 'bg-primary-container/5' : 'bg-white'"
                                :style="`min-height: ${timeSlots.length * 80}px`"
                            ></div>

                            <!-- Absolute Appointments -->
                            <template v-for="appt in appointments" :key="appt.id">
                                <div
                                    v-if="appt.dayOfWeek >= 1 && appt.dayOfWeek <= 5"
                                    class="absolute p-1"
                                    :style="appointmentStyle(appt)"
                                    @click="selectAppointment(appt)"
                                >
                                    <div
                                        class="h-full rounded-xl p-3 hover:scale-[1.02] transition-transform cursor-pointer border-l-4"
                                        :class="typeClass(appt.type)"
                                    >
                                        <p class="text-[10px] font-bold uppercase" :class="typeLabelClass(appt.type)">{{ appt.typeLabel }}</p>
                                        <h4 class="text-xs font-bold mt-0.5" :class="typeTextClass(appt.type)">{{ appt.patientName }}</h4>
                                        <p class="text-[10px] mt-1" :class="typeSubtextClass(appt.type)">{{ appt.timeRange }}</p>
                                    </div>
                                </div>
                            </template>

                        </div>
                    </div>
                </div>
            </section>

            <!-- Details Sidebar -->
            <aside class="w-80 border-l border-slate-100 bg-white p-8 overflow-y-auto flex flex-col shrink-0">
                <h3 class="text-xl font-bold font-headline mb-6 text-on-surface">Detalles de la Cita</h3>

                <!-- Empty state -->
                <div v-if="!selected" class="flex-1 flex flex-col items-center justify-center text-center text-on-surface-variant">
                    <span class="material-symbols-outlined text-5xl mb-3 opacity-30">event_note</span>
                    <p class="text-sm font-medium">Selecciona una cita<br/>para ver los detalles.</p>
                </div>

                <!-- Selected Appointment -->
                <div v-else>
                    <!-- Patient Card -->
                    <div class="bg-primary-container/20 rounded-3xl p-6 border-2 border-primary/10 mb-6">
                        <div class="flex items-center gap-4 mb-4">
                            <img
                                :alt="selected.patientName"
                                :src="selected.patientAvatar ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(selected.patientName)}&background=7af9c7&color=004933`"
                                class="w-16 h-16 rounded-2xl object-cover ring-4 ring-white"
                            />
                            <div>
                                <h4 class="text-lg font-bold text-on-primary-container leading-tight">{{ selected.patientName }}</h4>
                                <span class="inline-block bg-primary text-on-primary text-[10px] font-bold px-2 py-0.5 rounded-full mt-1">
                                    {{ selected.typeLabel.toUpperCase() }}
                                </span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 text-sm">
                                <span class="material-symbols-outlined text-primary text-lg">schedule</span>
                                <span class="text-on-surface-variant font-medium">{{ selected.timeRange }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm">
                                <span class="material-symbols-outlined text-primary text-lg">event</span>
                                <span class="text-on-surface-variant font-medium">{{ formatDate(selected.scheduledAt) }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm">
                                <span class="material-symbols-outlined text-primary text-lg">info</span>
                                <span
                                    class="text-xs font-bold px-2 py-0.5 rounded-full"
                                    :class="statusBadgeClass(selected.status)"
                                >{{ selected.statusLabel }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div v-if="selected.notes" class="mb-6">
                        <h5 class="text-[10px] font-bold text-outline-variant uppercase tracking-widest mb-3">Notas de Preparación</h5>
                        <p class="text-xs text-on-surface-variant leading-relaxed">{{ selected.notes }}</p>
                    </div>

                    <!-- Summary -->
                    <div v-if="selected.summary" class="mb-6">
                        <h5 class="text-[10px] font-bold text-outline-variant uppercase tracking-widest mb-3">Resumen Clínico</h5>
                        <p class="text-xs text-on-surface-variant leading-relaxed">{{ selected.summary }}</p>
                    </div>

                    <div class="h-[1px] bg-slate-100 mb-6"></div>

                    <!-- Quick Actions -->
                    <div>
                        <h5 class="text-[10px] font-bold text-outline-variant uppercase tracking-widest mb-3">Acciones Rápidas</h5>
                        <div class="grid grid-cols-2 gap-3">
                            <button class="flex flex-col items-center justify-center p-4 rounded-2xl bg-surface-container-low hover:bg-primary-container/30 transition-colors group">
                                <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary mb-2">video_call</span>
                                <span class="text-[10px] font-bold">Llamada</span>
                            </button>
                            <button class="flex flex-col items-center justify-center p-4 rounded-2xl bg-surface-container-low hover:bg-primary-container/30 transition-colors group">
                                <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary mb-2">description</span>
                                <span class="text-[10px] font-bold">Expediente</span>
                            </button>
                            <button class="flex flex-col items-center justify-center p-4 rounded-2xl bg-surface-container-low hover:bg-primary-container/30 transition-colors group">
                                <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary mb-2">edit</span>
                                <span class="text-[10px] font-bold">Reprogramar</span>
                            </button>
                            <button
                                @click="showCancelModal = true"
                                class="flex flex-col items-center justify-center p-4 rounded-2xl bg-surface-container-low hover:bg-error-container/20 transition-colors group">
                                <span class="material-symbols-outlined text-on-surface-variant group-hover:text-error mb-2">cancel</span>
                                <span class="text-[10px] font-bold">Cancelar</span>
                            </button>
                        </div>
                    </div>

                    <div class="mt-auto pt-6">
                        <button class="w-full bg-on-background text-white py-3 rounded-xl font-bold text-sm hover:opacity-90 transition-opacity">
                            Comenzar Consulta
                        </button>
                    </div>
                </div>
            </aside>

        </div>

        <NewAppointmentModal
            :show="showModal"
            :patients="patients"
            @close="showModal = false"
        />

        <CancelConfirmModal
            :show="showCancelModal"
            :appointment="selected"
            @close="showCancelModal = false"
            @cancelled="onCancelled"
        />
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import NewAppointmentModal from '@/Components/NewAppointmentModal.vue';
import CancelConfirmModal from '@/Components/CancelConfirmModal.vue';

const props = defineProps({
    appointments: { type: Array,  default: () => [] },
    weekStart:    { type: String, default: '' },
    weekEnd:      { type: String, default: '' },
    weekLabel:    { type: String, default: '' },
    monthLabel:   { type: String, default: '' },
    today:        { type: String, default: '' },
    patients:     { type: Array,  default: () => [] },
});

// ─── Estado ──────────────────────────────────────────────────────────────────
const selected        = ref(null);
const showModal       = ref(false);
const showCancelModal = ref(false);

function onCancelled() {
    selected.value = null;
    router.reload({ preserveScroll: true });
}

// ─── Grilla ──────────────────────────────────────────────────────────────────
// Horas visibles 08:00 → 18:00
const START_HOUR   = 0;
const END_HOUR     = 24;
const SLOT_PX      = 80; // px por hora

const timeSlots = computed(() => {
    const slots = [];
    for (let h = START_HOUR; h < END_HOUR; h++) {
        const suffix  = h < 12 ? 'AM' : 'PM';
        const display = h === 0 ? 12 : h <= 12 ? h : h - 12;
        slots.push(`${String(display).padStart(2, '0')}:00 ${suffix}`);
    }
    return slots;
});

// ─── Días de la semana actual ─────────────────────────────────────────────
const dayNames = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie'];

const weekDays = computed(() => {
    if (!props.weekStart) return [];
    const start = new Date(props.weekStart + 'T12:00:00');
    return dayNames.map((shortName, i) => {
        const d = new Date(start);
        d.setDate(start.getDate() + i);
        const dateStr = d.toISOString().slice(0, 10);
        return {
            shortName,
            dayNum:  d.getDate(),
            dateStr,
            isToday: dateStr === props.today,
        };
    });
});

// ─── Posición absoluta de cada cita ──────────────────────────────────────────
// La grilla tiene 6 columnas: [80px fija] + [5 × 1fr]
// Columna de la cita = dayOfWeek (1=Lun…5=Vie)  → offset = (col-1) * (100%-80px)/5
function appointmentStyle(appt) {
    const colIndex  = appt.dayOfWeek - 1;                     // 0–4
    const topPx     = (appt.startHour - START_HOUR) * SLOT_PX + (appt.startMin / 60) * SLOT_PX;
    const heightPx  = Math.max((appt.duration / 60) * SLOT_PX, SLOT_PX * 0.75);

    // left: 80px + colIndex × ((100% - 80px) / 5)
    const left  = `calc(80px + ${colIndex} * ((100% - 80px) / 5))`;
    const width = `calc((100% - 80px) / 5)`;

    return {
        position: 'absolute',
        top:    `${topPx}px`,
        height: `${heightPx}px`,
        left,
        width,
        padding: '4px',
        zIndex:  '10',
    };
}

// ─── Estilos por tipo de cita ─────────────────────────────────────────────────
const typeStyles = {
    follow_up:  { bg: 'bg-tertiary-container/30 border-tertiary',   label: 'text-tertiary',            text: 'text-on-tertiary-container', sub: 'text-tertiary' },
    initial:    { bg: 'bg-primary-container/40 border-primary',     label: 'text-primary',             text: 'text-on-primary-container',  sub: 'text-on-primary-container/80' },
    emergency:  { bg: 'bg-error-container/20 border-error',         label: 'text-error',               text: 'text-on-error-container',    sub: 'text-on-error-container' },
    online:     { bg: 'bg-secondary-container/40 border-secondary', label: 'text-secondary',           text: 'text-on-secondary-container', sub: 'text-on-secondary-container' },
};

function typeClass(type)        { return typeStyles[type]?.bg    ?? typeStyles.follow_up.bg; }
function typeLabelClass(type)   { return typeStyles[type]?.label ?? typeStyles.follow_up.label; }
function typeTextClass(type)    { return typeStyles[type]?.text  ?? typeStyles.follow_up.text; }
function typeSubtextClass(type) { return typeStyles[type]?.sub   ?? typeStyles.follow_up.sub; }

// ─── Badge de estado ──────────────────────────────────────────────────────────
function statusBadgeClass(status) {
    const map = {
        scheduled:  'bg-secondary-container text-on-secondary-container',
        confirmed:  'bg-primary-container text-on-primary-container',
        completed:  'bg-surface-container-high text-on-surface-variant',
        no_show:    'bg-error-container text-on-error-container',
    };
    return map[status] ?? 'bg-surface-container-high text-on-surface-variant';
}

// ─── Acciones ─────────────────────────────────────────────────────────────────
function selectAppointment(appt) {
    selected.value = appt;
}

function formatDate(iso) {
    return new Date(iso).toLocaleDateString('es-MX', {
        weekday: 'long', day: 'numeric', month: 'long',
    }).replace(/^\w/, c => c.toUpperCase());
}

function navigateWeek(direction) {
    const base = new Date(props.weekStart + 'T12:00:00');
    base.setDate(base.getDate() + direction * 7);
    const newWeek = base.toISOString().slice(0, 10);
    router.get(route('calendario'), { week: newWeek }, { preserveState: false });
}

function goToday() {
    router.get(route('calendario'), {}, { preserveState: false });
}
</script>

<style scoped>
.calendar-grid {
    display: grid;
    grid-template-columns: 80px repeat(5, 1fr);
}
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
