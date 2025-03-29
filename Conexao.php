<?php
    class Conexao {
            private $host = 'localhost';
            private $dbname = 'jr_broker_db';
            private $user = 'root';
            private $pass = '';//'T5rzr6b@yF!Rkn7y';
            public function conectar (){
            try{
                $conexao = new PDO(
                    "mysql:host=$this->host;dbname=$this->dbname; charset=utf8mb4",
                    "$this->user",
                    "$this->pass",
                    
                );
                return $conexao;
    
            } catch (PDOException $e){
                echo '<p>'.$e->getMessage();
    
            }
    
    
            }
    }
?>