<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Netshoes\Endpoints\Protocol;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Netshoes\Bases\BaseMethods;

/**
 * Protocolos de atendimento (SAC) Netshoes — Swagger V1, tag Protocols.
 *
 *   GET  /api/v1/protocols
 *   GET  /api/v1/protocols/{protocolNumber}
 *   GET  /api/v1/protocols/{protocolNumber}/messages
 *   POST /api/v1/protocols/{protocolNumber}/messages
 *   GET  /api/v1/orders/{orderNumber}/protocols
 *   GET  /api/v1/orders/{orderNumber}/protocols/{protocolNumber}
 *   GET  /api/v1/orders/{orderNumber}/protocols/{protocolNumber}/messages
 *
 * Todos aceitam `expand` (csv). ProtocolResource = {protocolNumber, orderNumber,
 * status, initDate, type, cause, lastMessageDate, channel, sla, priority,
 * responsible, messages[]}. Respostas devolvidas cruas.
 */
class ProtocolMethods extends BaseMethods
{
    /**
     * @param  array<int, string>  $expand
     * @return array<string, mixed>  ListResponseProtocolResource {items[], links[]}
     */
    public function listProtocols(array $expand = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v1/protocols', $this->expandQuery($expand));
    }

    /**
     * @param  array<int, string>  $expand
     * @return array<string, mixed>  ProtocolResource
     */
    public function getProtocol(string $protocolNumber, array $expand = []): array
    {
        return $this->makeRequest(
            method: HttpMethod::GET,
            path: '/api/v1/protocols/'.rawurlencode($protocolNumber),
            query: $this->expandQuery($expand),
        );
    }

    /**
     * @param  array<int, string>  $expand
     * @return array<string, mixed>  ListResponseMessageResource {items[], links[]}
     */
    public function listProtocolMessages(string $protocolNumber, array $expand = []): array
    {
        return $this->makeRequest(
            method: HttpMethod::GET,
            path: '/api/v1/protocols/'.rawurlencode($protocolNumber).'/messages',
            query: $this->expandQuery($expand),
        );
    }

    /**
     * Responde um protocolo (Swagger V1 postMessage).
     *
     * @param  array<string, mixed>  $message  PostMessageResource {text, responsible,
     *                                         messageDate, protocolNumber}
     * @param  array<int, string>  $expand
     * @return array<string, mixed>
     */
    public function postProtocolMessage(string $protocolNumber, array $message, array $expand = []): array
    {
        return $this->makeRequest(
            method: HttpMethod::POST,
            path: '/api/v1/protocols/'.rawurlencode($protocolNumber).'/messages',
            query: $this->expandQuery($expand),
            body: $message,
        );
    }

    /**
     * @param  array<int, string>  $expand
     * @return array<string, mixed>  ListResponseProtocolResource
     */
    public function listOrderProtocols(string $orderNumber, array $expand = []): array
    {
        return $this->makeRequest(
            method: HttpMethod::GET,
            path: '/api/v1/orders/'.rawurlencode($orderNumber).'/protocols',
            query: $this->expandQuery($expand),
        );
    }

    /**
     * @param  array<int, string>  $expand
     * @return array<string, mixed>  ProtocolResource
     */
    public function getOrderProtocol(string $orderNumber, string $protocolNumber, array $expand = []): array
    {
        return $this->makeRequest(
            method: HttpMethod::GET,
            path: $this->orderProtocolPath($orderNumber, $protocolNumber),
            query: $this->expandQuery($expand),
        );
    }

    /**
     * @param  array<int, string>  $expand
     * @return array<string, mixed>  ListResponseMessageResource
     */
    public function listOrderProtocolMessages(string $orderNumber, string $protocolNumber, array $expand = []): array
    {
        return $this->makeRequest(
            method: HttpMethod::GET,
            path: $this->orderProtocolPath($orderNumber, $protocolNumber).'/messages',
            query: $this->expandQuery($expand),
        );
    }

    private function orderProtocolPath(string $orderNumber, string $protocolNumber): string
    {
        return '/api/v1/orders/'.rawurlencode($orderNumber).'/protocols/'.rawurlencode($protocolNumber);
    }

    /**
     * @param  array<int, string>  $expand
     * @return array<string, string>
     */
    private function expandQuery(array $expand): array
    {
        return $expand ? ['expand' => implode(',', $expand)] : [];
    }
}
