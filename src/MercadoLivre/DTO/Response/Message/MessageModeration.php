<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Message;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** `message_moderation` — moderação do ML (bloqueio automático de contato etc). */
final class MessageModeration implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $status = null,
        public readonly ?string $reason = null,
        public readonly ?string $source = null,
        public readonly ?bool $isAutomatic = null,
        public readonly ?string $moderationDate = null,
    ) {}
}
