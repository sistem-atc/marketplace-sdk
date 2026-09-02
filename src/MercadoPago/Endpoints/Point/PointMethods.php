<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\Point;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;

/**
 * Point — maquininhas (Point Smart/Pro) em modo integrado: o sistema cria
 * uma "intencao de pagamento" no device e o operador so' aproxima o
 * cartao. O device precisa estar em `operating_mode: PDV` pra aceitar
 * intencoes via API (em STANDALONE ele ignora).
 *
 * Doc: https://www.mercadopago.com.br/developers/pt/reference/integrations_api/_point_integration-api_devices_deviceid_payment-intents/post
 */
class PointMethods extends BaseMethods
{
    /**
     * @param  array<string, mixed>  $payload  amount (centavos), description, payment (installments, type...), additional_info (external_reference, print_on_terminal)
     * @return array<string, mixed>
     */
    public function createPaymentIntent(string $deviceId, array $payload): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            '/point/integration-api/devices/'.rawurlencode($deviceId).'/payment-intents',
            body: $payload,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function getPaymentIntent(string $paymentIntentId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/point/integration-api/payment-intents/'.rawurlencode($paymentIntentId));
    }

    /**
     * Cancela uma intencao ainda nao paga (OPEN/ON_TERMINAL).
     *
     * @return array<string, mixed>
     */
    public function cancelPaymentIntent(string $deviceId, string $paymentIntentId): array
    {
        return $this->makeRequest(
            HttpMethod::DELETE,
            '/point/integration-api/devices/'.rawurlencode($deviceId).'/payment-intents/'.rawurlencode($paymentIntentId),
        );
    }

    /**
     * Eventos de intencao no periodo (datas `Y-m-d`). Janela maxima de 30 dias.
     *
     * @return array<string, mixed>
     */
    public function listPaymentIntents(string $startDate, string $endDate): array
    {
        return $this->makeRequest(HttpMethod::GET, '/point/integration-api/payment-intents/events', [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Ultimo status da intencao (OPEN, ON_TERMINAL, PROCESSING, FINISHED, CANCELED, ERROR...).
     *
     * @return array<string, mixed>
     */
    public function getPaymentIntentStatus(string $paymentIntentId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/point/integration-api/payment-intents/'.rawurlencode($paymentIntentId).'/events');
    }

    /**
     * Devices da conta. Filtros: store_id, pos_id, limit, offset.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getDevices(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/point/integration-api/devices', $filters);
    }

    /**
     * @param  string  $operatingMode  PDV | STANDALONE
     * @return array<string, mixed>
     */
    public function changeDeviceOperatingMode(string $deviceId, string $operatingMode): array
    {
        return $this->makeRequest(
            HttpMethod::PATCH,
            '/point/integration-api/devices/'.rawurlencode($deviceId),
            body: ['operating_mode' => $operatingMode],
        );
    }
}
