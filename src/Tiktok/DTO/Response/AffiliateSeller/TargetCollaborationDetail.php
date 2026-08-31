<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Convite dirigido detalhado: produtos E creators nas duas listas.
 *
 * Aqui o campo e' `creatorInvitedCount` (grafia CERTA) — a busca usa a errada.
 * As contagens so' somam quem esta em estado NORMAL.
 */
final class TargetCollaborationDetail implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $message = null,
        public readonly ?SellerContactInfo $sellerContactInfo = null,
        public readonly ?int $startTime = null,
        public readonly ?int $endTime = null,
        public readonly ?int $updateTime = null,
        // grafia correta; a busca devolve "creator_inivited_count"
        public readonly ?int $creatorInvitedCount = null,
        public readonly ?int $showcaseCreatorCount = null,
        public readonly ?int $contentCreatorCount = null,
        public readonly ?int $productCount = null,
        public readonly ?FreeSampleRule $freeSampleRule = null,
        #[ArrayOf(TargetCollaborationProduct::class)]
        public readonly ?array $products = null,
        #[ArrayOf(TargetCollaborationCreator::class)]
        public readonly ?array $creators = null,
        // STANDARD | TOP_CREATOR_PROGRAM
        public readonly ?string $type = null,
    ) {}
}
