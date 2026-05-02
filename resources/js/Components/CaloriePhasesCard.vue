<template>
    <div class="bg-surface-container-lowest rounded-3xl p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-6">
            <span class="material-symbols-outlined text-primary text-2xl" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
            <h3 class="font-bold text-on-surface text-lg">Tabulador de Calorías por Fase</h3>
        </div>

        <!-- GET Base Display -->
        <div class="mb-6 p-4 bg-gradient-to-r from-primary/10 to-primary/5 rounded-2xl border border-primary/20">
            <div class="flex items-center justify-between">
                <div>
                    <label class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">GET Base (Gasto Energético Total)</label>
                    <p class="text-[10px] text-on-surface-variant mt-1">No se modifica al borrar. Solo aumenta/disminuye si ingresas valores.</p>
                </div>
                <div class="text-right">
                    <span class="text-3xl font-extrabold text-primary block">{{ Math.round(baseGet) }}</span>
                    <span class="text-xs text-on-surface-variant font-semibold">kcal/día</span>
                </div>
            </div>
        </div>

        <!-- Grid de fases con inputs -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- CUT -->
            <div class="relative p-5 rounded-2xl bg-error-container/20 border-2 border-error/50 hover:border-error transition-colors">
                <div class="space-y-3">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-error text-lg">trending_down</span>
                        <h4 class="font-bold text-on-surface">Cut -</h4>
                        <span class="text-[9px] font-bold bg-error/20 text-error px-2 py-0.5 rounded-full">-15%</span>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-on-surface-variant">Ingresa kcal</label>
                        <div class="relative">
                            <input
                                v-model="cutInput"
                                @blur="applyCutKcal"
                                @keyup.enter="applyCutKcal"
                                type="number"
                                placeholder="ej: 2100"
                                class="w-full bg-white border-2 border-error/30 rounded-xl p-3 font-bold text-lg focus:ring-2 focus:ring-error/20 focus:border-error transition-all outline-none"
                            />
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant font-medium text-xs">kcal</span>
                        </div>
                        <div class="text-[10px] text-on-surface-variant">
                            <p><strong>Fórmula:</strong> GET ÷ 0.85</p>
                            <p><strong>Pérdida:</strong> 0.5-1kg/sem</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MANTENIMIENTO -->
            <div class="relative p-5 rounded-2xl bg-tertiary-container/20 border-2 border-tertiary/50 hover:border-tertiary transition-colors">
                <div class="space-y-3">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-tertiary text-lg">balance</span>
                        <h4 class="font-bold text-on-surface">Mantenimiento</h4>
                        <span class="text-[9px] font-bold bg-tertiary/20 text-tertiary px-2 py-0.5 rounded-full">0%</span>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-on-surface-variant">Ingresa kcal</label>
                        <div class="relative">
                            <input
                                v-model="maintenanceInput"
                                @blur="applyMaintenanceKcal"
                                @keyup.enter="applyMaintenanceKcal"
                                type="number"
                                placeholder="ej: 2470"
                                class="w-full bg-white border-2 border-tertiary/30 rounded-xl p-3 font-bold text-lg focus:ring-2 focus:ring-tertiary/20 focus:border-tertiary transition-all outline-none"
                            />
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant font-medium text-xs">kcal</span>
                        </div>
                        <div class="text-[10px] text-on-surface-variant">
                            <p><strong>Fórmula:</strong> GET = igual</p>
                            <p><strong>Peso:</strong> Estable</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BULK -->
            <div class="relative p-5 rounded-2xl bg-secondary-container/20 border-2 border-secondary/50 hover:border-secondary transition-colors">
                <div class="space-y-3">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-secondary text-lg">trending_up</span>
                        <h4 class="font-bold text-on-surface">Bulk +</h4>
                        <span class="text-[9px] font-bold bg-secondary/20 text-secondary px-2 py-0.5 rounded-full">+9%</span>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-on-surface-variant">Ingresa kcal</label>
                        <div class="relative">
                            <input
                                v-model="bulkInput"
                                @blur="applyBulkKcal"
                                @keyup.enter="applyBulkKcal"
                                type="number"
                                placeholder="ej: 2690"
                                class="w-full bg-white border-2 border-secondary/30 rounded-xl p-3 font-bold text-lg focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all outline-none"
                            />
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant font-medium text-xs">kcal</span>
                        </div>
                        <div class="text-[10px] text-on-surface-variant">
                            <p><strong>Fórmula:</strong> GET ÷ 1.09</p>
                            <p><strong>Ganancia:</strong> 0.25-0.5kg/sem</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nota informativa -->
        <div class="bg-primary-container/20 border border-primary/30 rounded-2xl p-4 flex gap-3">
            <span class="material-symbols-outlined text-primary text-lg flex-shrink-0 mt-0.5">info</span>
            <div>
                <p class="text-xs font-semibold text-on-surface mb-1">ℹ️ Cómo funciona:</p>
                <p class="text-xs text-on-surface-variant leading-relaxed">
                    Ingresa calorías en cualquier fase y presiona Enter o haz clic afuera. El GET se ajustará inversamente. Puedes dejar vacío sin afectar el GET base.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: Number,
        default: 2500  // GET base
    }
});

const emit = defineEmits(['update:modelValue']);

// Inputs locales para cada fase
const cutInput = ref('');
const maintenanceInput = ref('');
const bulkInput = ref('');

const baseGet = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

// Solo aplica cuando el usuario confirma (Enter o blur)
const applyCutKcal = () => {
    const cutKcal = parseFloat(cutInput.value);
    // Solo actualiza si es un número válido y > 0
    if (!isNaN(cutKcal) && cutKcal > 0) {
        baseGet.value = Math.round(cutKcal / 0.85);
        cutInput.value = ''; // Limpia el input después de aplicar
    }
    // Si está vacío, no hace nada (no borra el GET)
};

const applyMaintenanceKcal = () => {
    const maintKcal = parseFloat(maintenanceInput.value);
    if (!isNaN(maintKcal) && maintKcal > 0) {
        baseGet.value = maintKcal;
        maintenanceInput.value = '';
    }
};

const applyBulkKcal = () => {
    const bulkKcal = parseFloat(bulkInput.value);
    if (!isNaN(bulkKcal) && bulkKcal > 0) {
        baseGet.value = Math.round(bulkKcal / 1.09);
        bulkInput.value = '';
    }
};
</script>
