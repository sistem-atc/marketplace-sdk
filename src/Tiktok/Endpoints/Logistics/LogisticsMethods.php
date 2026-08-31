<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Logistics;

use Illuminate\Http\Client\Response;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Logistics\AvailableShippingTemplate;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Logistics\DeliveryOption;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Logistics\GlobalWarehouse;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Logistics\ShippingProvider;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Logistics\Warehouse;

/**
 * Logistics API do TikTok Shop (/logistics/{version}/...).
 *
 * A cadeia de dependência é: warehouse -> delivery option -> shipping provider.
 * Nenhum dos três aceita filtro, então descobrir a transportadora de um envio
 * exige percorrer a cadeia (o `deliveryOptionId` do pedido é o atalho pro
 * último passo).
 */
class LogisticsMethods extends BaseMethods
{
    private const DEFAULT_VERSION = '202309';

    /**
     * Armazéns da loja (`data.warehouses[]`).
     *
     * Traz galpões de VENDA e de DEVOLUÇÃO misturados — filtre por `type`
     * (SALES_WAREHOUSE / RETURN_WAREHOUSE). O mesmo endereço físico costuma
     * aparecer 2×, com ids diferentes e o mesmo `entityId`.
     *
     * @return list<Warehouse>
     */
    public function getWarehouseList(string $version = self::DEFAULT_VERSION): array
    {
        $response = $this->makeRequest(HttpMethod::GET, "/logistics/{$version}/warehouses");

        return array_map(
            static fn (array $w): Warehouse => Warehouse::fromArray($w),
            $response['data']['warehouses'] ?? [],
        );
    }

    /**
     * Opções de entrega de um armazém (`data.delivery_options[]`).
     *
     * @param  string  $scope  WAREHOUSE (padrão, com limites de peso/dimensão)
     *                         ou PRODUCT — este último devolve SÓ id e name.
     * @return list<DeliveryOption>
     */
    public function getWarehouseDeliveryOptions(
        string $warehouseId,
        ?string $scope = null,
        string $version = self::DEFAULT_VERSION,
    ): array {
        $query = $scope !== null ? ['scope' => $scope] : [];

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/logistics/{$version}/warehouses/{$warehouseId}/delivery_options",
            $query,
        );

        return array_map(
            static fn (array $o): DeliveryOption => DeliveryOption::fromArray($o),
            $response['data']['delivery_options'] ?? [],
        );
    }

    /**
     * Transportadoras de uma delivery option (`data.shipping_providers[]`).
     *
     * Lista vazia não é bug: quando a entrega é feita pelo próprio TikTok a API
     * responde 21008117 e não há transportadora a escolher.
     *
     * @return list<ShippingProvider>
     */
    public function getShippingProviders(
        string $deliveryOptionId,
        ?string $warehouseRegion = null,
        ?string $buyerRegion = null,
        string $version = self::DEFAULT_VERSION,
    ): array {
        $query = [];
        if ($warehouseRegion !== null) {
            $query['warehouse_region'] = $warehouseRegion;
        }
        if ($buyerRegion !== null) {
            $query['buyer_region'] = $buyerRegion;
        }

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/logistics/{$version}/delivery_options/{$deliveryOptionId}/shipping_providers",
            $query,
        );

        return array_map(
            static fn (array $p): ShippingProvider => ShippingProvider::fromArray($p),
            $response['data']['shipping_providers'] ?? [],
        );
    }

    /**
     * Armazéns GLOBAIS do seller cross-border (`data.global_warehouses[]`).
     *
     * Único endpoint de logística que NÃO é por loja: o escopo é o seller
     * global, então o shop_cipher é irrelevante aqui.
     *
     * @return list<GlobalWarehouse>
     */
    public function getGlobalSellerWarehouses(string $version = self::DEFAULT_VERSION): array
    {
        $response = $this->makeRequest(HttpMethod::GET, "/logistics/{$version}/global_warehouses");

        return array_map(
            static fn (array $w): GlobalWarehouse => GlobalWarehouse::fromArray($w),
            $response['data']['global_warehouses'] ?? [],
        );
    }

    /**
     * Templates de frete do seller com a disponibilidade avaliada PARA UM
     * PRODUTO (`data.templates[]`, versão 202510).
     *
     * O peso/dimensão vai no BODY de um GET — sim, GET com corpo; é assim que
     * o TikTok especifica. Sem `productAttribute` a API avalia sem restrição
     * física.
     *
     * O retorno inclui templates INDISPONÍVEIS: cheque `templateIsAvailable`
     * antes de usar, e o porquê está em `serviceUnreachableReason`.
     *
     * @param  array<string, mixed>|null  $productAttribute  {weight:{weight,unit}, dimension:{length,width,height,unit}}
     * @return list<AvailableShippingTemplate>
     */
    public function getAvailableShippingTemplates(
        ?array $productAttribute = null,
        string $version = '202510',
    ): array {
        $body = $productAttribute !== null ? ['product_attribute' => $productAttribute] : null;

        $response = $this->makeRequest(HttpMethod::GET, "/logistics/{$version}/seller_templates", [], $body);

        return array_map(
            static fn (array $t): AvailableShippingTemplate => AvailableShippingTemplate::fromArray($t),
            $response['data']['templates'] ?? [],
        );
    }

    /**
     * Diz se o armazém já tem template de frete configurado (versão 202606).
     *
     * `bizWarehouseId` é declarado INT na doc (não é o snowflake string dos
     * outros endpoints de armazém) e vai na QUERY. Retorna false também quando
     * o armazém existe mas nunca teve template — é pré-checagem de cadastro,
     * não validação de id.
     */
    public function shippingTemplateExists(
        int $bizWarehouseId,
        ?int $shopId = null,
        string $version = '202606',
    ): bool {
        $query = ['biz_warehouse_id' => $bizWarehouseId];
        if ($shopId !== null) {
            $query['shop_id'] = $shopId;
        }

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/logistics/{$version}/seller_template/template_exist",
            $query,
        );

        return (bool) ($response['data']['template_exist'] ?? false);
    }

    /**
     * Informações de envio de um pedido.
     *
     * Path legado (`/api/logistics/v202309/...`), mantido como estava — os
     * endpoints novos deste arquivo usam o esquema atual `/logistics/{v}/...`.
     */
    public function getShippingInfo(string $orderId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/api/logistics/v202309/orders/{$orderId}/shipping_info");
    }

    /** Despacha o pedido (ship). Path legado, idem acima. */
    public function shipOrder(string $orderId, array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, "/api/logistics/v202309/orders/{$orderId}/ship", [], $data);
    }

    /**
     * O Get Available Shipping Template é GET COM CORPO (peso/dimensão do
     * produto vão no body). O executeRequest padrão descarta o body num GET —
     * aqui ele precisa ir junto, senão a assinatura (que já inclui o body) não
     * bate com o que foi enviado.
     */
    protected function executeRequest(
        HttpMethod $method,
        string $apiPath,
        array $query,
        ?array $body,
    ): Response {
        if ($method === HttpMethod::GET && $body !== null) {
            return $this->httpClient->send('GET', $apiPath.'?'.http_build_query($query), ['json' => $body]);
        }

        return parent::executeRequest($method, $apiPath, $query, $body);
    }
}
