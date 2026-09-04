<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Endpoint Sellers v1 da SP-API.
 *
 * GET /sellers/v1/marketplaceParticipations e' GRANTLESS (usa o token da app,
 * nao exige o Selling Partner ID no path nem role especifica). Retorna as
 * participacoes do seller por marketplace (id, countryCode, storeName,
 * participation.isParticipating). Util pra confirmar conta/loja e descobrir
 * o marketplaceId de cada pais.
 *
 * NOTA: este endpoint NAO retorna o Selling Partner ID (merchant token). Esse
 * id vem do fluxo OAuth (selling_partner_id no callback), nao de uma API.
 *
 * Probado ao vivo 2026-07-05 (conta Soldiers): 5 participacoes.
 */
class Sellers
{
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * GET /sellers/v1/marketplaceParticipations.
     *
     * @return array<int, array<string, mixed>> lista de participacoes (payload)
     */
    public function getMarketplaceParticipations(): array
    {
        $resp = $this->client->get('/sellers/v1/marketplaceParticipations');

        return $resp['payload'] ?? [];
    }

    /**
     * Dados da CONTA do seller (GET /sellers/v1/account): businessType,
     * sellingPlan, marketplaceParticipationList, business (razão social,
     * endereço) e primaryContact. Dado em `payload`. Não é grantless.
     * Rate limit: 0.016 req/s + burst 15 (~1/min — cachear).
     *
     * @return array<string, mixed>
     */
    public function getAccount(): array
    {
        return $this->client->get('/sellers/v1/account');
    }
}
