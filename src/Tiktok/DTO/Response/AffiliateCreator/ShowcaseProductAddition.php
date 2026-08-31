<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Imagens customizadas que o creator subiu pro produto na vitrine.
 *
 * @property list<ShowcaseImage>|null $customizedMainImages
 */
final class ShowcaseProductAddition implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(ShowcaseImage::class)]
        public readonly ?array $customizedMainImages = null,
    ) {}
}
