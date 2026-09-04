<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Web.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class WebMutations extends BaseOperations
{
    /**
     * Activate a [web pixel extension](https://shopify.dev/docs/apps/build/marketing-analytics/build-web-pixels) by creating a web pixel record on the store where you installed your app. When you run the `
     *
     * @param array<string,mixed> $args Variaveis GraphQL: webPixel: WebPixelInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function webPixelCreate(array $args = [], string $selection = 'webPixel { id settings } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'webPixelCreate', $args, ['webPixel' => 'WebPixelInput!'], $selection);
    }

    /**
     * Deletes the web pixel shop settings.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function webPixelDelete(array $args = [], string $selection = 'deletedWebPixelId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'webPixelDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Activate a [web pixel extension](https://shopify.dev/docs/apps/build/marketing-analytics/build-web-pixels) by updating a web pixel record on the store where you installed your app. When you run the `
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, webPixel: WebPixelInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function webPixelUpdate(array $args = [], string $selection = 'webPixel { id settings } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'webPixelUpdate', $args, ['id' => 'ID!', 'webPixel' => 'WebPixelInput!'], $selection);
    }

    /**
     * Creates a web presence.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: WebPresenceCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function webPresenceCreate(array $args = [], string $selection = 'webPresence { id subfolderSuffix } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'webPresenceCreate', $args, ['input' => 'WebPresenceCreateInput!'], $selection);
    }

    /**
     * Deletes a web presence.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function webPresenceDelete(array $args = [], string $selection = 'deletedId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'webPresenceDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Updates a web presence.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, input: WebPresenceUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function webPresenceUpdate(array $args = [], string $selection = 'webPresence { id subfolderSuffix } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'webPresenceUpdate', $args, ['id' => 'ID!', 'input' => 'WebPresenceUpdateInput!'], $selection);
    }
}
