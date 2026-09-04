<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Market.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class MarketMutations extends BaseOperations
{
    /**
     * Update the backup region that is used when we have no better signal of what region a buyer is in.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: region: BackupRegionUpdateInput
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function backupRegionUpdate(array $args = [], string $selection = 'backupRegion { id name } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'backupRegionUpdate', $args, ['region' => 'BackupRegionUpdateInput'], $selection);
    }

    /**
     * Creates a [`Market`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Market) to deliver customized shopping experiences. Markets define various aspects of the buyer experience including pric
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: MarketCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketCreate(array $args = [], string $selection = 'market { assignedCustomization enabled handle id name primary status type } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'marketCreate', $args, ['input' => 'MarketCreateInput!'], $selection);
    }

    /**
     * Updates currency settings of a market.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: marketId: ID!, input: MarketCurrencySettingsUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketCurrencySettingsUpdate(array $args = [], string $selection = 'market { assignedCustomization enabled handle id name primary status type } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'marketCurrencySettingsUpdate', $args, ['marketId' => 'ID!', 'input' => 'MarketCurrencySettingsUpdateInput!'], $selection);
    }

    /**
     * Deletes a market definition.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketDelete(array $args = [], string $selection = 'deletedId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'marketDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Creates or updates market localizations.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: resourceId: ID!, marketLocalizations: [MarketLocalizationRegisterInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketLocalizationsRegister(array $args = [], string $selection = 'marketLocalizations { key outdated updatedAt value } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'marketLocalizationsRegister', $args, ['resourceId' => 'ID!', 'marketLocalizations' => '[MarketLocalizationRegisterInput!]!'], $selection);
    }

    /**
     * Deletes market localizations.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: resourceId: ID!, marketLocalizationKeys: [String!]!, marketIds: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketLocalizationsRemove(array $args = [], string $selection = 'marketLocalizations { key outdated updatedAt value } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'marketLocalizationsRemove', $args, ['resourceId' => 'ID!', 'marketLocalizationKeys' => '[String!]!', 'marketIds' => '[ID!]!'], $selection);
    }

    /**
     * Deletes a market region.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketRegionDelete(array $args = [], string $selection = 'deletedId market { assignedCustomization enabled handle id name primary status type } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'marketRegionDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Creates regions that belong to an existing market.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: marketId: ID!, regions: [MarketRegionCreateInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketRegionsCreate(array $args = [], string $selection = 'market { assignedCustomization enabled handle id name primary status type } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'marketRegionsCreate', $args, ['marketId' => 'ID!', 'regions' => '[MarketRegionCreateInput!]!'], $selection);
    }

    /**
     * Deletes a list of market regions.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: ids: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketRegionsDelete(array $args = [], string $selection = 'deletedIds userErrors { field message }'): array
    {
        return $this->execute('mutation', 'marketRegionsDelete', $args, ['ids' => '[ID!]!'], $selection);
    }

    /**
     * Updates the properties of a market.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, input: MarketUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketUpdate(array $args = [], string $selection = 'market { assignedCustomization enabled handle id name primary status type } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'marketUpdate', $args, ['id' => 'ID!', 'input' => 'MarketUpdateInput!'], $selection);
    }

    /**
     * Creates a web presence for a market.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: marketId: ID!, webPresence: MarketWebPresenceCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketWebPresenceCreate(array $args = [], string $selection = 'market { assignedCustomization enabled handle id name primary status type } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'marketWebPresenceCreate', $args, ['marketId' => 'ID!', 'webPresence' => 'MarketWebPresenceCreateInput!'], $selection);
    }

    /**
     * Deletes a market web presence.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: webPresenceId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketWebPresenceDelete(array $args = [], string $selection = 'deletedId market { assignedCustomization enabled handle id name primary status type } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'marketWebPresenceDelete', $args, ['webPresenceId' => 'ID!'], $selection);
    }

    /**
     * Updates a market web presence.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: webPresenceId: ID!, webPresence: MarketWebPresenceUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketWebPresenceUpdate(array $args = [], string $selection = 'market { assignedCustomization enabled handle id name primary status type } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'marketWebPresenceUpdate', $args, ['webPresenceId' => 'ID!', 'webPresence' => 'MarketWebPresenceUpdateInput!'], $selection);
    }
}
