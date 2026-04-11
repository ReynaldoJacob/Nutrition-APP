<template>
    <div class="min-h-screen bg-gradient-to-br from-primary-container to-emerald-50 flex items-center justify-center p-4">
        <Head title="Cambiar Contraseña" />
        <ChangePasswordModal :temporary-password="temporaryPassword" />
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import ChangePasswordModal from '@/Components/ChangePasswordModal.vue'

defineProps({
    temporaryPassword: {
        type: String,
        required: true,
    },
})
</script>
