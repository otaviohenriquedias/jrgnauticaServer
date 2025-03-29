<?php
    class Captador {
        private $id_captador;
        private $nome;
        private $contato;
        private $ativo;
        private $conexao;
        private $categoria;
        private $empresa;
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
        
        public function listaCaptador (){
            $query = 
            '
                SELECT 
                    *
                FROM 
                    captador
                ORDER BY 
                    nome_captador asc    
            ';

            $stmt = $this->conexao->prepare($query);
            if($stmt->execute()){
                $captador = $stmt->fetchAll(PDO::FETCH_OBJ);
                echo json_encode($captador);
            }
        }

        public function cadastrarCaptador (){
            $query = 
            '
            INSERT INTO captador(nome_captador, contato, ativo, empresa, categoria) 
            VALUES (:nome, :contato, :ativo, :empresa, :categoria)
            ';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':nome', $this->nome);
            $stmt->bindValue(':contato', $this->contato);
            $stmt->bindValue(':empresa', $this->empresa);
            $stmt->bindValue(':categoria', $this->categoria);
            $stmt->bindValue(':ativo',0);
            if($stmt->execute()){
                echo '{"status":"Cadastrado", "Mensagem" : "Captador cadastrado com sucesso! Os dados do novo captador já foram salvos no banco de dados.", "type" : "success"}';
            }
            
            }
            public function listaCaptadores (){
                $stmt = $this->conexao->prepare(' 
                SELECT  
                    * 
                FROM 
                    captador
                    ');
                $stmt->execute();
                $dados = $stmt->fetchAll(PDO::FETCH_OBJ);
                session_start();
                foreach($dados as $key => $valor){
                    $readonly = '';
                    $link2 = '</a>';
                    $link = '<a href="edit_captadores.php?id_captador='.$valor->id_captador.'" target="_blank">';
                    if($_SESSION['tipo'] == 2)
                        {
                            $link = '';
                            $readonly ='disabled';
                            $link2 = '';
                        }
                    $categoria = '';
                    if($valor->categoria == 0 ){
                        $categoria = 'Marinheiro';
                    }
                    else{
                        $categoria = 'Empresa - ';
                    }
                    echo '
                    <div class="article border-bottom" id="'.$valor->id_captador.'">
                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="col-xs-11 col-md-11">
                                        <h4>'.$link.$valor->nome_captador.$link2.'</h4>
                                        <p><b>Categoria: </b>'.$categoria.' '.$valor->empresa.' <b>Contato: </b>'.$valor->contato.'</p>

                                    </div>
                                    <div class="col-xs-2 col-md-1 align-items-center">
                                        <button onClick="deleteCaptador('.$valor->id_captador.')" '.$readonly.' type="button" class="btn btn-danger px-3"><i class="fa fa-xl fa-trash-o" aria-hidden="true" style="color:white"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>
                    </div>
                    ';
    
                    }
                }
                public function editaCaptador ($getId, $retorno){
                    $query_consulta =
                    '
                        SELECT 
                            *
                        FROM
                            captador
                        WHERE
                            id_captador = :getId
                    ';
        
                    $stmt = $this->conexao->prepare($query_consulta);
                    $stmt->bindValue(':getId', intval($getId));
                    if($stmt->execute()){
                        $dados = $stmt->fetchAll(PDO::FETCH_OBJ);
                        return $dados[0]->$retorno;
                    }
                }
                public function deletaCaptador($id){
                    $query = 
                    '   SET FOREIGN_KEY_CHECKS = 0;
                        DELETE FROM
                            captador
                        WHERE
                            id_captador= '.$id;
        
                    $stmt = $this->conexao->prepare($query);
                    try {
                        if($stmt->execute()){
                            echo '{"status":"Concluído", "mensagem" : "Captador removido da base de dados.", "type" : "success"}';
                        }
                    } catch (PDOException $e) {
                        echo '{"status":"Ops!", "mensagem" : "Captador não pode ser excluido, pois possúi vinculos em alguma (s) embarcações... ", "type" : "error"}';
                    }
                  
                }
                public function atualizaCaptador ($id){
                    $query =
        
                    '
                    UPDATE 
                        captador
                     SET
                        nome_captador= :nome, contato = :contato, empresa = :empresa, categoria= :categoria
                     WHERE
                        id_captador = :id
        
                     ';
        
                    $stmt = $this->conexao->prepare($query);
                    $stmt->bindValue(':nome', $this->nome);
                    $stmt->bindValue(':contato', $this->contato);
                    $stmt->bindValue(':empresa', $this->empresa);
                    $stmt->bindValue(':categoria', $this->categoria);
                    $stmt->bindValue(':id', $id);
                    if($stmt->execute()){
                        echo '{"status":"Atualizado!", "mensagem" : "Captador atualizado com sucesso!", "type" : "success"}';
                    }
                    else{
                        echo '{"status":"Ops!", "mensagem" : "Algo deu errado, tente novamente...", "type" : "error"}';
                    }
        
            }
                
            



    }
?>
