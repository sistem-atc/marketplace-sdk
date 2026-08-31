<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conteudo de `data` do webhook TYPE 62 — Product Package Recommended.
 *
 * O QUE E' DE VERDADE: o TikTok detectou que o peso e/ou as dimensoes
 * cadastradas do produto estao divergentes do que ele mede na operacao e manda
 * a FAIXA CORRIGIDA que ele sugere. `abnormalityTypes` diz qual eixo esta'
 * anormal ("Weight", "Dimension") e vem ["None"] quando o produto esta' normal.
 *
 * Nao e' "anomalia de produto" em sentido amplo (nada de preco, titulo ou
 * conteudo): e' EXCLUSIVAMENTE cubagem/peso de embalagem — o que afeta frete.
 * Aplicavel so' a BR e MX.
 *
 * O bloco correspondente ao eixo que NAO esta' anormal simplesmente nao vem.
 */
final class ProductPackageRecommendedWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<string>|null $abnormalityTypes */
    public function __construct(
        public readonly ?string $productId = null,
        // ["Weight"], ["Dimension"], os dois, ou ["None"] quando normal.
        public readonly ?array $abnormalityTypes = null,
        public readonly ?WeightRecommendation $weightRecommendation = null,
        public readonly ?DimensionRecommendation $dimensionRecommendation = null,
    ) {}
}
