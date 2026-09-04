<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\DraftOrder;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Concerns\PaginatesRestByCursor;

/**
 * Draft Orders (`draft_orders`) — rascunhos de pedido criados pelo lojista/app.
 */
class DraftOrderMethods extends BaseMethods
{
    use PaginatesRestByCursor;

    /**
     * Lista draft orders (1 pagina; max 250). Filtros: fields, limit, since_id,
     * updated_at_min/max, ids, status (open|invoice_sent|completed).
     *
     * @param  array<string, mixed>  $params
     */
    public function list(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/draft_orders', $params);
    }

    /**
     * Itera TODAS as draft orders seguindo o cursor (page_info).
     *
     * @param  array<string, mixed>  $params  filtros so' na 1a pagina
     * @return \Generator<int, array<string, mixed>>
     */
    public function each(array $params = [], int $limit = 250): \Generator
    {
        yield from $this->eachPage('/draft_orders', 'draft_orders', $params, $limit);
    }

    /**
     * Conta draft orders (since_id, status, updated_at_min/max).
     *
     * @param  array<string, mixed>  $params
     */
    public function count(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/draft_orders/count', $params);
    }

    /**
     * Recupera uma draft order.
     */
    public function get(int|string $draftOrderId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/draft_orders/{$draftOrderId}", $params);
    }

    /**
     * Cria uma draft order. Embrulha em `draft_order`.
     *
     * @param  array<string, mixed>  $draftOrder
     */
    public function create(array $draftOrder): array
    {
        return $this->makeRequest(HttpMethod::POST, '/draft_orders', [], ['draft_order' => $draftOrder]);
    }

    /**
     * Atualiza uma draft order. Embrulha em `draft_order`.
     *
     * @param  array<string, mixed>  $draftOrder
     */
    public function update(int|string $draftOrderId, array $draftOrder): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/draft_orders/{$draftOrderId}", [], ['draft_order' => $draftOrder]);
    }

    /**
     * Exclui uma draft order.
     */
    public function delete(int|string $draftOrderId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/draft_orders/{$draftOrderId}");
    }

    /**
     * Envia a fatura por e-mail (POST /draft_orders/{id}/send_invoice).
     * Embrulha em `draft_order_invoice` — ex.: ['to' => ..., 'subject' => ..., 'custom_message' => ...].
     * Vazio = usa o e-mail do cliente e o template padrao.
     *
     * @param  array<string, mixed>  $invoice
     */
    public function sendInvoice(int|string $draftOrderId, array $invoice = []): array
    {
        return $this->makeRequest(HttpMethod::POST, "/draft_orders/{$draftOrderId}/send_invoice", [], [
            'draft_order_invoice' => $invoice === [] ? new \stdClass : $invoice,
        ]);
    }

    /**
     * Completa a draft order virando pedido (PUT /draft_orders/{id}/complete).
     * `payment_pending=true` marca o pedido como pendente de pagamento.
     */
    public function complete(int|string $draftOrderId, bool $paymentPending = false, int|string|null $paymentGatewayId = null): array
    {
        $query = ['payment_pending' => $paymentPending ? 'true' : 'false'];
        if ($paymentGatewayId !== null) {
            $query['payment_gateway_id'] = $paymentGatewayId;
        }

        return $this->makeRequest(HttpMethod::PUT, "/draft_orders/{$draftOrderId}/complete", $query);
    }
}
