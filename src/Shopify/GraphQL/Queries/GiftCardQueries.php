<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio GiftCard.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class GiftCardQueries extends BaseOperations
{
    /**
     * Retrieves a [`GiftCard`](https://shopify.dev/docs/api/admin-graphql/latest/objects/GiftCard) by its ID. Returns the gift card's balance, transaction history, [`Customer`](https://shopify.dev/docs/api/
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function giftCard(array $args = [], string $selection = 'createdAt crossCurrencyRedemptionStrategy deactivatedAt enabled expiresOn id isRedeemable lastCharacters maskedCode note templateSuffix updatedAt'): array
    {
        return $this->execute('query', 'giftCard', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * The configuration for the shop's gift cards.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function giftCardConfiguration(array $args = [], string $selection = '__typename'): array
    {
        return $this->execute('query', 'giftCardConfiguration', $args, [], $selection);
    }

    /**
     * Returns a paginated list of [`GiftCard`](https://shopify.dev/docs/api/admin-graphql/latest/objects/GiftCard) objects issued for the shop. You can filter gift cards by attributes such as status, last
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: GiftCardSortKeys, query: String, savedSearchId: ID
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function giftCards(array $args = [], string $selection = 'edges { node { createdAt crossCurrencyRedemptionStrategy deactivatedAt enabled expiresOn id isRedeemable lastCharacters maskedCode note templateSuffix updatedAt } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'giftCards', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'GiftCardSortKeys', 'query' => 'String', 'savedSearchId' => 'ID'], $selection);
    }

    /**
     * Returns the total count of gift cards that have been issued by the shop. Use this for dashboard summaries or to understand the scale of a merchant's gift card program. The count includes all gift card
     *
     * @param array<string,mixed> $args Variaveis GraphQL: query: String, savedSearchId: ID, limit: Int
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function giftCardsCount(array $args = [], string $selection = 'count precision'): array
    {
        return $this->execute('query', 'giftCardsCount', $args, ['query' => 'String', 'savedSearchId' => 'ID', 'limit' => 'Int'], $selection);
    }
}
