<?php
session_start();
    class Procura {
        private $conexao;
        private $conexaoPool;
        private $cadastrante;
        private $id_procura;
        private $propulsor;
        private $valor_procura;
        private $quant_motores;
        private $id_cliente;
        private $fabricante;
        private $modelo;
        private $tipo;
        private $horas;
        private $potencia;
        private $ano;
        private $combustivel;
        private $tamanho;
        private $data_procura;
        private $ativo = true;
        private $listaprocuras;
        private $encontradas;
        private $tamanho_min_procura;
        private $tamanho_max_procura;
        private $valor_min_procura;
        private $valor_max_procura;

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

        public function cadastraProcura (){
            $this->conexao->beginTransaction();
            $query = 
            '
            INSERT INTO procuras(modelo, horas, potencia, ano, ativo, p_fabricante, Clientes_id_clientes, p_combustivel, p_tipo, Usuario_id_usuario, tamanho, quant_motores, valor, propulsor) 
            VALUES (:modelo,:horas,:potencia, :ano, :ativo, :fabricante, :cliente, :combustivel,:tipo, :usuario, :tamanho, :quantmotores, :valor, :propulsor)
            '
            ;
            try {
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':modelo', $this->modelo);
                $stmt->bindValue(':horas', intval($this->horas));
                $stmt->bindValue(':potencia', intval($this->potencia));
                $stmt->bindValue(':ano', intval($this->ano));
                $stmt->bindValue(':ativo', $this->ativo);
                $stmt->bindValue(':fabricante', intval($this->fabricante));
                $stmt->bindValue(':cliente', intval($this->id_cliente));
                $stmt->bindValue(':combustivel', intval($this->combustivel));
                $stmt->bindValue(':tipo', intval($this->tipo));
                $stmt->bindValue(':usuario', $this->cadastrante);
                $this->tamanho_min_procura = str_replace(',', '.', $this->tamanho_min_procura);
                $this->tamanho_max_procura = str_replace(',', '.', $this->tamanho_max_procura);
                $stmt->bindValue(':tamanho', $this->tamanho_min_procura.'-'.$this->tamanho_max_procura);
                $stmt->bindValue(':quantmotores', intval($this->quant_motores));
                $stmt->bindValue(':valor', $this->valor_min_procura.'-'.$this->valor_max_procura);
                $stmt->bindValue(':propulsor', intval($this->propulsor));
                $stmt->execute();
                $this->conexao->commit();
            } catch (Exception $e) {
                $this->conexao->rollBack();
                echo json_encode([
                    "status" => "Ops!",
                    "mensagem" => "Algo deu errado, tente novamente... (" . $e->getMessage() . ")",
                    "type" => "error"
                ]);
            }

        }
        public function listarProcuras ($id){
            $query = 
            '
                SELECT
                    *
                FROM
                    procuras  pro
                    INNER JOIN clientes cli ON (pro.Clientes_id_clientes = cli.id_clientes)
                    INNER JOIN usuario usu ON (pro.Usuario_id_usuario = usu.id_usuario )
                WHERE
                    Clientes_id_clientes = :id
                ORDER BY    
                data_procura ASC
            ';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':id', $id);
            if($stmt->execute());
            $dados = $stmt->fetchAll(PDO::FETCH_OBJ);            
            foreach ($dados as $key => $valor) {
                if ($valor->modelo == null){$valor->modelo = 'Todos';}
                if ($valor->p_fabricante == 5000){$valor->p_fabricante = 'Todos';}else{$valor->p_fabricante = $this->listaFabricantes($valor->p_fabricante);}
                if ($valor->p_tipo == 5000){$valor->p_tipo = 'Todos';}else{$valor->p_tipo = $this->listaTipo($valor->p_tipo);}
                if ($valor->p_combustivel == 5000){$valor->p_combustivel = 'Todos';} else {$valor->p_combustivel = $this->listaComb($valor->p_combustivel);}
                if ($valor->ano == 5000){$valor->ano = 'Todos';} else {$valor->ano = 'Igual ou acima de '.$valor->ano;}
                if ($valor->quant_motores == 5000){$valor->quant_motores = 'Todos';}
                if ($valor->tamanho == 5000){$valor->tamanho = 'Todos';}
                if ($valor->horas == 5000){$valor->horas = 'Todos';}
                if ($valor->propulsor == 5000){$valor->modelopropulsor = 'Todos';}
                if ($valor->propulsor == 1){$valor->modelopropulsor = 'Pé de galinha';}
                if ($valor->propulsor == 2){$valor->modelopropulsor = 'Rabeta IPS';}
                if ($valor->propulsor == 3){$valor->modelopropulsor = 'Rabeta comum';}
                if ($valor->propulsor == 4){$valor->modelopropulsor = 'Motor de popa';}
                if ($valor->potencia == 5000){$valor->potencia = 'Todos';}else{$valor->potencia = $valor->potencia.' HP';}
                switch ($valor->valor ) {
                    case 5000:
                        $valor->valor = 'Todos';
                        break;
                    case 1000000:
                        $valor->valor = 'Até R$ 1 Milhão';
                        break;
                    case 2000000:
                        $valor->valor = 'De R$ 1 Milhão a R$ 2 Milhões';
                        break;
                    case 2500000:
                        $valor->valor = 'De R$ 2 Milhões a R$ 2,5 Milhões';
                        break;
                    case 3000000:
                        $valor->valor = 'De R$ 2 Milhões a R$ 3 Milhões';
                        break;
                    case 4000000:
                        $valor->valor = 'De R$ 3 Milhões a R$ 4 Milhões';
                        break;
                    case 6000000:
                        $valor->valor = 'De R$ 4 Milhões a R$ 6 Milhões';
                        break;
                    case 6000:
                        $valor->valor = 'Acima de R$ 6 Milhões';
                        break;
                }

                switch ($valor->tamanho ) {
                    case 5000:
                        $valor->tamanho = 'Todos';
                        break;
                    case 20:
                        $valor->tamanho = 'Entre 20 a 31';
                        break;
                    case 31:
                        $valor->tamanho = 'Entre 31 a 38';
                        break;
                    case 38:
                        $valor->tamanho = 'Entre 38 a 50';
                        break;
                    case 50:
                        $valor->tamanho = 'Entre 50 a 60';
                        break;
                    case 60:
                        $valor->tamanho = 'Entre 60 a 70';
                        break;
                    case 70:
                        $valor->tamanho = 'Entre 75 a 83';
                        break;
                    case 83:
                        $valor->tamanho = 'Entre 83 a 100';
                        break;
                    case 90:
                        $valor->tamanho = 'Acima de 100';
                        break;
                }
                echo ' 
                <div class="alert bg-primary" role="alert" id="content'.$valor->id_procuras.'">
                <div class="row">
                    <div class="col-lg-1 align-center-center">
                        <img src="assets/money-finder.png" width=50 class="img-fluid" />
                    </div>
                    <div class="col-lg-8">
                        <b>Fabricante:</b> '.$valor->p_fabricante.'
                        <b>Modelo:</b> '.$valor->modelo.'
                        <b>Tipo:</b> '.$valor->p_tipo.'
                        <b>Combustível:</b> '.$valor->p_combustivel.'
                        <b>Ano:</b> Acima de '.$valor->ano.'
                        <b>Tamanho:</b> '.$valor->tamanho.'<br>
                        <b>Potência:</b> '.$valor->potencia.'
                        <b>Horas:</b> '.$valor->horas.'
                        <b>Motores:</b> '.$valor->quant_motores.'
                        <b>Valor:</b> '.$valor->valor.'
                        <b>Propulsor:</b> '.$valor->modelopropulsor.'
                        <div class="divider"></div>
                        <span style="border: solid 0.5px #787878; border-radius:5px; padding:2px">Cadastrado por: '.$valor->nome.'</span>
                        <span style="border: solid 0.5px #787878; border-radius:5px; padding:2px"> '.$valor->data_procura.'</span>
                    </div>
                    <div class="col-lg-3">
                        <a onclick= deleteProcura('.$valor->id_procuras.') class="pull-right" title="Excluir procura... Essa operação não poderá ser revertida!"><em class="fa fa-lg fa-close"></em></a>
                    </div>
                </div>
                </div>
            
            ';
                
            }
        }
        protected function listaFabricantes ($id){
            $query = 
            '
                SELECT 
                    *
                FROM
                    fabricantes
                WHERE
                    id_fabricantes = '.$id.'

            ';

            $stmt = $this->conexao->prepare($query);
            $stmt->execute();
            $fabricante = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $fabricante[0]->fabricante;

        }
        protected function listaTipo ($id){
            $query = 
            '
                SELECT 
                    *
                FROM
                    tipo
                WHERE
                    id_tipo = '.$id.'

            ';

            $stmt = $this->conexao->prepare($query);
            $stmt->execute();
            $fabricante = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $fabricante[0]->tipo;
        }

        protected function listaComb ($id){
            $query = 
            '
                SELECT 
                    *
                FROM
                    combustivel
                WHERE
                    id_combustivel = '.$id.'

            ';

            $stmt = $this->conexao->prepare($query);
            $stmt->execute();
            $fabricante = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $fabricante[0]->combustivel;

        }
        public function deleteProcura ($idprocura){
            $query = 
            '
                DELETE FROM
                    procuras
                WHERE
                    id_procuras= '.$idprocura;

            $stmt = $this->conexao->prepare($query);
            $stmt->execute();
        }
        public function deleteProcuraAll ($idprocura){
            $query = 
            '
                DELETE FROM
                    procuras
                WHERE
                Clientes_id_clientes= '.$idprocura;

            $stmt = $this->conexao->prepare($query);
            $stmt->execute();
        }

        public function setArray (){
            $query = 
            '
                SELECT
                    *
                FROM
                    procuras proc
                    INNER JOIN clientes cli ON (proc.Clientes_id_clientes = cli.id_clientes)
                WHERE
                    proc.Usuario_id_usuario = :id_user and cli.resp_atend = :resp_atend
                ORDER BY 
                    cli.heat ASC
                
            ';
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':id_user', $_SESSION['id_usuario']);
            $stmt->bindValue(':resp_atend', $_SESSION['id_usuario']);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_OBJ); 
            foreach ($data as $value) {
                if ($value->modelo == null){$modelo = '0 = 0';}else {$modelo = 'modelo LIKE "%'.$value->modelo.'%"';}
                if ($value->horas == 5000){$horas = 'AND 0 = 0';} else {$horas = 'AND horas <= '.$value->horas;}
                if ($value->propulsor == 5000){$propulsor = 'AND 0 = 0';} else {$propulsor = 'AND propulsor = '.$value->propulsor;}
                if ($value->potencia == 5000){$potencia = 'AND 0 = 0';} else {$potencia = 'AND potencia <= '.$value->potencia;}
                if ($value->ano == 5000){$ano = 'AND 0 = 0';} else {$ano = 'AND ano >= '.$value->ano;}
                if ($value->p_fabricante == 5000){$fabricante = 'AND 0 = 0';} else {$fabricante = 'AND Fabricantes_id_fabricantes = '.$value->p_fabricante;}
                if ($value->p_combustivel == 5000){$combustivel = 'AND 0 = 0';} else {$combustivel  = 'AND Combustivel_id_combustivel = '.$value->p_combustivel;}
                if ($value->p_tipo == 5000){$tipo = 'AND 0 = 0';} else {$tipo = 'AND Tipo_id_tipo = '.$value->p_tipo;}
                if ($value->quant_motores == 5000){$mtrs = 'AND 0 = 0';} else {$mtrs = 'AND quant_motor = '.$value->quant_motores;}
                //Quebra tamanho, transforma em inteiro e monta query.
                $novoTamanho = explode('-', $value->tamanho);
                $tamanho = 'AND (tamanho >= '.$novoTamanho[0].' AND tamanho <= '.$novoTamanho[1].')';
                //Quebra valor, transforma em inteiro e monta query.
                $novoValor = explode('-', $value->valor);
                $valor1 = str_replace('.', '', $novoValor[0]);
                $valor1 = str_replace(',', '.', $valor1);
                //------------------------------------------------
                $valor2 = str_replace('.', '', $novoValor[1]);
                $valor2 = str_replace(',', '.', $valor2);
                $valor = 'AND (valor >= '.$valor1.' AND valor <= '.$valor2.')';           
                $setQuery = '
                    SELECT
                        *
                    FROM
                        embarcacao emb
                        INNER JOIN fabricantes fab ON (emb.Fabricantes_id_fabricantes = fab.id_fabricantes)
                        INNER JOIN tipo tip ON (emb.Tipo_id_tipo = tip.id_tipo)
                        INNER JOIN combustivel comb ON (emb.Combustivel_id_combustivel = comb.id_combustivel)
                        INNER JOIN clientes cli ON (emb.Clientes_id_clientes = cli.id_clientes)
                        INNER JOIN propulsor prop ON (emb.propulsor = prop.id_propulsor)
                    WHERE
                        '.$modelo.' '.$horas.' '.$potencia.' '.$ano.' '.$fabricante.' '.$combustivel.' '.$tipo.' '.$mtrs.' '.$tamanho.' '.$valor.' '.' '.$propulsor.' '.'AND vendido = 0
                        ';
                        
                $this->listaprocuras[$value->Clientes_id_clientes][] = $setQuery;
                       
                        
                        
    
            }
                
            
        


        }

        public function Sincron ($array){
           foreach ($array as $key => $value) {
                for ($i=0; $i <= count($array[$key])-1 ; $i++) { 
                    $stmt = $this->conexao->prepare($value[$i]);
                    $stmt->execute();
                    $data = $stmt->fetchAll(PDO:: FETCH_OBJ);
                    foreach ($data as $chave => $valor) {
                        $this->encontradas [$key][] = array(
                            'id-barco' => $valor->id_embarcacao,
                            'fabricante' => $valor->fabricante,
                            'tamanho' => $valor->tamanho,
                            'modelo' => $valor->modelo,
                            'potencia' => $valor->potencia.' HP',
                            'horas' => $valor->horas,
                            'ano' => $valor->ano,
                            'quant-motor' => $valor->quant_motor.'x',
                            'modelo-motor' => $valor->modelo_motor,
                            'tipo' => $valor->tipo,
                            'combustivel' => $valor->combustivel,
                            'propietario' => $valor->nome.' '.$valor->sobrenome,
                            'id_propietario' => $valor->id_clientes,
                            'caminhoImg' => $valor->caminho_imagem,
                            'propulsor' => $valor->modelopropulsor,
                            'valor' => number_format($valor->valor, 2, ',', ''),
                      
                        );
                    }
                   
                   
                
                }
            }         
        }
        public function getInteressados ($idBarco){
            $queryBarco = 
            '
                SELECT 
                    emb.Fabricantes_id_fabricantes, emb.Tipo_id_tipo, emb.Combustivel_id_combustivel,
                    emb.ano, emb.tamanho, emb.potencia, emb.horas, emb.quant_motor, emb.propulsor, emb.valor, emb.modelo
                FROM
                    embarcacao AS emb
                WHERE 
                    id_embarcacao = :id

            ';
            $stmt = $this->conexao->prepare($queryBarco);
            $stmt->bindValue(':id', $idBarco);
            $stmt->execute();
            $barco = $stmt->fetch(PDO::FETCH_OBJ);

            $queryProcuras = 
            "
                SELECT
                    *
                FROM 
                    procuras proc
                    INNER JOIN clientes cli ON (proc.Clientes_id_clientes = cli.id_clientes)
                WHERE
                    (modelo = :modelo OR modelo = '') AND (horas <= :horas OR horas = 5000) AND (potencia <= :potencia OR potencia = 5000) AND
                    (ano <= :ano OR ano = 5000) AND (p_fabricante = :fabricante  OR p_fabricante = 5000) AND (p_combustivel = :combustivel OR p_combustivel = 5000) AND
                    (p_tipo = :tipo OR p_tipo = 5000) AND (quant_motores = :motores OR quant_motores = 5000) AND (propulsor = :propulsor OR propulsor=5000)
                ORDER BY
                    cli.heat ASC
            ";

            $stmt2 = $this->conexao->prepare($queryProcuras);
            $stmt2->bindValue(':modelo',$barco->modelo);
            $stmt2->bindValue(':horas',$barco->horas);
            $stmt2->bindValue(':potencia',$barco->potencia);
            $stmt2->bindValue(':ano',$barco->ano);
            $stmt2->bindValue(':fabricante',$barco->Fabricantes_id_fabricantes);
            $stmt2->bindValue(':combustivel',$barco->Combustivel_id_combustivel);
            $stmt2->bindValue(':tipo',$barco->Tipo_id_tipo);
            $stmt2->bindValue(':motores',$barco->quant_motor);
            $stmt2->bindValue(':propulsor',$barco->propulsor);
            $stmt2->execute();
            $procuras = $stmt2->fetchAll(PDO::FETCH_OBJ);
            foreach ($procuras as $key => $value) {
                $novoTamanho = explode('-', $value->tamanho );
                $novoValor = explode('-', $value->valor);
                $valor1 = str_replace('.', '', $novoValor[0]);
                $valor1 = str_replace(',', '.', $valor1);
                $valor2 = str_replace('.', '', $novoValor[1]);
                $valor2 = str_replace(',', '.', $valor2);
                switch ($value->heat) {
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
                if(($barco->tamanho >= floatval($novoTamanho[0]) AND $barco->tamanho <= floatval($novoTamanho[1])) AND ($barco->valor >= floatval($valor1) AND $barco->valor <= floatval($valor2))){
                    $link = preg_replace("/[^0-9]/", "", $value->contato);
                    echo '
                    <tr>
                        <td><a href="edit_cliente.php?id_cli='.$value->id_clientes.'" target="_blank">'.$value->nome.' '.$value->sobrenome.'</a> <img src="assets/heats/'.$heat.'.png" width=13 class="img-fluid" /></td>
                        <td> <a class="btn btn-success" href="http://wa.me/'.$link.'" target="_blank" role="button">ZAP</a></td>
                    </tr>
                    <tr>';
 
                } 
                
                
            }



        } 
            




    }
?>