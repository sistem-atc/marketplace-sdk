<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resultado de POST /fulfillment/202309/packages/ship (lote).
 *
 * Nao existe lista de sucesso: o que NAO aparecer em `errors` foi enviado.
 *
 * @property list<FulfillmentError>|null $errors
 */
final class BatchShipPackagesResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(FulfillmentError::class)]
        public readonly ?array $errors = null,
    ) {}
}
