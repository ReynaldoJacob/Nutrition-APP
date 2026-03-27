<template>
    <div class="bg-surface text-on-surface">
        <!-- Sidebar -->
        <aside class="bg-surface-container-lowest h-screen w-64 fixed left-0 top-0 overflow-y-auto flex flex-col py-4 z-50 border-r border-outline-variant/20">
            <div class="text-xl font-bold text-on-primary-container px-6 py-8 font-headline">
                {{ authUser?.role_key === 'admin' ? 'Panel Admin' : 'Dr. Nutrición' }}
                <div class="text-xs font-normal text-on-surface-variant mt-1">
                    {{ authUser?.role_key === 'admin' ? 'Administrador' : 'Especialista Clínico' }}
                </div>
            </div>
            <nav class="flex-1 px-4 space-y-2">
                <Link
                    v-for="item in navItems"
                    :key="item.href"
                    :href="item.href"
                    :class="[
                        'flex items-center gap-3 px-4 py-3 transition-colors duration-200',
                        isActive(item.href)
                            ? 'text-primary font-bold bg-primary/10 rounded-r-full'
                            : 'text-slate-500 hover:text-primary hover:bg-primary/5',
                    ]"
                >
                    <span class="material-symbols-outlined">{{ item.icon }}</span>
                    <span class="font-headline text-sm">{{ item.label }}</span>
                </Link>
            </nav>
            <div class="mt-auto px-6 py-4 flex flex-col gap-3">
                <div class="flex items-center gap-3">
                    <img
                        alt="Imagen de perfil del nutriólogo"
                        class="w-10 h-10 rounded-full bg-surface-container-high object-cover"
                        :src="authUser?.avatar ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuCs8i-EfMRlbNn4TqGAZobJDtwC3-A-GT5UR1m8D3ebJnKUrSaiUM5O7MK5bBVUWOQQnF0jFTmXyEjtSlKvwcifcUGAEMXeM6POEkj4mxXfFw7DBhGq5ZzAU_8jOJZP1ZxgmbaCexbgzm-5fsFIsonwmMsF_p9YAqQ1QW7AEMPqeynhLWQpJ6LYG9OllJsGoLwVGayHKahMRtIOVtrHLYJdXfB-lYLmVQii2q-VIU4xvN0dqII_AAzydeFPlIVixxptLYgc8eW1Ul9y'"
                    />
                    <div>
                        <p class="text-sm font-bold font-headline">{{ authUser?.full_name }}</p>
                        <p class="text-xs text-on-surface-variant">Clínica Norte</p>
                    </div>
                </div>
                <button
                    @click="handleLogout"
                    class="flex items-center gap-3 px-2 py-2 text-slate-500 hover:text-error transition-colors duration-200 group w-full"
                >
                    <span class="material-symbols-outlined text-xl">logout</span>
                    <span class="font-headline text-sm font-semibold">Cerrar Sesión</span>
                </button>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="ml-64 h-screen flex flex-col">
            <!-- Top Header -->
            <header class="shrink-0 z-40 w-full bg-surface-container-lowest/80 backdrop-blur-md flex justify-between items-center px-8 py-3 border-b border-outline-variant/20 shadow-sm">
                <div class="flex items-center bg-surface-container-high rounded-full px-4 py-2 w-96 gap-2">
                    <span class="material-symbols-outlined text-on-surface-variant" style="font-size:18px">search</span>
                    <input
                        class="bg-transparent border-none focus:ring-0 text-sm w-full placeholder:text-on-surface-variant outline-none"
                        placeholder="Buscar pacientes, planes o citas..."
                        type="text"
                    />
                </div>
                <div class="flex items-center gap-4">
                    <button
                        :title="isDark ? 'Activar modo claro' : 'Activar modo oscuro'"
                        class="w-10 h-10 rounded-full bg-surface-container-high text-on-surface-variant hover:text-primary hover:bg-primary/10 transition-colors flex items-center justify-center"
                        @click="toggleDarkMode"
                    >
                        <span class="material-symbols-outlined text-[20px]">{{ isDark ? 'light_mode' : 'dark_mode' }}</span>
                    </button>
                    <button class="text-on-surface-variant hover:text-primary transition-colors opacity-80 hover:opacity-100">
                        <span class="material-symbols-outlined">notifications</span>
                    </button>
                    <button class="text-on-surface-variant hover:text-primary transition-colors opacity-80 hover:opacity-100">
                        <span class="material-symbols-outlined">help</span>
                    </button>
                    <button class="bg-primary text-on-primary px-6 py-2 rounded-xl font-bold text-sm hover:opacity-90 transition-opacity">
                        Perfil
                    </button>
                </div>
            </header>

            <!-- Page Slot -->
            <div class="flex-1 overflow-y-auto flex flex-col">
                <slot />
            </div>
        </main>

        <!-- FAB -->
        <button class="fixed bottom-10 right-10 w-16 h-16 bg-primary text-on-primary rounded-full flex items-center justify-center shadow-xl hover:scale-105 active:scale-95 transition-transform z-50">
            <span class="material-symbols-outlined text-3xl">add</span>
        </button>
    </div>
</template>

<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watchEffect } from 'vue';

const page = usePage();
const currentUrl = computed(() => page.url);
const authUser = computed(() => page.props.auth?.user);
const isDark = ref(document.documentElement.classList.contains('dark'));

// Aplicar tema de color dinámicamente al cambiar el usuario o su tema
watchEffect(() => {
    const theme = authUser.value?.theme_color ?? 'emerald';
    const body = document.body;
    // Quitar clases de tema previas y aplicar la nueva
    body.classList.remove('theme-blue', 'theme-purple', 'theme-rose', 'theme-amber');
    if (theme !== 'emerald') {
        body.classList.add(`theme-${theme}`);
    }
});

function toggleDarkMode() {
    isDark.value = !isDark.value;
    document.documentElement.classList.toggle('dark', isDark.value);
    localStorage.setItem('theme-mode', isDark.value ? 'dark' : 'light');
}

function handleLogout() {
    localStorage.removeItem('theme-mode');
    document.documentElement.classList.remove('dark');
    document.body.classList.remove('theme-blue', 'theme-purple', 'theme-rose', 'theme-amber');
    router.post(route('logout'));
}

const navItems = computed(() => {
    if (authUser.value?.role_key === 'admin') {
        return [
            { href: '/admin',             icon: 'admin_panel_settings', label: 'Panel Admin' },
            { href: '/admin/nutriologos', icon: 'medical_services',     label: 'Nutriólogos' },
        ];
    }
    return [
        { href: '/',           icon: 'dashboard',       label: 'Dashboard' },
        { href: '/pacientes',  icon: 'group',           label: 'Pacientes' },
        { href: '/calendario', icon: 'calendar_today',  label: 'Calendario' },
        { href: '/planes',     icon: 'restaurant_menu', label: 'Planes Alimenticios' },
        { href: '/config',     icon: 'settings',        label: 'Configuración' },
    ];
});

function isActive(href) {
    if (href === '/') return currentUrl.value === '/';
    return currentUrl.value.startsWith(href);
}
</script>
