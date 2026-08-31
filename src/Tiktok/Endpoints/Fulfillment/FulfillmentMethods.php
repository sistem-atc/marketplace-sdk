<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Fulfillment;

use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\BatchShipPackagesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\CombinePackageResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\CreatePackageResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\EligibleShippingServiceResponseDTO;
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
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\UploadDeliveryImageResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment\UploadInvoiceResponseDTO;
use SistemAtc\Marketplaces\Tiktok\Support\HttpClientFactory;

/**
 * Fulfillment API do TikTok Shop (`/fulfillment/{version}/...`).
 *
 * ARMADILHA QUE ATRAVESSA O GRUPO INTEIRO: operacoes em lote respondem com
 * `code: 0` (o BaseMethods NAO lanca excecao) e listam o que falhou em
 * `data.errors`. Chamada "bem-sucedida" nao quer dizer item processado —
 * sempre inspecione `->errors`.
 *
 * As versoes NAO sao uniformes: o nucleo de pacote e' 202309, mas convivem
 * 202407 (bundle), 202502 (invoice), 202508 (tracking validation), 202510
 * (bundle v2), 202512 (create package), 202601 (redeem), 202606 (POD) e
 * 202607 (alfandega). Por isso cada metodo carrega a sua, e nao existe uma
 * DEFAULT_VERSION unica na classe.
 */
class FulfillmentMethods extends BaseMethods
{
    /** Limite declarado na doc do Search Package / Search Combinable Packages. */
    private const MAX_PAGE_SIZE = 50;

    /** Teto do arquivo de nota no Upload Invoice (1MB, ja' em base64). */
    private const MAX_INVOICE_BASE64_BYTES = 1024 * 1024;

    // ─────────────────────────────────────────────────────────────────────
    // Nota fiscal
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Sobe notas fiscais (XML) pro TikTok — `POST /fulfillment/202502/invoice/upload`.
     *
     * ONDE ESTE ENDPOINT MORA: sob **fulfillment**, nao sob finance. O
     * `Endpoints/Invoice/InvoiceMethods::getByOrderId()` aponta pra
     * `/finance/{v}/orders/{id}/invoices`, caminho que devolve "Invalid path"
     * em todas as versoes — nao existe. O caminho valido pra nota no TikTok e'
     * o daqui.
     *
     * O upload e' por PACOTE (nao por pedido): um pacote combinado carrega
     * varios `order_ids` e recebe UMA nota. E' pre-requisito do envio — o
     * shipPackage devolve 21004006/21004019 se a nota nao subiu antes.
     *
     * O arquivo vai em base64 no CORPO JSON (nao e' multipart) e o teto de
     * 1MB da doc vale sobre o base64.
     *
     * @param  array<int, array{package_id: string, order_ids: list<string>, file_type?: string, file: string}>  $invoices
     */
    public function uploadInvoice(array $invoices, string $version = '202502'): UploadInvoiceResponseDTO
    {
        if ($invoices === []) {
            throw new InvalidArgumentException('Informe ao menos uma nota para subir.');
        }

        foreach ($invoices as $invoice) {
            if (strlen((string) ($invoice['file'] ?? '')) > self::MAX_INVOICE_BASE64_BYTES) {
                throw new InvalidArgumentException(sprintf(
                    'Nota do pacote %s excede 1MB em base64 (limite do TikTok).',
                    $invoice['package_id'] ?? '?',
                ));
            }
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fulfillment/{$version}/invoice/upload",
            [],
            ['invoices' => $invoices],
        );

        return UploadInvoiceResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Atalho pra subir UMA NF-e ja' em XML cru — faz o base64 e o envelope.
     *
     * @param  list<string>  $orderIds  todos os pedidos do pacote
     */
    public function uploadInvoiceXml(
        string $packageId,
        array $orderIds,
        string $xml,
        string $version = '202502',
    ): UploadInvoiceResponseDTO {
        return $this->uploadInvoice([[
            'package_id' => $packageId,
            'order_ids' => array_values($orderIds),
            'file_type' => 'XML',
            'file' => base64_encode($xml),
        ]], $version);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Pacote — consulta
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Detalhe de UM pacote — `GET /fulfillment/{v}/packages/{package_id}`.
     *
     * E' a fonte do endereco do remetente, peso, dimensao e seguro: nada
     * disso existe no pedido. Ja' o CPF/nome do comprador NAO venha buscar
     * aqui — quando o frete e' da plataforma o TikTok mascara `name` e
     * `phone_number` do destinatario (use a Order API pra PII).
     */
    public function getPackageDetail(string $packageId, string $version = '202309'): ?PackageDetailResponseDTO
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/fulfillment/{$version}/packages/".rawurlencode($packageId),
        );

        $data = $response['data'] ?? null;

        return is_array($data) ? PackageDetailResponseDTO::fromArray($data) : null;
    }

    /**
     * Lista pacotes por janela de tempo — `POST /fulfillment/{v}/packages/search`.
     *
     * Diferente do search de PEDIDOS, aqui a janela nao tem limite declarado
     * de 30 dias e os filtros de tempo (`create_time_ge`...) vao no CORPO,
     * enquanto ordenacao e paginacao vao na QUERY. Epoch em SEGUNDOS.
     *
     * O item devolvido e' resumido (PackageSummary) — pra peso/endereco/
     * seguro e' preciso um getPackageDetail por pacote.
     *
     * @param  string|null  $packageStatus  PROCESSING | FULFILLING | COMPLETED | CANCELLED
     * @param  string  $sortField  create_time | update_time | order_pay_time
     * @param  string  $sortOrder  ASC | DESC
     */
    public function searchPackages(
        ?int $createTimeGe = null,
        ?int $createTimeLt = null,
        ?int $updateTimeGe = null,
        ?int $updateTimeLt = null,
        ?string $packageStatus = null,
        int $pageSize = 50,
        string $pageToken = '',
        string $sortField = 'create_time',
        string $sortOrder = 'DESC',
        string $version = '202309',
    ): PackageSearchResponseDTO {
        $query = [
            'page_size' => min($pageSize, self::MAX_PAGE_SIZE),
            'sort_field' => $sortField,
            'sort_order' => $sortOrder,
        ];
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        // O TikTok recusa null em filtro: manda so' o que foi informado.
        $body = array_filter([
            'create_time_ge' => $createTimeGe,
            'create_time_lt' => $createTimeLt,
            'update_time_ge' => $updateTimeGe,
            'update_time_lt' => $updateTimeLt,
            'package_status' => $packageStatus,
        ], static fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fulfillment/{$version}/packages/search",
            $query,
            $body,
        );

        return PackageSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Janelas de coleta/entrega disponiveis pro pacote —
     * `GET /fulfillment/{v}/packages/{id}/handover_time_slots`.
     *
     * O slot escolhido aqui e' o que vai em `pickup_slot` no shipPackage;
     * despachar fora da janela devolve 21011053.
     */
    public function getHandoverTimeSlots(string $packageId, string $version = '202309'): HandoverTimeSlotsResponseDTO
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/fulfillment/{$version}/packages/".rawurlencode($packageId).'/handover_time_slots',
        );

        return HandoverTimeSlotsResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Etiqueta / packing slip do pacote —
     * `GET /fulfillment/{v}/packages/{id}/shipping_documents`.
     *
     * A `docUrl` devolvida VENCE EM 24H: baixe o binario na hora.
     *
     * `INVOICE_LABEL` e' o tipo do mercado BRASILEIRO (etiqueta da nota) e
     * ignora `document_size` — vem fixo em A6. ZPL so' existe em BR e MX.
     *
     * @param  string  $documentType  SHIPPING_LABEL | PACKING_SLIP | SHIPPING_LABEL_AND_PACKING_SLIP | SHIPPING_LABEL_PICTURE | HAZMAT_LABEL | INVOICE_LABEL
     * @param  string|null  $documentSize  A6 (default) | A5
     * @param  string|null  $documentFormat  PDF (default) | ZPL
     * @param  string|null  $shippingPeriod  DEFAULT | LAST_MILE
     */
    public function getPackageShippingDocument(
        string $packageId,
        string $documentType = 'SHIPPING_LABEL',
        ?string $documentSize = null,
        ?string $documentFormat = null,
        ?string $shippingPeriod = null,
        string $version = '202309',
    ): ShippingDocumentResponseDTO {
        $query = array_filter([
            'document_type' => $documentType,
            'document_size' => $documentSize,
            'document_format' => $documentFormat,
            'shipping_period' => $shippingPeriod,
        ], static fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/fulfillment/{$version}/packages/".rawurlencode($packageId).'/shipping_documents',
            $query,
        );

        return ShippingDocumentResponseDTO::fromArray($response['data'] ?? []);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Pacote — montagem
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Cria um pacote — `POST /fulfillment/202512/packages`.
     *
     * CRIAR NAO E' DESPACHAR: sai um `package_id` em rascunho; o envio ainda
     * exige shipPackage (e, no BR, a nota subida antes).
     *
     * O `ship_type` decide QUAIS dos outros campos valem, e o resto e'
     * ignorado em silencio:
     *   1 = pedido inteiro num pacote  -> usa `order_id`
     *   2 = pedido dividido em varios  -> usa `order_line_item`
     *   3 = varios pedidos num pacote  -> usa `order_list_ids`
     *
     * @param  array<int, array{order_line_id?: string, sub_item_id?: string}>|null  $orderLineItem
     * @param  list<string>|null  $orderListIds
     * @param  array{length: string, width: string, height: string, unit: string}|null  $dimension
     * @param  array{value: string, unit: string}|null  $weight
     */
    public function createPackage(
        string $shipType,
        ?string $orderId = null,
        ?array $orderLineItem = null,
        ?array $orderListIds = null,
        ?array $dimension = null,
        ?array $weight = null,
        ?string $shippingServiceId = null,
        string $version = '202512',
    ): CreatePackageResponseDTO {
        $body = array_filter([
            'ship_type' => $shipType,
            'order_id' => $orderId,
            'order_line_item' => $orderLineItem,
            'order_list_ids' => $orderListIds,
            'dimension' => $dimension,
            'weight' => $weight,
            'shipping_service_id' => $shippingServiceId,
        ], static fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fulfillment/{$version}/packages",
            [],
            $body,
        );

        return CreatePackageResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Pacotes que PODEM ser combinados —
     * `GET /fulfillment/{v}/combinable_packages/search`.
     *
     * Os `id` retornados sao rascunhos pre-gerados: so' viram pacote de
     * verdade depois do combinePackages.
     */
    public function searchCombinablePackages(
        int $pageSize = 50,
        string $pageToken = '',
        string $version = '202309',
    ): SearchCombinablePackagesResponseDTO {
        $query = ['page_size' => min($pageSize, self::MAX_PAGE_SIZE)];
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/fulfillment/{$version}/combinable_packages/search",
            $query,
        );

        return SearchCombinablePackagesResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Combina pacotes — `POST /fulfillment/{v}/packages/combine`.
     *
     * O pacote combinado ganha um `id` NOVO; etiqueta impressa pro id antigo
     * perde a validade. Sucesso e falha convivem na mesma resposta
     * (`packages` + `errors`).
     *
     * @param  array<int, array{id: string, order_ids?: list<string>}>  $combinablePackages
     */
    public function combinePackages(array $combinablePackages, string $version = '202309'): CombinePackageResponseDTO
    {
        if ($combinablePackages === []) {
            throw new InvalidArgumentException('Informe ao menos um pacote para combinar.');
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fulfillment/{$version}/packages/combine",
            [],
            ['combinable_packages' => $combinablePackages],
        );

        return CombinePackageResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Separa um pacote — `POST /fulfillment/{v}/packages/{id}/uncombine`.
     *
     * `orderIds` seleciona quais pedidos SAIR do pacote (precisam pertencer a
     * ele). Sem lista, a plataforma desfaz a combinacao toda. Os pacotes
     * devolvidos sao NOVOS — o original deixa de existir.
     *
     * @param  list<string>  $orderIds
     */
    public function uncombinePackage(
        string $packageId,
        array $orderIds = [],
        string $version = '202309',
    ): UncombinePackagesResponseDTO {
        $body = $orderIds === [] ? [] : ['order_ids' => array_values($orderIds)];

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fulfillment/{$version}/packages/".rawurlencode($packageId).'/uncombine',
            [],
            $body,
        );

        return UncombinePackagesResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Se um pedido pode/deve ser dividido —
     * `GET /fulfillment/{v}/orders/split_attributes`.
     *
     * `order_ids` e' declarado []string na QUERY; o TikTok le lista em query
     * separada por virgula (mesma convencao do `ids` do Get Order Detail).
     *
     * @param  list<string>  $orderIds
     */
    public function getOrderSplitAttributes(array $orderIds, string $version = '202309'): OrderSplitAttributesResponseDTO
    {
        if ($orderIds === []) {
            throw new InvalidArgumentException('Informe ao menos um pedido.');
        }

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/fulfillment/{$version}/orders/split_attributes",
            ['order_ids' => implode(',', $orderIds)],
        );

        return OrderSplitAttributesResponseDTO::fromArray($response['data'] ?? []);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Pacote — despacho
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Despacha UM pacote — `POST /fulfillment/{v}/packages/{id}/ship`.
     *
     * `selfShipment` (tracking + shipping_provider_id) e' obrigatorio quando
     * o pacote e' `SEND_BY_SELLER`; em frete da plataforma ele nao vai.
     *
     * NO BR A NOTA VEM ANTES: sem uploadInvoice o TikTok recusa com
     * 21004006 / 21004019 / 21004020.
     *
     * Resposta e' `data: {}` — retorna bool porque so' ha' dois desfechos:
     * o BaseMethods lanca excecao no erro, ou passou.
     *
     * @param  array{start_time?: int, end_time?: int}|null  $pickupSlot
     * @param  array{tracking_number: string, shipping_provider_id: string}|null  $selfShipment
     */
    public function shipPackage(
        string $packageId,
        ?string $handoverMethod = null,
        ?array $pickupSlot = null,
        ?array $selfShipment = null,
        string $version = '202309',
    ): bool {
        $body = array_filter([
            'handover_method' => $handoverMethod,
            'pickup_slot' => $pickupSlot,
            'self_shipment' => $selfShipment,
        ], static fn ($v) => $v !== null);

        $this->makeRequest(
            HttpMethod::POST,
            "/fulfillment/{$version}/packages/".rawurlencode($packageId).'/ship',
            [],
            $body,
        );

        return true;
    }

    /**
     * Despacha pacotes em LOTE — `POST /fulfillment/{v}/packages/ship`.
     *
     * Nao existe lista de sucesso: o que nao aparecer em `->errors` foi
     * despachado. E o envelope volta `code: 0` mesmo com falhas dentro.
     *
     * @param  array<int, array{id: string, handover_method?: string, pickup_slot?: array, self_shipment?: array}>  $packages
     */
    public function batchShipPackages(array $packages, string $version = '202309'): BatchShipPackagesResponseDTO
    {
        if ($packages === []) {
            throw new InvalidArgumentException('Informe ao menos um pacote para despachar.');
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fulfillment/{$version}/packages/ship",
            [],
            ['packages' => $packages],
        );

        return BatchShipPackagesResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Troca rastreio/transportadora de um pacote ja' despachado —
     * `POST /fulfillment/{v}/packages/{id}/shipping_info/update`.
     *
     * Janela curta e' a regra: a doc lista 21011050 ("so' dentro de N horas")
     * e 21011051 ("so' pode ser alterado uma vez").
     */
    public function updatePackageShippingInfo(
        string $packageId,
        string $trackingNumber,
        string $shippingProviderId,
        string $version = '202309',
    ): bool {
        $this->makeRequest(
            HttpMethod::POST,
            "/fulfillment/{$version}/packages/".rawurlencode($packageId).'/shipping_info/update',
            [],
            [
                'tracking_number' => $trackingNumber,
                'shipping_provider_id' => $shippingProviderId,
            ],
        );

        return true;
    }

    /**
     * Informa entrega/insucesso de pacote em frota propria (SOF) ou retirada
     * na loja — `POST /fulfillment/{v}/packages/deliver`.
     *
     * `file_url` do comprovante nao aceita o arquivo direto: sobe antes via
     * uploadDeliveryFile/uploadDeliveryImage e usa a URL devolvida.
     * `UPDATE_POD` serve pra anexar comprovante a pacote JA entregue.
     *
     * @param  array<int, array{id: string, delivery_type: string, fail_delivery_reason?: string, file_type?: string, file_url?: string, verification_code?: string}>  $packages
     */
    public function updatePackageDeliveryStatus(
        array $packages,
        string $version = '202309',
    ): UpdatePackageDeliveryStatusResponseDTO {
        if ($packages === []) {
            throw new InvalidArgumentException('Informe ao menos um pacote.');
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fulfillment/{$version}/packages/deliver",
            [],
            ['packages' => $packages],
        );

        return UpdatePackageDeliveryStatusResponseDTO::fromArray($response['data'] ?? []);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Comprovantes de entrega (multipart)
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Sobe um PDF de comprovacao de entrega —
     * `/fulfillment/{v}/files/upload`. Max 10MB, so' PDF.
     *
     * A doc declara o metodo como GET, o que e' erro dela: o corpo e'
     * multipart/form-data, entao vai POST. E multipart nao entra na
     * assinatura — por isso o `buildSignedQuery` e' chamado com body null.
     */
    public function uploadDeliveryFile(
        string $contents,
        string $fileName,
        string $version = '202309',
    ): UploadDeliveryFileResponseDTO {
        $data = $this->multipartUpload(
            "/fulfillment/{$version}/files/upload",
            $contents,
            $fileName,
            ['name' => $fileName],
        );

        return UploadDeliveryFileResponseDTO::fromArray($data);
    }

    /**
     * Sobe uma imagem de comprovacao de entrega —
     * `/fulfillment/{v}/images/upload`. JPG/JPEG/PNG, max 5MB, entre
     * 100x100 e 20000x20000 px.
     *
     * Mesma correcao de metodo do uploadDeliveryFile (a doc diz GET; e' POST
     * multipart).
     */
    public function uploadDeliveryImage(
        string $contents,
        string $fileName = 'image.jpg',
        string $version = '202309',
    ): UploadDeliveryImageResponseDTO {
        $data = $this->multipartUpload(
            "/fulfillment/{$version}/images/upload",
            $contents,
            $fileName,
        );

        return UploadDeliveryImageResponseDTO::fromArray($data);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Alfandega (cross-border)
    // ─────────────────────────────────────────────────────────────────────

    /**
     * O que a alfandega exige por pedido —
     * `POST /fulfillment/202607/orders/customs_info/query`.
     *
     * Consulte ANTES de submeter: `needFillClearance=false` significa que o
     * submit e' desnecessario, e `alreadyFilledClearance=true` que ja' foi.
     *
     * @param  list<string>  $orderIds
     */
    public function getOrderCustomsRequirements(
        array $orderIds,
        string $version = '202607',
    ): OrderCustomsRequirementsResponseDTO {
        if ($orderIds === []) {
            throw new InvalidArgumentException('Informe ao menos um pedido.');
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fulfillment/{$version}/orders/customs_info/query",
            [],
            ['order_ids' => array_values($orderIds)],
        );

        return OrderCustomsRequirementsResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Declara a composicao alfandegaria dos itens —
     * `POST /fulfillment/202607/orders/customs_info/submit`.
     *
     * `price_percent` e' INT em pontos percentuais (10 = 10%), e a soma dos
     * itens de uma linha e' o rateio do valor dela. Resposta e' `data: {}`.
     *
     * @param  array<int, array{order_id: string, item_list?: array<int, array{order_line_id?: string, sku_id?: string, item_detail?: array<int, array{sku_id: string, qty: int, price_percent: int}>}>}>  $orderList
     */
    public function uploadCustomsInformation(array $orderList, string $version = '202607'): bool
    {
        if ($orderList === []) {
            throw new InvalidArgumentException('Informe ao menos um pedido.');
        }

        $this->makeRequest(
            HttpMethod::POST,
            "/fulfillment/{$version}/orders/customs_info/submit",
            [],
            ['order_list' => $orderList],
        );

        return true;
    }

    // ─────────────────────────────────────────────────────────────────────
    // First-mile bundle (cross-border)
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Agrupa pedidos num envio de primeira milha —
     * `POST /fulfillment/202407/bundles`.
     *
     * Restricoes da doc: todos os pedidos ja' RTS com etiqueta impressa, do
     * MESMO vendedor e do MESMO grupo de mercados (grupo 1: PH/SG/MY/VN/TH/
     * JP; grupo 2: DE/FR/IT/ES). US e UK NAO usam este endpoint.
     *
     * Em `DROP_OFF` os tres opcionais viram obrigatorios (transportadora,
     * rastreio e os 4 ultimos digitos do telefone do remetente).
     *
     * @param  list<string>  $orderIds
     * @param  string  $handoverMethod  PICKUP | DROP_OFF
     */
    public function createFirstMileBundle(
        array $orderIds,
        string $handoverMethod,
        ?string $shippingProviderId = null,
        ?string $trackingNumber = null,
        ?string $phoneTailNumber = null,
        string $version = '202407',
    ): FirstMileBundleResponseDTO {
        return $this->postFirstMileBundle(
            "/fulfillment/{$version}/bundles",
            $orderIds,
            $handoverMethod,
            $shippingProviderId,
            $trackingNumber,
            $phoneTailNumber,
        );
    }

    /**
     * Mesma operacao, endpoint novo — `POST /fulfillment/202510/first_mile_bundle`.
     *
     * Corpo e resposta sao IDENTICOS ao 202407; a v2 muda so' o caminho (e
     * passa a exigir `shop_cipher`, que o BaseMethods ja' assina). Existe pra
     * quem precisa fixar a versao nova; a logica e' a mesma.
     *
     * @param  list<string>  $orderIds
     * @param  string  $handoverMethod  PICKUP | DROP_OFF
     */
    public function createFirstMileBundleV2(
        array $orderIds,
        string $handoverMethod,
        ?string $shippingProviderId = null,
        ?string $trackingNumber = null,
        ?string $phoneTailNumber = null,
        string $version = '202510',
    ): FirstMileBundleResponseDTO {
        return $this->postFirstMileBundle(
            "/fulfillment/{$version}/first_mile_bundle",
            $orderIds,
            $handoverMethod,
            $shippingProviderId,
            $trackingNumber,
            $phoneTailNumber,
        );
    }

    // ─────────────────────────────────────────────────────────────────────
    // POD, resgate e rastreio
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Personalizacao POD (print on demand) das linhas de um pedido —
     * `POST /fulfillment/202606/pod_details/get`.
     *
     * NAO CONFUNDIR com `OrderMethods::getPodDetails()`: aquele e'
     * `/order/202606/pod_details/search` e devolve o campo `pod_detail`;
     * este e' o caminho de fulfillment e devolve `pod_info_json`. Endpoints
     * distintos, chaves distintas.
     *
     * A consulta e' por LINHA (order_line_id + product_id + sku_id +
     * pod_order_data_id) — nao existe "POD do pedido inteiro".
     *
     * @param  array<int, array{order_line_id: string, product_id: string, sku_id: string, pod_order_data_id: string}>  $queryOrderLines
     */
    public function getPodDetail(
        string $mainOrderId,
        array $queryOrderLines,
        string $version = '202606',
    ): PodDetailResponseDTO {
        if ($queryOrderLines === []) {
            throw new InvalidArgumentException('Informe ao menos uma linha para consultar o POD.');
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fulfillment/{$version}/pod_details/get",
            [],
            [
                'main_order_id' => $mainOrderId,
                'query_order_lines' => $queryOrderLines,
            ],
        );

        return PodDetailResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Devolve ao TikTok o codigo/link de resgate de produto digital —
     * `POST /fulfillment/202601/redeem_info/callback`.
     *
     * O `redeem_data` e' o que chega ao comprador no e-mail de entrega, e o
     * formato depende do `redeem_type`: `CODE` texto puro, `URL` link,
     * `CODE_PIN` um JSON serializado em string.
     *
     * Falha e' POR LINHA: confira `statusCode` de cada `orderStatuses[]`.
     *
     * @param  array<int, array{order_line_id: string, redeem_info: array{redeem_type: string, redeem_data?: string, redeem_instruction_info?: string}, source_unique_id?: string}>  $orderInfoList
     */
    public function redeemInfoCallback(
        string $orderId,
        array $orderInfoList,
        string $version = '202601',
    ): RedeemInfoCallbackResponseDTO {
        if ($orderInfoList === []) {
            throw new InvalidArgumentException('Informe ao menos uma linha de pedido.');
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fulfillment/{$version}/redeem_info/callback",
            [],
            [
                'order_id' => $orderId,
                'order_info_list' => $orderInfoList,
            ],
        );

        return RedeemInfoCallbackResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Diz se um rastreio e' de frete/coleta da plataforma —
     * `GET /fulfillment/202508/tts_tracking_validation`.
     *
     * Serve pra nao tentar operar como envio proprio um pacote que na verdade
     * e' TikTok Shipping. As duas flags sao independentes: da' pra ter coleta
     * da plataforma com etiqueta do vendedor.
     */
    public function validateTtsTracking(
        string $trackingNumber,
        string $version = '202508',
    ): TrackingValidationResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/fulfillment/{$version}/tts_tracking_validation",
            ['tracking_number' => $trackingNumber],
        );

        return TrackingValidationResponseDTO::fromArray($response['data'] ?? []);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Internos
    // ─────────────────────────────────────────────────────────────────────

    /** @param list<string> $orderIds */
    private function postFirstMileBundle(
        string $path,
        array $orderIds,
        string $handoverMethod,
        ?string $shippingProviderId,
        ?string $trackingNumber,
        ?string $phoneTailNumber,
    ): FirstMileBundleResponseDTO {
        if ($orderIds === []) {
            throw new InvalidArgumentException('Informe ao menos um pedido para o bundle.');
        }

        if ($handoverMethod === 'DROP_OFF' && ($shippingProviderId === null || $trackingNumber === null)) {
            throw new InvalidArgumentException(
                'DROP_OFF exige shipping_provider_id e tracking_number (regra do TikTok).',
            );
        }

        $body = array_filter([
            'order_ids' => array_values($orderIds),
            'handover_method' => $handoverMethod,
            'shipping_provider_id' => $shippingProviderId,
            'tracking_number' => $trackingNumber,
            'phone_tail_number' => $phoneTailNumber,
        ], static fn ($v) => $v !== null);

        $response = $this->makeRequest(HttpMethod::POST, $path, [], $body);

        return FirstMileBundleResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * POST multipart assinado.
     *
     * O BaseMethods so' fala JSON, e a assinatura do TikTok NAO inclui corpo
     * multipart — por isso a query e' assinada com body null e a requisicao
     * sai direto pelo httpClient com `attach()`.
     *
     * @param  array<string, string>  $extraFields
     * @return array<string, mixed>
     */
    private function multipartUpload(
        string $apiPath,
        string $contents,
        string $fileName,
        array $extraFields = [],
    ): array {
        $apiPath = $this->normalizeApiPath($apiPath);
        $query = $this->buildSignedQuery($apiPath, [], null);

        // O cliente do HttpClientFactory chama asJson(), que GRAVA
        // `Content-Type: application/json` no header. O attach() troca so' o
        // encoding do corpo — o header errado ficaria e o TikTok recusaria o
        // multipart. Por isso o cliente daqui e' montado do zero; o make()
        // continua sendo chamado antes so' pelo refresh de token.
        HttpClientFactory::make($this->integration);

        $client = Http::baseUrl(config('marketplaces.tiktok.base_url', 'https://open-api.tiktokglobalshop.com'))
            ->timeout(60)
            ->connectTimeout(10)
            ->acceptJson()
            ->withHeaders(['x-tts-access-token' => $this->integration->getAccessToken() ?? ''])
            ->attach('data', $contents, $fileName);

        $response = $client->post($apiPath.'?'.http_build_query($query), $extraFields);

        $payload = $response->json() ?? [];
        if ($response->failed() || ($payload['code'] ?? 0) !== 0) {
            $this->handleError($response);
        }

        return $payload['data'] ?? [];
    }

    /**
     * Cota os fretes elegiveis para o pedido
     * (`EligibleShippingServiceResponseDTO`).
     *
     * A doc declara o path como consulta, mas o verbo e' POST (`/query`) — os
     * filtros vao no CORPO, nao na query.
     *
     * Peso e dimensao sao OPCIONAIS: sem eles a API cota pelo cadastro do
     * produto. Enviar peso diferente muda o preco devolvido, entao a cotacao
     * so' vale para o pacote que voce descreveu aqui.
     *
     * `$orderLineList` e' a forma nova e convive com `$orderLineItemIds`: e' a
     * unica que enderca componente de kit (`sub_item_id`). Para pedido com
     * varios pacotes possiveis, omitir as linhas devolve o erro 21011048 —
     * a API exige que se diga qual linha sera' enviada.
     *
     * @param  list<string>  $orderLineItemIds
     * @param  array{value?: string, unit?: string}|null  $weight  valor em STRING (GRAM|POUND)
     * @param  array{length?: string, width?: string, height?: string, unit?: string}|null  $dimension
     * @param  list<array{order_line_id?: string, sub_item_id?: string}>  $orderLineList
     */
    public function getEligibleShippingServices(
        string $orderId,
        array $orderLineItemIds = [],
        ?array $weight = null,
        ?array $dimension = null,
        array $orderLineList = [],
    ): EligibleShippingServiceResponseDTO {
        $body = array_filter([
            'order_line_item_ids' => $orderLineItemIds !== [] ? array_values($orderLineItemIds) : null,
            'weight' => $weight,
            'dimension' => $dimension,
            'order_line_list' => $orderLineList !== [] ? array_values($orderLineList) : null,
        ], static fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/fulfillment/202309/orders/{$orderId}/shipping_services/query",
            [],
            $body,
        );

        return EligibleShippingServiceResponseDTO::fromArray($response['data'] ?? []);
    }
}
