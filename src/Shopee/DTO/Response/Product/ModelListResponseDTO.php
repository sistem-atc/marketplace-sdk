<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `/api/v2/product/get_model_list`.
 *
 * Anúncio SEM variação responde 200 com `model` vazio — não é erro.
 *
 * @property list<Model>|null $model
 * @property list<TierVariation>|null $tierVariation
 * @property list<StandardVariation>|null $standardiseTierVariation
 */
final class ModelListResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(Model::class)]
        public readonly ?array $model = null,
        #[ArrayOf(TierVariation::class)]
        public readonly ?array $tierVariation = null,
        #[ArrayOf(StandardVariation::class)]
        public readonly ?array $standardiseTierVariation = null,
    ) {}
}
