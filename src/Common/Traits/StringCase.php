<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Common\Traits;

/**
 * Helpers de caixa (camel/snake) compartilhados pelos traits de DTO.
 * Extraido pra evitar colisao quando um DTO usa AutoHydrate + CastToArray.
 */
trait StringCase
{
    /**
     * camelCase -> snake_case. Quebra APENAS em maiuscula.
     *
     * NAO tente ser esperto com digito: `addressLine1` (TikTok) e
     * `shippingFeeDiscountFrom3pl` (Shopee) sao ambos minuscula-seguida-de-
     * digito, mas as APIs mandam `address_line1` e `..._from_3pl`. Qualquer
     * regra automatica acerta um e QUEBRA o outro em silencio. Pra esses
     * casos existe o atributo #[JsonKey('chave_exata')].
     */
    private static function camelToSnake(string $name): string
    {
        return strtolower((string) preg_replace('/[A-Z]/', '_$0', $name));
    }
}
