<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Campanha/promoção TikTok. Serve a listagem (getActivities) E o detalhe
 * (getActivity).
 *
 * GOTCHA DO ID: o identificador tem NOME DIFERENTE nas duas respostas —
 * `id` na LISTAGEM, `activity_id` no DETALHE. Os dois são mapeados; use o
 * helper `activityId()` que retorna o que estiver preenchido. (Ler `activity_id`
 * cru na listagem dá null — foi o bug que deixou tiktok_promotions_raw vazia.)
 *
 * `products[]` só vem no detalhe. Datas = epoch em SEGUNDOS.
 *
 * @property list<ActivityProduct>|null $products
 */
final class Activity implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // Listagem usa `id`; detalhe usa `activity_id`. Ambos mapeados.
        public readonly ?string $id = null,
        public readonly ?string $activityId = null,
        public readonly ?string $title = null,
        public readonly ?string $status = null,
        public readonly ?string $activityType = null,
        public readonly ?string $durationType = null,
        public readonly ?string $productLevel = null,
        public readonly ?int $beginTime = null,
        public readonly ?int $endTime = null,
        public readonly ?int $createTime = null,
        public readonly ?int $updateTime = null,
        #[ArrayOf(ActivityProduct::class)]
        public readonly ?array $products = null,
    ) {}

    /** O id, venha ele como `id` (listagem) ou `activity_id` (detalhe). */
    public function activityId(): ?string
    {
        return $this->id ?? $this->activityId;
    }
}
