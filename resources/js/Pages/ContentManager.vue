<template>
    <AppLayout>
        <Head title="Gestor de Contenido" />
        <section class="p-8 pb-12 space-y-8">
            <header class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <nav class="flex items-center gap-2 text-sm font-semibold text-on-surface-variant mb-2">
                        <span>Contenido</span>
                        <span class="material-symbols-outlined text-xs">chevron_right</span>
                        <span class="text-primary">Gestión de Comunidad</span>
                    </nav>
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-4xl font-extrabold text-on-surface font-headline tracking-tight">Gestor de Contenido</h1>
                        <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-[11px] font-black uppercase tracking-[0.18em] text-amber-700 border border-amber-200">
                            Pro
                        </span>
                    </div>
                    <p class="text-on-surface-variant max-w-3xl">Administra artículos, recetas y publicaciones educativas para tus pacientes desde un solo lugar.</p>
                </div>
                <button
                    type="button"
                    class="flex items-center gap-2 bg-gradient-to-r from-primary to-primary-dim text-on-primary px-6 py-3.5 rounded-xl font-bold text-sm shadow-sm active:scale-95 transition-all w-fit"
                    @click="showCreateModal = true"
                >
                    <span class="material-symbols-outlined">add_circle</span>
                    Crear Nuevo Contenido
                </button>
            </header>

            <div class="rounded-2xl border border-primary/10 bg-primary-container/25 px-5 py-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-bold text-on-primary-container">Módulo exclusivo de plan Pro</p>
                    <p class="text-xs text-on-surface-variant">Aquí podrás centralizar campañas educativas, recursos para pacientes y publicaciones de comunidad.</p>
                </div>
                <div class="text-xs font-bold uppercase tracking-[0.18em] text-primary">Listo para escalar tu contenido</div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="md:col-span-2 bg-primary-container p-6 rounded-3xl flex flex-col justify-between overflow-hidden relative min-h-[184px]">
                    <div class="relative z-10">
                        <p class="text-on-primary-container font-bold text-sm mb-1">Publicaciones Totales</p>
                        <h3 class="text-4xl font-extrabold text-on-primary-container font-headline">{{ stats.total_items }}</h3>
                        <div class="mt-4 flex items-center gap-2 text-on-primary-fixed-variant text-xs font-bold bg-white/30 backdrop-blur w-fit px-3 py-1 rounded-full">
                            <span class="material-symbols-outlined text-xs">group</span>
                            Visibles únicamente para tus pacientes
                        </div>
                    </div>
                    <div class="absolute bottom-0 right-0 w-32 h-32 opacity-20 pointer-events-none">
                        <span class="material-symbols-outlined text-8xl" style="font-variation-settings: 'FILL' 1;">insights</span>
                    </div>
                </div>

                <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant/10">
                    <p class="text-on-surface-variant font-bold text-sm mb-4">Publicados</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-secondary-container flex items-center justify-center text-on-secondary-container">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">send</span>
                        </div>
                        <div>
                            <h4 class="text-2xl font-extrabold text-on-surface font-headline">{{ stats.published_items }}</h4>
                            <p class="text-xs text-outline font-medium">Listos para pacientes</p>
                        </div>
                    </div>
                </div>

                <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant/10">
                    <p class="text-on-surface-variant font-bold text-sm mb-4">Borradores</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-tertiary-container flex items-center justify-center text-on-tertiary-container">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">draft</span>
                        </div>
                        <div>
                            <h4 class="text-2xl font-extrabold text-on-surface font-headline">{{ stats.draft_items }}</h4>
                            <p class="text-xs text-outline font-medium">Pendientes de revisión</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-outline-variant/15 bg-white px-6 py-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-bold text-on-surface">Publicación privada por nutriólogo</p>
                    <p class="text-xs text-on-surface-variant">Cada contenido queda asociado a tu usuario y la estructura ya está preparada para que sólo lo consuman pacientes vinculados a tu perfil.</p>
                </div>
                <div class="inline-flex items-center gap-2 rounded-full bg-surface-container-low px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-primary">
                    <span class="material-symbols-outlined text-sm">lock</span>
                    Comunidad cerrada
                </div>
            </div>

            <div class="flex flex-col xl:flex-row gap-4 xl:items-center">
                <div class="relative flex-1">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline">search</span>
                    <input
                        v-model="searchQuery"
                        class="w-full bg-surface-container-low border-none rounded-xl py-4 pl-12 pr-4 text-sm font-medium focus:ring-2 focus:ring-primary/20"
                        placeholder="Buscar por título o palabras clave..."
                        type="text"
                    />
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <select v-model="selectedCategory" class="bg-white px-5 py-3.5 rounded-xl text-sm font-bold text-on-surface-variant border-none focus:ring-2 focus:ring-primary/20">
                        <option value="Todas">Todas las Categorías</option>
                        <option v-for="category in categories" :key="category" :value="category">{{ category }}</option>
                    </select>
                    <select v-model="selectedStatus" class="bg-white px-5 py-3.5 rounded-xl text-sm font-bold text-on-surface-variant border-none focus:ring-2 focus:ring-primary/20">
                        <option value="Todos">Todos los Estados</option>
                        <option value="Publicado">Publicado</option>
                        <option value="Borrador">Borrador</option>
                        <option value="Programado">Programado</option>
                    </select>
                </div>
            </div>

            <div class="space-y-4">
                <div class="hidden md:grid grid-cols-12 px-6 text-xs font-bold text-outline uppercase tracking-wider mb-2">
                    <div class="col-span-6">Detalles del Contenido</div>
                    <div class="col-span-2 text-center">Categoría</div>
                    <div class="col-span-2 text-center">Estado</div>
                    <div class="col-span-2 text-right">Interacción</div>
                </div>

                <div
                    v-for="item in paginatedItems"
                    :key="item.id"
                    class="bg-surface-container-lowest p-4 rounded-2xl flex flex-col md:grid md:grid-cols-12 items-center gap-4 hover:bg-white transition-all duration-300 group shadow-sm hover:shadow-md cursor-pointer"
                >
                    <div class="col-span-6 flex items-center gap-4 w-full">
                        <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 bg-surface-container flex items-center justify-center">
                            <img v-if="item.image_url" :src="item.image_url" :alt="item.title" class="w-full h-full object-cover" />
                            <span v-else class="material-symbols-outlined text-outline">image</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-on-surface group-hover:text-primary transition-colors">{{ item.title }}</h4>
                            <p class="text-xs text-outline font-medium mt-1">{{ item.published_at }}</p>
                            <p v-if="item.excerpt" class="text-xs text-on-surface-variant mt-1 max-w-xl">{{ item.excerpt }}</p>
                        </div>
                    </div>

                    <div class="col-span-2 flex justify-center w-full">
                        <span :class="categoryClasses(item.category)">{{ item.category }}</span>
                    </div>

                    <div class="col-span-2 flex justify-center w-full">
                        <span :class="statusClasses(item.status)">
                            <template v-if="item.status === 'Publicado'">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                            </template>
                            <template v-else-if="item.status === 'Programado'">
                                <span class="material-symbols-outlined text-xs">schedule</span>
                            </template>
                            <template v-else>
                                <span class="w-1.5 h-1.5 rounded-full bg-outline"></span>
                            </template>
                            {{ item.status }}
                        </span>
                    </div>

                    <div class="col-span-2 flex flex-col items-end w-full">
                        <div class="flex items-center gap-2 text-xs font-bold text-outline">
                            <span class="material-symbols-outlined text-sm">schedule</span>
                            {{ item.reading_time_minutes }} min
                        </div>
                    </div>
                </div>

                <div v-if="paginatedItems.length === 0" class="rounded-3xl bg-surface-container-lowest border border-outline-variant/15 p-12 text-center text-on-surface-variant">
                    <span class="material-symbols-outlined text-5xl block mb-3 opacity-50">post_add</span>
                    <p class="font-bold text-on-surface mb-1">No se encontraron publicaciones</p>
                    <p class="text-sm">Intenta con otra búsqueda o cambia los filtros activos.</p>
                </div>
            </div>

            <div class="mt-8 flex flex-col gap-4 md:flex-row md:justify-between md:items-center bg-surface-container-low px-6 py-4 rounded-2xl">
                <p class="text-sm font-semibold text-outline">Mostrando {{ paginatedItems.length }} de {{ filteredItems.length }} publicaciones</p>
                <div class="flex gap-2">
                    <button
                        type="button"
                        class="w-10 h-10 flex items-center justify-center rounded-xl bg-white text-outline border border-surface-variant hover:text-primary transition-all disabled:opacity-40"
                        :disabled="currentPage === 1"
                        @click="currentPage -= 1"
                    >
                        <span class="material-symbols-outlined">chevron_left</span>
                    </button>
                    <button
                        v-for="pageNumber in totalPages"
                        :key="pageNumber"
                        type="button"
                        class="w-10 h-10 flex items-center justify-center rounded-xl font-bold transition-colors"
                        :class="pageNumber === currentPage ? 'bg-primary text-on-primary shadow-sm' : 'bg-white text-on-surface hover:bg-surface-container-high'"
                        @click="currentPage = pageNumber"
                    >
                        {{ pageNumber }}
                    </button>
                    <button
                        type="button"
                        class="w-10 h-10 flex items-center justify-center rounded-xl bg-white text-outline border border-surface-variant hover:text-primary transition-all disabled:opacity-40"
                        :disabled="currentPage === totalPages"
                        @click="currentPage += 1"
                    >
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                </div>
            </div>

            <ContentCreateModal :show="showCreateModal" @close="showCreateModal = false" @saved="showCreateModal = false" />
        </section>
    </AppLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ContentCreateModal from '@/Components/ContentCreateModal.vue'

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
    contentItems: {
        type: Array,
        default: () => [],
    },
})

const searchQuery = ref('')
const selectedCategory = ref('Todas')
const selectedStatus = ref('Todos')
const currentPage = ref(1)
const showCreateModal = ref(false)
const itemsPerPage = 4

const categories = computed(() => [...new Set(props.contentItems.map(item => item.category))])

const filteredItems = computed(() => {
    const term = searchQuery.value.trim().toLowerCase()

    return props.contentItems.filter((item) => {
        const matchesTerm = !term || item.title.toLowerCase().includes(term) || item.category.toLowerCase().includes(term)
        const matchesCategory = selectedCategory.value === 'Todas' || item.category === selectedCategory.value
        const matchesStatus = selectedStatus.value === 'Todos' || item.status === selectedStatus.value

        return matchesTerm && matchesCategory && matchesStatus
    })
})

const totalPages = computed(() => Math.max(1, Math.ceil(filteredItems.value.length / itemsPerPage)))

const paginatedItems = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage
    return filteredItems.value.slice(start, start + itemsPerPage)
})

watch([searchQuery, selectedCategory, selectedStatus], () => {
    currentPage.value = 1
})

watch(totalPages, (value) => {
    if (currentPage.value > value) {
        currentPage.value = value
    }
})

function categoryClasses(category) {
    const base = 'text-[10px] font-black uppercase px-3 py-1 rounded-full'

    if (category === 'Receta') {
        return `${base} bg-secondary-container text-on-secondary-container`
    }

    if (category === 'Artículo') {
        return `${base} bg-tertiary-container text-on-tertiary-container`
    }

    return `${base} bg-surface-container-highest text-on-surface-variant`
}

function statusClasses(status) {
    const base = 'flex items-center gap-1.5 font-bold text-xs px-3 py-1 rounded-full'

    if (status === 'Publicado') {
        return `${base} text-primary bg-primary/10`
    }

    if (status === 'Programado') {
        return `${base} text-secondary bg-secondary-container`
    }

    return `${base} text-outline bg-surface-container-highest`
}
</script>
