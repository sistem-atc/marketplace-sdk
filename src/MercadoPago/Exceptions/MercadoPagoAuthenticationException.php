<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Exceptions;

use Exception;

/**
 * Erro de autenticacao Mercado Pago — credenciais OAuth invalidas,
 * access_token expirado sem refresh_token, integration desativada, ou
 * config faltando (client_id/client_secret).
 *
 * Distinto de MercadoPagoRequestException (que cobre erros runtime
 * da API). Esse aqui sinaliza problema de configuracao — usuario
 * precisa reconectar via OAuth no painel.
 */
class MercadoPagoAuthenticationException extends Exception {}
