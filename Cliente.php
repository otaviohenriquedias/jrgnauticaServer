<?php
session_start();
    class Cliente {
        private $conexao;
        private $id_cliente;
        private $heat;
        private $nome;
        private $sobrenome;
        private $sexo;
        private $data_nascimento;
        private $data_cadastro;
        private $contato;
        private $email;
        private $atendente_id;
        private $atendente_nome;
        private $codAtendente;
        private $atendente;
        function __construct(Conexao $conexao){
            $this->conexao = $conexao->conectar();
            
        }
        function __get($name){
            return $this->$name;    
        }
        function __set($name, $value){
            $this->$name = $value;
            
        }

        private function converteData($data){
            $data = strtotime(str_replace('/','-', $data)); 
            $data2 = date('Y/m/d',$data);
            return $data2;
        }
        private function verficaContato ($celular){
            $query = 
            '
                SELECT 
                    * 
                FROM
                    clientes
                WHERE    
                    contato = :contato
            ';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':contato', $celular);
            if($stmt->execute()){
                $retorno = $stmt->fetchAll(PDO::FETCH_OBJ);
                if (empty($retorno)){
                    return true;
                }
                else {
                    return false;
                }
            }
        }
        public function cadastrarCliente (){
            $query = 
            '
            SET FOREIGN_KEY_CHECKS = 0;
            INSERT INTO clientes(nome, sobrenome, data_nascimento, contato, email, Sexo_id_Sexo, resp_atend, heat) 
            VALUES (:nome, :sobrenome, :data_nascimento, :contato, :email, :sexo, 3, 1)
            ';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':nome', $this->nome);
            $stmt->bindValue(':sobrenome', $this->sobrenome);
            $stmt->bindValue(':data_nascimento', $this->converteData($this->data_nascimento));
            $stmt->bindValue(':contato', $this->contato);
            $stmt->bindValue(':email', $this->email);
            $stmt->bindValue(':sexo', intval($this->sexo));
            if($this->verficaContato($this->contato)){
                $stmt->execute();
                echo '{"status":"Cadastrado", "Mensagem" : "Cliente <b>'.$this->nome.'</b> , cadastrado com sucesso! Os dados do novo cliente já foram salvos no banco de dados.", "type" : "success"}';
            }
            else{
                echo '{"status":"Ops!", "Mensagem" : "Atenção! Este cliente já possui um cadastro, tente outros dados. Verifique se o telefone já foi utilizado.", "type" : "error"}';
            }
            
            }
            public function listarClientes (){
                $query = 
                '
                SELECT 
                    *
                FROM 
                    clientes 
                ORDER BY 
                    nome asc    
                ';

                $stmt = $this->conexao->prepare($query);
            if($stmt->execute()){
                $fabricantes = $stmt->fetchAll(PDO::FETCH_OBJ);
                echo json_encode($fabricantes);
            }


            }
            public function consultaCliente ($nome, $heatConsulta, $ordem){

                if($heatConsulta == 'todos'){
                    $heatConsulta = '1,2,3';
                }

                switch ($ordem) {
                    case 1:
                        $ordem = 'DESC';
                        break;
                    case 2:
                        $ordem = 'ASC';
                        break;
                }
                $query = 
                '
                SELECT 
                    *
                FROM
                    clientes cli
                    INNER JOIN sexo sex ON (cli.Sexo_id_Sexo = sex.id_Sexo)
                WHERE
                    heat IN ('.$heatConsulta.') AND (nome LIKE "'.$nome.'%" OR sobrenome LIKE "'.$nome.'%")
                ORDER BY
                    data_cadastro '.$ordem.'
                ';
                $stmt = $this->conexao->prepare($query);
                //$stmt->bindValue(':heat', $heatConsulta);
                // $stmt->bindValue(':sobrenome', $nome);
                if ($stmt->execute()){
                    $dados = $stmt->fetchAll(PDO::FETCH_OBJ);
                    $readyonly = 'disabled';
                    if($_SESSION['tipo'] == 1){$readyonly = '';}
                    foreach ($dados as $indice => $valor) {
                        $link = preg_replace("/[^0-9]/", "", $valor->contato);
                        $img = 'woman';
                        if ($valor->id_Sexo == 1){$img= 'man';}
                        switch ($valor->heat) {
                            case 1:
                                $heat = 'hot';
                                break;
                            case 2:
                                $heat = 'warm';
                                break;
                            case 3:
                                $heat = 'cold';
                                break;
                    
                        }
                        echo '
                            <div class="article border-bottom" id="'.$valor->id_clientes.'">
                                    <div class="col-xs-12">
                                        <div class="row">
                                            <div class="col-xs-1 col-md-1">
                                                <img src="assets/'.$img.'.png" width=80 class="img-fluid img-thumbnail" />
                                            </div>
                                            <div class="col-xs-9 col-md-9">
                                                <h4><a href="edit_cliente.php?id_cli='.$valor->id_clientes.'" target="_blank">'.$valor->nome.' '.$valor->sobrenome.'</a> <img src="assets/heats/'.$heat.'.png" width=20 class="img-fluid" /></h4>
                                                <p><b>E-mail:</b> '.$valor->email.' - <b>Contato:</b> '.$valor->contato.' - <b>Cadastrado em: </b>'.$valor->data_cadastro.'</p>
                                            </div>
                                            <div class="col-xs-1 col-md-1">
                                            <a href="http://wa.me/'.$link.'" target= "_blank">
                                            <button type="button" class="btn btn-success px-3"><i class="fa fa-xl fa-whatsapp" aria-hidden="true" style="color:white"></i>
                                            </a></button>
                                            </div>
                                            <div class="col-xs-1 col-md-1 align-items-center">
                                            <button onClick="deleteCliente('.$valor->id_clientes.')" '.$readyonly.' type="button" class="btn btn-danger px-3"><i class="fa fa-xl fa-trash-o" aria-hidden="true" style="color:white"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            ';
                    }
                }
            }

            public function setCliente ($id){
                $query = 
                '    SELECT 
                        clientes.nome, clientes.sobrenome, clientes.Sexo_id_Sexo, clientes.data_nascimento, clientes.data_cadastro, clientes.heat, clientes.contato,
                        clientes.email, clientes.id_clientes, usuario.id_usuario, clientes.resp_atend, usuario.nome AS atentende
                    FROM
                        clientes
                        LEFT JOIN usuario ON (clientes.resp_atend = usuario.id_usuario)
                    WHERE
                        id_clientes = :id_cliente               
                ';
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id_cliente', intval($id));
                if($stmt->execute()){
                    $stDados = $stmt->fetchAll(PDO::FETCH_OBJ);
                    $this->__set('nome', $stDados[0]->nome);
                    $this->__set('sobrenome', $stDados[0]->sobrenome);
                    $this->__set('sexo', $stDados[0]->Sexo_id_Sexo);
                    $this->__set('data_nascimento', $stDados[0]->data_nascimento);
                    $this->__set('data_cadastro', $stDados[0]->data_cadastro);
                    $this->__set('contato', $stDados[0]->contato);
                    $this->__set('email', $stDados[0]->email);
                    $this->__set('id_cliente', $stDados[0]->id_clientes);
                    $this->__set('atendente_id', $stDados[0]->id_usuario);
                    $this->__set('codAtendente', $stDados[0]->resp_atend);
                    $this->__set('atendente_nome', $stDados[0]->atentende);
                    $this->__set('heat', $stDados[0]->heat);

                
                }
            }
            public function atualizaCliente ($id){
                $query =
    
                '
                SET FOREIGN_KEY_CHECKS=0;
                UPDATE 
                    clientes
                 SET
                    nome = :nome, sobrenome = :sobrenome, data_nascimento = :data, contato = :contato, email = :email, Sexo_id_Sexo = :sexo, resp_atend = :atendente, heat = :heat
                    
                 WHERE
                    id_clientes = :id
    
                 ';
    
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':nome', $this->nome);
                $stmt->bindValue(':data', $this->data_nascimento);
                $stmt->bindValue(':contato', $this->contato);
                $stmt->bindValue(':email', $this->email);
                $stmt->bindValue(':sexo', $this->sexo);
                $stmt->bindValue(':sobrenome', $this->sobrenome);
                $stmt->bindValue(':atendente', $this->atendente);
                $stmt->bindValue(':heat', $this->heat);
                $stmt->bindValue(':id', $id);
                if($stmt->execute()){
                    echo '{"status":"Atualizado!", "mensagem" : "Cliente atualizado com sucesso!", "type" : "success"}';
                }
                else{
                    echo '{"status":"Ops!", "mensagem" : "Algo deu errado, tente novamente...", "type" : "error"}';
                }
    
        }
        public function deletaCliente ($id, $conexao){
            require 'procura.php';
            $removeCliente = new Procura($conexao);
            $removeCliente->deleteProcuraAll($id);
            $query = 
            '
                SET FOREIGN_KEY_CHECKS = 0;
                DELETE FROM
                    clientes
                WHERE
                    id_clientes= '.$id;
    
            $stmt = $this->conexao->prepare($query);
            $stmt->execute();
        }

        public function getAniversariantes ($NUMBER){

            $query = 
            '
            SELECT 
                *
            FROM
                clientes cli
                INNER JOIN sexo sex ON (cli.Sexo_id_Sexo = sex.id_Sexo)
            WHERE
                DAY(data_nascimento) = DAY(NOW()) AND MONTH(data_nascimento) = MONTH(NOW())
               
            ';
            $stmt = $this->conexao->prepare($query);
            if ($stmt->execute()){
                $dados = $stmt->fetchAll(PDO::FETCH_OBJ);
                $readyonly = 'disabled';
                if($_SESSION['tipo'] == 1){$readyonly = '';}

                if($NUMBER == false){
                foreach ($dados as $indice => $valor) {
                    $link = preg_replace("/[^0-9]/", "", $valor->contato);
                    $img = 'woman';
                    if ($valor->id_Sexo == 1){$img= 'man';}
                    switch ($valor->heat) {
                        case 1:
                            $heat = 'hot';
                            break;
                        case 2:
                            $heat = 'warm';
                            break;
                        case 3:
                            $heat = 'cold';
                            break;
                
                    }
                    echo '
                        <div class="article border-bottom" id="'.$valor->id_clientes.'">
                                <div class="col-xs-12">
                                    <div class="row">
                                        <div class="col-xs-1 col-md-1">
                                            <img src="assets/'.$img.'.png" width=80 class="img-fluid img-thumbnail" />
                                        </div>
                                        <div class="col-xs-9 col-md-9">
                                            <h4><a href="edit_cliente.php?id_cli='.$valor->id_clientes.'" target="_blank">'.$valor->nome.' '.$valor->sobrenome.'</a></h4>
                                            <p><b>E-mail:</b> '.$valor->email.' - <b>Contato:</b> '.$valor->contato.' - <b>Heat: </b><img src="assets/heats/'.$heat.'.png" width=20 class="img-fluid" /></p>
                                        </div>
                                        <div class="col-xs-1 col-md-1">
                                        <a href="http://wa.me/'.$link.'" target= "_blank">
                                        <button type="button" class="btn btn-success px-3"><i class="fa fa-xl fa-whatsapp" aria-hidden="true" style="color:white"></i>
                                        </a></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                        ';
                }
                }
                else {
                    if ($stmt->rowCount() == 0){
                        echo '';
                    }
                    echo '<span class="badge text-bg-danger">'.$stmt->rowCount().'</span>';

                }
            }
        }

           
    }
?>