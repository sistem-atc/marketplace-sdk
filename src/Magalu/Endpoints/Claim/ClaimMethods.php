<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Claim;

use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

/**
 * Reclamacoes/SAC do Magalu (pos-venda).
 *
 * ⚠️ PATH/SCOPE NAO CONFIRMADOS. Em 2026-07 o `/seller/v1/claims` retornou 404
 * ("Resource Not Found") — o app so' tem os scopes portfolio+order
 * (`open:order-*-seller:read`), sem scope de claims, e o gateway esconde recurso
 * fora do scope como 404. Antes de usar em producao: obter do Magalu (portal do
 * desenvolvedor / gerente de conta) o SCOPE OAuth (`open:{dominio}-...-seller:read`)
 * e o PATH corretos, corrigir aqui + no TokenRefresher (scope pedido), e ai'
 * ligar no Bunker via config marketplaces.magalu.claims_enabled.
 */
class ClaimMethods extends BaseMethods
{
    public function list(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/seller/v1/claims', $params);
    }

    public function get(string $claimId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/seller/v1/claims/{$claimId}");
    }

    public function reply(string $claimId, string $message): array
    {
        return $this->makeRequest(HttpMethod::POST, "/seller/v1/claims/{$claimId}/messages", [], [
            'message' => $message,
        ]);
    }
}
