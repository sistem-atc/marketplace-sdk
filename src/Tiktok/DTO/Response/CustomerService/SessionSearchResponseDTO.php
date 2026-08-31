<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de POST /customer_service/{v}/sessions/search.
 *
 * A API só devolve sessões dos últimos 90 dias — backfill mais antigo que isso
 * não existe.
 */
final class SessionSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nextPageToken = null,
        #[ArrayOf(Session::class)]
        public readonly ?array $sessions = null,
    ) {}
}
