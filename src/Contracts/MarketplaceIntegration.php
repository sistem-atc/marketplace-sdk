<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Contracts;

interface MarketplaceIntegration
{
    public function getIntegrationIdentifier(): int|string;
    public function getCompanyIdentifier(): int|string;
    public function getAccessToken(): ?string;
    public function getRefreshToken(): ?string;
    public function getMarketplaceSettings(): array;
    public function isIntegrationActive(): bool;
    public function updateTokens(string $accessToken, ?string $refreshToken = null, ?int $expiresIn = null): void;
}
