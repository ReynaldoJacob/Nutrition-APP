<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Services\FatSecretPlatformService;
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

    public function searchFatSecret(Request $request, FatSecretPlatformService $fatSecret)
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'min:2', 'max:120'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        if (! $fatSecret->isConfigured()) {
            return response()->json([
                'message' => 'Credenciales de FatSecret no configuradas.',
            ], 422);
        }

        try {
            $results = $fatSecret->autocomplete($validated['query'], min($validated['limit'] ?? 8, 10));
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al consultar FatSecret.',
                'detail' => $e instanceof RequestException
                    ? ($e->response?->json('error.message') ?? $e->getMessage())
                    : $e->getMessage(),
            ], 502);
        }

        return response()->json([
            'data' => $results,
        ]);
    }

    public function importFromFatSecret(Request $request, FatSecretPlatformService $fatSecret)
    {
        return response()->json([
            'message' => 'La importacion de contenido externo esta deshabilitada por cumplimiento de terminos de FatSecret.',
        ], 403);
    }
}
