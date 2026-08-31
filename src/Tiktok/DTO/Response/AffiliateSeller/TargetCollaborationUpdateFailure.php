<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * O que o update NAO conseguiu mudar, campo a campo.
 *
 * `addProducts` e `changeCommissions` sao declarados `object` (singular) pela
 * doc e vem como objeto unico no exemplo, apesar da descricao falar em lista —
 * mapeados como objeto pra casar com o que a API manda de fato.
 */
final class TargetCollaborationUpdateFailure implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?array $removeCreatorOpenIds = null,
        public readonly ?array $removeProductIds = null,
        public readonly ?array $addCreatorOpenIds = null,
        public readonly ?TargetCollaborationProductEntry $addProducts = null,
        public readonly ?TargetCollaborationCommissionChange $changeCommissions = null,
        public readonly ?int $endTime = null,
        public readonly ?SellerContactInfo $sellerContactInfo = null,
        public readonly ?string $name = null,
        public readonly ?array $invalidProductIdList = null,
        public readonly ?array $invalidOpenIdList = null,
    ) {}
}
