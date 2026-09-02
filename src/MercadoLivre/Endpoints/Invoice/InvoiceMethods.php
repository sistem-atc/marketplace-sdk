<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Invoice;

use Illuminate\Http\Client\Response;
use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice\BillingInfoResponseDTO;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice\InvoiceResponseDTO;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreRequestException;

class InvoiceMethods extends BaseMethods
{
    /**
     * Dados fiscais do comprador (GET /orders/{id}/billing_info) como DTO tipado.
     * Leia campos com `->billingInfo?->additional('FIRST_NAME')` — o ML manda tudo
     * como lista chave-valor. `toArray()` e' LOSSLESS (validado vs 544 reais).
     *
     * Erros (404 sem billing / 403 sem escopo) seguem estourando — o caller
     * decide o que fazer (ver PullMlBillingInfoJob).
     */
    public function getBillingInfo(int|string $orderId): BillingInfoResponseDTO
    {
        return BillingInfoResponseDTO::fromArray(
            $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/billing_info")
        );
    }

    public function getByOrderId(int|string $sellerId, int|string $orderId): ?InvoiceResponseDTO
    {
        return $this->toInvoiceDto(
            $this->httpClient->get("/users/{$sellerId}/invoices/orders/{$orderId}")
        );
    }

    /**
     * Nota fiscal individual por ID (resource do webhook `invoices`):
     * GET /users/{userId}/invoices/{invoiceId}.
     * Retorna null em 404 (NFe inexistente).
     */
    public function getById(int|string $userId, int|string $invoiceId): ?InvoiceResponseDTO
    {
        return $this->toInvoiceDto(
            $this->httpClient->get("/users/{$userId}/invoices/{$invoiceId}")
        );
    }

    public function getByShipment(int|string $sellerId, int|string $shipmentId): ?InvoiceResponseDTO
    {
        return $this->toInvoiceDto(
            $this->httpClient->get("/users/{$sellerId}/invoices/shipments/{$shipmentId}")
        );
    }

    /**
     * Parse UNICO das 3 chamadas de invoice-metadata: 404 -> null; erro ->
     * exception; senao hidrata a arvore completa (o DTO e' a RAIZ da resposta,
     * com o bloco de emissao em `->attributes`).
     */
    private function toInvoiceDto(Response $response): ?InvoiceResponseDTO
    {
        if ($response->status() === 404) {
            return null;
        }
        if ($response->failed()) {
            throw new MercadoLivreRequestException($response);
        }

        $json = (array) $response->json();

        return InvoiceResponseDTO::fromArray($json);
    }

    public function downloadXmlByLocation(string $xmlLocation): string
    {
        $response = $this->httpClient->timeout(60)->get($xmlLocation);
        if ($response->failed()) throw new MercadoLivreRequestException($response);
        return (string) $response->body();
    }

    public function batchDownloadInvoicesZip(int|string $sellerId, string $startDate, string $endDate, array $options = []): string
    {
        $url = sprintf('/users/%s/invoices/sites/%s/batch_request/period/stream', $sellerId, $options['site_id'] ?? 'MLB');
        $params = array_merge([
            'start' => str_replace('-', '', $startDate),
            'end' => str_replace('-', '', $endDate),
            'sale' => 'all',
            'return' => 'all',
            'full' => 'all',
            'file_types' => 'xml',
        ], $options);

        $response = $this->httpClient->timeout(600)->get($url, $params);
        if ($response->failed()) throw new MercadoLivreRequestException($response);
        return (string) $response->body();
    }

    // -----------------------------------------------------------------
    // Dados pra emissao (Enviar Nota Fiscal)
    // -----------------------------------------------------------------

    /**
     * Dados fiscais do comprador pelo billing_info_id
     * (GET /orders/billing-info/{siteId}/{billingInfoId}). O id vem de
     * `order.buyer.billing_info.id` no GET /orders/{id}. Resposta ja
     * estruturada (name, identification, taxes, address) — diferente de
     * getBillingInfo(), que devolve a lista chave-valor legada.
     *
     * @return array<string, mixed>
     */
    public function getBillingInfoById(string $billingInfoId, string $siteId = 'MLB'): array
    {
        $billingInfoId = rawurlencode($billingInfoId);

        return $this->makeRequest(HttpMethod::GET, "/orders/billing-info/{$siteId}/{$billingInfoId}");
    }

    /**
     * Documentos fiscais dos atores do envio (GET /shipments/{id}/billing_info):
     * `receiver`, `senders[]` (CNPJ + IE em additional_documents) e `carrier`.
     * Usado pra montar a NF-e de Coletas/Places (NT2020.006: CNPJ do
     * intermediador = Mercado Livre).
     *
     * @return array<string, mixed>
     */
    public function shipmentBillingInfo(int|string $shipmentId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/shipments/{$shipmentId}/billing_info");
    }

    /**
     * NF-e importada num envio (GET /shipments/{id}/invoice_data?siteId=):
     * fiscal_key, invoice_serie/number/amount, status, invoice_date. 404 se
     * nada foi importado.
     *
     * @return array<string, mixed>
     */
    public function shipmentInvoiceData(int|string $shipmentId, string $siteId = 'MLB'): array
    {
        return $this->makeRequest(HttpMethod::GET, "/shipments/{$shipmentId}/invoice_data", ['siteId' => $siteId]);
    }

    /**
     * Importa a NF-e (XML modelo 55 autorizado, com <nfeProc>) num envio em
     * ready_to_ship/invoice_pending (POST /shipments/{id}/invoice_data/?siteId=,
     * Content-Type application/xml). Destrava o substatus e libera a etiqueta
     * pra drop_off/xd_drop_off/xd_same_day/cross_docking. So MLB; so PJ
     * contribuinte (PF usa DC-e). Depois da etiqueta impressa nao da mais pra
     * trocar. Erros vem com `error_code` (duplicated_fiscal_key,
     * nfe_order_value_divergence, invalid_nfe_cstat, ...).
     *
     * @return array<string, mixed>
     */
    public function importShipmentInvoice(int|string $shipmentId, string $xml, string $siteId = 'MLB'): array
    {
        return $this->sendXml(HttpMethod::POST, "/shipments/{$shipmentId}/invoice_data/", $xml, $siteId);
    }

    /**
     * Substitui a NF-e ja importada (PUT /shipment_invoice/{invoiceId}/?siteId=).
     * O invoiceId e o `id` devolvido por shipmentInvoiceData(), NAO o
     * shipment_id. Mesmas regras do importShipmentInvoice().
     *
     * @return array<string, mixed>
     */
    public function updateShipmentInvoice(int|string $invoiceId, string $xml, string $siteId = 'MLB'): array
    {
        return $this->sendXml(HttpMethod::PUT, "/shipment_invoice/{$invoiceId}/", $xml, $siteId);
    }

    // -----------------------------------------------------------------
    // Faturador Mercado Livre
    // -----------------------------------------------------------------

    /**
     * Se o anuncio esta apto a ser faturado pelo Faturador ML
     * (GET /can_invoice/items/{itemId}[/variations/{variationId}]) ->
     * `{item_id, seller_id, variation_id, status}`. Anuncio com variacoes so
     * fica true quando TODAS as variacoes estao aptas — consulte por variacao
     * pra achar a que falta dado fiscal.
     *
     * @return array{item_id?: string, seller_id?: string, variation_id?: string, status?: bool}
     */
    public function canInvoice(string $itemId, int|string|null $variationId = null): array
    {
        $path = '/can_invoice/items/'.rawurlencode($itemId);
        if ($variationId !== null) {
            $path .= "/variations/{$variationId}";
        }

        return $this->makeRequest(HttpMethod::GET, $path);
    }

    /**
     * Grupos de regras tributarias do seller (GET /users/{id}/invoices/tax_rules),
     * paginado (`paging`, `results[]`). So Regime Normal usa; Simples Nacional
     * nao cadastra regra.
     *
     * @return array<string, mixed>
     */
    public function taxRules(int|string $userId, int $offset = 0, int $limit = 50): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$userId}/invoices/tax_rules", [
            'offset' => max(0, $offset),
            'limit' => max(1, $limit),
        ]);
    }

    /**
     * Um grupo de regras tributarias (GET /users/{id}/invoices/tax_rules/{ruleId}).
     *
     * @return array<string, mixed>
     */
    public function taxRule(int|string $userId, int|string $ruleId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$userId}/invoices/tax_rules/{$ruleId}");
    }

    /**
     * Cria grupo de regras tributarias (POST /users/{id}/invoices/tax_rules).
     * O JSON vai COMPLETO (description, user_id, transactions[].operations[]
     * .rules[] com icms/pis/cofins/ipi/difal/ibs-cbs...) — nao existe envio
     * parcial. O `id` devolvido e o tax_rule_id que vai no dado fiscal do SKU.
     *
     * @param  array<string, mixed>  $rule
     * @return array<string, mixed>
     */
    public function createTaxRule(int|string $userId, array $rule): array
    {
        return $this->makeRequest(HttpMethod::POST, "/users/{$userId}/invoices/tax_rules", body: $rule);
    }

    /**
     * Substitui um grupo de regras (PUT /users/{id}/invoices/tax_rules/{ruleId}),
     * JSON completo.
     *
     * @param  array<string, mixed>  $rule
     * @return array<string, mixed>
     */
    public function updateTaxRule(int|string $userId, int|string $ruleId, array $rule): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/users/{$userId}/invoices/tax_rules/{$ruleId}", body: $rule);
    }

    /**
     * Mensagens de operacao (campo de observacoes da NF-e), paginado
     * (GET /users/{id}/invoices/tax_rules/messages).
     *
     * @return array<string, mixed>
     */
    public function taxRuleMessages(int|string $userId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$userId}/invoices/tax_rules/messages");
    }

    /**
     * Uma mensagem de operacao (GET /users/{id}/invoices/tax_rules/messages/{messageId}).
     *
     * @return array<string, mixed>
     */
    public function taxRuleMessage(int|string $userId, int|string $messageId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$userId}/invoices/tax_rules/messages/{$messageId}");
    }

    /**
     * Cria mensagem de operacao (POST /users/{id}/invoices/tax_rules/messages).
     * `type`: COMPL (vai no PDF/DANFE, padrao, aceita a tag $EXTERNAL_ORDER_ID),
     * ITEM, FISCAL (so no XML da SEFAZ) ou ADDITIONAL. Doc manda `user_id`
     * tambem no body — vai junto.
     *
     * @return array<string, mixed>
     */
    public function createTaxRuleMessage(int|string $userId, string $message, string $type = 'COMPL'): array
    {
        return $this->makeRequest(HttpMethod::POST, "/users/{$userId}/invoices/tax_rules/messages", body: [
            'user_id' => is_numeric($userId) ? (int) $userId : $userId,
            'message' => $message,
            'type' => $type,
        ]);
    }

    /**
     * Atualiza mensagem de operacao (PUT /users/{id}/invoices/tax_rules/messages/{messageId}).
     *
     * @return array<string, mixed>
     */
    public function updateTaxRuleMessage(int|string $userId, int|string $messageId, string $message, string $type = 'COMPL'): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/users/{$userId}/invoices/tax_rules/messages/{$messageId}", body: [
            'user_id' => is_numeric($userId) ? (int) $userId : $userId,
            'message' => $message,
            'type' => $type,
        ]);
    }

    /**
     * Apaga mensagem de operacao (DELETE /users/{id}/invoices/tax_rules/messages/{messageId}).
     * Resposta e so o HTTP 200 -> [].
     *
     * @return array<string, mixed>
     */
    public function deleteTaxRuleMessage(int|string $userId, int|string $messageId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/users/{$userId}/invoices/tax_rules/messages/{$messageId}");
    }

    /**
     * Mensagens adicionais da NF-e com filtros fiscais
     * (GET /users/{id}/invoices/fiscal_rules/v2/additional-messages). Limite
     * de 30 cadastradas por seller.
     *
     * @return array<int|string, mixed>
     */
    public function additionalMessages(int|string $userId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$userId}/invoices/fiscal_rules/v2/additional-messages");
    }

    /**
     * Uma mensagem adicional (GET .../fiscal_rules/v2/additional-messages/{messageId}).
     *
     * @return array<string, mixed>
     */
    public function additionalMessage(int|string $userId, int|string $messageId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$userId}/invoices/fiscal_rules/v2/additional-messages/{$messageId}");
    }

    /**
     * Cria mensagem adicional (POST .../fiscal_rules/v2/additional-messages).
     * Body: title (<=255), message (<=500, aceita tags $IBPT_TOTAL_VALUE,
     * $ICMS_VFCPUFDEST, $ORIGIN_NUMERO_NF...), type (custom | custom_with_filter)
     * e filters[] {name (transactiontype, customertype, recipientstate,
     * sellerregime, sellerstate, origindetail...), operation (eq...), value}.
     *
     * @param  array<string, mixed>  $message
     * @return array<string, mixed>
     */
    public function createAdditionalMessage(int|string $userId, array $message): array
    {
        return $this->makeRequest(HttpMethod::POST, "/users/{$userId}/invoices/fiscal_rules/v2/additional-messages", body: $message);
    }

    /**
     * Atualiza mensagem adicional (PUT .../fiscal_rules/v2/additional-messages/{messageId}),
     * mesmo body do create.
     *
     * @param  array<string, mixed>  $message
     * @return array<string, mixed>
     */
    public function updateAdditionalMessage(int|string $userId, int|string $messageId, array $message): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/users/{$userId}/invoices/fiscal_rules/v2/additional-messages/{$messageId}", body: $message);
    }

    /**
     * Remove mensagem adicional (DELETE .../fiscal_rules/v2/additional-messages/{messageId}).
     *
     * @return array<string, mixed>
     */
    public function deleteAdditionalMessage(int|string $userId, int|string $messageId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/users/{$userId}/invoices/fiscal_rules/v2/additional-messages/{$messageId}");
    }

    /**
     * XML autorizado de uma NF-e do Faturador
     * (GET /users/{id}/invoices/documents/xml/{invoiceId}/authorized) — corpo
     * bruto. E o mesmo path que vem em `attributes.xml_location`; se ja tiver
     * a location, downloadXmlByLocation() serve igual.
     */
    public function downloadAuthorizedXml(int|string $userId, int|string $invoiceId): string
    {
        return $this->rawGet("/users/{$userId}/invoices/documents/xml/{$invoiceId}/authorized");
    }

    /**
     * Descricao de um erro de emissao do Faturador
     * (GET /users/invoices/errors/{siteId}/{errorCode}) -> `display_message`
     * + `front_properties.correctedBy`. O errorCode vem do `error_code` da
     * resposta de erro do emitForOrders().
     *
     * @return array<string, mixed>
     */
    public function invoiceError(int|string $errorCode, string $siteId = 'MLB'): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/invoices/errors/{$siteId}/{$errorCode}");
    }

    /**
     * Emite NF-e pelo Faturador ML a partir de 1..N orders do MESMO vendedor
     * (POST /users/{id}/invoices/orders, body {orders: [...]}). Em carrinho,
     * mande TODAS as orders do pack — vira uma nota unica. Sucesso devolve a
     * invoice completa (mesma arvore do getById()); falha devolve
     * `{message, error_code}` via exception — consulte invoiceError().
     *
     * @param  list<int|string>  $orderIds
     * @return array<string, mixed>
     */
    public function emitForOrders(int|string $userId, array $orderIds): array
    {
        return $this->makeRequest(HttpMethod::POST, "/users/{$userId}/invoices/orders", body: [
            'orders' => array_map(fn ($id) => is_numeric($id) ? (int) $id : $id, array_values($orderIds)),
        ]);
    }

    /**
     * ZIP com todas as NF-es de um MES
     * (GET /users/{id}/invoices/sites/{site}/batch_request/period/{AAAAMM}).
     * Sem filtros: vem tudo organizado em `emitidas_mercado_livre` e
     * `emitidas_outro_erp`. Pra intervalo de datas/filtros use
     * batchDownloadInvoicesZip() (variante /stream).
     *
     * @param  string  $yearMonth  AAAAMM (ex.: 202605) ou AAAA-MM
     */
    public function batchDownloadInvoicesZipByMonth(int|string $sellerId, string $yearMonth, string $siteId = 'MLB'): string
    {
        $period = str_replace('-', '', $yearMonth);
        if (! preg_match('/^\d{6}$/', $period)) {
            throw new \InvalidArgumentException("Periodo invalido: {$yearMonth}. Use AAAAMM.");
        }

        return $this->rawGet("/users/{$sellerId}/invoices/sites/{$siteId}/batch_request/period/{$period}", [], 600);
    }

    // -----------------------------------------------------------------
    // DC-e (Declaracao de Conteudo Eletronica) — so MLB, seller PF / PJ
    // nao-contribuinte. PJ contribuinte segue no importShipmentInvoice().
    // -----------------------------------------------------------------

    /**
     * Inicia a emissao da DC-e do pedido (POST /mlb/order/{id}/dce/emission)
     * -> `{id}` da cobertura fiscal. Prazo: 3 dias corridos apos a venda,
     * senao o pedido e cancelado automaticamente. order_id so digitos.
     *
     * @return array{id?: string}
     */
    public function dceEmit(int|string $orderId): array
    {
        return $this->makeRequest(HttpMethod::POST, "/mlb/order/{$orderId}/dce/emission");
    }

    /**
     * Status da cobertura DC-e do pedido (GET /mlb/order/{id}/dce/info):
     * `status` (pending|completed|error), `sub_status`, `documents[]`
     * com dce_key, status (authorized|rejected|issued), files[].location.
     *
     * @return array<string, mixed>
     */
    public function dceInfo(int|string $orderId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/mlb/order/{$orderId}/dce/info");
    }

    /**
     * Download do documento DC-e (GET /mlb/order/{id}/dce/info/{dceKey}?doctype=)
     * — corpo bruto (pdf | xml | json). O dceKey vem de documents[].dce_key
     * do dceInfo().
     */
    public function dceDownload(int|string $orderId, string $dceKey, string $doctype = 'pdf'): string
    {
        if (! in_array($doctype, ['pdf', 'xml', 'json'], true)) {
            throw new \InvalidArgumentException("doctype invalido: {$doctype}. Use pdf, xml ou json.");
        }

        return $this->rawGet("/mlb/order/{$orderId}/dce/info/".rawurlencode($dceKey), ['doctype' => $doctype], 120);
    }

    // -----------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------

    /**
     * GET que devolve o corpo bruto (XML/PDF/ZIP) em vez de JSON.
     *
     * @param  array<string, mixed>  $query
     */
    private function rawGet(string $path, array $query = [], int $timeout = 60): string
    {
        $response = $this->httpClient->timeout($timeout)->get($path, $query);
        if ($response->failed()) throw new MercadoLivreRequestException($response);

        return (string) $response->body();
    }

    // ── Inscrições estaduais do Faturador (doc "Envio de inscrições estaduais") ─

    /**
     * Inscrições estaduais cadastradas pro CNPJ
     * (GET /users/{id}/invoices/state_registry/{cnpj}):
     * `[{seller_id, cnpj, state, state_registry, gnre_enable, origin}]`.
     *
     * @return array<int, array<string, mixed>>
     */
    public function stateRegistries(int|string $userId, string $cnpj): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$userId}/invoices/state_registry/".rawurlencode($cnpj));
    }

    /**
     * Inscrição estadual de UM estado (GET .../state_registry/{cnpj}/{state}).
     * `state` é a UF em minúsculo (sp, rj, mg...).
     *
     * @return array<string, mixed>
     */
    public function stateRegistry(int|string $userId, string $cnpj, string $state): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/users/{$userId}/invoices/state_registry/".rawurlencode($cnpj).'/'.rawurlencode(strtolower($state)),
        );
    }

    /**
     * Cadastra inscrição estadual num estado (POST .../state_registry/{cnpj}/{state},
     * body `{state_registry, gnre_enable}`). 201 com o registro criado;
     * `gnre_enable=true` faz o Faturador emitir GNRE do DIFAL pra essa UF.
     *
     * @return array<string, mixed>
     */
    public function createStateRegistry(int|string $userId, string $cnpj, string $state, string $stateRegistry, bool $gnreEnable = false): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            "/users/{$userId}/invoices/state_registry/".rawurlencode($cnpj).'/'.rawurlencode(strtolower($state)),
            [],
            ['state_registry' => $stateRegistry, 'gnre_enable' => $gnreEnable],
        );
    }

    /**
     * Atualiza inscrição estadual existente (PUT .../state_registry/{cnpj}/{state}).
     *
     * @return array<string, mixed>
     */
    public function updateStateRegistry(int|string $userId, string $cnpj, string $state, string $stateRegistry, bool $gnreEnable = false): array
    {
        return $this->makeRequest(
            HttpMethod::PUT,
            "/users/{$userId}/invoices/state_registry/".rawurlencode($cnpj).'/'.rawurlencode(strtolower($state)),
            [],
            ['state_registry' => $stateRegistry, 'gnre_enable' => $gnreEnable],
        );
    }

    /**
     * Cria/atualiza várias inscrições de uma vez (PUT .../state_registry/{cnpj}/batch).
     * Body: lista `[{state, state_registry, gnre_enable}]` — o ML decide se
     * insere ou atualiza cada uma. Sempre 200: quando alguma falha, o corpo
     * traz a lista com o erro por registro.
     *
     * @param  array<int, array<string, mixed>>  $registries
     * @return array<string, mixed>
     */
    public function batchStateRegistries(int|string $userId, string $cnpj, array $registries): array
    {
        return $this->makeRequest(
            HttpMethod::PUT,
            "/users/{$userId}/invoices/state_registry/".rawurlencode($cnpj).'/batch',
            [],
            array_values($registries),
        );
    }

    /**
     * Apaga a inscrição estadual de um estado (DELETE .../state_registry/{cnpj}/{state}).
     * 200 sem corpo.
     *
     * @return array<string, mixed>
     */
    public function deleteStateRegistry(int|string $userId, string $cnpj, string $state): array
    {
        return $this->makeRequest(
            HttpMethod::DELETE,
            "/users/{$userId}/invoices/state_registry/".rawurlencode($cnpj).'/'.rawurlencode(strtolower($state)),
        );
    }

    /**
     * POST/PUT com corpo XML cru (Content-Type application/xml) + `?siteId=`.
     * Clona o client porque withBody() muta o PendingRequest.
     *
     * @return array<string, mixed>
     */
    private function sendXml(HttpMethod $method, string $path, string $xml, string $siteId): array
    {
        $url = $path.'?'.http_build_query(['siteId' => $siteId]);
        $client = (clone $this->httpClient)->withBody($xml, 'application/xml');

        $response = $method === HttpMethod::PUT ? $client->put($url) : $client->post($url);
        if ($response->failed()) throw new MercadoLivreRequestException($response);

        return (array) $response->json();
    }
}
