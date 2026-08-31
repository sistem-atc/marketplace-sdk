<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerEngagement;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de POST /customer_engagement/{v}/messages.
 *
 * ARMADILHA: sucesso PARCIAL vem com `code: 0` no envelope e a lista de falhas
 * aqui dentro. Quem só olhar o HTTP/`code` vai achar que os 500 e-mails foram
 * entregues — os que falharam estão em `errors[]`, um por destinatário.
 */
final class SendEngagementMessageResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(EngagementMessageError::class)]
        public readonly ?array $errors = null,
    ) {}
}
