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
                            <span class="text-primary font-bold">Mis Ingredientes</span>
                        </nav>
                        <h3 class="text-4xl font-extrabold text-on-surface mb-2 tracking-tight">Base de Ingredientes</h3>
                        <p class="text-on-surface-variant max-w-xl text-lg">Gestiona tu biblioteca personal de ingredientes con sus valores nutricionales.</p>
                    </div>
                    <div class="flex gap-2">
                        <button
                            @click="exportToCSV"
                            class="bg-surface-container-high px-6 py-2 rounded-full font-semibold text-sm hover:bg-surface-container-highest transition-colors"
                        >
                            Exportar CSV
                        </button>
                        <Link
                            :href="route('ingredients.create')"
                            class="bg-primary text-on-primary px-6 py-2 rounded-full font-bold text-sm shadow-sm hover:opacity-90 transition-all flex items-center gap-2"
                        >
                            <span class="material-symbols-outlined text-sm">add</span> Nuevo Ingrediente
                        </Link>
                    </div>
                </div>

                <!-- Search/Filter -->
                <div class="flex gap-3">
                    <div class="flex-1 flex items-center gap-2 bg-surface-container-high rounded-full px-4 py-2">
                        <span class="material-symbols-outlined text-on-surface-variant">search</span>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Buscar ingredientes..."
                            class="bg-transparent border-none focus:ring-0 text-sm w-full placeholder:text-on-surface-variant outline-none"
                        />
                    </div>
                    <select
                        v-model="filterCategory"
                        class="bg-surface-container-high border-none rounded-full px-4 py-2 text-sm focus:ring-2 focus:ring-primary/20"
                    >
                        <option value="">Todas las categorías</option>
                        <option v-for="(label, cat) in categoryLabels" :key="cat" :value="cat">
                            {{ label }}
                        </option>
                    </select>
                </div>
            </section>

            <!-- Ingredients Table -->
            <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden">
                <div v-if="filteredIngredients.length === 0" class="text-center py-16">
                    <span class="material-symbols-outlined text-6xl text-on-surface-variant/30 block mb-4">grocery</span>
                    <p class="text-on-surface-variant text-lg">{{ searchQuery ? 'No ingredientes encontrados' : 'No hay ingredientes registrados' }}</p>
                </div>

                <table v-else class="w-full text-left">
                    <thead>
                        <tr class="bg-surface-container-low">
                            <th class="px-8 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-widest">Ingrediente</th>
                            <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-widest">Categoría</th>
                            <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-widest">Unidad</th>
                            <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-widest text-center">Calorías</th>
                            <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-widest text-center">Proteína</th>
                            <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-widest text-center">Grasas</th>
                            <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-widest text-center">Carbs</th>
                            <th class="px-8 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-widest text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="ingredient in filteredIngredients"
                            :key="ingredient.id"
                            class="border-t border-surface-container-high hover:bg-surface-bright transition-colors"
                        >
                            <td class="px-8 py-5">
                                <div>
                                    <p class="font-bold text-on-surface text-sm">{{ ingredient.name }}</p>
                                    <p v-if="ingredient.description" class="text-xs text-on-surface-variant">{{ ingredient.description }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span
                                    v-if="ingredient.category"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-primary/15 text-primary"
                                >
                                    {{ categoryLabels[ingredient.category] || ingredient.category }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-sm font-medium text-on-surface">{{ ingredient.unit_of_measure }}</td>
                            <td class="px-6 py-5 text-sm text-center font-medium text-on-surface">{{ parseFloat(ingredient.calories_per_unit).toFixed(1) }}</td>
                            <td class="px-6 py-5 text-sm text-center font-medium text-primary">{{ parseFloat(ingredient.protein_per_unit).toFixed(2) }}g</td>
                            <td class="px-6 py-5 text-sm text-center font-medium text-secondary">{{ parseFloat(ingredient.fats_per_unit).toFixed(2) }}g</td>
                            <td class="px-6 py-5 text-sm text-center font-medium text-on-surface">{{ parseFloat(ingredient.carbs_per_unit).toFixed(2) }}g</td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Link
                                        :href="route('ingredients.edit', ingredient.id)"
                                        class="p-2 text-outline hover:text-primary transition-colors rounded-lg hover:bg-surface-container-high"
                                    >
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                    </Link>
                                    <button
                                        @click="confirmDelete(ingredient)"
                                        class="p-2 text-outline hover:text-error transition-colors rounded-lg hover:bg-error/10"
                                    >
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Delete Confirmation Modal -->
            <Teleport to="body">
                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6">
                        <div class="fixed inset-0 bg-inverse-surface/60 backdrop-blur-sm" @click="showDeleteModal = false" />
                        <div class="relative bg-surface-container-lowest w-full max-w-md rounded-2xl shadow-2xl overflow-hidden z-10 p-8">
                            <div class="mb-6">
                                <h3 class="text-2xl font-bold text-on-surface mb-2">Eliminar Ingrediente</h3>
                                <p class="text-on-surface-variant">¿Estás seguro de que deseas eliminar <strong>{{ ingredientToDelete?.name }}</strong>?</p>
                            </div>
                            <div class="flex gap-3 justify-end">
                                <button
                                    @click="showDeleteModal = false"
                                    class="px-6 py-2 text-primary font-bold hover:bg-primary/10 rounded-full"
                                >
                                    Cancelar
                                </button>
                                <button
                                    @click="deleteIngredient"
                                    class="px-6 py-2 bg-error text-on-error font-bold rounded-full hover:opacity-90"
                                >
                                    Eliminar
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
import { computed, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    ingredients: Array,
    categoryLabels: Object,
});

const searchQuery = ref('');
const filterCategory = ref('');
const showDeleteModal = ref(false);
const ingredientToDelete = ref(null);

const filteredIngredients = computed(() => {
    let result = props.ingredients;

    if (searchQuery.value) {
        result = result.filter(i =>
            i.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (i.description && i.description.toLowerCase().includes(searchQuery.value.toLowerCase()))
        );
    }

    if (filterCategory.value) {
        result = result.filter(i => i.category === filterCategory.value);
    }

    return result;
});

function confirmDelete(ingredient) {
    ingredientToDelete.value = ingredient;
    showDeleteModal.value = true;
}

function deleteIngredient() {
    if (ingredientToDelete.value) {
        router.delete(route('ingredients.destroy', ingredientToDelete.value.id), {
            onSuccess: () => {
                showDeleteModal.value = false;
                ingredientToDelete.value = null;
            },
        });
    }
}

function exportToCSV() {
    const headers = ['Nombre', 'Categoría', 'Unidad', 'Calorías', 'Proteína', 'Grasas', 'Carbs'];
    const rows = filteredIngredients.value.map(i => [
        i.name,
        props.categoryLabels[i.category] || i.category || '',
        i.unit_of_measure,
        i.calories_per_unit,
        i.protein_per_unit,
        i.fats_per_unit,
        i.carbs_per_unit,
    ]);

    const csv = [
        headers.join(','),
        ...rows.map(row => row.map(cell => `"${cell}"`).join(',')),
    ].join('\n');

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `ingredientes-${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}
</script>
