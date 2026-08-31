<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `POST /fbt/202602/list_available_inbound_method`.
 *
 * Passo 2 do fluxo de inbound: criar/atualizar o plano -> LISTAR metodos ->
 * detalhar o metodo -> confirmar -> despachar.
 *
 * @property list<FbtAvailableInboundMethod>|null $availableInboundMethodList
 */
final class FbtAvailableInboundMethodsResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(FbtAvailableInboundMethod::class)]
        public readonly ?array $availableInboundMethodList = null,
    ) {}
}
