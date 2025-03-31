<?php
    class Estados{
        private $id_estado;
        private $sigla;
        private $descricao;
        private $conexao;
        private $conexaoPool;
        function __construct(PDO $conexao, Conexao $conexaoPool){
            $this->conexao = $conexao;
            $this->conexaoPool = $conexaoPool;
        }
        public function __set($name, $value)
        {
            $this->$name = $value; 
        }
        public function __get($name)
        {
            return $this->$name;
        }
        
        public  function getEstados (){
            $stmt = $this->conexao->prepare('SELECT * FROM estados ORDER BY sigla ASC');
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_OBJ);
            echo json_encode($data);

        }

        
        



    }
?>