<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';


use App\Core\ViewHelper;
use App\Core\LanguageDetector;
use App\Core\Security;
use App\Modules\Login\LoginService;

Security::enforceSessionSecurity();

$currentLanguage = LanguageDetector::detectLanguage()['language'];

$user = new LoginService();
$userData = $user->findById($_SESSION['user_id']);
?>

<!DOCTYPE html data-bs-theme="light">
<html lang="<?= $currentLanguage ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= _('Profile title') ?></title>
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/headers.php'); ?>
</head>

<body>

    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/menu.php'); ?>

    <div class="container mt-5">
        <div class="row">
            <!-- Card Lateral -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <img src="<?= htmlspecialchars($userData['profile_picture'] ?? '../public/assets/images/150.png') ?>"
                            alt="Foto de Perfil"
                            class="rounded-circle mb-3"
                            style="width: 150px; height: 150px;">

                        <h5 class="card-title"><?= htmlspecialchars($userData['name']) ?></h5>
                        <p class="text-muted"><?= htmlspecialchars($userData['email']) ?></p>

                        <!-- Botão para abrir o modal -->
                        <button class="btn btn-primary btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#updatePhotoModal">
                            <?= _('Profile picture button') ?>
                        </button>
                    </div>
                </div>
                <div class="card mt-4">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted"><?= _('Profile additional info') ?></h6>
                        <p><strong><?= _('Profile last login') ?></strong> <?= ($userData ? date("d/m/Y H:i", strtotime($userData['last_login'])) : "Nunca"); ?></p>
                        <p><strong><?= _('Profile status') ?></strong> <?= ($userData['canceled'] == null ? "Ativo" : "Inativo"); ?></p>
                    </div>
                </div>
            </div>

            <!-- Conteúdo Principal -->
            <div class="col-md-8">
                <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="dados-tab" data-bs-toggle="tab" data-bs-target="#dados" type="button" role="tab"><?= _('Profile menu personal') ?></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="senha-tab" data-bs-toggle="tab" data-bs-target="#senha" type="button" role="tab"><?= _('Profile menu password') ?></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="preferencias-tab" data-bs-toggle="tab" data-bs-target="#preferencias" type="button" role="tab"><?= _('Profile menu settings') ?></button>
                    </li>
                </ul>

                <div class="tab-content p-4 border border-top-0" id="profileTabsContent">
                    <!-- Dados Pessoais -->
                    <div class="tab-pane fade show active" id="dados" role="tabpanel">
                        <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/messagesReturn.php'); ?>
                        <form action="/<?= $currentLanguage ?>/update-profile" method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label"><?= _('Profile form nome') ?></label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= $userData['name'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label"><?= _('Profile form email') ?></label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= $userData['email'] ?>" required readonly>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label"><?= _('Phone:') ?></label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?= $userData['telefone'] ?? null ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="nascimento" class="form-label">Data de nascimento</label>
                                <input type="date" class="form-control" id="nascimento" name="nascimento" value="<?= $userData['nascimento'] ?? null ?>" required>
                            </div>
                            <?php if ($_SESSION['role'] == 'admin') { ?>
                                <div class="mb-3">
                                    <label for="role" class="form-label"><?= _('Profile form permissions') ?></label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="" selected disabled><?= _('Profile form placeholder') ?></option>
                                        <?php
                                        if (!empty($rolesData) && is_array($rolesData)) {
                                            foreach ($rolesData as $role) { ?>
                                                <option value="<?= htmlspecialchars($role['id']) ?>" <?= $_SESSION['role'] == $role['name'] ? ' selected' : '' ?>><?= htmlspecialchars($role['name']) ?></option>
                                        <?php }
                                        } else {
                                            echo '<option value="" disabled>' . _('Profile form empty') . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            <?php } ?>
                            <div class="mb-3">
                                <input type="hidden" name="update_profile" value="1">
                                <button type="submit" class="btn btn-primary"><?= _('Profile form button') ?></button>
                            </div>
                        </form>

                    </div>

                    <!-- Alterar Senha -->
                    <div class="tab-pane fade" id="senha" role="tabpanel">
                        <form action="/<?= $currentLanguage ?>/update-password" method="POST">
                            <div class="mb-3">
                                <label for="current_password" class="form-label"><?= _('Profile current pass') ?></label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label"><?= _('Profile new pass') ?></label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label"><?= _('Profile confirm pass') ?></label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-warning"><?= _('Profile pass button') ?></button>
                            <input type="hidden" name="update_password" value="1">
                        </form>
                    </div>

                    <!-- Preferências -->
                    <div class="tab-pane fade" id="preferencias" role="tabpanel">
                        <form action="update_preferences.php" method="POST">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="notify_email" name="notify_email" checked>
                                <label class="form-check-label" for="notify_email"><?= _('Profile settings email') ?></label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="tfa" name="tfa" <?= $_SESSION['two_factor_enabled'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="tfa"><?= _('Profile settings 2fa') ?></label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="darkModeSwitch">
                                <label class="form-check-label" for="darkModeSwitch"><?= _('Ativar Modo Escuro') ?></label>
                            </div>
                            <button type="submit" class="btn btn-secondary mt-3"><?= _('Profile settings button') ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para Alterar Foto -->
    <div class="modal fade" id="updatePhotoModal" tabindex="-1" aria-labelledby="updatePhotoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data" action="/<?= $currentLanguage ?>/update-profile-picture">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updatePhotoModalLabel"><?= _('Profile modal title') ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="profile_picture" class="form-label"><?= _('Profile modal title') ?></label>
                            <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= _('Profile modal form cancel') ?></button>
                        <button type="submit" class="btn btn-primary"><?= _('Profile form button') ?></button>
                        <input type="hidden" name="update_profile_picture" value="1">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Rodapé -->
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/footer.php'); ?>

    <!-- intl-tel-input CSS e JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

    <script>
        // Inicializando intl-tel-input
        const phoneInput = document.querySelector("#phone");
        const iti = window.intlTelInput(phoneInput, {
            initialCountry: "br", // Define o país inicial como Estados Unidos
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        });
    </script>
</body>

</html>