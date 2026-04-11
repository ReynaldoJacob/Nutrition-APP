<template>
    <Teleport to="body">
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60">
            <div class="relative bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl flex flex-col overflow-hidden">

                <div class="px-8 py-8 flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-3xl text-amber-700">enhanced_encryption</span>
                    </div>
                    <h2 class="text-2xl font-extrabold text-on-surface mb-2">Actualiza tu Contraseña</h2>
                    <p class="text-on-surface-variant text-sm">Por seguridad, debes cambiar tu contraseña temporal en el primer acceso.</p>
                </div>

                <form class="px-8 pb-8 space-y-6" @submit.prevent="submit">
                    <div class="bg-amber-50/50 border border-amber-200 px-4 py-3 rounded-xl flex gap-3">
                        <span class="material-symbols-outlined text-amber-700 flex-shrink-0 mt-0.5">info</span>
                        <div class="text-sm text-amber-900">
                            <p class="font-bold mb-1">Tu contraseña temporal</p>
                            <p class="font-mono bg-white/50 px-2 py-1 rounded text-xs inline-block">{{ temporaryPassword }}</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-sm font-bold text-on-surface-variant">Contraseña Actual</label>
                        <input
                            v-model="form.current_password"
                            type="password"
                            class="w-full bg-surface-container-high border-none rounded-xl py-3 px-4 text-on-surface placeholder:text-outline/50 focus:ring-2 focus:ring-primary/20 transition-all"
                            placeholder="Tu contraseña temporal"
                        />
                        <p v-if="form.errors.current_password" class="text-xs text-error">{{ form.errors.current_password }}</p>
                    </div>

                    <div class="space-y-3">
                        <label class="text-sm font-bold text-on-surface-variant">Nueva Contraseña</label>
                        <input
                            v-model="form.password"
                            type="password"
                            class="w-full bg-surface-container-high border-none rounded-xl py-3 px-4 text-on-surface placeholder:text-outline/50 focus:ring-2 focus:ring-primary/20 transition-all"
                            placeholder="Crea una contraseña segura"
                        />
                        <p v-if="form.errors.password" class="text-xs text-error">{{ form.errors.password }}</p>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-xs">
                                <span class="material-symbols-outlined text-sm" :class="strength.uppercase ? 'text-primary' : 'text-outline'">
                                    done
                                </span>
                                <span :class="strength.uppercase ? 'text-primary font-bold' : 'text-on-surface-variant'">
                                    Mayúscula y minúscula
                                </span>
                            </div>
                            <div class="flex items-center gap-2 text-xs">
                                <span class="material-symbols-outlined text-sm" :class="strength.number ? 'text-primary' : 'text-outline'">
                                    done
                                </span>
                                <span :class="strength.number ? 'text-primary font-bold' : 'text-on-surface-variant'">
                                    Al menos 1 número
                                </span>
                            </div>
                            <div class="flex items-center gap-2 text-xs">
                                <span class="material-symbols-outlined text-sm" :class="strength.special ? 'text-primary' : 'text-outline'">
                                    done
                                </span>
                                <span :class="strength.special ? 'text-primary font-bold' : 'text-on-surface-variant'">
                                    Símbolo especial (!@#$%)
                                </span>
                            </div>
                            <div class="flex items-center gap-2 text-xs">
                                <span class="material-symbols-outlined text-sm" :class="strength.length ? 'text-primary' : 'text-outline'">
                                    done
                                </span>
                                <span :class="strength.length ? 'text-primary font-bold' : 'text-on-surface-variant'">
                                    Mínimo 8 caracteres
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-sm font-bold text-on-surface-variant">Confirmar Contraseña</label>
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            class="w-full bg-surface-container-high border-none rounded-xl py-3 px-4 text-on-surface placeholder:text-outline/50 focus:ring-2 focus:ring-primary/20 transition-all"
                            placeholder="Repite tu nueva contraseña"
                        />
                        <p v-if="form.errors.password_confirmation" class="text-xs text-error">{{ form.errors.password_confirmation }}</p>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-primary text-on-primary py-3.5 rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:shadow-primary/30 active:scale-95 transition-all flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed"
                        :disabled="form.processing || !isPasswordValid"
                    >
                        <span v-if="form.processing" class="material-symbols-outlined text-lg animate-spin">progress_activity</span>
                        <span v-else>Actualizar Contraseña</span>
                    </button>
                </form>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    temporaryPassword: {
        type: String,
        required: true,
    },
})

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

const strength = computed(() => ({
    uppercase: /[a-z]/.test(form.password) && /[A-Z]/.test(form.password),
    number: /\d/.test(form.password),
    special: /[!@#$%^&*()_+\-=\[\]{};:'",.<>?/\\|`~]/.test(form.password),
    length: form.password.length >= 8,
}))

const isPasswordValid = computed(
    () => strength.value.uppercase &&
           strength.value.number &&
           strength.value.special &&
           strength.value.length &&
           form.password === form.password_confirmation &&
           form.current_password.trim() !== ''
)

function submit() {
    form.post(route('auth.change-password'), {
        preserveScroll: true,
        onSuccess: () => {
            // La redirección será manejada por el servidor
        },
    })
}
</script>
