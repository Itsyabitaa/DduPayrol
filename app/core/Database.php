<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static $connection;

    // Get database connection
    public static function getConnection()
    {
        if (!self::$connection) {
            try {
                // Database configuration
                $host = 'localhost';
                $dbname = 'ooppayrol';
                $username = 'root';
                $password = '';

                // Create PDO instance and set it as a static connection
                self::$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }

        return self::$connection;
    }
}
