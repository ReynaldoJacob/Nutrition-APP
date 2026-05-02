<template>
    <AppLayout>
        <main class="p-8 min-h-screen">
            <!-- Header -->
            <div class="mb-8">
                <nav class="flex text-xs text-on-surface-variant mb-2 space-x-2 items-center">
                    <Link href="/biblioteca/ingredients" class="hover:text-primary">Mis Ingredientes</Link>
                    <span class="material-symbols-outlined" style="font-size:14px">chevron_right</span>
                    <span class="text-primary font-bold">{{ ingredient ? 'Editar' : 'Nuevo' }} Ingrediente</span>
                </nav>
                <h1 class="text-4xl font-extrabold text-on-surface tracking-tight">
                    {{ ingredient ? 'Editar Ingrediente' : 'Nuevo Ingrediente' }}
                </h1>
            </div>

            <!-- Form Card -->
            <div class="bg-surface-container-lowest rounded-xl p-8 shadow-sm max-w-2xl">
                <form @submit.prevent="handleSubmit">
                    <!-- Nombre -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">
                            Nombre *
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Ej. Pechuga de Pollo"
                            class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all"
                            :class="{ 'ring-2 ring-error': formErrors.name }"
                            required
                        />
                        <p v-if="formErrors.name" class="mt-1 text-xs text-error">{{ formErrors.name[0] }}</p>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">
                            Descripción
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="3"
                            placeholder="Detalles adicionales..."
                            class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all resize-none"
                            :class="{ 'ring-2 ring-error': formErrors.description }"
                        ></textarea>
                        <p v-if="formErrors.description" class="mt-1 text-xs text-error">{{ formErrors.description[0] }}</p>
                    </div>

                    <!-- Categoría -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">
                            Categoría
                        </label>
                        <select
                            v-model="form.category"
                            class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all"
                        >
                            <option value="">Seleccionar categoría</option>
                            <option value="protein">Proteína</option>
                            <option value="carbs">Carbohidratos</option>
                            <option value="fats">Grasas</option>
                            <option value="vegetables">Verduras</option>
                            <option value="fruits">Frutas</option>
                            <option value="diary">Lácteos</option>
                            <option value="other">Otros</option>
                        </select>
                    </div>

                    <!-- Unidad de Medida -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">
                            Unidad de Medida *
                        </label>
                        <select
                            v-model="form.unit_of_measure"
                            class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all"
                            :class="{ 'ring-2 ring-error': formErrors.unit_of_measure }"
                            required
                        >
                            <option value="">Seleccionar unidad</option>
                            <option value="g">Gramos (g)</option>
                            <option value="ml">Mililitros (ml)</option>
                            <option value="unit">Unidad</option>
                            <option value="cup">Taza</option>
                            <option value="tbsp">Cucharada (Tbsp)</option>
                            <option value="tsp">Cucharadita (Tsp)</option>
                        </select>
                        <p v-if="formErrors.unit_of_measure" class="mt-1 text-xs text-error">{{ formErrors.unit_of_measure[0] }}</p>
                    </div>

                    <!-- Macronutrientes Grid -->
                    <div class="mb-8 grid grid-cols-2 gap-4">
                        <!-- Calorías -->
                        <div>
                            <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">
                                Calorías por {{  form.unit_of_measure || 'unidad' }}
                            </label>
                            <input
                                v-model="form.calories_per_unit"
                                type="number"
                                step="0.0001"
                                placeholder="0"
                                class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all"
                            />
                        </div>

                        <!-- Proteína -->
                        <div>
                            <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">
                                Proteína (g) por {{ form.unit_of_measure || 'unidad' }}
                            </label>
                            <input
                                v-model="form.protein_per_unit"
                                type="number"
                                step="0.0001"
                                placeholder="0"
                                class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all"
                            />
                        </div>

                        <!-- Grasas -->
                        <div>
                            <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">
                                Grasas (g) por {{ form.unit_of_measure || 'unidad' }}
                            </label>
                            <input
                                v-model="form.fats_per_unit"
                                type="number"
                                step="0.0001"
                                placeholder="0"
                                class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all"
                            />
                        </div>

                        <!-- Carbs -->
                        <div>
                            <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">
                                Carbohidratos (g) por {{ form.unit_of_measure || 'unidad' }}
                            </label>
                            <input
                                v-model="form.carbs_per_unit"
                                type="number"
                                step="0.0001"
                                placeholder="0"
                                class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all"
                            />
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3 justify-end">
                        <Link
                            href="/biblioteca/ingredients"
                            class="px-6 py-3 text-primary font-bold hover:bg-primary/10 rounded-full transition-colors"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="px-8 py-3 bg-primary text-on-primary font-bold rounded-full hover:opacity-90 disabled:opacity-60 transition-all flex items-center gap-2"
                        >
                            <span v-if="submitting" class="material-symbols-outlined animate-spin">progress_activity</span>
                            <span v-else class="material-symbols-outlined">save</span>
                            {{ submitting ? 'Guardando...' : 'Guardar Ingrediente' }}
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    ingredient: Object,
});

const submitting = ref(false);
const formErrors = ref({});

const form = ref({
    name: props.ingredient?.name || '',
    description: props.ingredient?.description || '',
    category: props.ingredient?.category || '',
    unit_of_measure: props.ingredient?.unit_of_measure || 'g',
    calories_per_unit: props.ingredient?.calories_per_unit || '',
    protein_per_unit: props.ingredient?.protein_per_unit || '',
    fats_per_unit: props.ingredient?.fats_per_unit || '',
    carbs_per_unit: props.ingredient?.carbs_per_unit || '',
});

function handleSubmit() {
    submitting.value = true;
    formErrors.value = {};

    if (props.ingredient) {
        router.patch(
            route('ingredients.update', props.ingredient.id),
            form.value,
            {
                onError: (errors) => {
                    formErrors.value = errors;
                    submitting.value = false;
                },
                onSuccess: () => {
                    submitting.value = false;
                },
            }
        );
    } else {
        router.post(
            route('ingredients.store'),
            form.value,
            {
                onError: (errors) => {
                    formErrors.value = errors;
                    submitting.value = false;
                },
                onSuccess: () => {
                    submitting.value = false;
                },
            }
        );
    }
}
</script>
