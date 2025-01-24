<?php

namespace App\Modules\Login;

use App\Core\Database;
use PDO;
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

    public function findByEmail($email)
    {
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            return $user;
        }

        return null;
    }

    public function insertResetToken(string $token, int $userId): bool
    {
        $db = Database::getInstance();
        $expireTime = date('Y-m-d H:i:s', strtotime('+1 hour')); // Expira em 1 hora

        // Salva o token no banco de dados
        $stmt = $db->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':expires_at', $expireTime, PDO::PARAM_STR);
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    public function findByResetToken(string $token): bool
    {
        $db = Database::getInstance();

        // Verifica se o token é válido e não expirou
        $stmt = $db->prepare('SELECT token FROM password_resets WHERE token = :token AND expires_at > NOW()');
        $stmt->execute(['token' => $token]);
        $row = $stmt->fetch();

        return (bool) $row; // Retorna true se o token for válido, false caso contrário
    }

}
