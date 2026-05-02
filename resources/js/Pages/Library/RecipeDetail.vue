<template>
    <AppLayout>
        <main class="p-8 min-h-screen">
            <!-- Header -->
            <div class="mb-8 flex gap-4 justify-between items-start">
                <div>
                    <nav class="flex text-xs text-on-surface-variant mb-2 space-x-2 items-center">
                        <Link href="/biblioteca/recipes" class="hover:text-primary">Recetario</Link>
                        <span class="material-symbols-outlined" style="font-size:14px">chevron_right</span>
                        <span class="text-primary font-bold">{{ recipe.name }}</span>
                    </nav>
                    <h1 class="text-4xl font-extrabold text-on-surface tracking-tight">{{ recipe.name }}</h1>
                </div>
                <Link
                    :href="route('recipes.edit', recipe.id)"
                    class="px-6 py-3 bg-primary text-on-primary font-bold rounded-full flex items-center gap-2 hover:opacity-90"
                >
                    <span class="material-symbols-outlined">edit</span>
                    Editar
                </Link>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content (2 cols) -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Image -->
                    <div v-if="recipe.image_url" class="rounded-xl overflow-hidden h-96 bg-surface-container-high">
                        <img :src="recipe.image_url" :alt="recipe.name" class="w-full h-full object-cover" />
                    </div>

                    <!-- Description -->
                    <div v-if="recipe.description" class="bg-surface-container-lowest rounded-xl p-8 shadow-sm">
                        <p class="text-on-surface-variant leading-relaxed">{{ recipe.description }}</p>
                    </div>

                    <!-- Ingredients -->
                    <div class="bg-surface-container-lowest rounded-xl p-8 shadow-sm">
                        <h2 class="text-2xl font-bold text-on-surface mb-6">Ingredientes</h2>
                        <div class="space-y-3">
                            <div v-for="ing in recipe.ingredients" :key="ing.id" class="flex items-center justify-between py-3 border-b border-surface-container-high last:border-0">
                                <span class="text-on-surface font-medium">{{ ing.name }}</span>
                                <span class="text-on-surface-variant">{{ parseFloat(ing.quantity).toFixed(2) }} {{ ing.unit }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div v-if="recipe.instructions" class="bg-surface-container-lowest rounded-xl p-8 shadow-sm">
                        <h2 class="text-2xl font-bold text-on-surface mb-6">Instrucciones</h2>
                        <p class="text-on-surface whitespace-pre-line leading-relaxed">{{ recipe.instructions }}</p>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Macronutrientes Card -->
                    <div class="bg-surface-container-lowest rounded-xl p-8 shadow-sm sticky top-8">
                        <h2 class="text-xl font-bold text-on-surface mb-6">Información Nutricional</h2>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between pb-4 border-b border-surface-container-high">
                                <span class="text-on-surface-variant">Calorías por porción</span>
                                <span class="text-2xl font-bold text-on-surface">{{ Math.round(recipe.calories) }} kcal</span>
                            </div>
                            <div class="flex items-center justify-between py-3">
                                <span class="text-on-surface-variant">Proteína</span>
                                <span class="text-lg font-bold text-primary">{{ parseFloat(recipe.protein).toFixed(1) }}g</span>
                            </div>
                            <div class="flex items-center justify-between py-3">
                                <span class="text-on-surface-variant">Grasas</span>
                                <span class="text-lg font-bold text-secondary">{{ parseFloat(recipe.fats).toFixed(1) }}g</span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-t border-surface-container-high pt-4">
                                <span class="text-on-surface-variant">Carbohidratos</span>
                                <span class="text-lg font-bold text-on-surface">{{ parseFloat(recipe.carbs).toFixed(1) }}g</span>
                            </div>
                        </div>
                    </div>

                    <!-- Details Card -->
                    <div class="bg-surface-container-lowest rounded-xl p-8 shadow-sm">
                        <h2 class="text-xl font-bold text-on-surface mb-6">Detalles</h2>

                        <div class="space-y-4">
                            <div v-if="recipe.meal_type">
                                <p class="text-sm text-on-surface-variant uppercase font-bold">Tipo de Comida</p>
                                <p class="text-on-surface font-medium mt-1">{{ mealTypeLabel }}</p>
                            </div>
                            <div v-if="recipe.diet_type">
                                <p class="text-sm text-on-surface-variant uppercase font-bold mt-3">Dieta</p>
                                <p class="text-on-surface font-medium mt-1">{{ recipe.diet_type }}</p>
                            </div>
                            <div v-if="recipe.preparation_time">
                                <p class="text-sm text-on-surface-variant uppercase font-bold mt-3">Tiempo</p>
                                <p class="text-on-surface font-medium mt-1">{{ recipe.preparation_time }} minutos</p>
                            </div>
                            <div v-if="recipe.servings">
                                <p class="text-sm text-on-surface-variant uppercase font-bold mt-3">Porciones</p>
                                <p class="text-on-surface font-medium mt-1">{{ recipe.servings }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="space-y-2">
                        <Link
                            :href="route('recipes.index')"
                            class="w-full px-6 py-3 text-primary font-bold hover:bg-primary/10 rounded-full transition-colors text-center"
                        >
                            Volver
                        </Link>
                        <button
                            @click="deleteRecipe"
                            class="w-full px-6 py-3 text-error font-bold hover:bg-error/10 rounded-full transition-colors"
                        >
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    recipe: Object,
});

const mealTypeLabels = {
    breakfast: 'Desayuno',
    lunch: 'Almuerzo',
    dinner: 'Cena',
    snack: 'Refrigerio',
    dessert: 'Postre',
    beverage: 'Bebida',
};

const mealTypeLabel = computed(() => {
    return mealTypeLabels[props.recipe.meal_type] || props.recipe.meal_type;
});

function deleteRecipe() {
    if (confirm(`¿Eliminar la receta "${props.recipe.name}"?`)) {
        router.delete(route('recipes.destroy', props.recipe.id), {
            onSuccess: () => {
                router.push(route('recipes.index'));
            },
        });
    }
}
</script>
