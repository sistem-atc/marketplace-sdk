<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use SistemAtc\Marketplaces\MarketplacesServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            MarketplacesServiceProvider::class,
        ];
    }
}
