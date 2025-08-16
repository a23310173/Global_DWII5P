<?php
// PHP/con.php — Conexión PDO (Hostinger)
declare(strict_types=1);

const DB_HOST = 'localhost';                 // Confirma en hPanel
const DB_NAME = 'u178616640_global';         // Nombre con prefijo
const DB_USER = 'u178616640_23310173';       // Usuario con prefijo
const DB_PASS = 'Global12.';                 // Contraseña
const DEBUG_DB = false;

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4';
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $opt);
        $pdo->exec("SET time_zone='-06:00'");
        return $pdo;
    } catch (PDOException $e) {
        if (DEBUG_DB) die('Error de conexión: '.$e->getMessage());
        http_response_code(500);
        exit('Error de conexión a la base de datos.');
    }
}

function run(string $sql, array $params = []): PDOStatement {
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

if (php_sapi_name() !== 'cli' && realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(403);
    exit('Acceso denegado.');
}
