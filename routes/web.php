<?php

use Illuminate\Support\Facades\Mail;

Route::get('/test-mail', function () {
    try {
        Mail::raw('Ini adalah email percobaan.', function ($message) {
            $message->to('fadilj0704@example.com')
                    ->subject('Test Email Laravel');
        });
        return "Email berhasil dikirim!";
    } catch (\Exception $e) {
        return "Gagal: " . $e->getMessage();
    }
});