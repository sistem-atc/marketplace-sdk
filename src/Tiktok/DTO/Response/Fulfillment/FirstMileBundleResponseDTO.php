<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resultado da criacao de first-mile bundle (202407 /bundles e
 * 202510 /first_mile_bundle).
 *
 * `url` e' o link do conhecimento de transporte (waybill) — e' assinado e
 * temporario, baixe na hora em vez de guardar a URL.
 *
 * @property list<FulfillmentError>|null $errors
 */
final class FirstMileBundleResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $firstMileBundleId = null,
        public readonly ?string $url = null,
        #[ArrayOf(FulfillmentError::class)]
        public readonly ?array $errors = null,
    ) {}
}
