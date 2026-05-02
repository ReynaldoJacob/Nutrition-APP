<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IngredientController extends Controller
{
    public function index(Request $request)
    {
        $nutritionistId = $request->user()->id;

        $ingredients = Ingredient::where('nutritionist_id', $nutritionistId)
            ->orderBy('name')
            ->get()
            ->map(fn($i) => [
                'id'                  => $i->id,
                'name'                => $i->name,
                'description'         => $i->description,
                'category'            => $i->category,
                'unit_of_measure'     => $i->unit_of_measure,
                'calories_per_unit'   => (float) $i->calories_per_unit,
                'protein_per_unit'    => (float) $i->protein_per_unit,
                'fats_per_unit'       => (float) $i->fats_per_unit,
                'carbs_per_unit'      => (float) $i->carbs_per_unit,
            ]);

        $categoryLabels = [
            'protein'    => 'Proteína',
            'carbs'      => 'Carbohidratos',
            'fats'       => 'Grasas',
            'vegetables' => 'Verduras',
            'fruits'     => 'Frutas',
            'diary'      => 'Lácteos',
            'other'      => 'Otros',
        ];

        return Inertia::render('Library/Ingredients', [
            'ingredients' => $ingredients,
            'categoryLabels' => $categoryLabels,
        ]);
    }

    public function create()
    {
        return Inertia::render('Library/IngredientForm');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'description'         => ['nullable', 'string', 'max:500'],
            'category'            => ['nullable', 'string', 'max:100'],
            'unit_of_measure'     => ['required', 'string', 'max:50'],
            'calories_per_unit'   => ['nullable', 'numeric', 'min:0'],
            'protein_per_unit'    => ['nullable', 'numeric', 'min:0'],
            'fats_per_unit'       => ['nullable', 'numeric', 'min:0'],
            'carbs_per_unit'      => ['nullable', 'numeric', 'min:0'],
        ]);

        Ingredient::create([
            'nutritionist_id' => $request->user()->id,
            ...$validated,
        ]);

        return redirect()->route('ingredients.index')->with('success', "Ingrediente '{$validated['name']}' creado exitosamente.");
    }

    public function edit(Request $request, Ingredient $ingredient)
    {
        if ($ingredient->nutritionist_id !== $request->user()->id) {
            abort(403, 'No autorizado');
        }

        return Inertia::render('Library/IngredientForm', [
            'ingredient' => [
                'id'                  => $ingredient->id,
                'name'                => $ingredient->name,
                'description'         => $ingredient->description,
                'category'            => $ingredient->category,
                'unit_of_measure'     => $ingredient->unit_of_measure,
                'calories_per_unit'   => (float) $ingredient->calories_per_unit,
                'protein_per_unit'    => (float) $ingredient->protein_per_unit,
                'fats_per_unit'       => (float) $ingredient->fats_per_unit,
                'carbs_per_unit'      => (float) $ingredient->carbs_per_unit,
            ],
        ]);
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        if ($ingredient->nutritionist_id !== $request->user()->id) {
            abort(403, 'No autorizado');
        }

        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'description'         => ['nullable', 'string', 'max:500'],
            'category'            => ['nullable', 'string', 'max:100'],
            'unit_of_measure'     => ['required', 'string', 'max:50'],
            'calories_per_unit'   => ['nullable', 'numeric', 'min:0'],
            'protein_per_unit'    => ['nullable', 'numeric', 'min:0'],
            'fats_per_unit'       => ['nullable', 'numeric', 'min:0'],
            'carbs_per_unit'      => ['nullable', 'numeric', 'min:0'],
        ]);

        $ingredient->update($validated);

        return redirect()->route('ingredients.index')->with('success', 'Ingrediente actualizado exitosamente.');
    }

    public function destroy(Request $request, Ingredient $ingredient)
    {
        if ($ingredient->nutritionist_id !== $request->user()->id) {
            abort(403, 'No autorizado');
        }

        $ingredient->delete();

        return redirect()->route('ingredients.index')->with('success', 'Ingrediente eliminado exitosamente.');
    }
}
