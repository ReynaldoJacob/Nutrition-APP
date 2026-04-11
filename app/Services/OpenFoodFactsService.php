<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class OpenFoodFactsService
{
    private string $baseUrl;
    private bool $isStaging;

    public function __construct()
    {
        $this->isStaging = (bool) config('services.openfoodfacts.staging', false);
        $default = $this->isStaging
            ? 'https://world.openfoodfacts.net'
            : 'https://world.openfoodfacts.org';
        $this->baseUrl = rtrim(config('services.openfoodfacts.base_url') ?? $default, '/');
    }

    public function searchMexico(string $query, int $pageSize = 12): array
    {
        $appName    = (string) config('app.name', 'NutriApp');
        $appVersion = (string) config('app.version', '1.0');
        $appContact = (string) config('services.openfoodfacts.contact', config('mail.from.address', 'app@example.com'));
        $userAgent  = "{$appName}/{$appVersion} ({$appContact})";

        $request = Http::timeout(15)
            ->acceptJson()
            ->withHeaders(['User-Agent' => $userAgent]);

        if ($this->isStaging) {
            $request = $request->withBasicAuth('off', 'off');
        }

        $response = $request->get($this->baseUrl . '/api/v2/search', [
                'search_terms' => $query,
                'countries_tags' => 'mexico',
                'page_size' => max(1, min($pageSize, 25)),
                'fields' => 'code,product_name,product_name_es,generic_name,generic_name_es,brands,nutriments,countries_tags',
            ]);

        if ($response->status() === 503) {
            throw new \RuntimeException('El servicio de Open Food Facts no esta disponible en este momento. Intenta mas tarde.');
        }

        $response->throw();

        $products = $response->json('products', []);
        if (!is_array($products)) {
            return [];
        }

        return array_values(array_filter(array_map(fn (array $item) => $this->mapProduct($item), $products)));
    }

    private function mapProduct(array $item): ?array
    {
        $code = trim((string) Arr::get($item, 'code', ''));
        if ($code === '') {
            return null;
        }

        $name = trim((string) (
            Arr::get($item, 'product_name_es')
            ?? Arr::get($item, 'product_name')
            ?? Arr::get($item, 'generic_name_es')
            ?? Arr::get($item, 'generic_name')
            ?? ''
        ));

        if ($name === '') {
            return null;
        }

        $nutriments = Arr::get($item, 'nutriments', []);

        return [
            'external_id' => $code,
            'name' => $name,
            'name_es' => (string) (Arr::get($item, 'product_name_es') ?: $name),
            'brand' => Arr::get($item, 'brands') ? (string) Arr::get($item, 'brands') : null,
            'serving_size' => 100,
            'serving_unit' => 'g',
            'calories' => (float) (Arr::get($nutriments, 'energy-kcal_100g') ?? Arr::get($nutriments, 'energy-kcal') ?? 0),
            'protein' => (float) (Arr::get($nutriments, 'proteins_100g') ?? Arr::get($nutriments, 'proteins') ?? 0),
            'carbs' => (float) (Arr::get($nutriments, 'carbohydrates_100g') ?? Arr::get($nutriments, 'carbohydrates') ?? 0),
            'fat' => (float) (Arr::get($nutriments, 'fat_100g') ?? Arr::get($nutriments, 'fat') ?? 0),
            'raw_payload' => [
                'code' => $code,
                'countries_tags' => Arr::get($item, 'countries_tags', []),
                'product_name' => Arr::get($item, 'product_name'),
                'product_name_es' => Arr::get($item, 'product_name_es'),
                'brands' => Arr::get($item, 'brands'),
                'nutriments' => $nutriments,
            ],
        ];
    }
}
