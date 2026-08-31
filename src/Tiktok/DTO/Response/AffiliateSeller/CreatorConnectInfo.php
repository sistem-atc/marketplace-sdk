<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Estado da conexao entre a loja e o creator (pareado/conectado/bloqueado).
 */
final class CreatorConnectInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?bool $isPaired = null,
        public readonly ?bool $isConnect = null,
        public readonly ?bool $isBlocked = null,
    ) {}
}
