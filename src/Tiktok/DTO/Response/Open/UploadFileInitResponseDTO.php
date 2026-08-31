<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Open;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de POST /open/{v}/file/init — a ABERTURA de uma sessão de upload
 * em chunks (hoje só vídeo).
 *
 * Não sobe nada: devolve pra onde mandar os chunks (`upload_url`) e o token
 * que autentica esse envio. O arquivo em si vai depois, chunk a chunk, pra
 * `upload_url` — que é um host próprio e NÃO passa pela assinatura HMAC do
 * resto da API.
 */
final class UploadFileInitResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $uploadUrl = null,
        /** Credencial de curta duração do upload — não logar. */
        public readonly ?string $uploadToken = null,
    ) {}
}
