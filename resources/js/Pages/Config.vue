<template>
    <AppLayout>
        <div class="p-8 pb-12">

            <!-- Header -->
            <header class="mb-10">
                <h1 class="text-4xl font-extrabold text-on-surface tracking-tight mb-2 font-headline">Configuración del Sistema</h1>
                <p class="text-on-surface-variant">Gestiona tu identidad profesional y las preferencias de tu clínica.</p>
            </header>

            <div class="flex flex-col gap-6">

                <!-- ── Identidad Visual ── -->
                <section class="w-full bg-gradient-to-br from-surface-container-lowest to-primary-container/10 rounded-3xl p-8 border border-primary/10 shadow-sm">
                    <div class="flex items-center gap-3 mb-8">
                        <span class="material-symbols-outlined text-primary text-3xl">brush</span>
                        <div>
                            <h2 class="text-2xl font-bold text-on-surface font-headline">Identidad Visual</h2>
                            <p class="text-xs text-on-surface-variant">Personaliza la apariencia de tu clínica en la plataforma.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-10">
                        <!-- Logo -->
                        <div class="space-y-4">
                            <label class="text-sm font-bold text-on-surface-variant uppercase tracking-wider">Logotipo de la Clínica</label>
                            <input
                                ref="logoInput"
                                type="file"
                                accept="image/png,image/jpeg,image/jpg,image/webp"
                                class="hidden"
                                @change="handleClinicLogoChange"
                            />
                            <div class="flex items-center gap-6">
                                <button
                                    type="button"
                                    class="w-24 h-24 bg-surface-container-high rounded-2xl flex items-center justify-center border-2 border-dashed border-outline-variant/50 relative overflow-hidden group cursor-pointer"
                                    @click="triggerLogoPicker"
                                >
                                    <img
                                        v-if="clinicLogoUrl"
                                        :src="clinicLogoUrl"
                                        alt="Logo de la clínica"
                                        class="w-full h-full object-contain p-3"
                                    />
                                    <span v-else class="material-symbols-outlined text-outline-variant text-4xl group-hover:opacity-0 transition-opacity">image</span>
                                    <div class="absolute inset-0 bg-primary/10 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                        <span class="material-symbols-outlined text-primary">add_a_photo</span>
                                    </div>
                                </button>
                                <div class="flex-1">
                                    <p class="text-xs text-on-surface-variant mb-3">Sube tu logo profesional (PNG, JPG). Tamaño recomendado: 512×512px.</p>
                                    <button
                                        type="button"
                                        class="px-4 py-2 bg-primary text-on-primary rounded-xl text-xs font-bold hover:opacity-90 transition-opacity disabled:opacity-60"
                                        :disabled="isUploadingLogo || !isProPlan"
                                        @click="triggerLogoPicker"
                                    >
                                        {{ isUploadingLogo ? 'Subiendo...' : 'Subir Imagen' }}
                                    </button>
                                    <p v-if="!isProPlan" class="mt-2 text-[11px] font-medium text-on-surface-variant">
                                        Disponible solo en plan Pro.
                                    </p>
                                    <p v-if="page.props.errors?.clinic_logo" class="mt-2 text-[11px] font-medium text-error">
                                        {{ page.props.errors.clinic_logo }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- Colores -->
                        <div class="space-y-4">
                            <label class="text-sm font-bold text-on-surface-variant uppercase tracking-wider">Color de Identidad</label>
                            <p class="text-xs text-on-surface-variant">Este color se usará para botones, acentos y navegación.</p>
                            <div class="flex flex-wrap gap-3 mt-2">
                                <button
                                    v-for="t in colorThemes"
                                    :key="t.key"
                                    :title="t.label"
                                    :style="{ backgroundColor: t.hex, '--tw-ring-color': t.hex }"
                                    class="w-10 h-10 rounded-full transition-all hover:scale-110"
                                    :class="form.themeColor === t.key ? 'ring-2 ring-offset-2 ring-offset-surface scale-110' : ''"
                                    @click="applyTheme(t.key)"
                                ></button>
                                <button class="w-10 h-10 rounded-full bg-surface-container-highest flex items-center justify-center border border-outline-variant transition-transform hover:scale-110" title="Personalizado">
                                    <span class="material-symbols-outlined text-sm">colorize</span>
                                </button>
                            </div>
                        </div>
                        <!-- Nombre comercial -->
                        <div class="space-y-4">
                            <label class="text-sm font-bold text-on-surface-variant uppercase tracking-wider">Nombre Comercial</label>
                            <div class="space-y-2">
                                <input
                                    v-model="form.clinicName"
                                    type="text"
                                    class="w-full bg-surface-container-lowest border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all text-sm outline-none"
                                />
                                <p class="text-[10px] text-on-surface-variant px-1 italic">Este nombre aparecerá en el encabezado y comunicaciones.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Row 2: Perfil + Preferencias -->
                <div class="flex gap-6 items-start">

                <!-- ── Perfil Profesional ── -->
                <section class="flex-1 min-w-0 bg-surface-container-lowest rounded-3xl p-8 shadow-sm">
                    <div class="flex flex-row gap-8 items-start">
                        <!-- Avatar -->
                        <div class="relative group shrink-0">
                            <img
                                :alt="authUser?.full_name"
                                :src="authUser?.avatar ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(authUser?.full_name ?? 'N')}&background=7af9c7&color=004933&size=160`"
                                class="w-40 h-40 rounded-3xl object-cover border-4 border-surface-container shadow-inner"
                            />
                            <button
                                class="absolute -bottom-2 -right-2 bg-primary text-on-primary p-2 rounded-xl shadow-lg hover:scale-105 transition-transform"
                                @click="handleProfilePhotoEdit"
                            >
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>
                        </div>
                        <!-- Campos -->
                        <div class="flex-1 w-full space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider ml-1">Nombre Completo</label>
                                    <input
                                        v-model="form.fullName"
                                        type="text"
                                        class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all text-sm outline-none"
                                        :disabled="!isProPlan"
                                    />
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider ml-1">Especialidad</label>
                                    <input
                                        v-model="form.specialization"
                                        type="text"
                                        class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all text-sm outline-none"
                                        :disabled="!isProPlan"
                                    />
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider ml-1">Cédula Profesional</label>
                                    <input
                                        v-model="form.licenseNumber"
                                        type="text"
                                        class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all text-sm outline-none"
                                        :disabled="!isProPlan"
                                    />
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider ml-1">Firma Digital</label>
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-surface-container-high rounded-xl px-4 py-3 italic text-on-surface-variant/60 flex items-center border-2 border-dashed border-outline-variant/30 h-[46px] text-sm">
                                            {{ form.signatureFileName || 'Sin archivo' }}
                                        </div>
                                        <button
                                            class="bg-primary-container text-on-primary-container p-2.5 rounded-xl hover:bg-primary-fixed transition-colors shrink-0 disabled:opacity-50"
                                            :disabled="!isProPlan"
                                            @click.prevent="handleProfilePhotoEdit"
                                        >
                                            <span class="material-symbols-outlined">upload</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ── Preferencias ── -->
                <section class="w-80 shrink-0 bg-primary-container/30 rounded-3xl p-8 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <span class="material-symbols-outlined text-primary">settings_suggest</span>
                            <h2 class="text-xl font-bold text-on-surface font-headline">Preferencias</h2>
                        </div>
                        <div class="space-y-6">
                            <!-- Unidades -->
                            <div>
                                <label class="text-sm font-semibold block mb-2">Unidades de Medida</label>
                                <div class="flex gap-2 p-1 bg-surface-container-highest rounded-xl">
                                    <button
                                        class="flex-1 py-2 rounded-lg text-xs font-bold transition-colors"
                                        :class="form.units === 'metric' ? 'bg-surface-container-lowest shadow-sm text-on-surface' : 'text-on-surface-variant hover:bg-surface-container-low'"
                                        @click="form.units = 'metric'"
                                    >Métrico (kg/cm)</button>
                                    <button
                                        class="flex-1 py-2 rounded-lg text-xs font-bold transition-colors"
                                        :class="form.units === 'imperial' ? 'bg-surface-container-lowest shadow-sm text-on-surface' : 'text-on-surface-variant hover:bg-surface-container-low'"
                                        @click="form.units = 'imperial'"
                                    >Imperial (lb/in)</button>
                                </div>
                            </div>
                            <!-- IMC rangos -->
                            <div>
                                <label class="text-sm font-semibold block mb-2">Rangos de IMC</label>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-medium">Normal</span>
                                        <span class="text-primary font-bold">18.5 – 24.9</span>
                                    </div>
                                    <div class="h-2 w-full bg-surface-container-highest rounded-full overflow-hidden">
                                        <div class="h-full bg-primary rounded-full" style="width:45%; margin-left:20%"></div>
                                    </div>
                                    <button class="w-full py-2 border-2 border-primary/20 text-primary rounded-xl text-xs font-bold hover:bg-primary/5 transition-colors">
                                        Personalizar Rangos
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                </div><!-- end row 2 -->

                <!-- Row 3: Gestión + Seguridad -->
                <div class="flex gap-6 items-start">

                <!-- ── Gestión de Clínica ── -->
                <section class="flex-1 min-w-0 bg-surface-container-lowest rounded-3xl p-8 shadow-sm">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary">schedule</span>
                            <h2 class="text-xl font-bold text-on-surface font-headline">Gestión de Clínica</h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-on-secondary-fixed-variant text-lg" style="font-variation-settings:'FILL' 1">chat</span>
                            <span class="text-xs font-bold">WhatsApp Activo</span>
                            <!-- Toggle switch -->
                            <button
                                class="ml-2 relative w-10 h-5 rounded-full transition-colors"
                                :class="whatsappActive ? 'bg-primary' : 'bg-surface-container-highest'"
                                @click="whatsappActive = !whatsappActive"
                            >
                                <span
                                    class="absolute top-0.5 left-0.5 w-4 h-4 bg-surface-container-lowest rounded-full shadow-sm transition-transform"
                                    :class="whatsappActive ? 'translate-x-5' : 'translate-x-0'"
                                ></span>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-8">
                        <!-- Horarios -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-bold text-on-surface-variant flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg">calendar_month</span> Horarios de Atención
                            </h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-surface-container-low rounded-xl">
                                    <span class="text-sm font-medium">Lunes – Viernes</span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs bg-surface-container-lowest px-2 py-1 rounded-md shadow-sm">09:00</span>
                                        <span class="text-xs text-on-surface-variant">–</span>
                                        <span class="text-xs bg-surface-container-lowest px-2 py-1 rounded-md shadow-sm">18:00</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-surface-container-low rounded-xl">
                                    <span class="text-sm font-medium">Sábados</span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs bg-surface-container-lowest px-2 py-1 rounded-md shadow-sm">10:00</span>
                                        <span class="text-xs text-on-surface-variant">–</span>
                                        <span class="text-xs bg-surface-container-lowest px-2 py-1 rounded-md shadow-sm">14:00</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-error/5 text-error rounded-xl">
                                    <span class="text-sm font-medium">Domingos</span>
                                    <span class="text-xs font-bold">Cerrado</span>
                                </div>
                            </div>
                        </div>
                        <!-- Duración citas -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-sm font-bold text-on-surface-variant mb-4">Duración de Citas</h3>
                                <div class="grid grid-cols-3 gap-3">
                                    <button
                                        v-for="min in [30, 45, 60]"
                                        :key="min"
                                        class="py-3 rounded-2xl transition-colors flex flex-col items-center"
                                        :class="form.consultationDuration === min
                                            ? 'bg-primary text-on-primary ring-4 ring-primary/20'
                                            : 'bg-surface-container-low hover:bg-primary-container/40 text-on-surface'"
                                        @click="form.consultationDuration = min"
                                    >
                                        <span class="text-lg font-bold">{{ min }}</span>
                                        <span class="text-[10px] uppercase font-bold" :class="form.consultationDuration === min ? 'text-on-primary' : 'text-on-surface-variant'">min</span>
                                    </button>
                                </div>
                            </div>
                            <div class="p-4 bg-secondary-container/30 rounded-2xl">
                                <p class="text-[11px] leading-relaxed text-on-secondary-container">
                                    <strong>Nota:</strong> Los pacientes recibirán un recordatorio automático vía WhatsApp 24 horas antes de su cita.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ── Seguridad ── -->
                <section class="w-96 shrink-0 bg-surface-container-highest rounded-3xl p-8">
                    <div class="flex items-center gap-3 mb-8">
                        <span class="material-symbols-outlined text-error">verified_user</span>
                        <h2 class="text-xl font-bold text-on-surface font-headline">Seguridad</h2>
                    </div>
                    <div class="space-y-6">
                        <!-- Cambio de contraseña -->
                        <div class="p-6 bg-surface-container-lowest rounded-2xl space-y-4">
                            <h3 class="text-sm font-bold">Cambio de Contraseña</h3>
                            <div class="space-y-3">
                                <input
                                    v-model="passwordForm.current"
                                    type="password"
                                    placeholder="Contraseña actual"
                                    class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-error/20 transition-all outline-none"
                                />
                                <input
                                    v-model="passwordForm.new"
                                    type="password"
                                    placeholder="Nueva contraseña"
                                    class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-error/20 transition-all outline-none"
                                />
                                <button
                                    class="w-full py-3 bg-secondary text-on-secondary rounded-xl font-bold text-sm hover:opacity-90 transition-opacity"
                                    @click="updatePassword"
                                >Actualizar Contraseña</button>
                            </div>
                        </div>
                        <!-- 2FA -->
                        <div class="flex items-start gap-4 p-4 bg-surface-container-lowest rounded-2xl border border-outline-variant/10">
                            <div class="w-12 h-12 bg-tertiary-container rounded-xl flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-on-tertiary-container">vibration</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-bold mb-1">Doble Factor (2FA)</h4>
                                <p class="text-xs text-on-surface-variant leading-normal mb-3">Aumenta la seguridad de tu cuenta vinculando tu dispositivo móvil.</p>
                                <button class="flex items-center gap-2 text-primary font-bold text-xs group">
                                    Configurar Ahora
                                    <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                </div><!-- end row 3 -->

            </div>

            <!-- Footer acciones -->
            <footer class="mt-12 flex justify-end gap-4">
                <button
                    class="px-8 py-3 rounded-full text-on-surface-variant font-bold hover:bg-surface-container-high transition-colors"
                    @click="resetForm"
                >Descartar Cambios</button>
                <button
                    class="px-10 py-3 rounded-full bg-gradient-to-r from-primary to-primary-dim text-on-primary font-extrabold shadow-lg shadow-primary/20 active:scale-95 transition-all"
                    @click="saveConfig"
                >Guardar Configuración</button>
            </footer>

            <UpgradePlanModal
                :show="showUpgradeModal"
                :title="upgradeModal.title"
                :message="upgradeModal.message"
                :cta-label="upgradeModal.ctaLabel"
                :features="upgradeModal.features"
                @close="showUpgradeModal = false"
            />

        </div>
    </AppLayout>
</template>

<script setup>
import { computed, onBeforeUnmount, ref } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import UpgradePlanModal from '@/Components/UpgradePlanModal.vue';

// Props enviadas por ConfigController::index()
const props = defineProps({
    themeColor:           { type: String, default: 'emerald' },
    clinicLogoUrl:        { type: String, default: null },
    consultationDuration: { type: Number, default: 45 },
    specialization:       { type: String, default: '' },
    licenseNumber:        { type: String, default: '' },
    subscriptionPlanKey:  { type: String, default: 'free' },
});

const page     = usePage();
const authUser = computed(() => page.props.auth?.user);
const logoInput = ref(null);
const isUploadingLogo = ref(false);
const localClinicLogoUrl = ref(null);
const clinicLogoUrl = computed(() => localClinicLogoUrl.value ?? authUser.value?.clinic_logo_url ?? props.clinicLogoUrl ?? null);
const isProPlan = computed(() => props.subscriptionPlanKey === 'pro');
const showUpgradeModal = ref(false);
const upgradeModal = ref({
    title: '',
    message: '',
    ctaLabel: 'Ver Planes y Mejorar',
    features: [],
});
let logoObjectUrl = null;

// Catálogo de temas
const colorThemes = [
    { key: 'emerald', hex: '#006d4e', label: 'Esmeralda (default)' },
    { key: 'blue',    hex: '#1e40af', label: 'Azul' },
    { key: 'purple',  hex: '#7e22ce', label: 'Morado' },
    { key: 'rose',    hex: '#be123c', label: 'Rosa' },
    { key: 'amber',   hex: '#d97706', label: 'Ámbar' },
];

// Formulario principal
const form = ref({
    fullName:             authUser.value?.full_name ?? '',
    specialization:       props.specialization,
    licenseNumber:        props.licenseNumber,
    phone:                '',
    clinicName:           'Metabolé',
    units:                'metric',
    consultationDuration: props.consultationDuration,
    themeColor:           props.themeColor,
    signatureFileName:    null,
});

const whatsappActive = ref(true);

// Contraseña
const passwordForm = ref({ current: '', new: '' });

function resetClinicLogoPreview() {
    if (logoObjectUrl) {
        URL.revokeObjectURL(logoObjectUrl);
        logoObjectUrl = null;
    }

    localClinicLogoUrl.value = null;
}

function triggerLogoPicker() {
    if (!isProPlan.value) {
        openUpgradeModal('logo');
        return;
    }

    logoInput.value?.click();
}

function handleClinicLogoChange(event) {
    if (!isProPlan.value) {
        openUpgradeModal('logo');
        return;
    }

    const [file] = event.target.files ?? [];

    if (!file) {
        return;
    }

    resetClinicLogoPreview();
    logoObjectUrl = URL.createObjectURL(file);
    localClinicLogoUrl.value = logoObjectUrl;

    router.post(route('config.logo'), {
        clinic_logo: file,
    }, {
        forceFormData: true,
        preserveScroll: true,
        preserveState: true,
        onStart: () => {
            isUploadingLogo.value = true;
        },
        onSuccess: () => {
            resetClinicLogoPreview();
        },
        onError: () => {
            resetClinicLogoPreview();
        },
        onFinish: () => {
            isUploadingLogo.value = false;

            if (logoInput.value) {
                logoInput.value.value = '';
            }
        },
    });
}

function updatePassword() {
    // TODO: conectar al endpoint de cambio de contraseña
}

// Aplica el tema inmediatamente en el body y guarda en DB via PATCH
function applyTheme(key) {
    if (!isProPlan.value) {
        openUpgradeModal('theme');
        return;
    }

    form.value.themeColor = key;

    // Preview instantáneo: actualizar clase en body
    const body = document.body;
    body.classList.remove('theme-blue', 'theme-purple', 'theme-rose', 'theme-amber');
    if (key !== 'emerald') body.classList.add(`theme-${key}`);

    // Persiste en base de datos
    router.patch(route('config.theme'), { theme_color: key }, { preserveScroll: true });
}

function handleProfilePhotoEdit() {
    if (!isProPlan.value) {
        openUpgradeModal('profile-photo');
        return;
    }
}

function openUpgradeModal(context) {
    if (context === 'theme') {
        upgradeModal.value = {
            title: 'El cambio de tema es exclusivo del plan Pro',
            message: 'Personaliza colores, identidad visual y experiencia completa de tu clinica con el plan Pro.',
            ctaLabel: 'Ver Planes y Mejorar',
            features: [
                { icon: 'palette', label: 'Temas avanzados por clinica' },
                { icon: 'branding_watermark', label: 'Marca personalizada' },
                { icon: 'verified', label: 'Experiencia premium' },
            ],
        };
    } else if (context === 'profile-photo') {
        upgradeModal.value = {
            title: 'La edicion de perfil es exclusiva del plan Pro',
            message: 'Para editar datos profesionales y subir foto de perfil necesitas activar el plan Pro.',
            ctaLabel: 'Ver Planes y Mejorar',
            features: [
                { icon: 'photo_camera', label: 'Foto de perfil profesional' },
                { icon: 'edit_square', label: 'Edicion avanzada de perfil' },
                { icon: 'verified', label: 'Perfil con mayor confianza' },
            ],
        };
    } else {
        upgradeModal.value = {
            title: 'La personalizacion visual es exclusiva del plan Pro',
            message: 'Para subir logo y desbloquear herramientas visuales avanzadas, mejora tu plan a Pro.',
            ctaLabel: 'Ver Planes y Mejorar',
            features: [
                { icon: 'image', label: 'Logo personalizado de clinica' },
                { icon: 'palette', label: 'Estilo visual avanzado' },
                { icon: 'verified', label: 'Marca profesional completa' },
            ],
        };
    }

    showUpgradeModal.value = true;
}

function saveConfig() {
    // TODO: endpoint para guardar perfil completo
}

function resetForm() {
    form.value.fullName             = authUser.value?.full_name ?? '';
    form.value.specialization       = props.specialization;
    form.value.licenseNumber        = props.licenseNumber;
    form.value.phone                = '';
    form.value.clinicName           = 'Metabolé';
    form.value.units                = 'metric';
    form.value.consultationDuration = props.consultationDuration;
    form.value.themeColor           = props.themeColor;
    passwordForm.value              = { current: '', new: '' };

    // Revertir tema visual
    applyTheme(props.themeColor);
}

onBeforeUnmount(() => {
    resetClinicLogoPreview();
});
</script>
