<template>
    <div class="bg-surface text-on-surface">
        <!-- Sidebar -->
        <aside class="bg-slate-50 h-screen w-64 fixed left-0 top-0 overflow-y-auto flex flex-col py-4 z-50">
            <div class="text-xl font-bold text-emerald-900 px-6 py-8 font-headline">
                Dr. Nutrición
                <div class="text-xs font-normal text-on-surface-variant mt-1">Especialista Clínico</div>
            </div>
            <nav class="flex-1 px-4 space-y-2">
                <Link
                    v-for="item in navItems"
                    :key="item.href"
                    :href="item.href"
                    :class="[
                        'flex items-center gap-3 px-4 py-3 transition-colors duration-200',
                        isActive(item.href)
                            ? 'text-emerald-700 font-bold bg-emerald-50 rounded-r-full'
                            : 'text-slate-500 hover:text-emerald-600 hover:bg-emerald-50/50',
                    ]"
                >
                    <span class="material-symbols-outlined">{{ item.icon }}</span>
                    <span class="font-headline text-sm">{{ item.label }}</span>
                </Link>
            </nav>
            <div class="mt-auto px-6 py-4 flex items-center gap-3">
                <img
                    alt="Imagen de perfil del nutriólogo"
                    class="w-10 h-10 rounded-full bg-surface-container-high object-cover"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuCs8i-EfMRlbNn4TqGAZobJDtwC3-A-GT5UR1m8D3ebJnKUrSaiUM5O7MK5bBVUWOQQnF0jFTmXyEjtSlKvwcifcUGAEMXeM6POEkj4mxXfFw7DBhGq5ZzAU_8jOJZP1ZxgmbaCexbgzm-5fsFIsonwmMsF_p9YAqQ1QW7AEMPqeynhLWQpJ6LYG9OllJsGoLwVGayHKahMRtIOVtrHLYJdXfB-lYLmVQii2q-VIU4xvN0dqII_AAzydeFPlIVixxptLYgc8eW1Ul9y"
                />
                <div>
                    <p class="text-sm font-bold font-headline">Dr. Smith</p>
                    <p class="text-xs text-on-surface-variant">Clínica Norte</p>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="ml-64 min-h-screen">
            <!-- Top Header -->
            <header class="sticky top-0 z-40 w-full bg-white/80 backdrop-blur-md flex justify-between items-center px-8 py-3 border-b border-slate-100 shadow-sm">
                <div class="flex items-center bg-surface-container-high rounded-full px-4 py-2 w-96 gap-2">
                    <span class="material-symbols-outlined text-on-surface-variant" style="font-size:18px">search</span>
                    <input
                        class="bg-transparent border-none focus:ring-0 text-sm w-full placeholder:text-on-surface-variant outline-none"
                        placeholder="Buscar pacientes, planes o citas..."
                        type="text"
                    />
                </div>
                <div class="flex items-center gap-6">
                    <button class="text-slate-600 hover:text-emerald-600 transition-colors opacity-80 hover:opacity-100">
                        <span class="material-symbols-outlined">notifications</span>
                    </button>
                    <button class="text-slate-600 hover:text-emerald-600 transition-colors opacity-80 hover:opacity-100">
                        <span class="material-symbols-outlined">help</span>
                    </button>
                    <button class="bg-primary text-on-primary px-6 py-2 rounded-xl font-bold text-sm hover:opacity-90 transition-opacity">
                        Perfil
                    </button>
                </div>
            </header>

            <!-- Page Slot -->
            <slot />
        </main>

        <!-- FAB -->
        <button class="fixed bottom-10 right-10 w-16 h-16 bg-primary text-on-primary rounded-full flex items-center justify-center shadow-xl hover:scale-105 active:scale-95 transition-transform z-50">
            <span class="material-symbols-outlined text-3xl">add</span>
        </button>
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentUrl = computed(() => page.url);

const navItems = [
    { href: '/',           icon: 'dashboard',       label: 'Dashboard' },
    { href: '/pacientes',  icon: 'group',           label: 'Pacientes' },
    { href: '/calendario', icon: 'calendar_today',  label: 'Calendario' },
    { href: '/planes',     icon: 'restaurant_menu', label: 'Planes Alimenticios' },
    { href: '/config',     icon: 'settings',        label: 'Configuración' },
];

function isActive(href) {
    if (href === '/') return currentUrl.value === '/';
    return currentUrl.value.startsWith(href);
}
</script>
