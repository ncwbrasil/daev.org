<?php
$id = $_GET['id']; 

include('core/mod_includes/php/connect.php');
session_start (); 
error_reporting(0);
date_default_timezone_set('America/Sao_Paulo');

ob_start();  //inicia o buffer

$sql = "SELECT * FROM licitacao_documentacao
LEFT JOIN licitacao_documentos ON licitacao_documentos.ld_id = licitacao_documentacao.id_documento
LEFT JOIN fornecedores ON fornecedores.id = licitacao_documentacao.id_licitante
LEFT JOIN fornecedor_atributos ON fornecedor_atributos.fa_fornecedor = fornecedores.id
LEFT JOIN fornecedores_endereco ON fornecedores_endereco.user_id = fornecedores.id
LEFT JOIN end_uf ON end_uf.uf_id = fornecedores_endereco.state_id
WHERE licitacao_documentacao.id_licitante = :id_licitante
ORDER BY id_documento ASC";
$stmt = $PDO->prepare($sql);
$stmt->bindValue(":id_licitante", $id);
$stmt->execute();
$rows = $stmt->rowCount();
if ($rows > 0) {

    $result=$stmt->fetch(); 
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
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />        
        <div id='topo' >
            <div class='logo'><img src='core/imagens/logo_certificado.jpg' width='100%'></div>
            <div class='titulo'>
                <h3><span class='preto'>DEPARTAMENTO DE &Aacute;GUAS E ESGOTOS DE VALINHOS</span><br>  AUTARQUIA MUNICIPAL</h3>
            </div>
        </div>

            <br>
    <div id='corpo' style='margin-top:80px;'>
        <h3 class='preto'>COMISSÃO JULGADORA DE LICITAÇÕES</h3>
        <br>
        <h3 ><u style='color: #185d96;'>CERTIFICADO DE REGISTRO CADASTRAL Nº ".$result['fa_certificado_licitante']."</u></h3> 
        <br>
        <p>Razão Social: ".$result['field2']."<br>
        Endereço: ".$result['address'].", ".$result['number']."<br>
        Cidade: ".$result['city']." - ".$result['uf_sigla']."<br>
        Cep: ".$result['zip']."<br>
        Telefone: ".$result['fa_telefone']."<br>
        CNPJ: ".$result['fa_cnpj']."</p>";

        if($result['fa_objetivo_licitante']!=''){
            echo "<p>Objetivo Social:".$result['fa_objetivo_licitante']."</p>"; 
        }

        if($result['fa_observacao_licitante'] !=''){
            echo "<p>Observação:".$result['fa_observacao_licitante']."</p>"; 
        }       
        
       echo" <p>A COMISSÃO JULGADORA DE LICITAÇÕES, certifica que a empresa identificada está inscrita no Registro Cadastral de Habilitação de empresas, desta Autarquia, a contar da data de emissão deste certificado, preenchendo desta forma os requisitos da Lei Federal 8.666/93 e suas posteriores alterações. A referida empresa deverá atualizar os documentos abaixo descritos, sob pena de suspensão ou cancelamento, conforme dispõe o artigo 37, da Lei Federal 8.666/93 e suas posteriores alterações. </p>
    ";

        echo"<table class='licitacoes'>
                <tr>    
                    <th>Documento</th>
                    <th>Validade</th>
                </tr>                                
        "; 

    $sql2 = "SELECT * FROM licitacao_documentacao
    LEFT JOIN licitacao_documentos ON licitacao_documentos.ld_id = licitacao_documentacao.id_documento
    LEFT JOIN fornecedores ON fornecedores.id = licitacao_documentacao.id_licitante
    WHERE licitacao_documentacao.id_licitante = :id_licitante
    ORDER BY validade ASC";
    $stmt2 = $PDO->prepare($sql2);
    $stmt2->bindValue(":id_licitante", $id);
    $stmt2->execute();
        
    while($result2=$stmt2->fetch()){
        $data = date("d/m/Y", strtotime($result2['data'])); 
        $validade = date("d/m/Y", strtotime($result2['validade'])); 
        echo "
            <tr>
                <td>".$result2['titulo']."</td>
                <td>$validade</td>
            </tr>
        ";
    }

    $dia_mes = date("d", strtotime($result['fa_emissao_crc']));
    $mes = date("m", strtotime($result['fa_emissao_crc']));
    $ano = date("Y", strtotime($result['fa_emissao_crc']));

    switch ($mes) {
        case 1:
            $mes = "Janeiro";
            break;
        case 2:
            $mes = "Fevereiro";
            break;
        case 3:
            $mes = "Março";
            break;
        case 4:
            $mes = "Abril";
            break;
        case 5:
            $mes = "Maio";
            break;
        case 6:
            $mes = "Junho";
            break;
        case 7:
            $mes = "Julho";
            break;
        case 8:
            $mes = "Agosto";
            break;
        case 9:
            $mes = "Setembro";
            break;
        case 10:
            $mes = "Outubro";
            break;
        case 11:
            $mes = "Novembro";
            break;
        case 12:
            $mes = "Dezembro";
            break;
    }
    $emissao = $dia_mes . ' de ' . $mes . ' de ' . $ano;    
    echo"</table>
    </div>
    <center>C.J.L $emissao<br>
    A autenticidade das informações poderá ser confirmada no site: http://www.daev.org.br/compras, informando o usuário e a senha.</center>
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