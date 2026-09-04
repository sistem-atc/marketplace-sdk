<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Conversation;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;

/**
 * Perguntas & Respostas do anuncio (/v0/questions) — base `services.magalu.com`.
 *
 * Perguntas pre-venda feitas na pagina do produto. A resposta responde 202 e
 * passa por moderacao; o status final vem em getQuestion(). Paginacao
 * `_limit` (default 10)/`_offset`.
 */
class QuestionMethods extends BaseMethods
{
    /**
     * Perguntas (GET /v0/questions).
     *
     * Filtros: `status` (approved|rejected_response|pending...),
     * `answer_external_id`, `answer_owner_external_id`.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function list(array $filters = [], int $limit = 10, int $offset = 0): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->servicesUrl('/v0/questions'), array_merge($filters, [
            '_limit' => $limit,
            '_offset' => $offset,
        ]));
    }

    /**
     * Uma pergunta com resposta e moderacao (GET /v0/questions/{id}).
     *
     * @return array<string, mixed>
     */
    public function get(string $questionId): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->servicesUrl("/v0/questions/{$questionId}"));
    }

    /**
     * Responde a pergunta (POST /v0/questions/{id}/answer) — 202.
     *
     * @return array<string, mixed>
     */
    public function answer(
        string $questionId,
        string $message,
        string $ownerExternalId,
        string $ownerName,
        ?string $externalId = null,
    ): array {
        $body = ['message' => $message, 'owner' => ['external_id' => $ownerExternalId, 'name' => $ownerName]];
        if ($externalId !== null) $body['external_id'] = $externalId;

        return $this->makeRequest(HttpMethod::POST, $this->servicesUrl("/v0/questions/{$questionId}/answer"), [], $body);
    }
}
