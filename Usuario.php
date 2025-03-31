<?php
session_start();
    class Usuario {
        private $id_usuario;
        private $cpf;
        private $user;
        private $nome;
        private $senha;
        private $tipo;
        private $conexao;
        private $conexaoPool;

        function __construct(PDO $conexao, Conexao $conexaoPool){
            $this->conexao = $conexao;
            $this->conexaoPool = $conexaoPool;
        }
        public function __destruct() {
            $this->conexaoPool->liberarConexao($this->conexao);
        }

        public function __get($name)
        {
            return $this->$name;
        }
        public function __set($name, $value)
        {
            $this->$name = $value;
        }
        
        public function logarUser ($user, $pass){

            $stmt = $this->conexao->prepare(' 
            SELECT  
                * 
            FROM 
                usuario user
                INNER JOIN tipousuario tpuser ON (user.TipoUsuario_id_tipo_usuario = tpuser.id_tipo_usuario)
            WHERE 
                cpf = :cpf  AND senha = :senha ');
            $stmt->bindValue(':cpf', $user);
            $stmt->bindValue(':senha',sha1($pass));
            if($stmt->execute()){
                $data = $stmt->fetchAll(PDO::FETCH_OBJ);
                if(count($data) != 0 ){
                    $reglogin = $this->conexao->prepare("UPDATE usuario SET reg_login = CURRENT_TIMESTAMP WHERE id_usuario = ".$data[0]->id_usuario);
                    $reglogin->execute();
                    echo '{"status":"Autenticado!", "mensagem" : "Você será redirecionado em alguns instante...", "type" : "success"}';
                    //session_start();
                    $_SESSION['nome'] = $data[0]->nome;
                    $_SESSION['tipo'] = $data[0]->TipoUsuario_id_tipo_usuario;
                    $_SESSION['tipo-user'] = $data[0]->tipo_tp;
                    $_SESSION['id_usuario'] = $data[0]->id_usuario;

                }
                else{
                    echo '{"status":"Ops!", "mensagem" : "CPF e senha não encontrados, verifique os dados e tente novamente!", "type" : "error"}';
                }
            }
        }
        public function cadastraUsuario (){
            $this->conexao->beginTransaction();
            $stmt = $this->conexao->prepare(' 
            INSERT INTO usuario(cpf, nome, senha, TipoUsuario_id_tipo_usuario, reg_login) VALUES (:cpf, :nome, :senha, :tipo, CURRENT_TIMESTAMP)    
            ');
            try {
                $stmt->bindValue(':cpf', $this->cpf);
                $stmt->bindValue(':nome', $this->nome);
                $stmt->bindValue(':senha', sha1($this->senha));
                $stmt->bindValue(':tipo', $this->tipo);
                if($stmt->execute()){
                    $this->conexao->commit();
                    echo '{"status":"Cadastrado!", "mensagem" : "Broker cadastrado com sucesso!", "type" : "success"}';
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

        public function listaUsuario (){
            $stmt = $this->conexao->prepare(' 
            SELECT  
                * 
            FROM 
                usuario user
                INNER JOIN tipousuario tpuser ON (user.TipoUsuario_id_tipo_usuario = tpuser.id_tipo_usuario)
           
                ');
            $stmt->execute();
            $dados = $stmt->fetchAll(PDO::FETCH_OBJ);
            //session_start();
            foreach($dados as $key => $valor){
                $readonly ='';
                if($valor->id_usuario == $_SESSION['id_usuario'])
                    {
                        $readonly ='disabled';
                    }
                        echo '
                        <div class="article border-bottom" id="'.$valor->id_usuario.'">
                                <div class="col-xs-12">
                                    <div class="row">
                                        <div class="col-xs-11 col-md-11">
                                            <h4><a href="edit_usuario.php?id_user='.$valor->id_usuario.'" target="_blank">'.$valor->nome.'</a></h4>
                                            <p><b> Último acesso: </b>'.$valor->reg_login.' <b>Permissões: </b>'.$valor->tipo_tp.' <b>CPF:</b> '.$valor->cpf.' 
                                            <b>Senha:</b> As senhas estão criptografadas em SHA1, impossibilitando sua descriptografia.
                                            </p>

                                        </div>
                                        <div class="col-xs-2 col-md-1 align-items-center">
                                            <button onClick="deleteUser('.$valor->id_usuario.')" '.$readonly.' type="button" class="btn btn-danger px-3"><i class="fa fa-xl fa-trash-o" aria-hidden="true" style="color:white"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                        ';


            }
        }
        public function listaBroker (){
            $stmt = $this->conexao->prepare(' 
            SELECT  
                * 
            FROM 
                usuario user
                INNER JOIN tipousuario tpuser ON (user.TipoUsuario_id_tipo_usuario = tpuser.id_tipo_usuario)
           
                ');
            $stmt->execute();
            $dados = $stmt->fetchAll(PDO::FETCH_OBJ);
            echo json_encode($dados);
        }
        public function deletaUsuario ($id){
            $query = 
            '
                DELETE FROM
                    usuario
                WHERE
                    id_usuario= '.$id;

            $stmt = $this->conexao->prepare($query);
            $stmt->execute();

        }

        public function editaUsuario ($getId, $retorno){
            $query_consulta =
            '
                SELECT 
                    *
                FROM
                    usuario
                WHERE
                    id_usuario = :getId
            ';

            $stmt = $this->conexao->prepare($query_consulta);
            $stmt->bindValue(':getId', intval($getId));
            if($stmt->execute()){
                $dados = $stmt->fetchAll(PDO::FETCH_OBJ);
                return $dados[0]->$retorno;
            }
        }
        public function atualizaUsuario ($id){
            $this->conexao->beginTransaction();
            $query =

            '
            UPDATE 
                usuario
             SET
                cpf = :cpf, nome = :nome, senha = :senha, TipoUsuario_id_tipo_usuario = :tipo, reg_login = CURRENT_TIMESTAMP
             WHERE
                id_usuario = :id

             ';
            try {
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':cpf', $this->cpf);
                $stmt->bindValue(':nome', $this->nome);
                $stmt->bindValue(':senha', sha1($this->senha));
                $stmt->bindValue(':tipo', $this->tipo);
                $stmt->bindValue(':id', $id);
                if($stmt->execute()){
                    $this->conexao->commit();
                    echo '{"status":"Atualizado!", "mensagem" : "Broker atualizado com sucesso!", "type" : "success"}';
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

    }
?>