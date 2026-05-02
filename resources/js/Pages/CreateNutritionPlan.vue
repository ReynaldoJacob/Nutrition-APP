<template>
    <AppLayout>
        <div class="flex-1 p-8 lg:p-12 max-w-7xl mx-auto w-full">
            <!-- Wizard Header & Progress Bar -->
            <header class="mb-12">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
                    <div>
                        <h1 class="text-4xl font-extrabold font-headline text-on-surface tracking-tight mb-2">Crear Nuevo Plan Dietético</h1>
                        <p class="text-on-surface-variant font-medium">Paciente: <span class="text-primary font-bold">{{ patient.name }}</span> • {{ currentStepLabel }}</p>
                    </div>
                    <div class="flex items-center gap-3 px-4 py-2 bg-secondary-container rounded-full text-on-secondary-container text-sm font-semibold">
                        <span class="material-symbols-outlined text-lg">medical_services</span>
                        Objetivo: {{ patient.goal }}
                    </div>
                </div>
                <!-- Progress Bar -->
                <div class="relative px-2">
                    <div class="absolute top-1/2 left-0 w-full h-1.5 bg-surface-container-high -translate-y-1/2 rounded-full"></div>
                    <div class="absolute top-1/2 left-0 h-1.5 bg-primary -translate-y-1/2 rounded-full transition-all duration-500" :style="{ width: `${(currentStep / 4) * 100}%` }"></div>
                    <div class="relative flex justify-between">
                        <!-- Step 1 -->
                        <div class="flex flex-col items-center group">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center z-10 shadow-lg ring-4 ring-background transition-all" :class="currentStep >= 1 ? 'bg-primary text-on-primary shadow-primary/20' : 'bg-surface-container-high text-on-surface-variant'">
                                <span class="material-symbols-outlined" :class="{ 'step-active': currentStep >= 1 }">calculate</span>
                            </div>
                            <span class="absolute -bottom-7 text-xs font-bold whitespace-nowrap transition-colors" :class="currentStep >= 1 ? 'text-primary' : 'text-on-surface-variant'">1. Dietocálculo</span>
                        </div>
                        <!-- Step 2 -->
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center z-10 ring-4 ring-background transition-all" :class="currentStep >= 2 ? 'bg-primary text-on-primary shadow-lg shadow-primary/20' : 'bg-surface-container-high text-on-surface-variant'">
                                <span class="material-symbols-outlined" :class="{ 'step-active': currentStep >= 2 }">restaurant_menu</span>
                            </div>
                            <span class="absolute -bottom-7 text-xs font-medium whitespace-nowrap transition-colors" :class="currentStep >= 2 ? 'text-primary' : 'text-on-surface-variant'">2. Porciones</span>
                        </div>
                        <!-- Step 3 -->
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center z-10 ring-4 ring-background transition-all" :class="currentStep >= 3 ? 'bg-primary text-on-primary shadow-lg shadow-primary/20' : 'bg-surface-container-high text-on-surface-variant'">
                                <span class="material-symbols-outlined" :class="{ 'step-active': currentStep >= 3 }">schedule</span>
                            </div>
                            <span class="absolute -bottom-7 text-xs font-medium whitespace-nowrap transition-colors" :class="currentStep >= 3 ? 'text-primary' : 'text-on-surface-variant'">3. Distribución</span>
                        </div>
                        <!-- Step 4 -->
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center z-10 ring-4 ring-background transition-all" :class="currentStep >= 4 ? 'bg-primary text-on-primary shadow-lg shadow-primary/20' : 'bg-surface-container-high text-on-surface-variant'">
                                <span class="material-symbols-outlined" :class="{ 'step-active': currentStep >= 4 }">check_circle</span>
                            </div>
                            <span class="absolute -bottom-7 text-xs font-medium whitespace-nowrap transition-colors" :class="currentStep >= 4 ? 'text-primary' : 'text-on-surface-variant'">4. Finalizar</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Step 1: Dietocálculo -->
                <template v-if="currentStep === 1">
                    <!-- Left Column: Inputs -->
                    <div class="lg:col-span-7 space-y-8">
                        <!-- Section: Datos Basales -->
                    <section class="bg-surface-container-lowest p-8 rounded-2xl shadow-sm border-none">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-1 bg-primary h-6 rounded-full"></div>
                            <h2 class="text-xl font-bold font-headline">Datos Basales</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-on-surface-variant px-1">Peso Actual (kg)</label>
                                <div class="relative">
                                    <input v-model.number="formData.weight" class="w-full bg-surface-container-low border-none rounded-xl p-4 font-bold text-lg focus:ring-2 focus:ring-primary/20 transition-all outline-none" type="number" step="0.1"/>
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant font-medium">kg</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-on-surface-variant px-1">Estatura (cm)</label>
                                <div class="relative">
                                    <input v-model.number="formData.height" class="w-full bg-surface-container-low border-none rounded-xl p-4 font-bold text-lg focus:ring-2 focus:ring-primary/20 transition-all outline-none" type="number" step="0.1"/>
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant font-medium">cm</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-on-surface-variant px-1">Edad (años)</label>
                                <div class="relative">
                                    <input v-model.number="formData.age" class="w-full bg-surface-container-low border-none rounded-xl p-4 font-bold text-lg focus:ring-2 focus:ring-primary/20 transition-all outline-none" type="number" min="1" max="120"/>
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant font-medium">años</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-on-surface-variant px-1">Sexo</label>
                                <div class="relative">
                                    <select v-model="formData.gender" class="w-full bg-surface-container-low border-none rounded-xl p-4 font-bold text-base focus:ring-2 focus:ring-primary/20 transition-all outline-none appearance-none cursor-pointer">
                                        <option value="M">Masculino</option>
                                        <option value="F">Femenino</option>
                                    </select>
                                    <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-on-surface-variant px-1">Fórmula de Cálculo</label>
                                <div class="relative">
                                    <select v-model="formData.formula" class="w-full bg-surface-container-low border-none rounded-xl p-4 font-bold text-base focus:ring-2 focus:ring-primary/20 transition-all outline-none appearance-none cursor-pointer">
                                        <option value="harris-benedict">Harris-Benedict</option>
                                        <option value="mifflin">Mifflin-St Jeor</option>
                                        <option value="valencia">Valencia</option>
                                        <option value="oms">OMS</option>
                                    </select>
                                    <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                                </div>
                                <p class="text-[10px] text-primary px-1 font-medium italic">Considera edad, sexo y actividad física.</p>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-on-surface-variant px-1">Factor de Actividad</label>
                                <div class="relative">
                                    <select v-model.number="formData.activityFactor" class="w-full bg-surface-container-low border-none rounded-xl p-4 font-bold text-base focus:ring-2 focus:ring-primary/20 transition-all outline-none appearance-none cursor-pointer">
                                        <option :value="1.2">Sedentario (1.2)</option>
                                        <option :value="1.375">Ligero (1.375)</option>
                                        <option :value="1.55">Moderado (1.55)</option>
                                        <option :value="1.725">Activo (1.725)</option>
                                        <option :value="1.9">Muy Activo (1.9)</option>
                                    </select>
                                    <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                                </div>
                                <p class="text-[10px] text-on-surface-variant px-1 italic">Nivel de actividad diaria.</p>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-on-surface-variant px-1">Gasto Energético Basal (GEB)</label>
                                <div class="relative">
                                    <input :value="calculateGEB" class="w-full bg-primary-container/20 border-none rounded-xl p-4 font-bold text-lg text-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none" type="number" disabled/>
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-primary font-medium">kcal</span>
                                </div>
                                <p class="text-[10px] text-on-surface-variant px-1">Metabolismo basal sin actividad</p>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-on-surface-variant px-1">Gasto Energético Total (GET)</label>
                                <div class="relative">
                                    <input :value="calculateGET" class="w-full bg-primary-container/20 border-none rounded-xl p-4 font-bold text-lg text-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none" type="number" disabled/>
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-primary font-medium">kcal</span>
                                </div>
                                <p class="text-[10px] text-on-surface-variant px-1">GEB × Factor Actividad + 10% ETA (Efecto Térmico del Alimento)</p>
                            </div>
                        </div>
                    </section>

                    <!-- Section: Calculadora Macronutrientes -->
                    <section class="bg-surface-container-lowest p-8 rounded-2xl shadow-sm border-none">
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center gap-3">
                                <div class="w-1 bg-primary h-6 rounded-full"></div>
                                <h2 class="text-xl font-bold font-headline">Calculadora de Macronutrientes</h2>
                            </div>
                            <div class="flex items-center gap-2 px-3 py-1 bg-surface-container-high rounded-full">
                                <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Suma Total:</span>
                                <span class="text-sm font-extrabold" :class="totalMacros === 100 ? 'text-primary' : 'text-error'">{{ totalMacros }}%</span>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <!-- Proteínas -->
                            <div class="flex flex-wrap md:flex-nowrap items-center gap-4 p-4 rounded-xl bg-surface-container-low/50 border border-transparent hover:border-primary/10 transition-colors">
                                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-800">
                                    <span class="material-symbols-outlined">fitness_center</span>
                                </div>
                                <div class="flex-1 min-w-[120px]">
                                    <h3 class="font-bold text-on-surface">Proteínas</h3>
                                    <p class="text-xs text-on-surface-variant">Construcción muscular</p>
                                </div>
                                <div class="flex items-center gap-4 w-full md:w-auto">
                                    <div class="w-24 relative">
                                        <input v-model.number="formData.protein" class="w-full bg-white border-none rounded-lg p-2 text-center font-bold focus:ring-1 focus:ring-primary" type="number" min="0" max="100"/>
                                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-on-surface-variant">%</span>
                                    </div>
                                    <div class="w-24 bg-primary/10 px-3 py-2 rounded-lg text-center">
                                        <span class="text-sm font-extrabold text-primary">{{ proteinGrams }}g</span>
                                    </div>
                                    <div class="text-[10px] text-on-surface-variant font-medium">{{ (proteinGrams / formData.weight).toFixed(2) }} g/kg</div>
                                </div>
                            </div>

                            <!-- Grasas -->
                            <div class="flex flex-wrap md:flex-nowrap items-center gap-4 p-4 rounded-xl bg-surface-container-low/50 border border-transparent hover:border-primary/10 transition-colors">
                                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-800">
                                    <span class="material-symbols-outlined">opacity</span>
                                </div>
                                <div class="flex-1 min-w-[120px]">
                                    <h3 class="font-bold text-on-surface">Grasas</h3>
                                    <p class="text-xs text-on-surface-variant">Salud hormonal</p>
                                </div>
                                <div class="flex items-center gap-4 w-full md:w-auto">
                                    <div class="w-24 relative">
                                        <input v-model.number="formData.fat" class="w-full bg-white border-none rounded-lg p-2 text-center font-bold focus:ring-1 focus:ring-primary" type="number" min="0" max="100"/>
                                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-on-surface-variant">%</span>
                                    </div>
                                    <div class="w-24 bg-amber-100/30 px-3 py-2 rounded-lg text-center">
                                        <span class="text-sm font-extrabold text-amber-900">{{ fatGrams }}g</span>
                                    </div>
                                    <div class="text-[10px] text-on-surface-variant font-medium">{{ (fatGrams / formData.weight).toFixed(2) }} g/kg</div>
                                </div>
                            </div>

                            <!-- Carbohidratos -->
                            <div class="flex flex-wrap md:flex-nowrap items-center gap-4 p-4 rounded-xl bg-surface-container-low/50 border border-transparent hover:border-primary/10 transition-colors">
                                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-800">
                                    <span class="material-symbols-outlined">bolt</span>
                                </div>
                                <div class="flex-1 min-w-[120px]">
                                    <h3 class="font-bold text-on-surface">Carbohidratos</h3>
                                    <p class="text-xs text-on-surface-variant">Energía y rendimiento</p>
                                </div>
                                <div class="flex items-center gap-4 w-full md:w-auto">
                                    <div class="w-24 relative">
                                        <input v-model.number="formData.carbs" class="w-full bg-white border-none rounded-lg p-2 text-center font-bold focus:ring-1 focus:ring-primary" type="number" min="0" max="100"/>
                                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-on-surface-variant">%</span>
                                    </div>
                                    <div class="w-24 bg-blue-100/30 px-3 py-2 rounded-lg text-center">
                                        <span class="text-sm font-extrabold text-blue-900">{{ carbsGrams }}g</span>
                                    </div>
                                    <div class="text-[10px] text-on-surface-variant font-medium">{{ (carbsGrams / formData.weight).toFixed(2) }} g/kg</div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Right Column: Visualizations & Summary -->
                <div class="lg:col-span-5 space-y-8">
                    <!-- Distribution Chart Card -->
                    <section class="bg-surface-container-lowest p-8 rounded-2xl shadow-sm border-none relative overflow-hidden h-full flex flex-col">
                        <div class="flex items-center gap-3 mb-10">
                            <div class="w-1 bg-primary h-6 rounded-full"></div>
                            <h2 class="text-xl font-bold font-headline">Distribución Energética</h2>
                        </div>
                        <!-- Visual representation -->
                        <div class="flex-1 flex justify-center items-center py-10">
                            <div class="relative w-48 h-48 rounded-full flex items-center justify-center border-[16px]" style="border-top-color: #006d4e; border-right-color: #466089; border-bottom-color: #f59e0b; border-left-color: #006d4e;">
                                <div class="text-center">
                                    <span class="text-3xl font-extrabold text-on-surface block">{{ calculateGET }}</span>
                                    <p class="text-xs font-bold text-on-surface-variant tracking-widest uppercase">Kcal / Día</p>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 mt-auto pt-8">
                            <div class="text-center">
                                <div class="w-3 h-3 rounded-full bg-primary mx-auto mb-2"></div>
                                <span class="block text-lg font-bold">{{ formData.protein }}%</span>
                                <span class="text-[10px] uppercase font-bold text-on-surface-variant tracking-tighter">Proteínas</span>
                            </div>
                            <div class="text-center">
                                <div class="w-3 h-3 rounded-full bg-blue-500 mx-auto mb-2"></div>
                                <span class="block text-lg font-bold">{{ formData.carbs }}%</span>
                                <span class="text-[10px] uppercase font-bold text-on-surface-variant tracking-tighter">Carbos</span>
                            </div>
                            <div class="text-center">
                                <div class="w-3 h-3 rounded-full bg-amber-500 mx-auto mb-2"></div>
                                <span class="block text-lg font-bold">{{ formData.fat }}%</span>
                                <span class="text-[10px] uppercase font-bold text-on-surface-variant tracking-tighter">Grasas</span>
                            </div>
                        </div>
                        <!-- Info Tip -->
                        <div class="mt-12 p-4 bg-primary-container/20 rounded-xl flex gap-3">
                            <span class="material-symbols-outlined text-primary flex-shrink-0">lightbulb</span>
                            <p class="text-xs text-on-primary-container leading-relaxed">Esta distribución prioriza el superávit energético controlado, ideal para el desarrollo de masa muscular magra según el perfil del paciente.</p>
                        </div>
                    </section>
                </div>
                </template>

                <!-- Step 2: Porciones -->
                <template v-if="currentStep === 2">
                <!-- Left: Table Section -->
                <div class="lg:col-span-8">
                    <section class="bg-surface-container-lowest rounded-2xl shadow-sm border-none overflow-hidden">
                        <div class="p-6 border-b border-surface-container-low flex items-center justify-between">
                            <h3 class="text-xl font-bold font-headline">Sistema de Equivalentes</h3>
                            <span class="text-xs font-bold text-on-surface-variant bg-surface-container-high px-3 py-1 rounded-full uppercase tracking-wider">Detalle por Grupo</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-surface-container-low/30 text-on-surface-variant text-[10px] font-bold uppercase tracking-widest">
                                        <th class="p-4 pl-8">Grupo de Alimento</th>
                                        <th class="p-4 text-center">Porciones</th>
                                        <th class="p-4 text-right">Kcal</th>
                                        <th class="p-4 text-right">Proteína</th>
                                        <th class="p-4 text-right">Lípidos</th>
                                        <th class="p-4 text-right pr-8">HC</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-surface-container-low text-sm font-medium">
                                    <tr v-for="group in foodGroups" :key="group.id" class="hover:bg-emerald-50/30 transition-colors">
                                        <td class="p-4 pl-8 font-bold text-on-surface">{{ group.name }}</td>
                                        <td class="p-4">
                                            <input v-model.number="group.portions" class="w-16 mx-auto block bg-surface-container-low border-none rounded-xl text-center font-bold text-primary focus:ring-2 focus:ring-primary/20" type="number" min="0" />
                                        </td>
                                        <td class="p-4 text-right text-on-surface-variant">{{ group.portions * group.kcalPer }}</td>
                                        <td class="p-4 text-right text-on-surface-variant">{{ group.portions * group.proteinPer }}g</td>
                                        <td class="p-4 text-right text-on-surface-variant">{{ group.portions * group.fatPer }}g</td>
                                        <td class="p-4 text-right pr-8 text-on-surface-variant">{{ group.portions * group.carbsPer }}g</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-primary/5 font-bold">
                                        <td class="p-6 pl-8 text-primary font-headline">Subtotales</td>
                                        <td class="p-6 text-center text-primary">{{ totalPortions }}</td>
                                        <td class="p-6 text-right text-primary">{{ totalKcal }}</td>
                                        <td class="p-6 text-right text-primary">{{ totalProtein }}g</td>
                                        <td class="p-6 text-right text-primary">{{ totalFat }}g</td>
                                        <td class="p-6 text-right pr-8 text-primary">{{ totalCarbs }}g</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </section>
                </div>

                <!-- Right: Adequacy Cards -->
                <div class="lg:col-span-4 space-y-6">
                    <!-- Adecuación % Card -->
                    <section class="bg-surface-container-lowest p-8 rounded-2xl shadow-sm border-none">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-1 bg-primary h-6 rounded-full"></div>
                            <h4 class="text-lg font-bold font-headline">Adecuación %</h4>
                        </div>
                        <div class="space-y-6">
                            <!-- Energy -->
                            <div class="space-y-2">
                                <div class="flex justify-between text-xs font-bold font-headline">
                                    <span class="text-on-surface-variant">Energía</span>
                                    <span :class="energyAdequacy >= 95 && energyAdequacy <= 105 ? 'text-primary' : 'text-error'">{{ energyAdequacy }}%</span>
                                </div>
                                <div class="h-2 w-full bg-surface-container-high rounded-full overflow-hidden">
                                    <div class="h-full bg-primary rounded-full transition-all duration-700" :style="{ width: Math.min(energyAdequacy, 100) + '%' }"></div>
                                </div>
                                <p class="text-[10px] text-on-surface-variant text-right">{{ totalKcal }} / {{ calculateGET }} kcal</p>
                            </div>

                            <!-- Protein -->
                            <div class="space-y-2">
                                <div class="flex justify-between text-xs font-bold font-headline">
                                    <span class="text-on-surface-variant">Proteína</span>
                                    <span :class="proteinAdequacy >= 95 && proteinAdequacy <= 105 ? 'text-emerald-600' : 'text-error'">{{ proteinAdequacy }}%</span>
                                </div>
                                <div class="h-2 w-full bg-surface-container-high rounded-full overflow-hidden">
                                    <div class="h-full bg-emerald-500 rounded-full transition-all duration-700" :style="{ width: Math.min(proteinAdequacy, 100) + '%' }"></div>
                                </div>
                                <p class="text-[10px] text-on-surface-variant text-right">{{ totalProtein }} / {{ proteinTarget }}g</p>
                            </div>

                            <!-- Lipids -->
                            <div class="space-y-2">
                                <div class="flex justify-between text-xs font-bold font-headline">
                                    <span class="text-on-surface-variant">Lípidos</span>
                                    <span :class="fatAdequacy >= 95 && fatAdequacy <= 105 ? 'text-tertiary' : 'text-error'">{{ fatAdequacy }}%</span>
                                </div>
                                <div class="h-2 w-full bg-surface-container-high rounded-full overflow-hidden">
                                    <div class="h-full bg-tertiary rounded-full transition-all duration-700" :style="{ width: Math.min(fatAdequacy, 100) + '%' }"></div>
                                </div>
                                <p class="text-[10px] text-on-surface-variant text-right">{{ totalFat }} / {{ fatTarget }}g</p>
                            </div>

                            <!-- HC -->
                            <div class="space-y-2">
                                <div class="flex justify-between text-xs font-bold font-headline">
                                    <span class="text-on-surface-variant">HC</span>
                                    <span :class="carbsAdequacy >= 95 && carbsAdequacy <= 105 ? 'text-primary-dim' : 'text-error'">{{ carbsAdequacy }}%</span>
                                </div>
                                <div class="h-2 w-full bg-surface-container-high rounded-full overflow-hidden">
                                    <div class="h-full bg-primary-dim rounded-full transition-all duration-700" :style="{ width: Math.min(carbsAdequacy, 100) + '%' }"></div>
                                </div>
                                <p class="text-[10px] text-on-surface-variant text-right">{{ totalCarbs }} / {{ carbsTarget }}g</p>
                            </div>
                        </div>

                        <div class="mt-8 p-4 bg-primary/5 rounded-xl flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary text-xl" :style="{ 'font-variation-settings': 'FILL ' + (adequacyStatus === 'good' ? '1' : '0') }">{{ adequacyStatus === 'good' ? 'check_circle' : 'info' }}</span>
                            <span class="text-xs font-bold text-primary uppercase tracking-tight">{{ adequacyMessage }}</span>
                        </div>
                    </section>

                    <!-- Caloric Distribution Card -->
                    <section class="bg-surface-container-lowest p-8 rounded-2xl shadow-sm border-none relative overflow-hidden">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-1 bg-primary h-6 rounded-full"></div>
                            <h4 class="text-lg font-bold font-headline">Distribución Calórica</h4>
                        </div>
                        <div class="flex gap-4 items-center">
                            <div class="flex-1 space-y-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-primary"></div>
                                    <span class="text-xs font-bold text-on-surface">Proteínas: {{ calorieDistribution.protein }}%</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-tertiary"></div>
                                    <span class="text-xs font-bold text-on-surface">Lípidos: {{ calorieDistribution.fat }}%</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-primary-fixed-dim"></div>
                                    <span class="text-xs font-bold text-on-surface">HC: {{ calorieDistribution.carbs }}%</span>
                                </div>
                            </div>
                            <div class="relative w-24 h-24 flex-shrink-0 opacity-20">
                                <span class="material-symbols-outlined text-[96px] absolute -right-4 -bottom-4" style="font-variation-settings: 'FILL' 1;">pie_chart</span>
                            </div>
                        </div>
                    </section>
                </div>
                </template>
            </div>

            <!-- Footer Navigation -->
            <footer class="mt-12 pt-8 flex flex-col sm:flex-row items-center justify-between gap-6 border-t border-surface-container">
                <button @click="prevStep" v-if="currentStep > 1" class="flex items-center gap-2 text-on-surface-variant font-bold hover:text-on-surface transition-colors px-6 py-3">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Volver al Paso {{ currentStep - 1 }}
                </button>
                <Link v-else href="#" class="flex items-center gap-2 text-on-surface-variant font-bold hover:text-on-surface transition-colors px-6 py-3">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Cancelar Proceso
                </Link>
                <div class="flex items-center gap-4 w-full sm:w-auto">
                    <button @click="saveDraft" class="flex-1 sm:flex-none px-8 py-3 bg-surface-container-high text-on-surface-variant font-bold rounded-xl hover:bg-surface-variant transition-colors">
                        Guardar Borrador
                    </button>
                    <button v-if="currentStep < 4" @click="nextStep" class="flex-1 sm:flex-none px-10 py-4 bg-primary text-on-primary font-bold rounded-2xl shadow-xl shadow-primary/20 hover:bg-primary-dim transition-all active:scale-95 flex items-center justify-center gap-2">
                        Siguiente: {{ getNextStepLabel }}
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </button>
                    <button v-else @click="savePlan" class="flex-1 sm:flex-none px-10 py-4 bg-primary text-on-primary font-bold rounded-2xl shadow-xl shadow-primary/20 hover:bg-primary-dim transition-all active:scale-95 flex items-center justify-center gap-2">
                        Finalizar Plan
                        <span class="material-symbols-outlined">check_circle</span>
                    </button>
                </div>
            </footer>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    patient: { type: Object, required: true },
});

const currentStep = ref(1);
const formData = ref({
    weight: 78.5,
    height: 170,
    age: 35,
    gender: 'M',
    formula: 'harris-benedict',
    activityFactor: 1.55,
    protein: 25,
    fat: 30,
    carbs: 45,
});

const foodGroups = ref([
    { id: 1, name: 'Verduras', portions: 4, kcalPer: 25, proteinPer: 2, fatPer: 0, carbsPer: 4 },
    { id: 2, name: 'Frutas', portions: 3, kcalPer: 60, proteinPer: 0, fatPer: 0, carbsPer: 15 },
    { id: 3, name: 'Cereales y Tubérculos', portions: 6, kcalPer: 70, proteinPer: 2, fatPer: 0, carbsPer: 15 },
    { id: 4, name: 'Leguminosas', portions: 1, kcalPer: 120, proteinPer: 8, fatPer: 1, carbsPer: 20 },
    { id: 5, name: 'Origen Animal', portions: 5, kcalPer: 75, proteinPer: 7, fatPer: 5, carbsPer: 0 },
    { id: 6, name: 'Leche', portions: 1, kcalPer: 95, proteinPer: 9, fatPer: 2, carbsPer: 12 },
    { id: 7, name: 'Grasas', portions: 4, kcalPer: 45, proteinPer: 0, fatPer: 5, carbsPer: 0 },
]);

const calculateGEB = computed(() => {
    const w = formData.value.weight;
    const h = formData.value.height;
    const a = formData.value.age;
    const g = formData.value.gender;
    const f = formData.value.formula;

    let geb = 1840; // valor por defecto

    if (f === 'harris-benedict') {
        if (g === 'M') {
            geb = 88.362 + (13.397 * w) + (4.799 * h) - (5.677 * a);
        } else {
            geb = 447.593 + (9.247 * w) + (3.098 * h) - (4.330 * a);
        }
    } else if (f === 'mifflin') {
        if (g === 'M') {
            geb = (10 * w) + (6.25 * h) - (5 * a) + 5;
        } else {
            geb = (10 * w) + (6.25 * h) - (5 * a) - 161;
        }
    } else if (f === 'valencia') {
        geb = 24.2 * w;
    } else if (f === 'oms') {
        if (g === 'M') {
            geb = 88.32 + (13.4 * w) + (4.8 * h) - (5.68 * a);
        } else {
            geb = 135.25 + (6.75 * w) + (1.8 * h) - (4.7 * a);
        }
    }

    return Math.round(geb);
});

const calculateGET = computed(() => {
    const getWithoutETA = calculateGEB.value * formData.value.activityFactor;
    const eta = calculateGEB.value * 0.10; // 10% del GEB
    return Math.round(getWithoutETA + eta);
});

const proteinGrams = computed(() => {
    const totalCalories = calculateGET.value;
    const caloriesFromProtein = (totalCalories * formData.value.protein) / 100;
    return Math.round(caloriesFromProtein / 4); // 4 kcal por gramo de proteína
});

const fatGrams = computed(() => {
    const totalCalories = calculateGET.value;
    const caloriesFromFat = (totalCalories * formData.value.fat) / 100;
    return Math.round(caloriesFromFat / 9); // 9 kcal por gramo de grasa
});

const carbsGrams = computed(() => {
    const totalCalories = calculateGET.value;
    const caloriesFromCarbs = (totalCalories * formData.value.carbs) / 100;
    return Math.round(caloriesFromCarbs / 4); // 4 kcal por gramo de carbohidratos
});

const totalMacros = computed(() => {
    return formData.value.protein + formData.value.fat + formData.value.carbs;
});

// ---- STEP 2: Porciones - Computed Properties ----

// Totales de la tabla
const totalPortions = computed(() => {
    return foodGroups.value.reduce((sum, group) => sum + (group.portions || 0), 0);
});

const totalKcal = computed(() => {
    return Math.round(foodGroups.value.reduce((sum, group) => sum + (group.portions * group.kcalPer || 0), 0));
});

const totalProtein = computed(() => {
    return Math.round(foodGroups.value.reduce((sum, group) => sum + (group.portions * group.proteinPer || 0), 0));
});

const totalFat = computed(() => {
    return Math.round(foodGroups.value.reduce((sum, group) => sum + (group.portions * group.fatPer || 0), 0));
});

const totalCarbs = computed(() => {
    return Math.round(foodGroups.value.reduce((sum, group) => sum + (group.portions * group.carbsPer || 0), 0));
});

// Targets de la Step 1
const proteinTarget = computed(() => proteinGrams.value);
const fatTarget = computed(() => fatGrams.value);
const carbsTarget = computed(() => carbsGrams.value);

// Adecuación en porcentaje
const energyAdequacy = computed(() => {
    const get = calculateGET.value;
    return get > 0 ? Math.round((totalKcal.value / get) * 100) : 0;
});

const proteinAdequacy = computed(() => {
    const target = proteinTarget.value;
    return target > 0 ? Math.round((totalProtein.value / target) * 100) : 0;
});

const fatAdequacy = computed(() => {
    const target = fatTarget.value;
    return target > 0 ? Math.round((totalFat.value / target) * 100) : 0;
});

const carbsAdequacy = computed(() => {
    const target = carbsTarget.value;
    return target > 0 ? Math.round((totalCarbs.value / target) * 100) : 0;
});

// Estado de adecuación general
const adequacyStatus = computed(() => {
    const isEnergyOk = energyAdequacy.value >= 95 && energyAdequacy.value <= 105;
    const isProteinOk = proteinAdequacy.value >= 95 && proteinAdequacy.value <= 105;
    const isFatOk = fatAdequacy.value >= 95 && fatAdequacy.value <= 105;
    const isCarbsOk = carbsAdequacy.value >= 95 && carbsAdequacy.value <= 105;

    return (isEnergyOk && isProteinOk && isFatOk && isCarbsOk) ? 'good' : 'warning';
});

const adequacyMessage = computed(() => {
    if (adequacyStatus.value === 'good') {
        return 'Dentro de rangos aceptables';
    }
    return 'Ajusta las porciones para cumplir targets';
});

// Distribución calórica de los alimentos
const calorieDistribution = computed(() => {
    const proteinCals = totalProtein.value * 4;
    const fatCals = totalFat.value * 9;
    const carbsCals = totalCarbs.value * 4;
    const totalCals = proteinCals + fatCals + carbsCals || 1;

    return {
        protein: Math.round((proteinCals / totalCals) * 100),
        fat: Math.round((fatCals / totalCals) * 100),
        carbs: Math.round((carbsCals / totalCals) * 100),
    };
});

function nextStep() {
    if (currentStep.value < 4) {
        currentStep.value++;
    }
}

function prevStep() {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
}

function saveDraft() {
    // TODO: Implementar guardado de borrador
    console.log('Guardando borrador...', {
        step: currentStep.value,
        formData: formData.value,
        foodGroups: foodGroups.value,
    });
}

function savePlan() {
    // TODO: Implementar guardado del plan completo
    console.log('Guardando plan completo...', {
        formData: formData.value,
        foodGroups: foodGroups.value,
    });
}

const getNextStepLabel = computed(() => {
    const labels = {
        1: 'Porciones',
        2: 'Distribución',
        3: 'Finalizar',
    };
    return labels[currentStep.value] || 'Siguiente';
});

const currentStepLabel = computed(() => {
    const labels = {
        1: 'Paso 1: Dietocálculo',
        2: 'Paso 2: Asignación de Porciones',
        3: 'Paso 3: Distribución de Comidas',
        4: 'Paso 4: Revisión y Finalización',
    };
    return labels[currentStep.value] || 'Plan Terapéutico';
});
</script>

<style scoped>
.step-active {
    font-variation-settings: 'FILL' 1;
}
</style>
