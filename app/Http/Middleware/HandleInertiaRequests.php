<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id'         => $request->user()->id,
                    'first_name' => $request->user()->first_name,
                    'last_name'  => $request->user()->last_name,
                    'full_name'  => $request->user()->full_name,
                    'email'      => $request->user()->email,
                    'role_key'   => $request->user()->role_key,
                    'avatar'     => $request->user()->avatar,
                ] : null,
            ],
        ]);
    }
}
