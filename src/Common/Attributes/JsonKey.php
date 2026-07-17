<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Common\Attributes;

use Attribute;

/**
 * Declara a chave EXATA que a API usa pra este campo, quando a conversao
 * automatica camelCase <-> snake_case nao acerta.
 *
 * Existe porque nenhuma regra automatica serve pra todos os MPs — o caso que
 * provou isso (2026-07-17) foi o digito colado:
 *
 *   Shopee : shipping_fee_discount_from_3pl  (COM  '_' antes do digito)
 *   TikTok : address_line1                   (SEM  '_' antes do digito)
 *
 * Os dois sao "minuscula seguida de digito". Uma regra que conserta um QUEBRA
 * o outro — e o campo some em silencio no roundtrip. Entao aqui o contrato e'
 * explicito, nao adivinhado.
 *
 * Use SO quando a conversao automatica falha; o padrao continua sendo ela.
 *
 * Ex.: #[JsonKey('shipping_fee_discount_from_3pl')]
 *      public readonly ?float $shippingFeeDiscountFrom3pl = null,
 */
#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final class JsonKey
{
    public function __construct(
        public string $key,
    ) {}
}
