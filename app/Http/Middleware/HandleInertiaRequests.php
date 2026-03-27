<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        $user = $request->user();
        // Eager-load nutritionistProfile una sola vez si el usuario está autenticado
        if ($user && $user->role_key === 'nutritionist') {
            $user->loadMissing('nutritionistProfile');
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? [
                    'id'          => $user->id,
                    'first_name'  => $user->first_name,
                    'last_name'   => $user->last_name,
                    'full_name'   => $user->full_name,
                    'email'       => $user->email,
                    'role_key'    => $user->role_key,
                    'avatar'      => $user->avatar,
                    'theme_color' => $user->nutritionistProfile?->theme_color ?? 'emerald',
                ] : null,
            ],
        ]);
    }
}
