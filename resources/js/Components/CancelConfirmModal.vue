<template>
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
                v-if="show"
                class="fixed inset-0 z-[70] flex items-center justify-center bg-on-surface/30 backdrop-blur-sm p-4"
                @click.self="$emit('close')"
            >
                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0 scale-90 translate-y-4"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="opacity-100 scale-100 translate-y-0"
                    leave-to-class="opacity-0 scale-90 translate-y-4"
                    appear
                >
                    <div v-if="show" class="w-full max-w-md bg-surface-container-lowest rounded-[2rem] shadow-2xl overflow-hidden">

                        <!-- Franja de alerta superior -->
                        <div class="bg-error-container/40 px-8 pt-8 pb-6 border-b border-error-container/60">
                            <div class="flex items-start gap-4">
                                <div class="shrink-0 w-12 h-12 rounded-2xl bg-error/10 border border-error/20 flex items-center justify-center">
                                    <span
                                        class="material-symbols-outlined text-error text-2xl"
                                        style="font-variation-settings: 'FILL' 1;"
                                    >event_busy</span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-headline text-xl font-extrabold text-on-surface">¿Cancelar esta cita?</h3>
                                    <p class="text-sm text-on-surface-variant mt-1">Esta acción no se puede deshacer fácilmente.</p>
                                </div>
                                <button
                                    type="button"
                                    @click="$emit('close')"
                                    class="shrink-0 p-1.5 hover:bg-error-container/60 rounded-full transition-colors text-on-surface-variant"
                                >
                                    <span class="material-symbols-outlined text-lg">close</span>
                                </button>
                            </div>
                        </div>

                        <!-- Detalle de la cita -->
                        <div class="px-8 pt-6 pb-4 space-y-4">
                            <div v-if="appointment" class="bg-surface-container-low rounded-2xl p-4 flex items-center gap-4">
                                <img
                                    :alt="appointment.patientName"
                                    :src="appointment.patientAvatar ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(appointment.patientName)}&background=7af9c7&color=004933`"
                                    class="w-12 h-12 rounded-xl object-cover ring-2 ring-white shrink-0"
                                />
                                <div class="min-w-0">
                                    <p class="font-bold text-on-surface truncate">{{ appointment.patientName }}</p>
                                    <p class="text-xs text-on-surface-variant mt-0.5 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">schedule</span>
                                        {{ appointment.timeRange }}
                                        &nbsp;·&nbsp;
                                        <span class="material-symbols-outlined text-sm">event</span>
                                        {{ formatDate(appointment.scheduledAt) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Motivo (opcional) -->
                            <div class="space-y-1.5">
                                <div class="flex justify-between items-center ml-1">
                                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-widest">
                                        Motivo de cancelación
                                        <span class="normal-case font-normal text-outline ml-1">(opcional)</span>
                                    </label>
                                    <span
                                        class="text-xs font-medium"
                                        :class="reason.length > 230 ? 'text-error' : 'text-outline'"
                                    >{{ reason.length }}/255</span>
                                </div>
                                <textarea
                                    v-model="reason"
                                    rows="3"
                                    maxlength="255"
                                    class="w-full p-4 bg-surface-container-low border-none rounded-2xl focus:ring-2 focus:ring-error text-sm font-medium text-on-surface resize-none placeholder:text-outline"
                                    placeholder="Ej: El paciente solicitó cancelar por motivos personales..."
                                ></textarea>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="px-8 py-6 bg-surface-container/20 flex flex-col-reverse sm:flex-row gap-3 justify-end">
                            <button
                                type="button"
                                @click="$emit('close')"
                                :disabled="processing"
                                class="w-full sm:w-auto px-6 py-3.5 text-on-surface-variant font-bold rounded-2xl hover:bg-surface-container-high transition-all disabled:opacity-50"
                            >Volver</button>
                            <button
                                type="button"
                                @click="confirm"
                                :disabled="processing"
                                class="w-full sm:w-auto px-8 py-3.5 bg-error text-on-error font-bold rounded-2xl shadow-lg shadow-error/20 hover:opacity-90 hover:scale-[1.02] active:scale-95 transition-all disabled:opacity-60 disabled:cursor-not-allowed disabled:scale-100 flex items-center justify-center gap-2"
                            >
                                <span
                                    class="material-symbols-outlined text-sm"
                                    style="font-variation-settings: 'FILL' 1;"
                                >{{ processing ? 'hourglass_empty' : 'cancel' }}</span>
                                {{ processing ? 'Cancelando...' : 'Sí, cancelar cita' }}
                            </button>
                        </div>

                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    show:        { type: Boolean, default: false },
    appointment: { type: Object,  default: null },
});

const emit = defineEmits(['close', 'cancelled']);

const reason     = ref('');
const processing = ref(false);

// Limpiar motivo al abrir
watch(() => props.show, (val) => {
    if (val) reason.value = '';
});

function formatDate(iso) {
    if (!iso) return '';
    return new Date(iso).toLocaleDateString('es-MX', {
        weekday: 'short', day: 'numeric', month: 'short',
    }).replace(/^\w/, c => c.toUpperCase());
}

function confirm() {
    if (!props.appointment) return;
    processing.value = true;

    router.patch(route('citas.cancel', props.appointment.id), {
        cancelled_reason: reason.value || null,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            emit('cancelled');
            emit('close');
        },
        onFinish: () => {
            processing.value = false;
        },
    });
}
</script>
