<?php ob_start();
include("header.php");
include("core/mod_includes/php/funcoes.php");
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title><?php echo $ttl ?></title>
</head>

<body>
    <header>
        <?php
            include("core/mod_topo/topo.php");
            include("core/mod_includes/php/funcoes-jquery.php");
        ?>
    </header>

    <main>

        <section>
            <div class="banner-top">
                <h1 class="titulo">Meu Perfil</h1>
            </div>
        </section>

        <section>
            <div class="wrapper meu_perfil" id="pagina">
                <div class="conteudo">
                    <div class='pg' id='meus_dados'>
                        <h2 class="azul">Meus Dados</h2>

                        <?php
                            $sql = "SELECT * FROM fornecedores 
                            LEFT JOIN fornecedores_endereco ON fornecedores_endereco.user_id = fornecedores.id
                            LEFT JOIN fornecedores_ramo_atuacao ON fornecedores_ramo_atuacao.fra_id = fornecedores.id_ramo_atuacao
                            LEFT JOIN fornecedor_atributos ON fornecedor_atributos.fa_fornecedor = fornecedores.id
                            LEFT JOIN end_uf ON end_uf.uf_id = fornecedores_endereco.state_id
                            WHERE fornecedores.id = :id";
                            $stmt = $PDO->prepare($sql);
                            $stmt->bindValue(":id", $_SESSION["fn_id"]);
                            $stmt->execute();
                            $rows = $stmt->rowCount();
                            if ($rows > 0) {
                                $result = $stmt->fetch();
                                echo '
                                <form>
                                    <label>Razão Social </label>
                                    <input type="text" name="razao_social" id="razao_social" value="'.$result['field1'].'">
                
                                    <label>Nome Fantasia</label>
                                    <input type="text" name="razao_social" id="razao_social" value="'.$result['field2'].'">
                
                                    <div class="bloco_l">
                                        <label>CNPJ</label>
                                        <input type="text" name="razao_social" id="razao_social" value="'.$result['fa_cnpj'].'">
                                    </div>
                
                                    <div class="bloco_r">
                                        <label>IE </label>
                                        <input type="text" name="razao_social" id="razao_social" value="'.$result['fa_ie'].'">
                                    </div>
                
                                    <h2 class="azul"> Contato </h2>
                                    <div class="bloco_l">
                                        <label>Telefone </label>
                                        <input type="text" name="razao_social" id="razao_social" value="'.$result['fa_telefone'].'">
                                    </div>
                
                                    <div class="bloco_r">
                                        <label>Celular </label>
                                        <input type="text" name="razao_social" id="razao_social" value="'.$result['fa_celular'].'">
                                    </div>
                
                                    <h2 class="azul"> Endereço </h2>
                                    <div class="bloco_l">
                                        <label>CEP </label>
                                        <input type="text" name="razao_social" id="razao_social" value="'.$result['zip'].'">
                                    </div>
                
                                    <label>Endereço </label>
                                    <input type="text" name="razao_social" id="razao_social" value="'.$result['address'].'">
                
                                    <label>Bairro </label>
                                    <input type="text" name="razao_social" id="razao_social" value="'.$result['bairro'].'">
                
                                    <div class="bloco_l">
                                        <label>Número</label>
                                        <input type="text" name="razao_social" id="razao_social" value="'.$result['number'].'">
                                    </div>
                
                                    <div class="bloco_r">
                                        <label>Complemento</label>
                                        <input type="text" name="razao_social" id="razao_social" value="'.$result['complement'].'">
                                    </div>
                
                                    <label>Cidade </label>
                                    <input type="text" name="razao_social" id="razao_social" value="'.$result['city'].'">
                
                                    <label>Estado</label>
                                    <input type="text" name="razao_social" id="razao_social" value="'.$result['uf_nome'].'">

                                </form>';
                            }
                        ?>
                    </div>

                    <div class="pg" id='licitacao' style="display: none;">
                        <h2 class="azul">Minhas Licitações</h2>
                        <?php
                            $sql = "SELECT * FROM licitacao_participantes 
                            LEFT JOIN licitacao_pregao ON licitacao_pregao.lic_id = licitacao_participantes.id_licitacao
                            LEFT JOIN licitacao_edital ON licitacao_edital.le_id = licitacao_pregao.lic_id
                            WHERE licitacao_participantes.id_licitante = :id";
                            $stmt = $PDO->prepare($sql);
                            $stmt->bindValue(":id", $_SESSION["fn_id"]);
                            $stmt->execute();
                            $rows = $stmt->rowCount();
                            if ($rows > 0) {
                                echo"<div class='licitacoes'>"; 
                                while($result = $stmt->fetch()){
                                    $data = date("d/m/Y", strtotime($result['data_abertura'])); 
                                    echo "
                                        <a href='licitacoes-e-contratos/".$result['lic_id']."'>
                                            <div class='item bloco$p'>
                                                <h3>".$result['codigo']."</h3>
                                                <p>".$result['titulo']." <br>
                                                <span class='data'><i class='fas fa-calendar-alt'></i> $data </span>
                                                <p>Etapas:";
                                                if($result['abertura_status'] == '3'){
                                                    echo "<span class='situacao'> Aberto </span>";
                                                }
                                                if($result['habilitacao_status'] == '3'){
                                                    echo"<span class='situacao'> Habilitado </span>";
                                                }
                                                if($result['julgamento_status'] == '3'){
                                                    echo "<span class='situacao'> Em Julgamento </span>";
                                                }
                                                if($result['homologacao_status'] == '3'){
                                                    echo "<span class='situacao'> Homologado </span>";
                                                }
                        
                                                echo"</p>
                                            </div>
                                        </a>            
                                    ";
                                }
                                echo"</div>";
                            }
                            else {
                                echo "<p> Você não esta participando de nenhuma Licitação no momento!";
                            }
                        ?>
                    </div>

                    <div class="pg" id='meus_documentos' style="display: none;">
                        <h2 class="azul">Meus Documentos</h2>
                        <?php
                            $sql = "SELECT * FROM licitacao_documentacao
                            LEFT JOIN licitacao_documentos ON licitacao_documentos.ld_id = licitacao_documentacao.id_documento
                            LEFT JOIN fornecedores ON fornecedores.id = licitacao_documentacao.id_licitante
                            WHERE licitacao_documentacao.id_licitante = :id_licitante";
                            $stmt = $PDO->prepare($sql);
                            $stmt->bindValue(":id_licitante", $_SESSION["fn_id"]);
                            $stmt->execute();
                            $rows = $stmt->rowCount();
                            if ($rows > 0) {
                                echo"<table class='licitacoes'>
                                    <tr>    
                                        <th>Documento</th>
                                        <th>Data</th>
                                        <th>Validade</th>
                                    </tr>
                                
                                "; 
                                while($result = $stmt->fetch()){
                                    $data = date("d/m/Y", strtotime($result['data'])); 
                                    $validade = date("d/m/Y", strtotime($result['validade'])); 
                                    echo "
                                        <tr>
                                            <td>".$result['titulo']."</td>
                                            <td>$data </td>
                                            <td>$validade</td>
                                        </tr>
                                    ";
                                }
                                echo"</table>";
                            }
                            else {
                                echo "                        
                                    <h3>Certificado de Registro Cadastral não concluído</h3>
                                    <p> Para ter o Certificado de Registro Cadastral de sua empresa você deve enviar os documentos necessários para avaliação ao Departamento de Compras.<br>
                                    <p>Caso tenha dúvidas entre em contato pelo e-mail: compras@daev.org.br</p>
                                ";
                            }
                        ?>
                    </div>

                    <div class="pg" id='meus_certificados' style="display: none;">
                        <h2 class="azul">Certificado de Registro Cadastra</h2>

                        <h3>DEPARTAMENTO DE ÁGUAS E ESGOTOS DE VALINHOS</h3>
                        <p>AUTARQUIA MUNICIPAL<br>
                        Rua Orozimbo Maia, 1054 - Vila Sônia</p>

                        <p>Valinhos/SP - Telefone.:(19)2122-4444 - CEP 13.274-000</p>

                        <p>www.daev.org.br

                        <?php
                            $sql = "SELECT * FROM licitacao_documentacao
                            LEFT JOIN licitacao_documentos ON licitacao_documentos.ld_id = licitacao_documentacao.id_documento
                            LEFT JOIN fornecedores ON fornecedores.id = licitacao_documentacao.id_licitante
                            LEFT JOIN fornecedor_atributos ON fornecedor_atributos.fa_fornecedor = fornecedores.id
                            LEFT JOIN fornecedores_endereco ON fornecedores_endereco.user_id = fornecedores.id
                            LEFT JOIN end_uf ON end_uf.uf_id = fornecedores_endereco.state_id
                            WHERE licitacao_documentacao.id_licitante = :id_licitante
                            ORDER BY id_documento ASC";
                            $stmt = $PDO->prepare($sql);
                            $stmt->bindValue(":id_licitante", $_SESSION["fn_id"]);
                            $stmt->execute();
                            $rows = $stmt->rowCount();
                            if ($rows > 0) {

                                $result=$stmt->fetch(); 
                                echo "
                                    <h2 class='azul'>DEPARTAMENTO DE ÁGUAS E ESGOTOS DE VALINHOS AUTARQUIA MUNICIPAL</h2>

                                    <p>Rua Orozimbo Maia, 1054 - Vila Sônia <br>
                                    Valinhos/SP - Telefone.:(19)2122-4444 - CEP 13.274-000 <br>
                                    www.daev.org.br</p>
                                    
                                    <h3>COMISSÃO JULGADORA DE LICITAÇÕES</h3>
                                    
                                    <h3>CERTIFICADO DE REGISTRO CADASTRAL NÚMERO ".$result['fa_certificado_licitante']."</h3> 
                                    
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
                                                                
                                    echo "<p>A COMISSÃO JULGADORA DE LICITAÇÕES, certifica que a empresa identificada está inscrita no Registro Cadastral de Habilitação de empresas, desta Autarquia, a contar da data de emissão deste certificado, preenchendo desta forma os requisitos da Lei Federal 8.666/93 e suas posteriores alterações. A referida empresa deverá atualizar os documentos abaixo descritos, sob pena de suspensão ou cancelamento, conforme dispõe o artigo 37, da Lei Federal 8.666/93 e suas posteriores alterações. </p>
                                ";

                                    echo"<table class='licitacoes'>
                                            <tr>    
                                                <th>Documento</th>
                                                <th>Data</th>
                                                <th>Validade</th>
                                            </tr>                                
                                    "; 

                                $sql2 = "SELECT * FROM licitacao_documentacao
                                LEFT JOIN licitacao_documentos ON licitacao_documentos.ld_id = licitacao_documentacao.id_documento
                                LEFT JOIN fornecedores ON fornecedores.id = licitacao_documentacao.id_licitante
                                WHERE licitacao_documentacao.id_licitante = :id_licitante
                                ORDER BY validade ASC";
                                $stmt2 = $PDO->prepare($sql2);
                                $stmt2->bindValue(":id_licitante", $_SESSION["fn_id"]);
                                $stmt2->execute();
        
                                while($result2=$stmt2->fetch()){
                                    $data = date("d/m/Y", strtotime($result2['data'])); 
                                    $validade = date("d/m/Y", strtotime($result2['validade'])); 
                                    echo "
                                        <tr>
                                            <td>".$result2['titulo']."</td>
                                            <td>$data </td>
                                            <td>$validade</td>
                                        </tr>
                                    ";
                                }
                                echo"</table>
                                    <br>
                                    <center><button onclick='gerarPDF(".$_SESSION["fn_id"].")'> Imprimir Certificado </button> </center>                               
                                ";

                            }
                            else {
                                echo "                                    
                                    <h3>Certificado de Registro Cadastral não concluído</h3>
                                    <p> Para ter o Certificado de Registro Cadastral de sua empresa você deve enviar os documentos necessários para avaliação ao Departamento de Compras.<br>
                                    <p>Caso tenha dúvidas entre em contato pelo e-mail: compras@daev.org.br</p>
                                ";
                            }

                        ?>
                    </div>
                </div>

                <div class="veja_mais">
                    <div class="bloco">
                        <ul>
                            <li onclick="pagina('meus_dados');"> Meus Dados </li>
                            <li onclick="pagina('licitacao');"> Minhas Licitações </li>
                            <li onclick="pagina('meus_documentos');"> Meus Documentos </li>
                            <li onclick="pagina('meus_certificados');"> Certificado de Registro Cadastral </li>
                            <li> <a href="cadastre_senha/">Alterar Senha</a> </li>
                            <li > Sair </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include("core/mod_rodape/rodape.php"); ?>
</body>

</html>


<script>
 function pagina(pg){
    $('.pg').css('display','none');
    $('#'+pg).css('display','table');
}

function gerarPDF(id){
    window.location.href='gerar_pdf/'+id;
}


</script>