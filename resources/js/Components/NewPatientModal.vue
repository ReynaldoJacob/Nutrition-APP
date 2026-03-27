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
            <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 overflow-y-auto">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-inverse-surface/60 backdrop-blur-sm" @click="$emit('close')" />

                <!-- Modal Content -->
                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div v-if="show" class="relative bg-surface-container-lowest w-full max-w-2xl rounded-[2rem] shadow-2xl overflow-hidden z-10">

                        <!-- Modal Header -->
                        <div class="px-8 pt-8 pb-4 flex justify-between items-start">
                            <div>
                                <div class="inline-flex items-center gap-2 px-3 py-1 bg-primary-container rounded-full text-primary text-[10px] font-bold uppercase tracking-widest mb-3">
                                    Nuevo Perfil
                                </div>
                                <h2 class="text-3xl font-extrabold text-on-surface tracking-tight font-headline">Añadir Nuevo Paciente</h2>
                                <p class="text-on-surface-variant mt-1 text-sm font-medium">Ingresa los datos clínicos para iniciar el seguimiento.</p>
                            </div>
                            <button
                                type="button"
                                class="w-10 h-10 rounded-full flex items-center justify-center bg-surface-container-high hover:bg-surface-container-highest transition-colors text-on-surface-variant"
                                @click="$emit('close')"
                            >
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>

                        <!-- Form Body -->
                        <form class="px-8 py-6" @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <!-- Nombre Completo -->
                                <div class="col-span-2">
                                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2 ml-1">
                                        Nombre Completo
                                    </label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        placeholder="Ej. Javier Pérez"
                                        class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3.5 text-on-surface placeholder:text-outline/50 focus:ring-2 focus:ring-primary/20 transition-all font-medium"
                                        :class="{ 'ring-2 ring-error': errors.name }"
                                        required
                                    />
                                    <p v-if="errors.name" class="mt-1 text-xs text-error ml-1">{{ errors.name }}</p>
                                </div>

                                <!-- Correo -->
                                <div>
                                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2 ml-1">
                                        Correo Electrónico
                                    </label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        placeholder="javier@ejemplo.com"
                                        class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3.5 text-on-surface placeholder:text-outline/50 focus:ring-2 focus:ring-primary/20 transition-all font-medium"
                                        :class="{ 'ring-2 ring-error': errors.email }"
                                        required
                                    />
                                    <p v-if="errors.email" class="mt-1 text-xs text-error ml-1">{{ errors.email }}</p>
                                </div>

                                <!-- Teléfono -->
                                <div>
                                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2 ml-1">
                                        Teléfono
                                    </label>
                                    <input
                                        v-model="form.phone"
                                        type="tel"
                                        placeholder="+52 000 000 0000"
                                        class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3.5 text-on-surface placeholder:text-outline/50 focus:ring-2 focus:ring-primary/20 transition-all font-medium"
                                        :class="{ 'ring-2 ring-error': errors.phone }"
                                    />
                                    <p v-if="errors.phone" class="mt-1 text-xs text-error ml-1">{{ errors.phone }}</p>
                                </div>

                                <!-- Fecha Nacimiento -->
                                <div>
                                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2 ml-1">
                                        Fecha de Nacimiento
                                    </label>
                                    <div class="relative">
                                        <input
                                            v-model="form.birth_date"
                                            type="date"
                                            class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3.5 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all font-medium"
                                            :class="{ 'ring-2 ring-error': errors.birth_date }"
                                        />
                                        <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">calendar_today</span>
                                    </div>
                                    <p v-if="errors.birth_date" class="mt-1 text-xs text-error ml-1">{{ errors.birth_date }}</p>
                                </div>

                                <!-- Género -->
                                <div>
                                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2 ml-1">
                                        Género
                                    </label>
                                    <div class="relative">
                                        <select
                                            v-model="form.gender"
                                            class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3.5 text-on-surface appearance-none focus:ring-2 focus:ring-primary/20 transition-all font-medium"
                                            :class="{ 'ring-2 ring-error': errors.gender }"
                                        >
                                            <option value="" disabled>Seleccionar...</option>
                                            <option value="male">Masculino</option>
                                            <option value="female">Femenino</option>
                                            <option value="other">Otro</option>
                                        </select>
                                        <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                                    </div>
                                    <p v-if="errors.gender" class="mt-1 text-xs text-error ml-1">{{ errors.gender }}</p>
                                </div>

                                <!-- Motivo Consulta -->
                                <div class="col-span-2">
                                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2 ml-1">
                                        Motivo de Consulta / Objetivo
                                    </label>
                                    <textarea
                                        v-model="form.notes"
                                        rows="3"
                                        placeholder="Describe brevemente el objetivo del paciente..."
                                        class="w-full bg-surface-container-high border-none rounded-2xl px-4 py-3.5 text-on-surface placeholder:text-outline/50 focus:ring-2 focus:ring-primary/20 transition-all font-medium resize-none"
                                        :class="{ 'ring-2 ring-error': errors.notes }"
                                    />
                                    <p v-if="errors.notes" class="mt-1 text-xs text-error ml-1">{{ errors.notes }}</p>
                                </div>

                            </div>
                        </form>

                        <!-- Modal Footer -->
                        <div class="px-8 py-8 bg-surface-container-low/50 flex flex-col-reverse md:flex-row justify-end gap-4">
                            <button
                                type="button"
                                class="px-8 py-3.5 text-primary font-bold hover:bg-primary-container/30 rounded-full transition-all text-sm"
                                @click="$emit('close')"
                            >
                                Cancelar
                            </button>
                            <button
                                type="button"
                                class="px-10 py-3.5 bg-gradient-to-r from-primary to-primary-dim text-on-primary font-bold rounded-full shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all text-sm flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed disabled:scale-100"
                                :disabled="loading"
                                @click="submit"
                            >
                                <span v-if="loading" class="material-symbols-outlined text-[20px] animate-spin">progress_activity</span>
                                <span v-else class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">save</span>
                                {{ loading ? 'Guardando...' : 'Guardar Paciente' }}
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
    show: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close', 'saved']);

const loading = ref(false);
const errors  = ref({});

const initialForm = () => ({
    name:       '',
    email:      '',
    phone:      '',
    birth_date: '',
    gender:     '',
    notes:      '',
});

const form = ref(initialForm());

// Reset form when modal opens
watch(() => props.show, (val) => {
    if (val) {
        form.value = initialForm();
        errors.value = {};
    }
});

function validate() {
    const e = {};
    if (!form.value.name.trim())  e.name  = 'El nombre es requerido.';
    if (!form.value.email.trim()) e.email = 'El correo es requerido.';
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) e.email = 'Ingresa un correo válido.';
    errors.value = e;
    return Object.keys(e).length === 0;
}

function submit() {
    if (!validate()) return;

    loading.value = true;
    errors.value  = {};

    router.post(route('pacientes.store'), form.value, {
        onSuccess: () => {
            loading.value = false;
            emit('saved');
            emit('close');
        },
        onError: (errs) => {
            loading.value = false;
            errors.value  = errs;
        },
        onFinish: () => {
            loading.value = false;
        },
    });
}
</script>
