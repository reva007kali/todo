<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/push-subscribe', function (Request $request) {
    $request->validate([
        'endpoint'    => 'required',
        'keys.auth'   => 'required',
        'keys.p256dh' => 'required'
    ]);

    $user = $request->user();

    if ($user) {
        $user->updatePushSubscription(
            $request->endpoint,
            $request->keys['p256dh'],
            $request->keys['auth']
        );
        return response()->json(['success' => true]);
    }
    
    return response()->json(['error' => 'User not logged in'], 401);

})->middleware('web'); // <--- PENTING: Ganti ke 'web' dulu biar kebaca session login-nya