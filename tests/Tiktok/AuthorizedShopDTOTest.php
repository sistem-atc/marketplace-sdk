<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tiktok\DTO\Response\Authorization\AuthorizedShop;

it('tipa a loja autorizada (id/cipher = shop_cipher)', function () {
    $shop = AuthorizedShop::fromArray([
        'id' => '7494278406602786602',
        'name' => 'Everlast BR',
        'region' => 'BR',
        'seller_type' => 'LOCAL',
        'cipher' => 'TTP_ci4A...',
        'code' => 'ABC',
    ]);

    expect($shop->id)->toBe('7494278406602786602')
        ->and($shop->cipher)->toBe('TTP_ci4A...')
        ->and($shop->sellerType)->toBe('LOCAL')
        ->and($shop->region)->toBe('BR');
});

it('roundtrip preserva snake_case (seller_type)', function () {
    $payload = ['id' => '1', 'name' => 'X', 'region' => 'BR', 'seller_type' => 'CB', 'cipher' => 'c', 'code' => 'z'];

    expect(AuthorizedShop::fromArray($payload)->toArray())->toBe($payload);
});
