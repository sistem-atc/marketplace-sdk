<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `/order/202606/pod_details/search` — dados de personalizacao
 * (print on demand) das linhas de um pedido.
 *
 * E' de onde sai o que o comprador escreveu/enviou pra gravar no produto:
 * sem esta chamada o pedido chega na expedicao sem a arte, porque o detalhe
 * do POD nao vem no `getOrderDetail`.
 *
 * `statusCode` e' um SEGUNDO codigo, DENTRO do `data`, separado do `code` do
 * envelope — a chamada pode voltar `code: 0` no envelope com `status_code`
 * diferente de 0 aqui. Confira os dois.
 *
 * @property list<PodDetail>|null $podDetails
 */
final class PodDetailsResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(PodDetail::class)]
        public readonly ?array $podDetails = null,
        /** Codigo de status interno ao `data`; 0 = ok. */
        public readonly ?int $statusCode = null,
    ) {}
}
