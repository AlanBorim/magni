<?php

namespace App\Core;

use App\Core\Database;

class Roles
{
    private static $db;

    public static function init()
    {
        if (!self::$db) {
            self::$db = Database::getInstance();
        }
    }

    public static function getRoles(): ?array
    {
        self::init();
        $stmt = self::$db->prepare("SELECT * FROM roles");
        $stmt->execute();
        return $stmt->fetchAll() ?: null;
    }
}