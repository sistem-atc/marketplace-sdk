<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Enviali;

use Illuminate\Support\Str;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\LojaIntegrada\Bases\BaseMethods;

/**
 * Enviali — etiquetas de envio (`/enviali/v2`, fora do `/v1`). Toda chamada leva o
 * header `x-correlation-id` (UUID); se não informado, gera um. Filtros de listagem:
 * id[], status[] (created, billed, cancelling, cancelled, posted), trackingStatus[], page/limit.
 */
class EnvialiMethods extends BaseMethods
{
    /**
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    public function listPostages(array $filters = [], ?string $correlationId = null): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->rootUrl('enviali/v2/postage'), $filters, headers: $this->headers($correlationId));
    }

    /** @return array<string,mixed> */
    public function getPostage(int|string $postageId, ?string $correlationId = null): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->rootUrl("enviali/v2/postage/{$postageId}"), headers: $this->headers($correlationId));
    }

    /**
     * Programa o cancelamento (efetivo em até 2 dias úteis).
     *
     * @param int|string|array<int,int|string> $ids
     * @return array<string,mixed>
     */
    public function cancelPostage(int|string|array $ids, ?string $correlationId = null): array
    {
        return $this->makeRequest(HttpMethod::DELETE, $this->rootUrl('enviali/v2/postage'), ['id' => is_array($ids) ? implode(',', $ids) : $ids], headers: $this->headers($correlationId));
    }

    /**
     * Gera (fatura) etiquetas.
     *
     * @param array<int,array<string,mixed>> $postage lista de {order, recipient, sender, service...}
     * @return array<string,mixed>
     */
    public function billPostage(array $postage, ?string $correlationId = null): array
    {
        return $this->makeRequest(HttpMethod::POST, $this->rootUrl('enviali/v2/postage/bill'), [], ['postage' => array_values($postage)], headers: $this->headers($correlationId));
    }

    /**
     * Cotação da etiqueta.
     *
     * @param array<string,mixed> $data zipcode, merchandise_value, products[], order_date, options{}
     * @return array<string,mixed>
     */
    public function estimate(array $data, ?string $correlationId = null): array
    {
        return $this->makeRequest(HttpMethod::POST, $this->rootUrl('enviali/v2/postage/estimate'), [], $data, headers: $this->headers($correlationId));
    }

    /**
     * @param int|string|array<int,int|string> $ids
     * @return array<string,mixed>
     */
    public function documents(int|string|array $ids, ?string $correlationId = null): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->rootUrl('enviali/v2/postage/doc'), ['ids' => is_array($ids) ? implode(',', $ids) : $ids], headers: $this->headers($correlationId));
    }

    /**
     * @param int|string|array<int,int|string> $ids
     * @param string|null $paperType ex.: A4, A6
     * @return array<string,mixed>
     */
    public function pdf(int|string|array $ids, ?string $paperType = null, ?string $correlationId = null): array
    {
        $query = ['ids' => is_array($ids) ? implode(',', $ids) : $ids];
        if ($paperType !== null) {
            $query['paperType'] = $paperType;
        }

        return $this->makeRequest(HttpMethod::GET, $this->rootUrl('enviali/v2/postage/pdf'), $query, headers: $this->headers($correlationId));
    }

    /** @return array<string,mixed> */
    public function tracking(string $trackingCode, ?string $correlationId = null): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->rootUrl("enviali/v2/postage/tracking/{$trackingCode}"), headers: $this->headers($correlationId));
    }

    /** Saldo/carteira Enviali. @return array<string,mixed> */
    public function wallets(?string $correlationId = null): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->rootUrl('enviali/v2/wallets'), headers: $this->headers($correlationId));
    }

    /** @return array<string,string> */
    private function headers(?string $correlationId): array
    {
        return ['x-correlation-id' => $correlationId ?? (string) Str::uuid()];
    }
}
