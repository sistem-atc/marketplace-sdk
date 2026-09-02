<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Question;

use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Question\QuestionResponseDTO;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Question\QuestionSearchResponseDTO;

class QuestionMethods extends BaseMethods
{
    /**
     * Busca detalhes de uma pergunta especifica.
     */
    public function get(int|string $questionId): QuestionResponseDTO
    {
        return QuestionResponseDTO::fromArray(
            $this->makeRequest(HttpMethod::GET, "/questions/{$questionId}")
        );
    }

    /**
     * Responde uma pergunta (Sellers).
     */
    public function answer(int|string $questionId, string $text): array
    {
        return $this->makeRequest(HttpMethod::POST, '/answers', [], [
            'question_id' => $questionId,
            'text' => $text
        ]);
    }

    /**
     * Pesquisa perguntas recebidas.
     */
    public function search(array $filters = []): QuestionSearchResponseDTO
    {
        return QuestionSearchResponseDTO::fromArray(
            $this->makeRequest(HttpMethod::GET, '/questions/search', $filters)
        );
    }

    /**
     * Deleta uma pergunta (Sellers podem deletar perguntas inapropriadas).
     */
    public function delete(int|string $questionId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/questions/{$questionId}");
    }

    /**
     * Lista perguntas nao respondidas de um item.
     */
    public function unansweredByItem(string $itemId): QuestionSearchResponseDTO
    {
        return $this->search([
            'item' => $itemId,
            'status' => 'UNANSWERED'
        ]);
    }


    /**
     * Faz uma pergunta num anúncio como COMPRADOR (POST /questions, body
     * `{text, item_id}`). Útil pra testes com usuário de teste; o seller não
     * pergunta no próprio item. Mande UTF-8.
     */
    public function create(string $itemId, string $text): QuestionResponseDTO
    {
        return QuestionResponseDTO::fromArray(
            $this->makeRequest(HttpMethod::POST, '/questions', [], [
                'text' => $text,
                'item_id' => $itemId,
            ])
        );
    }

    /**
     * Tempo médio de resposta do seller (GET /users/{id}/questions/response_time):
     * `{user_id, total{response_time}, weekend{...}, weekdays_working_hours{...},
     * weekdays_extra_hours{...}}` — janela dos últimos 14 dias, em minutos,
     * com `sales_percent_increase` estimado por faixa.
     *
     * @return array<string, mixed>
     */
    public function responseTime(int|string $userId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$userId}/questions/response_time");
    }
}
