<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Contato do parceiro exibido pros vendedores da campanha.
 *
 * Qual canal é obrigatório muda por país: WhatsApp em MY/SG/GB/ID, Zalo em VN,
 * Viber em PH, LINE em TH. No Brasil só e-mail/telefone se aplicam.
 */
final class ContactInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $whatsapp = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?string $zalo = null,
        public readonly ?string $viber = null,
        public readonly ?string $line = null,
    ) {}
}
