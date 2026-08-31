<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Fbt;

use InvalidArgumentException;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
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
 * FBT — Fulfilled by TikTok: o fulfillment do proprio canal, equivalente ao
 * Full do Mercado Livre e ao FBA da Amazon.
 *
 * POR QUE ISSO NAO E' "SO' LOGISTICA" NO NOSSO ERP
 * ------------------------------------------------
 * Mercadoria em armazem FBT esta' em poder de TERCEIRO. Toda venda por
 * fulfillment exige DUAS notas fiscais:
 *   1. a de REMESSA/RETORNO (nosso CD -> armazem FBT, e a volta), disparada
 *      pelo fluxo de inbound daqui (`shipInboundOrder`) e pelos retornos que
 *      aparecem em `searchInventoryRecords`;
 *   2. a de VENDA, emitida quando o pedido sai do armazem.
 * Por isso os endpoints de leitura deste grupo interessam ao fiscal e ao
 * estoque, nao so' a' expedicao: `searchInventoryRecords` e' o livro-razao que
 * permite conferir movimento fisico x nota emitida, e
 * `inboundGoodsLotReceiveItemDetails` (em `getInboundOrderDetails`) e' o que
 * fecha planejado x recebido contra a nota de remessa.
 *
 * TRES VOCABULARIOS QUE NAO SE MISTURAM
 * -------------------------------------
 *   - GOODS  : entidade fisica do FBT, id proprio. Quase toda API daqui usa ele.
 *   - SKU/PRODUTO: entidade do TikTok Shop (anuncio). So' aparece no goods via
 *     `skus[]` e nos endpoints de vinculo.
 *   - WAREHOUSE: `fbt_warehouse_id` (FBT) != `warehouse_id` do PEDIDO (Shop).
 *     O de/para vem de `getWarehouses()`.
 *
 * VERSIONAMENTO: aqui NAO existe versao default. Cada endpoint nasceu numa
 * versao propria (202408, 202409, 202410, 202601, 202602, 202603, 202607) e
 * elas convivem — inclusive dentro do mesmo fluxo de inbound. Nunca "unifique".
 *
 * PAGINACAO: page token opaco, igual ao search de pedidos.
 */
class FbtMethods extends BaseMethods
{
    /** Teto de ids por filtro nos searches de goods/inventario. */
    private const MAX_IDS_PER_FILTER = 100;

    /** Teto de ordens de inbound por consulta de detalhe. */
    private const MAX_INBOUND_ORDERS_PER_CALL = 10;

    /** Teto de goods por consulta de estoque MCF. */
    private const MAX_MCF_GOODS_PER_CALL = 50;

    private const MAX_PAGE_SIZE = 100;

    // ---------------------------------------------------------------------
    // Leitura — cadastro e habilitacao
    // ---------------------------------------------------------------------

    /**
     * Armazens FBT visiveis pra loja (sem paginacao).
     *
     * Devolve TAMBEM os nao assinados: filtre por `subscribed` antes de planejar
     * envio. E' daqui que sai o de/para `fbtWarehouseId` <-> `warehouseIds`
     * (o id que aparece no pedido), sem o qual cruzar pedido x estoque FBT
     * devolve vazio.
     */
    public function getWarehouses(string $version = '202408'): FbtWarehouseListResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/fbt/{$version}/warehouses");

        return FbtWarehouseListResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Regioes em que o seller esta' habilitado no FBT.
     *
     * Teste barato de "essa loja usa FBT?": seller nao-onboarded recebe `data`
     * VAZIO (nao erro). Chame antes de qualquer rotina pesada deste grupo.
     */
    public function getOnboardedRegions(string $version = '202409'): FbtOnboardedRegionsResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/fbt/{$version}/merchants/onboarded_regions");

        return FbtOnboardedRegionsResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Adesao do seller ao Multi Channel Fulfillment (`is_mcf` 0/1, INT).
     *
     * MCF = usar o estoque do armazem TikTok pra atender venda de outro canal.
     * Sem adesao, `createMcfOrder` falha.
     */
    public function getMerchantMcfStatus(string $version = '202601'): FbtMerchantMcfStatusResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/fbt/{$version}/merchants/mcf_status");

        return FbtMerchantMcfStatusResponseDTO::fromArray($response['data'] ?? []);
    }

    // ---------------------------------------------------------------------
    // Leitura — goods
    // ---------------------------------------------------------------------

    /**
     * Busca goods FBT com o cadastro completo (medidas, lote/validade, hazmat,
     * SIOC e os SKUs vinculados).
     *
     * FILTROS SAO INTERSECAO, nao uniao: passar `goodsIds` + `skuIds` devolve
     * so' o que satisfaz os dois. Todos vazios = pagina mais recente por data de
     * criacao.
     *
     * O achado operacional aqui e' `skus[].matched=false`: goods no armazem que
     * NAO abastece nenhum anuncio (o equivalente ao de/para MLB x produto
     * quebrado no Full).
     *
     * @param  list<string>  $goodsIds
     * @param  list<string>  $productIds
     * @param  list<string>  $referenceCodes  codigos definidos pelo SELLER
     * @param  list<string>  $skuIds
     */
    public function searchGoods(
        array $goodsIds = [],
        array $productIds = [],
        array $referenceCodes = [],
        array $skuIds = [],
        int $pageSize = 50,
        string $pageToken = '',
        string $version = '202607',
    ): FbtGoodsSearchResponseDTO {
        $this->assertIdLimit($goodsIds, 'goods_ids');
        $this->assertIdLimit($productIds, 'product_ids');
        $this->assertIdLimit($referenceCodes, 'reference_codes');
        $this->assertIdLimit($skuIds, 'sku_ids');

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fbt/{$version}/goods/search",
            $this->pageQuery($pageSize, $pageToken),
            $this->withoutEmpty([
                'goods_ids' => $goodsIds,
                'product_ids' => $productIds,
                'reference_codes' => $referenceCodes,
                'sku_ids' => $skuIds,
            ]),
        );

        return FbtGoodsSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Regras de hazmat/validade que o FBT IMPOE aos SKUs informados.
     *
     * Consulte ANTES de criar goods: e' o que diz se o SKU vai exigir declaracao
     * de perigoso e controle de lote (suplemento costuma exigir os dois). A
     * chave aqui e' o SKU do Shop, nao goods_id.
     *
     * @param  list<string>  $ttsSkuIds
     */
    public function searchGoodsHazmatAndExpirationInfo(
        array $ttsSkuIds,
        string $version = '202603',
    ): FbtGoodsHazmatExpirationResponseDTO {
        if ($ttsSkuIds === []) {
            throw new InvalidArgumentException('tts_sku_ids e obrigatorio.');
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fbt/{$version}/goods/hazmat_and_expiration_info/search",
            [],
            ['tts_sku_ids' => array_values($ttsSkuIds)],
        );

        return FbtGoodsHazmatExpirationResponseDTO::fromArray($response['data'] ?? []);
    }

    // ---------------------------------------------------------------------
    // Leitura — inventario
    // ---------------------------------------------------------------------

    /**
     * Saldo por (goods x armazem).
     *
     * A API aceita `goodsIds` OU `skuIds`, NUNCA os dois — o guard abaixo evita
     * a chamada silenciosamente errada. Sem filtro nenhum, devolve todo goods
     * com saldo != 0 em todo armazem assinado.
     *
     * O mesmo goods aparece uma vez POR ARMAZEM: somar sem agrupar duplica.
     *
     * @param  list<string>  $goodsIds
     * @param  list<string>  $fbtWarehouseIds
     * @param  list<string>  $skuIds
     */
    public function searchInventory(
        array $goodsIds = [],
        array $fbtWarehouseIds = [],
        array $skuIds = [],
        int $pageSize = 50,
        string $pageToken = '',
        string $version = '202408',
    ): FbtInventorySearchResponseDTO {
        if ($goodsIds !== [] && $skuIds !== []) {
            throw new InvalidArgumentException(
                'Este endpoint aceita goods_ids OU sku_ids, nunca os dois juntos.'
            );
        }
        $this->assertIdLimit($goodsIds, 'goods_ids');
        $this->assertIdLimit($skuIds, 'sku_ids');

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fbt/{$version}/inventory/search",
            $this->pageQuery($pageSize, $pageToken),
            $this->withoutEmpty([
                'goods_ids' => $goodsIds,
                'fbt_warehouse_ids' => $fbtWarehouseIds,
                'sku_ids' => $skuIds,
            ]),
        );

        return FbtInventorySearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Movimentacoes de estoque no armazem FBT — o livro-razao do estoque em
     * poder de terceiro.
     *
     * E' o endpoint que sustenta a conferencia fiscal: cada linha tem `order.type`
     * (INBOUND_ORDER, CONSIGN_ORDER, CUSTOMER_RETURN, DISPOSAL, ajustes...) e
     * `changed_quantity` com sinal. Entrada/saida com contrapartida deveria ter
     * nota; ajuste e descarte NAO tem — sao justamente os que viram acerto de
     * inventario.
     *
     * Janela em epoch de SEGUNDOS. Sem `createTimeGe`, o padrao da API e' "desde
     * sempre", o que num catalogo grande vira paginacao infinita — sempre limite.
     *
     * @param  list<string>  $goodsIds
     * @param  list<string>  $fbtWarehouseIds
     */
    public function searchInventoryRecords(
        array $goodsIds = [],
        array $fbtWarehouseIds = [],
        ?int $createTimeGe = null,
        ?int $createTimeLe = null,
        int $pageSize = 50,
        string $pageToken = '',
        string $version = '202410',
    ): FbtInventoryRecordSearchResponseDTO {
        $this->assertIdLimit($goodsIds, 'goods_ids');

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fbt/{$version}/inventory_records/search",
            $this->pageQuery($pageSize, $pageToken),
            $this->withoutEmpty([
                'goods_ids' => $goodsIds,
                'fbt_warehouse_ids' => $fbtWarehouseIds,
                'create_time_ge' => $createTimeGe,
                'create_time_le' => $createTimeLe,
            ]),
        );

        return FbtInventoryRecordSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Estoque disponivel AGREGADO por goods, pra decidir se aceita um pedido MCF.
     *
     * Nao confundir com `searchInventory`: aqui o numero e' a soma dos armazens
     * e o campo se chama `available_qty` (abreviado). Max 50 goods por chamada.
     *
     * @param  list<string>  $goodsIds
     */
    public function queryGoodsInventoryForMcf(
        array $goodsIds,
        string $version = '202601',
    ): FbtMcfGoodsInventoryResponseDTO {
        if ($goodsIds === []) {
            throw new InvalidArgumentException('goods e obrigatorio.');
        }
        if (count($goodsIds) > self::MAX_MCF_GOODS_PER_CALL) {
            throw new InvalidArgumentException(sprintf(
                'Max %d goods por chamada (recebeu %d).',
                self::MAX_MCF_GOODS_PER_CALL,
                count($goodsIds),
            ));
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fbt/{$version}/mcf/goods/inventory/search",
            [],
            ['goods' => array_map(static fn (string $id): array => ['goods_id' => $id], array_values($goodsIds))],
        );

        return FbtMcfGoodsInventoryResponseDTO::fromArray($response['data'] ?? []);
    }

    // ---------------------------------------------------------------------
    // Leitura — inbound
    // ---------------------------------------------------------------------

    /**
     * Detalhe de ate' 10 ordens de inbound.
     *
     * Aceita id COM ou SEM o prefixo "IBR" — a API so' engole o numerico, entao
     * o prefixo e' removido aqui (mandar "IBR123" derruba a chamada).
     *
     * NAO existe campo de status atual: o corrente e' o `new_status` do
     * `status_logs` mais recente.
     *
     * `$includeCartonDetails` traz a quebra caixa a caixa; o TikTok omite o
     * bloco quando false (por performance), entao ausencia NAO significa
     * "sem caixas".
     *
     * @param  list<string>  $orderIds
     */
    public function getInboundOrderDetails(
        array $orderIds,
        bool $includeCartonDetails = false,
        string $version = '202602',
    ): FbtInboundOrderDetailResponseDTO {
        if ($orderIds === []) {
            throw new InvalidArgumentException('order_ids e obrigatorio.');
        }
        if (count($orderIds) > self::MAX_INBOUND_ORDERS_PER_CALL) {
            throw new InvalidArgumentException(sprintf(
                'Max %d ordens por chamada (recebeu %d). Fragmente o lote.',
                self::MAX_INBOUND_ORDERS_PER_CALL,
                count($orderIds),
            ));
        }

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/fbt/{$version}/inbound_orders",
            [
                'order_ids' => implode(',', array_map([$this, 'stripInboundPrefix'], array_values($orderIds))),
                'include_carton_details' => $includeCartonDetails ? 'true' : 'false',
            ],
        );

        return FbtInboundOrderDetailResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Passo 2 do inbound: metodos de envio disponiveis pro plano.
     *
     * Fluxo completo: `createOrUpdateInboundPlan` -> ESTE -> `getInboundMethodDetail`
     * -> `confirmInboundMethod` -> `shipInboundOrder`.
     *
     * D2FC pode QUEBRAR a carga em varios destinos (= varias notas de remessa);
     * ONE_HUB e' destino unico com tarifa de colocacao (= uma nota).
     */
    public function listAvailableInboundMethods(
        string $planId,
        string $version = '202602',
    ): FbtAvailableInboundMethodsResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fbt/{$version}/list_available_inbound_method",
            [],
            ['plan_id' => $planId],
        );

        return FbtAvailableInboundMethodsResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Passo 3: como o FBT propoe dividir a carga, e quanto cobra.
     *
     * ASSINCRONO — a primeira resposta vem `placement_task_status=Executing` com
     * `info` vazio; repita ate' `Succeed`. O resultado EXPIRA em um dia
     * (`expiration_timestamp`).
     *
     * Os timestamps da janela sao STRING e precisam voltar IDENTICOS aos que o
     * `listAvailableInboundMethods` devolveu — por isso a assinatura pede string,
     * nao int.
     */
    public function getInboundMethodDetail(
        string $planId,
        string $inboundMethod,
        string $startTimestamp,
        string $endTimestamp,
        string $version = '202602',
    ): FbtInboundMethodDetailResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fbt/{$version}/inbound_method_detail",
            [],
            [
                'plan_id' => $planId,
                'inbound_method' => $inboundMethod,
                'estimate_arrival_time' => [
                    'start_timestamp' => $startTimestamp,
                    'end_timestamp' => $endTimestamp,
                ],
            ],
        );

        return FbtInboundMethodDetailResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Gera etiquetas/documentos da ordem de inbound (ZIP).
     *
     * ASSINCRONO: com `task_status=PROCESSING` o `download_url` ainda nao presta;
     * repita ate' `SUCCESS`.
     *
     * `printItems`: CARTON_PALLET_LABEL | PACKING_PICKING_LIST | FBT_BARCODE.
     * Vazio = so' a etiqueta de caixa.
     *
     * @param  list<string>  $printItems
     */
    public function printInboundOrderLabels(
        string $orderId,
        array $printItems = [],
        ?string $cartonLabelFormat = null,
        ?string $barcodeFormat = null,
        string $version = '202602',
    ): FbtInboundLabelResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fbt/{$version}/inbound_orders/label_print",
            [],
            $this->withoutEmpty([
                'order_id' => $this->stripInboundPrefix($orderId),
                'print_items' => $printItems,
                'carton_label_format' => $cartonLabelFormat,
                'barcode_format' => $barcodeFormat,
            ]),
        );

        return FbtInboundLabelResponseDTO::fromArray($response['data'] ?? []);
    }

    // ---------------------------------------------------------------------
    // Escrita — inbound (remessa pro armazem do TikTok)
    // ---------------------------------------------------------------------

    /**
     * Passo 1: cria (sem `planId`) ou atualiza (com `planId`) o plano de inbound.
     *
     * Create e update sao o MESMO endpoint. Passe SEMPRE `idempotentKey`: sem
     * ela um retry de rede vira plano duplicado — e plano duplicado vira remessa
     * fisica duplicada, com nota de remessa a mais.
     *
     * @param  list<array<string, mixed>>  $goodsItems  [{goods_id, quantity}]
     * @param  list<array<string, mixed>>  $cartons     [{carton_type, items[], quantity, carton_nums[], box_measurements{}}]
     */
    public function createOrUpdateInboundPlan(
        array $goodsItems = [],
        array $cartons = [],
        ?string $planId = null,
        ?string $idempotentKey = null,
        string $version = '202602',
    ): FbtInboundPlanResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fbt/{$version}/create_update_inbound_plan",
            [],
            $this->withoutEmpty([
                'plan_id' => $planId,
                'idempotent_key' => $idempotentKey,
                'goods_items' => $goodsItems,
                'cartons' => $cartons,
            ]),
        );

        return FbtInboundPlanResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Passo 4: confirma a opcao de colocacao e materializa as ordens de inbound.
     *
     * UM plano vira N ordens (uma por destino no D2FC). Cada id devolvido e' uma
     * remessa fisica distinta — logo, no ERP, uma nota de remessa cada.
     *
     * Os timestamps tem que bater EXATAMENTE com os do
     * `listAvailableInboundMethods`.
     */
    public function confirmInboundMethod(
        string $planId,
        string $placementOptionId,
        string $inboundMethod,
        string $startTimestamp,
        string $endTimestamp,
        string $version = '202603',
    ): FbtConfirmInboundMethodResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fbt/{$version}/confirm_inbound_method",
            [],
            [
                'plan_id' => $planId,
                'placement_option_id' => $placementOptionId,
                'inbound_method' => $inboundMethod,
                'estimate_arrival_time' => [
                    'start_timestamp' => $startTimestamp,
                    'end_timestamp' => $endTimestamp,
                ],
            ],
        );

        return FbtConfirmInboundMethodResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Passo 5: declara que a carga SAIU do nosso CD.
     *
     * E' o gatilho natural da nota de remessa e da baixa do estoque proprio pra
     * "em transito" — a partir daqui a mercadoria e' estoque em poder de
     * terceiro.
     *
     * @param  array<string, mixed>  $shipmentOption  {shipping_method: SMALL_PARCEL|FREIGHT, freight_type?: FTL|LTL}
     * @param  list<array<string, mixed>>  $cartons
     */
    public function shipInboundOrder(
        string $inboundOrderId,
        array $shipmentOption,
        array $cartons = [],
        string $version = '202603',
    ): FbtShipInboundOrderResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fbt/{$version}/ship_inbound_order",
            [],
            $this->withoutEmpty([
                'inbound_order_id' => $this->stripInboundPrefix($inboundOrderId),
                'cartons' => $cartons,
                'shipment_option' => $shipmentOption,
            ]),
        );

        return FbtShipInboundOrderResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Cancela uma ordem de inbound. `data` volta VAZIO — o sucesso e' o proprio
     * `code: 0` (erro vira excecao no BaseMethods), dai o retorno bool.
     *
     * Se a nota de remessa ja' foi emitida, cancelar aqui NAO a cancela: sobra
     * mercadoria com nota e sem destino. Cancele antes de faturar.
     */
    public function cancelInboundOrder(
        string $orderId,
        string $cancelReason,
        string $version = '202602',
    ): bool {
        $this->makeRequest(
            HttpMethod::POST,
            "/fbt/{$version}/inbound_orders/cancel",
            [],
            [
                'order_id' => $this->stripInboundPrefix($orderId),
                'cancel_reason' => $cancelReason,
            ],
        );

        return true;
    }

    /**
     * Informa o rastreio POR CAIXA da ordem de inbound. `data` volta vazio.
     *
     * `carton_number` amarra o rastreio a' caixa ("C0001") do plano — e' o que
     * permite, depois, casar `logistic_info_list` com `carton_details`.
     *
     * @param  list<array<string, string>>  $parcelTrackingInfo  [{provider_name, tracking_number, carton_number}]
     */
    public function updateInboundOrderTracking(
        string $orderId,
        array $parcelTrackingInfo,
        string $version = '202602',
    ): bool {
        $this->makeRequest(
            HttpMethod::POST,
            "/fbt/{$version}/inbound_orders/tracking_update",
            [],
            $this->withoutEmpty([
                'order_id' => $this->stripInboundPrefix($orderId),
                'parcel_tracking_info' => $parcelTrackingInfo,
            ]),
        );

        return true;
    }

    // ---------------------------------------------------------------------
    // Escrita — goods
    // ---------------------------------------------------------------------

    /**
     * Cria goods FBT a partir de SKUs do TikTok Shop (lote).
     *
     * SUCESSO E' POR LINHA: a chamada volta `code: 0` mesmo com todos os itens
     * falhando. Percorra `createResultInfo->createResultList` e teste
     * `isSuccess` de cada um — o proprio exemplo da doc traz `is_success:false`
     * COM `tts_goods_id` preenchido.
     *
     * @param  list<array<string, mixed>>  $createGoodsDtoList
     */
    public function createGoods(
        array $createGoodsDtoList,
        string $version = '202607',
    ): FbtCreateGoodsResponseDTO {
        if ($createGoodsDtoList === []) {
            throw new InvalidArgumentException('create_goods_dto_list e obrigatorio.');
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fbt/{$version}/goods/create_goods",
            [],
            ['create_goods_dto_list' => array_values($createGoodsDtoList)],
        );

        return FbtCreateGoodsResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Atualiza o cadastro de UM goods.
     *
     * NAO E' PATCH: nome, imagem, peso/dimensao, hazmat e flags de lote sao
     * obrigatorios, e omitir um bloco APAGA o que estava la'. Leia com
     * `searchGoods` e reenvie o objeto inteiro.
     *
     * @param  array<string, mixed>  $updateGoodsDto
     */
    public function updateGoodsInfo(
        array $updateGoodsDto,
        string $version = '202607',
    ): FbtUpdateGoodsResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fbt/{$version}/goods/update_goods_info",
            [],
            ['update_goods_dto' => $updateGoodsDto],
        );

        return FbtUpdateGoodsResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Liga (BIND) ou desliga (UN_BIND) goods FBT dos SKUs do TikTok Shop.
     *
     * E' o de/para fisico <-> anuncio. Sem BIND o estoque existe no armazem mas
     * o anuncio nao vende (`matched=false` no `searchGoods`); UN_BIND corta o
     * abastecimento na hora.
     *
     * @param  list<array{tts_goods_id: string, tts_sku_ids: list<string>}>  $relations
     * @param  string  $operationType  BIND | UN_BIND
     */
    public function updateGoodsSkuRelation(
        array $relations,
        string $operationType,
        string $version = '202603',
    ): FbtGoodsSkuRelationResponseDTO {
        if ($relations === []) {
            throw new InvalidArgumentException('operate_goods_sku_relation_list e obrigatorio.');
        }
        if (! in_array($operationType, ['BIND', 'UN_BIND'], true)) {
            throw new InvalidArgumentException("operation_type deve ser BIND ou UN_BIND (recebeu '{$operationType}').");
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fbt/{$version}/goods/update_goods_sku_relation",
            [],
            [
                'operate_goods_sku_relation_list' => array_values($relations),
                'operation_type' => $operationType,
            ],
        );

        return FbtGoodsSkuRelationResponseDTO::fromArray($response['data'] ?? []);
    }

    // ---------------------------------------------------------------------
    // MCF — atender venda de OUTRO canal com estoque parado no FBT
    // ---------------------------------------------------------------------

    /**
     * Status/rastreio de um pedido MCF.
     *
     * Um pedido MCF vira N `consign_orders` (o armazem quebra por
     * disponibilidade), cada uma com rastreio proprio. `is_platform_closed`
     * desambigua quem cancelou: true = o FBT (falta de estoque, item bloqueado),
     * false = o nosso OMS. Sem ele os dois casos chegam identicos como CLOSED.
     */
    public function getMcfOrderStatus(
        string $mcfOrderId,
        string $version = '202601',
    ): FbtMcfOrderResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/fbt/{$version}/mcf_outbound_orders",
            ['mcf_order_id' => $mcfOrderId],
        );

        return FbtMcfOrderResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Cria pedido MCF: o armazem do TikTok despacha uma venda de OUTRO canal.
     *
     * `externalOrderId` e' o id do pedido no NOSSO OMS e e' a UNICA ponte entre
     * a saida fisica do armazem e o pedido/nota do ERP — sem ele a baixa de
     * estoque fica sem lastro.
     *
     * FISCAL: a saida ocorre de um armazem de TERCEIRO, entao a nota de venda do
     * canal externo tem que sair com o endereco do armazem FBT como local de
     * retirada (`getWarehouses()`), nao com o do nosso CD.
     *
     * O endereco do destinatario e' VALIDADO pelo TikTok: falhou a validacao,
     * nenhum pedido e' criado. Soma das quantidades limitada a 50.
     *
     * @param  list<array{id: string, quantity: int}>  $goods
     * @param  array<string, mixed>  $consignee  {name, phone_number, email, address{}}
     */
    public function createMcfOrder(
        string $externalOrderId,
        array $goods,
        array $consignee,
        string $version = '202607',
    ): FbtMcfOrderResponseDTO {
        if ($goods === []) {
            throw new InvalidArgumentException('goods e obrigatorio.');
        }

        $totalQuantity = array_sum(array_map(static fn (array $g): int => (int) ($g['quantity'] ?? 0), $goods));
        if ($totalQuantity > self::MAX_MCF_GOODS_PER_CALL) {
            throw new InvalidArgumentException(sprintf(
                'A soma das quantidades nao pode passar de %d (recebeu %d).',
                self::MAX_MCF_GOODS_PER_CALL,
                $totalQuantity,
            ));
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fbt/{$version}/mcf_outbound_orders",
            [],
            [
                'external_order_id' => $externalOrderId,
                'goods' => array_values($goods),
                'consignee' => $consignee,
            ],
        );

        return FbtMcfOrderResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Cancela um pedido MCF — inteiro, ou só as consign orders informadas.
     *
     * Cancelamento e' PARCIAL por natureza: a resposta traz o status resultante
     * de CADA consign order, e uma ja' despachada (SHIPPED/HANDOVER) nao volta
     * atras. Confira `consignOrders[].status` antes de estornar a nota.
     *
     * @param  list<string>  $consignOrderIds  vazio = cancela o pedido todo
     */
    public function cancelMcfOrder(
        string $mcfOrderId,
        array $consignOrderIds = [],
        string $version = '202601',
    ): FbtMcfOrderResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fbt/{$version}/mcf_outbound_orders/cancel",
            [],
            $this->withoutEmpty([
                'mcf_order_id' => $mcfOrderId,
                'consign_orders' => array_map(
                    static fn (string $id): array => ['id' => $id],
                    array_values($consignOrderIds),
                ),
            ]),
        );

        return FbtMcfOrderResponseDTO::fromArray($response['data'] ?? []);
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    /** @return array<string, mixed> */
    private function pageQuery(int $pageSize, string $pageToken): array
    {
        $query = ['page_size' => min(max($pageSize, 1), self::MAX_PAGE_SIZE)];
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        return $query;
    }

    /**
     * O TikTok recusa filtro nulo/vazio no corpo (nao trata como "sem filtro").
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    private function withoutEmpty(array $body): array
    {
        return array_filter($body, static fn ($v) => $v !== null && $v !== [] && $v !== '');
    }

    /** @param list<string> $ids */
    private function assertIdLimit(array $ids, string $field): void
    {
        if (count($ids) > self::MAX_IDS_PER_FILTER) {
            throw new InvalidArgumentException(sprintf(
                '%s aceita no maximo %d ids (recebeu %d).',
                $field,
                self::MAX_IDS_PER_FILTER,
                count($ids),
            ));
        }
    }

    /**
     * As APIs de inbound exigem o id NUMERICO; o painel e o `order_id_string`
     * mostram a forma "IBR<numero>". Mandar o prefixo derruba a chamada, entao
     * aceitamos as duas formas e normalizamos aqui.
     */
    private function stripInboundPrefix(string $orderId): string
    {
        return str_starts_with($orderId, 'IBR') ? substr($orderId, 3) : $orderId;
    }
}
