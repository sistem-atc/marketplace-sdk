<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Flex;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;

/**
 * Envios Flex / Turbo (prefixo /flex/sites/{site}/...).
 *
 * Turbo NAO e um logistic_type proprio: e Flex (self_service) com a tag `turbo`
 * no shipment. As configuracoes de servico (zonas, raio, faixas, feriados)
 * usam o `service_id` que vem em subscriptions().
 */
class FlexMethods extends BaseMethods
{
    /**
     * Assinaturas Flex/Turbo do vendedor (GET /flex/sites/{site}/users/{user}/subscriptions/v1):
     * lista de {site_id, user_id, service_id, mode: FLEX|TURBO, origin{...}}.
     * O service_id daqui alimenta as chamadas de configuracao.
     *
     * @return array<int, array<string,mixed>>
     */
    public function subscriptions(string $siteId, int|string $userId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/flex/sites/'.rawurlencode($siteId)."/users/{$userId}/subscriptions/v1",
        );
    }

    /**
     * Diz se o item esta sendo oferecido com Flex (GET /flex/sites/{site}/items/{item}/v2):
     * {has_flex: bool}. 404 se o item nao existe.
     *
     * @return array<string,mixed>
     */
    public function itemStatus(string $siteId, string $itemId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/flex/sites/'.rawurlencode($siteId).'/items/'.rawurlencode($itemId).'/v2',
        );
    }

    /**
     * Ativa Flex no item (POST /flex/sites/{site}/items/{item}/v2). Resposta 204
     * sem corpo (devolve []). 400 "item is already in flex" se ja ativo; 403 se a
     * categoria/usuario nao permite self_service. Turbo ativa sozinho depois,
     * se dimensoes/peso couberem.
     *
     * @return array<string,mixed>
     */
    public function enableItem(string $siteId, string $itemId): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            '/flex/sites/'.rawurlencode($siteId).'/items/'.rawurlencode($itemId).'/v2',
        );
    }

    /**
     * Desativa Flex no item (DELETE /flex/sites/{site}/items/{item}/v2). 204 sem
     * corpo; 403 "item down" se o item nao oferecia Flex.
     *
     * @return array<string,mixed>
     */
    public function disableItem(string $siteId, string $itemId): array
    {
        return $this->makeRequest(
            HttpMethod::DELETE,
            '/flex/sites/'.rawurlencode($siteId).'/items/'.rawurlencode($itemId).'/v2',
        );
    }

    /**
     * Transportista (driver) atribuido a um envio Flex
     * (GET /flex/sites/{site}/shipments/{id}/assignment/v2): {driver_id}.
     *
     * @return array<string,mixed>
     */
    public function assignment(string $siteId, int|string $shipmentId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/flex/sites/'.rawurlencode($siteId)."/shipments/{$shipmentId}/assignment/v2",
        );
    }

    /**
     * Mensageria informa que passou a gerenciar o envio
     * (POST /flex/sites/{site}/users/{user}/courier-shipment/v1, body {shipment_id}).
     * 204 sem corpo. Deve ser chamado NO INICIO do manuseio: envios finalizados
     * (delivered, cancelled, not_delivered, shipped+delivery_blocked/
     * waiting_for_confirmation) sao rejeitados.
     *
     * @return array<string,mixed>
     */
    public function registerCourierShipment(string $siteId, int|string $userId, int|string $shipmentId): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            '/flex/sites/'.rawurlencode($siteId)."/users/{$userId}/courier-shipment/v1",
            body: ['shipment_id' => $shipmentId],
        );
    }

    /**
     * Zonas de cobertura do servico Flex
     * (GET .../services/{service}/configurations/coverage/zones/v1): {zones[{id, cutoff?}]}.
     * Com show_availables=true vem tambem `availables.zones` (o que pode ser ativado).
     *
     * @return array<string,mixed>
     */
    public function coverageZones(string $siteId, int|string $userId, int|string $serviceId, bool $showAvailables = false): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            $this->servicePath($siteId, $userId, $serviceId, 'coverage/zones/v1'),
            $showAvailables ? ['show_availables' => 'true'] : [],
        );
    }

    /**
     * Atualiza zonas de cobertura (PUT .../coverage/zones/v1). Body {zones: [{id, cutoff?{week,saturday,sunday}}]}.
     * A lista SUBSTITUI a configuracao: zona fora da lista e removida. Zona sem
     * `cutoff` usa o horario de corte geral.
     *
     * @param  array<int, array<string,mixed>>  $zones
     * @return array<string,mixed>
     */
    public function updateCoverageZones(string $siteId, int|string $userId, int|string $serviceId, array $zones): array
    {
        return $this->makeRequest(
            HttpMethod::PUT,
            $this->servicePath($siteId, $userId, $serviceId, 'coverage/zones/v1'),
            body: ['zones' => $zones],
        );
    }

    /**
     * Raio de cobertura (Envios Turbo) — GET .../coverage/radius/v1: {radius} em
     * metros; com show_availables=true vem `availables.radius{min,max}`.
     *
     * @return array<string,mixed>
     */
    public function coverageRadius(string $siteId, int|string $userId, int|string $serviceId, bool $showAvailables = false): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            $this->servicePath($siteId, $userId, $serviceId, 'coverage/radius/v1'),
            $showAvailables ? ['show_availables' => 'true'] : [],
        );
    }

    /**
     * Atualiza o raio de cobertura Turbo (PUT .../coverage/radius/v1, body {radius}).
     * Valor em metros, dentro de availables.radius{min,max}.
     *
     * @return array<string,mixed>
     */
    public function updateCoverageRadius(string $siteId, int|string $userId, int|string $serviceId, int $radius): array
    {
        return $this->makeRequest(
            HttpMethod::PUT,
            $this->servicePath($siteId, $userId, $serviceId, 'coverage/radius/v1'),
            body: ['radius' => $radius],
        );
    }

    /**
     * Faixas de entrega (GET .../configurations/delivery-ranges/v1):
     * {delivery_window: same_day|next_day, delivery_ranges{week[], saturday[], sunday[]}}
     * cada faixa com {capacity, from, to, cutoff}. show_availables=true traz opcoes.
     *
     * @return array<string,mixed>
     */
    public function deliveryRanges(string $siteId, int|string $userId, int|string $serviceId, bool $showAvailables = false): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            $this->servicePath($siteId, $userId, $serviceId, 'delivery-ranges/v1'),
            $showAvailables ? ['show_availables' => 'true'] : [],
        );
    }

    /**
     * Atualiza faixas de entrega (PUT .../delivery-ranges/v1). Body completo
     * {delivery_window, delivery_ranges{week[], saturday[], sunday[]}} — substitui
     * a configuracao inteira.
     *
     * @param  array<string,mixed>  $deliveryRanges  {week: [...], saturday: [...], sunday: [...]}
     * @return array<string,mixed>
     */
    public function updateDeliveryRanges(string $siteId, int|string $userId, int|string $serviceId, string $deliveryWindow, array $deliveryRanges): array
    {
        return $this->makeRequest(
            HttpMethod::PUT,
            $this->servicePath($siteId, $userId, $serviceId, 'delivery-ranges/v1'),
            body: ['delivery_window' => $deliveryWindow, 'delivery_ranges' => $deliveryRanges],
        );
    }

    /**
     * Feriados configurados (GET .../configurations/holidays/v1):
     * {holidays[{date, description, selected}]}; selected=true = vendedor NAO
     * trabalha nesse dia.
     *
     * @return array<string,mixed>
     */
    public function holidays(string $siteId, int|string $userId, int|string $serviceId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            $this->servicePath($siteId, $userId, $serviceId, 'holidays/v1'),
        );
    }

    /**
     * Atualiza feriados (PUT .../holidays/v1, body {holidays: [{date, description, selected}]}).
     * 204 sem corpo. So aceita marcar como feriado um dia que ja e dia de
     * trabalho ativo do vendedor (sabado sem operacao = erro).
     *
     * @param  array<int, array<string,mixed>>  $holidays
     * @return array<string,mixed>
     */
    public function updateHolidays(string $siteId, int|string $userId, int|string $serviceId, array $holidays): array
    {
        return $this->makeRequest(
            HttpMethod::PUT,
            $this->servicePath($siteId, $userId, $serviceId, 'holidays/v1'),
            body: ['holidays' => $holidays],
        );
    }

    private function servicePath(string $siteId, int|string $userId, int|string $serviceId, string $suffix): string
    {
        return '/flex/sites/'.rawurlencode($siteId)."/users/{$userId}/services/{$serviceId}/configurations/{$suffix}";
    }
}
