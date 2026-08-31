<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Logistics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Motivo pelo qual um serviço do template não atende o produto.
 *
 * `filterType` é DECLARADO string na doc e vem como string ("9") no exemplo,
 * apesar de ser um código numérico:
 * 1=comprimento, 2=largura, 3=altura, 4=peso, 9=modo de serviço, 11=serviço.
 * Comparar com int (`=== 9`) falha em silêncio.
 */
final class TemplateFilterReason implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $filterType = null,
        public readonly ?string $reason = null,
    ) {}
}
