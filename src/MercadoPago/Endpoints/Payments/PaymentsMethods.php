<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\Payments;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;

/**
 * Endpoints de Payments individuais Mercado Pago.
 *
 * Diferente do SettlementMethods (que entrega CSV agregado do periodo),
 * esses endpoints consultam UM pagamento especifico — uteis pra:
 *
 *   - Debug de pagamento sem repasse aparente
 *   - Validar status apos webhook
 *   - Buscar payment_id por external_reference (= order_id ML/Shopee/etc)
 *
 * Doc:
 *   https://www.mercadopago.com.br/developers/pt/reference/payments/_payments_id/get
 *   https://www.mercadopago.com.br/developers/pt/reference/payments/_payments_search/get
 */
class PaymentsMethods extends BaseMethods
{
    /**
     * Detalhe de um pagamento por ID. Resposta inclui:
     *   - id, status, status_detail
     *   - transaction_amount (bruto)
     *   - net_received_amount (liquido recebido pelo seller)
     *   - fee_details (taxas MP)
     *   - external_reference (order_id no marketplace)
     *   - order.id (merchant_order do MP)
     *   - date_approved, date_released (quando libera no saldo)
     *
     * @return array<string, mixed>
     */
    public function get(int|string $paymentId): array
    {
        return $this->makeRequest(
            method: HttpMethod::GET,
            path: "/v1/payments/{$paymentId}",
        );
    }

    /**
     * Busca pagamentos por filtros. Util pra encontrar payment_id MP
     * a partir do order_id ML (external_reference).
     *
     * @param  array<string, mixed>  $filters  Aceita: external_reference,
     *                                         status, range_date_created, sort, criteria, limit, offset, etc.
     * @return array<string, mixed>
     */
    public function search(array $filters = []): array
    {
        return $this->makeRequest(
            method: HttpMethod::GET,
            path: '/v1/payments/search',
            query: $filters,
        );
    }

    /**
     * Lookup direto por external_reference (= order_id no marketplace).
     * Helper conveniente em cima do search().
     *
     * @return array<int, array<string, mixed>>
     */
    public function findByExternalReference(string $externalRef): array
    {
        $resp = $this->search(['external_reference' => $externalRef]);

        return $resp['results'] ?? [];
    }
}
