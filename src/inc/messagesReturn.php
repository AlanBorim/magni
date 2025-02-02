<?php

use App\Core\MessageHandler;

// Captura mensagens de requisição GET
if (isset($_GET['msg_type']) && isset($_GET['msg_var']) && isset($_GET['msg_text'])) {
    $msgType = htmlspecialchars($_GET['msg_type']);
    $msgVar = htmlspecialchars($_GET['msg_var']);
    $msgText = htmlspecialchars(urldecode($_GET['msg_text']));
    if ($msgVar == 'not_activated') {
        $resend = 1; // Atualiza a referência
    }
    echo "<div class='alert alert-{$msgType}'>{$msgText}</div>";
}

// Captura mensagens armazenadas localmente
$messages = MessageHandler::getMessages();
foreach ($messages as $msg) {
    if ($msg['var'] == 'not_activated') {
        $resend = 1; // Atualiza a referência
    }
    echo "<div class='alert alert-{$msg['type']}'>{$msg['message']}</div>";
}

/**
 * Toda vez que precisar apresentar mensagens na mesma página utiliza-se o trecho
 * use App\Core\MessageHandler;
 *
 * MessageHandler::addMessage('success', 'Usuário cadastrado com sucesso!');
 *
 */
