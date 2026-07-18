<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Notification;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesCamelCaseKeys;

/**
 * Destination de notificação (SQS/EventBridge). `resource` é o objeto aninhado
 * com o ARN/config do canal — passthrough (shape varia por tipo de canal).
 */
final class Destination implements DTOInterface, UsesCamelCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $destinationId = null,
        public readonly ?string $name = null,
        public readonly mixed $resource = null,
    ) {}
}
