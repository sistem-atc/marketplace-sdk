<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Amazon\DTO\Response\Notification\Destination;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Notification\Subscription;

it('Subscription tipa subscriptionId/destinationId (camelCase)', function () {
    $s = Subscription::fromArray(['subscriptionId' => 'sub-1', 'destinationId' => 'dest-9', 'payloadVersion' => '1.0']);
    expect($s->subscriptionId)->toBe('sub-1')->and($s->destinationId)->toBe('dest-9')
        ->and($s->toArray())->toEqual(['subscriptionId' => 'sub-1', 'destinationId' => 'dest-9', 'payloadVersion' => '1.0']);
});

it('Subscription vazia (tipo não assinado) → tudo null', function () {
    expect(Subscription::fromArray([])->subscriptionId)->toBeNull();
});

it('Destination tipa destinationId/name + resource passthrough', function () {
    $d = Destination::fromArray(['destinationId' => 'dest-9', 'name' => 'bunker-sqs', 'resource' => ['sqs' => ['arn' => 'arn:...']]]);
    expect($d->destinationId)->toBe('dest-9')->and($d->name)->toBe('bunker-sqs')
        ->and($d->resource)->toBe(['sqs' => ['arn' => 'arn:...']]);
});
