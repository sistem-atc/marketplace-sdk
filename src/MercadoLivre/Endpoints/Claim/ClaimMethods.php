<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Claim;

use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class ClaimMethods extends BaseMethods
{
    /**
     * Busca detalhes de uma reclamacao.
     */
    public function get(int|string $claimId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/claims/{$claimId}");
    }

    /**
     * Pesquisa reclamacoes com filtros.
     */
    public function search(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/claims/search', $filters);
    }

    /**
     * Busca o historico de eventos de uma reclamacao.
     */
    public function history(int|string $claimId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/claims/{$claimId}/history");
    }

    /**
     * Busca as mensagens trocadas dentro da reclamacao.
     */
    public function messages(int|string $claimId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/claims/{$claimId}/messages");
    }

    /**
     * Envia uma mensagem na reclamacao.
     */
    public function sendMessage(int|string $claimId, int|string $senderId, string $message, string $role = 'seller'): array
    {
        return $this->makeRequest(HttpMethod::POST, "/claims/{$claimId}/messages", [], [
            'sender_id' => $senderId,
            'role' => $role,
            'message' => $message
        ]);
    }
}
