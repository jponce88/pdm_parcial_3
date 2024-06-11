<?php
class DB {
    private static $dbHost = "localhost";
    private static $dbName = "apipr08012_db";
    private static $dbUser = "root";
    private static $dbPass = "";

    public static function connect() {
        $pdo = new PDO("mysql:host=" . self::$dbHost . ";dbname=" . self::$dbName, self::$dbUser, self::$dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    }
}
