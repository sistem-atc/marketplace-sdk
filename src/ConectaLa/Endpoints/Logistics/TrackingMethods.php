<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\ConectaLa\Endpoints\Logistics;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\ConectaLa\Bases\BaseMethods;

/**
 * Rastreio e frete (Freight & Tracking). A COTAÇÃO de frete é um callback INBOUND
 * (a Conecta Lá chama a URL do seller `POST .../cotacao`) — não é método daqui;
 * é um endpoint que o Bunker EXPÕE. Os métodos abaixo são os OUTBOUND (nós → API).
 */
class TrackingMethods extends BaseMethods
{
    /** Rastreio de um pedido (GET /Tracking/{order}). */
    public function get(string $orderId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/Tracking/{$orderId}");
    }

    /** Envia o rastreio do pedido (POST /Tracking/{order}). */
    public function send(string $orderId, array $body): array
    {
        return $this->makeRequest(HttpMethod::POST, "/Tracking/{$orderId}", body: $body);
    }

    /** Envia uma ocorrência de rastreio (POST /Tracking/occurrence/{order}/{trackingCode}). */
    public function sendOccurrence(string $orderId, string $trackingCode, array $body): array
    {
        return $this->makeRequest(HttpMethod::POST, "/Tracking/occurrence/{$orderId}/{$trackingCode}", body: $body);
    }
}
