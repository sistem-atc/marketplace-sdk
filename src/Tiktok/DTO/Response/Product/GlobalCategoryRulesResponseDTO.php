<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `/product/202309/categories/{category_id}/global_rules`.
 *
 * Diz o que a categoria EXIGE antes de publicar: certificacoes, tabela de
 * medidas e os dois papeis do GPSR europeu (responsavel e fabricante).
 *
 * Diferente do Get Category Rules local, aqui cada regra vem quebrada por
 * mercado — a mesma categoria pode exigir certificado no GB e nao exigir no US.
 *
 * @property list<GlobalProductCertificationRule>|null $productCertifications
 */
final class GlobalCategoryRulesResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(GlobalProductCertificationRule::class)]
        public readonly ?array $productCertifications = null,
        public readonly ?GlobalSizeChartRule $sizeChart = null,
        public readonly ?GlobalRegionRequirementRule $responsiblePerson = null,
        public readonly ?GlobalRegionRequirementRule $manufacturer = null,
    ) {}
}
