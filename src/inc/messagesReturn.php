<?php

use App\Core\FlashMessages;

$messages = FlashMessages::getFlash();

$resend = null;
if (!empty($messages)) {
    foreach ($messages as $type => $msgs) {
        foreach ($msgs as $msg) {
            if (array_keys($msgs)[0] == 'not_activated') {
                $resend = 1;
            }
            foreach ($msg as $txtMsg) {
                echo "<div class='alert alert-$type'>" . $txtMsg . "</div>";
            }
        }
    }
}

if ($resend == 1) {
?>
    <div class="text-center align-items-center flex-grow-1">
        <form method="POST" action="/<?= $currentLanguage; ?>/resendActivationEmail">
            <button type="submit" class="btn btn-primary">Reenviar E-mail de Ativação</button>
            <input type="hidden" name="resend_activation" value="1">
            <input type="hidden" name="id" value="<?= $_SESSION['user_id'] ?>">
        </form>
    </div>
<?php } ?>