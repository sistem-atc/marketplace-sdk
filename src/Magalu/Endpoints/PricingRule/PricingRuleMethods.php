<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\PricingRule;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;

/**
 * Regras de precificacao dinamica (/seller/v1/pricing-rules) — o canal
 * reprecifica automaticamente pra ganhar (TO_WIN) ou empatar (TO_DRAW) o
 * buy box, respeitando `min_price` (centavos).
 *
 * `config_type`: SKU (regra por SKU) ou CATALOG (todo o catalogo no canal).
 * `adjustment`: {type: PERCENTAGE|FIXED_VALUE, value, normalize} — PERCENTAGE
 * 500/100 = 5%. Escrita e' assincrona (202 + trace_id).
 */
class PricingRuleMethods extends BaseMethods
{
    /**
     * Regra ativa do canal, opcionalmente de um SKU (GET /seller/v1/pricing-rules/active).
     *
     * @return array<string, mixed>
     */
    public function active(string $channelId, ?string $sku = null): array
    {
        $query = ['channel_id' => $channelId];
        if ($sku !== null) $query['sku'] = $sku;

        return $this->makeRequest(HttpMethod::GET, '/seller/v1/pricing-rules/active', $query);
    }

    /**
     * Cria regra (POST /seller/v1/pricing-rules).
     *
     * @param array<string, mixed>|null $adjustment {type, value, normalize}
     * @return array<string, mixed>
     */
    public function create(
        string $channelId,
        string $configType,
        string $ruleType,
        int $minPrice,
        ?string $sku = null,
        ?array $adjustment = null,
    ): array {
        $body = [
            'channel' => ['id' => $channelId],
            'config_type' => $configType,
            'rule_type' => $ruleType,
            'min_price' => $minPrice,
        ];
        if ($sku !== null) $body['sku'] = $sku;
        if ($adjustment !== null) $body['adjustment'] = $adjustment;

        return $this->makeRequest(HttpMethod::POST, '/seller/v1/pricing-rules', [], $body);
    }

    /**
     * Atualiza regra (PATCH /seller/v1/pricing-rules). Body: `channel.id`,
     * `min_price` (obrigatorios), `config_type`, `sku`, `adjustment`.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function update(array $data): array
    {
        return $this->makeRequest(HttpMethod::PATCH, '/seller/v1/pricing-rules', [], $data);
    }

    /**
     * Remove a regra de CATALOGO do canal
     * (DELETE /seller/v1/pricing-rules/catalog/channel_id/{channel_id}).
     *
     * @return array<string, mixed>
     */
    public function deleteCatalogRule(string $channelId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/seller/v1/pricing-rules/catalog/channel_id/{$channelId}");
    }

    /**
     * Remove a regra de um SKU
     * (DELETE /seller/v1/pricing-rules/channel_id/{channel_id}/sku/{sku}).
     *
     * @return array<string, mixed>
     */
    public function deleteSkuRule(string $channelId, string $sku): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/seller/v1/pricing-rules/channel_id/{$channelId}/sku/".rawurlencode($sku));
    }
}
