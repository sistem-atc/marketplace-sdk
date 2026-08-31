<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Criador promovendo um produto da campanha.
 *
 * Dois desvios de unidade no MESMO objeto: `commission` é centésimo de % (100
 * = 1,00%) e os `effective_*_time` são STRING com epoch em MILISSEGUNDOS —
 * o restante da API TikTok usa int em segundos.
 */
final class PromotionCreator implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?Money $paidAmount = null,
        /** Lives em que o produto apareceu. */
        public readonly ?int $roomCount = null,
        public readonly ?int $videoCount = null,
        /** NOT_REQUESTED | PENDING | AWAITING_SHIPMENT | AWAITING_COLLECTION | SHIPPED | CONTENT_PENDING | … */
        public readonly ?string $freeSampleStatus = null,
        /** Centésimos de %. */
        public readonly ?string $commission = null,
        /** STRING, epoch em MILISSEGUNDOS. */
        public readonly ?string $effectiveEndTime = null,
        public readonly ?string $effectiveStartTime = null,
        public readonly ?Creator $creator = null,
        /** Chave pra pedir as estatísticas de conteúdo deste par criador×produto. */
        public readonly ?string $affiliateProductId = null,
    ) {}
}
