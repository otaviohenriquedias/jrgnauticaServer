<?php
    class BEM {
        private $id_bem;
        private $modelo_bem;
        private $fabricante_bem;
        private $marina_bem;
        private $tipo_bem;
        private $tamanho_bem;
        private $ano_bem;
        private $motores_bem;
        private $conexao;
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
        

        public function cadastrarBem (){
            $query = 
            '
            INSERT INTO embmarinas 
            (modelo, pes,  Marina_id_marina, Tipo_id_tipo, Fabricantes_id_fabricantes, quant_motores, ano)
            VALUES (:modelo, :pes, :marina, :tipo, :fabricante, :motores, :ano)
            ';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':modelo', $this->modelo_bem);
            $stmt->bindValue(':pes', $this->tamanho_bem);
            $stmt->bindValue(':marina', $this->marina_bem);
            $stmt->bindValue(':tipo', $this->tipo_bem);
            $stmt->bindValue(':fabricante', $this->fabricante_bem);
            $stmt->bindValue(':motores', $this->motores_bem);
            $stmt->bindValue(':ano', $this->ano_bem);
            if($stmt->execute()){
                echo '{"status":"Cadastrado", "mensagem" : "B.E.M cadastrada com sucesso! Os dados já foram salvos no banco de dados.", "type" : "success"}';
            }
            else {
                echo '{"status":"Ops!", "mensagem" : "Ops! Ocorreu um erro, tente novamente...", "type" : "error"}';
            }

            
            }



    }
?>