<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Opcao de frete elegivel (`shipping_services[]` do Get Eligible Shipping Service).
 *
 * NAO e' o mesmo objeto do `ShippingServiceInfo` (frete JA escolhido no
 * pacote): so' a cotacao traz `is_default` e a quebra do preco em
 * `shipping_fee` + `shipping_app_service_fee` + `signature_fee`. Por isso vive
 * numa classe propria — fundir as duas obrigaria a mexer no DTO do pacote.
 *
 * Todo valor monetario e' STRING ("5", "3"), inclusive os que parecem inteiro:
 * tipar float comeria centavo e quebraria o roundtrip.
 *
 * `price` e' o TOTAL estimado; as tres taxas sao a decomposicao dele. Somar
 * price + shipping_fee conta o frete duas vezes.
 *
 * Prazo e provider sao marcados "US only" na doc — no BR chegam nulos.
 */
final class EligibleShippingService implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $price = null,
        public readonly ?string $currency = null,
        public readonly ?int $earliestDeliveryDays = null,
        public readonly ?int $latestDeliveryDays = null,
        public readonly ?bool $isDefault = null,
        public readonly ?string $shippingProviderName = null,
        public readonly ?string $shippingProviderId = null,
        public readonly ?string $shippingFee = null,
        /** Sobretaxa paga aos ERPs. */
        public readonly ?string $shippingAppServiceFee = null,
        public readonly ?string $signatureFee = null,
    ) {}
}
