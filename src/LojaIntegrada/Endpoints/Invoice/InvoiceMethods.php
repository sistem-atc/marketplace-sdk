<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Invoice;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\LojaIntegrada\Bases\BaseMethods;

/**
 * Nota fiscal do pedido (`/v1/integration/pedido/nf`, `/v1/pedido_nf/{id}`) e
 * DCE — Declaração de Conteúdo (`/v1/integration/pedido/dce`, `/v1/pedido_dce/{numero}`).
 * Os POST/PUT de integração vão como multipart/form-data (contrato da API).
 */
class InvoiceMethods extends BaseMethods
{
    /**
     * Insere a NF do pedido. `account_key` = chave_api da loja (preenchida das settings se omitida).
     *
     * @param array<string,mixed> $data sale_number, date, invoice_number, serie, access_key, url (pdf), url_xml
     * @return array<string,mixed>
     */
    public function create(array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, 'integration/pedido/nf/', [], $this->withAccountKey($data), multipart: true);
    }

    /**
     * @param array<string,mixed> $data mesmos campos de create()
     * @return array<string,mixed>
     */
    public function update(array $data): array
    {
        return $this->makeRequest(HttpMethod::PUT, 'integration/pedido/nf/', [], $this->withAccountKey($data), multipart: true);
    }

    /** @return array<string,mixed> */
    public function get(int|string $nfId): array
    {
        return $this->makeRequest(HttpMethod::GET, "pedido_nf/{$nfId}/");
    }

    /**
     * Vincula DCE ao pedido.
     *
     * @param array<string,mixed> $data order_number, access_key, url_xml
     * @return array<string,mixed>
     */
    public function linkDce(array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, 'integration/pedido/dce/', [], $data, multipart: true);
    }

    /** @return array<string,mixed> */
    public function unlinkDce(int|string $orderNumber): array
    {
        return $this->makeRequest(HttpMethod::DELETE, 'integration/pedido/dce/', ['order_number' => $orderNumber]);
    }

    /** @return array<string,mixed> */
    public function getDce(int|string $numero): array
    {
        return $this->makeRequest(HttpMethod::GET, "pedido_dce/{$numero}/");
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    private function withAccountKey(array $data): array
    {
        if (! isset($data['account_key'])) {
            $settings = $this->integration->getMarketplaceSettings();
            $data['account_key'] = $settings['api_key'] ?? ($settings['chave_api'] ?? '');
        }

        return $data;
    }
}
