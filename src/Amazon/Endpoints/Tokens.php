<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Tokens API v2021-03-01 — Restricted Data Token (RDT).
 *
 * Operações que retornam PII (Orders buyer info, Tax Invoices, etc.) exigem um
 * RDT de curta duração no lugar do access token LWA normal. Fluxo:
 *   1. createRestrictedDataToken([{method, path}]) -> RDT (string)
 *   2. usar o RDT como x-amz-access-token na chamada restrita.
 *
 * Esta chamada usa o token LWA normal (não-restrito).
 */
class Tokens
{
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * @param  array<int, array{method: string, path: string, dataElements?: array<int, string>}>  $restrictedResources
     */
    public function createRestrictedDataToken(array $restrictedResources): string
    {
        $resp = $this->client->post('/tokens/2021-03-01/restrictedDataToken', [
            'restrictedResources' => $restrictedResources,
        ]);

        return (string) ($resp['restrictedDataToken'] ?? '');
    }
}
