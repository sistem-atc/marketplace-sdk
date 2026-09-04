<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Newsletter;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\LojaIntegrada\Bases\BaseMethods;

/**
 * Newsletter v1 (`/v1/newsletter`). A newsletter do módulo Marketing (v3) fica em MarketingMethods.
 */
class NewsletterMethods extends BaseMethods
{
    /** Consulta se o e-mail está inscrito (`?email=`). @return array<string,mixed> */
    public function find(string $email): array
    {
        return $this->makeRequest(HttpMethod::GET, 'newsletter/', ['email' => $email]);
    }

    /** @return array<string,mixed> */
    public function subscribe(string $email): array
    {
        return $this->makeRequest(HttpMethod::POST, 'newsletter/', [], ['email' => $email]);
    }
}
