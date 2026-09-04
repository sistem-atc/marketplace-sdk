<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Translation.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class TranslationMutations extends BaseOperations
{
    /**
     * Creates or updates translations for a resource's [translatable content](https://shopify.dev/docs/api/admin-graphql/latest/objects/TranslatableContent). Each translation requires a digest value from t
     *
     * @param array<string,mixed> $args Variaveis GraphQL: resourceId: ID!, translations: [TranslationInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function translationsRegister(array $args = [], string $selection = 'translations { key locale outdated updatedAt value } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'translationsRegister', $args, ['resourceId' => 'ID!', 'translations' => '[TranslationInput!]!'], $selection);
    }

    /**
     * Deletes translations.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: resourceId: ID!, translationKeys: [String!]!, locales: [String!]!, marketIds: [ID!]
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function translationsRemove(array $args = [], string $selection = 'translations { key locale outdated updatedAt value } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'translationsRemove', $args, ['resourceId' => 'ID!', 'translationKeys' => '[String!]!', 'locales' => '[String!]!', 'marketIds' => '[ID!]'], $selection);
    }
}
