<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de `POST /affiliate_seller/202412/conversatons/read`.
 *
 * O path tem ERRO DE DIGITACAO NA API ("conversatons", sem o segundo `i`) —
 * corrigir quebra a chamada.
 *
 * Sucesso parcial: `failedConversationIds` lista as que nao foram marcadas;
 * a doc recomenda repetir. A lista e' mantida CRUA porque o exemplo oficial
 * devolve numero (`[123]`) onde o tipo declarado e' `[]string`.
 */
final class MarkConversationReadResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // reenviar; a API mistura int e string aqui
        public readonly ?array $failedConversationIds = null,
    ) {}
}
