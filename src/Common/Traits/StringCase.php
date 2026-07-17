<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Common\Traits;

/**
 * Helpers de caixa (camel/snake) compartilhados pelos traits de DTO.
 * Extraido pra evitar colisao quando um DTO usa AutoHydrate + CastToArray.
 */
trait StringCase
{
    private static function camelToSnake(string $name): string
    {
        // Duas fronteiras: (1) maiuscula, (2) minuscula->digito.
        //
        // A (2) existe porque a Shopee manda `shipping_fee_discount_from_3pl`:
        // so' com a regra de maiuscula, `shippingFeeDiscountFrom3pl` virava
        // `..._from3pl` e o campo era DROPADO em silencio (achado no roundtrip
        // contra escrow real).
        //
        // Nao afeta digito depois de MAIUSCULA (`stockInfoV2` -> `stock_info_v2`,
        // `isB2cOwnedItem` -> `is_b2c_owned_item`), que e' o padrao das APIs
        // pra versao/sigla.
        return strtolower((string) preg_replace(['/(?<=[a-z])(?=[0-9])/', '/[A-Z]/'], ['_', '_$0'], $name));
    }
}
