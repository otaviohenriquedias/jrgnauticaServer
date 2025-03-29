<?php
    class Estados{
        private $id_estado;
        private $sigla;
        private $descricao;
        function __construct(Conexao $conexao){
            $this->conexao = $conexao->conectar();
            
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