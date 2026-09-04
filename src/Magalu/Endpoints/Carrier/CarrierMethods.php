<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Carrier;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;

/**
 * APIs de logistica na base `services.magalu.com`:
 *
 *  - Transportadora (/logistic/carrier/v1) — visao da TRANSPORTADORA parceira
 *    (escopos open:logistic-carrier-shippings:read / -trackings:create). So'
 *    funciona com credencial de transportadora; um seller comum recebe 403.
 *  - Tracking do seller (/logistics/v1/shippings/{id}/tracking) — escopo
 *    open:logistic-seller-trackings:read.
 *  - Smart Label (/smart-label/v1/labels/generate) — etiqueta ZPL/PDF do Magalog.
 */
class CarrierMethods extends BaseMethods
{
    /**
     * Cadastro da transportadora do tenant (GET /logistic/carrier/v1/account).
     *
     * @return array<string, mixed>
     */
    public function account(): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->servicesUrl('/logistic/carrier/v1/account'));
    }

    /**
     * Token de integracao da transportadora (GET /logistic/carrier/v1/token).
     *
     * @return array<string, mixed>
     */
    public function token(): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->servicesUrl('/logistic/carrier/v1/token'));
    }

    /**
     * Remessa (pedido logistico) por id (GET /logistic/carrier/v1/shippings/{id}).
     *
     * @return array<string, mixed>
     */
    public function getShipping(string $shippingId): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->servicesUrl("/logistic/carrier/v1/shippings/{$shippingId}"));
    }

    /**
     * Registra evento de tracking na remessa (POST .../shippings/{id}/trackings) — 201.
     *
     * `event`: DELIVERED | DELIVERY_ATTEMPT | IN_DELIVERED_ROUTE | PICKED_UP |
     * WAITING_PICKUP | SHIPPING_ACCEPTED | SHIPPING_REJECTED ... ;
     * `recipientDetails`: {type: HIMSELF|NEIGHBOR|RELATIVE|CONCIERGE|EMPLOYEE,
     * name, documents[], location {latitude, longitude}, note, proofs[]}.
     *
     * @param array<string, mixed> $recipientDetails
     * @return array<string, mixed>
     */
    public function createTracking(string $shippingId, string $event, string $date, array $recipientDetails): array
    {
        return $this->makeRequest(HttpMethod::POST, $this->servicesUrl("/logistic/carrier/v1/shippings/{$shippingId}/trackings"), [], [
            'event' => $event,
            'date' => $date,
            'recipient_details' => $recipientDetails,
        ]);
    }

    /**
     * Posicao geografica do veiculo na remessa
     * (POST .../shippings/{id}/trackings/locations).
     *
     * @return array<string, mixed>
     */
    public function createTrackingLocation(string $shippingId, float $latitude, float $longitude, string $date): array
    {
        return $this->makeRequest(HttpMethod::POST, $this->servicesUrl("/logistic/carrier/v1/shippings/{$shippingId}/trackings/locations"), [], [
            'coordinates' => ['latitude' => $latitude, 'longitude' => $longitude],
            'date' => $date,
        ]);
    }

    /**
     * Tracking da remessa na visao do SELLER (GET /logistics/v1/shippings/{shipping_id}/tracking).
     *
     * @return array<string, mixed>
     */
    public function tracking(string $shippingId): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->servicesUrl("/logistics/v1/shippings/{$shippingId}/tracking"));
    }

    /**
     * Gera Smart Label (POST /smart-label/v1/labels/generate).
     *
     * Body: `shipper {cnpj, tag}`, `transport {service_id, deadline_date}`,
     * `package {tag {code, volume}}`, `order {id}`, `origin {address}`,
     * `destination {name, document[], address}`, `invoice {access_key, number,
     * serie, ...}`. Resposta traz `format` (ZPL|PDF) e `content` (base64).
     * `correlationId` vira header `x-correlation-id`.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function generateSmartLabel(array $data, ?string $correlationId = null): array
    {
        $headers = $correlationId !== null ? ['x-correlation-id' => $correlationId] : [];

        return $this->makeRequest(
            HttpMethod::POST,
            $this->servicesUrl('/smart-label/v1/labels/generate'),
            [],
            $data,
            headers: $headers,
        );
    }
}
