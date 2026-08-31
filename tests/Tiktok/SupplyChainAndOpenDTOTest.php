<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tests\Support\RecursiveFieldCoverage;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Open\UploadFileInitResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\SupplyChain\ConfirmPackageShipmentResponseDTO;

function scFixture(string $slug): array
{
    return json_decode(
        file_get_contents(__DIR__.'/../Fixtures/Tiktok/'.$slug.'.json'),
        true,
    );
}

dataset('sc_fixtures', [
    'confirm package shipment' => ['sc-confirm-package-shipment', ConfirmPackageShipmentResponseDTO::class],
    'upload file init' => ['open-upload-file-init', UploadFileInitResponseDTO::class],
]);

it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function (string $slug, string $dto) {
    $payload = scFixture($slug);
    $perdidos = RecursiveFieldCoverage::missing($payload, $dto::fromArray($payload)->toArray());

    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
})->with('sc_fixtures');

it('faz roundtrip lossless do payload inteiro', function (string $slug, string $dto) {
    $payload = scFixture($slug);

    expect($dto::fromArray($payload)->toArray())->toEqual($payload);
})->with('sc_fixtures');

// ─────────────────────────────────────────────────────────────────────────
// Comportamento real
// ─────────────────────────────────────────────────────────────────────────

it('o lote de embarque e PARCIAL: code 0 nao significa todos confirmados', function () {
    $dto = ConfirmPackageShipmentResponseDTO::fromArray(scFixture('sc-confirm-package-shipment'));

    // 2 confirmados e 1 recusado na MESMA resposta de sucesso. Quem der o lote
    // por embarcado sem cruzar `successPackages` com o que enviou perde pacote.
    expect($dto->successPackages)->toBe(['5345241234', '42365346'])
        ->and($dto->errors)->toHaveCount(1)
        ->and($dto->errors[0]->detail->packageId)->toBe('4645645645');
});

it('o codigo de erro do pacote e STRING, ao contrario do code do envelope', function () {
    $erro = ConfirmPackageShipmentResponseDTO::fromArray(scFixture('sc-confirm-package-shipment'))->errors[0];

    // Envelope usa int (`code: 0`); aqui dentro vem "39003015". Comparar com
    // `=== 39003015` nunca casa.
    expect($erro->code)->toBeString()->toBe('39003015');
});

it('upload file init nao sobe arquivo nenhum: devolve destino + token', function () {
    $dto = UploadFileInitResponseDTO::fromArray(scFixture('open-upload-file-init'));

    // O upload em si vai chunk a chunk pra ESTA url, com este token — outro
    // host, fora da assinatura HMAC da OpenAPI.
    expect($dto->uploadUrl)->toBe('https://open-api.tiktokglobalshop.com/file/202512/upload')
        ->and($dto->uploadToken)->not->toBeEmpty();
});
