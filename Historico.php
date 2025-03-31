<?php
    class Historico {
        private $id_usuario;
        private $historico;
        private $id_cliente;
        private $ocorrencia;
        private $conexao;
        function __construct(Conexao $conexao){
            $this->conexao = $conexao;
            
        }
        public function __set($name, $value)
        {
            $this->$name = $value; 
        }
        public function __get($name)
        {
            return $this->$name;
        }

        public function createHistorico (){
            $query = 
            '
                INSERT INTO 
                    tb_historico
                    (id_usuario, historico, ocorrencia,	id_cliente )
                VALUES
                    (:id, :historico, :ocorrencia, :id_cliente)
            ';
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':id', intval($this->id_usuario));
            $stmt->bindValue(':historico', $this->historico);
            $stmt->bindValue(':ocorrencia', $this->ocorrencia);
            $stmt->bindValue(':id_cliente', $this->id_cliente);
            if($stmt->execute()){
                echo '{"status":"Cadastrado!", "mensagem" : "Histórico cadastrado com sucesso!", "type" : "success"}';
            }
            else{
                echo '{"status":"Ops!", "mensagem" : "Ocorreu uma falha na tentativa do cadastro. Consulte o suporte.", "type" : "error"}';
            }
        }
        public function getHistory (){
            $query = 
            '
                SELECT 
                    h.id_cliente,
                    h.id_usuario,
                    u.nome,
                    DATE_FORMAT(h.data_registro,"%d/%m/%Y %H:%i") as data,
                    h.ocorrencia,
                    h.historico
                FROM 
                    tb_historico h
                    INNER JOIN usuario u ON (h.id_usuario = u.id_usuario)
                WHERE
                    h.id_cliente = :id_cliente
                ORDER BY 
                    h.data_registro DESC
            ';
            $stmt = $this->conexao->prepare($query);
            session_start();
            $stmt->bindValue(':id_cliente', intval($this->id_cliente));
            if($stmt->execute()){
                $dados = $stmt->fetchAll(PDO::FETCH_OBJ);
                  foreach ($dados as $key => $value) {
                    echo 
                    '   <div class="historico">
                        <div class="card">
                        <div class="card-header">
                            <span class="data-historico">
                            <i class="fa fa-clock-o" aria-hidden="true"></i>&nbsp;&nbsp'.$value->data.'
                            </span>
                            <span class="usario-historico">
                            <i class="fa fa-user" aria-hidden="true"></i>&nbsp;&nbsp;'.$value->nome.'
                            </span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">'.$value->ocorrencia.'</h5>
                            <p class="card-text conteudo-histórico">'.$value->historico.'</p>
                        </div>
                        </div>
                        </div>
                    ';
                    
                }
            }

        }
    }
?>