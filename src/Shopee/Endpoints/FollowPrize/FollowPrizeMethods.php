<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\FollowPrize;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;

/**
 * Modulo `/api/v2/follow_prize` — cupom de "siga a loja".
 *
 * reward_type: 1 = valor fixo (discount_amount), 2 = percentual (percentage +
 * max_price), 3 = coin cashback (percentage + max_price). Nome max 20 chars;
 * end_time >= start_time + 1 dia; usage_quantity 1..200000.
 * O identificador e' `campaign_id`.
 *
 * Todos os metodos devolvem o bloco `response` cru (array).
 */
class FollowPrizeMethods extends BaseMethods
{
    /**
     * Lista follow prizes — /api/v2/follow_prize/get_follow_prize_list.
     * `status`: upcoming/ongoing/expired/all. Paginacao page_no 1-based +
     * flag `more`; page_size default 20 (max 100).
     *
     * @return array<string,mixed>
     */
    public function getFollowPrizeList(string $status = 'all', int $pageNo = 1, int $pageSize = 20): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/follow_prize/get_follow_prize_list', [
            'status' => $status,
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Detalhe de um follow prize — /api/v2/follow_prize/get_follow_prize_detail.
     *
     * @return array<string,mixed>
     */
    public function getFollowPrizeDetail(int $campaignId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/follow_prize/get_follow_prize_detail', [
            'campaign_id' => $campaignId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Cria um follow prize — /api/v2/follow_prize/add_follow_prize.
     * Obrigatorios: follow_prize_name, start_time, end_time, usage_quantity,
     * min_spend, reward_type. Conforme o reward_type: discount_amount (1) ou
     * percentage + max_price (2/3). Devolve `campain_id` (sic, grafia da Shopee).
     *
     * @param  array<string,mixed>  $payload
     * @return array<string,mixed>
     */
    public function addFollowPrize(array $payload): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/follow_prize/add_follow_prize', [], $payload);

        return $response['response'] ?? [];
    }

    /**
     * Atualiza um follow prize — /api/v2/follow_prize/update_follow_prize.
     * `$fields`: follow_prize_name, start_time, end_time, usage_quantity,
     * min_spend.
     *
     * @param  array<string,mixed>  $fields
     * @return array<string,mixed>
     */
    public function updateFollowPrize(int $campaignId, array $fields): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/follow_prize/update_follow_prize', [], array_merge(
            ['campaign_id' => $campaignId],
            $fields,
        ));

        return $response['response'] ?? [];
    }

    /**
     * Apaga um follow prize (so upcoming) — /api/v2/follow_prize/delete_follow_prize.
     *
     * @return array<string,mixed>
     */
    public function deleteFollowPrize(int $campaignId): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/follow_prize/delete_follow_prize', [], [
            'campaign_id' => $campaignId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Encerra um follow prize em andamento — /api/v2/follow_prize/end_follow_prize.
     *
     * @return array<string,mixed>
     */
    public function endFollowPrize(int $campaignId): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/follow_prize/end_follow_prize', [], [
            'campaign_id' => $campaignId,
        ]);

        return $response['response'] ?? [];
    }
}
