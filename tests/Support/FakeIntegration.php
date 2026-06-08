<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tests\Support;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;

/**
 * Test double do contract MarketplaceIntegration — substitui o Eloquent
 * Integration do host nos testes do pacote. Mantem estado em memoria.
 *
 * `isExpired()` e `refresh()` existem porque os TokenRefreshers da lib os
 * detectam via method_exists() (o host real e' Eloquent). refresh() aplica
 * um token "rotacionado externamente" pra simular a corrida entre workers.
 */
class FakeIntegration implements MarketplaceIntegration
{
    public ?string $accessToken;

    public ?string $refreshToken;

    public array $settings;

    public bool $active;

    public bool $expired;

    /** Token que outro worker rotacionou na "DB" — aplicado no refresh(). */
    private ?string $externallyRotatedToken = null;

    public int $updateTokensCalls = 0;

    public ?int $lastExpiresIn = null;

    public function __construct(
        public int|string $id = 1,
        ?string $accessToken = 'ACCESS',
        ?string $refreshToken = 'REFRESH',
        array $settings = [],
        bool $active = true,
        bool $expired = false,
        public int|string $companyId = 1,
    ) {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->settings = $settings;
        $this->active = $active;
        $this->expired = $expired;
    }

    public function getIntegrationIdentifier(): int|string
    {
        return $this->id;
    }

    public function getCompanyIdentifier(): int|string
    {
        return $this->companyId;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getMarketplaceSettings(): array
    {
        return $this->settings;
    }

    public function isIntegrationActive(): bool
    {
        return $this->active;
    }

    public function updateTokens(string $accessToken, ?string $refreshToken = null, ?int $expiresIn = null): void
    {
        $this->accessToken = $accessToken;
        if ($refreshToken !== null) {
            $this->refreshToken = $refreshToken;
        }
        $this->lastExpiresIn = $expiresIn;
        $this->expired = false;
        $this->updateTokensCalls++;
    }

    public function isExpired(): bool
    {
        return $this->expired;
    }

    /** Simula reload do Eloquent: aplica o token rotacionado por outro worker. */
    public function refresh(): void
    {
        if ($this->externallyRotatedToken !== null) {
            $this->accessToken = $this->externallyRotatedToken;
            $this->expired = false;
            $this->externallyRotatedToken = null;
        }
    }

    /** Helper de teste: simula outro worker rotacionando o token na DB. */
    public function simulateExternalRotation(string $token): void
    {
        $this->externallyRotatedToken = $token;
    }
}
