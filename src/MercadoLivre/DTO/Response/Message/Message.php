<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Message;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Mensagem do chat pós-venda do ML (`messages[]`).
 *
 * O pack da conversa sai de `messageResources` (entry com name='packs') —
 * use {@see self::packId()}.
 *
 * @property list<MessageResource> $messageResources
 * @property array<int|string, mixed> $messageAttachments
 */
final class Message implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  list<MessageResource>  $messageResources
     * @param  array<int|string, mixed>  $messageAttachments
     */
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $text = null,
        public readonly ?string $subject = null,
        public readonly ?string $status = null,
        public readonly ?string $siteId = null,
        public readonly ?string $clientId = null,
        public readonly ?MessageParty $from = null,
        public readonly ?MessageParty $to = null,
        public readonly ?MessageDate $messageDate = null,
        public readonly ?MessageModeration $messageModeration = null,
        #[ArrayOf(MessageResource::class)]
        public readonly array $messageResources = [],
        public readonly array $messageAttachments = [],
        public readonly ?bool $conversationFirstMessage = null,
        public readonly mixed $data = null,
    ) {}

    /** Id do pack (conversa) — vem do message_resource name='packs'. */
    public function packId(): ?string
    {
        foreach ($this->messageResources as $r) {
            if ($r->name === 'packs') {
                return $r->id;
            }
        }

        return null;
    }
}
