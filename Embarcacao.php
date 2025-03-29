<?php
session_start();
    class Embarcacao {
        private $query_consulta;
        private $conexao;
        private $id_emcarcacao;
        private $propulsor;
        private $fabricante;
        private $modelo;
        private $tipo;
        private $horas;
        private $potencia;
        private $quant_motor;
        private $modelo_motor;
        private $ano;
        private $combustivel;
        private $caminho_imagem;
        private $marina;
        private $descricao;
        private $vendido;
        private $offmarket;
        private $captador;
        private $propietario;
        private $src = '../assets/embarcacoes/';
        private $tamanho;
        private $preco;
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

        private function updateSrcImg ($caminho, $id){
            $stmt = $this->conexao->prepare('UPDATE embarcacao SET caminho_imagem = "'.$caminho.'" WHERE id_embarcacao = "'.strval($id).'"');
            $stmt->execute();
        }
        

        public function cadastrarEmb (){
            $query =

            '
            INSERT INTO embarcacao(horas, potencia, quant_motor, modelo_motor,
             ano, caminho_imagem, descricao, vendido, 
             Fabricantes_id_fabricantes, Tipo_id_tipo, modelo, Clientes_id_clientes, 
             Captador_id_captador, Combustivel_id_combustivel, offmarket, tamanho, valor, propulsor, marina_Marina)

             VALUES (:horas, :potencia, :quant_motor, :modelo_motor, :ano, :caminho_imagem,
                    :descricao, :vendido, :fabricante, :tipo, :modelo, :cliente,
                    :captador, :combustivel, :offmarket, :tamanho, :preco, :propulsor, :marina)
             ';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':horas', intval($this->horas));
            $stmt->bindValue(':potencia', intval($this->potencia));
            $stmt->bindValue(':quant_motor', intval($this->quant_motor));
            $stmt->bindValue(':modelo_motor', $this->modelo_motor);
            $stmt->bindValue(':ano', intval($this->ano));
            $stmt->bindValue(':caminho_imagem', $this->src);
            $stmt->bindValue(':descricao', $this->descricao);
            $stmt->bindValue(':vendido', $this->vendido);
            $stmt->bindValue(':fabricante', intval($this->fabricante));
            $stmt->bindValue(':tipo', intval($this->tipo));
            $stmt->bindValue(':modelo', $this->modelo);
            $stmt->bindValue(':cliente', intval ($this->propietario));
            $stmt->bindValue(':captador', intval($this->captador));
            $stmt->bindValue(':combustivel', intval($this->combustivel));
            $stmt->bindValue(':offmarket', $this->offmarket);
            $stmt->bindValue(':tamanho', $this->tamanho);
            $stmt->bindValue(':preco',$this->preco);
            $stmt->bindValue(':propulsor',$this->propulsor);
            $stmt->bindValue(':marina',$this->marina);
            if($stmt->execute()){
            $this->id_emcarcacao = $this->conexao->lastInsertId();
            $teste = mkdir($this->src.$this->id_emcarcacao);
               $this->updateSrcImg($this->src, $this->id_emcarcacao);
               echo '{"status":"Cadastrada!", "mensagem" : "<h3>O código da pasta é <b>'.$this->id_emcarcacao.'<b/>!</h3>", "type" : "success"}';
            }
            else {
                echo '{"status":"Ops!", "mensagem" : "Algo deu errado, tente novamente...", "type" : "error"}';
            }
        }
        public function listarBarcos (){
            $query = 
            '
                SELECT 
                    *
                FROM 
                    fabricantes
                ORDER BY
                    fabricante ASC

            ';
            $stmt = $this->conexao->prepare($query);
            if($stmt->execute()){
                $fabricantes = $stmt->fetchAll(PDO::FETCH_OBJ);
                echo json_encode($fabricantes);
            }
        }
        public function listaPropulsor (){
            $query = 
            '
                SELECT 
                    *
                FROM 
                    propulsor

            ';
            $stmt = $this->conexao->prepare($query);
            if($stmt->execute()){
                $propulsor = $stmt->fetchAll(PDO::FETCH_OBJ);
                echo json_encode($propulsor);
            }
        }

        public function consultarBarcos ($fabricante, $tipo, $combustivel, $ano, $tamanhoMin, $tamanhoMax, $potencia, $horas, $offmarket, $situacao, $captador, $propulsor, $modelo, $valorMin, $valorMax, $marina) {
            
            function retornaMarina ($marina){
                if($marina == 'N/D'){
                    return '';
                }
                    return 'AND marina_Marina = '.$marina.'';

            }

            function retornaTamanho ($tamanhoMin, $tamanhoMax){
                if(($tamanhoMin == '' and $tamanhoMax == '')){
                    return '';
                }
                    return 'AND (tamanho >= '.$tamanhoMin.' AND tamanho <= '.$tamanhoMax.')';

            }

            function retornaValor ($valorMin, $valorMax){
                if(($valorMin == '' and $valorMax == '')){
                    return '';
                }
                    return 'AND (valor >= '.$valorMin.' AND valor <= '.$valorMax.')';

            }
            //RETORNA QUERY DE POTENCIA
            function retornaPotencia($potencia) {
                switch ($potencia) {
                    case -1:
                        return '';
                        break;
                    case 100:
                         return 'and potencia <= 100';
                        break;
                    case 200:
                        return 'and potencia <= 200';
                        break;
                    case 300:
                        return 'and potencia <=300';
                        break;
                    case 400:
                        return 'and potencia <= 400';
                        break;
                    case 500:
                        return 'and potencia <= 500';
                        break;
                    case 600:
                        return 'and potencia <= 600';
                        break;
                    case 700:
                        return 'and potencia <= 700';
                        break;
                    case 800:
                        return 'and potencia <= 800';
                        break;
                    case 900:
                        return 'and potencia <= 900';
                        break;
                    case 1000:
                        return 'and potencia <= 1000';
                        break;
                    case 1500:
                        return 'and potencia <= 1500';
                        break;
                    case '>1500':
                        return 'and tamanho >=1500';
                        break;
                } 
            }
            //RETORNA QUERY DE HORAS
            function retornaHoras($horas) {
                switch ($horas) {
                    case -1:
                        return '';
                        break;
                    case 0:
                         return 'and horas = 0';
                        break;
                    case 200:
                        return 'and horas <= 200';
                        break;
                    case 400:
                        return 'and horas <= 400';
                        break;
                    case 600:
                        return 'and horas <= 600';
                        break;
                    case 800:
                        return 'and horas <= 800';
                        break;
                    case 1000:
                        return 'and horas <= 1000';
                        break;
                    case 1200:
                        return 'and horas <= 1200';
                        break;
                    case 1400:
                        return 'and horas <= 1400';
                        break;
                    case 1600:
                        return 'and horas <= 1600';
                        break;
                    case '>1600':
                        return 'and potencia >= 1600';
                        break;
                } 
            }
            function retornaFabricante($fabricante){
                if($fabricante == -1){
                    return '0=0';
                }
                else{
                    return 'Fabricantes_id_fabricantes = '.$fabricante;
                }
                }
                function retornaQuery($tipo, $campo){
                    if($tipo == -1){
                        return '';
                    }
                   
                    else{
                        return 'and '.$campo.' = '.$tipo;
                    }
                    }
                    function retornaQueryAno($tipo, $campo){
                        if($tipo == -1){
                            return '';
                        }
                       
                        else{
                            return 'and '.$campo.' >= '.$tipo;
                        }
                        }
            
            
            $query =
            "
                SELECT 
                    *
                FROM
                embarcacao emb
                    INNER JOIN fabricantes fab ON (emb.Fabricantes_id_fabricantes = fab.id_fabricantes)
                    INNER JOIN tipo tip ON (emb.Tipo_id_tipo = tip.id_tipo)
                    INNER JOIN combustivel comb ON (emb.Combustivel_id_combustivel = comb.id_combustivel)
                    INNER JOIN clientes cli ON (emb.Clientes_id_clientes = cli.id_clientes)
                    INNER JOIN propulsor pro ON (emb.propulsor = pro.id_propulsor)
                    INNER JOIN captador cap ON (emb.Captador_id_captador = cap.id_captador)
                    LEFT JOIN marina mar ON (emb.marina_Marina = mar.id_marina)
                WHERE
                ".retornaFabricante($fabricante)." ".retornaQuery($tipo, 'Tipo_id_tipo')." ".retornaQueryAno($ano, 'ano')." ".retornaQuery($combustivel, 'Combustivel_id_combustivel')." ".retornaTamanho($tamanhoMin, $tamanhoMax)." ".retornaHoras($horas)." ".retornaPotencia($potencia)." ".retornaQuery($offmarket, 'offmarket')." ".retornaQuery($situacao, 'vendido')." ".retornaQuery($captador, 'Captador_id_captador')." ".retornaQuery($propulsor, 'propulsor')." ".RetornaValor($valorMin, $valorMax)." ".retornaMarina($marina)." 
                ORDER BY horaLancamento DESC
                ";
            
            if ($modelo != ''){
                $query =
                "
                    SELECT 
                        *
                    FROM
                        embarcacao emb
                            INNER JOIN fabricantes fab ON (emb.Fabricantes_id_fabricantes = fab.id_fabricantes)
                            INNER JOIN tipo tip ON (emb.Tipo_id_tipo = tip.id_tipo)
                            INNER JOIN combustivel comb ON (emb.Combustivel_id_combustivel = comb.id_combustivel)
                            INNER JOIN clientes cli ON (emb.Clientes_id_clientes = cli.id_clientes)
                            INNER JOIN propulsor pro ON (emb.propulsor = pro.id_propulsor)
                            INNER JOIN captador cap ON (emb.Captador_id_captador = cap.id_captador)
                    WHERE
                        modelo LIKE '%".strval($modelo)."%'
                    ORDER BY horaLancamento ASC
                        
                        ";
            }

            $stmt= $this->conexao->prepare($query);
            if($stmt->execute()){
                $dados = $stmt->fetchAll(PDO::FETCH_OBJ);
                $readyonly = 'disabled';
                if($_SESSION['tipo'] == 1){$readyonly = '';}
                foreach ($dados as $indice => $valor) {
                    $pasta = $this->src.$valor->id_embarcacao;
                    /*echo '<pre>';
                    print_r($valor);
                    echo '</pre>'*/;
                    if((count(glob("$pasta/*")) === 0)) {
                        $span = '<span style="margin-left:10px" class="badge bg-warning text-dark">Sem fotos</span>';
                    }
                    else {
                        $span = '<span style="margin-left:10px" class="badge bg-success">Com fotos</span>';
                    }
                    $descritivo = '
✅DISPONÍVEL PARA VENDA✅
.
.
.
.

⚓FABRICANTE: '.ucfirst($valor->fabricante).'
🚤MODELO: '.$valor->modelo.'
⚙️MOTORES: '.$valor->quant_motor.'X '.$valor->modelo_motor.' '.$valor->potencia.' HP
🗓ANO: '.$valor->ano.'
⏰HORAS: '.$valor->horas.'
⛽COMBUSTÍVEL: '.$valor->combustivel.'
▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃
'
.$this->gerarDescritivo($valor->descricao).'
▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃
💰VALOR: R$ '.number_format($valor->valor,0,',','.').'
▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃
MAIORES INFORMAÇÕES VIA DIRECT OU WHATSAPP:
@seu_usuario
📞(XX) XXXXX-XXXX
▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃
                    ';
    
                   echo '
                    <div class="article border-bottom" id="'.$valor->id_embarcacao.'">
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-3 col-md-2 date">
                                    <div class="large">'.$valor->ano.'</div>
                                    <div class="text-muted">'.$valor->fabricante.'</div>
                                </div>
                                <div class="col-xs-7 col-md-9">
                                    <h4><a href="edit_embarc.php?id_emarb='.$valor->id_embarcacao.'" target="_blank">'.$valor->modelo.'</a>'.$span.' <button type="button" class="btn" '.$valor->id_embarcacao.'." data-clipboard-text="'.$descritivo.'">COPIAR DESCRITIVO <i class="fa fa-clipboard" aria-hidden="true"></i></button></h4>
                                    <p><b>Combustível:</b> '.$valor->combustivel.' - <b>Modelo:</b> '.$valor->tipo.' - <b>Pés: </b>'.$valor->tamanho.' - <b>Horas:</b> '.$valor->horas.' - <b>Potência:</b> '.$valor->potencia.' HP - <b>Motorização:</b> '.$valor->quant_motor.'x '.$valor->modelo_motor.' - <b>Propietário: </b>'.$valor->nome.' '.$valor->sobrenome.'
                                    <b>Valor:</b> R$ <span class="preco">'.number_format($valor->valor, 2, ',', '').'</span> <b>Cód. da pasta:</b> '.$valor->id_embarcacao.' <b> Propulsor:</b> '.$valor->modelopropulsor.' <b> Captador:</b> '.$valor->nome_captador.'
                                    </p>
                                </div>
                                <div class="col-xs-2 col-md-1 align-items-center">
                                        <button onClick="deleteEmb('.$valor->id_embarcacao.')" '.$readyonly.' type="button" class="btn btn-danger px-3"><i class="fa fa-xl fa-trash-o" aria-hidden="true" style="color:white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                   ';
                }
            }    
            
        }

        public function editarEmbarcacao ($getId, $retorno){
                $query_consulta =
                '
                    SELECT 
                        *
                    FROM
                        embarcacao emb
                            INNER JOIN fabricantes fab ON (emb.Fabricantes_id_fabricantes = fab.id_fabricantes)
                            INNER JOIN tipo tip ON (emb.Tipo_id_tipo = tip.id_tipo)
                            INNER JOIN combustivel comb ON (emb.Combustivel_id_combustivel = comb.id_combustivel)
                            INNER JOIN clientes cli ON (emb.Clientes_id_clientes = cli.id_clientes)
                            INNER JOIN captador cap ON (emb.Captador_id_captador = cap.id_captador)
                            INNER JOIN propulsor pro ON (emb.propulsor = pro.id_propulsor)
                    WHERE
                        id_embarcacao = :getId
                ';

                $stmt = $this->conexao->prepare($query_consulta);
                $stmt->bindValue(':getId', intval($getId));
                if($stmt->execute()){
                    $dados = $stmt->fetchAll(PDO::FETCH_OBJ);
                    return $dados[0]->$retorno;
                }
        }
        public function atualizaEmbarcacao (){
            $query =

            '
            UPDATE 
                embarcacao
            SET 
                marina_Marina = :marina, horas = :horas, potencia = :potencia, quant_motor = :quant_motor,
                modelo_motor = :modelo_motor, ano = :ano, descricao = :descricao,
                vendido = :vendido, Fabricantes_id_fabricantes = :fabricante,
                Tipo_id_tipo = :tipo, modelo = :modelo, Clientes_id_clientes = :cliente,
                Captador_id_captador = :captador, Combustivel_id_combustivel = :combustivel,
                offmarket = :offmarket, tamanho = :tamanho, valor = :preco, propulsor = :propulsor
            WHERE 
                id_embarcacao = :id_barco
             ';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':horas', intval($this->horas));
            $stmt->bindValue(':potencia', intval($this->potencia));
            $stmt->bindValue(':quant_motor', intval($this->quant_motor));
            $stmt->bindValue(':modelo_motor', $this->modelo_motor);
            $stmt->bindValue(':ano', intval($this->ano));
            $stmt->bindValue(':descricao', $this->descricao);
            $stmt->bindValue(':vendido', $this->vendido);
            $stmt->bindValue(':fabricante', intval($this->fabricante));
            $stmt->bindValue(':tipo', intval($this->tipo));
            $stmt->bindValue(':modelo', $this->modelo);
            $stmt->bindValue(':cliente', intval ($this->propietario));
            $stmt->bindValue(':captador', intval($this->captador));
            $stmt->bindValue(':combustivel', intval($this->combustivel));
            $stmt->bindValue(':offmarket', $this->offmarket);
            $stmt->bindValue(':tamanho', $this->tamanho);
            $stmt->bindValue(':id_barco', intval($this->id_embarcacao));
            $stmt->bindValue(':preco', $this->preco);
            $stmt->bindValue(':propulsor', $this->propulsor);
            $stmt->bindValue(':marina', $this->marina);
            $stmt->execute();

    }
    public function deletaEmb ($id){
        $query = 
        '
            SET FOREIGN_KEY_CHECKS = 0;
            DELETE FROM
                embarcacao
            WHERE
                id_embarcacao= '.$id;

        $stmt = $this->conexao->prepare($query);
        $stmt->execute();
    }
    public function gerarDescritivo($descricao){
        $novaString = explode('
', $descricao);
        asort($novaString, SORT_STRING | SORT_FLAG_CASE | SORT_NATURAL);
        $novaStringConv = '';
        foreach($novaString as $cat => $value){
            
            $novaStringConv .= '✅'.ucfirst(mb_strtolower($value, 'UTF-8')).'
';
        }
        return $novaStringConv;

    
        
    }
    public function cadastraFabricante (){
        $query = 
        '
            INSERT INTO 
                fabricantes(fabricante) 
            VALUES
                (:fabricante) 
        ';
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':fabricante', $this->fabricante);
        if($stmt->execute()){
            echo '{"status":"Cadastrada!", "mensagem" : "Fabricante cadastrada com sucesso!", "type" : "success"}';
        }
        else {
            echo '{"status":"Ops!", "mensagem" : "Algo deu errado, tente novamente...", "type" : "error"}';
        }
    }
}

?>