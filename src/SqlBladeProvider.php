<?php

declare(strict_types=1);

namespace Zjk\SqlBlade;

use Zjk\SqlBlade\Contract\SqlBladeInterface;
use Zjk\SqlBlade\Contract\SqlLoggerInterface;
use Zjk\SqlBlade\Logger\SqlLogger;
use Zjk\SqlBlade\Service\SqlBlade;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class SqlBladeProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SqlLoggerInterface::class, fn (): SqlLogger => new SqlLogger(App::make('log')));

        $this->app->bind(SqlBladeInterface::class, fn (): SqlBlade => new SqlBlade(
            App::make('db.connection')->getDoctrineConnection(),
            App::make('view'),
            App::make(SqlLoggerInterface::class),
            Config::get('app.debug')
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::addExtension('blade.sql', 'blade');
    }
}
