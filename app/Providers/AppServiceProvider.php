<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
            str_starts_with(config('app.url'), 'https')
        ) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            $webName = \App\Models\Setting::get('web_name', 'FutsalHub');
            return (new MailMessage)
                ->subject('Verifikasi Alamat Email Anda - ' . $webName)
                ->view('emails.verify_email', [
                    'user' => $notifiable,
                    'url' => $url,
                    'webName' => $webName,
                ]);
        });
    }
}
