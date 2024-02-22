<?php

$id = $_GET['id']; 

include('core/mod_includes/php/connect.php');
session_start (); 
error_reporting(0);
date_default_timezone_set('America/Sao_Paulo');

ob_start();  //inicia o buffer

$sql= "SELECT * FROM cadastro_noticias
LEFT JOIN cadastro_noticias_imagens ON cadastro_noticias_imagens.cni_id = cadastro_noticias.nt_foto
WHERE nt_id = :nt_id";
$stmt = $PDO->prepare($sql);
$stmt->bindValue(':nt_id', $id);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows> 0)
{	
    $result = $stmt->fetch(); 
    $nt_titulo = $result['nt_titulo']; 
    $nt_subtitulo = $result['nt_subtitulo']; 
    $nt_imagem = $result['cni_foto'];
    $nt_descricao = $result['nt_descricao'];
    $nt_data = "<i class='fas fa-calendar-alt'></i> ".implode("/", array_reverse(explode("-", $result['nt_data'])));
    $nt_ttl_imagem = $result['cni_titulo']; 
    $nt_hora_cadastro = $result['nt_hora_cadastro']; 
    $nt_id = $result['nt_id']; 

    echo "
        <style>
            *{
                font-family: Arial, Helvetica, sans-serif;
                color: #585757;
                font-weight: 400;
            }

            p{
                font-size: 16px;
            }

            .azul{
                color: #2f91ce;
            }

            h1, h3{
                text-align:center; 
                font-weight:bold; 
            }

            table tr td {
                border: 1px solid #585757;
                padding:15px
            }
              
            table tr th {
                background-color: #e9e9e9;
                padding: 15px;
                border: 1px solid #585757;              
            }

            center {
                position:absolute;
                bottom: 0;
            }

            .preto {
                color:#000; 
                font-weight:900; 
            }

            .logo { 
                float:left; 
                width:20%;
            }

            .titulo {
                float:left; 
                margin-left:-8%;
                width: 90%;
                display:block;
            }

        </style>
        <div id='topo' >
            <div class='logo'><img src='core/imagens/logo_certificado.jpg' width='100%'></div>
            <div class='titulo'>
                <h3>  <span class='preto'>$nt_titulo</h3>
            </div>

        </div>
            <br>
    <div id='corpo' style='margin-top:80px;'>
        <h3 class='preto'>COMISSÃO JULGADORA DE LICITAÇÕES</h3>
        <br>
        <h3 ><u style='color: #185d96;'>CERTIFICADO DE REGISTRO CADASTRAL Nº ".$result['ldc_id']."</u></h3> 
        <br>
        <p>Razão Social: DAE VALINHOS <br>
        Endereço: RUA OROZIMBO MAIA, 1054<br>
        Cidade: Valinhos - SP<br>
        Cep: 13274-000<br>
        Telefone: 1921224410<br>
        CNPJ: 44.635.233/0001-36</p>

        <p>Objetivo Social: Autarquia municipal responsável pelo tratamento de água e coleta, afastamento e tratamento de esgoto, no município.</p>
        
        <p>A COMISSÃO JULGADORA DE LICITAÇÕES, certifica que a empresa identificada está inscrita no Registro Cadastral de Habilitação de empresas, desta Autarquia, a contar da data de emissão deste certificado, preenchendo desta forma os requisitos da Lei Federal 8.666/93 e suas posteriores alterações. A referida empresa deverá atualizar os documentos abaixo descritos, sob pena de suspensão ou cancelamento, conforme dispõe o artigo 37, da Lei Federal 8.666/93 e suas posteriores alterações. </p>
    ";
}

$html = ob_get_clean();
$html = utf8_encode($html);


require_once("core/mod_includes/php/pdf/dompdf_config.inc.php");

/* Cria a instância */
$dompdf = new DOMPDF();

/* Carrega seu HTML */
$dompdf->load_html(utf8_decode("$html"));

/* Renderiza */
$dompdf->render();

/* Exibe */
$dompdf->stream(
    "Certificado.pdf", /* Nome do arquivo de saída */
    array(
        "Attachment" => false /* Para download, altere para true */
    )
);

exit();

?>