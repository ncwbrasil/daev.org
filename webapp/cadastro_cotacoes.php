<?php
$pagina_link = 'cadastro_cotacoes';
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start(); 
$page = "<a href='cadastro_cotacoes/view'>Cadastro de Cotações</a>"; 

?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include_once("header.php")?>

    <!-- DRAGDROP -->
    <link href="../core/mod_includes/js/dragdrop/dropzone.css" type="text/css" rel="stylesheet" />
    <script src="../core/mod_includes/js/dragdrop/dropzone.js"></script>

</head>
<body>
	<main class="cd-main-content">
        <div class="container">
            <?php include('../core/mod_menu/menu/menu.php')?>
			<div class="wrapper">
            <div class='mensagem'></div>
            <?php
            $page = " <a href='cadastro_cotacoes/view'> Cotações</a>";
            if (isset($_GET['cot_id'])) {
                $cot_id = $_GET['cot_id'];
            }
            if ($cot_id == '') {
                $cot_id = $_POST['cot_id'];
            }
            $cot_nome             = $_POST['cot_titulo'];
            $cot_descricao       =  $_POST['cot_descricao'];
            $cot_url             = geradorTags($cot_nome);
            $cot_data            = implode("-", array_reverse(explode("/", $_POST['cot_data'])));
            $cot_status          = $_POST['cot_status'];
            $cot_usuario         =  $_SESSION['usuario_id']; 
            $dados = array(
                'cot_titulo'         => $cot_nome,
                'cot_descricao'      => $cot_descricao,
                'cot_url'            => $cot_url,
                'cot_data'           => $cot_data,
                'cot_status'         => $cot_status,
                'cot_usuario'        => $cot_usuario,
            );
            
            if ($action == "adicionar") {

                $sql = "INSERT INTO cadastro_cotacoes SET " . bindFields($dados);
                $stmt = $PDO->prepare($sql);
                if ($stmt->execute($dados)) {
                    $cot_id = $PDO->lastInsertId();
                    //UPLOAD ARQUIVOS
                    $caminho = "uploads/cotacoes/";
                    foreach ($_FILES as $key => $files) {
                        $files_test = array_filter($files['name']);
                        if (!empty($files_test)) {
                            if (!file_exists($caminho)) {
                                mkdir($caminho, 0755, true);
                            }
                            if (!empty($files["name"]["documento"])) {
                                $nomeArquivo     = $files["name"]["documento"];
                                $nomeTemporario = $files["tmp_name"]["documento"];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $cot_documento    = $caminho;
                                $cot_documento .= "cotacao_" . $nomeArquivo . '.' . $extensao;
                                move_uploaded_file($nomeTemporario, ($cot_documento));
                                $sql = "UPDATE cadastro_cotacoes SET 
                                    cot_documento 	 = :cot_documento
                                    WHERE cot_id = :cot_id ";
                                $stmt = $PDO->prepare($sql);
                                $stmt->bindParam(':cot_documento', $cot_documento);
                                $stmt->bindParam(':cot_id', $cot_id);

                                if ($stmt->execute()) {
                                } else {
                                    $erro = 1;
                                    $err = $stmt->errorInfo();
                                }
                            }
                        }
                    }
   
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

            if ($action == 'editar') {

                $sql = "UPDATE cadastro_cotacoes SET " . bindFields($dados) . " WHERE cot_id = :cot_id ";
                $stmt = $PDO->prepare($sql);
                $dados['cot_id'] =  $cot_id;
                if ($stmt->execute($dados)) {
                    //UPLOAD ARQUIVOS
                    $caminho = "uploads/noticias/";
                    foreach ($_FILES as $key => $files) {
                        $files_test = array_filter($files['name']);
                        if (!empty($files_test)) {
                            if (!file_exists($caminho)) {
                                mkdir($caminho, 0755, true);
                            }
                            if (!empty($files["name"]["documento"])) {
                                # EXCLUI ANEXO ANTIGO #
                                $sql = "SELECT * FROM cadastro_cotacoes WHERE cot_id = :cot_id";
                                $stmt_antigo = $PDO->prepare($sql);
                                $stmt_antigo->bindParam(':cot_id', $cot_id);
                                $stmt_antigo->execute();
                                $result_antigo = $stmt_antigo->fetch();
                                $documento_antigo = $result_antigo['cot_documento'];
                                unlink($documento_antigo);

                                $nomeArquivo     = $files["name"]["documento"];
                                $nomeTemporario = $files["tmp_name"]["documento"];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $cot_documento    = $caminho;
                                $cot_documento .= "cotacao_" . $nomeArquivo . '.' . $extensao;
                                move_uploaded_file($nomeTemporario, ($cot_documento));
                                $sql = "UPDATE cadastro_cotacoes SET 
									cot_documento 	 = :cot_documento
									WHERE cot_id = :cot_id ";
                                $stmt = $PDO->prepare($sql);
                                $stmt->bindParam(':cot_documento', $cot_documento);
                                $stmt->bindParam(':cot_id', $cot_id);
                                if ($stmt->execute()) {
                                } else {
                                    $erro = 1;
                                    $err = $stmt->errorInfo();
                                }
                            }
                        }
                    }
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

            if ($action == 'excluir') {
                $sql = "DELETE FROM cadastro_cotacoes WHERE cot_id = :cot_id";
                $stmt = $PDO->prepare($sql);
                $stmt->bindParam(':cot_id', $cot_id);
                if ($stmt->execute()) {
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
            if ($action == 'ativar') {
                $sql = "UPDATE cadastro_cotacoes SET cot_status = :cot_status WHERE cot_id = :cot_id ";
                $stmt = $PDO->prepare($sql);
                $stmt->bindValue(':cot_status', 1);
                $stmt->bindParam(':cot_id', $cot_id);
                $stmt->execute();
            }
            if ($action == 'desativar') {
                $sql = "UPDATE cadastro_cotacoes SET cot_status = :cot_status WHERE cot_id = :cot_id ";
                $stmt = $PDO->prepare($sql);
                $stmt->bindValue(':cot_status', 0);
                $stmt->bindParam(':cot_id', $cot_id);
                $stmt->execute();
            }

            $num_por_pagina = 20;
            if (!$pag) {
                $primeiro_registro = 0;
                $pag = 1;
            } else {
                $primeiro_registro = ($pag - 1) * $num_por_pagina;
            }
            $fil_nome = $_REQUEST['fil_nome'];
            if ($fil_nome == '') {
                $nome_query = " 1 = 1 ";
            } else {
                $fil_nome1 = "%" . $fil_nome . "%";
                $nome_query = " (cot_titulo LIKE :fil_nome1  ) ";
            }

            $sql = "SELECT * FROM cadastro_cotacoes 
                WHERE " . $nome_query . "
                ORDER BY cot_id DESC
                LIMIT :primeiro_registro, :num_por_pagina ";
            $stmt = $PDO->prepare($sql);
            $stmt->bindParam(':fil_nome1',     $fil_nome1);
            $stmt->bindParam(':primeiro_registro',     $primeiro_registro);
            $stmt->bindParam(':num_por_pagina',     $num_por_pagina);
            $stmt->execute();
            $rows = $stmt->rowCount();
            if ($pagina == "view") {
                echo "
                    <div class='titulo'> $page  </div>
                    <div id='botoes'>
                    <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(" . $permissoes["add"] . ",\"" . $pagina_link . "/add\");'><i class='fas fa-plus'></i></div>
                    <div class='filtrar'><span class='f'><i class='fas fa-filter'></i></span></div>
                    <div class='filtro'>
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_cotacoes/view'>
                        <input name='fil_nome' id='fil_nome' value='$fil_nome' placeholder='Título'>                    
                        <input type='submit' value='Filtrar'> 
                        </form>            
                    </div>
                </div>
                ";
                if ($rows > 0) {
                    echo "
                <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                    <tr>
                        <td class='titulo_tabela' align='left' colspan='1' width='1'>Cotação</td>
                        <td class='titulo_tabela' align='center'>Data</td>
                        <td class='titulo_last' align='right' width='100'>Gerenciar</td>
                    </tr>";
                    $c = 0;
                    while ($result = $stmt->fetch()) {
                        $cot_id         = $result['cot_id'];
                        $cot_nome    = $result['cot_titulo'];
                        $cot_data    = date("d/m/Y", strtotime($result['cot_data']));
                        $cot_documento    = $result['cot_documento'];

                        if ($c == 0) {
                            $c1 = "linhaimpar";
                            $c = 1;
                        } else {
                            $c1 = "linhapar";
                            $c = 0;
                        }
                        echo "<tr class='$c1'>
                                <td>$cot_nome</td>
                                <td align='center'>$cot_data</td>
                                <td align=center>
                                    <div class='g_excluir' title='Excluir' onclick=\"
                                        abreMask(
                                            'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                            '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/view/excluir/$cot_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                            '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                        \">	<i class='far fa-trash-alt'></i>
                                    </div>
                                    <div class='g_editar' title='Editar' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"" . $pagina_link . "/edit/$cot_id\");'><i class='fas fa-pencil-alt'></i></div>
                                </td>
                            </tr>";
                    }
                    echo "</table>";
                    $variavel = "&fil_nome=$fil_nome";
                    $cnt = "SELECT COUNT(*) FROM cadastro_cotacoes WHERE " . $nome_query . "  ";
                    $stmt = $PDO->prepare($cnt);
                    $stmt->bindParam(':fil_nome1',     $fil_nome1);
                    include("../core/mod_includes/php/paginacao.php");
                } else {
                    echo "<br><br><br>Não há nenhum item cadastrado.";
                }
            }
            if ($pagina == 'add') {
                echo "	
            <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_cotacoes/edit/adicionar'>
                <div class='titulo'> $page &raquo; Adicionar  </div>
                <ul class='nav nav-tabs'>
                      <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                </ul>
                <div class='tab-content'>
                    <div id='dados_gerais' class='tab-pane fade in active'>
                        <p><label>Título*:</label> <input name='cot_titulo' id='cot_titulo' placeholder='Título' class='obg' >
						<p><label>Descrição*:</label> <div class='textarea'><textarea  name='cot_descricao' id='cot_descricao' placeholder='Descrição'></textarea></div>
                        <p><label>Documento:</label> <input type='file' name='cot_documento[documento]' id='cot_documento' class='obg'>
                        <p><label>Data:</label> <input name='cot_data' id='cot_data' placeholder='Data' class='obg'>
                        <p><label>Status:</label> <input type='radio' name='cot_status' value='1' > Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type='radio' name='cot_status' value='0' checked> Inativo<br>		
                        				
                    </div>                    
				</div>
                <br>
                <center>
                <div id='erro' align='center'>&nbsp;</div>
                <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_cotacoes/view'; value='Cancelar'/></center>
                </center>
            </form>
            ";
            }
            if ($pagina == 'edit') {
                $sql = "SELECT * FROM cadastro_cotacoes 
					WHERE cot_id = :cot_id";
                $stmt = $PDO->prepare($sql);
                $stmt->bindParam(':cot_id', $cot_id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if ($rows > 0) {
                    $result = $stmt->fetch();
                    echo "
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_cotacoes/view/editar/$cot_id'>
                    <div class='titulo'> $page &raquo; Editar </div>
                    <ul class='nav nav-tabs'>
                        <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                    </ul>
                    <div class='tab-content'>
                        <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Título*:</label> <input name='cot_titulo' id='cot_titulo' value='" . $result['cot_titulo'] . "' placeholder='Título' class='obg' >
                                <p><label>Descrição*:</label> <div class='textarea'><textarea name='cot_descricao' id='cot_descricao' placeholder='Descrição'>" . $result['cot_descricao'] . "</textarea></div>
                                <p><label>Data:</label> <input name='cot_data' id='cot_data' value='" .date( "d/m/Y", strtotime($result['cot_data'])). "' placeholder='Data' class='obg'>
                                <p><label>Documento:</label> ";
                            if ($result['cot_documento'] != '') {
                                echo "<a href='" . $result['cot_documento'] . "' target='_blank'>".$result['cot_documento']." </a> ";
                            }
                            echo " &nbsp; 
                                <p><label>Alterar Documento:</label> <input type='file' name='cot_documento[documento]' id='cot_documento'>
                                <p><label>Status:</label>";
                            if ($result['cot_status'] == 1) {
                                echo "<input type='radio' name='cot_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type='radio' name='cot_status' value='0'> Inativo
                                        ";
                            } else {
                                echo "<input type='radio' name='cot_status' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type='radio' name='cot_status' value='0' checked> Inativo
                                        ";
                            }
                            echo "
                        </div>
						<br>
						<center>
						<div id='erro' align='center'>&nbsp;</div>
						<input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
						<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_cotacoes/view'; value='Cancelar'/></center>
						</center>
                    </div>
                </form>
                ";
                }
            }

            ?>
        </div> <!-- .content-wrapper -->
    </main> <!-- .cd-main-content -->
</body>

</html>