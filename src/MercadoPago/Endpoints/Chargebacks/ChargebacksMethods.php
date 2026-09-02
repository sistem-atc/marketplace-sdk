<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\Chargebacks;

use Generator;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Chargebacks\ChargebackResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Chargebacks\ChargebackSearchResponseDTO;

/**
 * Chargebacks — contestacoes abertas pelo comprador junto ao emissor.
 *
 * Pro financeiro e' o que explica um debito no extrato sem reembolso
 * correspondente: o MP retem o valor (`amount`) enquanto a disputa corre e
 * so' devolve se `coverage_applied` ou se o seller ganhar. Consulta
 * complementar ao relatorio de liberacoes (SettlementMethods).
 *
 * Doc: https://www.mercadopago.com.br/developers/pt/reference/chargebacks/_chargebacks_id/get
 */
class ChargebacksMethods extends BaseMethods
{
    public function get(int|string $chargebackId): ChargebackResponseDTO
    {
        return ChargebackResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, "/v1/chargebacks/{$chargebackId}"));
    }

    /**
     * Filtros: payment_id, status, range de datas, limit, offset.
     *
     * @param  array<string, mixed>  $filters
     */
    public function search(array $filters = []): ChargebackSearchResponseDTO
    {
        return ChargebackSearchResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, '/v1/chargebacks/search', $filters));
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Generator<int, ChargebackResponseDTO>
     */
    public function searchAll(array $filters = [], int $limit = 100): Generator
    {
        return $this->paginate('/v1/chargebacks/search', $filters, $limit, map: ChargebackResponseDTO::fromArray(...));
    }
}
