<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio PointOfSale.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class PointOfSaleMutations extends BaseOperations
{
    /**
     * Creates a cash drawer in a provided location.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: locationId: ID!, name: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function cashDrawerCreate(array $args = [], string $selection = 'cashDrawer { id name } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'cashDrawerCreate', $args, ['locationId' => 'ID!', 'name' => 'String!'], $selection);
    }

    /**
     * Finds or creates a cash drawer for cash management. Also ensures the provided device is assigned to the drawer.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: locationId: ID!, name: String!, pointOfSaleDeviceId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function cashDrawerFindOrCreate(array $args = [], string $selection = 'cashDrawer { id name } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'cashDrawerFindOrCreate', $args, ['locationId' => 'ID!', 'name' => 'String!', 'pointOfSaleDeviceId' => 'ID!'], $selection);
    }

    /**
     * Updates a cash drawer.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, input: CashDrawerUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function cashDrawerUpdate(array $args = [], string $selection = 'cashDrawer { id name } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'cashDrawerUpdate', $args, ['id' => 'ID!', 'input' => 'CashDrawerUpdateInput!'], $selection);
    }

    /**
     * Create a cash management reason code.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: code: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function cashManagementReasonCodeCreate(array $args = [], string $selection = 'reasonCode { code id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'cashManagementReasonCodeCreate', $args, ['code' => 'String!'], $selection);
    }

    /**
     * Deletes a cash management reason code.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function cashManagementReasonCodeDelete(array $args = [], string $selection = 'deletedId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'cashManagementReasonCodeDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Assigns a point of sale device to a cash drawer, removing any prior assignment.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: cashDrawerId: ID!, pointOfSaleDeviceId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function pointOfSaleDeviceAssignToCashDrawer(array $args = [], string $selection = 'pointOfSaleDevice { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'pointOfSaleDeviceAssignToCashDrawer', $args, ['cashDrawerId' => 'ID!', 'pointOfSaleDeviceId' => 'ID!'], $selection);
    }

    /**
     * Adds an adjustment to a point of sale device payment session.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: pointOfSaleDevicePaymentSessionId: ID!, cash: MoneyInput!, staffMemberId: ID!, reasonCodeId: ID, note: String, time: DateTime
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function pointOfSaleDevicePaymentSessionAdjust(array $args = [], string $selection = 'pointOfSaleDevicePaymentSession { closingNote closingTime currency id openingNote openingTime status totalsReady } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'pointOfSaleDevicePaymentSessionAdjust', $args, ['pointOfSaleDevicePaymentSessionId' => 'ID!', 'cash' => 'MoneyInput!', 'staffMemberId' => 'ID!', 'reasonCodeId' => 'ID', 'note' => 'String', 'time' => 'DateTime'], $selection);
    }

    /**
     * Closes a point of sale device payment session.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: pointOfSaleDevicePaymentSessionId: ID!, balance: MoneyInput!, staffMemberId: ID!, time: DateTime, reasonCodeId: ID, note: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function pointOfSaleDevicePaymentSessionClose(array $args = [], string $selection = 'pointOfSaleDevicePaymentSession { closingNote closingTime currency id openingNote openingTime status totalsReady } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'pointOfSaleDevicePaymentSessionClose', $args, ['pointOfSaleDevicePaymentSessionId' => 'ID!', 'balance' => 'MoneyInput!', 'staffMemberId' => 'ID!', 'time' => 'DateTime', 'reasonCodeId' => 'ID', 'note' => 'String'], $selection);
    }

    /**
     * Records a mid-session cash count for a point of sale device payment session.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: pointOfSaleDevicePaymentSessionId: ID!, balance: MoneyInput!, staffMemberId: ID!, time: DateTime, reasonCodeId: ID, note: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function pointOfSaleDevicePaymentSessionCount(array $args = [], string $selection = 'pointOfSaleDevicePaymentSession { closingNote closingTime currency id openingNote openingTime status totalsReady } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'pointOfSaleDevicePaymentSessionCount', $args, ['pointOfSaleDevicePaymentSessionId' => 'ID!', 'balance' => 'MoneyInput!', 'staffMemberId' => 'ID!', 'time' => 'DateTime', 'reasonCodeId' => 'ID', 'note' => 'String'], $selection);
    }

    /**
     * Opens a point of sale device payment session.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: pointOfSaleDeviceId: ID!, balance: MoneyInput, staffMemberId: ID!, time: DateTime, reasonCodeId: ID, note: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function pointOfSaleDevicePaymentSessionOpen(array $args = [], string $selection = 'pointOfSaleDevicePaymentSession { closingNote closingTime currency id openingNote openingTime status totalsReady } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'pointOfSaleDevicePaymentSessionOpen', $args, ['pointOfSaleDeviceId' => 'ID!', 'balance' => 'MoneyInput', 'staffMemberId' => 'ID!', 'time' => 'DateTime', 'reasonCodeId' => 'ID', 'note' => 'String'], $selection);
    }
}
