<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de
 * `POST /affiliate_seller/202508/target_collaborations/{id}`.
 *
 * Mesma armadilha do create: `code=0` com `updateFailed` preenchido significa
 * que parte do update caiu no chao. Sempre inspecione `updateFailed`.
 *
 * BAIXAR comissao so' vale a partir de 00:00 do dia seguinte se algum creator
 * ja pos o produto na vitrine; SUBIR vale na hora.
 */
final class TargetCollaborationUpdateResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(TargetCollaborationConflict::class)]
        public readonly ?array $targetCollaborationConflicts = null,
        public readonly ?TargetCollaborationUpdateFailure $updateFailed = null,
    ) {}
}
