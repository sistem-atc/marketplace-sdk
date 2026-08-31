<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Colaboracao aberta (comissao publica, qualquer creator pode promover).
 *
 * `status` TERMINATING = ja removida, expirando; as removidas E expiradas
 * somem da busca.
 */
final class OpenCollaboration implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        // NORMAL | TERMINATING
        public readonly ?string $status = null,
        public readonly ?OpenCollaborationCommission $currentCommission = null,
        // creators que puseram o produto na vitrine
        public readonly ?int $showcaseCreatorCount = null,
        // creators que postaram video/LIVE
        public readonly ?int $contentCreatorCount = null,
        public readonly ?OpenCollaborationProduct $product = null,
    ) {}
}
