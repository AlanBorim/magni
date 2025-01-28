<?php

use App\Core\FlashMessages;

$messages = FlashMessages::getFlash();

if (!empty($messages)) {
    foreach ($messages as $type => $msgs) {
        foreach ($msgs as $msg) {
            if (array_keys($msgs)[0] == 'not_activated') {
                $resend = 1; // Atualiza a referência
            }
            foreach ($msg as $txtMsg) {
                echo "<div class='alert alert-$type'>" . $txtMsg . "</div>";
            }
        }
    }
}

