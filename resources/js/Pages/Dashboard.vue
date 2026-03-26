<template>
    <AppLayout>
        <section class="p-10 space-y-10">

            <!-- Hero Header -->
            <div class="flex justify-between items-end">
                <div class="space-y-2">
                    <h1 class="text-4xl font-extrabold font-headline tracking-tight text-on-surface">Bienvenido, {{ authUser?.first_name }}</h1>
                    <p class="text-on-surface-variant font-body">Tienes 4 citas pendientes para el día de hoy.</p>
                </div>
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

            <!-- Metrics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-surface-container-lowest p-6 rounded-xl border-l-4 border-primary">
                    <p class="text-on-surface-variant text-sm font-semibold">Total Pacientes</p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-3xl font-extrabold font-headline">128</span>
                        <span class="text-primary text-xs font-bold bg-primary-container px-2 py-1 rounded-full">+12%</span>
                    </div>
                </div>
                <div class="bg-surface-container-lowest p-6 rounded-xl">
                    <p class="text-on-surface-variant text-sm font-semibold">Citas Semanales</p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-3xl font-extrabold font-headline">34</span>
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
                        <!-- Card 1 -->
                        <div class="bg-surface-container-lowest p-5 rounded-xl flex items-center justify-between hover:bg-surface-container-low transition-colors group">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 bg-secondary-container rounded-full overflow-hidden">
                                    <img alt="Paciente" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA3qgQsn-12D5dyUhV1eMCUtv51-5T8p8De7nBZDETKTCsXd1pmlu0SrbSSTi51GDpiglgYeLZETRrPqJk7GdEKfAKeS91t5AYtmA9FoYXcsNxj3T5FbH89nMwMceyjG_FNIfJJcZWQCXRXrbGU-iFUqyFHMTYLMgZz-XocRRIm6DppiU46RsKrPL4Io6k4ncxu1QmSFL1zKAxDaiiLTLw8s3monH6cU_zvP7g-VoTpUvQ_t6GKK4-ES_jVwGy7G8z7VWqlpu8KaDi7"/>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg font-headline">Lucía Martínez</h3>
                                    <p class="text-on-surface-variant text-sm">Control de Diabetes Tipo II</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="px-3 py-1 bg-surface-container-high rounded-lg">
                                    <p class="text-sm font-bold text-on-surface">09:00 AM</p>
                                </div>
                                <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary transition-colors">arrow_forward_ios</span>
                            </div>
                        </div>
                        <!-- Card 2 -->
                        <div class="bg-surface-container-lowest p-5 rounded-xl flex items-center justify-between hover:bg-surface-container-low transition-colors group">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 bg-primary-container rounded-full overflow-hidden">
                                    <img alt="Paciente" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAVDWzfjOLinVzzcDWCXeL1uodAKgODgRw36mCV7IBmccXJ5TwvdY3YbhXYNMAUh8l9L8ay6YtThra-uSRwJQVv7gWhyIbYTcf_McwMlV8jKnSQPvWnSht8Z2N1DzLVoMbMvaJ475P9E4Yjm19RBgEHFSvGsievbPOLxuQ_1R4hD-POK9UtAa0XIHVGgqFATedYCM8zh_rmBR08-1CEANTwRc6jgFOQ7fqe7nByxypUkL1Mpr78L6fRdJ39YW0_8V5sLKVezUfny_06"/>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg font-headline">Carlos Ruiz</h3>
                                    <p class="text-on-surface-variant text-sm">Rendimiento Deportivo</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="px-3 py-1 bg-surface-container-high rounded-lg">
                                    <p class="text-sm font-bold text-on-surface">11:30 AM</p>
                                </div>
                                <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary transition-colors">arrow_forward_ios</span>
                            </div>
                        </div>
                        <!-- Card 3 -->
                        <div class="bg-surface-container-lowest p-5 rounded-xl flex items-center justify-between hover:bg-surface-container-low transition-colors group">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 bg-secondary-container rounded-full overflow-hidden">
                                    <img alt="Paciente" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuASge0VbE7HfHHr4hztUBqP-3bGy8lP89Kgw7oBYQWCXEhWZnRhLHVxrSzLNcYXcXpzTAEYWEIzoGD9zFPyPUZVLRZQUGmh7EgEGPP8hwBweB2F6jQiEaqef9zpomsnV4XLDpL2lGgSXL3tjkLDx_h2fA_tZsfxGMt1qrYaCmRLCPROMkp6YuZuMLuwVFD00rQc7CCaimFJz-P72dx4-MdXyidRr30pcaPe-W4l1Yo3EsZ3uwu9B-K3NuiC374K1ZMnhkllxJP7XFGJ"/>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg font-headline">Elena Vargas</h3>
                                    <p class="text-on-surface-variant text-sm">Alergias Alimentarias</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="px-3 py-1 bg-surface-container-high rounded-lg">
                                    <p class="text-sm font-bold text-on-surface">04:00 PM</p>
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
                            Esperando eventos en tiempo real...
                        </div>
                        <ul v-else class="space-y-2">
                            <li
                                v-for="(msg, i) in messages"
                                :key="i"
                                class="flex items-start gap-3 p-3 bg-surface-container-low rounded-lg"
                            >
                                <span class="material-symbols-outlined text-primary mt-0.5" style="font-size:16px;font-variation-settings:'FILL' 1;">circle</span>
                                <div>
                                    <p class="text-sm font-medium text-on-surface">{{ msg.message }}</p>
                                    <p class="text-xs text-on-surface-variant">{{ msg.time }}</p>
                                </div>
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
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const messages = ref([]);
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
            messages.value.unshift({
                message: data.message,
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
</script>
