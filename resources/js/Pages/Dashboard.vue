<template>
    <AppLayout>
        <section class="p-10 space-y-10">

            <!-- Hero Header -->
            <div class="flex justify-between items-end">
                <div class="space-y-2">
                    <h1 class="text-4xl font-extrabold font-headline tracking-tight text-on-surface">Bienvenido, {{ authUser?.first_name }}</h1>
                    <p class="text-on-surface-variant font-body">Tienes {{ todayAppointments.length }} citas pendientes para el día de hoy.</p>
                </div>
                <div class="flex items-center gap-4">
                    <button
                        @click="showModal = true"
                        class="flex items-center gap-2 bg-primary text-on-primary px-6 py-3 rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:opacity-90 active:scale-95 transition-all"
                    >
                        <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">add</span>
                        Nueva Cita
                    </button>
                    <div class="bg-primary-container px-6 py-4 rounded-xl flex items-center gap-4">
                        <div class="bg-primary p-2 rounded-lg">
                            <span class="material-symbols-outlined text-white">calendar_month</span>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-on-primary-container uppercase tracking-wider">Hoy es</p>
                            <p class="text-on-primary-container font-headline font-bold">{{ today }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Metrics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-surface-container-lowest p-6 rounded-xl border-l-4 border-primary">
                    <p class="text-on-surface-variant text-sm font-semibold">Total Pacientes</p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-3xl font-extrabold font-headline">{{ totalPatients }}</span>
                        <span class="text-primary text-xs font-bold bg-primary-container px-2 py-1 rounded-full">+12%</span>
                    </div>
                </div>
                <div class="bg-surface-container-lowest p-6 rounded-xl">
                    <p class="text-on-surface-variant text-sm font-semibold">Citas Semanales</p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-3xl font-extrabold font-headline">  {{ appointmentsPerWeekend }}</span>
                        <span class="material-symbols-outlined text-secondary">trending_up</span>
                    </div>
                </div>
                <div class="bg-surface-container-lowest p-6 rounded-xl">
                    <p class="text-on-surface-variant text-sm font-semibold">Planes Activos</p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-3xl font-extrabold font-headline">89</span>
                        <span class="material-symbols-outlined text-primary">assignment</span>
                    </div>
                </div>
                <div class="bg-surface-container-lowest p-6 rounded-xl">
                    <p class="text-on-surface-variant text-sm font-semibold">Satisfacción</p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-3xl font-extrabold font-headline">98%</span>
                        <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">favorite</span>
                    </div>
                </div>
            </div>

            <!-- Appointments & Tasks -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                <!-- Appointments Column -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold font-headline">Citas del Día</h2>
                        <button class="text-primary font-bold text-sm hover:underline">Ver calendario completo</button>
                    </div>
                    <div class="space-y-4">
                        <!-- Estado vacío -->
                        <div
                            v-if="todayAppointments.length === 0"
                            class="text-center py-12 text-on-surface-variant"
                        >
                            <span class="material-symbols-outlined text-4xl mb-2 block">event_available</span>
                            <p class="text-sm font-medium">No tienes citas programadas para hoy.</p>
                        </div>

                        <!-- Card dinámica -->
                        <div
                            v-for="appt in todayAppointments"
                            :key="appt.id"
                            class="bg-surface-container-lowest p-5 rounded-xl flex items-center justify-between hover:bg-surface-container-low transition-colors group"
                        >
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 bg-secondary-container rounded-full overflow-hidden shrink-0">
                                    <img
                                        :alt="appt.patientName"
                                        :src="appt.patientAvatar ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(appt.patientName)}&background=7af9c7&color=004933`"
                                        class="w-full h-full object-cover"
                                    />
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg font-headline">{{ appt.patientName }}</h3>
                                    <p class="text-on-surface-variant text-sm">{{ appt.type }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="px-3 py-1 bg-surface-container-high rounded-lg">
                                    <p class="text-sm font-bold text-on-surface">{{ appt.time }}</p>
                                </div>
                                <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary transition-colors">arrow_forward_ios</span>
                            </div>
                        </div>
                    </div>

                    <!-- Realtime Notifications -->
                    <div class="bg-surface-container-lowest p-6 rounded-xl">
                        <h2 class="text-lg font-bold font-headline mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">notifications_active</span>
                            Notificaciones en vivo
                            <span class="ml-1 text-xs text-on-surface-variant font-normal">(canal: notifications)</span>
                        </h2>
                        <div v-if="messages.length === 0" class="text-on-surface-variant text-sm text-center py-6">
                            No hay notificaciones todavía.
                        </div>
                        <ul v-else class="space-y-3 max-h-[420px] overflow-y-auto pr-1">
                            <li
                                v-for="(msg, i) in messages"
                                :key="i"
                                class="p-4 bg-surface-container-low rounded-xl"
                            >
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-bold uppercase px-2 py-1 rounded-full bg-primary/15 text-primary">{{ categoryLabel(msg.category) }}</span>
                                    <span class="text-[11px] text-on-surface-variant">{{ msg.time }}</span>
                                </div>
                                <p class="text-sm font-bold text-on-surface">{{ msg.title }}</p>
                                <p class="text-xs text-on-surface-variant mt-1 whitespace-pre-line">{{ msg.message }}</p>
                                <p class="text-[10px] text-on-surface-variant mt-2">Enviado por {{ msg.admin }}</p>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Tasks & Health Pulse Column -->
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold font-headline">Pendientes</h2>
                    <div class="bg-surface-container-low p-6 rounded-2xl space-y-5">
                        <div class="flex items-start gap-4">
                            <input class="rounded text-primary focus:ring-primary h-5 w-5 bg-surface-container-lowest border-outline mt-1 shrink-0" type="checkbox"/>
                            <div>
                                <p class="font-semibold text-sm">Revisar resultados Lucía M.</p>
                                <p class="text-xs text-on-surface-variant mt-1">Antes de la cita de las 09:00</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <input class="rounded text-primary focus:ring-primary h-5 w-5 bg-surface-container-lowest border-outline mt-1 shrink-0" type="checkbox"/>
                            <div>
                                <p class="font-semibold text-sm">Actualizar plan deportivo Carlos</p>
                                <p class="text-xs text-on-surface-variant mt-1">Incluir suplementación nueva</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <input class="rounded text-primary focus:ring-primary h-5 w-5 bg-surface-container-lowest border-outline mt-1 shrink-0" type="checkbox"/>
                            <div>
                                <p class="font-semibold text-sm">Llamar a farmacia proveedora</p>
                                <p class="text-xs text-on-surface-variant mt-1">Consultar stock de probióticos</p>
                            </div>
                        </div>
                        <button class="w-full py-3 bg-surface-container-highest rounded-xl text-on-surface font-bold text-sm flex items-center justify-center gap-2 hover:bg-slate-300 transition-colors mt-4">
                            <span class="material-symbols-outlined text-sm">add</span>
                            Nueva Tarea
                        </button>
                    </div>

                    <!-- Weekly Goal -->
                    <div class="bg-primary p-6 rounded-2xl text-on-primary space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-sm opacity-80 uppercase tracking-widest">Objetivo Semanal</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">bolt</span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-xl font-extrabold font-headline">
                                <span>28 / 40</span>
                                <span>70%</span>
                            </div>
                            <div class="h-3 bg-white/20 rounded-full overflow-hidden">
                                <div class="h-full bg-primary-fixed w-[70%] rounded-full shadow-[0_0_10px_rgba(122,249,199,0.5)]"></div>
                            </div>
                            <p class="text-xs opacity-75 font-body">¡Casi llegas a tu meta de pacientes atendidos esta semana!</p>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <NewAppointmentModal
            :show="showModal"
            :patients="patients"
            @close="showModal = false"
        />
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import NewAppointmentModal from '@/Components/NewAppointmentModal.vue';

const props = defineProps({
    totalPatients:     { type: Number, default: 0 },
    todayAppointments: { type: Array,  default: () => [] },
    patients:          { type: Array,  default: () => [] },
    adminNotices:      { type: Array,  default: () => [] },
    appointmentsPerWeekend:{ type: Number, default: 0 },
});

const showModal = ref(false);

const messages = ref(
    (props.adminNotices ?? []).map((notice) => ({
        category: notice.category,
        title: notice.title,
        message: notice.message,
        admin: notice.admin,
        time: notice.sentAt,
    }))
);
let channel = null;

const authUser = computed(() => usePage().props.auth?.user);

const today = computed(() => {
    return new Date().toLocaleDateString('es-ES', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
    }).replace(/^\w/, c => c.toUpperCase());
});

onMounted(() => {
    if (window.Echo) {
        channel = window.Echo.channel('notifications');
        channel.listen('.notification.sent', (data) => {
            const myUserId = authUser.value?.id;
            const isForMe = !data.target_user_id || Number(data.target_user_id) === Number(myUserId);
            if (!isForMe) return;

            messages.value.unshift({
                category: data.category ?? 'update',
                title: data.title ?? 'Notificación en vivo',
                message: data.message ?? 'Tienes un nuevo aviso.',
                admin: data.admin_name ?? 'Sistema',
                time: new Date().toLocaleTimeString(),
            });
        });
    }
});

onUnmounted(() => {
    if (channel) {
        window.Echo.leave('notifications');
    }
});

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
</script>
