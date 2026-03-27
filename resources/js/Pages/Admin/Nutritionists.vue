<template>
    <AppLayout>
        <main class="p-8 min-h-screen">

            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
                <div>
                    <nav class="flex text-xs text-on-surface-variant mb-2 space-x-2 items-center">
                        <Link :href="route('admin.dashboard')" class="hover:text-primary transition-colors">Admin</Link>
                        <span class="material-symbols-outlined" style="font-size:14px">chevron_right</span>
                        <span class="text-primary font-bold">Gestión de Nutriólogos</span>
                    </nav>
                    <h1 class="text-4xl font-extrabold text-on-surface tracking-tight font-headline">Gestión de Nutriólogos</h1>
                    <p class="text-on-surface-variant mt-1">Administra los nutriólogos registrados en la plataforma.</p>
                </div>
                <button
                    class="self-start shrink-0 bg-primary text-on-primary px-6 py-3 rounded-xl font-bold inline-flex items-center gap-2 shadow-lg hover:opacity-90 transition-opacity active:scale-95"
                    @click="showModal = true"
                >
                    <span class="material-symbols-outlined">person_add</span>
                    Añadir Nutriólogo
                </button>
            </div>

            <!-- Flash de contraseña temporal -->
            <div v-if="$page.props.flash?.success" class="mb-6 bg-primary-container text-on-primary-container rounded-2xl px-6 py-4 flex items-start gap-3">
                <span class="material-symbols-outlined shrink-0">info</span>
                <p class="text-sm font-medium">{{ $page.props.flash.success }}</p>
            </div>

            <!-- Stats rápidas -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-surface-container-lowest rounded-2xl p-4 text-center">
                    <p class="text-3xl font-extrabold font-headline text-on-surface">{{ nutritionists.length }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Total</p>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl p-4 text-center">
                    <p class="text-3xl font-extrabold font-headline text-primary">{{ activeCount }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Activos</p>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl p-4 text-center">
                    <p class="text-3xl font-extrabold font-headline text-secondary">{{ verifiedCount }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Verificados</p>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl p-4 text-center">
                    <p class="text-3xl font-extrabold font-headline text-tertiary">{{ totalPatientsCount }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Pacientes</p>
                </div>
            </div>

            <!-- Filtro y búsqueda -->
            <div class="bg-white rounded-2xl px-6 py-4 flex flex-wrap items-center gap-4 mb-4 shadow-sm">
                <div class="relative flex-1 min-w-[200px]">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Buscar por nombre, email o cédula..."
                        class="w-full bg-surface-container-low rounded-xl pl-9 pr-4 py-2 text-sm focus:ring-1 focus:ring-primary border-none outline-none"
                    />
                </div>
                <select
                    v-model="filterStatus"
                    class="bg-surface-container-low border-none rounded-lg text-sm font-medium focus:ring-1 focus:ring-primary py-2 px-3"
                >
                    <option value="">Todos los estados</option>
                    <option value="active">Activos</option>
                    <option value="inactive">Inactivos</option>
                </select>
                <button v-if="search || filterStatus" class="text-sm font-semibold text-primary hover:underline" @click="search=''; filterStatus=''">
                    Limpiar
                </button>
            </div>

            <!-- Tabla -->
            <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-surface-container-high">
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant">Nutriólogo</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant hidden md:table-cell">Cédula</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant hidden lg:table-cell">Especialidad</th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-on-surface-variant hidden sm:table-cell">Pacientes</th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-on-surface-variant">Estado</th>
                            <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-on-surface-variant hidden md:table-cell">Registro</th>
                            <th class="px-6 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container-high/50">
                        <tr v-if="!filteredNutritionists.length">
                            <td colspan="7" class="py-16 text-center text-on-surface-variant">
                                <span class="material-symbols-outlined text-5xl block mb-2 opacity-30">search_off</span>
                                Sin resultados
                            </td>
                        </tr>
                        <tr
                            v-for="nutri in filteredNutritionists"
                            :key="nutri.id"
                            class="hover:bg-surface-container-low/40 transition-colors"
                            :class="!nutri.is_active ? 'opacity-60' : ''"
                        >
                            <!-- Nutriólogo -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img
                                        :alt="nutri.name"
                                        :src="nutri.avatar ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(nutri.name)}&background=c9f0e3&color=004933&size=64`"
                                        class="w-10 h-10 rounded-2xl object-cover shrink-0"
                                    />
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="font-bold text-on-surface text-sm truncate">{{ nutri.name }}</p>
                                            <span v-if="nutri.is_verified" class="material-symbols-outlined text-primary text-sm" style="font-variation-settings:'FILL' 1" title="Verificado">verified</span>
                                        </div>
                                        <p class="text-xs text-on-surface-variant truncate">{{ nutri.email }}</p>
                                    </div>
                                </div>
                            </td>
                            <!-- Cédula -->
                            <td class="px-6 py-4 hidden md:table-cell">
                                <span class="text-sm text-on-surface-variant font-mono">{{ nutri.license }}</span>
                            </td>
                            <!-- Especialidad -->
                            <td class="px-6 py-4 hidden lg:table-cell">
                                <span class="text-sm text-on-surface">{{ nutri.specialization }}</span>
                            </td>
                            <!-- Pacientes -->
                            <td class="px-6 py-4 text-center hidden sm:table-cell">
                                <span class="text-sm font-bold">{{ nutri.totalPatients }}</span>
                            </td>
                            <!-- Estado -->
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="text-xs font-bold px-3 py-1 rounded-full"
                                    :class="nutri.is_active ? 'bg-secondary-container text-on-secondary-container' : 'bg-surface-container-high text-on-surface-variant'"
                                >{{ nutri.is_active ? 'Activo' : 'Inactivo' }}</span>
                            </td>
                            <!-- Fecha registro -->
                            <td class="px-6 py-4 text-right text-xs text-on-surface-variant hidden md:table-cell">{{ nutri.memberSince }}</td>
                            <!-- Acciones -->
                            <td class="px-6 py-4 text-right">
                                <div class="relative inline-block">
                                    <button
                                        class="p-2 text-outline hover:text-primary transition-colors rounded-lg hover:bg-surface-container-high"
                                        @click.stop="activeMenu = activeMenu === nutri.id ? null : nutri.id"
                                    >
                                        <span class="material-symbols-outlined">more_vert</span>
                                    </button>
                                    <div
                                        v-if="activeMenu === nutri.id"
                                        class="absolute right-0 mt-1 w-48 bg-surface rounded-2xl shadow-lg border border-outline-variant z-10 overflow-hidden"
                                    >
                                        <button
                                            class="flex w-full items-center gap-3 px-4 py-3 text-sm font-medium hover:bg-surface-container-high transition-colors"
                                            :class="nutri.is_active ? 'text-error' : 'text-primary'"
                                            @click="toggleStatus(nutri)"
                                        >
                                            <span class="material-symbols-outlined text-lg">{{ nutri.is_active ? 'person_off' : 'how_to_reg' }}</span>
                                            {{ nutri.is_active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t border-surface-container-high text-xs text-on-surface-variant">
                    Mostrando {{ filteredNutritionists.length }} de {{ nutritionists.length }} nutriólogos
                </div>
            </div>

        </main>

        <!-- Modal Añadir Nutriólogo -->
        <Teleport to="body">
            <div
                v-if="showModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
                @click.self="showModal = false"
            >
                <div class="bg-surface w-full max-w-lg rounded-3xl shadow-2xl p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-extrabold font-headline text-on-surface">Nuevo Nutriólogo</h2>
                        <button @click="showModal = false" class="text-outline hover:text-on-surface transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">Nombre completo *</label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                placeholder="Ej: Ana Pérez García"
                                class="w-full bg-surface-container-low rounded-xl px-4 py-3 text-sm border-none focus:ring-1 focus:ring-primary outline-none"
                            />
                            <p v-if="errors.name" class="text-xs text-error mt-1">{{ errors.name }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">Correo electrónico *</label>
                            <input
                                v-model="form.email"
                                type="email"
                                required
                                placeholder="nutriologa@clinica.com"
                                class="w-full bg-surface-container-low rounded-xl px-4 py-3 text-sm border-none focus:ring-1 focus:ring-primary outline-none"
                            />
                            <p v-if="errors.email" class="text-xs text-error mt-1">{{ errors.email }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">Cédula Profesional *</label>
                                <input
                                    v-model="form.license_number"
                                    type="text"
                                    required
                                    placeholder="12345678"
                                    class="w-full bg-surface-container-low rounded-xl px-4 py-3 text-sm border-none focus:ring-1 focus:ring-primary outline-none"
                                />
                                <p v-if="errors.license_number" class="text-xs text-error mt-1">{{ errors.license_number }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">Teléfono</label>
                                <input
                                    v-model="form.phone"
                                    type="tel"
                                    placeholder="+52 55 1234 5678"
                                    class="w-full bg-surface-container-low rounded-xl px-4 py-3 text-sm border-none focus:ring-1 focus:ring-primary outline-none"
                                />
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">Especialización</label>
                            <input
                                v-model="form.specialization"
                                type="text"
                                placeholder="Ej: Nutrición Deportiva"
                                class="w-full bg-surface-container-low rounded-xl px-4 py-3 text-sm border-none focus:ring-1 focus:ring-primary outline-none"
                            />
                        </div>
                        <div class="pt-2 flex gap-3">
                            <button
                                type="button"
                                class="flex-1 py-3 rounded-xl border border-outline-variant text-on-surface font-bold hover:bg-surface-container-low transition-colors"
                                @click="showModal = false"
                            >Cancelar</button>
                            <button
                                type="submit"
                                :disabled="loading"
                                class="flex-1 py-3 rounded-xl bg-primary text-on-primary font-bold hover:opacity-90 transition-opacity disabled:opacity-60"
                            >{{ loading ? 'Guardando...' : 'Registrar' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>
    </AppLayout>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    nutritionists: { type: Array, default: () => [] },
});

// Filtros
const search       = ref('');
const filterStatus = ref('');
const activeMenu   = ref(null);

function closeMenu() { activeMenu.value = null; }
onMounted(()  => document.addEventListener('click', closeMenu));
onUnmounted(() => document.removeEventListener('click', closeMenu));

const filteredNutritionists = computed(() => {
    return props.nutritionists.filter(n => {
        if (filterStatus.value === 'active'   && !n.is_active) return false;
        if (filterStatus.value === 'inactive' &&  n.is_active) return false;
        if (search.value) {
            const q = search.value.toLowerCase();
            return n.name.toLowerCase().includes(q)
                || n.email.toLowerCase().includes(q)
                || n.license.toLowerCase().includes(q);
        }
        return true;
    });
});

// Stats computadas
const activeCount       = computed(() => props.nutritionists.filter(n => n.is_active).length);
const verifiedCount     = computed(() => props.nutritionists.filter(n => n.is_verified).length);
const totalPatientsCount = computed(() => props.nutritionists.reduce((s, n) => s + n.totalPatients, 0));

// Toggle estado
function toggleStatus(nutri) {
    activeMenu.value = null;
    router.patch(route('admin.nutriologos.toggle', nutri.id), {}, {
        preserveScroll: true,
    });
}

// Modal nuevo nutriólogo
const showModal = ref(false);
const loading   = ref(false);
const errors    = ref({});
const form      = ref({ name: '', email: '', phone: '', license_number: '', specialization: '' });

function resetForm() {
    form.value   = { name: '', email: '', phone: '', license_number: '', specialization: '' };
    errors.value = {};
    loading.value = false;
}

function submitForm() {
    loading.value = true;
    errors.value  = {};
    router.post(route('admin.nutriologos.store'), form.value, {
        onSuccess: () => {
            showModal.value = false;
            resetForm();
        },
        onError: (errs) => {
            errors.value  = errs;
            loading.value = false;
        },
    });
}
</script>
