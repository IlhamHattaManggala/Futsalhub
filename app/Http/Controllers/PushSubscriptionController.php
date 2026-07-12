<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController extends Controller
{
    /**
     * Store or update a push subscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $endpoint = $request->endpoint;
        $key = $request->input('keys.p256dh');
        $token = $request->input('keys.auth');
        $contentEncoding = $request->input('content_encoding', 'aes128gcm');

        $user->updatePushSubscription($endpoint, $key, $token, $contentEncoding);

        return response()->json(['message' => 'Subscription saved successfully.']);
    }

    /**
     * Delete a push subscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $endpoint = $request->endpoint;
        $user->deletePushSubscription($endpoint);

        return response()->json(['message' => 'Subscription deleted successfully.']);
    }
}
