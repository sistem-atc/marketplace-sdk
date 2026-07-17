<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * SKU do catálogo (`results[]` do listSkus). NÃO traz preço (esse vem no
 * priceBySku).
 *
 * Traz muito que o app hoje não usa mas a API entrega — `identifiers[]`
 * (GTIN/EAN fiscal), `ncm`, `dimensions[]`, `attributes[]`, `group` de variação.
 *
 * @property list<NameValue>|null $attributes
 * @property list<NameValue>|null $extraData
 * @property list<Identifier>|null $identifiers
 * @property list<PortfolioImage>|null $images
 * @property list<Dimension>|null $dimensions
 * @property list<Channel>|null $channels
 * @property list<UrlMarketplace>|null $urlMarketplace
 * @property array<int, mixed>|null $category
 * @property array<int, mixed>|null $datasheet
 * @property array<int, mixed>|null $videos
 * @property array<int, mixed>|null $podcasts
 */
final class PortfolioSku implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $sku = null,
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly ?string $brand = null,
        public readonly ?string $type = null,
        public readonly ?string $condition = null,
        public readonly ?string $origin = null,
        public readonly ?string $ncm = null,
        public readonly ?string $taxReplacement = null,
        public readonly ?string $status = null,
        public readonly ?bool $active = null,
        public readonly ?bool $fulfillment = null,
        public readonly ?bool $hasEan = null,
        public readonly ?bool $perishable = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
        public readonly ?string $creator = null,
        public readonly ?string $updater = null,
        public readonly ?Group $group = null,
        #[ArrayOf(NameValue::class)]
        public readonly ?array $attributes = null,
        #[ArrayOf(NameValue::class)]
        public readonly ?array $extraData = null,
        #[ArrayOf(Identifier::class)]
        public readonly ?array $identifiers = null,
        #[ArrayOf(PortfolioImage::class)]
        public readonly ?array $images = null,
        #[ArrayOf(Dimension::class)]
        public readonly ?array $dimensions = null,
        #[ArrayOf(Channel::class)]
        public readonly ?array $channels = null,
        #[ArrayOf(UrlMarketplace::class)]
        public readonly ?array $urlMarketplace = null,
        // Listas de shape livre (vazias no corpus) — passthrough.
        public readonly ?array $category = null,
        public readonly ?array $datasheet = null,
        public readonly ?array $videos = null,
        public readonly ?array $podcasts = null,
    ) {}
}
