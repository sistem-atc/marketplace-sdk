<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Supply Sources API 2020-07-01 (path /supplySources/2020-07-01) — cadastro
 * de origens de estoque (lojas/depósitos) pra Multi-Channel / BOPIS.
 *
 * O modelo não publica rate limit. Respostas no nível raiz (sem `payload`).
 */
class SupplySources
{
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Lista origens de estoque (GET /supplySources/2020-07-01/supplySources).
     * Paginação por `nextPageToken`. Retorno em `supplySources[]`.
     *
     * @param  array<string, mixed>  $query  nextPageToken, pageSize
     */
    public function getSupplySources(array $query = []): array
    {
        return $this->client->get('/supplySources/2020-07-01/supplySources', $query);
    }

    /**
     * Cria uma origem de estoque (POST /supplySources/2020-07-01/supplySources).
     * Retorno: supplySourceId + supplySourceCode.
     *
     * @param  array<string, mixed>  $payload  CreateSupplySourceRequest (supplySourceCode, alias, address)
     */
    public function createSupplySource(array $payload): array
    {
        return $this->client->post('/supplySources/2020-07-01/supplySources', $payload);
    }

    /**
     * Detalhe de uma origem (GET /supplySources/2020-07-01/supplySources/{supplySourceId}).
     */
    public function getSupplySource(string $supplySourceId): array
    {
        return $this->client->get('/supplySources/2020-07-01/supplySources/'.rawurlencode($supplySourceId));
    }

    /**
     * Atualiza configuração/capacidades de uma origem
     * (PUT /supplySources/2020-07-01/supplySources/{supplySourceId}).
     *
     * @param  array<string, mixed>  $payload  UpdateSupplySourceRequest (alias, configuration, capabilities)
     */
    public function updateSupplySource(string $supplySourceId, array $payload = []): array
    {
        return $this->client->put('/supplySources/2020-07-01/supplySources/'.rawurlencode($supplySourceId), $payload);
    }

    /**
     * Arquiva uma origem (DELETE /supplySources/2020-07-01/supplySources/{supplySourceId}).
     */
    public function archiveSupplySource(string $supplySourceId): array
    {
        return $this->client->delete('/supplySources/2020-07-01/supplySources/'.rawurlencode($supplySourceId));
    }

    /**
     * Muda o status de uma origem (Active/Inactive)
     * (PUT /supplySources/2020-07-01/supplySources/{supplySourceId}/status).
     *
     * @param  array<string, mixed>  $payload  UpdateSupplySourceStatusRequest (status)
     */
    public function updateSupplySourceStatus(string $supplySourceId, array $payload = []): array
    {
        return $this->client->put('/supplySources/2020-07-01/supplySources/'.rawurlencode($supplySourceId).'/status', $payload);
    }
}
