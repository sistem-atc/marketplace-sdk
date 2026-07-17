<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Message;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** `message_date` — os marcos da mensagem (criada/recebida/lida/notificada). */
final class MessageDate implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $created = null,
        public readonly ?string $received = null,
        public readonly ?string $available = null,
        public readonly ?string $notified = null,
        public readonly ?string $read = null,
    ) {}
}
