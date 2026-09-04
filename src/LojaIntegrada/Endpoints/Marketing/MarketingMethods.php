<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Marketing;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\LojaIntegrada\Bases\BaseMethods;

/**
 * Marketing v3 (`/v3/marketing`, fora do `/v1`): automações (carrinho abandonado etc.),
 * campanhas, regras, newsletter e lista de espera (avise-me). Paginação: campanhas usam
 * `limit/offset`; lista de espera usa `page/pageSize`.
 */
class MarketingMethods extends BaseMethods
{
    /** @return array<string,mixed> */
    public function listActiveAutomations(): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->rootUrl('v3/marketing/automations/active'));
    }

    /** @return array<string,mixed> */
    public function automationRules(int|string $automationId): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->rootUrl("v3/marketing/rules/channels/{$automationId}"));
    }

    /**
     * @param array<string,mixed> $filters clientName, productId, regionId, categoryId, valueMin, valueMax, onlyWithPhone
     * @return array<string,mixed>
     */
    public function listCampaigns(int|string $automationId, int $limit = 20, int $offset = 0, array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->rootUrl("v3/marketing/campaign/{$automationId}"), array_merge(['limit' => $limit, 'offset' => $offset], $filters));
    }

    /** @return array<string,mixed> */
    public function campaignDetails(int|string $campaignId): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->rootUrl("v3/marketing/campaign/details/{$campaignId}"));
    }

    /** @return array<string,mixed> */
    public function deleteCampaign(int|string $campaignId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, $this->rootUrl("v3/marketing/campaign/{$campaignId}"));
    }

    /**
     * @param array<string,mixed> $filters mesmos de listCampaigns()
     * @return array<string,mixed>
     */
    public function exportCampaigns(int|string $automationId, array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->rootUrl("v3/marketing/campaign/export/{$automationId}"), $filters);
    }

    /** @return array<string,mixed> */
    public function optOut(string $recipient, int|string $storeId, int|string $automationId): array
    {
        return $this->makeRequest(HttpMethod::POST, $this->rootUrl('v3/marketing/automations/optout'), [], [
            'recipient' => $recipient,
            'storeId' => $storeId,
            'automationId' => $automationId,
        ]);
    }

    /** @return array<string,mixed> */
    public function getAutomationConfig(): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->rootUrl('v3/marketing/automations/config/'));
    }

    /**
     * @param array<string,mixed> $data custom, startInterval, endInterval
     * @return array<string,mixed>
     */
    public function createAutomationConfig(array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, $this->rootUrl('v3/marketing/automations/config/'), [], $data);
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function updateAutomationConfig(array $data): array
    {
        return $this->makeRequest(HttpMethod::PUT, $this->rootUrl('v3/marketing/automations/config/'), [], $data);
    }

    /** @return array<string,mixed> */
    public function deleteAutomationConfig(): array
    {
        return $this->makeRequest(HttpMethod::DELETE, $this->rootUrl('v3/marketing/automations/config/'));
    }

    /**
     * Cria regras (body é uma LISTA).
     *
     * @param array<int,array<string,mixed>> $rules [{automationId, triggerDelay}, ...]
     * @return array<string,mixed>
     */
    public function createRules(array $rules): array
    {
        return $this->makeRequest(HttpMethod::POST, $this->rootUrl('v3/marketing/rules'), [], array_values($rules));
    }

    /** @return array<string,mixed> */
    public function deleteRule(int|string $ruleId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, $this->rootUrl("v3/marketing/rules/{$ruleId}"));
    }

    /**
     * @param array<string,bool> $toggles ex.: ['abandoned-cart' => true, 'cancelled-order' => false]
     * @return array<string,mixed>
     */
    public function toggleAutomations(array $toggles): array
    {
        return $this->makeRequest(HttpMethod::POST, $this->rootUrl('v3/marketing/rules/toggle'), [], $toggles);
    }

    /**
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    public function listNewsletter(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->rootUrl('v3/marketing/newsletter'), $filters);
    }

    /** @return array<string,mixed> */
    public function subscribeNewsletter(string $email, ?string $name = null): array
    {
        $body = ['email' => $email];
        if ($name !== null) {
            $body['name'] = $name;
        }

        return $this->makeRequest(HttpMethod::POST, $this->rootUrl('v3/marketing/newsletter'), [], $body);
    }

    /** @return array<string,mixed> */
    public function getNewsletter(int|string $newsletterId): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->rootUrl("v3/marketing/newsletter/{$newsletterId}"));
    }

    /** Remove pelo E-MAIL (path). @return array<string,mixed> */
    public function unsubscribeNewsletter(string $email): array
    {
        return $this->makeRequest(HttpMethod::DELETE, $this->rootUrl('v3/marketing/newsletter/'.rawurlencode($email)));
    }

    /**
     * Produtos com clientes na lista de espera (avise-me).
     *
     * @param array<string,mixed> $filters categoryId (1,2,3), marcaId, order, status, s, withVariation
     * @return array<string,mixed>
     */
    public function awaitingList(int $page = 1, int $pageSize = 10, array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->rootUrl('v3/marketing/awaiting/list'), array_merge(['page' => $page, 'pageSize' => $pageSize], $filters));
    }
}
