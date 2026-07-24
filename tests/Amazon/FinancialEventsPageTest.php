<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Finance\FinancialEventsPage;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function amazonFinancesIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: [
            'client_id' => 'test-client',
            'client_secret' => 'test-secret',
            'marketplace_id' => 'A2Q3Y263D00KWC',
        ],
        active: true,
        expired: false,
    );
}

it('listFinancialEvents (por período) devolve página tipada + nextToken', function () {
    Http::fake([
        'https://sellingpartnerapi-na.amazon.com/finances/v0/financialEvents*' => Http::response([
            'payload' => [
                'FinancialEvents' => [
                    'ShipmentEventList' => [[
                        'AmazonOrderId' => '701-1111111-1111111',
                        'ShipmentItemList' => [[
                            'ItemChargeList' => [['ChargeType' => 'Principal', 'ChargeAmount' => ['CurrencyAmount' => 99.9, 'CurrencyCode' => 'BRL']]],
                            'ItemFeeList' => [['FeeType' => 'Commission', 'FeeAmount' => ['CurrencyAmount' => -15.0, 'CurrencyCode' => 'BRL']]],
                        ]],
                    ]],
                ],
                'NextToken' => 'TOKEN-PAGINA-2',
            ],
        ], 200),
    ]);

    $page = MarketPlaces::Amazon()->client(amazonFinancesIntegration())->finances()
        ->listFinancialEvents(['PostedAfter' => '2024-09-01T00:00:00Z', 'MaxResultsPerPage' => 100]);

    expect($page)->toBeInstanceOf(FinancialEventsPage::class)
        ->and($page->nextToken)->toBe('TOKEN-PAGINA-2')
        ->and($page->financialEvents->shipmentEventList)->toBeArray()->toHaveCount(1)
        // a árvore PascalCale profunda tem que sobreviver (o parser do Bunker depende)
        ->and(data_get($page->financialEvents->toArray(), 'ShipmentEventList.0.AmazonOrderId'))->toBe('701-1111111-1111111')
        // toEqual (loose): o round-trip JSON do Http::fake coage -15.0 -> -15;
        // o que importa é o VALOR e a posição na árvore, não o tipo numérico.
        ->and(data_get($page->financialEvents->toArray(), 'ShipmentEventList.0.ShipmentItemList.0.ItemFeeList.0.FeeAmount.CurrencyAmount'))->toEqual(-15.0);
});

it('última página (sem NextToken) → nextToken null encerra a paginação', function () {
    Http::fake([
        'https://sellingpartnerapi-na.amazon.com/finances/v0/financialEvents*' => Http::response([
            'payload' => [
                'FinancialEvents' => ['ShipmentEventList' => []],
            ],
        ], 200),
    ]);

    $page = MarketPlaces::Amazon()->client(amazonFinancesIntegration())->finances()
        ->listFinancialEvents(['NextToken' => 'algum-token-opaco']);

    expect($page->nextToken)->toBeNull();
});

it('FinancialEventsPage::fromArray monta o DTO aninhado a partir do payload', function () {
    $page = FinancialEventsPage::fromArray([
        'FinancialEvents' => ['RefundEventList' => [['AmazonOrderId' => '701-2']]],
        'NextToken' => 'abc',
    ]);

    expect($page->nextToken)->toBe('abc')
        ->and($page->financialEvents->refundEventList)->toHaveCount(1)
        ->and(data_get($page->toArray(), 'FinancialEvents.RefundEventList.0.AmazonOrderId'))->toBe('701-2')
        ->and(data_get($page->toArray(), 'NextToken'))->toBe('abc');
});
