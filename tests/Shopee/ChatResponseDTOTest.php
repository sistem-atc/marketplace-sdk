<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Shopee\DTO\Response\Chat\ConversationListResponseDTO;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Chat\MessageListResponseDTO;

it('hidrata a listagem de conversas paginada por cursor nano', function () {
    $dto = ConversationListResponseDTO::fromArray([
        'conversations' => [[
            'conversation_id' => '2001', 'to_id' => 555, 'to_name' => 'comprador_fake',
            'to_avatar' => 'https://cf.shopee.com.br/file/av', 'shop_id' => 999,
            'unread_count' => 2, 'pinned' => false, 'mute' => false,
            'latest_message_id' => 'm9', 'latest_message_type' => 'text',
            'latest_message_content' => ['text' => 'oi'],
            'latest_message_from_id' => 555, 'last_message_timestamp' => 1752710400,
        ]],
        'page_result' => [
            'more' => true, 'page_size' => 25,
            'next_cursor' => ['conversation_id' => '2001', 'next_message_time_nano' => '1752710400000000000'],
        ],
    ]);

    expect($dto->conversations)->toHaveCount(1)
        ->and($dto->conversations[0]->toName)->toBe('comprador_fake')
        ->and($dto->conversations[0]->unreadCount)->toBe(2)
        ->and($dto->conversations[0]->latestMessageContent?->text)->toBe('oi')
        // nano e' STRING (nao cabe/nao e' epoch em segundos)
        ->and($dto->pageResult?->nextCursor?->nextMessageTimeNano)->toBe('1752710400000000000')
        ->and($dto->pageResult?->more)->toBeTrue();
});

it('previa da ultima mensagem vem null quando nao e texto', function () {
    // Conversa cuja ultima mensagem e' imagem: latest_message_content = null,
    // o tipo esta em latest_message_type.
    $dto = ConversationListResponseDTO::fromArray([
        'conversations' => [[
            'conversation_id' => '2002', 'latest_message_type' => 'image',
            'latest_message_content' => null,
        ]],
    ]);

    expect($dto->conversations[0]->latestMessageType)->toBe('image')
        ->and($dto->conversations[0]->latestMessageContent)->toBeNull();
});

it('hidrata mensagem de TEXTO', function () {
    $dto = MessageListResponseDTO::fromArray(['messages' => [[
        'message_id' => 'm1', 'conversation_id' => '2001', 'message_type' => 'text',
        'content' => ['text' => 'ola'], 'from_id' => 555, 'to_id' => 999,
        'created_timestamp' => 1752710400, 'status' => 'normal', 'source' => 'chat',
    ]]]);

    expect($dto->messages[0]->messageType)->toBe('text')
        ->and($dto->messages[0]->content?->text)->toBe('ola')
        ->and($dto->messages[0]->content?->url)->toBeNull();
});

it('hidrata mensagem de IMAGEM (content muda por message_type)', function () {
    $dto = MessageListResponseDTO::fromArray(['messages' => [[
        'message_id' => 'm2', 'message_type' => 'image',
        'content' => [
            'url' => 'https://img.sp.mms.shopee.sg/fake', 'thumb_url' => '',
            'thumb_height' => 110, 'thumb_width' => 330, 'file_server_id' => 0,
        ],
    ]]]);

    expect($dto->messages[0]->content?->url)->toContain('fake')
        ->and($dto->messages[0]->content?->thumbHeight)->toBe(110)
        // `text` null aqui so' quer dizer "nao e' mensagem de texto"
        ->and($dto->messages[0]->content?->text)->toBeNull();
});

it('hidrata mensagem de STICKER', function () {
    $dto = MessageListResponseDTO::fromArray(['messages' => [[
        'message_id' => 'm3', 'message_type' => 'sticker',
        'content' => ['sticker_id' => '0004', 'sticker_package_id' => 'br_shoppito',
            'image_url' => 'https://deo.shopeemobile.com/fake.png'],
    ]]]);

    expect($dto->messages[0]->content?->stickerId)->toBe('0004')
        ->and($dto->messages[0]->content?->stickerPackageId)->toBe('br_shoppito');
});

it('aguenta source_content ora lista ora objeto', function () {
    $dto = MessageListResponseDTO::fromArray(['messages' => [
        ['message_id' => 'm4', 'source_content' => []],
        ['message_id' => 'm5', 'source_content' => ['order_sn' => '2607170AB1CDEF']],
    ]]);

    expect($dto->messages[0]->sourceContent)->toBe([])
        ->and($dto->messages[1]->sourceContent)->toBe(['order_sn' => '2607170AB1CDEF']);
});

it('next_offset do get_message e cursor STRING, nao indice', function () {
    $dto = MessageListResponseDTO::fromArray([
        'messages' => [],
        'page_result' => ['next_offset' => '2290533437706420593', 'page_size' => 5],
    ]);

    expect($dto->pageResult?->nextOffset)->toBe('2290533437706420593')
        ->and($dto->pageResult?->pageSize)->toBe(5);
});

it('faz roundtrip lossless da mensagem', function () {
    $payload = ['messages' => [[
        'message_id' => 'm1', 'conversation_id' => '2001', 'message_type' => 'text',
        'content' => ['text' => 'ola'], 'from_id' => 555, 'to_id' => 999,
        'from_shop_id' => 0, 'to_shop_id' => 999, 'created_timestamp' => 1752710400,
        'region' => 'BR', 'status' => 'normal', 'source' => 'chat', 'message_option' => 0,
        'source_content' => [],
    ]]];

    expect(MessageListResponseDTO::fromArray($payload)->toArray())->toEqual($payload);
});
