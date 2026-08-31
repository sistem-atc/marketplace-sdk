<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt\FbtAvailableInboundMethodsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt\FbtConfirmInboundMethodResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt\FbtCreateGoodsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt\FbtGoodsHazmatExpirationResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt\FbtGoodsSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt\FbtGoodsSkuRelationResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt\FbtInboundLabelResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt\FbtInboundMethodDetailResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt\FbtInboundOrderDetailResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt\FbtInboundPlanResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt\FbtInventoryRecordSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt\FbtInventorySearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt\FbtMcfGoodsInventoryResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt\FbtMcfOrderResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt\FbtMerchantMcfStatusResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt\FbtOnboardedRegionsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt\FbtShipInboundOrderResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt\FbtUpdateGoodsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt\FbtWarehouseListResponseDTO;

/**
 * Grupo FBT (Fulfilled by TikTok) — equivalente ao Full do ML e ao FBA da
 * Amazon. Mercadoria em armazem de TERCEIRO: no ERP, cada venda por
 * fulfillment envolve DUAS notas (remessa/retorno + venda), entao perder campo
 * aqui e' perder lastro fiscal, nao so' detalhe logistico.
 */
function fbtFixture(string $slug): array
{
    return json_decode(
        (string) file_get_contents(__DIR__.'/../Fixtures/Tiktok/'.$slug.'.json'),
        true,
    );
}

/**
 * Diff RECURSIVO payload x roundtrip.
 *
 * O teste de regressao do grupo Order so' comparava as chaves de PRIMEIRO
 * NIVEL — um campo perdido dentro de um objeto aninhado passava batido. Como o
 * FBT e' quase todo aninhado (goods -> skus -> product, inbound -> cartons ->
 * items), aqui a comparacao desce a arvore inteira e confere TAMBEM o valor:
 * `"1.122"` virando `1.122` (float) e perda silenciosa de casa decimal.
 *
 * @return list<string> caminhos perdidos/alterados
 */
function fbtCamposPerdidos(array $payload, array $roundtrip, string $path = ''): array
{
    $perdidos = [];

    foreach ($payload as $key => $value) {
        $atual = $path === '' ? (string) $key : $path.'.'.$key;

        if (! array_key_exists($key, $roundtrip)) {
            $perdidos[] = $atual;

            continue;
        }

        if (is_array($value) && is_array($roundtrip[$key])) {
            $perdidos = array_merge($perdidos, fbtCamposPerdidos($value, $roundtrip[$key], $atual));
        } elseif ($value !== $roundtrip[$key]) {
            $perdidos[] = sprintf(
                '%s (%s -> %s)',
                $atual,
                var_export($value, true),
                var_export($roundtrip[$key], true),
            );
        }
    }

    return $perdidos;
}

dataset('fixtures oficiais do FBT', [
    'Get FBT Warehouse List' => ['fbt-warehouse-list', FbtWarehouseListResponseDTO::class],
    'Get FBT Merchant Onboarded Regions' => ['fbt-onboarded-regions', FbtOnboardedRegionsResponseDTO::class],
    'Get FBT Merchant MCF Status' => ['fbt-merchant-mcf-status', FbtMerchantMcfStatusResponseDTO::class],
    'Search Goods Info' => ['fbt-search-goods', FbtGoodsSearchResponseDTO::class],
    'Search FBT Inventory' => ['fbt-search-inventory', FbtInventorySearchResponseDTO::class],
    'Search FBT Inventory Record' => ['fbt-search-inventory-record', FbtInventoryRecordSearchResponseDTO::class],
    'Search Goods Hazmat And Expiration Info' => ['fbt-goods-hazmat-expiration', FbtGoodsHazmatExpirationResponseDTO::class],
    'Query Goods Inventory For MCF' => ['fbt-mcf-goods-inventory', FbtMcfGoodsInventoryResponseDTO::class],
    'Get FBT MCF Order Status' => ['fbt-mcf-order-status', FbtMcfOrderResponseDTO::class],
    'Create FBT MCF Order' => ['fbt-create-mcf-order', FbtMcfOrderResponseDTO::class],
    'Cancel FBT MCF Order' => ['fbt-cancel-mcf-order', FbtMcfOrderResponseDTO::class],
    'Get FBT Inbound Order Details' => ['fbt-inbound-order-details', FbtInboundOrderDetailResponseDTO::class],
    'List Available Inbound Methods' => ['fbt-available-inbound-methods', FbtAvailableInboundMethodsResponseDTO::class],
    'Get Inbound Method Detail' => ['fbt-inbound-method-detail', FbtInboundMethodDetailResponseDTO::class],
    'Print Inbound Order Labels' => ['fbt-inbound-order-labels', FbtInboundLabelResponseDTO::class],
    'Create or Update Inbound Plan' => ['fbt-inbound-plan', FbtInboundPlanResponseDTO::class],
    'Confirm Inbound Method' => ['fbt-confirm-inbound-method', FbtConfirmInboundMethodResponseDTO::class],
    'Ship Inbound Order' => ['fbt-ship-inbound-order', FbtShipInboundOrderResponseDTO::class],
    'Create FBT Goods' => ['fbt-create-goods', FbtCreateGoodsResponseDTO::class],
    'Update FBT Goods Info' => ['fbt-update-goods', FbtUpdateGoodsResponseDTO::class],
    'Update Goods Sku Relation' => ['fbt-goods-sku-relation', FbtGoodsSkuRelationResponseDTO::class],
]);

it('nao descarta NENHUM campo do exemplo OFICIAL da doc (recursivo)', function (string $slug, string $dto) {
    $payload = fbtFixture($slug);

    $perdidos = fbtCamposPerdidos($payload, $dto::fromArray($payload)->toArray());

    expect($perdidos)->toBe([], 'campos descartados/alterados: '.implode(', ', $perdidos));
})->with('fixtures oficiais do FBT');

it('mantem o de/para entre o id do armazem FBT e os ids do TikTok Shop', function () {
    $w = FbtWarehouseListResponseDTO::fromArray(fbtFixture('fbt-warehouse-list'))->warehouses[0];

    // Um armazem FISICO do FBT corresponde a VARIOS ids de armazem do Shop —
    // e' o `warehouse_id` do pedido que aponta pra ca'. Sem esta lista nao ha'
    // como cruzar pedido x estoque FBT.
    expect($w->fbtWarehouseId)->toBe('121219993203023')
        ->and($w->warehouseIds)->toBe(['161213293423029', '181213293423030'])
        ->and($w->fbtWarehouseId)->not->toBeIn($w->warehouseIds);

    // Endereco do armazem: e' ele que vai como local de retirada na nota de
    // venda por fulfillment. Aqui a chave e' `address_line_1` COM underscore,
    // ao contrario do endereco do PEDIDO (`address_line1`) — a divergencia que
    // exigiu #[JsonKey].
    expect($w->addresses[0]->addressLine1)->toBe('1420 Tamarind Ave, Rialto, San Bernardino')
        ->and($w->addresses[0]->regionCode)->toBe('US');
});

it('separa o saldo vendavel do reservado, e o nivel goods do nivel sku', function () {
    $item = FbtInventorySearchResponseDTO::fromArray(fbtFixture('fbt-search-inventory'))->inventory[0];

    // "Quanto posso vender" e' `availableQuantity`, NUNCA `totalQuantity`:
    // o total inclui reservado e inutilizavel.
    expect($item->onHandDetail->totalQuantity)->toBe(20)
        ->and($item->onHandDetail->availableQuantity)->toBe(5)
        ->and($item->onHandDetail->reservedQuantity)->toBe(15)
        ->and($item->onHandDetail->unfulfillableQuantity)->toBe(0);

    // No nivel do SKU o campo `unfulfillable_quantity` NAO existe — por isso ele
    // e' nullable e nao pode virar 0 por default (0 mentiria sobre o saldo).
    expect($item->goods->skus[0]->onHandDetail->unfulfillableQuantity)->toBeNull();

    // Estoque em transito NAO e' disponivel: no ERP ja' saiu por nota de
    // remessa mas ainda nao chegou ao armazem de terceiro.
    expect($item->inTransitQuantity)->toBe(10);
});

it('trata o movimento de estoque como razao com sinal e origem', function () {
    $rec = FbtInventoryRecordSearchResponseDTO::fromArray(fbtFixture('fbt-search-inventory-record'))->inventoryRecords[0];

    // changed = final - inicial. Negativo e' SAIDA; e o `order.type` e' o que
    // decide se aquele movimento tem nota (INBOUND/CONSIGN) ou e' ajuste sem
    // contrapartida fiscal.
    expect($rec->changedQuantity)->toBe($rec->finalOnHandQuantity - $rec->initialOnHandQuantity)
        ->and($rec->order->type)->toBe('INBOUND_ORDER')
        ->and($rec->inventoryGoodsType)->toBe('NORMAL')
        ->and($rec->createTime)->toBe(1212139230); // epoch em SEGUNDOS
});

it('mantem peso e dimensao como STRING e separa declarado de conferido', function () {
    $goods = FbtGoodsSearchResponseDTO::fromArray(fbtFixture('fbt-search-goods'))->goods[0];

    // "1.122" tipado float viraria 1.122 e voltaria "1.122" -> ok, mas "1"
    // viraria 1 e voltaria "1"... e "1.100" perderia o zero. String ponta a ponta.
    expect($goods->merchantDeclarationInfo->weight->value)->toBe('1.122')
        ->and($goods->merchantDeclarationInfo->weight->value)->toBeString()
        ->and($goods->merchantDeclarationInfo->dimension->length)->toBe('2.122');

    // O armazem RE-MEDE no primeiro inbound: divergencia entre declarado e
    // conferido e' o que gera cobranca retroativa de tarifa.
    expect($goods->warehouseConfirmationInfo->dimension->length)->toBe('2.5')
        ->and($goods->warehouseConfirmationInfo->dimension->length)
        ->not->toBe($goods->merchantDeclarationInfo->dimension->length);
});

it('preserva as chaves com underscore no meio do numero da norma UN 38.3', function () {
    $payload = fbtFixture('fbt-search-goods');
    $bateria = FbtGoodsSearchResponseDTO::fromArray($payload)->goods[0]->hazmatInfo->batteryExtraInfo;

    // camelCase->snake_case automatico geraria `is_need_un383_test`; a API manda
    // `is_need_un_38_3_test`. Sem #[JsonKey] o campo sumia em silencio.
    expect($bateria->isNeedUn383Test)->toBeFalse()
        ->and($bateria->un383TestFileUrl)->toBe('https://example.com/fbt/msds-report.pdf')
        ->and(array_keys($bateria->toArray()))
        ->toContain('is_need_un_38_3_test')
        ->toContain('un_38_3_test_file_url');

    // Contagem de bateria e' STRING na doc, nao int.
    expect($bateria->batteryCountPerPackage)->toBe('2');
});

it('devolve os tres blocos de hazmat mesmo quando so um tipo se aplica', function () {
    $hazmat = FbtGoodsSearchResponseDTO::fromArray(fbtFixture('fbt-search-goods'))->goods[0]->hazmatInfo;

    // Quem manda e' `hazmatType`; a presenca dos blocos nao indica o tipo.
    expect($hazmat->hazmatType)->toBe('MAGNETIZED')
        ->and($hazmat->batteryExtraInfo)->not->toBeNull()
        ->and($hazmat->flammableLiquidsExtraInfo)->not->toBeNull()
        ->and($hazmat->aerosolExtraInfo)->not->toBeNull();

    // O exemplo OFICIAL manda "CLASS_2\n" — com quebra de linha. Comparacao
    // estrita crua falha; sempre trim().
    expect($hazmat->dgClass)->toBe("CLASS_2\n")
        ->and(trim((string) $hazmat->dgClass))->toBe('CLASS_2');
});

it('expoe o vinculo goods<->sku, que e o de/para que faz o estoque vender', function () {
    $goods = FbtGoodsSearchResponseDTO::fromArray(fbtFixture('fbt-search-goods'))->goods[0];

    // GOODS != SKU != PRODUTO: tres ids distintos na mesma resposta.
    expect($goods->id)->toBe('86533997569')
        ->and($goods->skus[0]->id)->toBe('1729439634328225260')
        ->and($goods->skus[0]->product->id)->toBe('1729439631253210604');

    // `matched=false` seria estoque fisico que NAO abastece anuncio nenhum.
    expect($goods->skus[0]->matched)->toBeTrue();

    // `referenceCode` e' o codigo do SELLER — chave do de/para com o ERP.
    expect($goods->referenceCode)->toBe('abc32323');
});

it('nao existe status atual na ordem de inbound: e o ultimo status_log', function () {
    $ordem = FbtInboundOrderDetailResponseDTO::fromArray(fbtFixture('fbt-inbound-order-details'))->inboundOrders[0];

    expect($ordem->statusLogs[0]->newStatus)->toBe('RECEIVED')
        ->and($ordem->statusLogs[0]->previousStatus)->toBe('PARTIALLY_RECEIVED');

    // Dois ids pra mesma ordem: as APIs so' aceitam o NUMERICO; o "IBR..." e'
    // so' pra exibicao.
    expect($ordem->orderId)->toBe('5766071177167344427')
        ->and($ordem->orderIdString)->toBe('IBR5766071177167344427')
        ->and($ordem->orderIdString)->toBe('IBR'.$ordem->orderId);
});

it('fecha planejado x recebido — o numero que confere a nota de remessa', function () {
    $lote = FbtInboundOrderDetailResponseDTO::fromArray(fbtFixture('fbt-inbound-order-details'))
        ->inboundOrders[0]->inboundGoodsLotReceiveItemDetails[0];

    // total = normal + defective. O que veio avariado NAO volta pro vendavel,
    // entao "recebeu tudo" nao significa "tem tudo pra vender".
    expect($lote->totalReceivedCount)->toBe($lote->normalReceivedCount + $lote->defectiveReceivedCount)
        ->and($lote->plannedCount)->toBe(100)
        ->and($lote->normalReceivedCount)->toBe(98)
        ->and($lote->defectiveReceivedCount)->toBe(2)
        // epoch em SEGUNDOS e INT aqui (no carton o equivalente vem STRING)
        ->and($lote->expirationTime)->toBe(1735689600);
});

it('conta CAIXAS na caixa planejada e PECAS nos itens dela', function () {
    $caixa = FbtInboundMethodDetailResponseDTO::fromArray(fbtFixture('fbt-inbound-method-detail'))
        ->info[0]->allocationShipments[0]->cartons[0];

    // quantity=2 sao DUAS caixas identicas de 20 pecas => 40 pecas no total.
    // Ler `quantity` como peca erra o volume da nota por um fator inteiro.
    expect($caixa->quantity)->toBe(2)
        ->and($caixa->items[0]->quantity)->toBe(20)
        ->and($caixa->cartonNums)->toBe(['C0001', 'C0002'])
        ->and($caixa->quantity * $caixa->items[0]->quantity)->toBe(40);

    // Validade no carton vem STRING (no recebimento vem int) — nao unifique.
    expect($caixa->items[0]->expirationTimestamp)->toBe('1623812664')->toBeString();
});

it('mantem os timestamps da janela de inbound como STRING, pra devolver identicos', function () {
    $metodo = FbtAvailableInboundMethodsResponseDTO::fromArray(fbtFixture('fbt-available-inbound-methods'))
        ->availableInboundMethodList[0];

    // O passo seguinte exige o MESMO valor de volta; converter pra int e
    // reserializar muda a representacao e a API recusa.
    expect($metodo->availableTimeWindowList[0]->startTimestamp)->toBe('1774162800')->toBeString();

    // Tarifa de colocacao e' DINHEIRO: string, com as casas decimais intactas.
    expect($metodo->placementFeeEstimate->amount)->toBe('13.50')->toBeString()
        ->and($metodo->placementFeeEstimate->currency)->toBe('USD');
});

it('sinaliza que o placement e assincrono e expira', function () {
    $detalhe = FbtInboundMethodDetailResponseDTO::fromArray(fbtFixture('fbt-inbound-method-detail'));

    // "Executing" em CamelCase, ao contrario dos enums SCREAMING_CASE do resto
    // da API — comparar com 'EXECUTING' nunca casa.
    expect($detalhe->placementTaskStatus)->toBe('Executing')
        ->and($detalhe->expirationTimestamp)->toBe('1774767600')->toBeString();

    // O label print tem o mesmo padrao assincrono, com outro vocabulario ainda.
    expect(FbtInboundLabelResponseDTO::fromArray(fbtFixture('fbt-inbound-order-labels'))->taskStatus)
        ->toBe('PROCESSING');
});

it('quebra o pedido MCF em consign orders e diz quem cancelou', function () {
    $pedido = FbtMcfOrderResponseDTO::fromArray(fbtFixture('fbt-mcf-order-status'))->mcfOrder;

    // `externalOrderId` e' a unica ponte com o pedido/nota do nosso OMS.
    expect($pedido->externalOrderId)->toBe('shopify202208291503530001100220033')
        ->and($pedido->mcfOrderId)->toBe('7136104329798256386');

    $consign = $pedido->consignOrders[0];

    // Diferente do pedido do TikTok Shop (onde cada line_item e' 1 unidade),
    // aqui EXISTE quantidade — e a chave do item e' `id`, nao `goods_id`.
    expect($consign->goods[0]->id)->toBe('123124124')
        ->and($consign->goods[0]->quantity)->toBe(1);

    // false = cancelado pelo NOSSO OMS; true = cancelado pela plataforma FBT.
    // Sem esse campo os dois casos chegam identicos como CLOSED.
    expect($consign->isPlatformClosed)->toBeFalse()
        ->and($consign->issue)->toBe('Lack of inventory');
});

it('devolve o status por consign order no cancelamento, em minuscula', function () {
    $consign = FbtMcfOrderResponseDTO::fromArray(fbtFixture('fbt-cancel-mcf-order'))
        ->mcfOrder->consignOrders[0];

    // O exemplo oficial responde "cancelled" MINUSCULO, enquanto o status do
    // pedido usa SCREAMING_CASE (PENDING, SHIPPED...). Comparacao
    // case-sensitive contra a lista de enums nao casa.
    expect($consign->status)->toBe('cancelled')
        ->and($consign->status)->not->toBe(strtoupper((string) $consign->status));
});

it('trata is_mcf como INT (0/1), nao bool', function () {
    $status = FbtMerchantMcfStatusResponseDTO::fromArray(fbtFixture('fbt-merchant-mcf-status'))->mcfStatus;

    // Tipar bool serializaria de volta como `false` e quebraria o roundtrip.
    expect($status->isMcf)->toBe(0)->toBeInt()
        ->and($status->toArray())->toBe(['is_mcf' => 0]);
});

it('reporta sucesso POR LINHA no lote de goods, mesmo com code 0 global', function () {
    $result = FbtCreateGoodsResponseDTO::fromArray(fbtFixture('fbt-create-goods'))
        ->createResultInfo->createResultList[0];

    // O proprio exemplo da doc traz is_success=false COM tts_goods_id
    // preenchido: a presenca do id NAO prova que criou.
    expect($result->isSuccess)->toBeFalse()
        ->and($result->ttsGoodsId)->toBe('9988776655')
        ->and($result->failReason)->toBe('Goods creation failed.');

    // O update, ao contrario do create, devolve OBJETO unico (um goods por
    // chamada) — nao lista.
    $update = FbtUpdateGoodsResponseDTO::fromArray(fbtFixture('fbt-update-goods'))->updateResultInfo;
    expect($update->isSuccess)->toBeTrue()->and($update->ttsGoodsId)->toBe('1233444');

    $rel = FbtGoodsSkuRelationResponseDTO::fromArray(fbtFixture('fbt-goods-sku-relation'))
        ->operateGoodsSkuRelationInfo->operateResultList[0];
    expect($rel->isSuccess)->toBeFalse()->and($rel->failReason)->toBe('SKU does not exist.');
});

it('materializa N ordens de inbound a partir de UM plano confirmado', function () {
    $ids = FbtConfirmInboundMethodResponseDTO::fromArray(fbtFixture('fbt-confirm-inbound-method'))->inboundOrderIds;

    // Cada id e' uma remessa fisica distinta => no ERP, uma nota de remessa
    // cada. Tratar como "a ordem do plano" (singular) perde nota.
    expect($ids)->toHaveCount(2)
        ->and($ids)->toBe(['5766071177167344427', '5766071177167344428']);

    // Ids vem SEM o prefixo IBR — e' assim que ship/cancel/tracking os querem.
    foreach ($ids as $id) {
        expect($id)->not->toStartWith('IBR');
    }

    expect(FbtInboundPlanResponseDTO::fromArray(fbtFixture('fbt-inbound-plan'))->inboundPlan->planId)
        ->toBe('5766071177167344427')
        ->and(FbtShipInboundOrderResponseDTO::fromArray(fbtFixture('fbt-ship-inbound-order'))->inboundOrderId)
        ->toBe('5766071177167344427');
});

it('devolve regra de hazmat/validade por SKU do Shop, nao por goods', function () {
    $item = FbtGoodsHazmatExpirationResponseDTO::fromArray(fbtFixture('fbt-goods-hazmat-expiration'))
        ->hazmatAndExpirationInfo->hazmatAndExpirationDtoList[0];

    // Consultado ANTES de criar o goods: a chave e' o SKU do TikTok Shop.
    expect($item->ttsSkuId)->toBe('723456789012345678')
        ->and($item->isLotCode)->toBeTrue()
        ->and($item->isExpirationManagement)->toBeTrue();

    // Os dias sao contados PRA TRAS a partir do vencimento do lote.
    expect($item->expBaseInfo->inboundCutoffDays)->toBe(90)
        ->and($item->expBaseInfo->salesCutoffDays)->toBe(70);
});

it('agrega o estoque MCF por goods, com nome de campo diferente do inventario', function () {
    $mcf = FbtMcfGoodsInventoryResponseDTO::fromArray(fbtFixture('fbt-mcf-goods-inventory'))->goodsInventoryList[0];

    // `available_qty` (abreviado, agregado) != `available_quantity` (por
    // armazem) do Search FBT Inventory. Nao sao o mesmo numero.
    expect($mcf->goodsId)->toBe('69167899745030')
        ->and($mcf->availableQty)->toBe(10)
        ->and($mcf->toArray())->toHaveKey('available_qty')
        ->and($mcf->toArray())->not->toHaveKey('available_quantity');
});

it('devolve data vazio quando o seller nao esta onboarded no FBT', function () {
    // O TikTok responde code 0 com `data` VAZIO (nao erro) pra loja sem FBT.
    // E' o teste barato antes de gastar quota nas rotinas pesadas do grupo.
    expect(FbtOnboardedRegionsResponseDTO::fromArray([])->onboardedRegions)->toBeNull();

    expect(FbtOnboardedRegionsResponseDTO::fromArray(fbtFixture('fbt-onboarded-regions'))
        ->onboardedRegions[0]->regionCode)->toBe('GB');
});
