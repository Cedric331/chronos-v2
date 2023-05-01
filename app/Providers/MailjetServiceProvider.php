<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Mailjet\Client;

class MailjetServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Client::class, function ($app) {
            $config = $app['config']['mail.services.mailjet'];

            return new Client($config['key'], $config['secret'], true, ['version' => 'v3.1']);
        });
    }
}
