<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `generate_fbs_invoices` e `get_fbs_invoices_result`.
 *
 * ATENÇÃO AO ENVELOPE: estes endpoints FBS NÃO seguem a convenção da Shopee —
 * o `result_list` vem na RAIZ, não dentro de `response`. Foi por isso que o
 * app carregava `$resp['result_list'] ?? $resp['response']['result_list']`.
 *
 * @property list<FbsRequest>|null $resultList
 */
final class FbsRequestListResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(FbsRequest::class)]
        public readonly ?array $resultList = null,
    ) {}
}
