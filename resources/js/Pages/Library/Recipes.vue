<template>
    <AppLayout>
        <main class="p-8 min-h-screen">
            <!-- Header Section -->
            <section class="mb-12">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
                    <div>
                        <nav class="flex text-xs text-on-surface-variant mb-2 space-x-2 items-center">
                            <span>Directorio</span>
                            <span class="material-symbols-outlined" style="font-size:14px">chevron_right</span>
                            <span class="text-primary font-bold">Recetario</span>
                        </nav>
                        <h3 class="text-4xl font-extrabold text-on-surface mb-2 tracking-tight">Curaduría de Bienestar</h3>
                        <p class="text-on-surface-variant max-w-xl text-lg">Explora nuestra colección premium de recetas diseñadas para maximizar el rendimiento metabólico y la salud integral.</p>
                    </div>
                    <div class="flex gap-2">
                        <Link
                            :href="route('recipes.index')"
                            class="bg-surface-container-high px-6 py-2 rounded-full font-semibold text-sm hover:bg-surface-container-highest transition-colors"
                        >
                            Exportar PDF
                        </Link>
                        <Link
                            :href="route('recipes.create')"
                            class="bg-primary text-on-primary px-6 py-2 rounded-full font-bold text-sm shadow-sm hover:opacity-90 transition-all flex items-center gap-2"
                        >
                            <span class="material-symbols-outlined text-sm">add</span> Nueva Receta
                        </Link>
                    </div>
                </div>

                <!-- Filter Chips -->
                <div class="flex flex-wrap gap-3">
                    <button
                        @click="filterMealType = ''"
                        :class="[
                            'px-5 py-2 rounded-full text-sm font-bold transition-colors',
                            !filterMealType
                                ? 'bg-primary text-on-primary shadow-md'
                                : 'bg-secondary-container text-on-secondary-container hover:bg-surface-container-highest'
                        ]"
                    >
                        Todas
                    </button>
                    <button
                        v-for="mealType in Object.keys(mealTypeLabels)"
                        :key="mealType"
                        @click="filterMealType = mealType"
                        :class="[
                            'px-5 py-2 rounded-full text-sm font-semibold transition-colors',
                            filterMealType === mealType
                                ? 'bg-primary text-on-primary shadow-md'
                                : 'bg-secondary-container text-on-secondary-container hover:bg-surface-container-highest'
                        ]"
                    >
                        {{ mealTypeLabels[mealType] }}
                    </button>
                </div>
            </section>

            <!-- Recipe Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <!-- Featured Card (Large) -->
                <div
                    v-if="featuredRecipe"
                    class="md:col-span-2 lg:col-span-2 bg-surface-container-lowest rounded-xl overflow-hidden group flex flex-col md:flex-row h-full"
                >
                    <div class="md:w-1/2 relative h-64 md:h-auto overflow-hidden bg-surface-container-high">
                        <img
                            v-if="featuredRecipe.image_url"
                            :src="featuredRecipe.image_url"
                            :alt="featuredRecipe.name"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-4xl text-on-surface-variant">no_photography</span>
                        </div>
                        <div class="absolute top-4 left-4 bg-primary text-on-primary px-3 py-1 rounded-full text-xs font-bold tracking-wider uppercase">
                            Destacada
                        </div>
                    </div>
                    <div class="md:w-1/2 p-8 flex flex-col justify-between">
                        <div>
                            <h4 class="text-2xl font-bold text-on-surface mb-3 leading-tight">{{ featuredRecipe.name }}</h4>
                            <p class="text-on-surface-variant text-sm mb-6 leading-relaxed">{{ featuredRecipe.description }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4 border-t border-surface-container-high pt-6">
                            <div class="flex flex-col">
                                <span class="text-[10px] uppercase tracking-widest text-outline font-bold">Calorías</span>
                                <span class="text-lg font-bold text-on-surface">{{ Math.round(featuredRecipe.calories) }} kcal</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] uppercase tracking-widest text-outline font-bold">Proteínas</span>
                                <span class="text-lg font-bold text-primary">{{ parseFloat(featuredRecipe.protein).toFixed(1) }}g</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] uppercase tracking-widest text-outline font-bold">Grasas</span>
                                <span class="text-lg font-bold text-secondary">{{ parseFloat(featuredRecipe.fats).toFixed(1) }}g</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] uppercase tracking-widest text-outline font-bold">Carbs</span>
                                <span class="text-lg font-bold text-on-surface-variant">{{ parseFloat(featuredRecipe.carbs).toFixed(1) }}g</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Standard Recipe Cards -->
                <div
                    v-for="recipe in filteredRecipes"
                    :key="recipe.id"
                    class="bg-surface-container-lowest rounded-xl overflow-hidden group flex flex-col shadow-sm hover:shadow-md transition-shadow"
                >
                    <div class="h-48 relative overflow-hidden bg-surface-container-high">
                        <img
                            v-if="recipe.image_url"
                            :src="recipe.image_url"
                            :alt="recipe.name"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-4xl text-on-surface-variant">no_photography</span>
                        </div>
                        <Link
                            :href="route('recipes.edit', recipe.id)"
                            class="absolute top-3 right-3 w-8 h-8 rounded-full glass bg-surface-container-lowest/80 flex items-center justify-center text-on-surface hover:text-primary transition-colors hover:bg-primary/10"
                        >
                            <span class="material-symbols-outlined text-sm">edit</span>
                        </Link>
                    </div>
                    <div class="p-6 flex flex-col flex-1">
                        <Link :href="route('recipes.show', recipe.id)" class="font-bold text-on-surface mb-4 hover:text-primary transition-colors">
                            {{ recipe.name }}
                        </Link>
                        <p class="text-xs text-on-surface-variant mb-4 line-clamp-2">{{ recipe.description }}</p>
                        <div class="mt-auto grid grid-cols-2 gap-y-3">
                            <div class="text-xs">
                                <p class="text-outline font-medium">Kcal</p>
                                <p class="font-bold text-on-surface">{{ Math.round(recipe.calories) }}</p>
                            </div>
                            <div class="text-xs">
                                <p class="text-outline font-medium">P</p>
                                <p class="font-bold text-primary">{{ parseFloat(recipe.protein).toFixed(1) }}g</p>
                            </div>
                            <div class="text-xs">
                                <p class="text-outline font-medium">G</p>
                                <p class="font-bold text-on-surface">{{ parseFloat(recipe.fats).toFixed(1) }}g</p>
                            </div>
                            <div class="text-xs">
                                <p class="text-outline font-medium">C</p>
                                <p class="font-bold text-on-surface">{{ parseFloat(recipe.carbs).toFixed(1) }}g</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="filteredRecipes.length === 0 && !featuredRecipe" class="text-center py-16">
                <span class="material-symbols-outlined text-6xl text-on-surface-variant/30 block mb-4">restaurant</span>
                <p class="text-on-surface-variant text-lg">No hay recetas disponibles</p>
                <Link :href="route('recipes.create')" class="inline-block mt-4 bg-primary text-on-primary px-6 py-2 rounded-full font-bold text-sm">
                    Crear Primera Receta
                </Link>
            </div>
        </main>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    recipes: Array,
    mealTypeLabels: Object,
});

const filterMealType = ref('');

const featuredRecipe = computed(() => {
    return props.recipes.find(r => r.is_featured);
});

const filteredRecipes = computed(() => {
    let result = props.recipes.filter(r => !r.is_featured);
    if (filterMealType.value) {
        result = result.filter(r => r.meal_type === filterMealType.value);
    }
    return result;
});
</script>
