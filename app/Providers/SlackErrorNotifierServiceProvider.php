<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Log;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\SlackHandler;
use Monolog\Logger;

class SlackErrorNotifierServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!$this->app->environment('local')) {
            $token      = env('ERROR_SLACK_API_TOKEN', '');
            $channel    = env('ERROR_SLACK_CHANNEL', '');

            if (!empty($token) && !empty($channel)) {
                $url    = str_replace("https://", "", env("APP_URL"));
                $url    = str_replace("http://", "", $url);

                $monolog = Log::getMonolog();
                $slackHandler = new SlackHandler($token, $channel, $url, true, null, Logger::ERROR);

                $monolog->pushHandler($slackHandler);
            }
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
