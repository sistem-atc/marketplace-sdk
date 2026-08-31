<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de `POST /affiliate_seller/202508/target_collaborations`.
 *
 * SUCESSO PARCIAL E' O NORMAL: mesmo com `code=0` o TikTok devolve o que NAO
 * entrou em `targetCollaborationConflicts`, `invalidOpenIdList` e
 * `invalidProductIdList`. Quem so' olha o codigo de retorno acha que convidou
 * 50 creators e convidou 12.
 */
final class TargetCollaborationCreateResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?TargetCollaborationRef $targetCollaboration = null,
        #[ArrayOf(TargetCollaborationConflict::class)]
        public readonly ?array $targetCollaborationConflicts = null,
        public readonly ?array $invalidOpenIdList = null,
        public readonly ?array $invalidProductIdList = null,
        public readonly ?int $createTime = null,
    ) {}
}
