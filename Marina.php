<?php
    class Marina{
        private $id_marinas;
        private $cidade;
        private $contato;
        private $estado;
        private $conexao;
        private $conexaoPool;
        function __construct(PDO $conexao, Conexao $conexaoPool){
            $this->conexao = $conexao;
            $this->conexaoPool = $conexaoPool;
        }
        public function __destruct() {
            $this->conexaoPool->liberarConexao($this->conexao);
        }
        
        public function __set($name, $value)
        {
            $this->$name = $value; 
        }
        public function __get($name)
        {
            return $this->$name;
        }

        public function cadastraMarina()
        {
            $this->conexao->beginTransaction();
            try {
                $stmt = $this->conexao->prepare(' 
                INSERT INTO marina(cidade, contato, Estados_id_estados, nome) 
                VALUES (:cidade, :contato, :estado, :nome)    
                ');
                $stmt->bindValue(':cidade', $this->cidade);
                $stmt->bindValue(':contato', $this->contato);
                $stmt->bindValue(':estado', $this->estado);
                $stmt->bindValue(':nome', $this->nome);
                if($stmt->execute()){
                    $this->conexao->commit();
                    echo '{"status":"Cadastrada!", "mensagem" : "Marina cadastrada com sucesso!", "type" : "success"}';
                }
            } catch (Exception $e) {
                $this->conexao->rollBack();
                echo json_encode([
                    "status" => "Ops!",
                    "mensagem" => "Algo deu errado, tente novamente... (" . $e->getMessage() . ")",
                    "type" => "error"
                ]);
            }
            
        }
        public function getMarinas (){
            $stmt = $this->conexao->prepare(
                'SELECT 
                    * 
                 FROM 
                    marina as ma
                    INNER JOIN estados as es ON (ma.Estados_id_estados = es.id_estados) 
                ORDER BY nome ASC
                '
            );
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_OBJ);
            echo json_encode($data);
        
        }

        public function listaMarina (){
            $stmt = $this->conexao->prepare(' 
            SELECT  
                *
            FROM 
                marina mar
                INNER JOIN estados est ON (mar.Estados_id_estados = est.id_estados)
            ORDER BY
                est.sigla ASC
                ');
            $stmt->execute();
            $dados = $stmt->fetchAll(PDO::FETCH_OBJ);
            //session_start();
            foreach($dados as $key => $valor){
                        echo '
                        <div class="article border-bottom" id="'.$valor->id_marina.'">
                                <div class="col-xs-12">
                                    <div class="row">
                                        <div class="col-xs-11 col-md-11">
                                            <h4>'.$valor->nome.'</a></h4>
                                            <p><b> Contato: </b>'.$valor->contato.' <b> Cidade: </b>'.$valor->cidade.' <b>Estado:</b> '.$valor->descricao.' 
                                            </p>

                                        </div>
                                        <div class="col-xs-2 col-md-1 align-items-center">
                                            <button onClick="deleteMarina('.$valor->id_marina.')" type="button" class="btn btn-danger px-3"><i class="fa fa-xl fa-trash-o" aria-hidden="true" style="color:white"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                        ';


            }
        }

        public function listaMarinaEdit ($id_embarcacao){
            $stmt = $this->conexao->prepare(' 
            SELECT  
                *
            FROM 
                marina mar
                INNER JOIN estados est ON (mar.Estados_id_estados = est.id_estados)
            WHERE
                id_marina = :id
                ');
            $stmt->bindValue(':id', $id_embarcacao);
            $stmt->execute();
            $dados = $stmt->fetchAll(PDO::FETCH_OBJ);
            $data = [
                "VALUE" => $dados[0]->id_marina,
                "NOME" => $dados[0]->nome,
                "UF" => $dados[0]->sigla,
            ];
            return $data;
            
        }
        public function deletaMarina ($id){
            $query = 
            '
                DELETE FROM
                    marina
                WHERE
                    id_marina = '.$id;

            $stmt = $this->conexao->prepare($query);
            $stmt->execute();

        }
        




    }
?>