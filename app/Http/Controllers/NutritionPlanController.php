<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class NutritionPlanController extends Controller
{
    public function index()
    {
        // Aquí se cargarían los planes alimenticios del nutriólogo autenticado
        // Por ahora retornamos datos de ejemplo

        return Inertia::render('NutritionPlans', [
            'patientName' => 'Carlos Fuentes',
            'planName' => 'Déficit Calórico Moderado',
            'planStatus' => 'Plan Activo',
            'weekNumber' => 12,
            'startDate' => 'Oct 23',
            'endDate' => 'Oct 29',
        ]);
    }

    public function show($id)
    {
        // Mostrar un plan específico
        return Inertia::render('NutritionPlans', [
            'patientName' => 'Carlos Fuentes',
            'planName' => 'Déficit Calórico Moderado',
            'planStatus' => 'Plan Activo',
            'weekNumber' => 12,
            'startDate' => 'Oct 23',
            'endDate' => 'Oct 29',
        ]);
    }

    public function store()
    {
        // Guardar un nuevo plan alimenticio
        // Validar datos, guardar en BD, etc.

        return back()->with('success', 'Plan alimenticio guardado exitosamente.');
    }

    public function update($id)
    {
        // Actualizar un plan existente

        return back()->with('success', 'Plan alimenticio actualizado exitosamente.');
    }

    public function destroy($id)
    {
        // Eliminar un plan

        return back()->with('success', 'Plan alimenticio eliminado.');
    }
}
