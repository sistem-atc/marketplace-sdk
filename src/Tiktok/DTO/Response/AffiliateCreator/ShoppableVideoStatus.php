<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Status de publicacao do video: SUCCESS | FAIL | PROCESSING.
 * `postTime` (epoch em SEGUNDOS) so' vem quando post_status = SUCCESS.
 */
final class ShoppableVideoStatus implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $postStatus = null,
        public readonly ?int $postTime = null,
    ) {}
}
