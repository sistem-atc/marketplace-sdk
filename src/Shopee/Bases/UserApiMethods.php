<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Bases;

use InvalidArgumentException;
use SistemAtc\Marketplaces\Shopee\Support\SignatureGenerator;

/**
 * Base das APIs Shopee de `api_type` **User** (livestream, video).
 *
 * Diferenca pra Shop API: o parametro comum e' `user_id` (nao `shop_id`) e a
 * assinatura e' `partner_id + path + timestamp + access_token + user_id` —
 * mesmo HMAC, so' troca a entidade, entao basta declarar `$authEntity` e o
 * BaseMethods faz o resto (retry 429/5xx, re-auth, erro com HTTP 200 + `error`).
 *
 * `user_id` vem de `settings['user_id']`: e' o id do usuario Shopee dono da
 * conta (main account), NAO o shop_id. Sem ele a chamada e' recusada aqui
 * mesmo, antes de bater na API.
 */
abstract class UserApiMethods extends BaseMethods
{
    protected string $authEntity = 'user_id';

    /**
     * @return array<string, int|string>
     */
    protected function buildAuthQuery(string $apiPath, bool $publicApi, bool $merchantApi = false): array
    {
        if ($publicApi || $merchantApi) {
            return parent::buildAuthQuery($apiPath, $publicApi, $merchantApi);
        }

        $settings = $this->integration->getMarketplaceSettings();
        // Fallback main_account_id: algumas integracoes gravam o dono da conta
        // com esse nome; e' o mesmo identificador que a API chama de user_id.
        $userId = (int) ($settings['user_id'] ?? $settings['main_account_id'] ?? 0);

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                'Shopee User API exige settings[user_id] (id do usuario/main account) na integracao.'
            );
        }

        $partnerId = (int) ($settings['partner_id'] ?? 0);
        $partnerKey = (string) ($settings['partner_key'] ?? '');
        $timestamp = time();
        $accessToken = (string) $this->integration->getAccessToken();
        $sign = SignatureGenerator::entitySign($partnerId, $apiPath, $timestamp, $accessToken, $userId, $partnerKey);

        return [
            'partner_id' => $partnerId,
            'timestamp' => $timestamp,
            'access_token' => $accessToken,
            'user_id' => $userId,
            'sign' => $sign,
        ];
    }

    /**
     * Query assinada pra chamadas que saem FORA do makeRequest (multipart).
     *
     * @return array<string, int|string>
     */
    protected function signedUserQuery(string $apiPath): array
    {
        return $this->buildAuthQuery($this->normalizeApiPath($apiPath), false);
    }
}
