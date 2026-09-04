<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Logistics;

use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Magalu\Exceptions\MagaluRequestException;

class LogisticsMethods extends BaseMethods
{
    public function generateShippingLabels(array $deliveryIds): string
    {
        $body = ['deliveries' => array_map(fn (string $id) => ['id' => $id], array_values($deliveryIds))];
        $response = $this->httpClient->post('/seller/v1/logistics/shipping-labels', $body);
        if (!$response->successful()) throw new MagaluRequestException($response);
        return $response->body();
    }


    /**
     * Etiquetas de envio conforme a doc (POST /seller/v1/logistics/shipping-labels).
     *
     * Diferente de `generateShippingLabels()` (body legado sem canal), manda
     * `channel.id`, `deliveries[] {id}` e `label {format, type}` e devolve o
     * JSON com `label.signed_url` / `label.expires_on`.
     *
     * @param list<string> $deliveryIds
     * @param array<string, string> $channelExtras
     * @return array<string, mixed>
     */
    public function requestShippingLabels(
        array $deliveryIds,
        string $channelId,
        string $format = 'pdf',
        string $type = 'shipping',
        array $channelExtras = [],
    ): array {
        $body = [
            'channel' => ['id' => $channelId],
            'deliveries' => array_map(static fn (string $id): array => ['id' => $id], array_values($deliveryIds)),
            'label' => ['format' => $format, 'type' => $type],
        ];
        if ($channelExtras) $body['channel']['extras'] = $channelExtras;

        return $this->makeRequest(HttpMethod::POST, '/seller/v1/logistics/shipping-labels', [], $body);
    }

    /**
     * Armazens/filiais logisticas do seller (GET /seller/v1/logistics/warehouses).
     * Paginacao `_limit` (default 50) / `_offset`.
     *
     * @return array<string, mixed>
     */
    public function listWarehouses(int $limit = 50, int $offset = 0): array
    {
        return $this->makeRequest(HttpMethod::GET, '/seller/v1/logistics/warehouses', [
            '_limit' => $limit,
            '_offset' => $offset,
        ]);
    }
}
