<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Finance;

use SistemAtc\Marketplaces\Common\Attributes\JsonKey;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/**
 * Uma PÁGINA de `GET /finances/v0/financialEventGroups` — a lista de settlements
 * (repasses reais) do período + o `NextToken` pra paginação.
 *
 * Cada item é um FinancialEventGroup (um depósito da Amazon, com valor real e
 * status de transferência). Pra abrir o detalhe por-pedido de um settlement, use
 * `Finances::listFinancialEventsByGroupId($group->financialEventGroupId)`.
 */
final class FinancialEventGroupsPage implements DTOInterface, UsesPascalCaseKeys
{
    use CastToArray;

    /** @param list<FinancialEventGroup> $groups */
    public function __construct(
        #[JsonKey('FinancialEventGroupList')]
        public readonly array $groups = [],
        public readonly ?string $nextToken = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data  o `payload` (FinancialEventGroupList + NextToken)
     */
    public static function fromArray(array $data): self
    {
        $list = is_array($data['FinancialEventGroupList'] ?? null) ? $data['FinancialEventGroupList'] : [];

        return new self(
            groups: array_values(array_map(
                fn ($g) => FinancialEventGroup::fromArray(is_array($g) ? $g : []),
                $list
            )),
            nextToken: isset($data['NextToken']) ? (string) $data['NextToken'] : null,
        );
    }
}
