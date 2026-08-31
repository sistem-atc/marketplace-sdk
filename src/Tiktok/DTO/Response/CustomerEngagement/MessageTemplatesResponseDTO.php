<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerEngagement;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de GET /customer_engagement/{v}/message_templates.
 *
 * O texto vem PRONTO no `locale` pedido e não é editável — quem quiser texto
 * próprio usa Create Custom Engagement Task (que exige a feature CUSTOM_MSG).
 */
final class MessageTemplatesResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(MessageTemplate::class)]
        public readonly ?array $messageTemplates = null,
    ) {}
}
