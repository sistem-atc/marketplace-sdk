<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\OAuth;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * OAuth resource. Represents an OAuth token response from the MercadoPago authorization server.
 * Contains the access token, refresh token, and associated metadata needed to authenticate API
 * requests on behalf of a seller (marketplace/platform integrations).
 */
final class OAuthResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Access token. */
        public readonly ?string $accessToken = null,

        /** Token type. */
        public readonly ?string $tokenType = null,

        /** Expires in. */
        public readonly ?int $expiresIn = null,

        /** Scope. */
        public readonly ?string $scope = null,

        /** User ID. */
        public readonly ?int $userId = null,

        /** Refresh token. */
        public readonly ?string $refreshToken = null,

        /** Public key. */
        public readonly ?string $publicKey = null,

        /** Live mode. */
        public readonly ?bool $liveMode = null,
    ) {}
}
