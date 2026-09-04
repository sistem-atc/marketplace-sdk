<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Event.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class EventMutations extends BaseOperations
{
    /**
     * Updates the server pixel to connect to an EventBridge endpoint. Running this mutation deletes any previous subscriptions for the server pixel.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: arn: ARN!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function eventBridgeServerPixelUpdate(array $args = [], string $selection = 'serverPixel { id status webhookEndpointAddress } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'eventBridgeServerPixelUpdate', $args, ['arn' => 'ARN!'], $selection);
    }
}
