<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do webhook type 22 — FBT merchant onboarding.
 *
 * Sem efeito fiscal direto: e' cadastro. Vale como gatilho operacional —
 * regiao nova habilitada significa armazem novo e, portanto, NF-e de remessa
 * com destinatario/UF novos a configurar antes do primeiro envio.
 *
 * `onboarded_regions` vem SEMPRE completa (todas as regioes ja' habilitadas),
 * nao so' a que acabou de entrar.
 *
 * @property list<FbtOnboardedRegion>|null $onboardedRegions
 */
final class FbtMerchantOnboardingWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(FbtOnboardedRegion::class)]
        public readonly ?array $onboardedRegions = null,
        /** Epoch em SEGUNDOS da ultima atualizacao. */
        public readonly ?int $updateTime = null,
    ) {}
}
