<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class UsdaFoodDataCentralService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.usda.base_url', 'https://api.nal.usda.gov/fdc/v1'), '/');
        $this->apiKey = (string) config('services.usda.api_key', '');
    }

    public function isConfigured(): bool
    {
        return $this->apiKey !== '';
    }

    public function searchFoods(string $query, int $pageSize = 20): array
    {
        $response = Http::timeout(12)
            ->get($this->baseUrl . '/foods/search', [
                'api_key' => $this->apiKey,
                'query' => $query,
                'pageSize' => max(1, min($pageSize, 50)),
                'dataType' => ['Foundation', 'SR Legacy', 'Survey (FNDDS)'],
            ]);

        $response->throw();

        $foods = $response->json('foods', []);

        return array_map(fn (array $item) => $this->mapFoodSummary($item), $foods);
    }

    public function getFoodDetail(int $fdcId): array
    {
        $response = Http::timeout(12)
            ->get($this->baseUrl . '/food/' . $fdcId, [
                'api_key' => $this->apiKey,
            ]);

        $response->throw();

        return $this->mapFoodSummary((array) $response->json());
    }

    public function translateLabelToSpanish(string $text): string
    {
        return $this->translateToSpanish($text);
    }

    private function mapFoodSummary(array $item): array
    {
        $nutrients = Arr::get($item, 'foodNutrients', []);

        return [
            'external_id' => (string) Arr::get($item, 'fdcId', ''),
            'name' => (string) Arr::get($item, 'description', ''),
            'name_es' => $this->translateToSpanish((string) Arr::get($item, 'description', '')),
            'brand' => Arr::get($item, 'brandOwner') ?: Arr::get($item, 'brandName'),
            'serving_size' => (float) (Arr::get($item, 'servingSize') ?? 100),
            'serving_unit' => (string) (Arr::get($item, 'servingSizeUnit') ?? 'g'),
            'calories' => $this->extractNutrient($nutrients, [1008], ['Energy']),
            'protein' => $this->extractNutrient($nutrients, [1003], ['Protein']),
            'carbs' => $this->extractNutrient($nutrients, [1005], ['Carbohydrate, by difference', 'Carbohydrate']),
            'fat' => $this->extractNutrient($nutrients, [1004], ['Total lipid (fat)', 'Total Fat']),
            'raw_payload' => $item,
        ];
    }

    private function extractNutrient(array $nutrients, array $ids, array $names): float
    {
        foreach ($nutrients as $n) {
            $nutrientId = (int) (Arr::get($n, 'nutrientId') ?? Arr::get($n, 'nutrient.id') ?? 0);
            $nutrientName = (string) (Arr::get($n, 'nutrientName') ?? Arr::get($n, 'nutrient.name') ?? '');
            $value = Arr::get($n, 'value') ?? Arr::get($n, 'amount');

            if (in_array($nutrientId, $ids, true) || in_array($nutrientName, $names, true)) {
                return (float) ($value ?? 0);
            }
        }

        return 0.0;
    }

    private function translateToSpanish(string $text): string
    {
        $dictionary = [
            'chicken' => 'pollo',
            'beef' => 'res',
            'pork' => 'cerdo',
            'fish' => 'pescado',
            'salmon' => 'salmon',
            'tuna' => 'atun',
            'egg' => 'huevo',
            'eggs' => 'huevos',
            'milk' => 'leche',
            'cheese' => 'queso',
            'yogurt' => 'yogur',
            'rice' => 'arroz',
            'bread' => 'pan',
            'potato' => 'papa',
            'sweet potato' => 'camote',
            'beans' => 'frijoles',
            'lentils' => 'lentejas',
            'oats' => 'avena',
            'apple' => 'manzana',
            'banana' => 'platano',
            'orange' => 'naranja',
            'strawberry' => 'fresa',
            'blueberry' => 'arandano',
            'avocado' => 'aguacate',
            'lettuce' => 'lechuga',
            'tomato' => 'jitomate',
            'onion' => 'cebolla',
            'garlic' => 'ajo',
            'olive oil' => 'aceite de oliva',
            'oil' => 'aceite',
            'raw' => 'crudo',
            'cooked' => 'cocido',
            'boiled' => 'hervido',
            'grilled' => 'a la parrilla',
            'roasted' => 'asado',
            'without salt' => 'sin sal',
            'salted' => 'con sal',
            'fresh' => 'fresco',
            'frozen' => 'congelado',
        ];

        $translated = strtolower(trim($text));

        // Frases largas primero para evitar reemplazos parciales incorrectos.
        uksort($dictionary, fn ($a, $b) => strlen($b) <=> strlen($a));

        foreach ($dictionary as $en => $es) {
            $translated = preg_replace('/\b' . preg_quote($en, '/') . '\b/i', $es, $translated) ?? $translated;
        }

        return ucfirst($translated);
    }
}
