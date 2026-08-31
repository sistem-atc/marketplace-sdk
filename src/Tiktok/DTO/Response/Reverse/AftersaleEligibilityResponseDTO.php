<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `/return_refund/202602/orders/{order_id}/aftersale_eligibility`.
 *
 * E' a consulta que se faz ANTES de abrir cancelamento/devolucao: diz o que o
 * iniciador (SELLER ou BUYER — muda o resultado para o MESMO pedido) pode
 * pedir, e com quais motivos.
 *
 * @property list<SkuAftersaleEligibility>|null $skuEligibility
 */
final class AftersaleEligibilityResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(SkuAftersaleEligibility::class)]
        public readonly ?array $skuEligibility = null,
    ) {}
}
