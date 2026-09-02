<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\Point;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Point\PaymentIntentCancelResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Point\PaymentIntentListResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Point\PaymentIntentResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Point\PaymentIntentStatusResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Point\PointDeviceOperatingModeResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Point\PointDevicesResponseDTO;

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
     */
    public function createPaymentIntent(string $deviceId, array $payload): PaymentIntentResponseDTO
    {
        return PaymentIntentResponseDTO::fromArray($this->makeRequest(
            HttpMethod::POST,
            '/point/integration-api/devices/'.rawurlencode($deviceId).'/payment-intents',
            body: $payload,
        ));
    }

    public function getPaymentIntent(string $paymentIntentId): PaymentIntentResponseDTO
    {
        return PaymentIntentResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, '/point/integration-api/payment-intents/'.rawurlencode($paymentIntentId)));
    }

    /**
     * Cancela uma intencao ainda nao paga (OPEN/ON_TERMINAL).
     */
    public function cancelPaymentIntent(string $deviceId, string $paymentIntentId): PaymentIntentCancelResponseDTO
    {
        return PaymentIntentCancelResponseDTO::fromArray($this->makeRequest(
            HttpMethod::DELETE,
            '/point/integration-api/devices/'.rawurlencode($deviceId).'/payment-intents/'.rawurlencode($paymentIntentId),
        ));
    }

    /**
     * Eventos de intencao no periodo (datas `Y-m-d`). Janela maxima de 30 dias.
     */
    public function listPaymentIntents(string $startDate, string $endDate): PaymentIntentListResponseDTO
    {
        return PaymentIntentListResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, '/point/integration-api/payment-intents/events', [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]));
    }

    /**
     * Ultimo status da intencao (OPEN, ON_TERMINAL, PROCESSING, FINISHED, CANCELED, ERROR...).
     */
    public function getPaymentIntentStatus(string $paymentIntentId): PaymentIntentStatusResponseDTO
    {
        return PaymentIntentStatusResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, '/point/integration-api/payment-intents/'.rawurlencode($paymentIntentId).'/events'));
    }

    /**
     * Devices da conta. Filtros: store_id, pos_id, limit, offset.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getDevices(array $filters = []): PointDevicesResponseDTO
    {
        return PointDevicesResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, '/point/integration-api/devices', $filters));
    }

    /**
     * @param  string  $operatingMode  PDV | STANDALONE
     */
    public function changeDeviceOperatingMode(string $deviceId, string $operatingMode): PointDeviceOperatingModeResponseDTO
    {
        return PointDeviceOperatingModeResponseDTO::fromArray($this->makeRequest(
            HttpMethod::PATCH,
            '/point/integration-api/devices/'.rawurlencode($deviceId),
            body: ['operating_mode' => $operatingMode],
        ));
    }
}
