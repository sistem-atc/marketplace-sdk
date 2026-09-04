<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Shop.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class ShopMutations extends BaseOperations
{
    /**
     * Deletes a locale for a shop. This also deletes all translations of this locale.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: locale: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function shopLocaleDisable(array $args = [], string $selection = 'locale userErrors { field message }'): array
    {
        return $this->execute('mutation', 'shopLocaleDisable', $args, ['locale' => 'String!'], $selection);
    }

    /**
     * Adds a locale for a shop. The newly added locale is in the unpublished state.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: locale: String!, marketWebPresenceIds: [ID!]
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function shopLocaleEnable(array $args = [], string $selection = 'shopLocale { locale name primary published } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'shopLocaleEnable', $args, ['locale' => 'String!', 'marketWebPresenceIds' => '[ID!]'], $selection);
    }

    /**
     * Updates a locale for a shop.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: locale: String!, shopLocale: ShopLocaleInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function shopLocaleUpdate(array $args = [], string $selection = 'shopLocale { locale name primary published } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'shopLocaleUpdate', $args, ['locale' => 'String!', 'shopLocale' => 'ShopLocaleInput!'], $selection);
    }

    /**
     * Updates a shop policy.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: shopPolicy: ShopPolicyInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function shopPolicyUpdate(array $args = [], string $selection = 'shopPolicy { body createdAt id title type updatedAt url } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'shopPolicyUpdate', $args, ['shopPolicy' => 'ShopPolicyInput!'], $selection);
    }

    /**
     * The `ResourceFeedback` object lets your app report the status of shops and their resources. For example, if your app is a marketplace channel, then you can use resource feedback to alert merchants tha
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: ResourceFeedbackCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function shopResourceFeedbackCreate(array $args = [], string $selection = 'feedback { feedbackGeneratedAt state } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'shopResourceFeedbackCreate', $args, ['input' => 'ResourceFeedbackCreateInput!'], $selection);
    }
}
