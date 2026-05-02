<template>
    <AppLayout>
        <main class="p-8 min-h-screen">
            <!-- Header -->
            <div class="mb-8">
                <nav class="flex text-xs text-on-surface-variant mb-2 space-x-2 items-center">
                    <Link href="/biblioteca/recipes" class="hover:text-primary">Recetario</Link>
                    <span class="material-symbols-outlined" style="font-size:14px">chevron_right</span>
                    <span class="text-primary font-bold">{{ recipe ? 'Editar' : 'Nueva' }} Receta</span>
                </nav>
                <h1 class="text-4xl font-extrabold text-on-surface tracking-tight">
                    {{ recipe ? 'Editar Receta' : 'Nueva Receta' }}
                </h1>
            </div>

            <!-- Form -->
            <form @submit.prevent="handleSubmit" class="space-y-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content (2 cols) -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Información Básica -->
                        <div class="bg-surface-container-lowest rounded-xl p-8 shadow-sm">
                            <h2 class="text-xl font-bold text-on-surface mb-6">Información Básica</h2>

                            <div class="mb-6">
                                <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">
                                    Nombre de Receta *
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Ej. Pollo a la Parrilla con Verduras"
                                    class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all"
                                    :class="{ 'ring-2 ring-error': formErrors.name }"
                                    required
                                />
                                <p v-if="formErrors.name" class="mt-1 text-xs text-error">{{ formErrors.name[0] }}</p>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">
                                    Descripción
                                </label>
                                <textarea
                                    v-model="form.description"
                                    rows="4"
                                    placeholder="Describe brevemente los beneficios y características de esta receta..."
                                    class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all resize-none"
                                ></textarea>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">
                                    URL de Imagen
                                </label>
                                <input
                                    v-model="form.image_url"
                                    type="url"
                                    placeholder="https://..."
                                    class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all"
                                />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">
                                        Tipo de Comida
                                    </label>
                                    <select v-model="form.meal_type" class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all">
                                        <option value="">Seleccionar</option>
                                        <option value="breakfast">Desayuno</option>
                                        <option value="lunch">Almuerzo</option>
                                        <option value="dinner">Cena</option>
                                        <option value="snack">Refrigerio</option>
                                        <option value="dessert">Postre</option>
                                        <option value="beverage">Bebida</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">
                                        Tipo de Dieta
                                    </label>
                                    <input
                                        v-model="form.diet_type"
                                        type="text"
                                        placeholder="Ej. keto, vegano"
                                        class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Preparación -->
                        <div class="bg-surface-container-lowest rounded-xl p-8 shadow-sm">
                            <h2 class="text-xl font-bold text-on-surface mb-6">Preparación</h2>

                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">
                                        Tiempo de Preparación (min)
                                    </label>
                                    <input
                                        v-model="form.preparation_time"
                                        type="number"
                                        placeholder="30"
                                        class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">
                                        Porciones *
                                    </label>
                                    <input
                                        v-model="form.servings"
                                        type="number"
                                        placeholder="1"
                                        class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all"
                                        :class="{ 'ring-2 ring-error': formErrors.servings }"
                                        required
                                    />
                                    <p v-if="formErrors.servings" class="mt-1 text-xs text-error">{{ formErrors.servings[0] }}</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">
                                    Instrucciones
                                </label>
                                <textarea
                                    v-model="form.instructions"
                                    rows="6"
                                    placeholder="Paso a paso para preparar..."
                                    class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all resize-none"
                                ></textarea>
                            </div>
                        </div>

                        <!-- Ingredientes -->
                        <div class="bg-surface-container-lowest rounded-xl p-8 shadow-sm">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-bold text-on-surface">Ingredientes</h2>
                                <button
                                    type="button"
                                    @click="showIngredientModal = true"
                                    class="px-4 py-2 bg-primary text-on-primary rounded-full font-bold text-sm flex items-center gap-2 hover:opacity-90"
                                >
                                    <span class="material-symbols-outlined text-sm">add</span>
                                    Agregar
                                </button>
                            </div>

                            <div v-if="form.ingredients.length === 0" class="text-center py-8 text-on-surface-variant">
                                <span class="material-symbols-outlined text-4xl block mb-2 opacity-30">grocery</span>
                                <p>No hay ingredientes agregados</p>
                            </div>

                            <div v-else class="space-y-3">
                                <div
                                    v-for="(ing, idx) in form.ingredients"
                                    :key="idx"
                                    class="flex items-center justify-between bg-surface-container-high rounded-xl p-4"
                                >
                                    <div class="flex-1">
                                        <p class="font-bold text-on-surface">{{ getIngredientName(ing.id) }}</p>
                                        <p class="text-sm text-on-surface-variant">{{ parseFloat(ing.quantity).toFixed(2) }} {{ ing.unit }}</p>
                                    </div>
                                    <button
                                        type="button"
                                        @click="removeIngredient(idx)"
                                        class="p-2 text-error hover:bg-error/10 rounded-lg transition-colors"
                                    >
                                        <span class="material-symbols-outlined">close</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar (1 col) -->
                    <div class="space-y-6">
                        <!-- Macronutrientes -->
                        <div class="bg-surface-container-lowest rounded-xl p-8 shadow-sm sticky top-8">
                            <h2 class="text-xl font-bold text-on-surface mb-6">Macronutrientes por Porción</h2>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-on-surface-variant uppercase mb-2">Calorías</label>
                                    <input
                                        v-model="form.calories"
                                        type="number"
                                        step="0.01"
                                        placeholder="0"
                                        class="w-full bg-surface-container-high border-none rounded-lg px-3 py-2 text-on-surface focus:ring-2 focus:ring-primary/20"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-on-surface-variant uppercase mb-2">Proteína (g)</label>
                                    <input
                                        v-model="form.protein"
                                        type="number"
                                        step="0.01"
                                        placeholder="0"
                                        class="w-full bg-surface-container-high border-none rounded-lg px-3 py-2 text-on-surface focus:ring-2 focus:ring-primary/20"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-on-surface-variant uppercase mb-2">Grasas (g)</label>
                                    <input
                                        v-model="form.fats"
                                        type="number"
                                        step="0.01"
                                        placeholder="0"
                                        class="w-full bg-surface-container-high border-none rounded-lg px-3 py-2 text-on-surface focus:ring-2 focus:ring-primary/20"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-on-surface-variant uppercase mb-2">Carbs (g)</label>
                                    <input
                                        v-model="form.carbs"
                                        type="number"
                                        step="0.01"
                                        placeholder="0"
                                        class="w-full bg-surface-container-high border-none rounded-lg px-3 py-2 text-on-surface focus:ring-2 focus:ring-primary/20"
                                    />
                                </div>
                            </div>

                            <div class="mt-6 pt-6 border-t border-surface-container-high">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input v-model="form.is_featured" type="checkbox" class="w-5 h-5 rounded accent-primary" />
                                    <span class="text-sm font-bold text-on-surface">Destacar en Inicio</span>
                                </label>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="flex flex-col gap-3">
                            <Link
                                href="/biblioteca/recipes"
                                class="px-6 py-3 text-primary font-bold hover:bg-primary/10 rounded-full transition-colors text-center"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                :disabled="submitting"
                                class="px-6 py-3 bg-primary text-on-primary font-bold rounded-full hover:opacity-90 disabled:opacity-60 transition-all flex items-center justify-center gap-2"
                            >
                                <span v-if="submitting" class="material-symbols-outlined animate-spin">progress_activity</span>
                                <span v-else class="material-symbols-outlined">save</span>
                                {{ submitting ? 'Guardando...' : 'Guardar Receta' }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Add Ingredient Modal -->
            <Teleport to="body">
                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-if="showIngredientModal" class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6">
                        <div class="fixed inset-0 bg-inverse-surface/60 backdrop-blur-sm" @click="showIngredientModal = false" />
                        <div class="relative bg-surface-container-lowest w-full max-w-md rounded-2xl shadow-2xl overflow-hidden z-10 p-8">
                            <h3 class="text-2xl font-bold text-on-surface mb-6">Agregar Ingrediente</h3>

                            <div class="space-y-4">
                                <!-- Seleccionar Ingrediente -->
                                <div>
                                    <label class="block text-sm font-bold text-on-surface-variant uppercase mb-2">Ingrediente *</label>
                                    <select
                                        v-model="selectedIngredient"
                                        class="w-full bg-surface-container-high border-none rounded-lg px-4 py-2 text-on-surface focus:ring-2 focus:ring-primary/20"
                                    >
                                        <option value="">Seleccionar...</option>
                                        <option v-for="ing in availableIngredients" :key="ing.id" :value="ing.id">
                                            {{ ing.name }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Cantidad -->
                                <div>
                                    <label class="block text-sm font-bold text-on-surface-variant uppercase mb-2">Cantidad *</label>
                                    <input
                                        v-model="ingredientQuantity"
                                        type="number"
                                        step="0.01"
                                        placeholder="0"
                                        class="w-full bg-surface-container-high border-none rounded-lg px-4 py-2 text-on-surface focus:ring-2 focus:ring-primary/20"
                                        required
                                    />
                                </div>

                                <!-- Unidad -->
                                <div>
                                    <label class="block text-sm font-bold text-on-surface-variant uppercase mb-2">Unidad *</label>
                                    <input
                                        v-model="ingredientUnit"
                                        type="text"
                                        placeholder="g, ml, unit, cup..."
                                        class="w-full bg-surface-container-high border-none rounded-lg px-4 py-2 text-on-surface focus:ring-2 focus:ring-primary/20"
                                        required
                                    />
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="flex gap-3 justify-end mt-8">
                                <button
                                    type="button"
                                    @click="showIngredientModal = false"
                                    class="px-6 py-2 text-primary font-bold hover:bg-primary/10 rounded-full"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="button"
                                    @click="addIngredientToForm"
                                    class="px-6 py-2 bg-primary text-on-primary font-bold rounded-full hover:opacity-90"
                                >
                                    Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>
            </Teleport>
        </main>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    recipe: Object,
    ingredients: Object,
});

const submitting = ref(false);
const formErrors = ref({});
const showIngredientModal = ref(false);
const selectedIngredient = ref('');
const ingredientQuantity = ref('');
const ingredientUnit = ref('g');

// Flatten ingredients from grouped structure
const allIngredients = computed(() => {
    let result = [];
    Object.values(props.ingredients || {}).forEach(category => {
        if (Array.isArray(category)) {
            result.push(...category);
        }
    });
    return result;
});

const availableIngredients = computed(() => {
    return allIngredients.value.filter(
        ing => !form.value.ingredients.some(fi => fi.id === ing.id)
    );
});

const form = ref({
    name: props.recipe?.name || '',
    description: props.recipe?.description || '',
    image_url: props.recipe?.image_url || '',
    meal_type: props.recipe?.meal_type || '',
    diet_type: props.recipe?.diet_type || '',
    preparation_time: props.recipe?.preparation_time || '',
    servings: props.recipe?.servings || '1',
    instructions: props.recipe?.instructions || '',
    calories: props.recipe?.calories || '',
    protein: props.recipe?.protein || '',
    fats: props.recipe?.fats || '',
    carbs: props.recipe?.carbs || '',
    is_featured: props.recipe?.is_featured || false,
    ingredients: props.recipe?.ingredients?.map(i => ({ id: i.id, quantity: i.quantity, unit: i.unit })) || [],
});

function getIngredientName(id) {
    return allIngredients.value.find(i => i.id === id)?.name || 'Ingrediente desconocido';
}

function addIngredientToForm() {
    if (!selectedIngredient.value || !ingredientQuantity.value) return;

    const newIngredient = {
        id: parseInt(selectedIngredient.value),
        quantity: parseFloat(ingredientQuantity.value),
        unit: ingredientUnit.value || 'g',
    };

    form.value.ingredients.push(newIngredient);

    selectedIngredient.value = '';
    ingredientQuantity.value = '';
    ingredientUnit.value = 'g';
    showIngredientModal.value = false;
}

function removeIngredient(idx) {
    form.value.ingredients.splice(idx, 1);
}

function handleSubmit() {
    submitting.value = true;
    formErrors.value = {};

    if (props.recipe) {
        router.patch(
            route('recipes.update', props.recipe.id),
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
            route('recipes.store'),
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
