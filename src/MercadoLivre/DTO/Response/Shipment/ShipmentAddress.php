<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Shipment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Shared\Common\NamedRef;

/**
 * Endereço do envio — serve pra `receiver_address` E `sender_address` (mesma
 * shape; os campos de receiver ficam null no sender).
 *
 * @property array<int|string, mixed>|null $types
 */
final class ShipmentAddress implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int|string, mixed>|null $types */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $addressLine = null,
        public readonly ?string $streetName = null,
        public readonly ?string $streetNumber = null,
        public readonly ?string $zipCode = null,
        public readonly ?string $comment = null,
        public readonly ?string $intersection = null,
        public readonly ?NamedRef $city = null,
        public readonly ?NamedRef $state = null,
        public readonly ?NamedRef $country = null,
        public readonly ?NamedRef $neighborhood = null,
        public readonly ?NamedRef $municipality = null,
        public readonly ?float $latitude = null,
        public readonly ?float $longitude = null,
        public readonly ?string $receiverName = null,
        public readonly ?string $receiverPhone = null,
        public readonly ?string $deliveryPreference = null,
        public readonly ?string $geolocationType = null,
        public readonly ?string $geolocationSource = null,
        public readonly ?string $geolocationLastUpdated = null,
        public readonly ?string $locationId = null,
        public readonly mixed $agency = null,
        public readonly mixed $node = null,
        public readonly ?array $types = null,
        public readonly mixed $scoring = null,
        public readonly mixed $version = null,
    ) {}
}
