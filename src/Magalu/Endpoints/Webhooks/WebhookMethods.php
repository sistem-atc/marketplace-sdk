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
}
