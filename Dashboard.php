<?php
include 'cliente.php';
    class Dashboard {
        private $conexao;
        function __construct(Conexao $conexao){
            $this->conexao = $conexao->conectar();
            
        }

        public function viewProcura ($array){
            foreach ($array as $key => $value) {
                switch ($this->getNome($key, 'heat')) {
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


                echo '<div class="panel panel-default">
                <div class="panel-heading" style="height:auto">';
                   echo'<a href="edit_cliente.php?id_cli='.$key.'" target="_blank" >'.$this->getNome($key, 'nome').' '.$this->getNome($key, 'sobrenome').'</a> - <img src="assets/heats/'.$heat.'.png" width=20 class="img-fluid" /> - ';
                   echo '<span class="xs-md-12 position-absolute top-0 start-100 translate-middle badge rounded-pill bg-Warning ">';
                   echo  count($array[$key]);
                   echo  '<span class="visually-hidden"> Encontradas</span></span> - ';

                   echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-Warning">';
                   echo  '<span class="visually-hidden">'.$this->getNome($key, 'atendente').'</span></span>
                    <span class="pull-right clickable panel-toggle panel-button-tab-left panel-collapsed"><em class="fa fa-toggle-down"></em></span>
                </div>
                <div class="panel-body" style="display: none;">';
                   foreach ($value as $chave=> $valor) {

                    echo '<div class="chat-body clearfix">';
                    echo '<div class="header"><a href="edit_embarc.php?id_emarb='.$valor['id-barco'].'" target="_blank"><strong class="primary-font">'.$valor['modelo'].'</strong></a> <small class="text-muted">'.$valor['fabricante'].'</small></div>
                        <p><b>Tipo:</b> '.$valor['tipo'].'<b> Ano: </b>'.$valor['ano'].' <b> Tamanho:</b> '.$valor['tamanho'].'<b> Horas: </b>'.$valor['horas'].'<b> Propulsão: </b>'.$valor['propulsor'].' <b>Potencia: </b>'.$valor['potencia'].' HP 
                        <b>Valor:</b> R$ <span class="preco">'.$valor['valor'].' </span> <b>Motorização: </b>'.$valor['quant-motor'].' '.$valor['modelo-motor'].'<b> Propietário: </b><a href="edit_cliente.php?id_cli='.$valor['id_propietario'].'" target="_blank">'.$valor['propietario'].'
                        </a></p>
                        <hr>
                        </div>';

                   }

            
                echo '</span>
                    
                </div>
                </div>';
            }
            
            
        }

        private function getNome ($id, $coluna){
            $stmt = $this->conexao->prepare('
            SELECT 
                clientes.heat, clientes.nome, clientes.sobrenome, usuario.nome AS atendente 
            FROM 
                clientes
                LEFT JOIN usuario ON (clientes.resp_atend = usuario.id_usuario)
            WHERE 
                id_clientes = :id'
            );
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $data[0]->$coluna;
        }

        public function getDash ($tabela){
            $query = 
                '
                SELECT
                    count(*) as n_count
                FROM
                    '.$tabela.'
                ';
            $stmt = $this->conexao->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ)->n_count;
            
        }

    }
    
?>