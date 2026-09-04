<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\TopPicks;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;

/**
 * Modulo `/api/v2/top_picks` — colecoes "Destaques da loja" (top picks).
 *
 * Uma loja pode ter varias colecoes mas so UMA ativa: `is_activated=true`
 * numa colecao desativa as outras. Cada colecao tem de 4 a 8 itens.
 *
 * Todos os metodos devolvem o bloco `response` cru (array).
 */
class TopPicksMethods extends BaseMethods
{
    /**
     * Todas as colecoes da loja com seus itens —
     * /api/v2/top_picks/get_top_picks_list. Sem parametros.
     *
     * @return array<string,mixed>
     */
    public function getTopPicksList(): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/top_picks/get_top_picks_list');

        return $response['response'] ?? [];
    }

    /**
     * Cria uma colecao — /api/v2/top_picks/add_top_picks (name, item_id_list
     * de 4 a 8 itens, is_activated). Devolve a colecao criada em `collection_list`.
     *
     * @param  list<int>  $itemIdList
     * @return array<string,mixed>
     */
    public function addTopPicks(string $name, array $itemIdList, bool $isActivated = false): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/top_picks/add_top_picks', [], [
            'name' => $name,
            'item_id_list' => array_values($itemIdList),
            'is_activated' => $isActivated,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Atualiza uma colecao — /api/v2/top_picks/update_top_picks. So envia os
     * campos informados; `item_id_list` SUBSTITUI a lista inteira.
     *
     * @param  list<int>|null  $itemIdList
     * @return array<string,mixed>
     */
    public function updateTopPicks(
        int $topPicksId,
        ?string $name = null,
        ?array $itemIdList = null,
        ?bool $isActivated = null,
    ): array {
        $body = array_filter([
            'top_picks_id' => $topPicksId,
            'name' => $name,
            'item_id_list' => $itemIdList !== null ? array_values($itemIdList) : null,
            'is_activated' => $isActivated,
        ], static fn ($v) => $v !== null);

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/top_picks/update_top_picks', [], $body);

        return $response['response'] ?? [];
    }

    /**
     * Apaga uma colecao — /api/v2/top_picks/delete_top_picks.
     *
     * @return array<string,mixed>
     */
    public function deleteTopPicks(int $topPicksId): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/top_picks/delete_top_picks', [], [
            'top_picks_id' => $topPicksId,
        ]);

        return $response['response'] ?? [];
    }
}
