<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conteudo de `data` do webhook TYPE 16 — Product creation.
 *
 * `updateTime` aqui e' a hora da CRIACAO (a doc reusa o nome do campo).
 */
final class ProductCreationWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<string>|null $productTypes */
    public function __construct(
        // Snowflake: chega como numero no exemplo oficial, guardado como string.
        public readonly ?string $productId = null,
        // COMBINED_PRODUCT (bundle virtual) | GPR_PRODUCT (criado pela ferramenta GPR)
        public readonly ?array $productTypes = null,
        public readonly ?int $updateTime = null,
    ) {}
}
