<?php

namespace App\Modules\Login;

use App\Core\Database;
use App\Core\FlashMessages;
use App\Core\LanguageDetector;
use PDO;
use Exception;

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

    public function findById($id)
    {
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
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
        if ($stmt->execute()) {
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

    public function resetPassword(string $token, string $password): void
    {
        $db = Database::getInstance();
        $currentLanguage = LanguageDetector::detectLanguage()['language'];

        // Verifica novamente o token antes de alterar a senha
        $stmt = $db->prepare('SELECT users.id, users.email FROM password_resets 
                          INNER JOIN users ON password_resets.user_id = users.id 
                          WHERE password_resets.token = :token AND password_resets.expires_at > NOW()');
        $stmt->execute(['token' => $token]);
        $row = $stmt->fetch();

        if (!$row) {
            FlashMessages::setFlash('danger', 'invalid_token', 'Token inválido ou expirado.');
            header("Location: /{$currentLanguage}/reset-password");
            exit;
        }

        // Atualiza a senha do usuário
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $updateStmt = $db->prepare('UPDATE users SET password = :password WHERE id = :userId');
        $updateStmt->execute(['password' => $hashedPassword, 'userId' => $row['id']]);

        if ($updateStmt->rowCount() === 0) {
            FlashMessages::setFlash('danger', 'password_change_error', 'Erro ao atualizar a senha. Tente novamente.');
            header("Location: /{$currentLanguage}/reset-password?token=$token");
            exit;
        }

        // Remove o token usado
        $deleteStmt = $db->prepare('DELETE FROM password_resets WHERE token = :token');
        $deleteStmt->execute(['token' => $token]);

        FlashMessages::setFlash('success','password_chenge_success' , 'Senha redefinida com sucesso. Você já pode fazer login.');
        header("Location: /{$currentLanguage}/");
        exit;
    }

    /**
     * Undocumented function
     *
     * @param array $userData
     * @param array|null $roles
     * @return boolean
     */
    public static function addUserWithRoles(array $userData, array $roles = null): bool
    {
        try {
            // Inicia a conexão com o banco
            $db = Database::getInstance();

            // Inicia a transação
            $db->beginTransaction();

            // Insere o novo usuário na tabela `users`
            $stmt = $db->prepare("
            INSERT INTO users (name, email, password, telefone, mail_notification, activation_token)
            VALUES (:name, :email, :password, :telefone, :mail_notification, :activation_token)");
            $stmt->execute([
                ':name' => $userData['name'],
                ':email' => $userData['email'],
                ':password' => password_hash($userData['password'], PASSWORD_BCRYPT),
                ':telefone' => $userData['telefone'] ?? null,
                ':mail_notification' => $userData['mail_notification'] ?? '0',
                ':activation_token' => $userData['activationToken'],
            ]);

            // Obtém o ID do usuário recém-inserido se não houver permissões inseridas a permissão padrão é 2
            $userId = $db->lastInsertId();
            $roleId = $userData['role'] ?? 2;

            // Insere as roles associadas na tabela `user_roles`
            $roleStmt = $db->prepare("
            INSERT INTO user_roles (user_id, role_id)
            VALUES (:user_id, :role_id)");
            $roleStmt->execute([
                ':user_id' => $userId,
                ':role_id' => $roleId,
            ]);

            // Confirma a transação
            $db->commit();

            return true;
        } catch (Exception $e) {
            // Reverte a transação em caso de erro
            $db->rollBack();
            throw new Exception("Erro ao adicionar o usuário: " . $e->getMessage());
        }
    }

    public static function findByToken(string $token)
    {
        $db = Database::getInstance();

        // Verifica o token
        $stmt = $db->prepare("SELECT id FROM users WHERE activation_token = :token AND activated = '0'");
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
       
        if ($user) {
            // Ativa o usuário
            $updateStmt = $db->prepare("UPDATE users SET activated = '1', activation_token = NULL WHERE id = :id");
            $updateStmt->execute([':id' => $user['id']]);

            return true;
        } else {
            return false;
        }
    }

    public static function updateLastLogin(int $userId): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = :user_id");
        $stmt->execute(['user_id' => $userId]);
    }
}
