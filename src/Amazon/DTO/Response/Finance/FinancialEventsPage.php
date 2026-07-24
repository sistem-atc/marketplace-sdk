<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Finance;

use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/**
 * Uma PÁGINA de `GET /finances/v0/financialEvents` (por período) — os eventos
 * financeiros do intervalo + o `NextToken` opaco pra paginação.
 *
 * Diferente do por-pedido (`listOrderFinancialEvents`), aqui a Amazon devolve
 * eventos de VÁRIOS pedidos postados no período (PostedAfter/PostedBefore) numa
 * única chamada, paginados por NextToken. É o caminho eficiente pra backfill
 * histórico: ~centenas de eventos por chamada em vez de 1 chamada por pedido.
 *
 * `financialEvents` reaproveita o mesmo DTO tipado do por-pedido — o consumidor
 * lê as event lists exatamente igual.
 */
final class FinancialEventsPage implements DTOInterface, UsesPascalCaseKeys
{
    use CastToArray;

    public function __construct(
        public readonly FinancialEvents $financialEvents,
        public readonly ?string $nextToken = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data  o `payload` (FinancialEvents + NextToken)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            financialEvents: FinancialEvents::fromArray(
                is_array($data['FinancialEvents'] ?? null) ? $data['FinancialEvents'] : []
            ),
            nextToken: isset($data['NextToken']) ? (string) $data['NextToken'] : null,
        );
    }
}
