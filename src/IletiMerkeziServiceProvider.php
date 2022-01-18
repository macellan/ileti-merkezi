<?php

namespace Macellan\IletiMerkezi;

use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class IletiMerkeziServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     * @noinspection PhpUndefinedFunctionInspection
     */
    public function boot()
    {
        Notification::resolved(function (ChannelManager $service) {
            $service->extend('iletimerkezi', function () {
                return new IletiMerkeziChannel(config('services.sms.iletimerkezi'));
            });
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        //
    }
}
