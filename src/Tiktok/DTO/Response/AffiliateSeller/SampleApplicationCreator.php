<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Creator que pediu amostra, com o desempenho que sustenta a decisao.
 *
 * `gmv` e' dos ultimos 30 dias; `fulfillmentPercentage` ("60.50") e' dos
 * ultimos 90 e vem como STRING de porcentagem — nao e' fracao.
 * Se o creator nao autorizou compartilhar o dado, o campo vem vazio.
 */
final class SampleApplicationCreator implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $creatorOpenId = null,
        public readonly ?string $username = null,
        public readonly ?string $nickname = null,
        public readonly ?int $followerCount = null,
        // aqui a URL vem CRUA, nao embrulhada em {url} como no resto do grupo
        public readonly ?string $avatarUrl = null,
        // GMV de conteudo comprável nos ultimos 30 dias
        public readonly ?AffiliateMoney $gmv = null,
        public readonly ?int $contentCount = null,
        // porcentagem com 2 casas: "60.50" = 60,50%
        public readonly ?string $fulfillmentPercentage = null,
        // mediana de views de video comprável em 30 dias
        public readonly ?int $ecVideoView = null,
    ) {}
}
