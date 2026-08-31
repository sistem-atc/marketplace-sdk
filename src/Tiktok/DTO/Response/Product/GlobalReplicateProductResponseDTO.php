<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `/product/202507/products/{product_id}/global_replicate`.
 *
 * ARMADILHA: e' operacao em LOTE (um mercado por item de `replicate_target`) e
 * a resposta volta com `code: 0` mesmo quando NENHUM mercado foi replicado —
 * o BaseMethods nao lanca excecao. O unico sinal de falha e' `errors[]`, um
 * item por mercado que nao entrou. Sempre inspecione antes de dar por feito.
 *
 * A resposta NAO traz os ids das replicas criadas; para isso, chame
 * `getGlobalReplicatedProducts()` depois.
 *
 * @property list<GlobalReplicateError>|null $errors
 */
final class GlobalReplicateProductResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(GlobalReplicateError::class)]
        public readonly ?array $errors = null,
    ) {}
}
