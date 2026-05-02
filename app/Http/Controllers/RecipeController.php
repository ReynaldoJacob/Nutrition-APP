<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        $nutritionistId = $request->user()->id;

        $recipes = Recipe::where('nutritionist_id', $nutritionistId)
            ->with('ingredients')
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($r) => [
                'id'              => $r->id,
                'name'            => $r->name,
                'description'     => $r->description,
                'image_url'       => $r->image_url,
                'meal_type'       => $r->meal_type,
                'diet_type'       => $r->diet_type,
                'preparation_time' => $r->preparation_time,
                'servings'        => $r->servings,
                'instructions'    => $r->instructions,
                'calories'        => (float) $r->calories,
                'protein'         => (float) $r->protein,
                'fats'            => (float) $r->fats,
                'carbs'           => (float) $r->carbs,
                'is_featured'     => $r->is_featured,
                'ingredient_count' => $r->ingredients->count(),
            ]);

        $mealTypeLabels = [
            'breakfast' => 'Desayuno',
            'lunch'     => 'Almuerzo',
            'dinner'    => 'Cena',
            'snack'     => 'Refrigerio',
            'dessert'   => 'Postre',
            'beverage'  => 'Bebida',
        ];

        return Inertia::render('Library/Recipes', [
            'recipes' => $recipes,
            'mealTypeLabels' => $mealTypeLabels,
        ]);
    }

    public function create()
    {
        return Inertia::render('Library/RecipeForm', [
            'ingredients' => Ingredient::where('nutritionist_id', auth()->id())
                ->get(['id', 'name', 'category', 'unit_of_measure'])
                ->groupBy('category'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string', 'max:1000'],
            'image_url'         => ['nullable', 'url'],
            'meal_type'         => ['nullable', 'in:breakfast,lunch,dinner,snack,dessert,beverage'],
            'diet_type'         => ['nullable', 'string', 'max:100'],
            'preparation_time'  => ['nullable', 'integer', 'min:1'],
            'servings'          => ['required', 'integer', 'min:1'],
            'instructions'      => ['nullable', 'string'],
            'calories'          => ['nullable', 'numeric', 'min:0'],
            'protein'           => ['nullable', 'numeric', 'min:0'],
            'fats'              => ['nullable', 'numeric', 'min:0'],
            'carbs'             => ['nullable', 'numeric', 'min:0'],
            'is_featured'       => ['boolean'],
            'ingredients'       => ['array'],
            'ingredients.*.id'  => ['required_with:ingredients', 'exists:ingredients,id'],
            'ingredients.*.quantity' => ['required_with:ingredients', 'numeric', 'min:0.01'],
            'ingredients.*.unit'     => ['required_with:ingredients', 'string', 'max:50'],
        ]);

        $recipe = Recipe::create([
            'nutritionist_id' => $request->user()->id,
            ...$validated,
        ]);

        // Attach ingredients
        if ($validated['ingredients'] ?? []) {
            foreach ($validated['ingredients'] as $ingredient) {
                $recipe->ingredients()->attach(
                    $ingredient['id'],
                    [
                        'quantity' => $ingredient['quantity'],
                        'unit'     => $ingredient['unit'],
                    ]
                );
            }
        }

        return redirect()->route('recipes.index')->with('success', "Receta '{$recipe->name}' creada exitosamente.");
    }

    public function show(Request $request, Recipe $recipe)
    {
        if ($recipe->nutritionist_id !== $request->user()->id) {
            abort(403, 'No autorizado');
        }

        $recipe->load('ingredients');

        return Inertia::render('Library/RecipeDetail', [
            'recipe' => [
                'id'              => $recipe->id,
                'name'            => $recipe->name,
                'description'     => $recipe->description,
                'image_url'       => $recipe->image_url,
                'meal_type'       => $recipe->meal_type,
                'diet_type'       => $recipe->diet_type,
                'preparation_time' => $recipe->preparation_time,
                'servings'        => $recipe->servings,
                'instructions'    => $recipe->instructions,
                'calories'        => (float) $recipe->calories,
                'protein'         => (float) $recipe->protein,
                'fats'            => (float) $recipe->fats,
                'carbs'           => (float) $recipe->carbs,
                'is_featured'     => $recipe->is_featured,
                'ingredients'     => $recipe->ingredients->map(fn($i) => [
                    'id'       => $i->id,
                    'name'     => $i->name,
                    'quantity' => $i->pivot->quantity,
                    'unit'     => $i->pivot->unit,
                ]),
            ],
        ]);
    }

    public function edit(Request $request, Recipe $recipe)
    {
        if ($recipe->nutritionist_id !== $request->user()->id) {
            abort(403, 'No autorizado');
        }

        $recipe->load('ingredients');

        return Inertia::render('Library/RecipeForm', [
            'recipe' => $recipe,
            'ingredients' => Ingredient::where('nutritionist_id', $request->user()->id)
                ->get(['id', 'name', 'category', 'unit_of_measure'])
                ->groupBy('category'),
        ]);
    }

    public function update(Request $request, Recipe $recipe)
    {
        if ($recipe->nutritionist_id !== $request->user()->id) {
            abort(403, 'No autorizado');
        }

        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string', 'max:1000'],
            'image_url'         => ['nullable', 'url'],
            'meal_type'         => ['nullable', 'in:breakfast,lunch,dinner,snack,dessert,beverage'],
            'diet_type'         => ['nullable', 'string', 'max:100'],
            'preparation_time'  => ['nullable', 'integer', 'min:1'],
            'servings'          => ['required', 'integer', 'min:1'],
            'instructions'      => ['nullable', 'string'],
            'calories'          => ['nullable', 'numeric', 'min:0'],
            'protein'           => ['nullable', 'numeric', 'min:0'],
            'fats'              => ['nullable', 'numeric', 'min:0'],
            'carbs'             => ['nullable', 'numeric', 'min:0'],
            'is_featured'       => ['boolean'],
            'ingredients'       => ['array'],
            'ingredients.*.id'  => ['required_with:ingredients', 'exists:ingredients,id'],
            'ingredients.*.quantity' => ['required_with:ingredients', 'numeric', 'min:0.01'],
            'ingredients.*.unit'     => ['required_with:ingredients', 'string', 'max:50'],
        ]);

        $recipe->update($validated);

        // Sync ingredients
        $ingredientData = [];
        foreach ($validated['ingredients'] ?? [] as $ingredient) {
            $ingredientData[$ingredient['id']] = [
                'quantity' => $ingredient['quantity'],
                'unit'     => $ingredient['unit'],
            ];
        }
        $recipe->ingredients()->sync($ingredientData);

        return redirect()->route('recipes.index')->with('success', "Receta actualizada exitosamente.");
    }

    public function destroy(Request $request, Recipe $recipe)
    {
        if ($recipe->nutritionist_id !== $request->user()->id) {
            abort(403, 'No autorizado');
        }

        $recipe->delete();

        return redirect()->route('recipes.index')->with('success', 'Receta eliminada exitosamente.');
    }
}
