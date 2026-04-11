<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="emit('close')" />

                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div v-if="show" class="relative bg-white w-full max-w-4xl max-h-[92vh] overflow-y-auto rounded-[2.5rem] shadow-2xl flex flex-col z-10">
                        <div class="sticky top-0 bg-white/90 backdrop-blur-md px-10 py-6 flex justify-between items-center border-b border-slate-100 z-10">
                            <div>
                                <h3 class="text-2xl font-extrabold text-emerald-900 tracking-tight">Crear Nuevo Contenido</h3>
                                <p class="text-sm text-slate-500 font-medium">Nutre a tu comunidad con información valiosa</p>
                            </div>
                            <button type="button" class="p-2 hover:bg-slate-100 rounded-full transition-colors" @click="emit('close')">
                                <span class="material-symbols-outlined text-slate-400">close</span>
                            </button>
                        </div>

                        <form class="p-10 space-y-8" @submit.prevent="submit('published')">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-3">
                                    <label class="text-sm font-bold text-emerald-900 ml-1">Tipo de Contenido</label>
                                    <div class="flex gap-2 p-1 bg-slate-100 rounded-2xl">
                                        <button
                                            v-for="option in typeOptions"
                                            :key="option.value"
                                            type="button"
                                            class="flex-1 flex items-center justify-center gap-2 py-3 px-4 rounded-xl transition-all"
                                            :class="form.content_type === option.value ? 'bg-white text-emerald-800 shadow-sm font-bold' : 'text-slate-500 hover:text-emerald-700 font-semibold'"
                                            @click="form.content_type = option.value"
                                        >
                                            <span class="material-symbols-outlined text-sm" :style="form.content_type === option.value ? filledIconStyle : ''">{{ option.icon }}</span>
                                            {{ option.label }}
                                        </button>
                                    </div>
                                    <p v-if="form.errors.content_type" class="text-xs text-error ml-1">{{ form.errors.content_type }}</p>
                                </div>

                                <div class="space-y-3">
                                    <label class="text-sm font-bold text-emerald-900 ml-1">Tiempo estimado de lectura</label>
                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            v-for="minutes in readingTimeOptions"
                                            :key="minutes"
                                            type="button"
                                            class="px-4 py-3 rounded-full text-sm font-bold cursor-pointer transition-colors border-2"
                                            :class="form.reading_time_minutes === minutes ? 'bg-secondary-container text-on-secondary-container border-primary/20' : 'bg-secondary-container/40 text-on-secondary-container border-transparent hover:bg-secondary-container'"
                                            @click="form.reading_time_minutes = minutes"
                                        >
                                            {{ readingTimeLabel(minutes) }}
                                        </button>
                                    </div>
                                    <p v-if="form.errors.reading_time_minutes" class="text-xs text-error ml-1">{{ form.errors.reading_time_minutes }}</p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <label class="text-sm font-bold text-emerald-900 ml-1">Imagen de portada</label>
                                <button
                                    type="button"
                                    class="relative group w-full overflow-hidden border-2 border-dashed rounded-[2rem] transition-all aspect-[21/9] flex flex-col items-center justify-center gap-4"
                                    :class="form.errors.cover_image ? 'border-error bg-error/5' : 'border-slate-200 hover:border-emerald-300 bg-slate-50/50'"
                                    @click="openFilePicker"
                                >
                                    <div v-if="coverPreview" class="absolute inset-0">
                                        <img :src="coverPreview" alt="Vista previa de portada" class="w-full h-full object-cover" />
                                        <div class="absolute inset-0 bg-slate-900/35" />
                                    </div>
                                    <div class="relative w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-md text-emerald-600 group-hover:scale-110 transition-transform">
                                        <span class="material-symbols-outlined text-3xl">cloud_upload</span>
                                    </div>
                                    <div class="relative text-center">
                                        <p class="font-bold" :class="coverPreview ? 'text-white' : 'text-emerald-900'">
                                            {{ coverPreview ? 'Cambiar imagen de portada' : 'Sube o arrastra una imagen aquí' }}
                                        </p>
                                        <p class="text-xs" :class="coverPreview ? 'text-white/80' : 'text-slate-400'">JPG, PNG hasta 5MB (Recomendado 1200x500px)</p>
                                    </div>
                                </button>
                                <input ref="fileInput" type="file" class="hidden" accept="image/png,image/jpeg,image/jpg,image/webp" @change="handleCoverChange" />
                                <p v-if="selectedFileName" class="text-xs text-on-surface-variant ml-1">Archivo seleccionado: {{ selectedFileName }}</p>
                                <p v-if="form.errors.cover_image" class="text-xs text-error ml-1">{{ form.errors.cover_image }}</p>
                            </div>

                            <div class="space-y-3">
                                <label class="text-sm font-bold text-emerald-900 ml-1">Título de la publicación</label>
                                <input
                                    v-model="form.title"
                                    class="w-full bg-surface-container-high border-none rounded-xl py-4 px-6 text-xl font-bold text-emerald-950 placeholder:text-slate-400 focus:ring-2 focus:ring-emerald-500/20 transition-all"
                                    placeholder="Ej: 5 Superalimentos para mejorar tu microbiota"
                                    type="text"
                                />
                                <p v-if="form.errors.title" class="text-xs text-error ml-1">{{ form.errors.title }}</p>
                            </div>

                            <div class="space-y-3">
                                <label class="text-sm font-bold text-emerald-900 ml-1">Escribe aquí tu contenido...</label>
                                <div class="border border-slate-100 rounded-[2rem] overflow-hidden bg-white shadow-sm">
                                    <div class="flex items-center gap-2 p-3 bg-slate-50 border-b border-slate-100">
                                        <button type="button" class="p-2 hover:bg-white hover:shadow-sm rounded-lg transition-all text-slate-600" @click="applyFormat('bold')">
                                            <span class="material-symbols-outlined">format_bold</span>
                                        </button>
                                        <button type="button" class="p-2 hover:bg-white hover:shadow-sm rounded-lg transition-all text-slate-600" @click="applyFormat('italic')">
                                            <span class="material-symbols-outlined">format_italic</span>
                                        </button>
                                        <button type="button" class="p-2 hover:bg-white hover:shadow-sm rounded-lg transition-all text-slate-600" @click="applyFormat('list')">
                                            <span class="material-symbols-outlined">format_list_bulleted</span>
                                        </button>
                                        <div class="w-px h-6 bg-slate-200 mx-1"></div>
                                        <button type="button" class="p-2 hover:bg-white hover:shadow-sm rounded-lg transition-all text-slate-600" @click="applyFormat('link')">
                                            <span class="material-symbols-outlined">link</span>
                                        </button>
                                        <button type="button" class="p-2 hover:bg-white hover:shadow-sm rounded-lg transition-all text-slate-600" @click="applyFormat('image')">
                                            <span class="material-symbols-outlined">image</span>
                                        </button>
                                    </div>
                                    <textarea
                                        ref="bodyField"
                                        v-model="form.body"
                                        class="w-full border-none focus:ring-0 p-8 text-on-surface leading-relaxed resize-none"
                                        placeholder="Comienza a redactar tu artículo o receta..."
                                        rows="8"
                                    />
                                </div>
                                <p v-if="form.errors.body" class="text-xs text-error ml-1">{{ form.errors.body }}</p>
                            </div>
                        </form>

                        <div class="sticky bottom-0 bg-white/90 backdrop-blur-md px-10 py-8 flex justify-end items-center gap-4 border-t border-slate-100 z-10">
                            <button
                                type="button"
                                class="px-8 py-3.5 rounded-full font-bold text-emerald-700 hover:bg-emerald-50 transition-all disabled:opacity-60"
                                :disabled="form.processing"
                                @click="submit('draft')"
                            >
                                Guardar como Borrador
                            </button>
                            <button
                                type="button"
                                class="px-10 py-3.5 bg-primary text-on-primary rounded-full font-bold shadow-lg shadow-emerald-900/10 hover:shadow-emerald-900/20 hover:translate-y-[-2px] active:translate-y-0 transition-all flex items-center gap-2 disabled:opacity-60 disabled:translate-y-0"
                                :disabled="form.processing"
                                @click="submit('published')"
                            >
                                <span class="material-symbols-outlined text-sm">send</span>
                                {{ form.processing ? 'Guardando...' : 'Publicar Ahora' }}
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { computed, nextTick, ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['close', 'saved'])

const fileInput = ref(null)
const bodyField = ref(null)
const coverPreview = ref(null)
const selectedFileName = ref('')

const typeOptions = [
    { value: 'recipe', label: 'Receta', icon: 'restaurant_menu' },
    { value: 'article', label: 'Artículo', icon: 'article' },
    { value: 'news', label: 'Noticia', icon: 'campaign' },
]

const readingTimeOptions = [3, 5, 10]
const filledIconStyle = "font-variation-settings: 'FILL' 1;"

const form = useForm({
    content_type: 'recipe',
    reading_time_minutes: 5,
    title: '',
    body: '',
    cover_image: null,
    status: 'draft',
})

const selectedFile = computed(() => form.cover_image)

watch(() => props.show, (value) => {
    if (value) {
        resetForm()
    }
})

watch(selectedFile, (file) => {
    if (!file) {
        coverPreview.value = null
        selectedFileName.value = ''
        return
    }

    selectedFileName.value = file.name
    const reader = new FileReader()
    reader.onload = (event) => {
        coverPreview.value = event.target?.result ?? null
    }
    reader.readAsDataURL(file)
})

function readingTimeLabel(minutes) {
    return minutes >= 10 ? '10+ min' : `${minutes} min`
}

function openFilePicker() {
    fileInput.value?.click()
}

function handleCoverChange(event) {
    const [file] = event.target.files || []
    form.cover_image = file ?? null
}

function applyFormat(type) {
    const field = bodyField.value
    if (!field) {
        return
    }

    const start = field.selectionStart ?? 0
    const end = field.selectionEnd ?? 0
    const selectedText = form.body.slice(start, end)

    const wrappers = {
        bold: ['**', '**'],
        italic: ['*', '*'],
        list: ['\n- ', ''],
        link: ['[', '](https://)'],
        image: ['![Descripción](', ')'],
    }

    const [prefix, suffix] = wrappers[type] ?? ['', '']
    const replacement = `${prefix}${selectedText || ''}${suffix}`

    form.body = `${form.body.slice(0, start)}${replacement}${form.body.slice(end)}`

    nextTick(() => {
        field.focus()
        const cursor = start + replacement.length
        field.setSelectionRange(cursor, cursor)
    })
}

function resetForm() {
    form.reset()
    form.clearErrors()
    form.content_type = 'recipe'
    form.reading_time_minutes = 5
    form.status = 'draft'
    coverPreview.value = null
    selectedFileName.value = ''

    if (fileInput.value) {
        fileInput.value.value = ''
    }
}

function submit(status) {
    form.status = status
    form.post(route('content-manager.store'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            emit('saved')
            emit('close')
        },
    })
}
</script>
