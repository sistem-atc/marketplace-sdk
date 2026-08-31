<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Anexo da certificacao SIOC (Ships In Own Container — produto que viaja na
 * propria caixa, sem embalagem extra do armazem).
 */
final class FbtSiocFile implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $siocCertificationUrl = null,
        // PHOTO | DOCUMENT | DROP_TEST_VIDEO
        public readonly ?string $siocFileType = null,
    ) {}
}
