<?php

class Database {
    private static $pdo;

    public static function connect() {
        if (!self::$pdo) {
            $host = 'localhost';
            $db   = 'u174511361_pdv';
            $user = 'root';
            $pass = '';
            try {
                self::$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                Response::error("Erro ao conectar ao banco: " . $e->getMessage());
                exit;
            }
        }
        return self::$pdo;
    }
}


class Response {
    public static function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function success($message, $extra = []) {
        self::json(array_merge(['success' => true, 'message' => $message], $extra));
    }

    public static function error($message, $code = 400) {
        self::json(['success' => false, 'message' => $message], $code);
    }
}
