<?php

namespace App\Core;

class MessageHandler
{
    private static array $messages = [];

    /**
     * Adiciona uma nova mensagem ao sistema.
     *
     * @param string $type Tipo da mensagem (success, error, warning, info).
     * @param string $message Conteúdo da mensagem.
     */
    public static function addMessage(string $type, string $var, string $message): void
    {
        self::$messages[] = ['type' => $type, 'var' => $var, 'message' => $message];
    }

    /**
     * Retorna todas as mensagens registradas e limpa o buffer.
     *
     * @return array Lista de mensagens.
     */
    public static function getMessages(): array
    {
        $messages = self::$messages;
        self::$messages = []; // Limpa as mensagens após a exibição
        return $messages;
    }

    /**
     * Retorna mensagens formatadas em JSON para requisições AJAX.
     */
    public static function getMessagesJson(): void
    {
        header('Content-Type: application/json');
        echo json_encode(['messages' => self::getMessages()]);
        exit;
    }

    /**
     * Adiciona mensagem e redireciona com parâmetro GET.
     *
     * @param string $type Tipo da mensagem.
     * @param string $message Texto da mensagem.
     * @param string $redirectUrl URL para onde redirecionar.
     */
    public static function redirectWithMessage(string $type, string $var, string $message, string $redirectUrl): void
    {
        $queryParam = http_build_query(['msg_type' => $type, 'msg_var' => $var, 'msg_text' => urlencode($message)]);
        header("Location: $redirectUrl?$queryParam");
        exit;
    }
}
