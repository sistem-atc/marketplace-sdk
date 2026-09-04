<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio PointOfSale.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class PointOfSaleQueries extends BaseOperations
{
    /**
     * Returns a `CashDrawer` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function cashDrawer(array $args = [], string $selection = 'id name'): array
    {
        return $this->execute('query', 'cashDrawer', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * A list of cash drawers in the shop.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function cashDrawers(array $args = [], string $selection = 'edges { node { id name } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'cashDrawers', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'query' => 'String'], $selection);
    }

    /**
     * Summary of cash management data for a location. Data is returned only if the location has a POS Pro subscription.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: locationId: ID!, startDate: Date!, endDate: Date!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function cashManagementLocationSummary(array $args = [], string $selection = 'sessionsClosed sessionsOpened'): array
    {
        return $this->execute('query', 'cashManagementLocationSummary', $args, ['locationId' => 'ID!', 'startDate' => 'Date!', 'endDate' => 'Date!'], $selection);
    }

    /**
     * Returns the cash management reason codes for the shop.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function cashManagementReasonCodes(array $args = [], string $selection = 'edges { node { __typename } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'cashManagementReasonCodes', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Summary of cash management data across all locations with a POS Pro subscription for a shop, filtered by currency.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: currencyCode: CurrencyCode!, startDate: Date!, endDate: Date!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function cashManagementShopSummary(array $args = [], string $selection = 'sessionsClosed sessionsOpened'): array
    {
        return $this->execute('query', 'cashManagementShopSummary', $args, ['currencyCode' => 'CurrencyCode!', 'startDate' => 'Date!', 'endDate' => 'Date!'], $selection);
    }

    /**
     * Returns a `CashTrackingSession` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function cashTrackingSession(array $args = [], string $selection = 'cashTrackingEnabled closingNote closingTime id openingNote openingTime registerName'): array
    {
        return $this->execute('query', 'cashTrackingSession', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns a shop's cash tracking sessions for locations with a POS Pro subscription. Tip: To query for cash tracking sessions in bulk, you can [perform a bulk operation](https://shopify.dev/docs/api/us
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: CashTrackingSessionsSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function cashTrackingSessions(array $args = [], string $selection = 'edges { node { cashTrackingEnabled closingNote closingTime id openingNote openingTime registerName } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'cashTrackingSessions', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'CashTrackingSessionsSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * Returns a `PointOfSaleDevice` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function pointOfSaleDevice(array $args = [], string $selection = 'id'): array
    {
        return $this->execute('query', 'pointOfSaleDevice', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Lookup a point of sale device payment session by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function pointOfSaleDevicePaymentSession(array $args = [], string $selection = 'closingNote closingTime currency id openingNote openingTime status totalsReady'): array
    {
        return $this->execute('query', 'pointOfSaleDevicePaymentSession', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * A list of point of sale device payment sessions in the shop. Cash management data is returned only for locations with a POS Pro subscription.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: PointOfSaleDevicePaymentSessionSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function pointOfSaleDevicePaymentSessions(array $args = [], string $selection = 'edges { node { closingNote closingTime currency id openingNote openingTime status totalsReady } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'pointOfSaleDevicePaymentSessions', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'PointOfSaleDevicePaymentSessionSortKeys', 'query' => 'String'], $selection);
    }
}
