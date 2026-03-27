<template>
    <AppLayout>
        <main class="p-8 min-h-screen space-y-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <nav class="flex text-xs text-on-surface-variant mb-2 space-x-2 items-center">
                        <Link :href="route('admin.dashboard')" class="hover:text-primary transition-colors">Admin</Link>
                        <span class="material-symbols-outlined" style="font-size:14px">chevron_right</span>
                        <span class="text-primary font-bold">Avisos</span>
                    </nav>
                    <h1 class="text-4xl font-extrabold text-on-surface tracking-tight font-headline">Centro de Avisos</h1>
                    <p class="text-on-surface-variant mt-1">Envía comunicados a todos los nutriólogos o a uno en específico.</p>
                </div>
            </div>

            <div v-if="$page.props.flash?.success" class="bg-primary-container/25 text-on-primary-container rounded-2xl px-6 py-4 text-sm font-medium">
                {{ $page.props.flash.success }}
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                <section class="xl:col-span-5 bg-surface-container-lowest rounded-3xl p-6 shadow-sm">
                    <h2 class="text-xl font-extrabold font-headline mb-5">Nuevo aviso</h2>

                    <form class="space-y-4" @submit.prevent="submitNotice">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-2">Destino</label>
                            <div class="grid grid-cols-2 gap-2 p-1 rounded-xl bg-surface-container-low">
                                <button
                                    type="button"
                                    class="py-2 text-xs font-bold rounded-lg transition-colors"
                                    :class="form.scope === 'all' ? 'bg-surface-container-lowest text-on-surface shadow-sm' : 'text-on-surface-variant hover:bg-surface-container'"
                                    @click="form.scope = 'all'"
                                >Todos</button>
                                <button
                                    type="button"
                                    class="py-2 text-xs font-bold rounded-lg transition-colors"
                                    :class="form.scope === 'individual' ? 'bg-surface-container-lowest text-on-surface shadow-sm' : 'text-on-surface-variant hover:bg-surface-container'"
                                    @click="form.scope = 'individual'"
                                >Individual</button>
                            </div>
                        </div>

                        <div v-if="form.scope === 'individual'">
                            <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-2">Nutriólogo</label>
                            <select
                                v-model="form.nutritionist_id"
                                class="w-full bg-surface-container-low rounded-xl px-4 py-3 text-sm border-none focus:ring-1 focus:ring-primary"
                            >
                                <option value="">Selecciona uno...</option>
                                <option v-for="n in nutritionists" :key="n.id" :value="n.id">{{ n.name }}</option>
                            </select>
                            <p v-if="errors.nutritionist_id" class="text-xs text-error mt-1">{{ errors.nutritionist_id }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-2">Categoría</label>
                            <select
                                v-model="form.category"
                                class="w-full bg-surface-container-low rounded-xl px-4 py-3 text-sm border-none focus:ring-1 focus:ring-primary"
                            >
                                <option value="update">Actualización</option>
                                <option value="maintenance">Mantenimiento</option>
                                <option value="policy">Política</option>
                                <option value="training">Capacitación</option>
                                <option value="alert">Alerta</option>
                                <option value="reminder">Recordatorio</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-2">Título</label>
                            <input
                                v-model="form.title"
                                type="text"
                                maxlength="120"
                                class="w-full bg-surface-container-low rounded-xl px-4 py-3 text-sm border-none focus:ring-1 focus:ring-primary"
                                placeholder="Ej. Actualización del módulo de citas"
                            />
                            <p v-if="errors.title" class="text-xs text-error mt-1">{{ errors.title }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-2">Mensaje</label>
                            <textarea
                                v-model="form.message"
                                maxlength="2000"
                                rows="6"
                                class="w-full bg-surface-container-low rounded-xl px-4 py-3 text-sm border-none focus:ring-1 focus:ring-primary"
                                placeholder="Describe el aviso para el equipo de nutriólogos"
                            ></textarea>
                            <div class="flex justify-between mt-1">
                                <p v-if="errors.message" class="text-xs text-error">{{ errors.message }}</p>
                                <p class="text-xs text-on-surface-variant ml-auto">{{ form.message.length }}/2000</p>
                            </div>
                        </div>

                        <button
                            type="submit"
                            :disabled="sending"
                            class="w-full py-3 rounded-xl bg-primary text-on-primary font-bold hover:opacity-90 transition-opacity disabled:opacity-60"
                        >{{ sending ? 'Enviando...' : 'Enviar aviso' }}</button>
                    </form>
                </section>

                <section class="xl:col-span-7 bg-surface-container-lowest rounded-3xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-xl font-extrabold font-headline">Historial reciente</h2>
                        <span class="text-xs text-on-surface-variant">{{ recentNotices.length }} registros</span>
                    </div>

                    <div v-if="!recentNotices.length" class="text-center py-16 text-on-surface-variant">
                        <span class="material-symbols-outlined text-5xl opacity-30">mark_email_unread</span>
                        <p class="mt-2">Aún no se han enviado avisos.</p>
                    </div>

                    <div v-else class="space-y-3 max-h-[680px] overflow-y-auto pr-1">
                        <article
                            v-for="notice in recentNotices"
                            :key="notice.id"
                            class="p-4 rounded-2xl border border-outline-variant/20 bg-surface-container-low"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[10px] font-bold uppercase px-2 py-1 rounded-full"
                                            :class="notice.scope === 'all' ? 'bg-primary/15 text-primary' : 'bg-secondary/15 text-secondary'"
                                        >{{ notice.scope === 'all' ? 'Todos' : 'Individual' }}</span>
                                        <span class="text-[10px] font-bold uppercase px-2 py-1 rounded-full bg-surface-container-high text-on-surface-variant">{{ categoryLabel(notice.category) }}</span>
                                    </div>
                                    <h3 class="font-bold text-on-surface">{{ notice.title }}</h3>
                                    <p class="text-sm text-on-surface-variant mt-1 whitespace-pre-line">{{ notice.message }}</p>
                                </div>
                            </div>
                            <div class="mt-3 text-xs text-on-surface-variant flex flex-wrap gap-x-4 gap-y-1">
                                <span>Destino: {{ notice.recipientName }}</span>
                                <span>Enviado por: {{ notice.adminName }}</span>
                                <span>{{ notice.sentAt }}</span>
                            </div>
                        </article>
                    </div>
                </section>
            </div>
        </main>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    nutritionists: { type: Array, default: () => [] },
    recentNotices: { type: Array, default: () => [] },
});

const sending = ref(false);
const errors = ref({});
const form = ref({
    scope: 'all',
    nutritionist_id: '',
    category: 'update',
    title: '',
    message: '',
});

function resetForm() {
    form.value = {
        scope: 'all',
        nutritionist_id: '',
        category: 'update',
        title: '',
        message: '',
    };
}

function submitNotice() {
    sending.value = true;
    errors.value = {};

    router.post(route('admin.avisos.send'), form.value, {
        preserveScroll: true,
        onSuccess: () => {
            resetForm();
        },
        onError: (errs) => {
            errors.value = errs;
        },
        onFinish: () => {
            sending.value = false;
        },
    });
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
</script>
