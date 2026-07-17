<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Auditoria/moderação do produto (`audit`). `status` = estado da revisão
 * TikTok; `preApprovedReasons` justifica pré-aprovações.
 *
 * @property list<mixed>|null $preApprovedReasons
 */
final class Audit implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $status = null,
        public readonly ?array $preApprovedReasons = null,
    ) {}
}
