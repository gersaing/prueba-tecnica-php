<?php
class Database
{
    private PDO $pdo;

    public function __construct(array $cfg)
    {
        $dsn = "mysql:host={$cfg['host']};dbname={$cfg['dbname']};charset=utf8mb4";
        $this->pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    public function run($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }
}
