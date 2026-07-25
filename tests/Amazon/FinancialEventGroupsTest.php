<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Finance\FinancialEventGroupsPage;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Finance\FinancialEventsPage;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function amazonSettlementIntegration(): FakeIntegration
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

it('listFinancialEventGroups devolve os settlements REAIS tipados + nextToken', function () {
    Http::fake([
        'https://sellingpartnerapi-na.amazon.com/finances/v0/financialEventGroups*' => Http::response([
            'payload' => [
                'FinancialEventGroupList' => [[
                    'FinancialEventGroupId' => 'GROUP-123',
                    'ProcessingStatus' => 'Closed',
                    'FundTransferStatus' => 'Successful',
                    'OriginalTotal' => ['CurrencyCode' => 'BRL', 'CurrencyAmount' => 100000.50],
                    'FundTransferDate' => '2025-09-15T10:00:00Z',
                    'TraceId' => 'TRACE-9988',
                    'AccountTail' => '1234',
                    'BeginningBalance' => ['CurrencyCode' => 'BRL', 'CurrencyAmount' => 0.0],
                    'FinancialEventGroupStart' => '2025-09-01T00:00:00Z',
                    'FinancialEventGroupEnd' => '2025-09-14T23:59:59Z',
                ]],
                'NextToken' => 'PROXIMA-PAGINA',
            ],
        ], 200),
    ]);

    $page = MarketPlaces::Amazon()->client(amazonSettlementIntegration())->finances()
        ->listFinancialEventGroups(['FinancialEventGroupStartedAfter' => '2025-09-01T00:00:00Z']);

    expect($page)->toBeInstanceOf(FinancialEventGroupsPage::class)
        ->and($page->nextToken)->toBe('PROXIMA-PAGINA')
        ->and($page->groups)->toHaveCount(1);

    $g = $page->groups[0];
    expect($g->financialEventGroupId)->toBe('GROUP-123')
        ->and($g->fundTransferStatus)->toBe('Successful')
        ->and($g->traceId)->toBe('TRACE-9988')
        // o valor REAL depositado (nao calculado)
        ->and(data_get($g->originalTotal, 'CurrencyAmount'))->toEqual(100000.50)
        ->and(data_get($g->originalTotal, 'CurrencyCode'))->toBe('BRL');
});

it('listFinancialEventsByGroupId abre o detalhe por-pedido de um settlement', function () {
    Http::fake([
        'https://sellingpartnerapi-na.amazon.com/finances/v0/financialEventGroups/GROUP-123/financialEvents*' => Http::response([
            'payload' => [
                'FinancialEvents' => [
                    'ShipmentEventList' => [[
                        'AmazonOrderId' => '701-9999999-9999999',
                        'ShipmentItemList' => [[
                            'ItemChargeList' => [['ChargeType' => 'Principal', 'ChargeAmount' => ['CurrencyAmount' => 25.0, 'CurrencyCode' => 'BRL']]],
                            'ItemFeeList' => [['FeeType' => 'Commission', 'FeeAmount' => ['CurrencyAmount' => -5.0, 'CurrencyCode' => 'BRL']]],
                        ]],
                    ]],
                ],
            ],
        ], 200),
    ]);

    $page = MarketPlaces::Amazon()->client(amazonSettlementIntegration())->finances()
        ->listFinancialEventsByGroupId('GROUP-123');

    expect($page)->toBeInstanceOf(FinancialEventsPage::class)
        ->and($page->nextToken)->toBeNull()
        ->and($page->financialEvents->shipmentEventList)->toHaveCount(1)
        ->and(data_get($page->financialEvents->toArray(), 'ShipmentEventList.0.AmazonOrderId'))->toBe('701-9999999-9999999');
});

it('FinancialEventGroupsPage::fromArray monta a lista de settlements', function () {
    $page = FinancialEventGroupsPage::fromArray([
        'FinancialEventGroupList' => [
            ['FinancialEventGroupId' => 'G1', 'OriginalTotal' => ['CurrencyAmount' => 10.0, 'CurrencyCode' => 'BRL']],
            ['FinancialEventGroupId' => 'G2', 'OriginalTotal' => ['CurrencyAmount' => 20.0, 'CurrencyCode' => 'BRL']],
        ],
        'NextToken' => 'tok',
    ]);

    expect($page->groups)->toHaveCount(2)
        ->and($page->groups[0]->financialEventGroupId)->toBe('G1')
        ->and($page->nextToken)->toBe('tok')
        ->and(data_get($page->toArray(), 'FinancialEventGroupList.0.FinancialEventGroupId'))->toBe('G1');
});
