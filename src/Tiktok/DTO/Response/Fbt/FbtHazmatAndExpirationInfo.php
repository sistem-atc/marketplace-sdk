<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Envelope da regra de hazmat/validade.
 *
 * O nome da chave (`hazmat_and_expiration_dto_list`) carrega "dto" — e'
 * vazamento do backend do TikTok, nao um typo nosso. Mantido literal pra
 * roundtrip.
 *
 * @property list<FbtHazmatAndExpirationItem>|null $hazmatAndExpirationDtoList
 */
final class FbtHazmatAndExpirationInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(FbtHazmatAndExpirationItem::class)]
        public readonly ?array $hazmatAndExpirationDtoList = null,
    ) {}
}
