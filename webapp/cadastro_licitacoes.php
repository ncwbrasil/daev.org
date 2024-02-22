<?php
$pagina_link = 'cadastro_licitacoes';
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");
session_start();
$page = "<a href='cadastro_licitacoes/view'>Licitações</a>";

?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <?php
        include_once("header.php");
    ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            $("select[name=categorias]").html('<option value="">Carregando...</option>');
            $.post("carrega_menu.php", {
                    action: 'categorias'
                },
                function(valor) {
                    $("select[name=categorias]").html(valor);
                }
            )
        })

    </script>
</head>

<body>
    <main class="cd-main-content">
        <div class="container">
            <?php include('../core/mod_menu/menu/menu.php') ?>
            <div class="wrapper">
                <div class='mensagem'></div>
                <?php
                if (isset($_GET['lic_id'])) {
                    $lic_id = $_GET['lic_id'];
                }
                if ($lic_id == '') {
                    $lic_id = $_POST['lic_id'];
                }

                $id_categoria       = $_POST['id_categoria'];
                $ramo_atuacao       = ($_POST['ramo_atuacao'] ? $_POST['ramo_atuacao'] : 0) ;
                $codigo             = $_POST['codigo'];
                $titulo             = $_POST['titulo'];
                $objetivo           = $_POST['objetivo'];
                $comunicado         = $_POST['comunicado'];
                $exibir             = $_POST['exibir'];
                $ordem             = ($_POST['ordem'] ? $_POST['ordem'] : 0) ;
                $situacao          = $_POST['situacao'];

                if($_POST['data1'] == '' || $_POST['hora1'] == ''){
                    $data_abertura      = NULL;

                }else {
                    $data_abertura      = implode("-", array_reverse(explode("/", $_POST['data1']))) . ' ' . $_POST['hora1'];

                }
                if($_POST['data2'] == '' || $_POST['data2'] == ''){
                    $data_habilitacao      = NULL;

                }else {
                    $data_habilitacao      = implode("-", array_reverse(explode("/", $_POST['data2']))) . ' ' . $_POST['hora2'];

                }
                if($_POST['data3'] == '' || $_POST['data3'] == ''){
                    $data_julgamento      = NULL;

                }else {
                    $data_julgamento      = implode("-", array_reverse(explode("/", $_POST['data3']))) . ' ' . $_POST['hora3'];

                }
                if($_POST['data4'] == '' || $_POST['data4'] == ''){
                    $data_homologacao      = NULL;

                }else {
                    $data_homologacao      = implode("-", array_reverse(explode("/", $_POST['data4']))) . ' ' . $_POST['hora4'];

                }

                $abertura_status    = $_POST['abertura_status'];
                $habilitacao_status = $_POST['habilitacao_status'];
                $julgamento_status  = $_POST['julgamento_status'];
                $homologacao_status = $_POST['homologacao_status'];

                if($_POST['data_criacao'] == ''){
                    $data_criacao = date('Y-m-d');
                }else{
                    $data_criacao = implode("-", array_reverse(explode("/", $_POST['data_criacao'])));
                }

                if($_POST['hora_criacao'] == ''){
                    $hora_criacao = date('H:i:s');
                }else{
                    $hora_criacao = $_POST['hora_criacao']; 
                }


                
                $comunicado_abertura            = $_POST['comunicado_abertura'];
                $comunicado_habilitacao         = $_POST['comunicado_habilitacao'];
                $comunicado_julgamento          = $_POST['comunicado_julgamento'];
                $comunicado_homologacao         = $_POST['comunicado_homologacao'];

                $id_fornecedor = $_POST['id_fornecedor']; 

                $le_titulo = $_POST['le_titulo'];
                $le_id =  $_POST['le_id'];

                if($_POST['le_ordem'] == '' || $_POST['le_ordem'] == ' '){
                    $le_ordem = 0; 
                }
                else {
                    $le_ordem = $_POST['le_ordem']; 
                }

                $numero_processo = $_POST['numero_processo']; 

                $dados = array(
                    'id_categoria'       => $id_categoria,
                    'ramo_atuacao'      => $ramo_atuacao,
                    'codigo'             => $codigo,
                    'titulo'             => $titulo,
                    'objetivo'           => $objetivo,
                    'ordem'           => $ordem,
                    'situacao'           => $situacao,
                    'comunicado'         => $comunicado,
                    'exibir'             => $exibir,
                    'data_abertura'      => $data_abertura,
                    'data_habilitacao'   => $data_habilitacao,
                    'data_julgamento'    => $data_julgamento,
                    'data_homologacao'   => $data_homologacao,
                    'abertura_status'    => $abertura_status,
                    'habilitacao_status' => $habilitacao_status,
                    'julgamento_status'  => $julgamento_status,
                    'homologacao_status' => $homologacao_status,
                    'data_criacao'       => $data_criacao,
                    'hora_criacao'       => $hora_criacao,
                    'comunicado_abertura'            => $comunicado_abertura,
                    'comunicado_habilitacao'         => $comunicado_habilitacao,
                    'comunicado_julgamento'          => $comunicado_julgamento,
                    'comunicado_homologacao'         => $comunicado_homologacao,
                    'numero_processo'                => $numero_processo, 
                );

                if ($action == "adicionar") {

                      if($titulo !== '' && $codigo !==''){
                        $sql = "INSERT INTO licitacao_pregao SET " . bindFields($dados);
                        $stmt = $PDO->prepare($sql);    
                        if ($stmt->execute($dados)) {
                            $id = $PDO->lastInsertId();
                            //UPLOAD ARQUIVOS        
    
                            $f = count($id_fornecedor);
                            for ($z = 0; $z < $f; $z++) {
                                $sqlf = "INSERT INTO licitacao_participantes (id_licitacao, id_licitante) VALUES (:id_licitacao, :id_licitante)";
                                $stmtf = $PDO->prepare($sqlf);
                                $stmtf->bindParam(':id_licitante', $id_fornecedor[$z]);
                                $stmtf->bindParam(':id_licitacao', $id);
                                $stmtf->execute(); 
                            }
    
                            $c = count($le_titulo);
                            $caminho = "uploads/licitacoes/";
                            for ($i = 0; $i < $c; $i++) {
                                $nomeArquivo = $_FILES["documento"]["name"][$i];
                                $nomeTemporario = $_FILES["documento"]["tmp_name"][$i];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $titulo = $le_titulo[$i];  
                                $l_ordem =   $le_ordem[$i];
                                if (!empty($nomeArquivo)) {
                                    if (!file_exists($caminho)) {
                                        mkdir($caminho, 0755, true);
                                    }
                                    $lic_documento  = $caminho;
                                    $arq= geradorTags($nomeArquivo); 
                                    $lic_documento .=  $arq . '.' . $extensao;
                                    $lic_documento2 = $arq . '.' . $extensao;

                                    move_uploaded_file($nomeTemporario, ($caminho . $arq.".".$extensao));
                                    $sql = "INSERT INTO licitacao_edital (id_licitacao, le_titulo, documento, le_ordem) VALUES (:id_licitacao, :le_titulo, :documento, :le_ordem)";
                                    $stmt = $PDO->prepare($sql);
                                    $stmt->bindParam(':documento', $lic_documento2);
                                    $stmt->bindParam(':le_titulo', $titulo);
                                    $stmt->bindParam(':id_licitacao', $id);
                                    $stmt->bindParam(':le_ordem', $l_ordem);
                                    if ($stmt->execute()) {
                                    } else {
                                        $erro = 1;
                                        $err = $stmt->errorInfo();
                                        print_r($err);
                                    }
                                }
                            }
                            ?>
                                <script>
                                    abreMask('Operação realizada com sucesso! <br><br>' +
                                    '<input value=\' Voltar \' type=\'button\' class=\'close_janela\' onclick=sucess(<?php echo $id?>);>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                                    function sucess(id){window.location.replace('cadastro_licitacoes/edit/'+id)};
                                </script>
                            <?php
                        } else {
                            $err = $stmt->errorInfo();
                            $errado = $err[2];    
                            echo $errado;                     
                            ?>
                                <script>
                                    abreMask('Erro ao realizar operação! <br><br>' +
                                    '<br><input value=\' Voltar \' type=\'button\' class=\'close_janela\' onclick=sucess();>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                                    function sucess(){window.location.replace('cadastro_licitacoes/add')};
                                </script>
                            <?php
                        }
    
                    }
                }

                if ($action == 'editar') {?>
                                    <script>
                        abreMask('Carregando! <br><br>');
                    </script>
                    
                    <?php $sql = "UPDATE licitacao_pregao SET " . bindFields($dados) . " WHERE lic_id = :lic_id ";
                    $stmt = $PDO->prepare($sql);
                    $dados['lic_id'] =  $lic_id;
                    if ($stmt->execute($dados)) {
                        //UPLOAD ARQUIVOS
                        $caminho = "uploads/licitacoes/";
                        $sql2 = "SELECT * FROM licitacao_edital WHERE id_licitacao = :id_licitacao ";
                        $stmt2 = $PDO->prepare($sql2);
                        $stmt2->bindParam(':id_licitacao', $lic_id);
                        if ($stmt->execute()) {

                            $sqlf = "DELETE FROM licitacao_participantes WHERE id_licitacao =:id_licitacao";
                            $stmtf = $PDO->prepare($sqlf);
                            $stmtf->bindParam(':id_licitacao', $lic_id);
                            if($stmtf->execute()){
                                $f = count($id_fornecedor);
                                for ($z = 0; $z < $f; $z++) {
                                    $sqlf2 = "INSERT INTO licitacao_participantes (id_licitacao, id_licitante) VALUES (:id_licitacao, :id_licitante)";
                                    $stmtf2 = $PDO->prepare($sqlf2);
                                    $stmtf2->bindParam(':id_licitante', $id_fornecedor[$z]);
                                    $stmtf2->bindParam(':id_licitacao', $lic_id);
                                    $stmtf2->execute(); 
                                } 
                            }
                            $c = count($le_titulo);
                            $caminho = "uploads/licitacoes/";
                            for ($i = 0; $i < $c; $i++) {
                                $nomeArquivo = $_FILES["documento"]["name"][$i];
                                $nomeTemporario = $_FILES["documento"]["tmp_name"][$i];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);

                                $titulo = $le_titulo[$i];
                                $l_ordem = $le_ordem[$i];

                                $sql5 = "UPDATE licitacao_edital SET le_titulo = :le_titulo, le_ordem = :le_ordem WHERE le_id = :le_id";
                                $stmt5 = $PDO->prepare($sql5);
                                $stmt5->bindParam(':le_titulo', $titulo);
                                $stmt5->bindParam(':le_ordem', $l_ordem);
                                $stmt5->bindParam(':le_id', $le_id[$i]);
                                if ($stmt5->execute()) {
                                } else {
                                    $erro = 1;
                                    $err = $stmt5->errorInfo();
                                    print_r($err);
                                }
                                if (!empty($nomeArquivo)) {
                                    $lic_documento  = $caminho;
                                    $arq = geradorTags($nomeArquivo);

                                    $lic_documento .=  $arq. '.' . $extensao;
                                    $lic_documento2 = $arq . '.' . $extensao;
                                    if (!file_exists($caminho)) {
                                        mkdir($caminho, 0755, true);
                                    }

                                    move_uploaded_file($nomeTemporario, ($caminho . $arq .".". $extensao));
                                    if ($le_id[$i] == '') {
                                        $sql4 = "INSERT INTO licitacao_edital (id_licitacao, le_titulo, documento, le_ordem) VALUES (:id_licitacao, :le_titulo, :documento, :le_ordem)";
                                        $stmt4 = $PDO->prepare($sql4);
                                        $stmt4->bindParam(':documento', $lic_documento2);
                                        $stmt4->bindParam(':le_titulo', $titulo);
                                        $stmt4->bindParam(':id_licitacao', $lic_id);
                                        $stmt4->bindParam(':le_ordem', $l_ordem);
                                        if ($stmt4->execute()) {
                                        } else {
                                            $erro = 1;
                                            $err = $stmt4->errorInfo();
                                            print_r($err);
                                        }
                                    } else {
                                        $sql4 = "UPDATE licitacao_edital SET id_licitacao = :id_licitacao, le_titulo = :le_titulo, documento = :documento, le_ordem = :le_ordem WHERE le_id = :le_id";
                                        $stmt4 = $PDO->prepare($sql4);
                                        $stmt4->bindParam(':documento', $lic_documento2);
                                        $stmt4->bindParam(':le_titulo', $titulo);
                                        $stmt4->bindParam(':id_licitacao', $lic_id);
                                        $stmt4->bindParam(':le_id', $le_id[$i]);
                                        $stmt4->bindParam(':le_ordem', $l_ordem);
                                        if ($stmt4->execute()) {
                                        } else {
                                            $erro = 1;
                                            $err = $stmt4->errorInfo();
                                            print_r($err);
                                        }
                                    }
                                }
                            }
                        }

                    ?>
                            <script>
                                abreMask('Operação realizada com sucesso! <br><br>' +
                                '<input value=\' Voltar \' type=\'button\' class=\'close_janela\' onclick=sucess(<?php echo $lic_id?>);>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                                function sucess(id){window.location.replace('cadastro_licitacoes/edit/'+id)};
                            </script>
                    <?php
                    } else {
                        $err = $stmt->errorInfo();

                        print_r($err);

                    ?>
                        <script>
                            mensagem("X", "<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                        </script>
                    <?php
                    }
                }

                if ($action == 'excluir') {
                    // PEGA CAMINHO DO ARQUIVO PARA FAZER EXCLUSÃO
                    $sql = "SELECT * FROM licitacao_pregao WHERE lic_id = :lic_id ";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam(':lic_id', $lic_id);
                    if ($stmt->execute()) {
                        $result = $stmt->fetch();
                        $arquivo = $result['lic_documento'];
                    }


                    $sql = "DELETE FROM licitacao_pregao WHERE lic_id = :lic_id ";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam(':lic_id', $lic_id);
                    if ($stmt->execute()) {
                        // EXCLUI ARQUIVO DO FTP
                        unlink($arquivo);

                    ?>
                        <script>
                            mensagem("Ok", "<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                        </script>
                    <?php
                    } else {
                    ?>
                        <script>
                            mensagem("X", "<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                        </script>
                <?php
                    }
                }

                $num_por_pagina = 20;
                if (!$pag) {
                    $primeiro_registro = 0;
                    $pag = 1;
                } else {
                    $primeiro_registro = ($pag - 1) * $num_por_pagina;
                }

                $fil_nome = $_REQUEST['fil_nome'];
                $fil_categoria = $_REQUEST['categorias'];


                if ($fil_nome == '' && $fil_categoria == '') {
                    $nome_query = " 1 = 1 ";
                } elseif ($fil_nome != '' && $fil_categoria == '') {
                    $fil_nome1 = "%" . $fil_nome . "%";
                    $nome_query = " (codigo LIKE :fil_nome1  ) ";
                } elseif ($fil_nome != '' && $fil_categoria != '' ) {
                    $fil_nome1 = "%" . $fil_nome . "%";
                    $nome_query = " (codigo LIKE :fil_nome1  AND lic_id = :fil_categoria) ";
                }elseif ($fil_nome == '' && $fil_categoria != '' ) {
                    $fil_nome1 = "%" . $fil_nome . "%";
                    $nome_query = " (lc_id = :fil_categoria) ";
                } 

                $sql = "SELECT * FROM licitacao_pregao 
                            LEFT JOIN licitacao_categorias ON licitacao_categorias.lc_id = licitacao_pregao.id_categoria
                            WHERE " . $nome_query . " ORDER BY lic_id DESC
                            LIMIT :primeiro_registro, :num_por_pagina ";
                $stmt = $PDO->prepare($sql);
                $stmt->bindParam(':fil_nome1',     $fil_nome1);
                $stmt->bindParam(':fil_categoria',     $fil_categoria);
                $stmt->bindParam(':primeiro_registro',     $primeiro_registro);
                $stmt->bindParam(':num_por_pagina',     $num_por_pagina);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if ($pagina == "view") {
                    echo "
                            <div class='titulo'> $page  </div>
                            <div id='botoes'>
                                <div class='filtro2'>
                                <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(" . $permissoes["add"] . ",\"cadastro_licitacoes/add\");'><i class='fas fa-plus'></i></div>

                                    <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_licitacoes/view'>
                                        <input type='text' name='fil_nome' id='fil_nome' value='$fil_nome' placeholder='Título'>
                                        <select name='categorias' id='categorias'>
                                        </select>                                    
                                        <input type='submit' value='Filtrar'> 
                                    </form> 

                                </div>           
                            </div>
                        ";
                    if ($rows > 0) {
                        echo "
                                <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                    <tr>
                                        <td class='titulo_first'>Código</td>
                                        <td class='titulo_first'>Número do Processo</td>
                                        <td class='titulo_tabela'> Título </td>
                                        <td class='titulo_tabela'> Categoria </td>
                                        <td class='titulo_tabela'> Data de Criação </td>
                                        <td class='titulo_last' align='right'>Gerenciar</td>
                                    </tr>";
                        $c = 0;
                        while ($result = $stmt->fetch()) {
                            $data       = date("d/m/Y", strtotime($result['data_criacao']));
                            $nome         = $result['codigo'];
                            $titulo         = $result['titulo'];
                            $cat_nome   = $result['lc_titulo'];
                            $lic_id         = $result['lic_id'];
                            $numero_processo = $result['numero_processo'];

                            if ($c == 0) {
                                $c1 = "linhaimpar";
                                $c = 1;
                            } else {
                                $c1 = "linhapar";
                                $c = 0;
                            }
                            echo "<tr class='$c1'>
                                                <td>$nome</td>
                                                <td>$numero_processo</td>
                                                <td>$titulo</td>
                                                <td>$cat_nome</td>
                                                <td>$data</td>
                                                <td align=center>
                                                    <div class='g_excluir' onclick=\"
                                                            abreMask(
                                                                'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'cadastro_licitacoes/view/excluir/$lic_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                            \">	<i class='far fa-trash-alt'></i>
                                                    </div>
                                                    <div class='g_editar'  title='Editar' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"cadastro_licitacoes/edit/$lic_id\");'><i class='fas fa-pencil-alt'></i></div>											
                                                </td>
                                            </tr>";
                        }
                        echo "</table>";
                        $variavel = "&fil_nome=$fil_nome&fil_categoria=$fil_categoria";
                        $cnt = "SELECT COUNT(*) FROM licitacao_pregao 
                        LEFT JOIN licitacao_categorias ON licitacao_categorias.lc_id = licitacao_pregao.id_categoria
                        WHERE " . $nome_query . "";
                        $stmt = $PDO->prepare($cnt);
                        $stmt->bindParam(':fil_nome1',     $fil_nome1);
                        $stmt->bindParam(':fil_categoria',     $fil_categoria);
                        include("../core/mod_includes/php/paginacao.php");
                    } else {
                        echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                    }
                }

                if ($pagina == 'add') {
                    echo "	
                        <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_licitacoes/view/adicionar'>
                            <div class='titulo'> $page &raquo; Adicionar  </div>
                            <ul class='nav nav-tabs'>
                                <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                                <li><a data-toggle='tab' href='#documentos'>Documentos</a></li>
                                <li><a data-toggle='tab' href='#datas_licitacao'>Comunicados</a></li>
                                <li><a data-toggle='tab' href='#participantes'>Participantes</a></li>
                            </ul>
                            <div class='tab-content'>

                                <div id='dados_gerais' class='tab-pane fade in active'>
                                    <label>Título:</label> <input name='titulo' id='titulo' placeholder='Título' class='obg'>
                                    <p><label>Codigo:</label> <input name='codigo' id='codigo' placeholder='Código' class='obg'>
                                    <p><label>Número do Processo:</label> <input name='numero_processo' id='numero_processo' placeholder='Número do Processo'>
                                    <p><label>Categoria:</label> 
                                        <select id='id_categoria' name='id_categoria'>    
                                            <option value='0'> Selecione </option>";
                                            $sql = "SELECT * FROM licitacao_categorias";
                                            $stmt = $PDO->prepare($sql);
                                            $stmt->execute();
                                            $rows = $stmt->rowCount();
                                            if ($rows > 0) {
                                                while ($result = $stmt->fetch()) {
                                                    echo "<option value='" . $result['lc_id'] . "'>" . $result['lc_titulo'] . "</option>";
                                                }
                                            }
                                            echo " 
                                            <option value='nova_categoria'><b>Nova Categoria*</b> </option>
                                          
                                        </select> 
                                        <p id='cat' style='display:none'><label>Nova Categoria:</label> <input name='lc_titulo' id='lc_titulo' placeholder='Nova Categoria'>

                                        <p><label>Ramo de Atuação:</label> 
                                        <select id='ramo_atuacao' name='ramo_atuacao'>    
                                            <option value='0'> Selecione </option>";
                                            $sql = "SELECT * FROM fornecedores_ramo_atuacao";
                                            $stmt = $PDO->prepare($sql);
                                            $stmt->execute();
                                            $rows = $stmt->rowCount();
                                            if ($rows > 0) {
                                                while ($result = $stmt->fetch()) {
                                                    echo "<option value='" . $result['fra_id'] . "'>" . $result['fra_descricao'] . "</option>";
                                                }
                                            }
                                            echo "
                                            <option value='novo_ramo'><b>Novo Ramo de Atuação*</b> </option>

                                            </select> 
                                        <p id='ra' style='display:none'><label>Novo Ramo de Atuação:</label> <input name='fra_descricao' id='fra_descricao' placeholder='Novo Ramo de Atuação'>

                                        <p><label>Objetivo:</label> <div class='textarea'><textarea  name='objetivo' id='objetivo' placeholder='Objetivo'></textarea></div>
                                        <p><label>Comunicado:</label> <div class='textarea'><textarea  name='comunicado' id='comunicado' placeholder='Comunicado'></textarea></div>
                                        <p><label>Ordenação:</label> <input type='number' name='ordem' id='ordem'>
                                        <div class='bloco'>
                                            <p><label>Data e Hora*:</label>
                                            <input type='text' name='data_criacao' id='data_criacao'> 
                                            <input type='time' name='hora_criacao' id='hora_criacao'>
                                        </div>
        
                                        <p><label>Status:</label> <input type='radio' name='exibir' value='1' > Exibir &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type='radio' name='exibir' value='0' checked> Não Exibir<br>		
                                    </div>

                                <div id='documentos' class='tab-pane fade in'>
                                    <p><label>Titulo:</label> <input type='text' name='ttl_doc' id='ttl_doc'>
                                    <center onclick='addDoc()' style='font-size:18px; font-weight:bold; cursor:pointer'>Adicionar </center> <br><br>
                                    <table class='doc'>
                                        <tr>
                                            <th width='55%'> Titulo  </th>
                                            <th width='30%'> Documento </th>
                                            <th width='5%'> Ordem </th>
                                            <th width='10%'> Excluir </th>
                                        </tr>
                                    </table>
                             </div>

                                <div id='datas_licitacao' class='tab-pane fade in'>
                                    <p><label>Abertura*:</label> <div class='textarea'><textarea  name='comunicado_abertura' id='comunicado_abertura' placeholder='Objetivo'></textarea></div>
                                    <div class='bloco'>
                                        <p><label>Data e Hora*:</label>
                                        <input type='text' name='data1' id='data1'> 
                                        <input type='time' name='hora1' id='hora1'>
                                    </div>
                                    <p><label>Status:</label> <input type='radio' name='abertura_status' value='1' > Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type='radio' name='abertura_status' value='0' checked> Inativo<br>		
            
                                    <p><label>Habilitação*:</label> <div class='textarea'><textarea  name='comunicado_habilitacao' id='comunicado_habilitacao' placeholder='Objetivo'></textarea></div>
                                    <div class='bloco'>
                                        <p><label>Data e Hora:</label> 
                                        <input type='text' name='data2' id='data2'> 
                                        <input type='time' name='hora2' id='hora2'>
                                    </div>
                                    <p><label>Status:</label> <input type='radio' name='habilitacao_status' value='1' > Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type='radio' name='habilitacao_status' value='0' checked> Inativo<br>		

                                    <p><label>Julgamento*:</label> <div class='textarea'><textarea  name='comunicado_julgamento' id='comunicado_julgamento' placeholder='Objetivo'></textarea></div>
                                    <div class='bloco'>
                                        <p><label>Data e hora:</label> 
                                        <input type='text' name='data3' id='data3'> 
                                        <input type='time' name='hora3' id='hora3'>
                                    </div>
                                    <p><label>Status:</label> <input type='radio' name='julgamento_status' value='1' > Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type='radio' name='julgamento_status' value='0' checked> Inativo<br>		

                                    <p><label>Homologação*:</label> <div class='textarea'><textarea  name='comunicado_homologacao' id='comunicado_homologacao' placeholder='Objetivo'></textarea></div>
                                    <div class='bloco'>
                                        <p><label>data e Hora:</label> 
                                        <input type='text' name='data4' id='data4'> 
                                        <input type='time' name='hora4' id='hora4'>
                                    </div>
                                    <p><label>Status:</label> <input type='radio' name='homologacao_status' value='1' > Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type='radio' name='homologacao_status' value='0' checked> Inativo<br><br>
                                    
                                    <p><label>Situação:</label> 
                                        <select id='situacao' name='situacao'>    
                                            <option value='0'> Selecione... </option>
                                            <option value='1'> Cancelado </option>
                                            <option value='2'> Revogado </option>
                                        </select>

                                </div>

                                <div id='participantes' class='tab-pane fade in' >
                                    <p><label>CNPJ:</label> <input type='text' name='busca' id='busca' placeholder='CNPJ'>
                                    <table class='doc2 busca'> </table>

                                    <table class='doc2' id='forn'>
                                        <tr>
                                            <th width='90%'> Paricipante  </th>
                                            <th width='10%'> Selecionar </th>
                                        </tr>
                                    </table>
                                </div>


                                <center>
                                <div id='erro' align='center'>&nbsp;</div>
                                <input type='submit' id='bt_cadastro_licitacoes' value='Salvar' onclick=\"
                                abreMask('Carregando conteudo!');
                                \">&nbsp;&nbsp;&nbsp;&nbsp; 
                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_licitacoes/view'; value='Voltar'/></center>
                                </center>
                            </div>
                        </form>
                        ";
                }

                if ($pagina == 'edit') {
                    $sql = "SELECT * FROM licitacao_pregao 
                        LEFT JOIN licitacao_categorias ON licitacao_categorias.lc_id = licitacao_pregao.id_categoria
                        LEFT JOIN fornecedores_ramo_atuacao ON fornecedores_ramo_atuacao.fra_id = licitacao_pregao.ramo_atuacao
                        WHERE lic_id = :lic_id";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam(':lic_id', $lic_id);
                    $stmt->execute();
                    $rows = $stmt->rowCount();
                    if ($rows > 0) {
                        $result = $stmt->fetch();
                        $objetivo = $result['objetivo'];
                        $comunicado = $result['comunicado'];

                        $comunicado_abertura = $result['comunicado_abertura'];
                        $comunicado_habilitacao = $result['comunicado_habilitacao'];
                        $comunicado_julgamento = $result['comunicado_julgamento'];
                        $comunicado_homologacao = $result['comunicado_homologacao'];
                        $ordem = $result['ordem'];
                        $situacao = $result['situacao'];

                        $data1 = date_create($result['data_criacao']);
                        $data_criacao = date_format($data1, "d/m/Y");

                        if ($result['data_abertura'] != '') {
                            $data2 = date_create($result['data_abertura']);
                            $data_abertura = date_format($data2, "d/m/Y");
                            $hora_abertura = date_format($data2, "H:i");
                        }
                        if ($result['data_habilitacao'] != '') {
                            $data3 = date_create($result['data_habilitacao']);
                            $data_habilitacao = date_format($data3, "d/m/Y");
                            $hora_habilitacao = date_format($data3, "H:i");
                        }
                        if ($result['data_julgamento'] != '') {
                            $data4 = date_create($result['data_julgamento']);
                            $data_julgamento = date_format($data4, "d/m/Y");
                            $hora_julgamento = date_format($data4, "H:i");
                        }
                        if ($result['data_homologacao'] != '') {
                            $data5 = date_create($result['data_homologacao']);
                            $data_homologacao = date_format($data5, "d/m/Y");
                            $hora_homologacao = date_format($data5, "H:i");
                        }

                        echo "
                            <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_licitacoes/edit/editar/$lic_id'>
                                <div class='titulo'> $page &raquo; Adicionar  </div>
                                <ul class='nav nav-tabs'>
                                    <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                                    <li><a data-toggle='tab' href='#documentos'>Documentos</a></li>
                                    <li><a data-toggle='tab' href='#datas_licitacao'>Comunicados</a></li>
                                    <li><a data-toggle='tab' href='#participantes'>Participantes</a></li>
                                </ul>
                                <div class='tab-content'>

                                    <div id='dados_gerais' class='tab-pane fade in active'>
                                        <label>Título:</label> <input name='titulo' id='titulo' placeholder='Título' value='" . $result['titulo'] . "' class='obg'>
                                        <p><label>Codigo:</label> <input name='codigo' id='codigo' placeholder='Código' value='" . $result['codigo'] . "' class='obg'>
                                        <p><label>Número do Processo:</label> <input name='numero_processo' id='numero_processo' placeholder='Número do Processo' value='".$result['numero_processo']."'>
                                        <p><label>Categoria:</label> 
                                            <select id='id_categoria' name='id_categoria'>";
                                            if($result['id_categoria'] == '0'){
                                                echo"<option value='0'> Selecione </option>";
                                            }else {
                                                echo"<option value='" . $result['lc_id'] . "'> " . $result['lc_titulo'] . " </option>";
                                            }
                                                $sql1 = "SELECT * FROM licitacao_categorias";
                                                $stmt1 = $PDO->prepare($sql1);
                                                $stmt1->execute();
                                                $rows1 = $stmt1->rowCount();
                                                if ($rows1 > 0) {
                                                    while ($result1 = $stmt1->fetch()) {
                                                        echo "<option value='" . $result1['lc_id'] . "'>" . $result1['lc_titulo'] . "</option>";
                                                    }
                                                }
                                                echo "
                                                <option value='nova_categoria'><b>Nova Categoria*</b> </option>
                                          
                                                </select> 
                                                <p id='cat' style='display:none'><label>Nova Categoria:</label> <input name='lc_titulo' id='lc_titulo' placeholder='Nova Categoria'>
        
                                            <p><label>Ramo de Atuação:</label> 
                                            <select id='ramo_atuacao' name='ramo_atuacao'>"; 
                                                if($result['ramo_atuacao'] == '0'){
                                                    echo"<option value='0'> Selecione </option>";
                                                }else {
                                                    echo"<option value='" . $result['fra_id'] . "'> " . $result['fra_descricao'] . " </option>";
                                                }
                                                
                                                $sql2 = "SELECT * FROM fornecedores_ramo_atuacao";
                                                $stmt2 = $PDO->prepare($sql2);
                                                $stmt2->execute();
                                                $rows2 = $stmt2->rowCount();
                                                if ($rows2 > 0) {
                                                    while ($result2 = $stmt2->fetch()) {
                                                        echo "<option value='" . $result2['fra_id'] . "'>" . $result2['fra_descricao'] . "</option>";
                                                    }
                                                }
                                                echo "                                            
                                                <option value='novo_ramo'><b>Novo Ramo de Atuação*</b> </option>
                                                </select> 
                                            <p id='ra' style='display:none'><label>Novo Ramo de Atuação:</label> <input name='fra_descricao' id='fra_descricao' placeholder='Novo Ramo de Atuação'>
    

                                            <p><label>Objetivo:</label> <div class='textarea'><textarea  name='objetivo' id='objetivo' placeholder='Objetivo'>$objetivo</textarea></div>
                                            <p><label>Comunicado:</label> <div class='textarea'><textarea  name='comunicado' id='comunicado' placeholder='Comunicado'>$comunicado</textarea></div>
                                            <p><label>Ordenação:</label> <input type='text' name='ordem' id='ordem' value='$ordem'>
                                            <div class='bloco'>
                                                <p><label>Data e Hora*:</label>
                                                <input type='text' name='data_criacao' id='data_criacao' value='" . implode("/", array_reverse(explode("-", $data_criacao))) . "'> 
                                                <input type='time' name='hora_criacao' id='hora_criacao' value='" . $result['hora_criacao'] . "'>
                                            </div>
                                                        <p><label>Status:</label>";
                        if ($result['exibir'] == 1) {
                            echo "<input type='radio' name='exibir' value='1' checked> Exibir &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <input type='radio' name='exibir' value='0'> Não Exibir
                                                        ";
                        } else {
                            echo "<input type='radio' name='exibir' value='1'> Exibir &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <input type='radio' name='exibir' value='0' checked> Não Exibir
                                                        ";
                        }
                        echo "
                                                        </div>

                                    <div id='documentos' class='tab-pane fade in'>
                                    
                                        <p><label>Titulo:</label> <input type='text' name='ttl_doc' id='ttl_doc'>
                                        <center onclick='addDoc()' style='font-size:18px; font-weight:bold; cursor:pointer'>Adicionar </center> <br><br>
                                        <table class='doc'>
                                            <tr>
                                                <th width='45%'> Titulo  </th>
                                                <th width='40%'> Documento </th>
                                                <th width='5%'> Ordem </th>
                                                <th width='10%'> Excluir </th>
                                            </tr>";
                        $sql3 = "SELECT * FROM licitacao_edital
                                        WHERE id_licitacao = :id_licitacao
                                        ORDER BY le_ordem ASC";
                        $stmt3 = $PDO->prepare($sql3);
                        $stmt3->bindParam(':id_licitacao', $lic_id);
                        $stmt3->execute();
                        $rows3 = $stmt3->rowCount();
                        if ($rows3 > 0) {
                            while ($result3 = $stmt3->fetch()) {
                                echo "
                                    <tr id='" . $result3['le_id'] . "'>
                                        <td><input type='hidden' name='le_id[]' id='le_id' value='" . $result3['le_id'] . "'><input type='text' name='le_titulo[]' id='le_titulo' value='" . $result3['le_titulo'] . "' ></td>
                                        <td>
                                            <input type='file' name='documento[]' id='documento' value='uploads/licitacoes/" . $result3['documento'] . "' style='width:100%; border:none; padding:0; margin:0; margin-bottom: 15px; display:table;  ' >";
                                            if($result3['documento'] !== ''){
                                               echo "<a href='uploads/licitacoes/" . $result3['documento'] . "' target='_blank'><i class='fas fa-file-alt' style='font-size:20px'></i> - " . $result3['documento'] . "</a>"; 
                                            }
                                        
                                        echo "</td>
                                        <td> <input type='text' name='le_ordem[]' id='le_ordem' value='".$result3['le_ordem'] . "' style='width:100%' ></td>
                                        <td style='text-align:center'> <i class='fas fa-times' onclick='remover(" . $result3['le_id'] . ")' style='cursor:pointer'></i> </td>
                                    </tr>
                                ";
                            }
                        }

                        echo " </table>
                                    </div>
        
                                    <div id='datas_licitacao' class='tab-pane fade in'>
                                        <p><label>Abertura*:</label> <div class='textarea'><textarea  name='comunicado_abertura' id='comunicado_abertura'>$comunicado_abertura</textarea></div>
                                        <div class='bloco'>
                                            <p><label>Data e Hora*:</label>
                                            <input type='text' name='data1' id='data1' value='$data_abertura'> 
                                            <input type='time' name='hora1' id='hora1' value='$hora_abertura'>
                                        </div>
                                        <p><label>Status:</label>";
                                            if ($result['abertura_status'] == 1) {
                                                echo "<input type='radio' name='abertura_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <input type='radio' name='abertura_status' value='0'> Inativo";
                                            } else {
                                                echo "<input type='radio' name='abertura_status' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <input type='radio' name='abertura_status' value='0' checked> Inativo";
                                            }
                                            echo "
               
                                        <p><label>Habilitação*:</label> <div class='textarea'><textarea  name='comunicado_habilitacao' id='comunicado_habilitacao'> $comunicado_habilitacao</textarea></div>
                                        <div class='bloco'>
                                            <p><label>Data e Hora:</label> 
                                            <input type='text' name='data2' id='data2' value='$data_habilitacao'> 
                                            <input type='time' name='hora2' id='hora2' value='$hora_habilitacao'>
                                        </div>
                                        <p><label>Status:</label>";
                                        if ($result['habilitacao_status'] == 1) {
                                            echo "<input type='radio' name='habilitacao_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type='radio' name='habilitacao_status' value='0'> Inativo";
                                        } else {
                                            echo "<input type='radio' name='habilitacao_status' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type='radio' name='habilitacao_status' value='0' checked> Inativo";
                                        }
                                        echo "
                                        <p><label>Julgamento*:</label> <div class='textarea'><textarea  name='comunicado_julgamento' id='comunicado_julgamento'> $comunicado_julgamento</textarea></div>
                                        <div class='bloco'>
                                            <p><label>Data e hora:</label> 
                                            <input type='text' name='data3' id='data3' value='$data_julgamento'> 
                                            <input type='time' name='hora3' id='hora3' value='$hora_julgamento'>
                                        </div>
                                        <p><label>Status:</label>";
                                        if ($result['julgamento_status'] == 1) {
                                            echo "<input type='radio' name='julgamento_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type='radio' name='julgamento_status' value='0'> Inativo";
                                        } else {
                                            echo "<input type='radio' name='julgamento_status' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type='radio' name='julgamento_status' value='0' checked> Inativo";
                                        }
                                        echo "
                                        <p><label>Homologação*:</label> <div class='textarea'><textarea  name='comunicado_homologacao' id='comunicado_homologacao'>  $comunicado_homologacao</textarea></div>
                                        <div class='bloco'>
                                            <p><label>data e Hora:</label> 
                                            <input type='text' name='data4' id='data4' value='$data_homologacao'> 
                                            <input type='time' name='hora4' id='hora4' value='$hora_homologacao'>
                                        </div>
                                        <p><label>Status:</label>";
                                        if ($result['homologacao_status'] == 1) {
                                            echo "<input type='radio' name='homologacao_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type='radio' name='homologacao_status' value='0'> Inativo";
                                        } else {
                                            echo "<input type='radio' name='homologacao_status' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type='radio' name='homologacao_status' value='0' checked> Inativo";
                                        }
                                        echo "<br>
                                        <p><label>Situação:</label> 
                                        <select id='situacao' name='situacao'>    
                                            <option value='0' ".($situacao == '0' ? 'selected' : '')."> Selecione... </option>
                                            <option value='1' ".($situacao == '1' ? 'selected' : '')."> Cancelado </option>
                                            <option value='2' ".($situacao == '2' ? 'selected' : '')."> Revogado </option>
                                           
                                        </select>
                                    </div>

                                    <div id='participantes' class='tab-pane fade in' >
                                        <p><label>CNPJ:</label> <input type='text' name='busca' id='busca' placeholder='CNPJ'>
                                        <table class='doc2 busca'> </table>

                                        <table class='doc2' id='forn'>
                                            <tr>
                                                <th width='90%'> Paricipante  </th>
                                                <th width='10%'> Selecionar </th>
                                            </tr>";
                                                $sql9="SELECT * FROM licitacao_pregao
                                                LEFT JOIN licitacao_participantes ON licitacao_participantes.id_licitacao = licitacao_pregao.lic_id
                                                LEFT JOIN fornecedores ON fornecedores.id = licitacao_participantes.id_licitante
                                                LEFT JOIN fornecedor_atributos ON fornecedor_atributos.fa_fornecedor = fornecedores.id
                                                WHERE lic_id = :lic_id AND id <> :id ";
                                                $stmt9 = $PDO->prepare($sql9);
                                                $stmt9->bindParam(':lic_id', $lic_id);
                                                $stmt9->bindValue(':id', '');
                                                $stmt9->execute();
                                                $rows9 = $stmt9->rowCount();
                                                if ($rows9 > 0) {	
                                                    while($result9 = $stmt9->fetch()){
                                                        echo "<tr id='".$result9['id']."'>
                                                                <td><h3><b>".$result9['field1']."</b></h3><i>".$result9['field2']."</i> - ".$result9['fa_cnpj']." </td>
                                                                <td><input type='checkbox' class='rm_licitante' id='id_fornecedor[]' name='id_fornecedor[]' value='".$result9['id']."' checked></td>
                                                            </tr>				
                                                        ";
                                                    }
                                                }
                                        echo"</table>
                                    </div>
                                    <center>
                                    <div id='erro' align='center'>&nbsp;</div>
                                    <input type='submit' id='bt_cadastro_licitacoes' value='Salvar' onclick=\"
                                    abreMask('Carregando conteudo!');
                                    \">&nbsp;&nbsp;&nbsp;&nbsp; 
                                    <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_licitacoes/view'; value='Voltar'/></center>
                                    </center>
                                </div>
                            </form>
                            ";
                    }
                }
                ?>
            </div>
        </div> <!-- .content-wrapper -->
    </main> <!-- .cd-main-content -->
</body>

</html>

<script>
    $("#busca").mask("00.000.000/0000-00");

    function addDoc() {
        var titulo = $("#ttl_doc").val();
        var doc = $("#doc").val();
        $('.doc').append("<tr><td><input id='le_titulo[]' name='le_titulo[]' value='" + titulo + "'></td> <td><input type='file' id='documento[]' name='documento[]' style='width:100%' ></td><td><input type='text' id='le_ordem' name='le_ordem[]' style='width:100%' ></td> <td style='text-align:center'> <i class='fas fa-times' id='remover'></i> </td></tr>");
    }

    function remover(le_id) {

        abreMask('Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>' +
            '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=rm(' + le_id + ');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
            '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');

    };

    function rm(le_id) {
        if (le_id == '') {} else {
            $.post("excluir.php", {
                    action: 'excluir_documento',
                    le_id: le_id
                },
                function() {
                    $('tr#' + le_id).remove();
                }
            )
        }
    }

    $(".doc").on("click", "#remover", function(e) {
        $(this).closest('tr').remove();
    });


    $( "#busca" ).change(function() {
		var valor = $(this).val();
        if(valor == ' ' || valor == ''){
            $('.busca').html('Parâmetro de busca inválido!'); 
        }else {
            $('.busca').html('<p>Carregando <img src="../core/imagens/carregando.gif"> </p>'); 
            $.post("../carrega_conteudo.php",{pagina: 'carrega_fornecedor', busca:valor},
                function(dados){
                    $('.busca').html(dados); 
                }
            )
        }
	});

    $(".rm_licitante").change(function() {
        if(this.checked) {
		}else {
			var valor = this.value; 
			$('tr#'+valor).remove();

		}
    });




    $("#id_categoria").change(function() {
        var valor = $(this).val();
        if (valor == 'nova_categoria') {
            $('#cat').css('display', 'block');
        }
        else {
            $('#cat').css('display', 'none');
  
        }
    });

    $("#lc_titulo").change(function() {
        var valor = $(this).val();
        $.post("../carrega_conteudo.php", {
                pagina: 'cadastra_categoria_licitacao',
                categoria: valor
            },
            function(dados) {
                if (dados != '') {
                    $('#cat').css('display', 'none');
                    $("#id_categoria").append('<option value="'+dados+'" selected>'+valor+'</option>')
                }
            }
        )
    });


    $("#ramo_atuacao").change(function() {
        var valor = $(this).val();
        if (valor == 'novo_ramo') {
            $('#ra').css('display', 'block');
        }
        else {
            $('#ra').css('display', 'none');
  
        }

    });

    $("#fra_descricao").change(function() {
        var valor = $(this).val();
        $.post("../carrega_conteudo.php", {
                pagina: 'cadastro_ramo_atuacao',
                categoria: valor
            },
            function(dados) {
                if (dados != '') {
                    $('#ra').css('display', 'none');
                    $("#ramo_atuacao").append('<option value="'+dados+'" selected>'+valor+'</option>')
                }
            }
        )
    });
</script>