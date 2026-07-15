<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Contracts;

use Illuminate\Http\Request;

/**
 * Verificador da assinatura do push (Webhook) da Shopee.
 *
 * A Shopee assina o push com HMAC-SHA256 usando o Partner Key do app (base
 * string = url + corpo bruto). Como o Partner Key vive por-integration no
 * host (Bunker, encriptado), a lib nao consegue resolve-lo sozinha — o host
 * vincula uma implementacao deste contrato no container.
 *
 * `ShopeeWebhookHandler::validate()` delega pra este verifier QUANDO ligado;
 * se nenhuma implementacao estiver vinculada, o handler mantem o
 * comportamento legado (aceita — backward-compat).
 */
interface ShopeeWebhookVerifier
{
    /**
     * @return bool true = push autentico (ou modo observacao); false = rejeita (401)
     */
    public function verify(Request $request): bool;
}
