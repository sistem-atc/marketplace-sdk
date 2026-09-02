<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Customers;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\City;

/**
 * Endereco salvo no customer (GET /v1/customers/{id}). Mais rico que o Address comum:
 * cidade/estado/pais vem como objeto {id, name}.
 */
final class CustomerAddress implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Unique identifier of the address. */
        public readonly ?string $id = null,

        /** Postal/ZIP code of the address. */
        public readonly ?string $zipCode = null,

        /** Name of the street. */
        public readonly ?string $streetName = null,

        /** House or building number on the street. */
        public readonly ?string $streetNumber = null,

        /** Neighborhood or district name. */
        public readonly string|array|null $neighborhood = null,

        /** State or province name. */
        public readonly string|array|null $state = null,

        /** Additional address details (e.g. apartment, suite, floor). */
        public readonly ?string $complement = null,

        /** Floor number within a building. */
        public readonly ?string $floor = null,

        /** Apartment or unit identifier within a floor. */
        public readonly ?string $apartment = null,

        /** City information associated with this address. */
        public readonly ?City $city = null,

        /** (campo presente na resposta real, ausente no SDK oficial) */
        public readonly ?array $country = null,

        /** (campo presente na resposta real, ausente no SDK oficial) */
        public readonly ?string $dateCreated = null,

        /** (campo presente na resposta real, ausente no SDK oficial) */
        public readonly ?array $municipality = null,

        /** (campo presente na resposta real, ausente no SDK oficial) */
        public readonly ?bool $normalized = null,

        /** (campo presente na resposta real, ausente no SDK oficial) */
        public readonly ?string $phone = null,

        /** (campo presente na resposta real, ausente no SDK oficial) */
        public readonly ?array $verifications = null,
    ) {}
}
