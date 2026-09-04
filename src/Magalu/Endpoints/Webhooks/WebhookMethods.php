<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Webhooks;

use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class WebhookMethods extends BaseMethods
{
    public function signup(string $webhookUrl, ?string $topicId = null, array $filterBy = []): array
    {
        return $this->signupVersion('v1', $webhookUrl, $topicId, $filterBy);
    }

    public function signupV0(string $webhookUrl, ?string $topicId = null, array $filterBy = []): array
    {
        return $this->signupVersion('v0', $webhookUrl, $topicId, $filterBy);
    }

    private function signupVersion(string $version, string $webhookUrl, ?string $topicId, array $filterBy): array
    {
        $body = ['webhook' => $webhookUrl];
        if ($topicId !== null && $topicId !== '') $body['topic_id'] = $topicId;
        if (!empty($filterBy)) $body['filter_by'] = $filterBy;

        return $this->makeRequest(HttpMethod::PUT, "/{$version}/onboarding/signup", [], $body);
    }

    public function consultar(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/v1/onboarding/signup');
    }

    /**
     * Inscreve no historico (catchup de eventos antigos).
     * PUT /v1/onboarding/signup/history
     */
    public function signupHistory(string $webhookUrl, ?string $topicId = null, array $filterBy = []): array
    {
        $body = ['webhook' => $webhookUrl];
        if ($topicId !== null && $topicId !== '') $body['topic_id'] = $topicId;
        if (!empty($filterBy)) $body['filter_by'] = $filterBy;

        return $this->makeRequest(HttpMethod::PUT, '/v1/onboarding/signup/history', [], $body);
    }

    public function signoff(string $signupId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/v1/onboarding/signup/{$signupId}");
    }


    // ------------------------------------------------------------------
    // Onboarding v0 (legado, ainda documentado) + historico de fila + traces.
    // ------------------------------------------------------------------

    /**
     * Lista as inscricoes v0 (GET /v0/onboarding/signup). Paginacao `_limit`/`_offset`.
     *
     * @return array<string, mixed>
     */
    public function listV0(int $limit = 50, int $offset = 0): array
    {
        return $this->makeRequest(HttpMethod::GET, '/v0/onboarding/signup', ['_limit' => $limit, '_offset' => $offset]);
    }

    /**
     * Inscricao no historico v0 (PUT /v0/onboarding/signup/history) — sem `webhook` no body.
     *
     * @param array<string, mixed> $filterBy
     * @return array<string, mixed>
     */
    public function signupHistoryV0(?string $topicId = null, array $filterBy = []): array
    {
        $body = [];
        if ($topicId !== null && $topicId !== '') $body['topic_id'] = $topicId;
        if ($filterBy) $body['filter_by'] = $filterBy;

        return $this->makeRequest(HttpMethod::PUT, '/v0/onboarding/signup/history', [], $body);
    }

    /**
     * Cancela inscricao v0 (DELETE /v0/onboarding/signup/{subscription_id}).
     *
     * @return array<string, mixed>
     */
    public function signoffV0(string $subscriptionId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/v0/onboarding/signup/{$subscriptionId}");
    }

    /**
     * Historico de notificacoes entregues (GET /v0/queues/history).
     * Filtros: `type` (topico), `id`, `request_id`, `created_at`; `_sort`
     * (default created_at:asc), `_fields`.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function queuesHistory(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        return $this->makeRequest(HttpMethod::GET, '/v0/queues/history', array_merge($filters, [
            '_limit' => $limit,
            '_offset' => $offset,
        ]));
    }

    /**
     * Traces das operacoes assincronas (GET /v0/traces) — e' aqui que cai o
     * resultado dos 202 + trace_id de SKU/preco/estoque.
     * Filtros: `code`, `severity`, `origin.context`, `origin.resource`,
     * `app_trace_id`; `_sort` default created_at:asc; `_fields`.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function listTraces(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        return $this->makeRequest(HttpMethod::GET, '/v0/traces', array_merge($filters, [
            '_limit' => $limit,
            '_offset' => $offset,
        ]));
    }

    /**
     * Um trace (GET /v0/traces/{id}).
     *
     * @return array<string, mixed>
     */
    public function getTrace(string $traceId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/v0/traces/{$traceId}");
    }
}
