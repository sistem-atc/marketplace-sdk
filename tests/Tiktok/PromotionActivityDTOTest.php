<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion\Activity;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion\ActivitySearchResponseDTO;

it('LISTAGEM usa `id` — activityId() resolve', function () {
    // getActivities devolve `id` (NAO activity_id).
    $a = Activity::fromArray([
        'id' => '7500000000000000001',
        'title' => 'Flash Sale Julho',
        'status' => 'ONGOING',
        'activity_type' => 'FLASHSALE',
        'begin_time' => 1752710400,
        'end_time' => 1753315200,
    ]);

    expect($a->id)->toBe('7500000000000000001')
        ->and($a->activityId)->toBeNull()         // esse campo so vem no detalhe
        ->and($a->activityId())->toBe('7500000000000000001');  // helper resolve
});

it('DETALHE usa `activity_id` — activityId() resolve o mesmo', function () {
    // getActivity devolve `activity_id` (NAO id) + products[].
    $a = Activity::fromArray([
        'activity_id' => '7500000000000000001',
        'title' => 'Flash Sale Julho',
        'status' => 'ONGOING',
        'activity_type' => 'FLASHSALE',
        'products' => [[
            'id' => '1729592094930765000',
            'activity_price' => ['amount' => '89.90', 'currency' => 'BRL'],
            'quantity_limit' => 100,
            'quantity_per_user' => 2,
        ]],
    ]);

    expect($a->id)->toBeNull()
        ->and($a->activityId)->toBe('7500000000000000001')
        ->and($a->activityId())->toBe('7500000000000000001')   // MESMO helper
        ->and($a->products[0]->activityPrice->amount)->toBe('89.90');  // string
});

it('travar a regressao: ler activity_id cru na LISTAGEM da null', function () {
    // Era o bug que deixava tiktok_promotions_raw vazia: o consumidor lia
    // ['activity_id'] sobre a listagem, que so' tem 'id'.
    $a = Activity::fromArray(['id' => 'X1']);

    expect($a->activityId)->toBeNull()   // ler o campo cru falha
        ->and($a->activityId())->toBe('X1');  // o helper e o caminho certo
});

it('faz roundtrip lossless da listagem', function () {
    $p = ['id' => 'X1', 'title' => 'T', 'status' => 'ONGOING', 'activity_type' => 'FLASHSALE',
        'begin_time' => 1752710400, 'end_time' => 1753315200, 'create_time' => 1752700000,
        'update_time' => 1752705000, 'duration_type' => 'FIXED', 'product_level' => 'PRODUCT'];

    expect(Activity::fromArray($p)->toArray())->toEqual($p);
});

it('hidrata o envelope do search', function () {
    $dto = ActivitySearchResponseDTO::fromArray([
        'activities' => [['id' => 'A1'], ['id' => 'A2']],
        'next_page_token' => 'tok',
        'total_count' => 50,
    ]);

    expect($dto->activities)->toHaveCount(2)
        ->and($dto->activities[0]->activityId())->toBe('A1')
        ->and($dto->nextPageToken)->toBe('tok');
});
