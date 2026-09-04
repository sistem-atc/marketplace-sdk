<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\MobileApp;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;

/**
 * Mobile Platform Applications (`mobile_platform_applications`) — apps iOS/Android
 * associados a loja (universal links / app links).
 */
class MobilePlatformApplicationMethods extends BaseMethods
{
    /**
     * Lista as aplicacoes mobile.
     */
    public function list(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/mobile_platform_applications');
    }

    /**
     * Recupera uma aplicacao mobile.
     */
    public function get(int|string $applicationId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/mobile_platform_applications/{$applicationId}");
    }

    /**
     * Cria uma aplicacao mobile. Embrulha em `mobile_platform_application`.
     *
     * @param  array<string, mixed>  $application
     */
    public function create(array $application): array
    {
        return $this->makeRequest(HttpMethod::POST, '/mobile_platform_applications', [], ['mobile_platform_application' => $application]);
    }

    /**
     * Atualiza uma aplicacao mobile. Embrulha em `mobile_platform_application`.
     *
     * @param  array<string, mixed>  $application
     */
    public function update(int|string $applicationId, array $application): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/mobile_platform_applications/{$applicationId}", [], ['mobile_platform_application' => $application]);
    }

    /**
     * Exclui uma aplicacao mobile.
     */
    public function delete(int|string $applicationId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/mobile_platform_applications/{$applicationId}");
    }
}
