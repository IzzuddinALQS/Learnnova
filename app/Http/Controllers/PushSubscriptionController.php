<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController extends Controller
{
    /**
     * Store or update a push subscription.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $subscription = PushSubscription::updateOrCreate(
            ['endpoint' => $request->endpoint],
            [
                'user_id' => Auth::id(),
                'public_key' => $request->input('keys.p256dh'),
                'auth_token' => $request->input('keys.auth'),
                'content_encoding' => 'aesgcm',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Subscription saved successfully.',
            'subscription' => $subscription,
        ]);
    }

    /**
     * Delete a push subscription.
     */
    public function unsubscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
        ]);

        PushSubscription::where('endpoint', $request->endpoint)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subscription deleted successfully.',
        ]);
    }
}
