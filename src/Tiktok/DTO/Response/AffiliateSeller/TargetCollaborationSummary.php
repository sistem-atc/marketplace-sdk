<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Attributes\JsonKey;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Convite dirigido na listagem de busca.
 *
 * ATENCAO ao `creatorInivitedCount`: o erro de digitacao ("inivited") esta na
 * API, nao aqui. O detalhe (`getTargetCollaboration`) devolve o mesmo numero
 * com o nome CERTO (`creator_invited_count`) — as duas grafias sao reais.
 */
final class TargetCollaborationSummary implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        // recado do seller ao creator
        public readonly ?string $message = null,
        public readonly ?int $startTime = null,
        public readonly ?int $endTime = null,
        public readonly ?int $createTime = null,
        public readonly ?int $updateTime = null,
        // typo da API: "inivited"
        #[JsonKey('creator_inivited_count')]
        public readonly ?int $creatorInivitedCount = null,
        public readonly ?int $showcaseCreatorCount = null,
        public readonly ?int $contentCreatorCount = null,
        public readonly ?int $productCount = null,
        public readonly ?FreeSampleRule $freeSampleRule = null,
        // STANDARD | TOP_CREATOR_PROGRAM
        public readonly ?string $type = null,
    ) {}
}
