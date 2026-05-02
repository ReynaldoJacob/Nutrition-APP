<template>
    <div class="bg-surface text-on-surface">
        <!-- Sidebar -->
        <aside class="bg-surface-container-lowest h-screen w-64 fixed left-0 top-0 overflow-y-auto flex flex-col py-4 z-50 border-r border-outline-variant/20">
            <div class="px-6 py-6">
                <div
                    v-if="authUser?.role_key !== 'admin' && authUser?.clinic_logo_url"
                    class="mb-5 rounded-3xl border border-outline-variant/20 bg-surface-container-high p-4 flex items-center justify-center min-h-[112px]"
                >
                    <img
                        :src="authUser.clinic_logo_url"
                        alt="Logo de la clínica"
                        class="max-h-20 max-w-full object-contain"
                    />
                </div>
                <div class="text-xl font-bold text-on-primary-container font-headline text-center">
                    {{ authUser?.role_key === 'admin' ? 'Panel Admin' : 'Dr. Nutrición' }}
                    <div class="text-xs font-normal text-on-surface-variant mt-1">
                        {{ authUser?.role_key === 'admin' ? 'Administrador' : 'Especialista Clínico' }}
                    </div>
                </div>
            </div>
            <nav class="flex-1 px-4 space-y-2">
                <template v-for="item in navItems" :key="item.href || item.label">
                    <!-- Regular Menu Item -->
                    <Link
                        v-if="!item.submenu"
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

                    <!-- Menu Item with Submenu -->
                    <div v-else class="space-y-1">
                        <button
                            @click="toggleSubmenu(item.label)"
                            :class="[
                                'w-full flex items-center justify-between px-4 py-3 transition-colors duration-200',
                                isSubmenuActive(item)
                                    ? 'text-primary font-bold bg-primary/10 rounded-r-full'
                                    : 'text-slate-500 hover:text-primary hover:bg-primary/5',
                            ]"
                        >
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined">{{ item.icon }}</span>
                                <span class="font-headline text-sm">{{ item.label }}</span>
                            </div>
                            <span
                                class="material-symbols-outlined text-sm transition-transform duration-200"
                                :style="{ transform: expandedMenus.has(item.label) ? 'rotate(180deg)' : 'rotate(0deg)' }"
                            >
                                keyboard_arrow_down
                            </span>
                        </button>
                        <div v-if="expandedMenus.has(item.label)" class="pl-10 space-y-1">
                            <Link
                                v-for="subitem in item.submenu"
                                :key="subitem.href"
                                :href="subitem.href"
                                :class="[
                                    'flex items-center px-4 py-2 rounded-lg text-sm font-headline transition-colors duration-200',
                                    isActive(subitem.href)
                                        ? 'text-primary font-bold bg-primary/10 border-l-2 border-primary'
                                        : 'text-slate-500 hover:text-primary hover:bg-primary/5 border-l-2 border-transparent',
                                ]"
                            >
                                <span>{{ subitem.label }}</span>
                            </Link>
                        </div>
                    </div>
                </template>
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
                        <p v-if="authUser?.role_key === 'admin'" class="text-xs text-on-surface-variant">Administrador</p>
                        <span
                            v-else
                            :class="planBadgeClasses"
                        >
                            {{ subscriptionPlanLabel }}
                        </span>
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
                    <div class="relative" @click.stop>
                        <button
                            class="w-10 h-10 rounded-full bg-surface-container-high text-on-surface-variant hover:text-primary hover:bg-primary/10 transition-colors flex items-center justify-center relative"
                            @click="toggleNotifications"
                        >
                            <span class="material-symbols-outlined">notifications</span>
                            <span
                                v-if="unreadCount > 0"
                                class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-error rounded-full ring-2 ring-surface-container-lowest"
                            ></span>
                        </button>

                        <div
                            v-if="showNotifications"
                            class="absolute right-0 mt-2 w-[360px] max-w-[90vw] bg-surface-container-lowest border border-outline-variant/30 rounded-2xl shadow-xl overflow-hidden z-50"
                        >
                            <div class="px-4 py-3 border-b border-outline-variant/20 flex items-center justify-between">
                                <h3 class="text-sm font-bold text-on-surface">Notificaciones nuevas</h3>
                                <span v-if="unreadCount > 0" class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-primary/15 text-primary">{{ unreadCount }}</span>
                            </div>

                            <div v-if="notificationItems.length === 0" class="p-5 text-sm text-on-surface-variant text-center">
                                No hay notificaciones.
                            </div>

                            <ul v-else class="max-h-96 overflow-y-auto p-3 space-y-2">
                                <li
                                    v-for="item in notificationItems"
                                    :key="item.id"
                                    class="p-3 rounded-xl bg-surface-container-low"
                                >
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[10px] font-bold uppercase px-2 py-1 rounded-full bg-primary/15 text-primary">{{ categoryLabel(item.category) }}</span>
                                        <span class="text-[11px] text-on-surface-variant">{{ item.sentAt }}</span>
                                    </div>
                                    <p class="text-sm font-bold text-on-surface">{{ item.title }}</p>
                                    <p class="text-xs text-on-surface-variant mt-1 whitespace-pre-line">{{ item.message }}</p>
                                    <p class="text-[10px] text-on-surface-variant mt-2">Enviado por {{ item.admin }}</p>
                                </li>
                            </ul>
                        </div>
                    </div>
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
                <div v-if="flashSuccess || flashWarning" class="px-8 pt-6 space-y-3">
                    <div
                        v-if="flashSuccess"
                        class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800"
                    >
                        {{ flashSuccess }}
                    </div>
                    <div
                        v-if="flashWarning"
                        class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800"
                    >
                        {{ flashWarning }}
                    </div>
                </div>
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
import { computed, onMounted, onUnmounted, ref, watchEffect } from 'vue';

const page = usePage();
const currentUrl = computed(() => page.url);
const authUser = computed(() => page.props.auth?.user);
const effectivePlanKey = computed(() => authUser.value?.subscription_effective_plan ?? 'free');
const flashSuccess = computed(() => page.props.flash?.success ?? null);
const flashWarning = computed(() => page.props.flash?.warning ?? null);
const subscriptionPlanLabel = computed(() => {
    const key = effectivePlanKey.value;
    const labels = {
        free: 'Free',
        normal: 'Normal',
        pro: 'PRO',
    };
    return labels[key] ?? 'Free';
});
const isProPlan = computed(() => effectivePlanKey.value === 'pro');
const planBadgeClasses = computed(() => {
    if (isProPlan.value) {
        return 'inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-semibold tracking-[0.08em] bg-amber-50/90 text-amber-700 border border-amber-200/80 shadow-[inset_0_1px_0_rgba(255,255,255,0.6)]';
    }

    return 'inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-semibold tracking-[0.08em] bg-surface-container-low/90 text-on-surface-variant border border-outline-variant/35';
});
const isDark = ref(document.documentElement.classList.contains('dark'));
const showNotifications = ref(false);
const notificationItems = ref([]);
const unreadCount = ref(0);
const expandedMenus = ref(new Set()); // Los menús se expanden al hacer clic
let notificationsChannel = null;
let notificationAudioContext = null;

watchEffect(() => {
    const shared = page.props.notifications ?? { items: [], unread_count: 0 };
    notificationItems.value = shared.items ?? [];
    unreadCount.value = shared.unread_count ?? 0;
});

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

function categoryLabel(category) {
    const labels = {
        update: 'Actualización',
        maintenance: 'Mantenimiento',
        policy: 'Política',
        training: 'Capacitación',
        alert: 'Alerta',
        reminder: 'Recordatorio',
    };
    return labels[category] ?? category;
}

function closeNotifications() {
    showNotifications.value = false;
}

function toggleNotifications() {
    showNotifications.value = !showNotifications.value;

    // Desbloquea contexto de audio tras interacción del usuario.
    if (notificationAudioContext && notificationAudioContext.state === 'suspended') {
        notificationAudioContext.resume().catch(() => {
            // noop
        });
    }

    if (showNotifications.value && unreadCount.value > 0) {
        window.axios.patch(route('notifications.seen')).then(() => {
            unreadCount.value = 0;
        }).catch(() => {
            // noop
        });
    }
}

function playNotificationSound() {
    try {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (!AudioCtx) return;

        if (!notificationAudioContext) {
            notificationAudioContext = new AudioCtx();
        }

        const now = notificationAudioContext.currentTime;
        const osc = notificationAudioContext.createOscillator();
        const gain = notificationAudioContext.createGain();

        osc.type = 'sine';
        osc.frequency.setValueAtTime(950, now);
        osc.frequency.exponentialRampToValueAtTime(760, now + 0.14);

        gain.gain.setValueAtTime(0.0001, now);
        gain.gain.exponentialRampToValueAtTime(0.045, now + 0.015);
        gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.16);

        osc.connect(gain);
        gain.connect(notificationAudioContext.destination);
        osc.start(now);
        osc.stop(now + 0.17);
    } catch (e) {
        // noop
    }
}

function handleLogout() {
    localStorage.removeItem('theme-mode');
    document.documentElement.classList.remove('dark');
    document.body.classList.remove('theme-blue', 'theme-purple', 'theme-rose', 'theme-amber');
    router.post(route('logout'));
}

onMounted(() => {
    document.addEventListener('click', closeNotifications);

    if (window.Echo) {
        notificationsChannel = window.Echo.channel('notifications');
        notificationsChannel.listen('.notification.sent', (data) => {
            const myUserId = authUser.value?.id;
            const isForMe = !data.target_user_id || Number(data.target_user_id) === Number(myUserId);
            if (!isForMe) return;

            notificationItems.value.unshift({
                id: `rt-${Date.now()}`,
                category: data.category ?? 'update',
                title: data.title ?? 'Notificación en vivo',
                message: data.message ?? 'Tienes una nueva notificación.',
                admin: data.admin_name ?? 'Administración',
                sentAt: new Date().toLocaleTimeString(),
            });

            notificationItems.value = notificationItems.value.slice(0, 20);
            unreadCount.value += 1;
            playNotificationSound();
        });
    }
});

onUnmounted(() => {
    document.removeEventListener('click', closeNotifications);
    if (notificationsChannel && window.Echo) {
        window.Echo.leave('notifications');
    }
});

const navItems = computed(() => {
    if (authUser.value?.role_key === 'admin') {
        return [
            { href: '/admin',             icon: 'admin_panel_settings', label: 'Panel Admin' },
            { href: '/admin/nutriologos', icon: 'medical_services',     label: 'Nutriólogos' },
            { href: '/admin/avisos',      icon: 'campaign',             label: 'Avisos' },
        ];
    }
    return [
        { href: '/',                    icon: 'dashboard',       label: 'Dashboard' },
        { href: '/pacientes',           icon: 'group',           label: 'Pacientes' },
        { href: '/calendario',          icon: 'calendar_today',  label: 'Calendario' },
        {
            icon: 'menu_book',
            label: 'Biblioteca',
            submenu: [
                { href: '/biblioteca/recipes', label: 'Recetario' },
                { href: '/biblioteca/ingredients', label: 'Mis Ingredientes' },
            ],
        },
        ...(isProPlan.value ? [{ href: '/gestor-de-contenido', icon: 'post_add', label: 'Gestor de Contenido' }] : []),
        { href: '/config',              icon: 'settings',        label: 'Configuración' },
    ];
});

function isSubmenuActive(item) {
    return item.submenu?.some(sub => isActive(sub.href));
}

function toggleSubmenu(label) {
    if (expandedMenus.value.has(label)) {
        expandedMenus.value.delete(label);
    } else {
        expandedMenus.value.add(label);
    }
}

function isActive(href) {
    if (href === '/') return currentUrl.value === '/';
    return currentUrl.value.startsWith(href);
}
</script>
