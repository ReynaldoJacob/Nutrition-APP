<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class FatSecretPlatformService
{
    private string $tokenUrl;
    private string $apiUrl;
    private string $clientId;
    private string $clientSecret;
    private string $scope;

    public function __construct()
    {
        $this->tokenUrl = (string) config('services.fatsecret.token_url', 'https://oauth.fatsecret.com/connect/token');
        $this->apiUrl = (string) config('services.fatsecret.api_url', 'https://platform.fatsecret.com/rest/food/autocomplete/v2');
        $this->clientId = (string) config('services.fatsecret.client_id', '');
        $this->clientSecret = (string) config('services.fatsecret.client_secret', '');
        $this->scope = (string) config('services.fatsecret.scope', '');
    }

    public function isConfigured(): bool
    {
        return $this->clientId !== '' && $this->clientSecret !== '';
    }

    public function autocomplete(string $query, int $maxResults = 8, string $region = 'MX'): array
    {
        $token = $this->getAccessToken();

        $response = Http::timeout(12)
            ->withToken($token)
            ->acceptJson()
            ->get($this->apiUrl, [
                'expression' => $query,
                'max_results' => max(1, min($maxResults, 10)),
                'region' => $region,
                'format' => 'json',
            ]);

        $response->throw();

        $payload = $response->json();
        $apiErrorMessage = data_get($payload, 'error.message');
        if (is_string($apiErrorMessage) && $apiErrorMessage !== '') {
            throw new RuntimeException('FatSecret: ' . $apiErrorMessage);
        }

        $suggestions = $response->json('suggestions', []);

        if (is_array($suggestions) && array_key_exists('suggestion', $suggestions)) {
            $suggestions = $suggestions['suggestion'];
        }

        if (!is_array($suggestions)) {
            return [];
        }

        return array_values(array_filter(array_map(function ($item) {
            $label = is_array($item)
                ? (string) ($item['suggestion'] ?? $item['name'] ?? '')
                : (string) $item;

            $label = trim($label);
            if ($label === '') {
                return null;
            }

            return [
                'external_id' => 'fatsecret-suggestion-' . md5(Str::lower($label)),
                'name' => $label,
                'name_es' => $this->translateToSpanish($label),
                'brand' => null,
                'serving_size' => 100,
                'serving_unit' => 'g',
                'calories' => 0,
                'protein' => 0,
                'carbs' => 0,
                'fat' => 0,
                'raw_payload' => ['suggestion' => $label],
            ];
        }, $suggestions)));
    }

    public function buildImportFromSuggestion(string $name): array
    {
        $name = trim($name);

        return [
            'external_id' => 'fatsecret-suggestion-' . md5(Str::lower($name)),
            'name' => $name,
            'name_es' => $this->translateToSpanish($name),
            'brand' => null,
            'serving_size' => 100,
            'serving_unit' => 'g',
            'calories' => 0,
            'protein' => 0,
            'carbs' => 0,
            'fat' => 0,
            'raw_payload' => ['suggestion' => $name],
        ];
    }

    private function getAccessToken(): string
    {
        $payload = [
            'grant_type' => 'client_credentials',
        ];

        if (trim($this->scope) !== '') {
            $payload['scope'] = trim($this->scope);
        }

        $response = Http::asForm()
            ->withBasicAuth($this->clientId, $this->clientSecret)
            ->timeout(12)
            ->post($this->tokenUrl, $payload);

        if (
            $response->status() === 400
            && isset($payload['scope'])
            && str_contains((string) $response->body(), 'invalid_scope')
        ) {
            $response = Http::asForm()
                ->withBasicAuth($this->clientId, $this->clientSecret)
                ->timeout(12)
                ->post($this->tokenUrl, [
                    'grant_type' => 'client_credentials',
                ]);
        }

        if (
            $response->status() === 400
            && !isset($payload['scope'])
            && str_contains((string) $response->body(), 'Missing scope')
        ) {
            $response = Http::asForm()
                ->withBasicAuth($this->clientId, $this->clientSecret)
                ->timeout(12)
                ->post($this->tokenUrl, [
                    'grant_type' => 'client_credentials',
                    'scope' => 'premier',
                ]);
        }

        $response->throw();

        return (string) $response->json('access_token', '');
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
        uksort($dictionary, fn ($a, $b) => strlen($b) <=> strlen($a));

        foreach ($dictionary as $en => $es) {
            $translated = preg_replace('/\\b' . preg_quote($en, '/') . '\\b/i', $es, $translated) ?? $translated;
        }

        return ucfirst($translated);
    }
}
