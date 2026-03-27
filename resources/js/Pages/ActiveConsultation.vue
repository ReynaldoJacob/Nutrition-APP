<template>
    <div class="bg-surface font-body text-on-surface antialiased min-h-screen">

        <!-- Top Navigation Bar -->
        <header class="fixed top-0 w-full flex items-center justify-between px-6 py-3 bg-white/80 backdrop-blur-md z-50 shadow-sm">
            <div class="flex items-center gap-6">
                <div class="text-primary font-extrabold tracking-tight font-headline text-lg">Nutri App</div>

                <!-- Indicador de consulta activa -->
                <div class="flex items-center gap-2 bg-primary-container/30 px-3 py-1.5 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    <span class="text-xs font-semibold text-primary">En Consulta · {{ elapsed }}</span>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <!-- Info del paciente -->
                <div class="flex items-center gap-3 border-r border-surface-container pr-4">
                    <div class="text-right">
                        <div class="font-headline font-bold text-sm text-on-surface">{{ patient.name }}</div>
                        <div class="text-[10px] text-on-surface-variant uppercase tracking-wider">{{ patient.code }}</div>
                    </div>
                    <img
                        :alt="patient.name"
                        :src="patient.avatar ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(patient.name)}&background=7af9c7&color=004933`"
                        class="w-10 h-10 rounded-full object-cover ring-2 ring-primary/20"
                    />
                </div>

                <button
                    @click="openFinish"
                    class="bg-primary text-on-primary px-5 py-2 rounded-xl font-headline font-bold text-sm hover:opacity-90 active:scale-95 transition-all shadow-lg shadow-primary/10 flex items-center gap-2"
                >
                    <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                    Finalizar Consulta
                </button>
            </div>
        </header>

        <div class="flex h-screen pt-[57px] overflow-hidden">

            <!-- ─── Left Sidebar: Visita Anterior ─────────────────────────────── -->
            <aside class="w-80 flex flex-col bg-surface-container overflow-y-auto hide-scrollbar shrink-0">
                <div class="p-6 space-y-6">
                    <div class="flex items-center gap-2 text-on-surface-variant">
                        <span class="material-symbols-outlined text-lg">history</span>
                        <h2 class="font-headline font-bold text-sm uppercase tracking-widest">Visita Anterior</h2>
                    </div>

                    <!-- Sin visita previa -->
                    <div v-if="!previousVisit" class="bg-surface-container-lowest p-5 rounded-2xl text-center text-on-surface-variant space-y-2">
                        <span class="material-symbols-outlined text-3xl opacity-30">event_note</span>
                        <p class="text-xs font-medium">No hay registros de visitas anteriores.</p>
                    </div>

                    <!-- Con visita previa -->
                    <div v-else class="space-y-4">
                        <div class="bg-surface-container-lowest p-5 rounded-2xl space-y-4">
                            <div class="text-xs text-on-surface-variant">{{ formatDate(previousVisit.date) }}</div>

                            <div class="grid grid-cols-2 gap-4">
                                <div v-if="previousVisit.weight" class="space-y-1">
                                    <span class="text-[10px] text-on-surface-variant uppercase font-bold">Peso</span>
                                    <div class="font-headline font-bold text-lg text-on-surface">
                                        {{ previousVisit.weight }} <span class="text-xs font-normal">kg</span>
                                    </div>
                                </div>
                                <div v-if="previousVisit.bodyFat" class="space-y-1">
                                    <span class="text-[10px] text-on-surface-variant uppercase font-bold">Grasa Corp.</span>
                                    <div class="font-headline font-bold text-lg text-on-surface">
                                        {{ previousVisit.bodyFat }} <span class="text-xs font-normal">%</span>
                                    </div>
                                </div>
                                <div v-if="previousVisit.muscleMass" class="space-y-1">
                                    <span class="text-[10px] text-on-surface-variant uppercase font-bold">Masa Musc.</span>
                                    <div class="font-headline font-bold text-lg text-on-surface">
                                        {{ previousVisit.muscleMass }} <span class="text-xs font-normal">kg</span>
                                    </div>
                                </div>
                                <div v-if="previousVisit.bmi" class="space-y-1">
                                    <span class="text-[10px] text-on-surface-variant uppercase font-bold">IMC</span>
                                    <div class="font-headline font-bold text-lg text-on-surface">
                                        {{ previousVisit.bmi }}
                                    </div>
                                </div>
                            </div>

                            <div v-if="previousVisit.waist || previousVisit.hip || previousVisit.bloodPressure" class="pt-4 border-t border-surface-variant space-y-2">
                                <span class="text-[10px] text-on-surface-variant uppercase font-bold tracking-wider">Medidas</span>
                                <div class="space-y-1.5">
                                    <div v-if="previousVisit.waist" class="flex justify-between text-xs">
                                        <span class="text-on-surface-variant">Cintura</span>
                                        <span class="font-bold">{{ previousVisit.waist }} cm</span>
                                    </div>
                                    <div v-if="previousVisit.hip" class="flex justify-between text-xs">
                                        <span class="text-on-surface-variant">Cadera</span>
                                        <span class="font-bold">{{ previousVisit.hip }} cm</span>
                                    </div>
                                    <div v-if="previousVisit.bloodPressure" class="flex justify-between text-xs">
                                        <span class="text-on-surface-variant">Presión Art.</span>
                                        <span class="font-bold">{{ previousVisit.bloodPressure }}</span>
                                    </div>
                                </div>
                            </div>

                            <div v-if="previousVisit.summary" class="pt-4 border-t border-surface-variant space-y-2">
                                <span class="text-[10px] text-on-surface-variant uppercase font-bold tracking-wider">Notas Clínicas</span>
                                <p class="text-sm leading-relaxed text-on-surface-variant">{{ previousVisit.summary }}</p>
                            </div>

                            <!-- Plan nutricional anterior -->
                            <div v-if="previousVisit.prescribedKcal || previousVisit.gebAverage" class="pt-4 border-t border-surface-variant space-y-3">
                                <span class="text-[10px] text-on-surface-variant uppercase font-bold tracking-wider flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">local_fire_department</span>
                                    Plan Nutricional Anterior
                                </span>
                                <div class="space-y-1.5">
                                    <div v-if="previousVisit.gebAverage" class="flex justify-between text-xs">
                                        <span class="text-on-surface-variant">GEB (prom.)</span>
                                        <span class="font-bold">{{ Number(previousVisit.gebAverage).toLocaleString('es-MX') }} kcal</span>
                                    </div>
                                    <div v-if="previousVisit.getTotal" class="flex justify-between text-xs">
                                        <span class="text-on-surface-variant">GET</span>
                                        <span class="font-bold">{{ Number(previousVisit.getTotal).toLocaleString('es-MX') }} kcal</span>
                                    </div>
                                    <div v-if="previousVisit.prescribedKcal" class="flex justify-between text-xs">
                                        <span class="text-on-surface-variant">Prescripción</span>
                                        <span class="font-bold text-primary">{{ Number(previousVisit.prescribedKcal).toLocaleString('es-MX') }} kcal</span>
                                    </div>
                                    <div v-if="previousVisit.proteinGkg || previousVisit.fatGkg" class="flex justify-between text-xs">
                                        <span class="text-on-surface-variant">Macros</span>
                                        <span class="font-bold">
                                            {{ previousVisit.proteinGkg }}P · {{ previousVisit.fatGkg }}L g/kg
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contexto del paciente -->
                        <div v-if="patient.allergies?.length || patient.medicalConditions?.length" class="bg-error-container/20 p-4 rounded-2xl border border-error/10 space-y-3">
                            <span class="text-[10px] font-bold uppercase text-error tracking-wider">Alertas Clínicas</span>
                            <div v-if="patient.allergies?.length" class="space-y-1">
                                <p class="text-[10px] text-on-surface-variant font-bold">ALERGIAS</p>
                                <p class="text-xs text-on-surface-variant">{{ patient.allergies.join(', ') }}</p>
                            </div>
                            <div v-if="patient.medicalConditions?.length" class="space-y-1">
                                <p class="text-[10px] text-on-surface-variant font-bold">CONDICIONES</p>
                                <p class="text-xs text-on-surface-variant">{{ patient.medicalConditions.join(', ') }}</p>
                            </div>
                        </div>

                        <!-- Objetivo -->
                        <div v-if="patient.goalWeight" class="bg-primary-container/20 p-5 rounded-2xl border border-primary/10">
                            <h3 class="font-headline font-bold text-sm text-primary mb-2">Meta del Paciente</h3>
                            <div class="flex items-baseline gap-1">
                                <span class="font-headline font-extrabold text-2xl text-on-surface">{{ patient.goalWeight }}</span>
                                <span class="text-sm text-on-surface-variant">kg objetivo</span>
                            </div>
                            <div v-if="patient.currentWeight" class="mt-2 text-xs text-on-surface-variant">
                                Faltan {{ Math.abs(patient.currentWeight - patient.goalWeight).toFixed(1) }} kg para la meta
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- ─── Main: Formulario de mediciones ────────────────────────────── -->
            <main class="flex-1 bg-surface overflow-y-auto hide-scrollbar p-8">
                <div class="max-w-4xl mx-auto space-y-10">
                    <header>
                        <h1 class="font-headline font-extrabold text-4xl text-on-surface tracking-tight">Evaluación Activa</h1>
                        <p class="text-on-surface-variant mt-2">
                            {{ appointment.typeLabel }} · {{ formatDateFull(appointment.scheduledAt) }}
                        </p>
                    </header>

                    <!-- Tarjetas principales -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                        <!-- Peso -->
                        <div class="bg-surface-container-lowest p-8 rounded-[2.5rem] shadow-sm flex flex-col justify-between min-h-[220px]">
                            <div class="flex justify-between items-start">
                                <div>
                                    <label class="text-xs font-bold text-on-surface-variant uppercase tracking-[0.2em]">Peso Corporal</label>
                                    <div class="mt-2 flex items-baseline gap-2">
                                        <input
                                            v-model="form.weight"
                                            class="w-36 bg-transparent border-none p-0 font-headline font-extrabold text-6xl text-on-surface focus:ring-0 placeholder:text-surface-dim"
                                            placeholder="00.0"
                                            type="number"
                                            min="1" max="500" step="0.1"
                                        />
                                        <span class="font-headline font-bold text-2xl text-on-surface-variant">kg</span>
                                    </div>
                                </div>
                                <div class="bg-primary-container p-3 rounded-2xl">
                                    <span class="material-symbols-outlined text-primary text-3xl">scale</span>
                                </div>
                            </div>
                            <div v-if="previousVisit?.weight" class="mt-4 text-sm font-medium flex items-center gap-1"
                                :class="weightDiff < 0 ? 'text-primary' : weightDiff > 0 ? 'text-error' : 'text-on-surface-variant'"
                            >
                                <span class="material-symbols-outlined text-sm">{{ weightDiff <= 0 ? 'trending_down' : 'trending_up' }}</span>
                                Último registro: {{ previousVisit.weight }} kg
                                <span v-if="form.weight && weightDiff !== 0">
                                    ({{ weightDiff > 0 ? '+' : '' }}{{ weightDiff.toFixed(1) }} kg)
                                </span>
                            </div>
                        </div>

                        <!-- Grasa Corporal -->
                        <div class="bg-surface-container-lowest p-8 rounded-[2.5rem] shadow-sm flex flex-col justify-between min-h-[220px]">
                            <div class="flex justify-between items-start">
                                <div>
                                    <label class="text-xs font-bold text-on-surface-variant uppercase tracking-[0.2em]">Grasa Corporal %</label>
                                    <div class="mt-2 flex items-baseline gap-2">
                                        <input
                                            v-model="form.body_fat"
                                            class="w-36 bg-transparent border-none p-0 font-headline font-extrabold text-6xl text-on-surface focus:ring-0 placeholder:text-surface-dim"
                                            placeholder="00.0"
                                            type="number"
                                            min="1" max="100" step="0.1"
                                        />
                                        <span class="font-headline font-bold text-2xl text-on-surface-variant">%</span>
                                    </div>
                                </div>
                                <div class="bg-secondary-container p-3 rounded-2xl">
                                    <span class="material-symbols-outlined text-on-secondary-container text-3xl">fitness_center</span>
                                </div>
                            </div>
                            <div class="mt-4 text-sm text-on-surface-variant font-medium">
                                Método: Plicometría / Bioimpedancia
                            </div>
                        </div>

                        <!-- Masa Magra -->
                        <div class="bg-surface-container-lowest p-8 rounded-[2.5rem] shadow-sm flex flex-col justify-between min-h-[220px]">
                            <div class="flex justify-between items-start">
                                <div>
                                    <label class="text-xs font-bold text-on-surface-variant uppercase tracking-[0.2em]">Masa Magra</label>
                                    <div class="mt-2 flex items-baseline gap-2">
                                        <div
                                            class="font-headline font-extrabold text-6xl"
                                            :class="leanMass !== null ? 'text-tertiary' : 'text-surface-dim'"
                                        >{{ leanMass !== null ? leanMass : '--.-' }}</div>
                                        <span class="font-headline font-bold text-2xl text-on-surface-variant">kg</span>
                                    </div>
                                </div>
                                <div class="bg-tertiary-container p-3 rounded-2xl">
                                    <span class="material-symbols-outlined text-on-tertiary-container text-3xl">monitoring</span>
                                </div>
                            </div>
                            <div class="mt-4 text-xs text-on-surface-variant">
                                <span v-if="leanMass !== null">Peso × (1 − % Grasa / 100)</span>
                                <span v-else>Requiere peso y % de grasa corporal</span>
                            </div>
                        </div>

                        <!-- IMC (calculado) -->
                        <div class="bg-surface-container-lowest p-8 rounded-[2.5rem] shadow-sm flex flex-col justify-between min-h-[220px]">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <label class="text-xs font-bold text-on-surface-variant uppercase tracking-[0.2em]">IMC (BMI)</label>
                                    <div class="mt-2 flex items-baseline gap-2">
                                        <div
                                            class="font-headline font-extrabold text-6xl"
                                            :class="bmi ? bmiColor : 'text-surface-dim'"
                                        >{{ bmi ?? '--.-' }}</div>
                                        <span class="font-headline font-bold text-2xl text-on-surface-variant">kg/m²</span>
                                    </div>
                                    <!-- Estatura -->
                                    <div class="mt-4 flex items-center gap-2">
                                        <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest whitespace-nowrap">Estatura</label>
                                        <input
                                            v-model="form.height"
                                            class="w-24 bg-transparent border-b-2 border-outline-variant focus:border-primary border-t-0 border-x-0 p-0 text-base font-bold text-on-surface focus:ring-0"
                                            type="number" min="50" max="250" step="0.5"
                                            placeholder="170"
                                        />
                                        <span class="text-[10px] text-on-surface-variant">cm</span>
                                    </div>
                                </div>
                                <div class="bg-surface-container-high p-3 rounded-2xl shrink-0">
                                    <span class="material-symbols-outlined text-on-surface-variant text-3xl">calculate</span>
                                </div>
                            </div>
                            <div class="mt-4 text-xs font-medium" :class="bmi ? bmiColor : 'text-on-surface-variant'">
                                {{ bmiLabel }}
                            </div>
                        </div>
                    </div>

                    <!-- Signos vitales y circunferencias -->
                    <section class="space-y-6">
                        <h3 class="font-headline font-bold text-xl text-on-surface">Signos Vitales y Circunferencias</h3>

                        <!-- Vitales -->
                        <div class="grid grid-cols-3 gap-6">
                            <div class="bg-surface-container-low p-5 rounded-2xl">
                                <label class="text-[10px] font-bold text-on-surface-variant uppercase block mb-2">Cintura (cm)</label>
                                <input
                                    v-model="form.waist"
                                    class="w-full bg-transparent border-b-2 border-outline-variant focus:border-primary border-t-0 border-x-0 p-0 text-xl font-headline font-bold focus:ring-0"
                                    type="number" min="30" max="300" step="0.5"
                                />
                            </div>
                            <div class="bg-surface-container-low p-5 rounded-2xl">
                                <label class="text-[10px] font-bold text-on-surface-variant uppercase block mb-2">Cadera (cm)</label>
                                <input
                                    v-model="form.hip"
                                    class="w-full bg-transparent border-b-2 border-outline-variant focus:border-primary border-t-0 border-x-0 p-0 text-xl font-headline font-bold focus:ring-0"
                                    type="number" min="30" max="300" step="0.5"
                                />
                            </div>
                            <div class="bg-surface-container-low p-5 rounded-2xl">
                                <label class="text-[10px] font-bold text-on-surface-variant uppercase block mb-2">Presión Arterial</label>
                                <input
                                    v-model="form.blood_pressure"
                                    class="w-full bg-transparent border-b-2 border-outline-variant focus:border-primary border-t-0 border-x-0 p-0 text-xl font-headline font-bold focus:ring-0"
                                    placeholder="120/80"
                                    type="text"
                                />
                            </div>
                        </div>

                        <!-- Circunferencias para Lee et al. -->
                        <div class="bg-surface-container-low p-5 rounded-2xl space-y-4">
                            <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Circunferencias (cm) — para masa muscular Lee et al.</p>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="text-[10px] font-bold text-on-surface-variant uppercase block mb-2">Brazo relajado</label>
                                    <input v-model="form.arm_circ" type="number" min="1" max="100" step="0.1"
                                        class="w-full bg-transparent border-b-2 border-outline-variant focus:border-primary border-t-0 border-x-0 p-0 text-xl font-headline font-bold focus:ring-0"
                                        placeholder="28.0" />
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-on-surface-variant uppercase block mb-2">Brazo contraído</label>
                                    <input v-model="form.arm_contracted" type="number" min="1" max="100" step="0.1"
                                        class="w-full bg-transparent border-b-2 border-outline-variant focus:border-primary border-t-0 border-x-0 p-0 text-xl font-headline font-bold focus:ring-0"
                                        placeholder="30.0" />
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-on-surface-variant uppercase block mb-2">Abdomen</label>
                                    <input v-model="form.abdomen_circ" type="number" min="30" max="300" step="0.5"
                                        class="w-full bg-transparent border-b-2 border-outline-variant focus:border-primary border-t-0 border-x-0 p-0 text-xl font-headline font-bold focus:ring-0"
                                        placeholder="85.0" />
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-on-surface-variant uppercase block mb-2">Muslo</label>
                                    <input v-model="form.thigh_circ" type="number" min="1" max="150" step="0.1"
                                        class="w-full bg-transparent border-b-2 border-outline-variant focus:border-primary border-t-0 border-x-0 p-0 text-xl font-headline font-bold focus:ring-0"
                                        placeholder="55.0" />
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-on-surface-variant uppercase block mb-2">Pantorrilla</label>
                                    <input v-model="form.calf_circ" type="number" min="1" max="100" step="0.1"
                                        class="w-full bg-transparent border-b-2 border-outline-variant focus:border-primary border-t-0 border-x-0 p-0 text-xl font-headline font-bold focus:ring-0"
                                        placeholder="36.0" />
                                </div>
                            </div>
                        </div>

                        <!-- Pliegues cutáneos (opcionales) -->
                        <div class="bg-surface-container-low p-5 rounded-2xl space-y-4">
                            <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Pliegues cutáneos (mm) <span class="normal-case font-normal">— opcionales, mejoran precisión Lee</span></p>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="text-[10px] font-bold text-on-surface-variant uppercase block mb-2">Tríceps</label>
                                    <input v-model="form.tricep_mm" type="number" min="1" max="80" step="0.1"
                                        class="w-full bg-transparent border-b-2 border-outline-variant focus:border-primary border-t-0 border-x-0 p-0 text-xl font-headline font-bold focus:ring-0"
                                        placeholder="—" />
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-on-surface-variant uppercase block mb-2">Muslo</label>
                                    <input v-model="form.thigh_mm" type="number" min="1" max="80" step="0.1"
                                        class="w-full bg-transparent border-b-2 border-outline-variant focus:border-primary border-t-0 border-x-0 p-0 text-xl font-headline font-bold focus:ring-0"
                                        placeholder="—" />
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-on-surface-variant uppercase block mb-2">Pantorrilla</label>
                                    <input v-model="form.calf_mm" type="number" min="1" max="80" step="0.1"
                                        class="w-full bg-transparent border-b-2 border-outline-variant focus:border-primary border-t-0 border-x-0 p-0 text-xl font-headline font-bold focus:ring-0"
                                        placeholder="—" />
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- ─── Plan Nutricional ─────────────────────────────── -->
                    <section class="space-y-6 pb-8">
                        <h3 class="font-headline font-bold text-xl text-on-surface flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
                            Plan Nutricional
                        </h3>

                        <!-- GEB — Gasto Energético Basal -->
                        <div class="bg-surface-container-lowest p-6 rounded-3xl space-y-4">
                            <div class="flex items-center justify-between">
                                <h4 class="font-headline font-bold text-sm text-on-surface">GEB — Gasto Energético Basal</h4>
                                <span class="text-[10px] text-on-surface-variant">Requiere: peso + talla + edad · Katch requiere además % grasa</span>
                            </div>
                            <div class="grid grid-cols-5 gap-3">
                                <div v-for="geb in [
                                    { label: 'Harris-Ben.', value: gebHarris },
                                    { label: 'Mifflin',     value: gebMifflin },
                                    { label: 'Owen',        value: gebOwen },
                                    { label: 'Katch',       value: gebKatch },
                                ]" :key="geb.label"
                                    class="bg-surface-container p-4 rounded-2xl text-center"
                                >
                                    <div class="text-[9px] font-bold text-on-surface-variant uppercase tracking-wider mb-2">{{ geb.label }}</div>
                                    <div class="font-headline font-bold text-xl" :class="geb.value ? 'text-on-surface' : 'text-outline-variant'">
                                        {{ geb.value ? geb.value.toLocaleString('es-MX') : '—' }}
                                    </div>
                                    <div class="text-[9px] text-on-surface-variant mt-1">kcal</div>
                                </div>

                                <!-- Promedio destacado -->
                                <div class="bg-primary-container p-4 rounded-2xl text-center">
                                    <div class="text-[9px] font-bold text-primary uppercase tracking-wider mb-2">Promedio</div>
                                    <div class="font-headline font-extrabold text-xl" :class="gebAverage ? 'text-on-primary-container' : 'text-outline-variant'">
                                        {{ gebAverage ? gebAverage.toLocaleString('es-MX') : '—' }}
                                    </div>
                                    <div class="text-[9px] text-on-primary-container mt-1">kcal / día</div>
                                </div>
                            </div>
                        </div>

                        <!-- Factor de Actividad + GET + Prescripción -->
                        <div class="bg-surface-container-lowest p-6 rounded-3xl space-y-6">

                            <!-- Factor de actividad -->
                            <div class="space-y-3">
                                <h4 class="font-headline font-bold text-sm text-on-surface">Factor de Actividad</h4>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        v-for="af in activityFactors" :key="af.value"
                                        type="button"
                                        class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex flex-col items-center gap-0.5"
                                        :class="form.activity_factor === af.value
                                            ? 'bg-primary text-on-primary shadow-md scale-105'
                                            : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high'"
                                        @click="form.activity_factor = af.value"
                                    >
                                        <span>{{ af.label }}</span>
                                        <span class="font-normal opacity-60 text-[9px]">×{{ af.value }}</span>
                                    </button>
                                </div>
                            </div>

                            <!-- GET y Prescripción -->
                            <div class="flex items-stretch gap-6">
                                <!-- GET -->
                                <div v-if="getTotal" class="bg-secondary-container px-8 py-5 rounded-2xl text-center shrink-0">
                                    <div class="text-[9px] font-bold text-on-secondary-container uppercase tracking-widest mb-1">GET Total</div>
                                    <div class="font-headline font-extrabold text-4xl text-on-secondary-container">
                                        {{ getTotal.toLocaleString('es-MX') }}
                                    </div>
                                    <div class="text-xs text-on-secondary-container opacity-70 mt-1">
                                        kcal / día
                                    </div>
                                    <div class="text-[9px] text-on-secondary-container opacity-50 mt-1">
                                        {{ gebAverage?.toLocaleString('es-MX') }} × {{ form.activity_factor }}
                                    </div>
                                </div>
                                <div v-else class="bg-surface-container px-8 py-5 rounded-2xl text-center text-outline-variant shrink-0 flex flex-col items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-2xl">bolt</span>
                                    <span class="text-xs">Ingresa peso para calcular GET</span>
                                </div>

                                <!-- Tipo de prescripción -->
                                <div class="flex-1 space-y-3">
                                    <h4 class="font-headline font-bold text-sm text-on-surface">Tipo de Prescripción</h4>
                                    <div class="flex gap-3 h-full">
                                        <button
                                            v-for="pt in prescriptionTypes" :key="pt.value"
                                            type="button"
                                            class="flex-1 py-3 px-4 rounded-2xl text-sm font-bold transition-all border-2 flex flex-col items-center gap-1"
                                            :class="form.prescription_type === pt.value
                                                ? pt.activeClass
                                                : 'bg-surface-container border-transparent text-on-surface-variant hover:bg-surface-container-high'"
                                            @click="form.prescription_type = pt.value"
                                        >
                                            <span>{{ pt.label }}</span>
                                            <span class="text-[10px] opacity-70">{{ pt.mod }}</span>
                                            <span v-if="prescribedKcal && form.prescription_type === pt.value" class="text-xs font-extrabold">
                                                {{ prescribedKcal.toLocaleString('es-MX') }} kcal
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Distribución de Macronutrientes -->
                        <div class="bg-surface-container-lowest p-6 rounded-3xl space-y-5">
                            <div class="flex items-center justify-between">
                                <h4 class="font-headline font-bold text-sm text-on-surface">Distribución de Macronutrientes</h4>
                                <div v-if="prescribedKcal" class="text-xs font-bold text-on-surface-variant">
                                    Prescripción: <span class="text-primary font-extrabold">{{ prescribedKcal.toLocaleString('es-MX') }} kcal</span>
                                </div>
                                <div v-else class="text-xs text-on-surface-variant">Requiere GET calculado</div>
                            </div>

                            <!-- Header -->
                            <div class="grid grid-cols-5 gap-3 text-[9px] font-bold text-on-surface-variant uppercase tracking-widest px-3">
                                <div>Macronutriente</div>
                                <div class="text-center">g / kg</div>
                                <div class="text-center">g total</div>
                                <div class="text-center">kcal</div>
                                <div class="text-center">%</div>
                            </div>

                            <!-- Proteínas -->
                            <div class="grid grid-cols-5 gap-3 items-center bg-secondary-container/10 p-3 rounded-2xl">
                                <div class="text-sm font-bold text-on-surface flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-secondary shrink-0"></span>Proteínas
                                </div>
                                <div>
                                    <input v-model.number="form.protein_gkg"
                                        type="number" min="0.5" max="5" step="0.1"
                                        class="w-full bg-surface-container-high border-none rounded-xl text-center text-sm font-bold focus:ring-2 focus:ring-secondary/30 py-2"
                                    />
                                </div>
                                <div class="text-center text-sm font-bold" :class="macros ? 'text-on-surface' : 'text-outline-variant'">{{ macros?.protein.g ?? '—' }}</div>
                                <div class="text-center text-sm font-bold" :class="macros ? 'text-on-surface' : 'text-outline-variant'">{{ macros ? macros.protein.kcal.toLocaleString('es-MX') : '—' }}</div>
                                <div class="text-center">
                                    <span v-if="macros" class="px-2 py-0.5 bg-secondary-container text-on-secondary-container text-xs font-bold rounded-full">{{ macros.protein.pct }}%</span>
                                    <span v-else class="text-outline-variant text-sm">—</span>
                                </div>
                            </div>

                            <!-- Lípidos -->
                            <div class="grid grid-cols-5 gap-3 items-center bg-tertiary-container/10 p-3 rounded-2xl">
                                <div class="text-sm font-bold text-on-surface flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-tertiary shrink-0"></span>Lípidos
                                </div>
                                <div>
                                    <input v-model.number="form.fat_gkg"
                                        type="number" min="0.3" max="3" step="0.1"
                                        class="w-full bg-surface-container-high border-none rounded-xl text-center text-sm font-bold focus:ring-2 focus:ring-tertiary/30 py-2"
                                    />
                                </div>
                                <div class="text-center text-sm font-bold" :class="macros ? 'text-on-surface' : 'text-outline-variant'">{{ macros?.fat.g ?? '—' }}</div>
                                <div class="text-center text-sm font-bold" :class="macros ? 'text-on-surface' : 'text-outline-variant'">{{ macros ? macros.fat.kcal.toLocaleString('es-MX') : '—' }}</div>
                                <div class="text-center">
                                    <span v-if="macros" class="px-2 py-0.5 bg-tertiary-container text-on-tertiary-container text-xs font-bold rounded-full">{{ macros.fat.pct }}%</span>
                                    <span v-else class="text-outline-variant text-sm">—</span>
                                </div>
                            </div>

                            <!-- Carbohidratos (calculado) -->
                            <div class="grid grid-cols-5 gap-3 items-center bg-primary-container/10 p-3 rounded-2xl">
                                <div class="text-sm font-bold text-on-surface flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-primary shrink-0"></span>
                                    Carbohidratos
                                    <span class="text-[9px] bg-surface-container px-1.5 py-0.5 rounded text-on-surface-variant">auto</span>
                                </div>
                                <div class="text-center text-sm font-bold" :class="macros ? 'text-on-surface' : 'text-outline-variant'">{{ macros?.carbs.gkg ?? '—' }}</div>
                                <div class="text-center text-sm font-bold" :class="macros ? 'text-on-surface' : 'text-outline-variant'">{{ macros?.carbs.g ?? '—' }}</div>
                                <div class="text-center text-sm font-bold" :class="macros ? 'text-on-surface' : 'text-outline-variant'">{{ macros ? macros.carbs.kcal.toLocaleString('es-MX') : '—' }}</div>
                                <div class="text-center">
                                    <span v-if="macros && macros.carbs.kcal > 0" class="px-2 py-0.5 bg-primary-container text-on-primary-container text-xs font-bold rounded-full">{{ macros.carbs.pct }}%</span>
                                    <span v-else class="text-outline-variant text-sm">—</span>
                                </div>
                            </div>

                            <!-- Total -->
                            <div class="grid grid-cols-5 gap-3 items-center border-t-2 border-surface-container-high pt-4 px-3">
                                <div class="text-xs font-extrabold text-on-surface uppercase tracking-wider">Total</div>
                                <div></div>
                                <div class="text-center text-sm font-extrabold text-on-surface">
                                    {{ macros ? (parseFloat(macros.protein.g) + parseFloat(macros.fat.g) + parseFloat(macros.carbs.g)).toFixed(0) + ' g' : '—' }}
                                </div>
                                <div class="text-center text-base font-extrabold text-primary">
                                    {{ prescribedKcal ? prescribedKcal.toLocaleString('es-MX') + ' kcal' : '—' }}
                                </div>
                                <div class="text-center text-xs font-bold text-on-surface-variant">
                                    {{ macros ? '100%' : '—' }}
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </main>

            <!-- ─── Right Panel: Notas y Objetivos ─────────────────────────────── -->
            <aside class="w-96 flex flex-col bg-surface border-l border-surface-container-high overflow-y-auto hide-scrollbar shrink-0">
                <div class="p-6 space-y-8">

                    <!-- Notas de la cita previa -->
                    <section v-if="appointment.notes" class="space-y-3">
                        <div class="flex items-center gap-2 text-on-surface-variant">
                            <span class="material-symbols-outlined text-lg">sticky_note_2</span>
                            <h2 class="font-headline font-bold text-sm uppercase tracking-widest">Preparación</h2>
                        </div>
                        <div class="bg-primary-container/20 rounded-2xl p-4 border border-primary/10">
                            <p class="text-sm leading-relaxed text-on-surface-variant">{{ appointment.notes }}</p>
                        </div>
                    </section>

                    <!-- Notas de la consulta -->
                    <section class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-on-surface">
                                <span class="material-symbols-outlined text-lg">edit_note</span>
                                <h2 class="font-headline font-bold text-sm uppercase tracking-widest">Notas de Consulta</h2>
                            </div>
                            <span class="text-xs text-on-surface-variant">{{ form.summary.length }}/5000</span>
                        </div>
                        <div class="relative">
                            <textarea
                                v-model="form.summary"
                                class="w-full bg-surface-container-lowest rounded-3xl border-none focus:ring-2 focus:ring-primary/20 p-5 text-sm leading-relaxed text-on-surface placeholder:text-on-surface-variant/40 shadow-sm resize-none"
                                placeholder="Comienza a escribir las observaciones clínicas..."
                                rows="12"
                                maxlength="5000"
                            ></textarea>
                        </div>
                    </section>

                    <!-- Estado rápido -->
                    <div class="pt-4">
                        <button
                            @click="openFinish"
                            class="w-full bg-on-surface text-surface-container-lowest rounded-2xl p-5 flex items-center justify-between hover:opacity-90 transition-opacity"
                        >
                            <div class="space-y-1 text-left">
                                <div class="text-[10px] font-bold uppercase opacity-60">Guardar y cerrar</div>
                                <div class="text-sm font-bold">Finalizar Consulta</div>
                            </div>
                            <span class="material-symbols-outlined text-primary-fixed" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        </button>
                    </div>
                </div>
            </aside>
        </div>

        <!-- ─── Modal: Confirmar finalización ─────────────────────────────────── -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="showFinishModal"
                    class="fixed inset-0 z-[60] flex items-center justify-center bg-on-surface/30 backdrop-blur-sm p-4"
                    @click.self="showFinishModal = false"
                >
                    <Transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="opacity-0 scale-90 translate-y-4"
                        enter-to-class="opacity-100 scale-100 translate-y-0"
                        appear
                    >
                        <div v-if="showFinishModal" class="w-full max-w-md bg-surface-container-lowest rounded-[2rem] shadow-2xl overflow-hidden">

                            <div class="px-8 pt-8 pb-6">
                                <div class="flex items-start gap-4 mb-6">
                                    <div class="w-12 h-12 rounded-2xl bg-primary-container flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-primary text-2xl" style="font-variation-settings: 'FILL' 1;">task_alt</span>
                                    </div>
                                    <div>
                                        <h3 class="font-headline text-xl font-extrabold text-on-surface">¿Finalizar la consulta?</h3>
                                        <p class="text-sm text-on-surface-variant mt-1">Se guardarán todas las mediciones y notas ingresadas.</p>
                                    </div>
                                </div>

                                <!-- Resumen de lo que se guardará -->
                                <div class="bg-surface-container-low rounded-2xl p-4 space-y-2 text-sm mb-6">
                                    <div class="flex justify-between">
                                        <span class="text-on-surface-variant">Paciente</span>
                                        <span class="font-bold">{{ patient.name }}</span>
                                    </div>
                                    <div v-if="form.weight" class="flex justify-between">
                                        <span class="text-on-surface-variant">Peso registrado</span>
                                        <span class="font-bold">{{ form.weight }} kg</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-on-surface-variant">Notas</span>
                                        <span class="font-bold">{{ form.summary ? form.summary.slice(0,30) + (form.summary.length > 30 ? '…' : '') : 'Sin notas' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-on-surface-variant">Duración</span>
                                        <span class="font-bold">{{ elapsed }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="px-8 py-6 bg-surface-container/20 flex flex-col-reverse sm:flex-row gap-3 justify-end">
                                <button
                                    type="button"
                                    @click="showFinishModal = false"
                                    :disabled="finishing"
                                    class="w-full sm:w-auto px-6 py-3.5 text-on-surface-variant font-bold rounded-2xl hover:bg-surface-container-high transition-all disabled:opacity-50"
                                >Seguir editando</button>
                                <button
                                    type="button"
                                    @click="submitFinish"
                                    :disabled="finishing"
                                    class="w-full sm:w-auto px-8 py-3.5 bg-primary text-on-primary font-bold rounded-2xl shadow-lg shadow-primary/20 hover:opacity-90 hover:scale-[1.02] active:scale-95 transition-all disabled:opacity-60 flex items-center justify-center gap-2"
                                >
                                    <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">
                                        {{ finishing ? 'hourglass_empty' : 'check_circle' }}
                                    </span>
                                    {{ finishing ? 'Guardando...' : 'Confirmar y cerrar' }}
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    appointment:   { type: Object, required: true },
    patient:       { type: Object, required: true },
    previousVisit: { type: Object, default: null },
});

// ─── Cronómetro ───────────────────────────────────────────────────────────────
const startedAt = new Date(props.appointment.startedAt);
const elapsed   = ref('00:00');
let timer = null;

function updateElapsed() {
    const diff = Math.floor((Date.now() - startedAt.getTime()) / 1000);
    const h    = Math.floor(diff / 3600);
    const m    = Math.floor((diff % 3600) / 60);
    const s    = diff % 60;
    if (h > 0) {
        elapsed.value = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    } else {
        elapsed.value = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    }
}

onMounted(() => {
    updateElapsed();
    timer = setInterval(updateElapsed, 1000);
});

onUnmounted(() => clearInterval(timer));

// ─── Formulario de mediciones ─────────────────────────────────────────────────
const form = ref({
    weight:            '',
    height:            props.patient.height ?? '',
    body_fat:          '',
    muscle_mass:       '',
    waist:             '',
    hip:               '',
    blood_pressure:    '',
    summary:           '',
    // Plan nutricional
    activity_factor:   props.patient.activityFactor ?? 1.375,
    prescription_type: 'maintenance',
    protein_gkg:       2.0,
    fat_gkg:           0.8,
    // Circunferencias y pliegues Lee et al. (2000)
    arm_circ:          '',
    arm_contracted:    '',
    abdomen_circ:      '',
    thigh_circ:        '',
    calf_circ:         '',
    tricep_mm:         '',
    thigh_mm:          '',
    calf_mm:           '',
});

// ─── Constantes de plan nutricional ──────────────────────────────────────────
const activityFactors = [
    { value: 1.2,   label: 'Sedentario',  desc: 'Sin ejercicio' },
    { value: 1.375, label: 'Ligero',      desc: '1-3 días/semana' },
    { value: 1.55,  label: 'Moderado',    desc: '3-5 días/semana' },
    { value: 1.725, label: 'Activo',      desc: '6-7 días/semana' },
    { value: 1.9,   label: 'Muy Activo',  desc: 'Intenso diario' },
];

const prescriptionTypes = [
    { value: 'maintenance', label: 'Mantenimiento', mod: '',     activeClass: 'bg-primary-container border-primary text-on-primary-container' },
    { value: 'bulk',        label: 'Volumen',        mod: '+10%', activeClass: 'bg-tertiary-container border-tertiary text-on-tertiary-container' },
    { value: 'cut',         label: 'Definición',     mod: '-10%', activeClass: 'bg-secondary-container border-secondary text-on-secondary-container' },
];

// ─── IMC calculado ────────────────────────────────────────────────────────────
const bmi = computed(() => {
    const w = parseFloat(form.value.weight);
    const h = parseFloat(form.value.height);
    if (!w || !h) return null;
    const hm = h / 100;
    return (w / (hm * hm)).toFixed(1);
});

const bmiColor = computed(() => {
    const v = parseFloat(bmi.value);
    if (!v) return 'text-surface-dim';
    if (v < 18.5) return 'text-tertiary';
    if (v < 25)   return 'text-primary';
    if (v < 30)   return 'text-secondary';
    return 'text-error';
});

const bmiLabel = computed(() => {
    const v = parseFloat(bmi.value);
    if (!v) return 'Calculado automáticamente a partir de peso/estatura';
    if (v < 18.5) return `IMC ${bmi.value} — Bajo peso`;
    if (v < 25)   return `IMC ${bmi.value} — Peso normal ✓`;
    if (v < 30)   return `IMC ${bmi.value} — Sobrepeso`;
    return `IMC ${bmi.value} — Obesidad`;
});

// ─── Diferencia de peso ───────────────────────────────────────────────────────
const weightDiff = computed(() => {
    const current  = parseFloat(form.value.weight);
    const previous = parseFloat(props.previousVisit?.weight ?? 0);
    if (!current || !previous) return 0;
    return current - previous;
});

// ─── Plan Nutricional: GEB / GET / Macros ────────────────────────────────────
const patientAge = computed(() => {
    if (!props.patient.birthDate) return null;
    const birth = new Date(props.patient.birthDate);
    const today = new Date();
    let age = today.getFullYear() - birth.getFullYear();
    const m = today.getMonth() - birth.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
    return age;
});

const isFemale = computed(() => props.patient.gender !== 'male');

const gebHarris = computed(() => {
    const w = parseFloat(form.value.weight);
    const h = parseFloat(form.value.height);
    const a = patientAge.value;
    if (!w || !h || !a) return null;
    return isFemale.value
        ? Math.round(655 + (9.6 * w) + (1.8 * h) - (4.7 * a))
        : Math.round(65.5 + (13.75 * w) + (5.003 * h) - (6.775 * a));
});

const gebMifflin = computed(() => {
    const w = parseFloat(form.value.weight);
    const h = parseFloat(form.value.height);
    const a = patientAge.value;
    if (!w || !h || !a) return null;
    return isFemale.value
        ? Math.round((10 * w) + (6.25 * h) - (5 * a) - 161)
        : Math.round((10 * w) + (6.25 * h) - (5 * a) + 5);
});

const gebOwen = computed(() => {
    const w = parseFloat(form.value.weight);
    if (!w) return null;
    return isFemale.value
        ? Math.round(795 + (7.18 * w))
        : Math.round(879 + (10.2 * w));
});

const gebKatch = computed(() => {
    const w  = parseFloat(form.value.weight);
    const bf = parseFloat(form.value.body_fat);
    if (!w || !bf) return null;
    const leanMass = w * (1 - bf / 100);
    return Math.round(370 + (21.6 * leanMass));
});

const gebAverage = computed(() => {
    const vals = [gebHarris.value, gebMifflin.value, gebOwen.value, gebKatch.value].filter(v => v !== null);
    if (!vals.length) return null;
    return Math.round(vals.reduce((a, b) => a + b, 0) / vals.length);
});

const getTotal = computed(() => {
    if (!gebAverage.value) return null;
    return Math.round(gebAverage.value * parseFloat(form.value.activity_factor));
});

const prescribedKcal = computed(() => {
    if (!getTotal.value) return null;
    if (form.value.prescription_type === 'bulk') return Math.round(getTotal.value * 1.10);
    if (form.value.prescription_type === 'cut')  return Math.round(getTotal.value * 0.90);
    return getTotal.value;
});

const macros = computed(() => {
    const w    = parseFloat(form.value.weight);
    const kcal = prescribedKcal.value;
    const proGkg = parseFloat(form.value.protein_gkg) || 0;
    const lipGkg = parseFloat(form.value.fat_gkg)     || 0;
    if (!w || !kcal) return null;

    const proG    = proGkg * w;
    const proKcal = proG * 4;
    const lipG    = lipGkg * w;
    const lipKcal = lipG * 9;
    const choKcal = Math.max(0, kcal - proKcal - lipKcal);
    const choG    = choKcal / 4;

    return {
        protein: { gkg: proGkg,                  g: proG.toFixed(1),  kcal: Math.round(proKcal), pct: Math.round(proKcal / kcal * 100) },
        fat:     { gkg: lipGkg,                  g: lipG.toFixed(1),  kcal: Math.round(lipKcal), pct: Math.round(lipKcal / kcal * 100) },
        carbs:   { gkg: (choG / w).toFixed(2),   g: choG.toFixed(1),  kcal: Math.round(choKcal), pct: Math.round(choKcal / kcal * 100) },
    };
});

// ─── Masa Magra ───────────────────────────────────────────────────────────────
// Fórmula: Masa Magra = Peso(kg) - (% Grasa / 100 × Peso(kg))
//                     = Peso × (1 - %Grasa / 100)
const leanMass = computed(() => {
    const w  = parseFloat(form.value.weight);
    const bf = parseFloat(form.value.body_fat);
    if (!w || !bf) return null;
    return parseFloat((w * (1 - bf / 100)).toFixed(2));
});

// ─── Modal de finalización ────────────────────────────────────────────────────
const showFinishModal = ref(false);
const finishing       = ref(false);

function openFinish() {
    showFinishModal.value = true;
}

function submitFinish() {
    finishing.value = true;
    router.post(route('citas.finish', props.appointment.id), {
        weight:         form.value.weight       || null,
        height:         form.value.height       || null,
        body_fat:       form.value.body_fat     || null,
        muscle_mass:    leanMass.value          || null,
        waist:          form.value.waist        || null,
        hip:            form.value.hip          || null,
        blood_pressure: form.value.blood_pressure || null,
        summary:        form.value.summary      || null,
        // Plan nutricional
        activity_factor:   form.value.activity_factor   || null,
        prescription_type: form.value.prescription_type || 'maintenance',
        prescribed_kcal:   prescribedKcal.value         || null,
        protein_gkg:       form.value.protein_gkg       || null,
        fat_gkg:           form.value.fat_gkg           || null,
        // GEB calculado
        geb_harris:  gebHarris.value,
        geb_mifflin: gebMifflin.value,
        geb_owen:    gebOwen.value,
        geb_katch:   gebKatch.value,
        geb_average: gebAverage.value,
        get_total:   getTotal.value,
        // Circunferencias y pliegues Lee et al. (2000)
        arm_circ:        form.value.arm_circ        || null,
        arm_contracted:  form.value.arm_contracted  || null,
        abdomen_circ:    form.value.abdomen_circ    || null,
        thigh_circ:      form.value.thigh_circ      || null,
        calf_circ:       form.value.calf_circ   || null,
        tricep_skinfold: form.value.tricep_mm   || null,
        thigh_skinfold:  form.value.thigh_mm    || null,
        calf_skinfold:   form.value.calf_mm     || null,
        muscle_mass_method: leanMass.value ? 'lean_mass_formula' : null,
    }, {
        onFinish: () => { finishing.value = false; },
    });
}

// ─── Helpers de fecha ─────────────────────────────────────────────────────────
function formatDate(iso) {
    return new Date(iso).toLocaleDateString('es-MX', {
        day: 'numeric', month: 'long', year: 'numeric',
    }).replace(/^\w/, c => c.toUpperCase());
}

function formatDateFull(iso) {
    return new Date(iso).toLocaleDateString('es-MX', {
        weekday: 'long', day: 'numeric', month: 'long',
    }).replace(/^\w/, c => c.toUpperCase());
}
</script>

<style scoped>
.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
