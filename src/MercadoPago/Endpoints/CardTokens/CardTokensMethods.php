<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\CardTokens;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\CardTokens\CardTokenResponseDTO;

/**
 * Card Tokens — tokenizacao de cartao no servidor. Em producao o token
 * nasce no FRONT (SDK JS/MercadoPago.js) pra que o PAN nunca passe pelo
 * nosso servidor; este endpoint serve pra testes e pra gerar token a
 * partir de um cartao ja' salvo (`card_id` + `security_code`).
 *
 * Doc: https://www.mercadopago.com.br/developers/pt/reference/card_tokens/_card_tokens/post
 */
class CardTokensMethods extends BaseMethods
{
    /**
     * @param  array<string, mixed>  $payload  card_id + security_code (cartao salvo) ou dados abertos do cartao (so' teste).
     */
    public function create(array $payload): CardTokenResponseDTO
    {
        return CardTokenResponseDTO::fromArray($this->makeRequest(HttpMethod::POST, '/v1/card_tokens', body: $payload));
    }

    public function get(string $cardTokenId): CardTokenResponseDTO
    {
        return CardTokenResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, '/v1/card_tokens/'.rawurlencode($cardTokenId)));
    }
}
