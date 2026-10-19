<?php

// routes/console.php - Artisan commands

use Illuminate\Support\Facades\Schedule;

// Cek dan nonaktifkan subscription yang expired setiap hari
Schedule::call(function () {
    \App\Models\Subscription::where('berakhir', '<', now())
        ->where('is_active', true)
        ->update(['is_active' => false]);
})->daily()->name('deactivate-expired-subscriptions');
