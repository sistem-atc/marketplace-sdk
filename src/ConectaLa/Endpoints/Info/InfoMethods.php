<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\ConectaLa\Endpoints\Info;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\ConectaLa\Bases\BaseMethods;

/** Dados da loja e do canal (Infos). Útil pra validar credenciais (smoke test). */
class InfoMethods extends BaseMethods
{
    /** Dados da loja (GET /Infos). */
    public function store(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/Infos');
    }

    /** Dados do canal / seller center (GET /Infos/sellercenter). */
    public function channel(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/Infos/sellercenter');
    }
}
