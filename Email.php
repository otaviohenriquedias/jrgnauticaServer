<?php
  require 'PHPMailer/Exception.php';
  require 'PHPMailer/OAuth.php';
  require 'PHPMailer/PHPMailer.php';
  require 'PHPMailer/POP3.php';
  require 'PHPMailer/SMTP.php';
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\SMTP;
  use PHPMailer\PHPMailer\Exception;
    class Email {
        private $id_email;
        private $nome;
        private $contato;
        private $email;
        private $corpo_email;
        private $lista_email;
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
        private function verificaEmail ($email){
            $query = 
             '
            SELECT 
                * 
            FROM
                email
            WHERE
                email = :email
            '
             ;
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue( ':email' , $this->email);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_OBJ);
            if(!empty($data)){
                return false;
            }
            else{
                return true;
            }


        }
        

        public function cadastrarEmail (){
            $query = '
             
            INSERT INTO Email 
            (nome, contato, email)
            VALUES (:nome, :contato, :email)'
             ;

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue( ':nome' , $this->nome);
            $stmt->bindValue( ':contato' , $this->contato);
            $stmt->bindValue( ':email' , $this->email);
            if($this->verificaEmail($this->email)){
                if($stmt->execute()){
                    echo  '<p style="color:green">Cadastro realizado com sucesso!</p>' ;
                }
                else {
                    echo  '<p style="color:red">Ocorreu algum erro, tente mais tarde!</p>' ;
                }

            }
            else{
                    echo  '<p style="color:red">Este Email já foi utilizado!</p>' ; 
            }
            }
            
            public function setEmail (){
                $query = 
                           '
                            SELECT 
                                *
                            FROM 
                                Email
                            '
                           ;
                          $stmt = $this->conexao->prepare($query);
                          $stmt->execute();
                          $lista = $stmt->fetchAll(PDO::FETCH_OBJ);
                          foreach ($lista as $key => $value) {
                              $this->lista_email[] = array($value->email);
                          }

            }


            public function setCorpo(){
                          $query = 
                           '
                            SELECT 
                                *
                            FROM 
                                embarcacao emb
                                INNER JOIN tipo tip ON (emb.Tipo_id_tipo = tip.id_tipo)
                            WHERE 
                                sendEmail = 0 AND offmarket = 0
                            '
                           ;
                          $stmt = $this->conexao->prepare($query);
                          $stmt->execute();
                          $lista = $stmt->fetchAll(PDO::FETCH_OBJ);
                          foreach($lista as $key => $value){
                            $img = glob("../gestao/assets/embarcacoes/$value->id_embarcacao/principal.*",  GLOB_BRACE);
                            if(count($img) == 0){
                               $source = "https://www.jrbroker.com.br/assets/ENTRE-EM-CONTATO.png";
                            }
                            else{
                                $extension = pathinfo($img[0]);
                                $source = 'https://www.jrbroker.com.br/gestao/assets/embarcacoes/'.$value->id_embarcacao.'/'.'principal.'.$extension['extension'];
                            }
                            $this->corpo_email[] = ' 
                            <!DOCTYPE html>
                            <html lang="pt-br" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:v="urn:schemas-microsoft-com:vml">
                            <head><meta charset="utf-8">
                                
                                <meta content="width=device-width, initial-scale=1.0" name="viewport" />
                                <!--[if mso]><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch><o:AllowPNG/></o:OfficeDocumentSettings></xml><![endif]-->
                                <!--[if !mso]><!-->
                                <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css" />
                                <link href="https://fonts.googleapis.com/css?family=Abril+Fatface" rel="stylesheet" type="text/css" />
                                <link href="https://fonts.googleapis.com/css?family=Merriweather" rel="stylesheet" type="text/css" />
                                <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css" />
                                <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css" />
                                <link href="https://fonts.googleapis.com/css?family=Bitter" rel="stylesheet" type="text/css" />
                                <link href="https://fonts.googleapis.com/css?family=Permanent+Marker" rel="stylesheet" type="text/css" />
                                <link href="https://fonts.googleapis.com/css?family=Roboto+Slab" rel="stylesheet" type="text/css" />
                                <link href="https://fonts.googleapis.com/css?family=Cabin" rel="stylesheet" type="text/css" />
                                <link href="https://fonts.googleapis.com/css?family=Oxygen" rel="stylesheet" type="text/css" />
                                <link href="https://fonts.googleapis.com/css?family=Oswald" rel="stylesheet" type="text/css" />
                                <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css" />
                                <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet" type="text/css" />
                                <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css" />
                                <link href="https://fonts.googleapis.com/css?family=Droid+Serif" rel="stylesheet" type="text/css" />
                                <!--<![endif]-->
                                <style>
                                    * {
                                        box-sizing: border-box;
                                    }
                                    
                                    body {
                                        margin: 0;
                                        padding: 0;
                                    }
                                    
                                    a[x-apple-data-detectors] {
                                        color: inherit !important;
                                        text-decoration: inherit !important;
                                    }
                                    
                                    #MessageViewBody a {
                                        color: inherit;
                                        text-decoration: none;
                                    }
                                    
                                    p {
                                        line-height: inherit
                                    }
                                    
                                    @media (max-width:700px) {
                                        .icons-inner {
                                            text-align: center;
                                        }
                                        .icons-inner td {
                                            margin: 0 auto;
                                        }
                                        .row-content {
                                            width: 100% !important;
                                        }
                                        .column .border {
                                            display: none;
                                        }
                                        table {
                                            table-layout: fixed !important;
                                        }
                                        .stack .column {
                                            width: 100%;
                                            display: block;
                                        }
                                    }
                                </style>
                            </head>

                            <body style="background-color: #f9f9f9; margin: 0; padding: 0; -webkit-text-size-adjust: none; text-size-adjust: none;">
                                <table border="0" cellpadding="0" cellspacing="0" class="nl-container" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f9f9f9;" width="100%">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <table align="center" border="0" cellpadding="0" cellspacing="0" class="row row-1" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <table align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff; color: #000000; width: 680px;" width="680">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 0px; padding-bottom: 0px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;"
                                                                                width="100%">
                                                                                <table border="0" cellpadding="0" cellspacing="0" class="heading_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                                                                                    <tr>
                                                                                        <td style="padding-bottom:10px;padding-left:30px;padding-right:30px;padding-top:40px;text-align:center;width:100%;">
                                                                                            <h1 style="margin: 0; color: #000000; direction: ltr; font-family:  Cabin , Arial,  Helvetica Neue , Helvetica, sans-serif; font-size: 30px; font-weight: 400; letter-spacing: normal; line-height: 120%; text-align: center; margin-top: 0; margin-bottom: 0;"><strong><span class="tinyMce-placeholder">Imperdível!</span></strong></h1>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                                <table border="0" cellpadding="0" cellspacing="0" class="heading_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                                                                                    <tr>
                                                                                        <td style="padding-bottom:10px;padding-left:30px;padding-right:30px;padding-top:10px;text-align:center;width:100%;">
                                                                                            <h2 style="margin: 0; color: #000000; direction: ltr; font-family:  Cabin , Arial,  Helvetica Neue , Helvetica, sans-serif; font-size: 22px; font-weight: 400; letter-spacing: normal; line-height: 150%; text-align: center; margin-top: 0; margin-bottom: 0;"><span class="tinyMce-placeholder">Acabou de chegar na JR Broker. <b>Confira!</b></span></h2>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                                <table border="0" cellpadding="0" cellspacing="0" class="image_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                                                                                    <tr>
                                                                                        <td style="padding-bottom:30px;padding-left:30px;padding-right:30px;padding-top:10px;width:100%;">
                                                                                            <div align="center" style="line-height:10px">
                                                                                            <img alt="Product" src="'.$source.'" style="display: block; height: auto; border: 0; width: 510px; max-width: 100%;" title="Product" width="510" />
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                                <table border="0" cellpadding="0" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
                                                                                    <tr>
                                                                                        <td style="padding-bottom:5px;padding-left:30px;padding-right:30px;padding-top:15px;">
                                                                                            <div style="font-family: sans-serif">
                                                                                                <div class="txtTinyMce-wrapper" style="font-size: 12px; mso-line-height-alt: 14.399999999999999px; color: #000000; line-height: 1.2; font-family: Cabin, Arial, Helvetica Neue, Helvetica, sans-serif;">
                                                                                                    <p style="margin: 0; font-size: 14px; text-align: center;"><span style="font-size:20px;"><strong><span style="">'.$value->modelo.'</span></strong>
                                                                                                        </span>
                                                                                                    </p>
                                                                                                </div>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                                <table border="0" cellpadding="0" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
                                                                                    <tr>
                                                                                        <td style="padding-bottom:30px;padding-left:50px;padding-right:50px;padding-top:5px;">
                                                                                            <div style="font-family: sans-serif">
                                                                                                <div class="txtTinyMce-wrapper" style="font-size: 12px; mso-line-height-alt: 14.399999999999999px; color: #000000; line-height: 1.2; font-family: Cabin, Arial, Helvetica Neue, Helvetica, sans-serif;">
                                                                                                    <p style="margin: 0; font-size: 14px; text-align: center;"><span style="font-size:22px;"><strong>'.$value->ano.' | '.$value->tipo.'</strong></span></p>
                                                                                                </div>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                                <table border="0" cellpadding="0" cellspacing="0" class="button_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                                                                                    <tr>
                                                                                        <td style="padding-bottom:20px;text-align:center;">
                                                                                            <div align="center">
                                                                                                <!--[if mso]><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="https://www.jrbroker.com.br/embarcacao.php?if_hKai='.$value->id_embarcacao.'&raster=f033ab37c30201f73f142449d037028d" style="height:38px;width:221px;v-text-anchor:middle;" arcsize="0%" strokeweight="1.5pt" strokecolor="#000000" fillcolor="#000000"><w:anchorlock/><v:textbox inset="0px,0px,0px,0px"><center style="color:#ffffff; font-family:Arial, sans-serif; font-size:12px"><![endif]--><a href="https://www.jrbroker.com.br/embarcacao.php?if_hKai='.$value->id_embarcacao.'&raster='.md5($value->id_embarcacao).'"  style="text-decoration:none;display:inline-block;color:#ffffff;background-color:#000000;border-radius:0px;width:auto;border-top:2px solid #000000;border-right:2px solid #000000;border-bottom:2px solid #000000;border-left:2px solid #000000;padding-top:5px;padding-bottom:5px;font-family:Cabin, Arial, Helvetica Neue, Helvetica, sans-serif;text-align:center;mso-border-alt:none;word-break:keep-all;"
                                                                                                    target="_blank"><span style="padding-left:60px;padding-right:60px;font-size:12px;display:inline-block;letter-spacing:normal;"><span style="font-size: 12px; line-height: 2; word-break: break-word; mso-line-height-alt: 24px;">VER DETALHES </span></span></a>
                                                                                                <!--[if mso]></center></v:textbox></v:roundrect><![endif]-->
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                                <table border="0" cellpadding="0" cellspacing="0" class="heading_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                                                                                    <tr>
                                                                                        <td style="padding-bottom:10px;padding-left:30px;padding-right:30px;padding-top:30px;text-align:center;width:100%;">
                                                                                            <h2 style="margin: 0; color: #000000; direction: ltr; font-family: Cabin, Arial, Helvetica Neue , Helvetica, sans-serif; font-size: 22px; font-weight: 400; letter-spacing: normal; line-height: 150%; text-align: center; margin-top: 0; margin-bottom: 0;">Dúvidas?</h2>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                                <table border="0" cellpadding="0" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
                                                                                    <tr>
                                                                                        <td style="padding-bottom:30px;padding-left:30px;padding-right:30px;padding-top:10px;">
                                                                                            <div style="font-family: sans-serif">
                                                                                                <div class="txtTinyMce-wrapper" style="font-size: 12px; font-family: Cabin, Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 18px; color: #1d1d1b; line-height: 1.5;">
                                                                                                    <p style="margin: 0; font-size: 14px; text-align: center;">Se você tiver alguma dúvida, envie um e-mail para <strong>contato@jrbroker.com.br</strong><br/>ou ligue para (24) 99996-2209. Estamos em torno de 24h, 7 dias por semana.</p>
                                                                                                </div>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                                <table border="0" cellpadding="0" cellspacing="0" class="divider_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                                                                                    <tr>
                                                                                        <td style="padding-bottom:20px;padding-right:10px;padding-top:20px;">
                                                                                            <div align="center">
                                                                                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="20%">
                                                                                                    <tr>
                                                                                                        <td class="divider_inner" style="font-size: 1px; line-height: 1px; border-top: 4px solid #1D1D1B;"><span> </span></td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table align="center" border="0" cellpadding="0" cellspacing="0" class="row row-2" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <table align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #000000; color: #000000; width: 680px;" width="680">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-left: 5px; padding-right: 5px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;"
                                                                                width="50%">
                                                                                <table border="0" cellpadding="0" cellspacing="0" class="image_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                                                                                    <tr>
                                                                                        <td style="padding-right:30px;padding-top:10px;width:100%;padding-left:0px;padding-bottom:5px;">
                                                                                            <div align="center" style="line-height:10px"><img alt="Logo" src="https://www.jrbroker.com.br/assets/logo-jr-broker.png" style="display: block; height: auto; border: 0; width: 180 px; max-width: 100%;" title="Logo" width="180" /></div>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </td>
                                                                            <td class="column column-2" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="50%">
                                                                                <table border="0" cellpadding="0" cellspacing="0" class="social_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                                                                                    <tr>
                                                                                        <td style="padding-bottom:30px;padding-left:25px;padding-right:25px;padding-top:30px;text-align:center;">
                                                                                            <table align="center" border="0" cellpadding="0" cellspacing="0" class="social-table" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="104px">
                                                                                                <tr>
                                                                                                    <td style="padding:0 10px 0 10px;">
                                                                                                        <a href="https://www.facebook.com/juniorbrokerr/" target="_blank"><img alt="Facebook" height="32" src="https://www.jrbroker.com.br/assets/facebook2x.png" style="display: block; height: auto; border: 0;" title="facebook" width="32" /></a>
                                                                                                    </td>
                                                                                                    <td style="padding:0 10px 0 10px;">
                                                                                                        <a href="https://instagram.com/junior__broker?utm_medium=copy_link" target="_blank"><img alt="Instagram" height="32" src="https://www.jrbroker.com.br/assets/instagram2x.png" style="display: block; height: auto; border: 0;" title="instagram" width="32" /></a>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </table>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table align="center" border="0" cellpadding="0" cellspacing="0" class="row row-3" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <table align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #000000; color: #000000; width: 680px;" width="680">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 0px; padding-bottom: 0px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;"
                                                                                width="100%">
                                                                                <table border="0" cellpadding="30" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
                                                                                    <tr>
                                                                                        <td>
                                                                                            <div style="font-family: sans-serif">
                                                                                                <div class="txtTinyMce-wrapper" style="font-size: 12px; mso-line-height-alt: 18px; color: #8c8c8c; line-height: 1.5; font-family: Cabin, Arial, Helvetica Neue, Helvetica, sans-serif;">
                                                                                                    <p style="margin: 0; text-align: center;">Se você tiver dúvidas sobre seus dados, visite nossa Política de Privacidade</p>
                                                                                                    <p style="margin: 0; text-align: center;">Quer mudar a forma como você recebe esses e-mails? Você pode atualizar suas preferências ou cancelar a inscrição nesta lista.</p>
                                                                                                    <p style="margin: 0; text-align: center;">© 2022 Empresa. Todos os direitos reservados.</p>
                                                                                                </div>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table align="center" border="0" cellpadding="0" cellspacing="0" class="row row-4" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <table align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000000; width: 680px;" width="680">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 5px; padding-bottom: 5px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;"
                                                                                width="100%">
                                                                                <table border="0" cellpadding="0" cellspacing="0" class="icons_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                                                                                    <tr>
                                                                                        <td style="vertical-align: middle; color: #9d9d9d; font-family: inherit; font-size: 15px; padding-bottom: 5px; padding-top: 5px; text-align: center;">
                                                                                            <table cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
                                                                                                <tr>
                                                                                                    <td style="vertical-align: middle; text-align: center;">
                                                                                                        <!--[if vml]><table align="left" cellpadding="0" cellspacing="0" role="presentation" style="display:inline-block;padding-left:0px;padding-right:0px;mso-table-lspace: 0pt;mso-table-rspace: 0pt;"><![endif]-->
                                                                                                        <!--[if !vml]><!-->
                                                                                                        <table cellpadding="0" cellspacing="0" class="icons-inner" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; display: inline-block; margin-right: -4px; padding-left: 0px; padding-right: 0px;">
                                                                                                            <!--<![endif]-->
                                                                                                        </table>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </table>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <!-- End -->
                            </body>

                            </html>
                                  ';
                            $stmtAtualiza = $this->conexao->prepare(
                                '
                                    UPDATE embarcacao
                                    SET
                                        sendEmail = 1
                                    WHERE
                                        id_embarcacao = '.$value->id_embarcacao.'
                                '
                            );
                            $stmtAtualiza->execute();
                          }
                        }
            public function DisparaEmail ($listaDeEmails, $listaCorpo){
                foreach ($listaCorpo as $key => $value) {
                    foreach ($listaDeEmails as $chave => $valor) {        
                         try {
                            $mail = new PHPMailer(true);
                            $mail->CharSet = 'UTF-8';
                            $email = 'contato@jrbroker.com.br';
                            $mail->isSMTP();                                            // Send using SMTP
                            $mail->Host       = gethostbyname('mail.jrbroker.com.br');
                            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                            $mail->Username   = 'contato@jrbroker.com.br';                     // SMTP username
                            $mail->Password   = 'h?r+!w^XcE1UjQDM';                               // SMTP password
                            $mail->SMTPSecure = 'ssl';         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged   
                            $mail->Port       = 465;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
                            $mail->SMTPOptions = array('ssl' => array('verify_peer_name' => false));
                            //Recipients
                            $mail->setFrom('naoresponda-'.$email.'', 'Equipe JR Broker');
                            $mail->addAddress($valor[0]);     // Add a recipient
                            //$mail->addAddress('ellen@example.com');               // Name is optional
                            $mail->addReplyTo($email, 'Response');
                            $mail->isHTML(true);          
                            $mail->Subject = 'Embarcação nova na JR Broker! Confira!';
                            $mail->Body    =  $listaCorpo[$key];
                            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';        
                            $mail->send();
                            sleep(15);
                  
                          } 
                          //Try Email send user
        
                        catch (PDOException $e) {
                            echo 'CRUD CONSULTA';
                        }
                        
                    }

                }
                    
            }

            public function listaEmail (){
                $stmt = $this->conexao->prepare(' 
                SELECT  
                    * 
                FROM 
                    email
                    ');
                $stmt->execute();
                $dados = $stmt->fetchAll(PDO::FETCH_OBJ);
                //session_start();
                foreach($dados as $key => $valor){
                            echo '
                            <div class="article border-bottom" id="'.$valor->idEmail.'">
                                    <div class="col-xs-12">
                                        <div class="row">
                                            <div class="col-xs-11 col-md-11">
                                                <h4>'.$valor->nome.'</a></h4>
                                                <p><b> Email: </b>'.$valor->email.' <b>Contato: </b>'.$valor->contato.' <b>Data do Lead:</b> '.$valor->data_cadastro.' 
                                                </p>
    
                                            </div>
                                            <div class="col-xs-2 col-md-1 align-items-center">
                                                <button onClick="deleteEmail('.$valor->idEmail.')" type="button" class="btn btn-danger px-3"><i class="fa fa-xl fa-trash-o" aria-hidden="true" style="color:white"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            ';
    
    
                }
            }
            public function deletaEmail ($id){
                $query = 
                '
                    DELETE FROM
                        Email
                    WHERE
                        idEmail= '.$id;
    
                $stmt = $this->conexao->prepare($query);
                $stmt->execute();
    
            }
    

    }
?>