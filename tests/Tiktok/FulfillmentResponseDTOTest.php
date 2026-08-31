<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\BatchShipPackagesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\CombinePackageResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\CreatePackageResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\FirstMileBundleResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\HandoverTimeSlotsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\OrderCustomsRequirementsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\OrderSplitAttributesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\PackageDetailResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\PackageSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\PodDetailResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\RedeemInfoCallbackResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\SearchCombinablePackagesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\ShippingDocumentResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\TrackingValidationResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\UncombinePackagesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\UpdatePackageDeliveryStatusResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\UploadDeliveryFileResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\UploadInvoiceResponseDTO;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Fulfillment\FulfillmentMethods;
use SistemAtc\Marketplaces\Tiktok\Tiktok;

function fulfillmentFixture(string $slug): array
{
    return json_decode(
        file_get_contents(__DIR__.'/../Fixtures/Tiktok/'.$slug.'.json'),
        true,
    );
}

/**
 * Caminhos de TODAS as chaves do payload, incluindo as aninhadas.
 *
 * O array_diff so' de chaves de topo (padrao do OrderResponseDTOTest) nao
 * pegaria campo perdido dentro de `orders[].skus[]` ou de
 * `order_clearance_list[].item_list[].related_item_detail[]` — e o grupo
 * fulfillment e' quase todo estrutura aninhada.
 *
 * @return list<string>
 */
function chavesProfundas(mixed $valor, string $prefixo = ''): array
{
    if (! is_array($valor)) {
        return [];
    }

    $caminhos = [];
    foreach ($valor as $chave => $filho) {
        // Indice de lista nao e' campo: normaliza pra [] pra comparar shape.
        $atual = is_int($chave) ? $prefixo.'[]' : ($prefixo === '' ? $chave : $prefixo.'.'.$chave);
        if (! is_int($chave)) {
            $caminhos[] = $atual;
        }
        $caminhos = array_merge($caminhos, chavesProfundas($filho, $atual));
    }

    return array_values(array_unique($caminhos));
}

dataset('respostas oficiais do fulfillment', [
    'get package detail' => ['get-package-detail', PackageDetailResponseDTO::class],
    'search package' => ['search-package', PackageSearchResponseDTO::class],
    'upload invoice' => ['upload-invoice', UploadInvoiceResponseDTO::class],
    'batch ship packages' => ['batch-ship-packages', BatchShipPackagesResponseDTO::class],
    'combine package' => ['combine-package', CombinePackageResponseDTO::class],
    'uncombine packages' => ['uncombine-packages', UncombinePackagesResponseDTO::class],
    'search combinable packages' => ['search-combinable-packages', SearchCombinablePackagesResponseDTO::class],
    'create packages' => ['create-packages', CreatePackageResponseDTO::class],
    'create first mile bundle' => ['create-first-mile-bundle', FirstMileBundleResponseDTO::class],
    'upload delivery file' => ['fulfillment-upload-delivery-file', UploadDeliveryFileResponseDTO::class],
    'upload delivery image' => ['fulfillment-upload-delivery-image', \SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\UploadDeliveryImageResponseDTO::class],
    'get order customs requirements' => ['get-order-customs-requirements', OrderCustomsRequirementsResponseDTO::class],
    'get order split attributes' => ['get-order-split-attributes', OrderSplitAttributesResponseDTO::class],
    'get package handover time slots' => ['get-package-handover-time-slots', HandoverTimeSlotsResponseDTO::class],
    'get package shipping document' => ['get-package-shipping-document', ShippingDocumentResponseDTO::class],
    'get pod detail (fulfillment)' => ['fulfillment-get-pod-detail', PodDetailResponseDTO::class],
    'redeem info callback' => ['redeem-info-callback', RedeemInfoCallbackResponseDTO::class],
    'tts tracking validation' => ['tts-tracking-validation', TrackingValidationResponseDTO::class],
    'update package delivery status' => ['update-package-delivery-status', UpdatePackageDeliveryStatusResponseDTO::class],
]);

it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function (string $slug, string $dto) {
    $payload = fulfillmentFixture($slug);

    $perdidos = array_diff(
        chavesProfundas($payload),
        chavesProfundas($dto::fromArray($payload)->toArray()),
    );

    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
})->with('respostas oficiais do fulfillment');

it('faz roundtrip lossless de toda resposta do fulfillment', function (string $slug, string $dto) {
    $payload = fulfillmentFixture($slug);

    expect($dto::fromArray($payload)->toArray())->toEqual($payload);
})->with('respostas oficiais do fulfillment');

it('mantem peso e dimensao como STRING — igual dinheiro no pedido', function () {
    $dto = PackageDetailResponseDTO::fromArray(fulfillmentFixture('get-package-detail'));

    // "1.2" tipado como float viraria 1.2 e "0.03" continuaria 0.03, mas
    // "200" (cobertura do seguro) viraria 200 e perderia a forma do raw.
    expect($dto->weight?->value)->toBe('1.2')
        ->and($dto->dimension?->height)->toBe('0.03')
        ->and($dto->insurance?->coverageAmount)->toBe('200');

    // A doc fixa a cobertura do seguro em USD — NAO e' a moeda do pedido.
    expect((float) $dto->insurance->coverageAmount)->toBe(200.0);
});

it('o pacote no DETALHE e na LISTAGEM usa nomes de campo diferentes', function () {
    $detalhe = PackageDetailResponseDTO::fromArray(fulfillmentFixture('get-package-detail'));
    $lista = PackageSearchResponseDTO::fromArray(fulfillmentFixture('search-package'));

    // Detalhe: package_id / package_status. Listagem: id / status.
    // Quem tratar os dois como o mesmo objeto le null em producao.
    expect($detalhe->packageId)->toBe('5433567853345')
        ->and($detalhe->packageStatus)->toBe('PROCESSING')
        ->and($lista->packages[0]->id)->toBe('577828281214600000')
        ->and($lista->packages[0]->status)->toBe('PROCESSING');

    // E a listagem nao traz endereco/peso/dimensao/seguro: pra isso, detalhe.
    expect($lista->packages[0])->not->toHaveProperty('weight');
});

it('lote responde SUCESSO com falhas dentro — errors precisa ser lido', function () {
    // O envelope veio code: 0 (o BaseMethods nao lanca excecao), mas a nota
    // NAO subiu. Marcar como enviada aqui deixaria o pedido sem NFe no canal.
    $dto = UploadInvoiceResponseDTO::fromArray(fulfillmentFixture('upload-invoice'));

    expect($dto->errors)->toHaveCount(1)
        ->and($dto->errors[0]->code)->toBe(10007014)
        ->and($dto->errors[0]->detail?->packageId)->toBe('1231231231231')
        ->and($dto->errors[0]->detail?->orderIds)->toBe(['58747683456']);

    // Mesma forma no batch ship e no update de entrega.
    $ship = BatchShipPackagesResponseDTO::fromArray(fulfillmentFixture('batch-ship-packages'));
    $deliver = UpdatePackageDeliveryStatusResponseDTO::fromArray(fulfillmentFixture('update-package-delivery-status'));

    expect($ship->errors[0]->detail?->packageId)->toBe('123123123123131')
        ->and($deliver->errors[0]->message)->toBe('order not in sof service');
});

it('combine e uncombine devolvem package_id NOVO — etiqueta antiga morre', function () {
    $combine = CombinePackageResponseDTO::fromArray(fulfillmentFixture('combine-package'));
    $uncombine = UncombinePackagesResponseDTO::fromArray(fulfillmentFixture('uncombine-packages'));

    // Sucesso e falha convivem na MESMA resposta do combine.
    expect($combine->packages)->toHaveCount(1)
        ->and($combine->packages[0]->id)->toBe('413234134123412')
        ->and($combine->packages[0]->orderIds)->toHaveCount(2)
        ->and($combine->errors)->toHaveCount(1);

    expect($uncombine->packages[0]->id)->toBe('123456');

    // E os rascunhos do search combinable NAO sao pacotes ainda.
    $draft = SearchCombinablePackagesResponseDTO::fromArray(fulfillmentFixture('search-combinable-packages'));
    expect($draft->combinablePackages[0]->id)->toBe('57466538837665');
});

it('preserva o typo "avaliable" da API nos slots de coleta', function () {
    $dto = HandoverTimeSlotsResponseDTO::fromArray(fulfillmentFixture('get-package-handover-time-slots'));

    // A API escreve "avaliable" (sem o "i"). Corrigir o nome do campo no DTO
    // faria o dado sumir silenciosamente na hidratacao.
    expect($dto->pickupSlots[0]->avaliable)->toBeTrue()
        ->and($dto->canVanCollection)->toBeTrue();
});

it('price_percent da alfandega e INT em PONTOS percentuais, nao fracao', function () {
    $dto = OrderCustomsRequirementsResponseDTO::fromArray(fulfillmentFixture('get-order-customs-requirements'));
    $item = $dto->orderClearanceList[0]->itemList[0];

    // 10 significa 10% (nao 0,10 e nao 1000%).
    expect($item->relatedItemDetail[0]->pricePercent)->toBe(10)
        ->and($item->relatedItemDetail[0]->qty)->toBe(2);

    // bizType/shipmentType sao enum NUMERICO (1 = FS / 1 = CROSS_BORDER).
    expect($dto->orderClearanceList[0]->bizType)->toBe(1)
        ->and($dto->orderClearanceList[0]->shipmentType)->toBe(1);

    // Item virtual: o que se declara e' o relatedItemDetail, nao o sku da linha.
    expect($item->isVirtualUnboxing)->toBeTrue();
});

it('canSplit e mustSplit sao independentes; max_count vem STRING', function () {
    $dto = OrderSplitAttributesResponseDTO::fromArray(fulfillmentFixture('get-order-split-attributes'));
    $attr = $dto->splitAttributes[0];

    expect($attr->canSplit)->toBeFalse()
        ->and($attr->mustSplit)->toBeFalse()
        ->and($attr->reason)->toBe('Order has been canceled')
        // Contagem declarada como string pela API — tipar int quebra o roundtrip.
        ->and($attr->mustSplitReasons[0]->maxCount)->toBe('2');
});

it('POD do fulfillment usa pod_info_json (o da Order API usa pod_detail)', function () {
    $dto = PodDetailResponseDTO::fromArray(fulfillmentFixture('fulfillment-get-pod-detail'));

    // Sao DOIS endpoints distintos: /fulfillment/202606/pod_details/get e
    // /order/202606/pod_details/search. Chaves diferentes, DTOs diferentes.
    expect($dto->podDetails[0]->podInfoJson)->toBe('pod_info_json')
        ->and($dto->podDetails[0]->orderLineId)->toBe('577457287176491132')
        // statusCode e' um SEGUNDO status, dentro de data, alem do code do envelope.
        ->and($dto->statusCode)->toBe(0);
});

it('tracking validation tem duas flags independentes', function () {
    $dto = TrackingValidationResponseDTO::fromArray(fulfillmentFixture('tts-tracking-validation'));

    // Frete da plataforma e coleta da plataforma sao coisas separadas.
    expect($dto->isTiktokShipping)->toBeFalse()
        ->and($dto->isTiktokCollection)->toBeFalse();
});

it('endereco do pacote vem sem district_info e pode estar mascarado', function () {
    $dto = PackageDetailResponseDTO::fromArray(fulfillmentFixture('get-package-detail'));

    // Diferente do endereco do PEDIDO, aqui nao ha district_info: cidade e
    // estado so' existem soltos no texto.
    expect($dto->recipientAddress?->addressLine1)->toBe("TikTok 5800 bristol Pkwy\n")
        ->and($dto->recipientAddress?->addressLine4)->toBe("Suite 100\n")
        // Logistica da plataforma dessensibiliza nome/telefone: nao use como PII.
        ->and($dto->recipientAddress?->phoneNumber)->toContain('***');

    // O remetente usa exatamente a mesma shape.
    expect($dto->senderAddress?->regionCode)->toBe('US');
});

it('skus do pacote estao deprecated — o vinculo vivo e order_line_item_ids', function () {
    $dto = PackageDetailResponseDTO::fromArray(fulfillmentFixture('get-package-detail'));

    // Mapeados porque a API ainda manda (zero perda), mas a doc os marca
    // [Deprecated]: quem for casar item de pacote com item de pedido usa
    // orderLineItemIds.
    expect($dto->orders[0]->skus[0]->quantity)->toBe(5)
        ->and($dto->orderLineItemIds)->toBe(['1729382476852921560']);
});

it('create package devolve o servico de frete com preco STRING e moeda livre', function () {
    $dto = CreatePackageResponseDTO::fromArray(fulfillmentFixture('create-packages'));

    expect($dto->packageId)->toBe('2882335594258860015')
        ->and($dto->shippingServiceInfo?->price)->toBe('10')
        // "dollar", nao "USD": a API manda texto livre, nao codigo ISO.
        ->and($dto->shippingServiceInfo?->currency)->toBe('dollar');
});

it('bundle de primeira milha devolve waybill temporario', function () {
    $dto = FirstMileBundleResponseDTO::fromArray(fulfillmentFixture('create-first-mile-bundle'));

    expect($dto->firstMileBundleId)->toBe('BA123444534')
        // URL assinada e com timestamp: baixar na hora, nao guardar o link.
        ->and($dto->url)->toContain('sign=')
        // O erro aqui detalha ORDER_ID (nos outros endpoints e' package_id).
        ->and($dto->errors[0]->detail?->orderId)->toBe('578967030217083407');
});

it('redeem callback reporta falha POR LINHA de pedido', function () {
    $dto = RedeemInfoCallbackResponseDTO::fromArray(fulfillmentFixture('redeem-info-callback'));

    expect($dto->orderStatuses[0]->orderLineId)->toBe('577086512123755123')
        ->and($dto->orderStatuses[0]->statusCode)->toBe(0);
});

it('documento de envio expira: a URL vale 24h', function () {
    $dto = ShippingDocumentResponseDTO::fromArray(fulfillmentFixture('get-package-shipping-document'));

    expect($dto->docUrl)->toStartWith('https://')
        ->and($dto->trackingNumber)->toBe('752455325694');
});

it('o acessor fulfillment() existe e aponta pro grupo certo', function () {
    // A nota fiscal do TikTok sobe por AQUI (/fulfillment/202502/invoice/upload),
    // nao pelo acessor invoice() — cujo /finance/.../invoices nao existe.
    expect(method_exists(Tiktok::class, 'fulfillment'))->toBeTrue()
        ->and((new ReflectionMethod(Tiktok::class, 'fulfillment'))->getReturnType()?->getName())
        ->toBe(FulfillmentMethods::class);
});
