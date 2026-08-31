<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Miolo de `data` do topico 7 — Upcoming authorization expiration.
 *
 * O aviso PREVENTIVO que o topico 6 nao da': dispara 30 DIAS antes de a
 * autorizacao da loja expirar sozinha, e REPETE todo dia as 00:00 ate' o
 * lojista reautorizar. Ou seja: da' 30 avisos antes de a integracao cair.
 *
 * ⚠️ `expiration_time` vem como STRING no exemplo oficial ("1627587506"),
 * apesar de ser epoch em segundos — tipado string pra roundtrip lossless.
 * Converta com (int) na hora de comparar com now(). Nao confunda com o
 * `timestamp` do envelope, que e' a hora do DISPARO (hoje), nao a do
 * vencimento.
 */
final class UpcomingAuthorizationExpirationWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Texto livre com o shop_id e os dias restantes interpolados. */
        public readonly ?string $message = null,
        /** Epoch em SEGUNDOS do vencimento — vem como STRING no exemplo oficial. */
        public readonly ?string $expirationTime = null,
    ) {}
}
