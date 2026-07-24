<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Finance\FinancialEvents;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Finance\FinancialEventsPage;

/**
 * Endpoint Finances v0 da SP-API — eventos financeiros (taxas, comissoes,
 * fees FBA, estornos) de um pedido + NFe de envio FBA BR.
 *
 * Amazon manda fees com valor NEGATIVO (deducao do repasse).
 * Rate limit Finances: 0.5 req/s + burst 30.
 */
class Finances
{
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * NFe de UM envio FBA BR (GET /fba/outbound/brazil/v0/shipments/{id}/invoice).
     * Retorna `payload.invoice` ou [] em 404 (NFe ainda nao emitida).
     * Rate limit FBA Invoice: 1.133 req/s + burst 25.
     *
     * @return array<string, mixed>
     */
    public function getFbaShipmentInvoice(string $shipmentId): array
    {
        $resp = $this->client->get(
            '/fba/outbound/brazil/v0/shipments/'.rawurlencode($shipmentId).'/invoice'
        );

        return data_get($resp, 'payload.invoice', []);
    }

    /**
     * Eventos financeiros de UM pedido → `payload.FinancialEvents` tipado
     * (FinancialEvents; vazio quando o settlement não postou).
     */
    public function listOrderFinancialEvents(string $amazonOrderId): FinancialEvents
    {
        $resp = $this->client->get(
            '/finances/v0/orders/'.rawurlencode($amazonOrderId).'/financialEvents'
        );

        return FinancialEvents::fromArray(data_get($resp, 'payload.FinancialEvents', []));
    }

    /**
     * Eventos financeiros de UM PERÍODO (GET /finances/v0/financialEvents) —
     * devolve os eventos de VÁRIOS pedidos postados no intervalo, paginados por
     * NextToken. É o caminho eficiente pro backfill histórico: ~centenas de
     * eventos por chamada em vez de 1 chamada por pedido.
     *
     * Query aceita: PostedAfter (ISO8601, obrigatório na 1ª página), PostedBefore,
     * MaxResultsPerPage (1–100, default 100), NextToken (nas páginas seguintes —
     * quando presente, a Amazon IGNORA os demais filtros).
     *
     * Rate limit: 0.5 req/s + burst 30 (mesmo teto do por-pedido, mas rende
     * MUITO mais por chamada). Percorra as páginas até `nextToken === null`.
     *
     * @param  array<string, mixed>  $query
     */
    public function listFinancialEvents(array $query): FinancialEventsPage
    {
        $resp = $this->client->get('/finances/v0/financialEvents', $query);

        return FinancialEventsPage::fromArray(data_get($resp, 'payload', []));
    }
}
