<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `im_dsat_chat_items[]` — atendimento mal avaliado.
 * `customerName` vem MASCARADO pelo TikTok ("J*** D***"); nao ha' como desmascarar
 * por este endpoint. `ratingReasons` e' []string na doc mas string no exemplo
 * oficial -> `mixed` pra nao descartar.
 */
final class SpsImDsatChatItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $chatDurationHours = null,
        public readonly ?string $chatRecordId = null,
        public readonly ?string $customerName = null,
        public readonly ?int $customerRating = null,
        public readonly ?int $firstReplyTime = null,
        public mixed $ratingReasons = null,
        public readonly ?string $serviceAgent = null,
    ) {}
}
