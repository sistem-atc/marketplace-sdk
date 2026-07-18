<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Notification;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesCamelCaseKeys;

/**
 * Subscription de um tipo de notificação (Notifications API v1). Amarra o
 * `notificationType` ao `destinationId` (SQS/EventBridge). Vazia (todos null)
 * quando o tipo ainda não está assinado.
 */
final class Subscription implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $subscriptionId = null,
        public readonly ?string $destinationId = null,
        public readonly ?string $payloadVersion = null,
        public readonly ?string $processingDirective = null,
    ) {}
}
