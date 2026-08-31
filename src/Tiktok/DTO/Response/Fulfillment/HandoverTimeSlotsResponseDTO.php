<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resultado de GET /fulfillment/202309/packages/{id}/handover_time_slots.
 *
 * `pickupSlots` pode vir preenchido com janelas `avaliable = false` — a
 * lista e' de horarios EXISTENTES, nao de horarios livres. Filtre.
 *
 * @property list<HandoverPickupSlot>|null $pickupSlots
 */
final class HandoverTimeSlotsResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?bool $canPickup = null,
        public readonly ?bool $canDropOff = null,
        /** Especifico do UK. */
        public readonly ?bool $canVanCollection = null,
        public readonly ?string $dropOffPointUrl = null,
        #[ArrayOf(HandoverPickupSlot::class)]
        public readonly ?array $pickupSlots = null,
    ) {}
}
