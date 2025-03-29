<?php
class API {
    private $searchTerm; 
    private $credenciais = [];
    private $conexao;
        private $conexaoPool;
        function __construct(PDO $conexao, Conexao $conexaoPool, $q = ''){
            $this->conexao = $conexao->conectar();
            $this->conexaoPool = $conexaoPool;
            $this->searchTerm = $q;
        }

        public function __destruct() {
            $this->conexaoPool->liberarConexao($this->conexao);
        }
     
   public function getProduct(){
    $this->getCredentials();
    $url = "https://api.mercadolibre.com/users/".$this->credenciais[0]->id_seller."/items/search?q=".$this->searchTerm."&access_token=".$this->credenciais[0]->token."&category=MLB1743";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    
    if (isset($data['results']) && count($data['results']) > 0) {
        foreach ($data['results'] as $item_id) {
            $item_url = "https://api.mercadolibre.com/items/$item_id?access_token=".$this->credenciais[0]->token;
            $item_response = file_get_contents($item_url);
            
            if ($item_response === false) {
                echo "Erro ao buscar detalhes do anúncio: $item_id";
                continue;
            }
            
            $product = json_decode($item_response, true);
            
            // Exiba os detalhes do anúncio
            $productName = $product['title'];
            $productLink = $product['permalink'];
            
            // Verifica se existem imagens para o produto
            if (isset($product['pictures']) && is_array($product['pictures']) && count($product['pictures']) > 0) {
                // Obter a primeira imagem (que é a imagem original em alta resolução)
                $productImage = $product['pictures'][0]['url'];
            } else {
                $productImage = 'URL_PARA_IMAGEM_PADRAO_SE_NAO_HOUVER_IMAGENS'; // Defina uma URL padrão de imagem caso não haja imagens disponíveis.
            }
            
            $productlocal = $product['seller_address'];
            $price = $product['price'];

            echo 
            '
                <div class="row product">
                    <div class="col-sm-4 col-md-2">
                        <img src="'.$productImage.'" class="img-item-ml">
                    </div>
                    <div class="col-sm-8 col-md-10">
                        <h3>'.$productName.'</h3>'.
                        '<span class="city"><i class="fa fa-map-marker" aria-hidden="true"></i> '.$productlocal['city']['name'].'</span>'.
                        '<span class="preco">'.$this->formatarValor($price).'</span>'.
                        '</p>
                        <p>
                            <a class="btn btn-info display-button" title="'.$productName.'" href="'.$productLink.'" target="_blank">
                                <i class="fa fa-info" aria-hidden="true"></i> MAIS INFORMAÇÕES
                            </a>
                            <a class="btn btn-success display-button" title="'.$productName.'" href="https://api.whatsapp.com/send?phone=5524999962209" target="_blank">
                                <i class="fa fa-whatsapp" aria-hidden="true"></i> CONTATO
                            </a>
                        </p>
                    </div>
                </div>
            ';
        }

    } else {
        echo 'Nenhum produto encontrado.';
    }
}


    protected function formatarValor($numero) {
        return 'R$ ' . number_format($numero, 2, ',', '.');
    }

    public function setToken($token){
        $stmt = $this->conexao->prepare(
            "
            UPDATE token
            SET token = '$token'
            WHERE id_token = 1;
            "
        );

        if ($stmt->execute()) {
            return true;
        }   
        
    }

    public function getCredentials ($token = 0){
        $stmt = $this->conexao->prepare(
            "
            SELECT *
            FROM token
            WHERE id_token = 1
            "
        );
        if ($stmt->execute()) {
           $this->credenciais =  $stmt->fetchAll(PDO::FETCH_OBJ);
           if($token == 1){
                return $this->credenciais[0]->token;
           }
        }  


    }

  public function recents(){
    // Fazer a requisição à API do Mercado Livre para obter os últimos veículos cadastrados do vendedor
    $url = 'https://api.mercadolibre.com/users/'.$this->credenciais[0]->id_seller.'/items/search?category=MLB1743&order=title_asc&access_token='.$this->credenciais[0]->token;
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (isset($data['results']) && count($data['results']) > 0) {
        foreach ($data['results'] as $veiculoId) {
            $veiculoUrl = "https://api.mercadolibre.com/items/$veiculoId?access_token=".$this->credenciais[0]->token;
            $veiculoResponse = file_get_contents($veiculoUrl);
            $veiculoData = json_decode($veiculoResponse, true);

            if ($veiculoData !== false) {
                $veiculoNome = $veiculoData['title'];
                $veiculoPreco = $veiculoData['price'];
                
                // Verifica se existem imagens para o veículo
                if (isset($veiculoData['pictures']) && is_array($veiculoData['pictures']) && count($veiculoData['pictures']) > 0) {
                    // Obter a primeira imagem (que é a imagem original em alta resolução)
                    $veiculoFoto = $veiculoData['pictures'][0]['url'];
                } else {
                    $veiculoFoto = 'URL_PARA_IMAGEM_PADRAO_SE_NAO_HOUVER_IMAGENS'; // Defina uma URL padrão de imagem caso não haja imagens disponíveis.
                }

                $veiculoLink = $veiculoData['permalink'];

                // Exibir o card do veículo
               echo '
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card">
                        <img src="'.$veiculoFoto.'" class="card-img-top" alt="'.$veiculoNome.'">
                        <div class="card-body">
                            <h5 class="card-title" title="'.$veiculoNome.'">'.$veiculoNome.'</h5>
                            <p class="card-text">R$ '.number_format($veiculoPreco, 2, ',', '.').'</p>
                            <a title="'.$veiculoNome.'" href="'.$veiculoLink.'" class="btn btn-primary W-100">MAIS DETALHES</a>
                        </div>
                    </div>
                </div>';
            } else {
                echo "Erro ao buscar detalhes do veículo: $veiculoId";
            }
        }
    } else {
        echo 'Nenhum veículo encontrado.';
    }
}
}