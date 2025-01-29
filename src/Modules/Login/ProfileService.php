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
}