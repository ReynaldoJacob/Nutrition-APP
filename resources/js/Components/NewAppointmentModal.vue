<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="show"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-on-surface/20 backdrop-blur-sm p-4"
                @click.self="$emit('close')"
            >
                <!-- Modal Window -->
                <div class="w-full max-w-2xl bg-surface-container-lowest rounded-[2rem] shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">

                    <!-- Header -->
                    <div class="px-8 pt-8 pb-4 flex justify-between items-start shrink-0">
                        <div>
                            <h3 class="font-headline text-2xl font-extrabold text-on-surface">Agendar Nueva Cita</h3>
                            <p class="text-on-surface-variant mt-1">Completa los detalles de la consulta nutricional.</p>
                        </div>
                        <button
                            type="button"
                            @click="$emit('close')"
                            class="p-2 hover:bg-surface-container-high rounded-full transition-colors text-on-surface-variant"
                        >
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Modal Content (Scrollable) -->
                    <form @submit.prevent="submit" class="flex flex-col overflow-hidden">
                        <div class="px-8 py-4 overflow-y-auto no-scrollbar space-y-6 flex-1">

                            <!-- Error general -->
                            <div
                                v-if="errors.general"
                                class="bg-error-container/30 text-on-error-container text-sm font-medium px-4 py-3 rounded-2xl"
                            >
                                {{ errors.general }}
                            </div>

                            <!-- Patient Selection -->
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-on-surface-variant ml-1">Seleccionar Paciente</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline">person_search</span>
                                    <select
                                        v-model="form.patient_id"
                                        class="w-full pl-12 pr-10 py-4 bg-surface-container-low border-none rounded-2xl focus:ring-2 focus:ring-primary appearance-none font-medium text-on-surface"
                                        :class="errors.patient_id ? 'ring-2 ring-error' : ''"
                                    >
                                        <option value="">Buscar o seleccionar paciente...</option>
                                        <option v-for="p in patients" :key="p.id" :value="p.userId">{{ p.name }}</option>
                                    </select>
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline pointer-events-none">expand_more</span>
                                </div>
                                <p v-if="errors.patient_id" class="text-xs text-error ml-1">{{ errors.patient_id }}</p>
                            </div>

                            <!-- Date and Time Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-bold text-on-surface-variant ml-1">Fecha de la Cita</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline">calendar_month</span>
                                        <input
                                            v-model="form.date"
                                            type="date"
                                            class="w-full pl-12 pr-4 py-4 bg-surface-container-low border-none rounded-2xl focus:ring-2 focus:ring-primary font-medium text-on-surface"
                                            :class="errors.date ? 'ring-2 ring-error' : ''"
                                        />
                                    </div>
                                    <p v-if="errors.date" class="text-xs text-error ml-1">{{ errors.date }}</p>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-bold text-on-surface-variant ml-1">Hora Inicio</label>
                                        <input
                                            v-model="form.start_time"
                                            type="time"
                                            class="w-full px-4 py-4 bg-surface-container-low border-none rounded-2xl focus:ring-2 focus:ring-primary font-medium text-on-surface"
                                            :class="errors.start_time ? 'ring-2 ring-error' : ''"
                                        />
                                        <p v-if="errors.start_time" class="text-xs text-error ml-1">{{ errors.start_time }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-sm font-bold text-on-surface-variant ml-1">Hora de Fin</label>
                                        <input
                                            v-model="form.end_time"
                                            type="time"
                                            class="w-full px-4 py-4 bg-surface-container-low border-none rounded-2xl focus:ring-2 focus:ring-primary font-medium text-on-surface"
                                            :class="errors.end_time ? 'ring-2 ring-error' : ''"
                                        />
                                        <p v-if="errors.end_time" class="text-xs text-error ml-1">{{ errors.end_time }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Appointment Type -->
                            <div class="space-y-3">
                                <label class="block text-sm font-bold text-on-surface-variant ml-1">Tipo de Cita</label>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        v-for="opt in typeOptions"
                                        :key="opt.value"
                                        type="button"
                                        @click="form.type = opt.value"
                                        class="px-5 py-2.5 rounded-full font-bold text-sm transition-colors"
                                        :class="form.type === opt.value
                                            ? 'bg-primary text-on-primary shadow-sm ring-2 ring-primary'
                                            : 'bg-surface-container-high text-on-surface-variant hover:bg-primary-container/40'"
                                    >{{ opt.label }}</button>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-on-surface-variant ml-1">Notas de Preparación</label>
                                <textarea
                                    v-model="form.notes"
                                    class="w-full p-4 bg-surface-container-low border-none rounded-2xl focus:ring-2 focus:ring-primary font-medium text-on-surface resize-none placeholder:text-outline"
                                    placeholder="Indicar si el paciente requiere ayuno o traer analíticas previas..."
                                    rows="4"
                                ></textarea>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="px-8 py-6 bg-surface-container/30 shrink-0 flex flex-col md:flex-row gap-4 items-center justify-end">
                            <button
                                type="button"
                                @click="$emit('close')"
                                class="w-full md:w-auto px-8 py-4 text-on-surface-variant font-bold rounded-2xl hover:bg-surface-container-high transition-all"
                            >Cancelar</button>
                            <button
                                type="submit"
                                :disabled="processing"
                                class="w-full md:w-auto px-10 py-4 bg-primary text-on-primary font-bold rounded-2xl shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed disabled:scale-100"
                            >
                                <span
                                    class="material-symbols-outlined"
                                    style="font-variation-settings: 'FILL' 1;"
                                >{{ processing ? 'hourglass_empty' : 'check_circle' }}</span>
                                {{ processing ? 'Guardando...' : 'Confirmar Cita' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    show:     { type: Boolean, default: false },
    patients: { type: Array,   default: () => [] },
});

const emit = defineEmits(['close', 'saved']);

const typeOptions = [
    { value: 'initial',   label: 'Primera Consulta' },
    { value: 'follow_up', label: 'Seguimiento' },
    { value: 'emergency', label: 'Urgencia' },
    { value: 'online',    label: 'En Línea' },
];

const defaultForm = () => ({
    patient_id: '',
    date:       new Date().toISOString().slice(0, 10),
    start_time: '09:00',
    end_time:   '10:00',
    type:       'initial',
    notes:      '',
});

const form       = ref(defaultForm());
const errors     = ref({});
const processing = ref(false);

// Resetear formulario al abrir el modal
watch(() => props.show, (val) => {
    if (val) {
        form.value   = defaultForm();
        errors.value = {};
    }
});

function submit() {
    errors.value = {};

    if (!form.value.patient_id) {
        errors.value.patient_id = 'Selecciona un paciente.';
        return;
    }
    if (!form.value.date) {
        errors.value.date = 'La fecha es obligatoria.';
        return;
    }
    if (!form.value.start_time) {
        errors.value.start_time = 'La hora de inicio es obligatoria.';
        return;
    }
    if (!form.value.end_time) {
        errors.value.end_time = 'La hora de fin es obligatoria.';
        return;
    }
    if (form.value.start_time >= form.value.end_time) {
        errors.value.end_time = 'La hora de fin debe ser mayor al inicio.';
        return;
    }

    processing.value = true;

    router.post(route('citas.store'), {
        patient_id:  form.value.patient_id,
        date:        form.value.date,
        start_time:  form.value.start_time,
        end_time:    form.value.end_time,
        type:        form.value.type,
        notes:       form.value.notes,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            emit('saved');
            emit('close');
        },
        onError: (errs) => {
            errors.value = errs;
        },
        onFinish: () => {
            processing.value = false;
        },
    });
}
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
