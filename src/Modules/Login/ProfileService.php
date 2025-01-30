<?php

namespace App\Modules\Login;

use App\Core\Database;
use Exception;

class ProfileService
{
    private static $db;

    public static function init()
    {
        if (!self::$db) {
            self::$db = Database::getInstance();
        }
    }
    
    public static function updateProfile(string $name, string $telefone, string $date, int $userId, string $role, int $isAdmin = null): bool
    {
        self::init();

        try {
            self::$db->beginTransaction();

            $stmt = self::$db->prepare("UPDATE users SET name = :name, telefone = :telefone, nascimento = :nascimento  WHERE id = :id");
            $stmt->execute([
                'name' => $name,
                'telefone' => $telefone,
                'nascimento' => $date,
                'id' => $userId
            ]);
            // Atualizar permissões (somente se for admin)
            if ($isAdmin) {
                // Limpa permissões existentes
                $deleteQuery = "DELETE FROM user_roles WHERE user_id = :user_id";
                $deleteStmt = self::$db->prepare($deleteQuery);
                $deleteStmt->execute(['user_id' => $userId]);

                // Insere novas permissões
                $insertQuery = "INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)";
                $insertStmt = self::$db->prepare($insertQuery);

                $insertStmt->execute(['user_id' => $userId, 'role_id' => $role]);
            }

            // Confirma a transação
            self::$db->commit();

            return true;
        } catch (Exception $e) {
            // Reverte a transação em caso de erro
            self::$db->rollBack();
            throw new Exception("Erro ao atualizar o perfil: " . $e->getMessage());
        }
    }

    public static function updatePassword(int $userId, string $currentPassword, string $newPassword, bool $perfil = false): array
    {
        try {
            self::init();

            if (!$perfil) {
                // Recupera a senha atual do banco de dados
                $stmt = self::$db->prepare("SELECT password FROM users WHERE id = :id");
                $stmt->execute(['id' => $userId]);
                $user = $stmt->fetch();

                if (!$user) {
                    return [
                        'success' => 'user_not_found',
                        'message' => 'Usuário não encontrado.',
                    ];
                }

                // Verifica se a senha atual é válida
                if (!password_verify($currentPassword, $user['password'])) {
                    return [
                        'success' => 'password_not_found',
                        'message' => 'A senha atual está incorreta.',
                    ];
                }
            }

            // Verifica se a nova senha é suficientemente forte
            if (strlen($newPassword) < 8) {
                return [
                    'success' => 'password_wrong_type',
                    'message' => 'A nova senha deve ter pelo menos 8 caracteres.',
                ];
            }

            // Hash da nova senha
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            // Atualiza a senha no banco de dados
            $updateStmt = self::$db->prepare("UPDATE users SET password = :password WHERE id = :id");
            $updateStmt->execute(['password' => $hashedPassword, 'id' => $userId]);

            return [
                'success' => 'password_ok',
                'message' => 'Senha alterada com sucesso.',
            ];
        } catch (Exception $e) {
            return [
                'success' => 'password_failure',
                'message' => 'Erro ao alterar a senha: ' . $e->getMessage(),
            ];
        }
    }
}