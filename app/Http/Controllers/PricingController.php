<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PricingController extends Controller
{
    public function index(Request $request): Response
    {
        $catalog = collect(config('plans.catalog', []))
            ->values()
            ->map(function (array $plan): array {
                $plan['isRecommended'] = ($plan['key'] ?? '') === 'normal';
                return $plan;
            })
            ->all();

        $selected = (string) $request->query('plan', (string) config('plans.default_plan', 'free'));
        $selected = array_key_exists($selected, config('plans.catalog', [])) ? $selected : (string) config('plans.default_plan', 'free');

        return Inertia::render('Plans', [
            'plans' => $catalog,
            'currency' => (string) config('plans.currency', 'USD'),
            'selectedPlan' => $selected,
        ]);
    }
}
