<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Shopee\DTO\Response\Order\FbsDownloadItem;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Order\FbsRequestListResponseDTO;

/**
 * Os endpoints FBS NAO seguem o envelope `response` do resto da Shopee.
 * Sao 3 formatos DIFERENTES, todos confirmados ao vivo em 2026-07-17.
 */
it('generate: result_list vem na RAIZ, nao em response', function () {
    $dto = FbsRequestListResponseDTO::fromArray([
        'result_list' => [['request_id' => 21764095], ['request_id' => 21764096]],
        'request_id' => 'e3e3e7f356cd3bbb0d494f61c01ec100',  // id da REQUISICAO, outro conceito
        'timestamp' => '2026-07-17T08:55:30-03:00',
    ]);

    expect($dto->resultList)->toHaveCount(2)
        ->and($dto->resultList[0]->requestId)->toBe(21764095)
        ->and($dto->resultList[0]->status)->toBeNull();  // generate nao devolve status
});

it('result: mesma shape + status por tarefa', function () {
    $dto = FbsRequestListResponseDTO::fromArray([
        'result_list' => [
            ['request_id' => 21764095, 'status' => 'AVAILABLE'],
            ['request_id' => 21764097, 'status' => 'PROCESSING'],
        ],
    ]);

    expect($dto->resultList[0]->status)->toBe('AVAILABLE')
        ->and($dto->resultList[1]->status)->toBe('PROCESSING');
});

it('download: cada item traz o link assinado do ZIP', function () {
    $item = FbsDownloadItem::fromArray([
        'request_id' => 21764095,
        'file_link' => 'https://sfile-origin-br.sp-cdn.susercontent.com/fake_xml.zip?X-Amz-Expires=1800',
    ]);

    expect($item->requestId)->toBe(21764095)
        // URL S3 assinada — expira em 30min, baixar na hora
        ->and($item->fileLink)->toContain('X-Amz-Expires=1800');
});

it('faz roundtrip lossless do result_list', function () {
    $payload = ['result_list' => [['request_id' => 1, 'status' => 'AVAILABLE']]];

    expect(FbsRequestListResponseDTO::fromArray($payload)->toArray())->toEqual($payload);
});
