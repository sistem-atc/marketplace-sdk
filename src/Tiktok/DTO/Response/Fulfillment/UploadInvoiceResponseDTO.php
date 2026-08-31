<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resultado de POST /fulfillment/202502/invoice/upload.
 *
 * SUCESSO PARCIAL: `data.errors` lista o que NAO subiu. Envelope com
 * `code: 0` e `errors` preenchido significa que parte das notas falhou —
 * conferir SEMPRE antes de marcar a nota como enviada ao canal.
 *
 * @property list<FulfillmentError>|null $errors
 */
final class UploadInvoiceResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(FulfillmentError::class)]
        public readonly ?array $errors = null,
    ) {}
}
