<?php

namespace App\Modules\Login;

use App\Core\Database;

class LoginService
{
    public function validateUser($email, $password)
    {
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT users.id, users.email, users.name, users.telefone, users.nascimento, 
                                users.profile_picture_path AS profile_picture, users.last_login, 
                                users.canceled, users.two_factor_enabled,users.two_factor_secret, 
                                users.password, roles.id as roleId , roles.name AS roleName, roles.role, 
                                roles.description AS roleDescription , activated FROM users
                                INNER JOIN user_roles on user_roles.user_id = users.id
                                INNER JOIN roles on roles.id = user_roles.role_id
                                WHERE users.email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return null;
    }

    public function validateTwoFactorCode($userId, $code)
    {
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT two_factor_secret FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();

        return $user && $user['two_factor_secret'] === $code;
    }
}
