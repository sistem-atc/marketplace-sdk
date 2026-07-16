<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Bloco `billing_info` — dados fiscais do comprador (doc + endereço), que o ML
 * entrega como lista chave-valor em `additional_info`.
 *
 * Use {@see self::additional()} pra ler um campo sem varrer a lista:
 *   $b->additional('FIRST_NAME')  ·  $b->additional('ZIP_CODE')
 *
 * @property list<BillingInfoAdditional> $additionalInfo
 */
final class BillingInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<BillingInfoAdditional> $additionalInfo */
    public function __construct(
        public readonly ?string $docType = null,
        public readonly ?string $docNumber = null,
        #[ArrayOf(BillingInfoAdditional::class)]
        public readonly array $additionalInfo = [],
    ) {}

    /**
     * Valor de um `additional_info` pelo type (ex: FIRST_NAME, DOC_NUMBER,
     * ZIP_CODE, STATE_CODE). Null quando o ML não mandou aquele campo.
     */
    public function additional(string $type): ?string
    {
        foreach ($this->additionalInfo as $item) {
            if ($item->type === $type) {
                return $item->value;
            }
        }

        return null;
    }
}
