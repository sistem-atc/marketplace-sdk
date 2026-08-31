<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Erro PARCIAL de adicao na vitrine: o batch responde 200 com a lista dos
 * que falharam. Resposta sem `errors` = tudo entrou.
 */
final class ShowcaseError implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $code = null,
        public readonly ?string $message = null,
        public readonly ?ShowcaseErrorDetail $detail = null,
    ) {}
}
