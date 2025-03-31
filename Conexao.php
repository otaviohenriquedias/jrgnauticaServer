<?php
class Conexao {
    private static ?Conexao $instance = null;
    private array $pool = [];
    private int $poolSize = 5;
    private string $host = '108.181.92.74:3306';
    private string $dbname = 'jr_broker_novo';
    private string $user = 'jr_broker_db';
    private string $pass = 'T5rzr6b@yF!Rkn7y';

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
            error_log("Erro de conexão: " . $e->getMessage()); // Log de erro
            return null;
        }
    }

    public function getConexao(): ?PDO {
        // Retorna uma nova conexão se a pool estiver vazia ou uma conexão válida
        $conexao = array_pop($this->pool);
        if ($conexao && $this->verificarConexao($conexao)) {
            return $conexao;
        }
        return $this->criarConexao();
    }

    private function verificarConexao(PDO $conexao): bool {
        try {
            $conexao->getAttribute(PDO::ATTR_SERVER_INFO);
            return true; // Conexão válida
        } catch (PDOException $e) {
            return false; // Conexão inválida
        }
    }

    public function liberarConexao(PDO $conexao): void {
        if ($this->verificarConexao($conexao) && count($this->pool) < $this->poolSize) {
            $this->pool[] = $conexao;
        }
    }

    // Método para aumentar o pool
    public function aumentarPool(int $quantidade): void {
        for ($i = 0; $i < $quantidade; $i++) {
            $this->pool[] = $this->criarConexao();
        }
    }

    private function __clone() {}
    public function __wakeup() {}
}
?>