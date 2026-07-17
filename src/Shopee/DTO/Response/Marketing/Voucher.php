<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Marketing;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Cupom do vendedor (`voucher_list[]`).
 *
 * `rewardType` diz qual campo vale: valor fixo usa `discountAmount`, e
 * percentual usa `percentage` (+ `maxPrice` como teto). Os dois nunca vêm
 * preenchidos juntos — ler o errado dá 0 em silêncio.
 *
 * `isAdmin` true = cupom criado pela SHOPEE, não pelo vendedor.
 *
 * Datas são epoch em SEGUNDOS.
 *
 * @property list<int>|null $displayChannelList
 * @property list<int>|null $itemIdList
 */
final class Voucher implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $voucherId = null,
        public readonly ?string $voucherCode = null,
        public readonly ?string $voucherName = null,
        public readonly ?int $voucherType = null,
        public readonly ?int $voucherPurpose = null,
        public readonly ?int $rewardType = null,
        public readonly ?int $targetVoucher = null,
        public readonly ?int $usecase = null,
        public readonly ?bool $isAdmin = null,
        // Valor: dinheiro é float (int truncaria centavos).
        public readonly ?float $discountAmount = null,
        public readonly ?int $percentage = null,
        public readonly ?float $maxPrice = null,
        public readonly ?float $minBasketPrice = null,
        // Uso
        public readonly ?int $usageQuantity = null,
        public readonly ?int $currentUsage = null,
        // Datas (epoch em SEGUNDOS)
        public readonly ?int $startTime = null,
        public readonly ?int $endTime = null,
        public readonly ?int $displayStartTime = null,
        // Listas de INTEIROS (ids/canais), não de objetos.
        public readonly ?array $displayChannelList = null,
        public readonly ?array $itemIdList = null,
    ) {}
}
