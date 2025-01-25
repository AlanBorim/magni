<?php

namespace App\Core;

class Validations
{
    public function validatePasswordReset(string $password, string $confirmPassword): array
    {
        $errors = [];

        if (empty($password)) {
            $errors[] = 'A senha está vazia.';
        }

        if (strlen($password) < 8) {
            $errors[] = 'A senha deve ter pelo menos 8 caracteres.';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'As senhas não coincidem.';
        }

        return $errors;
    }
}