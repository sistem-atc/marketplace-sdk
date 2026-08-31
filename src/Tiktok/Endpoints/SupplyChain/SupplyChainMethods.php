<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\SupplyChain;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\SupplyChain\ConfirmPackageShipmentResponseDTO;

/**
 * TikTok Shop Supply Chain API — o canal do WMS/operador logistico (3PL) pro
 * TikTok, nao o do vendedor.
 *
 * Quem chama aqui e' o PROVEDOR DE ARMAZEM (identificado por
 * `warehouse_provider_id`, emitido pelo TikTok), informando que o pacote saiu.
 * O caminho do vendedor pra despachar continua sendo o Fulfillment
 * (`/fulfillment/.../packages/ship`) — nao trocar um pelo outro.
 */
class SupplyChainMethods extends BaseMethods
{
    private const DEFAULT_VERSION = '202309';

    /**
     * Confirma, em LOTE, o embarque de pacotes despachados pelo armazem.
     *
     * Tres armadilhas:
     * 1. Os tempos daqui sao epoch em MILISSEGUNDOS (`*_time_millis`) —
     *    o resto da API TikTok usa segundos. Mandar segundos aqui "funciona"
     *    e registra o embarque em 1970.
     * 2. `time_zone` e' do ARMAZEM e nao e' redundante com o epoch: e' o que
     *    o TikTok usa pra apurar o SLA de expedicao no fuso certo.
     * 3. O retorno e' PARCIAL: `code: 0` no envelope nao significa que todos
     *    os pacotes entraram. Confira `success_packages`.
     *
     * @param  list<array<string, mixed>>  $packages  pacotes na shape da doc (id, wms_order_id, *_time_millis, dimension, weight, skus[]…)
     */
    public function confirmPackageShipment(
        string $warehouseProviderId,
        array $packages,
        string $version = self::DEFAULT_VERSION,
    ): ConfirmPackageShipmentResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/supply_chain/{$version}/packages/sync",
            [],
            ['warehouse_provider_id' => $warehouseProviderId, 'packages' => $packages],
        );

        return ConfirmPackageShipmentResponseDTO::fromArray($response['data'] ?? []);
    }
}
