<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Remetente de uma mensagem. SYSTEM e ROBOT reusam a identidade da SHOP. */
final class MessageSender implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $imUserId = null,
        public readonly ?string $avatar = null,
        /** BUYER | SHOP | CUSTOMER_SERVICE | SYSTEM | ROBOT */
        public readonly ?string $role = null,
        public readonly ?string $nickname = null,
    ) {}
}
