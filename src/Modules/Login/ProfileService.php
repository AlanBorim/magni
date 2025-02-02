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

    public static function updateProfilePicture(int $userId, array $file): array
    {
        self::init();

        // Validar o arquivo enviado
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return [
                'success' => 'no_picture',
                'message' => 'Nenhuma imagem válida foi enviada.',
            ];
        }

        // Obtém o caminho atual da imagem do banco de dados
        $stmt = self::$db->prepare('SELECT profile_picture_path FROM users WHERE id = :id');
        $stmt->execute(['id' => $userId]);
        $currentPicture = $stmt->fetchColumn();

        // Remove o arquivo da imagem atual, se existir
        if (!is_null($currentPicture)) {
            if (file_exists(__DIR__ . $currentPicture)) {
                if (!unlink(__DIR__ . $currentPicture)) {
                    error_log("Falha ao remover o arquivo: $currentPicture");
                }
            } else {
                error_log("Arquivo não encontrado ou caminho inválido:" . __DIR__ . $currentPicture);
            }
        }

        // Tipos de imagem permitidos
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($file['tmp_name']);
        $imageSize = filesize($file['tmp_name']);

        // Validar tipo e tamanho
        if (!in_array($fileType, $allowedTypes)) {
            return [
                'success' => 'wrong_type',
                'message' => 'Tipo de arquivo inválido. Apenas JPEG, PNG e GIF são aceitos.',
            ];
        }

        if ($imageSize > 2 * 1024 * 1024) { // Limite de 2MB
            return [
                'success' => 'wrong_size',
                'message' => 'O tamanho da imagem excede o limite permitido de 2MB.',
            ];
        }

        // Criar o caminho para salvar a imagem
        $uploadDir = __DIR__ . '/../../../public/assets/images/profile_pictures/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true); // Cria o diretório se não existir
        }

        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('profile_', true) . '.' . $fileExtension;
        $filePath = $uploadDir . $fileName;

        // Mover a imagem para o diretório
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            return [
                'success' => 'save_file_error',
                'message' => 'Erro ao salvar a imagem no servidor.',
            ];
        }

        // Caminho relativo para armazenar no banco
        $relativePath = '/../../../public/assets/images/profile_pictures/' . $fileName;

        // Atualizar o caminho da imagem no banco de dados
        $stmt = self::$db->prepare("UPDATE users SET profile_picture_path = :profile_picture_path WHERE id = :user_id");

        if ($stmt->execute([
            ':profile_picture_path' => $relativePath,
            ':user_id' => $userId
        ])) {
            return [
                'success' => 'save_ok',
                'message' => 'Foto de perfil atualizada com sucesso!',
            ];
        }

        return [
            'success' => 'save_error',
            'message' => 'Não foi possível atualizar a imagem de perfil',
        ];
    }
}
