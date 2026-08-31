<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Contato do seller exposto ao creator no convite dirigido.
 *
 * PII do PROPRIO seller (email/telefone/WhatsApp/Telegram/Line) — nao e' dado
 * de comprador, mas tambem nao deveria ir pra log.
 */
final class SellerContactInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $email = null,
        public readonly ?string $phoneNumber = null,
        public readonly ?string $whatsapp = null,
        public readonly ?string $telegram = null,
        public readonly ?string $line = null,
    ) {}
}
