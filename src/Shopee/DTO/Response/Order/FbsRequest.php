<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Tarefa de geração de NFes FBS (`result_list[]`).
 *
 * `status` só vem no get_fbs_invoices_result (AVAILABLE | PROCESSING | ERROR);
 * o generate devolve só o `requestId`.
 */
final class FbsRequest implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $requestId = null,
        public readonly ?string $status = null,
    ) {}
}
