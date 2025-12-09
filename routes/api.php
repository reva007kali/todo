<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/push-subscribe', function () {
    auth()->user()->updatePushSubscription(
        request()->endpoint,
        request()->keys['p256dh'],
        request()->keys['auth']
    );

    return response()->json(['success' => true]);
});