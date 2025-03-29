<?php
class Conexao {
    private static ?Conexao $instance = null;
    private array $pool = [];
    private int $poolSize = 5;
    private string $host = 'localhost';
    private string $dbname = 'jr_broker_db';
    private string $user = 'root';
    private string $pass = '';

    private function __construct() {
        for ($i = 0; $i < $this->poolSize; $i++) {
            $this->pool[] = $this->criarConexao();
        }
    }

    public static function getInstance(): Conexao {
        if (self::$instance === null) {
            self::$instance = new Conexao();
        }
        return self::$instance;
    }

    private function criarConexao(): ?PDO {
        try {
            return new PDO(
                "mysql:host=$this->host;dbname=$this->dbname;charset=utf8mb4",
                $this->user,
                $this->pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            echo '<p>Erro de conexão: ' . $e->getMessage() . '</p>';
            return null;
        }
    }

    public function getConexao(): ?PDO {
        return array_pop($this->pool) ?: $this->criarConexao();
    }

    public function liberarConexao(PDO $conexao): void {
        if (count($this->pool) < $this->poolSize) {
            $this->pool[] = $conexao;
        }
    }
}
