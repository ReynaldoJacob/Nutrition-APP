<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'

const selectedDay = ref(0)
const days = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom']
const searchQuery = ref('')

// Datos de ejemplo
const meals = ref({
    0: {
        desayuno: { time: '07:30 - 08:30', items: [{ id: 1, name: 'Aguacate y Huevo', calories: 380, protein: 18 }] },
        comida: { time: '12:30 - 13:30', items: [{ id: 2, name: 'Bowl de Quinoa', calories: 520, protein: 42 }] },
        cena: { time: '19:00 - 20:30', items: [] },
    },
    1: { desayuno: { time: '07:30 - 08:30', items: [] }, comida: { time: '12:30 - 13:30', items: [] }, cena: { time: '19:00 - 20:30', items: [] } },
    2: { desayuno: { time: '07:30 - 08:30', items: [] }, comida: { time: '12:30 - 13:30', items: [] }, cena: { time: '19:00 - 20:30', items: [] } },
    3: { desayuno: { time: '07:30 - 08:30', items: [] }, comida: { time: '12:30 - 13:30', items: [] }, cena: { time: '19:00 - 20:30', items: [] } },
    4: { desayuno: { time: '07:30 - 08:30', items: [] }, comida: { time: '12:30 - 13:30', items: [] }, cena: { time: '19:00 - 20:30', items: [] } },
    5: { desayuno: { time: '07:30 - 08:30', items: [] }, comida: { time: '12:30 - 13:30', items: [] }, cena: { time: '19:00 - 20:30', items: [] } },
    6: { desayuno: { time: '07:30 - 08:30', items: [] }, comida: { time: '12:30 - 13:30', items: [] }, cena: { time: '19:00 - 20:30', items: [] } },
})

const foodDatabase = ref([])
const fatSecretResults = ref([])
const isLoadingFoods = ref(false)
const isSearchingFatSecret = ref(false)
const localSearchError = ref('')
const fatSecretSearchError = ref('')

const fallbackFoods = [
    { id: 'fb-1', name: 'Pechuga de pollo', portion: '100g', calories: 165, protein: 31, carbs: 0, fat: 3.6 },
    { id: 'fb-2', name: 'Arroz blanco cocido', portion: '100g', calories: 130, protein: 2.4, carbs: 28, fat: 0.3 },
    { id: 'fb-3', name: 'Huevo entero', portion: '1 pieza', calories: 72, protein: 6.3, carbs: 0.4, fat: 4.8 },
    { id: 'fb-4', name: 'Avena', portion: '40g', calories: 156, protein: 6.8, carbs: 26.5, fat: 2.8 },
    { id: 'fb-5', name: 'Yogur griego natural', portion: '150g', calories: 95, protein: 15, carbs: 4.5, fat: 1.5 },
    { id: 'fb-6', name: 'Atun en agua', portion: '100g', calories: 116, protein: 25.5, carbs: 0, fat: 0.8 },
    { id: 'fb-7', name: 'Frijoles cocidos', portion: '100g', calories: 127, protein: 8.7, carbs: 22.8, fat: 0.5 },
    { id: 'fb-8', name: 'Camote cocido', portion: '100g', calories: 86, protein: 1.6, carbs: 20.1, fat: 0.1 },
]

const currentDayMeals = computed(() => meals.value[selectedDay.value] || {})

const dailyTotals = computed(() => {
    const dayMeals = currentDayMeals.value
    let total = { calories: 0, protein: 0, carbs: 0, fat: 0 }
    Object.values(dayMeals).forEach(meal => {
        if (meal.items) meal.items.forEach(item => {
            total.calories += item.calories || 0
            total.protein += item.protein || 0
            total.carbs += item.carbs || 0
            total.fat += item.fat || 0
        })
    })
    return total
})

const filteredFoods = computed(() => foodDatabase.value)

const mealLabels = { desayuno: 'Desayuno', comida: 'Comida', cena: 'Cena' }

const normalizeFood = (food) => ({
    id: food.id || food.external_id,
    external_id: food.external_id,
    name: food.name_es || food.name,
    portion: food.serving_size && food.serving_unit ? `${food.serving_size}${food.serving_unit}` : '100g',
    calories: Number(food.calories || 0),
    protein: Number(food.protein || 0),
    carbs: Number(food.carbs || 0),
    fat: Number(food.fat || 0),
})

const fallbackFilter = (query) => {
    const q = query.trim().toLowerCase()
    const source = fallbackFoods.map(item => ({ ...item }))
    return !q ? source : source.filter(item => item.name.toLowerCase().includes(q))
}

const parseResponse = async (response) => {
    const contentType = response.headers.get('content-type') || ''
    if (!contentType.includes('application/json')) {
        return null
    }

    return response.json()
}

const loadFoods = async () => {
    isLoadingFoods.value = true
    localSearchError.value = ''

    try {
        const params = new URLSearchParams()
        if (searchQuery.value.trim()) params.set('query', searchQuery.value.trim())
        params.set('limit', '30')

        const response = await fetch(`/api/foods?${params.toString()}`, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })

        const payload = await parseResponse(response)

        if (!response.ok || !payload) {
            throw new Error(payload?.message || 'No se pudo consultar el catalogo local.')
        }

        foodDatabase.value = (payload.data || []).map(normalizeFood)

        if (foodDatabase.value.length === 0) {
            foodDatabase.value = fallbackFilter(searchQuery.value)
        }
    } catch {
        localSearchError.value = 'No se pudo consultar tu catalogo. Mostrando lista base en espanol.'
        foodDatabase.value = fallbackFilter(searchQuery.value)
    } finally {
        isLoadingFoods.value = false
    }
}

const searchInFatSecret = async () => {
    if (searchQuery.value.trim().length < 2) return

    isSearchingFatSecret.value = true
    fatSecretSearchError.value = ''

    try {
        const params = new URLSearchParams({ query: searchQuery.value.trim(), limit: '12' })
        const response = await fetch(`/api/foods/fatsecret/search?${params.toString()}`, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })

        const payload = await parseResponse(response)

        if (!response.ok || !payload) {
            const detail = payload?.detail ? ` ${payload.detail}` : ''
            throw new Error((payload?.message || 'No se pudo buscar en FatSecret.') + detail)
        }

        fatSecretResults.value = (payload.data || []).map(normalizeFood)
        if (fatSecretResults.value.length === 0) {
            fatSecretSearchError.value = 'FatSecret no devolvio sugerencias para ese termino.'
        }
    } catch (error) {
        fatSecretResults.value = []
        fatSecretSearchError.value = error?.message || 'No se pudo consultar FatSecret.'
    } finally {
        isSearchingFatSecret.value = false
    }
}

watch(searchQuery, () => {
    loadFoods()
})

onMounted(() => {
    loadFoods()
})

const addFood = (mealType, food) => {
    meals.value[selectedDay.value][mealType].items.push({
        id: Math.random(),
        name: food.name,
        calories: food.calories || 0,
        protein: food.protein || 0,
        carbs: food.carbs || 0,
        fat: food.fat || 0,
    })
}

const removeFood = (mealType, itemId) => {
    const items = meals.value[selectedDay.value][mealType].items
    const idx = items.findIndex(i => i.id === itemId)
    if (idx > -1) items.splice(idx, 1)
}

const savePlan = () => console.log('Guardado:', meals.value)
</script>

<template>
    <AppLayout>
        <Head title="Planes Alimenticios" />

        <main class="p-8">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
                <div>
                    <nav class="flex text-xs text-on-surface-variant mb-2 space-x-2 items-center">
                        <span>Directorio</span>
                        <span class="material-symbols-outlined" style="font-size:14px">chevron_right</span>
                        <span class="text-primary font-bold">Planes Alimenticios</span>
                    </nav>
                    <h1 class="text-4xl font-extrabold text-on-surface tracking-tight font-headline">Planes Alimenticios</h1>
                    <p class="text-on-surface-variant mt-1">Diseña planes nutricionales personalizados.</p>
                </div>
                <button
                    @click="savePlan"
                    class="self-start shrink-0 bg-primary text-on-primary px-6 py-3 rounded-xl font-bold inline-flex items-center gap-2 shadow-lg hover:opacity-90 transition-opacity"
                >
                    <span class="material-symbols-outlined">save</span>
                    Guardar
                </button>
            </div>

            <!-- Days Tabs -->
            <div class="mb-8 flex gap-2 bg-surface-container-low p-1.5 rounded-xl overflow-x-auto">
                <button
                    v-for="(day, i) in days"
                    :key="i"
                    @click="selectedDay = i"
                    :class="[
                        'px-4 py-3 rounded-lg font-bold text-sm transition-all whitespace-nowrap shrink-0',
                        selectedDay === i
                            ? 'bg-primary text-on-primary shadow-md'
                            : 'text-on-surface-variant hover:text-on-surface'
                    ]"
                >
                    {{ day }}
                </button>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Meals -->
                <div class="lg:col-span-2 space-y-6">
                    <template v-for="(mealType) in ['desayuno', 'comida', 'cena']" :key="mealType">
                        <div class="bg-gradient-to-br from-surface-container-lowest to-surface-container-low rounded-2xl p-6 shadow-sm border border-outline-variant/10">
                            <div class="flex items-start justify-between mb-6">
                                <div>
                                    <div class="flex items-center gap-3 mb-2">
                                        <div
                                            :class="[
                                                'w-10 h-10 rounded-lg flex items-center justify-center',
                                                mealType === 'desayuno' ? 'bg-orange-100 text-orange-600' :
                                                mealType === 'comida' ? 'bg-blue-100 text-blue-600' :
                                                'bg-indigo-100 text-indigo-600'
                                            ]"
                                        >
                                            <span class="material-symbols-outlined text-lg">
                                                {{ mealType === 'desayuno' ? 'light_mode' : mealType === 'comida' ? 'sunny' : 'bedtime' }}
                                            </span>
                                        </div>
                                        <h3 class="font-bold text-lg text-on-surface capitalize">{{ mealType }}</h3>
                                    </div>
                                    <p class="text-xs text-on-surface-variant ml-13">{{ currentDayMeals[mealType]?.time }}</p>
                                </div>
                                <span class="text-xs font-bold bg-primary/10 text-primary px-3 py-1 rounded-full">
                                    {{ currentDayMeals[mealType]?.items?.length || 0 }} items
                                </span>
                            </div>

                            <div class="space-y-3">
                                <div
                                    v-if="!currentDayMeals[mealType]?.items?.length"
                                    class="py-8 text-center text-on-surface-variant rounded-xl border-2 border-dashed border-outline-variant/20"
                                >
                                    <span class="material-symbols-outlined text-4xl block mb-2 opacity-40">restaurant</span>
                                    <p class="text-sm font-medium">Sin alimentos añadidos</p>
                                </div>
                                <div
                                    v-for="item in currentDayMeals[mealType]?.items"
                                    :key="item.id"
                                    class="flex items-center justify-between p-4 bg-surface-container-lowest rounded-xl hover:bg-surface-container-high transition-all group border border-outline-variant/20"
                                >
                                    <div class="flex-1">
                                        <p class="font-semibold text-sm text-on-surface">{{ item.name }}</p>
                                        <p class="text-xs text-on-surface-variant mt-1">{{ item.calories }} kcal</p>
                                    </div>
                                    <button
                                        @click="removeFood(mealType, item.id)"
                                        class="ml-4 text-on-surface-variant hover:text-error opacity-0 group-hover:opacity-100 transition-all p-1"
                                    >
                                        <span class="material-symbols-outlined text-sm">close</span>
                                    </button>
                                </div>
                            </div>

                            <button
                                class="w-full mt-4 py-3 border-2 border-dashed border-outline-variant/30 text-outline-variant text-sm font-semibold rounded-xl hover:border-primary hover:text-primary transition-all flex items-center justify-center gap-2 group"
                            >
                                <span class="material-symbols-outlined text-sm group-hover:scale-110 transition-transform">add_circle</span>
                                Añadir alimento
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Totales -->
                    <div class="bg-primary text-on-primary p-6 rounded-2xl shadow-lg">
                        <h3 class="font-bold text-lg mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-2xl">bar_chart</span>
                            Resumen del día
                        </h3>
                        <div class="space-y-6">
                            <!-- Calorías destacadas -->
                            <div class="bg-white/15 rounded-xl p-4 border border-white/20">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-semibold opacity-90">Calorías totales</span>
                                    <span class="text-2xl font-bold">{{ dailyTotals.calories }}</span>
                                </div>
                                <p class="text-xs opacity-70 mb-3">Objetivo: ~2,000 kcal</p>
                                <div class="h-3 bg-white/20 rounded-full overflow-hidden">
                                    <div class="h-full bg-white rounded-full transition-all" :style="{ width: `${Math.min((dailyTotals.calories / 2000) * 100, 100)}%` }"></div>
                                </div>
                            </div>

                            <!-- Macros -->
                            <div class="grid grid-cols-3 gap-3">
                                <!-- Proteína -->
                                <div class="bg-white/10 rounded-xl p-3 border border-white/15 hover:bg-white/15 transition-colors">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-2 h-2 rounded-full bg-white"></div>
                                        <p class="text-xs opacity-75 uppercase font-semibold tracking-wide">Proteína</p>
                                    </div>
                                    <p class="font-bold text-lg">{{ dailyTotals.protein }}g</p>
                                    <p class="text-xs opacity-60 mt-1">150g obj</p>
                                </div>

                                <!-- Carbos -->
                                <div class="bg-white/10 rounded-xl p-3 border border-white/15 hover:bg-white/15 transition-colors">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-2 h-2 rounded-full bg-white"></div>
                                        <p class="text-xs opacity-75 uppercase font-semibold tracking-wide">Carbos</p>
                                    </div>
                                    <p class="font-bold text-lg">{{ dailyTotals.carbs }}g</p>
                                    <p class="text-xs opacity-60 mt-1">300g obj</p>
                                </div>

                                <!-- Grasas -->
                                <div class="bg-white/10 rounded-xl p-3 border border-white/15 hover:bg-white/15 transition-colors">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-2 h-2 rounded-full bg-white"></div>
                                        <p class="text-xs opacity-75 uppercase font-semibold tracking-wide">Grasas</p>
                                    </div>
                                    <p class="font-bold text-lg">{{ dailyTotals.fat }}g</p>
                                    <p class="text-xs opacity-60 mt-1">65g obj</p>
                                </div>
                            </div>
                        </div>

                        <button class="w-full mt-6 py-3 bg-white/20 hover:bg-white/30 text-on-primary font-bold rounded-xl transition-all flex items-center justify-center gap-2 border border-white/20">
                            <span class="material-symbols-outlined">download</span>
                            Exportar plan
                        </button>
                    </div>

                    <!-- Alimentos -->
                    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm h-[500px] flex flex-col border border-outline-variant/10">
                        <h3 class="font-bold text-lg text-on-surface mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined">restaurant_menu</span>
                            Base de Datos
                        </h3>
                        <div class="relative mb-4">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
                            <input
                                v-model="searchQuery"
                                type="text"
                                class="w-full bg-surface-container-low border-2 border-outline-variant/20 rounded-xl py-3 pl-11 text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                placeholder="Buscar alimentos..."
                            />
                        </div>

                        <div class="mb-4 flex items-center justify-between gap-2">
                            <p class="text-xs text-on-surface-variant">
                                Catálogo local{{ searchQuery ? ` para "${searchQuery}"` : '' }}
                            </p>
                            <button
                                v-if="searchQuery.length >= 2"
                                @click="searchInFatSecret"
                                :disabled="isSearchingFatSecret"
                                class="text-xs py-2 px-3 bg-primary/10 text-primary rounded-lg hover:bg-primary/20 transition-all font-semibold disabled:opacity-50"
                            >
                                {{ isSearchingFatSecret ? 'Buscando FatSecret...' : 'Buscar en FatSecret' }}
                            </button>
                        </div>

                        <div v-if="localSearchError" class="mb-3 rounded-lg border border-amber-300/60 bg-amber-50 px-3 py-2 text-xs text-amber-800">
                            {{ localSearchError }}
                        </div>

                        <div v-if="fatSecretSearchError" class="mb-3 rounded-lg border border-red-300/60 bg-red-50 px-3 py-2 text-xs text-red-700">
                            {{ fatSecretSearchError }}
                        </div>

                        <div class="flex-1 overflow-y-auto space-y-3 pr-2">
                            <div v-if="isLoadingFoods" class="text-center py-4 text-on-surface-variant text-sm">
                                Cargando alimentos...
                            </div>

                            <div
                                v-for="food in filteredFoods"
                                :key="food.id"
                                class="p-4 bg-surface-container rounded-xl hover:shadow-md hover:bg-surface-container-high transition-all border border-outline-variant/10 group cursor-pointer"
                            >
                                <p class="font-semibold text-sm text-on-surface group-hover:text-primary transition-colors">{{ food.name }}</p>
                                <p class="text-xs text-on-surface-variant mb-3 mt-1">{{ food.portion }} • <span class="font-bold text-orange-600">{{ food.calories }} kcal</span></p>
                                <div class="grid grid-cols-3 gap-2">
                                    <button
                                        @click="addFood('desayuno', food)"
                                        class="text-xs py-2 bg-primary/10 text-primary rounded-lg hover:bg-primary/20 hover:shadow-sm transition-all font-semibold"
                                    >
                                        <span class="material-symbols-outlined text-sm inline mr-1 -my-0.5">light_mode</span>
                                        Desayuno
                                    </button>
                                    <button
                                        @click="addFood('comida', food)"
                                        class="text-xs py-2 bg-primary/10 text-primary rounded-lg hover:bg-primary/20 hover:shadow-sm transition-all font-semibold"
                                    >
                                        <span class="material-symbols-outlined text-sm inline mr-1 -my-0.5">sunny</span>
                                        Comida
                                    </button>
                                    <button
                                        @click="addFood('cena', food)"
                                        class="text-xs py-2 bg-primary/10 text-primary rounded-lg hover:bg-primary/20 hover:shadow-sm transition-all font-semibold"
                                    >
                                        <span class="material-symbols-outlined text-sm inline mr-1 -my-0.5">bedtime</span>
                                        Cena
                                    </button>
                                </div>
                            </div>

                            <div v-if="fatSecretResults.length" class="pt-3 mt-4 border-t border-outline-variant/20">
                                <p class="text-xs font-semibold text-on-surface-variant mb-2">Sugerencias FatSecret (externas)</p>

                                <div
                                    v-for="food in fatSecretResults"
                                    :key="`fatsecret-${food.id || food.external_id}`"
                                    class="p-3 mb-2 bg-primary/5 rounded-lg border border-primary/15"
                                >
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-semibold text-sm text-on-surface">{{ food.name }}</p>
                                            <p class="text-xs text-on-surface-variant">{{ food.portion }} • {{ food.calories }} kcal</p>
                                        </div>
                                        <span class="text-[11px] px-2 py-1 rounded-md bg-surface-container-low text-on-surface-variant border border-outline-variant/25">
                                            Solo vista previa
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 p-3 rounded-lg bg-surface-container-low border border-outline-variant/25 text-xs text-on-surface-variant">
                                <p class="font-semibold text-on-surface mb-1">Fuente externa: FatSecret</p>
                                <p>Estos resultados son solo de consulta y no se almacenan en la plataforma.</p>
                                <p class="mt-1">
                                    Revisa terminos en
                                    <a
                                        href="https://platform.fatsecret.com/terms"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-primary font-semibold hover:underline"
                                    >
                                        FatSecret Terms of Use
                                    </a>
                                    .
                                </p>
                                <p class="mt-1 text-[11px]">
                                    La informacion nutricional no sustituye orientacion medica profesional.
                                </p>
                            </div>

                            <div v-if="filteredFoods.length === 0" class="text-center py-8 text-on-surface-variant">
                                <span class="material-symbols-outlined text-4xl block mb-2 opacity-40">search_off</span>
                                <p class="text-sm">No se encontraron alimentos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </AppLayout>
</template>

<style scoped>
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: rgb(220, 228, 232);
    border-radius: 10px;
}
</style>
