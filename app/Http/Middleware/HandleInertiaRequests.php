<?php

namespace App\Http\Middleware;

use App\Models\AdminNotice;
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

        $notificationItems = [];
        $unreadCount = 0;

        if ($user && $user->role_key === 'nutritionist') {
            $notices = AdminNotice::with('admin')
                ->where('recipient_user_id', $user->id)
                ->latest('sent_at')
                ->take(12)
                ->get();

            $notificationItems = $notices->map(fn($n) => [
                'id'       => $n->id,
                'category' => $n->category,
                'title'    => $n->title,
                'message'  => $n->message,
                'admin'    => $n->admin?->full_name ?? 'Administración',
                'sentAt'   => optional($n->sent_at)->translatedFormat('d M, h:i A'),
                'sentAtIso'=> optional($n->sent_at)->toIso8601String(),
            ])->values()->all();

            $unreadQuery = AdminNotice::where('recipient_user_id', $user->id);
            if ($user->notifications_last_seen_at) {
                $unreadQuery->where('sent_at', '>', $user->notifications_last_seen_at);
            }
            $unreadCount = $unreadQuery->count();
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
                    'clinic_logo_url' => $user->nutritionistProfile?->clinic_logo_path
                        ? asset('storage/' . $user->nutritionistProfile->clinic_logo_path)
                        : null,
                ] : null,
            ],
            'notifications' => [
                'items' => $notificationItems,
                'unread_count' => $unreadCount,
            ],
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'warning' => fn() => $request->session()->get('warning'),
            ],
        ]);
    }
}
