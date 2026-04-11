<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Services\OpenFoodFactsService;
use App\Services\UsdaFoodDataCentralService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;

class FoodCatalogController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'query' => ['nullable', 'string', 'max:120'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = $validated['query'] ?? null;
        $limit = $validated['limit'] ?? 20;

        $foodsQuery = Food::query()->orderBy('name');

        if ($query) {
            $foodsQuery->where(function ($subQuery) use ($query) {
                $subQuery->where('name', 'like', '%' . $query . '%')
                    ->orWhere('name_es', 'like', '%' . $query . '%');
            });
        }

        $foods = $foodsQuery->limit($limit)->get();

        return response()->json([
            'data' => $foods,
        ]);
    }

    public function searchOpenFoodFactsMexico(Request $request, OpenFoodFactsService $openFoodFacts)
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'min:2', 'max:120'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:25'],
        ]);

        try {
            $results = $openFoodFacts->searchMexico($validated['query'], (int) ($validated['limit'] ?? 12));
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json([
                'message' => 'No se pudo conectar con Open Food Facts. Verifica tu conexion o intenta mas tarde.',
            ], 502);
        } catch (RequestException $e) {
            $status = $e->response?->status();
            if ($status === 503) {
                return response()->json([
                    'message' => 'Open Food Facts no esta disponible en este momento. Intenta en unos minutos.',
                ], 503);
            }
            return response()->json([
                'message' => 'Error al consultar Open Food Facts Mexico.',
                'detail' => $e->response?->json('status_verbose') ?? 'HTTP ' . $status,
            ], 502);
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 503);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error inesperado al consultar Open Food Facts Mexico.',
            ], 500);
        }

        return response()->json([
            'data' => $results,
        ]);
    }

    public function searchUsda(Request $request, UsdaFoodDataCentralService $usda)
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'min:2', 'max:120'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:25'],
        ]);

        if (! $usda->isConfigured()) {
            return response()->json([
                'message' => 'La clave de USDA no esta configurada. Agrega USDA_API_KEY en tu .env.',
            ], 422);
        }

        try {
            $results = $usda->searchFoods($validated['query'], (int) ($validated['limit'] ?? 12));
        } catch (RequestException $e) {
            return response()->json([
                'message' => 'Error al consultar USDA FoodData Central.',
            ], 502);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error inesperado al consultar USDA.',
            ], 500);
        }

        return response()->json(['data' => $results]);
    }
}
