<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Produto da etiqueta de amostra, com os SKUs elegiveis.
 *
 * @property list<SampleSku>|null $sampleSkuList
 */
final class SampleLabelProduct implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(SampleSku::class)]
        public readonly ?array $sampleSkuList = null,
    ) {}
}
