<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * FBA Inbound Eligibility API v1 da SP-API (`/fba/inbound/v1/eligibility`).
 *
 * Rate limit (modelo): 1 req/s + burst 1.
 */
class FbaInboundEligibility
{
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Elegibilidade de um ASIN para um programa (INBOUND | COMMINGLING)
     * (GET /fba/inbound/v1/eligibility/itemPreview). Retorno em `payload`
     * (`asin`, `marketplaceId`, `program`, `isEligibleForProgram`, `ineligibilityReasonList`).
     *
     * @param  array<string, mixed>  $query  marketplaceIds (obrigatorio quando program=INBOUND)
     */
    public function getItemEligibilityPreview(string $asin, string $program, array $query = []): array
    {
        return $this->client->get('/fba/inbound/v1/eligibility/itemPreview', ['asin' => $asin, 'program' => $program] + $query);
    }
}
