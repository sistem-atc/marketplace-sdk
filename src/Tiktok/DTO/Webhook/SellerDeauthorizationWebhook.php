<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Miolo de `data` do topico 6 — Seller deauthorization.
 *
 * Dispara DEPOIS que o lojista tira o acesso do App. E' o unico aviso de que a
 * integracao daquela loja MORREU: dali em diante todo endpoint volta erro de
 * autorizacao. Sem tratar isso, o conector fica horas "sem pedido novo" sem
 * ninguem entender por que.
 *
 * ⚠️ O payload NAO traz codigo de motivo nem estrutura: `data` tem UM campo,
 * `message`, texto livre em ingles com o shop_id interpolado
 * ("Shop_id {xxx} is deauthorized from your APP by merchant."). Quem identifica
 * a loja e' o `shop_id` do ENVELOPE, nao o texto — nao parseie a mensagem.
 */
final class SellerDeauthorizationWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Texto livre em ingles. Nao e' codigo de erro; nao parseie. */
        public readonly ?string $message = null,
    ) {}
}
