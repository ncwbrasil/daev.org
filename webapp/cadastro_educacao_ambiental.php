<?php
$pagina_link = 'cadastro_educacao_ambiental';
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start(); 
$page = "<a href='cadastro_educacao_ambiental/view'>Educação Ambiental</a>"; 

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
            $page = " <a href='cadastro_educacao_ambiental/view'>Educação Ambiental</a>";
            if (isset($_GET['cea_id'])) {
                $cea_id = $_GET['cea_id'];
            }
            if ($cea_id == '') {
                $cea_id = $_POST['cea_id'];
            }
            $cea_titulo          = $_POST['cea_titulo'];
            $cea_descricao       =  $_POST['cea_descricao'];
            $cea_url             = geradorTags($cea_titulo);
            $cea_status          = $_POST['cea_status'];
            $cea_usuario         =  $_SESSION['usuario_id']; 
            $dados = array(
                'cea_titulo'         => $cea_titulo,
                'cea_descricao'      => $cea_descricao,
                'cea_url'            => $cea_url,
                'cea_status'         => $cea_status,
                'cea_usuario'        => $cea_usuario,
            );
            
            if ($action == "adicionar") {

                $sql = "INSERT INTO cadastro_educacao_ambiental SET " . bindFields($dados);
                $stmt = $PDO->prepare($sql);
                if ($stmt->execute($dados)) {
                    $cea_id = $PDO->lastInsertId();
                    //UPLOAD ARQUIVOS
                    require_once '../core/mod_includes/php/lib/WideImage.php';
                    $caminho = "uploads/noticias/";
                    foreach ($_FILES as $key => $files) {
                        $files_test = array_filter($files['name']);
                        if (!empty($files_test)) {
                            if (!file_exists($caminho)) {
                                mkdir($caminho, 0755, true);
                            }
                            if (!empty($files["name"]["imagem"])) {
                                $nomeArquivo     = $files["name"]["imagem"];
                                $nomeTemporario = $files["tmp_name"]["imagem"];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $cea_imagem    = $caminho;
                                $cea_imagem .= "imagem_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                move_uploaded_file($nomeTemporario, ($cea_imagem));
                                $imnfo = getimagesize($cea_imagem);
                                $img_w = $imnfo[0];      // largura
                                $img_h = $imnfo[1];      // altura
                                if ($img_w > 900 || $img_h > 900) {
                                    $image = WideImage::load($cea_imagem);
                                    $image = $image->resize(900, 900);
                                    $image->saveToFile($cea_imagem);
                                }
                                $sql = "UPDATE cadastro_educacao_ambiental SET 
                                    cea_imagem 	 = :cea_imagem
                                    WHERE cea_id = :cea_id ";
                                $stmt = $PDO->prepare($sql);
                                $stmt->bindParam(':cea_imagem', $cea_imagem);
                                $stmt->bindParam(':cea_id', $cea_id);

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

                $sql = "UPDATE cadastro_educacao_ambiental SET " . bindFields($dados) . " WHERE cea_id = :cea_id ";
                $stmt = $PDO->prepare($sql);
                $dados['cea_id'] =  $cea_id;
                if ($stmt->execute($dados)) {
                    //UPLOAD ARQUIVOS
                    require_once '../core/mod_includes/php/lib/WideImage.php';
                    $caminho = "uploads/noticias/";
                    foreach ($_FILES as $key => $files) {
                        $files_test = array_filter($files['name']);
                        if (!empty($files_test)) {
                            if (!file_exists($caminho)) {
                                mkdir($caminho, 0755, true);
                            }
                            if (!empty($files["name"]["imagem"])) {
                                # EXCLUI ANEXO ANTIGO #
                                $sql = "SELECT * FROM cadastro_educacao_ambiental WHERE cea_id = :cea_id";
                                $stmt_antigo = $PDO->prepare($sql);
                                $stmt_antigo->bindParam(':cea_id', $cea_id);
                                $stmt_antigo->execute();
                                $result_antigo = $stmt_antigo->fetch();
                                $imagem_antigo = $result_antigo['cea_imagem'];
                                unlink($imagem_antigo);

                                $nomeArquivo     = $files["name"]["imagem"];
                                $nomeTemporario = $files["tmp_name"]["imagem"];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $cea_imagem    = $caminho;
                                $cea_imagem .= "imagem_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                move_uploaded_file($nomeTemporario, ($cea_imagem));
                                $imnfo = getimagesize($cea_imagem);
                                $img_w = $imnfo[0];      // largura
                                $img_h = $imnfo[1];      // altura
                                if ($img_w > 900 || $img_h > 900) {
                                    $image = WideImage::load($cea_imagem);
                                    $image = $image->resize(900, 900);
                                    $image->saveToFile($cea_imagem);
                                }
                                $sql = "UPDATE cadastro_educacao_ambiental SET 
									cea_imagem 	 = :cea_imagem
									WHERE cea_id = :cea_id ";
                                $stmt = $PDO->prepare($sql);
                                $stmt->bindParam(':cea_imagem', $cea_imagem);
                                $stmt->bindParam(':cea_id', $cea_id);
                                if ($stmt->execute()) {
                                } else {
                                    $erro = 1;
                                    $err = $stmt->errorInfo();
                                }
                            }
                        }
                    }
                    //
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
                $sql = "DELETE FROM cadastro_educacao_ambiental WHERE cea_id = :cea_id";
                $stmt = $PDO->prepare($sql);
                $stmt->bindParam(':cea_id', $cea_id);
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
                $sql = "UPDATE cadastro_educacao_ambiental SET cea_status = :cea_status WHERE cea_id = :cea_id ";
                $stmt = $PDO->prepare($sql);
                $stmt->bindValue(':cea_status', 1);
                $stmt->bindParam(':cea_id', $cea_id);
                $stmt->execute();
            }

            if ($action == 'desativar') {
                $sql = "UPDATE cadastro_educacao_ambiental SET cea_status = :cea_status WHERE cea_id = :cea_id ";
                $stmt = $PDO->prepare($sql);
                $stmt->bindValue(':cea_status', 0);
                $stmt->bindParam(':cea_id', $cea_id);
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
                $nome_query = " (cea_titulo LIKE :fil_nome1  ) ";
            }

            $sql = "SELECT * FROM cadastro_educacao_ambiental 
                WHERE " . $nome_query . "
                ORDER BY cea_id DESC
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
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_educacao_ambiental/view'>
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
                        <td class='titulo_tabela' align='left' colspan='2' width='1'>Título</td>
                        <td class='titulo_tabela' align='center'>Data</td>
                        <td class='titulo_last' align='right' width='100'>Gerenciar</td>
                    </tr>";
                    $c = 0;
                    while ($result = $stmt->fetch()) {
                        $cea_id         = $result['cea_id'];
                        $cea_titulo     = $result['cea_titulo'];
                        $cea_data       = date("d/m/Y", strtotime($result['cea_data']));
                        $cea_imagem     = $result['cea_imagem'];

                        if ($c == 0) {
                            $c1 = "linhaimpar";
                            $c = 1;
                        } else {
                            $c1 = "linhapar";
                            $c = 0;
                        }
                        echo "<tr class='$c1'>
                                <td width='1'><img src='$cea_imagem' style='object-fit:cover; width:120px; height:80px'></td>
                                <td>$cea_titulo</td>
                                  <td align='center'>$cea_data</td>
                                  <td align=center>
										<div class='g_excluir' title='Excluir' onclick=\"
											abreMask(
												'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
												'<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/view/excluir/$cea_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
												'<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
											\">	<i class='far fa-trash-alt'></i>
										</div>
										<div class='g_editar' title='Editar' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"" . $pagina_link . "/edit/$cea_id\");'><i class='fas fa-pencil-alt'></i></div>
								  </td>
                              </tr>";
                    }
                    echo "</table>";
                    $variavel = "&fil_nome=$fil_nome";
                    $cnt = "SELECT COUNT(*) FROM cadastro_educacao_ambiental WHERE " . $nome_query . "  ";
                    $stmt = $PDO->prepare($cnt);
                    $stmt->bindParam(':fil_nome1',     $fil_nome1);
                    include("../core/mod_includes/php/paginacao.php");
                } else {
                    echo "<br><br><br>Não há nenhum item cadastrado.";
                }
            }

            if ($pagina == 'add') {
                echo "	
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_educacao_ambiental/view/adicionar'>
                        <div class='titulo'> $page &raquo; Adicionar  </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Título*:</label> <input name='cea_titulo' id='cea_titulo' placeholder='Título' class='obg' >
                                <p><label>Descrição*:</label> <div class='textarea'><textarea  name='cea_descricao' id='cea_descricao' placeholder='Descrição'></textarea></div>
                                <p><label>Imagem:</label> <input type='file' name='cea_imagem[imagem]' id='cea_imagem'>
                                <p><label>Status:</label> <input type='radio' name='cea_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type='radio' name='cea_status' value='0'> Inativo<br>		
                                                
                            </div>                    
                        </div>
                        <br>
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_educacao_ambiental/view'; value='Cancelar'/></center>
                        </center>
                    </form>
                ";
            }

            if ($pagina == 'edit') {
                $sql = "SELECT * FROM cadastro_educacao_ambiental 
					WHERE cea_id = :cea_id";
                $stmt = $PDO->prepare($sql);
                $stmt->bindParam(':cea_id', $cea_id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if ($rows > 0) {
                    $result = $stmt->fetch();
                    echo "
                        <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_educacao_ambiental/view/editar/$cea_id'>
                            <div class='titulo'> $page &raquo; Editar </div>
                            <ul class='nav nav-tabs'>
                                <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                            </ul>
                            <div class='tab-content'>
                                <div id='dados_gerais' class='tab-pane fade in active'>
                                        <p><label>Título*:</label> <input name='cea_titulo' id='cea_titulo' value='" . $result['cea_titulo'] . "' placeholder='Título' class='obg' >
                                        <p><label>Descrição*:</label> <div class='textarea'><textarea name='cea_descricao' id='cea_descricao' placeholder='Descrição'>" . $result['cea_descricao'] . "</textarea></div>
                                        <p><label>Imagem:</label> ";
                                        if ($result['cea_imagem'] != '') {
                                            echo "<img src='" . $result['cea_imagem'] . "' style='max-width:400px;'> ";
                                        }
                                        echo " &nbsp; 
                                        <p><label>Alterar Imagem:</label> <input type='file' name='cea_imagem[imagem]' id='cea_imagem'>
                                        <p><label>Status:</label>";
                                    if ($result['cea_status'] == 1) {
                                        echo "<input type='radio' name='cea_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type='radio' name='cea_status' value='0'> Inativo
                                                ";
                                    } else {
                                        echo "<input type='radio' name='cea_status' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type='radio' name='cea_status' value='0' checked> Inativo
                                                ";
                                    }
                                    echo "
                                </div>
                                <br>
                                <center>
                                <div id='erro' align='center'>&nbsp;</div>
                                <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_educacao_ambiental/view'; value='Cancelar'/></center>
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