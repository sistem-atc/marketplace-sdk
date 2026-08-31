<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `/order/{v}/orders/external_orders` (gravar a NOSSA referencia
 * dentro do pedido do TikTok).
 *
 * PESO ESTRATEGICO: gravando a chave da NF-e como referencia externa, o
 * vinculo nota <-> pedido deixa de ser inferido por valor/data e passa a ser
 * uma chave que o proprio canal guarda e devolve (via
 * `searchOrderByExternalOrderReference`). E' a saida definitiva pro caso de
 * nota duplicada, onde a heuristica de valor erra na maioria das vezes.
 *
 * ARMADILHA CENTRAL: o envelope volta `code: 0` / "Success" MESMO quando
 * entradas do lote falharam — as falhas vem em `errors[]`, e o
 * `makeRequest()` nao lanca excecao nenhuma. Um lote de 100 pode ter gravado
 * 3. Quem chama TEM que inspecionar `errors`; ignorar essa lista e' gravar
 * silenciosamente pela metade.
 *
 * `errors` VAZIO/ausente = lote inteiro gravado.
 *
 * @property list<ExternalOrderReferenceError>|null $errors
 */
final class AddExternalOrderReferencesResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(ExternalOrderReferenceError::class)]
        public readonly ?array $errors = null,
    ) {}
}
