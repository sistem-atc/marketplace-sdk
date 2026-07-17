<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\User;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * RAIZ de GET /users/me e GET /users/{id} (UserMethods::me / get).
 *
 * O uso dominante é só `->id` (o seller_id que quase todo endpoint pede), mas a
 * árvore inteira é tipada. Blocos de reputação/status/credit ficam crus (shape
 * volátil e volumosa). `toArray()` é LOSSLESS.
 *
 * @property array<int|string, mixed> $tags
 */
final class UserResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int|string, mixed> $tags */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $nickname = null,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
        public readonly ?string $email = null,
        public readonly ?string $secureEmail = null,
        public readonly ?string $gender = null,
        public readonly ?string $countryId = null,
        public readonly ?string $siteId = null,
        public readonly ?string $userType = null,
        public readonly ?string $permalink = null,
        public readonly ?string $registrationDate = null,
        public readonly ?int $points = null,
        public readonly ?UserIdentification $identification = null,
        public readonly ?UserAddress $address = null,
        public readonly ?UserPhone $phone = null,
        public readonly ?UserPhone $alternativePhone = null,
        public readonly ?UserCompany $company = null,
        public readonly array $tags = [],
        // blocos de shape volátil — preservados crus
        public readonly mixed $status = null,
        public readonly mixed $sellerReputation = null,
        public readonly mixed $buyerReputation = null,
        public readonly mixed $sellerExperience = null,
        public readonly mixed $billData = null,
        public readonly mixed $credit = null,
        public readonly mixed $context = null,
        public readonly mixed $thumbnail = null,
        public readonly mixed $logo = null,
        public readonly mixed $registrationIdentifiers = null,
    ) {}
}
