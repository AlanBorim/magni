<?php

namespace App\Modules\General;

class GeneralController
{
    public function getSessionTime()
    {
        include __DIR__ . '/views/get_session_time.php';
    }
}