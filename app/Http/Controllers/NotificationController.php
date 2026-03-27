<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsSeen(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->forceFill([
                'notifications_last_seen_at' => now(),
            ])->save();
        }

        return response()->noContent();
    }
}
