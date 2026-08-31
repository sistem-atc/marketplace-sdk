<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Resposta de GET /customer_service/{v}/agents/settings. */
final class AgentSettingsResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /**
         * false = o atendente só recebe conversa atribuída MANUALMENTE. Quem
         * consome a IM API por integração quer isso true, senão as conversas
         * novas nunca chegam.
         */
        public readonly ?bool $canAcceptChat = null,
    ) {}
}
