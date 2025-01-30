<?php

use App\Core\LanguageDetector;

$currentLanguage = LanguageDetector::detectLanguage()['language'];

?>
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <!-- Substituindo "Dashboard" pelo logo -->
        <a class="navbar-brand" href="/dashboard">
            <img src="../../public/assets/images/logo.png" alt="Logo" style="height: 60px;">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="/<?= $currentLanguage ?>/dashboard">
                    <i class="bi bi-house-door" style="font-size: 20px; margin-right: 10px;"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/<?= $currentLanguage ?>/profile">
                    <i class="bi bi-person" style="font-size: 20px; margin-right: 10px;"></i><?= _('Menu profile') ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/<?= $currentLanguage ?>/settings">
                    <i class="bi bi-gear" style="font-size: 20px; margin-right: 10px;"></i><?= _('Menu config') ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="/<?= $currentLanguage ?>/logout">
                    <i class="bi bi-box-arrow-right" style="font-size: 20px; margin-right: 10px;"></i><?= _('Menu out') ?>
                </a>
            </li>
        </ul>

    </div>
    <!-- Barra de seleção de idioma -->
    <div class="w-25 py-2 border-bottom text-end small">
        <?php
        // Detecta o idioma atual e a URI
        $currentUri = $_SERVER['REQUEST_URI']; // URI completa
        $currentUriWithoutLang = preg_replace('/^\/(pt|en)\//', '/', $currentUri); // Remove o idioma atual da URI

        // Links para os idiomas
        $ptUrl = '/pt' . $currentUriWithoutLang; // Adiciona o prefixo "pt"
        $enUrl = '/en' . $currentUriWithoutLang; // Adiciona o prefixo "en"
        ?>
        <a href="<?= $ptUrl ?>">
            <img src="/public/assets/images/flags/pt.png" alt="Português" title="Português" width="20" height="20">
        </a>
        <a href="<?= $enUrl ?>" class="me-2">
            <img src="/public/assets/images/flags/en.png" alt="English" title="English" width="20" height="20">
        </a>
        Sua sessão expirará em: <span id="session-timer"></span>
    </div>
    


</nav>