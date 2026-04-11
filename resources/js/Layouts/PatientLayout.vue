<template>
    <div class="min-h-screen bg-surface text-on-surface">
        <!-- Sidebar -->
        <aside class="h-screen w-64 fixed left-0 top-0 bg-slate-50 flex-col py-6 border-r border-surface-variant hidden md:flex z-40">
            <div class="px-6 mb-8">
                <div class="flex items-center gap-3 mb-6">
                    <img
                        :src="user.avatar || 'https://via.placeholder.com/48'"
                        :alt="user.full_name"
                        class="w-12 h-12 rounded-full object-cover ring-2 ring-primary-container"
                    />
                    <div>
                        <p class="font-headline font-bold text-slate-800 dark:text-slate-100 text-sm">Hola, {{ user.first_name }}</p>
                        <p class="text-[10px] text-on-surface-variant uppercase tracking-wider font-semibold">Plan Activo</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 space-y-1">
                <Link
                    href="/paciente/inicio"
                    :class="[
                        'flex items-center gap-3 px-4 py-3 mx-2 rounded-xl transition-all',
                        route().current('patient.dashboard')
                            ? 'bg-emerald-100 text-emerald-800'
                            : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-700'
                    ]"
                >
                    <span class="material-symbols-outlined">home</span>
                    <span class="font-medium text-sm">Inicio</span>
                </Link>

                <Link
                    href="/paciente/plan"
                    :class="[
                        'flex items-center gap-3 px-4 py-3 mx-2 rounded-xl transition-all',
                        route().current('patient.plan')
                            ? 'bg-emerald-100 text-emerald-800'
                            : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-700'
                    ]"
                >
                    <span class="material-symbols-outlined">description</span>
                    <span class="font-medium text-sm">Mi Plan</span>
                </Link>

                <Link
                    href="/paciente/progreso"
                    :class="[
                        'flex items-center gap-3 px-4 py-3 mx-2 rounded-xl transition-all',
                        route().current('patient.progress')
                            ? 'bg-emerald-100 text-emerald-800'
                            : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-700'
                    ]"
                >
                    <span class="material-symbols-outlined">insights</span>
                    <span class="font-medium text-sm">Progreso</span>
                </Link>

                <Link
                    href="/paciente/citas"
                    :class="[
                        'flex items-center gap-3 px-4 py-3 mx-2 rounded-xl transition-all',
                        route().current('patient.appointments')
                            ? 'bg-emerald-100 text-emerald-800'
                            : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-700'
                    ]"
                >
                    <span class="material-symbols-outlined">calendar_today</span>
                    <span class="font-medium text-sm">Citas</span>
                </Link>

                <Link
                    href="/paciente/comunidad"
                    :class="[
                        'flex items-center gap-3 px-4 py-3 mx-2 rounded-xl transition-all',
                        route().current('patient.community')
                            ? 'bg-emerald-100 text-emerald-800'
                            : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-700'
                    ]"
                >
                    <span class="material-symbols-outlined">groups</span>
                    <span class="font-medium text-sm">Comunidad</span>
                </Link>
            </nav>

            <div class="px-4 mt-auto space-y-4">
                <button class="w-full bg-primary text-on-primary py-3 rounded-xl font-bold text-sm shadow-sm hover:scale-[0.98] transition-transform">
                    Agendar Cita
                </button>
                <div class="pt-4 border-t border-surface-variant">
                    <a href="#" class="flex items-center gap-3 text-slate-600 px-4 py-2 text-sm hover:text-primary transition-colors">
                        <span class="material-symbols-outlined">help</span>
                        <span>Ayuda</span>
                    </a>
                    <Link href="/logout" method="post" as="button" class="w-full flex items-center gap-3 text-slate-600 px-4 py-2 text-sm hover:text-error transition-colors text-left">
                        <span class="material-symbols-outlined">logout</span>
                        <span>Cerrar Sesión</span>
                    </Link>
                </div>
            </div>
        </aside>

        <!-- TopAppBar -->
        <header class="sticky top-0 w-full flex justify-between items-center px-4 md:px-6 py-3 bg-white/80 backdrop-blur-md z-50 md:pl-72 shadow-sm shadow-slate-200/50 border-b border-surface-variant">
            <div class="flex items-center gap-4">
                <span class="text-[32px] leading-none font-extrabold text-emerald-700 tracking-tight font-headline">Clinical Sanctuary</span>
            </div>
            <div class="flex items-center gap-2 md:gap-4">
                <div class="relative hidden sm:block">
                    <input
                        class="bg-surface-container-high border-none rounded-full py-2 pl-10 pr-4 text-sm focus:ring-2 focus:ring-primary/20 w-44 md:w-64 transition-all"
                        placeholder="Buscar..."
                        type="text"
                    />
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
                </div>
                <button class="p-2 text-slate-500 hover:text-emerald-600 transition-colors">
                    <span class="material-symbols-outlined">notifications</span>
                </button>
                <button class="p-2 text-slate-500 hover:text-emerald-600 transition-colors">
                    <span class="material-symbols-outlined">settings</span>
                </button>
                <div class="w-8 h-8 rounded-full overflow-hidden border border-emerald-100">
                    <img
                        :src="user.avatar || 'https://via.placeholder.com/32'"
                        :alt="user.full_name"
                        class="w-full h-full object-cover"
                    />
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="md:ml-64 p-6 lg:p-10 pb-28 md:pb-10">
            <div class="max-w-[1260px] mx-auto">
                <slot />
            </div>
        </main>

        <!-- Bottom Nav Mobile -->
        <nav class="md:hidden fixed bottom-0 left-0 w-full bg-white/90 backdrop-blur-md flex justify-around items-center py-4 px-2 z-50 shadow-[0_-4px_20px_rgba(0,0,0,0.05)] border-t border-surface-variant">
            <Link
                href="/paciente/inicio"
                :class="[
                    'flex flex-col items-center gap-1 text-center',
                    route().current('patient.dashboard')
                        ? 'text-primary'
                        : 'text-on-surface-variant'
                ]"
            >
                <span class="material-symbols-outlined" :style="{ fontVariationSettings: route().current('patient.dashboard') ? `'FILL' 1` : `'FILL' 0` }">home</span>
                <span class="text-[10px] font-bold">Inicio</span>
            </Link>
            <Link
                href="/paciente/plan"
                :class="[
                    'flex flex-col items-center gap-1 text-center',
                    route().current('patient.plan')
                        ? 'text-primary'
                        : 'text-on-surface-variant'
                ]"
            >
                <span class="material-symbols-outlined">description</span>
                <span class="text-[10px] font-bold">Plan</span>
            </Link>
            <Link
                href="/paciente/progreso"
                :class="[
                    'flex flex-col items-center gap-1 text-center',
                    route().current('patient.progress')
                        ? 'text-primary'
                        : 'text-on-surface-variant'
                ]"
            >
                <span class="material-symbols-outlined">insights</span>
                <span class="text-[10px] font-bold">Progreso</span>
            </Link>
            <Link
                href="/paciente/citas"
                :class="[
                    'flex flex-col items-center gap-1 text-center',
                    route().current('patient.appointments')
                        ? 'text-primary'
                        : 'text-on-surface-variant'
                ]"
            >
                <span class="material-symbols-outlined">calendar_today</span>
                <span class="text-[10px] font-bold">Citas</span>
            </Link>
            <Link
                href="/paciente/comunidad"
                :class="[
                    'flex flex-col items-center gap-1 text-center',
                    route().current('patient.community')
                        ? 'text-primary'
                        : 'text-on-surface-variant'
                ]"
            >
                <span class="material-symbols-outlined">groups</span>
                <span class="text-[10px] font-bold">Comunidad</span>
            </Link>
        </nav>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const user = page.props.auth.user;
</script>
