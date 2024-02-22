<?php
$pagina_link = 'cadastro_departamentos';
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start(); 
$page = "<a href='cadastro_departamentos/view'>Departamentos</a>"; 

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
            $page = "<a href='cadastro_departamentos/view'>Departamentos</a>";
            if (isset($_GET['dep_id'])) {
                $dep_id = $_GET['dep_id'];
            }
            if ($dep_id == '') {
                $dep_id = $_POST['dep_id'];
            }
            $dep_titulo          = $_POST['dep_titulo'];
            $dep_descricao       =  $_POST['dep_descricao'];
            $dep_url             = geradorTags($dep_titulo);
            $dep_status          = $_POST['dep_status'];
            $dep_usuario         =  $_SESSION['usuario_id']; 
            $dep_responsavel     = $_POST['dep_responsavel'];
            $dep_ordem           = $_POST['dep_ordem'];

            $dados = array(
                'dep_titulo'         => $dep_titulo,
                'dep_descricao'      => $dep_descricao,
                'dep_url'            => $dep_url,
                'dep_status'         => $dep_status,
                'dep_usuario'        => $dep_usuario,
                'dep_responsavel'    => $dep_responsavel,
                'dep_ordem'          => $dep_ordem,
            );
            
            if ($action == "adicionar") {

                $sql = "INSERT INTO cadastro_departamentos SET " . bindFields($dados);
                $stmt = $PDO->prepare($sql);
                if ($stmt->execute($dados)) {
                    $dep_id = $PDO->lastInsertId();
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
                                $dep_imagem    = $caminho;
                                $dep_imagem .= "imagem_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                move_uploaded_file($nomeTemporario, ($dep_imagem));
                                $imnfo = getimagesize($dep_imagem);
                                $img_w = $imnfo[0];      // largura
                                $img_h = $imnfo[1];      // altura
                                if ($img_w > 900 || $img_h > 900) {
                                    $image = WideImage::load($dep_imagem);
                                    $image = $image->resize(900, 900);
                                    $image->saveToFile($dep_imagem);
                                }
                                $sql = "UPDATE cadastro_departamentos SET 
                                    dep_imagem 	 = :dep_imagem
                                    WHERE dep_id = :dep_id ";
                                $stmt = $PDO->prepare($sql);
                                $stmt->bindParam(':dep_imagem', $dep_imagem);
                                $stmt->bindParam(':dep_id', $dep_id);

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

                $sql = "UPDATE cadastro_departamentos SET " . bindFields($dados) . " WHERE dep_id = :dep_id ";
                $stmt = $PDO->prepare($sql);
                $dados['dep_id'] =  $dep_id;
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
                                $sql = "SELECT * FROM cadastro_departamentos WHERE dep_id = :dep_id";
                                $stmt_antigo = $PDO->prepare($sql);
                                $stmt_antigo->bindParam(':dep_id', $dep_id);
                                $stmt_antigo->execute();
                                $result_antigo = $stmt_antigo->fetch();
                                $imagem_antigo = $result_antigo['dep_imagem'];
                                unlink($imagem_antigo);

                                $nomeArquivo     = $files["name"]["imagem"];
                                $nomeTemporario = $files["tmp_name"]["imagem"];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $dep_imagem    = $caminho;
                                $dep_imagem .= "imagem_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                move_uploaded_file($nomeTemporario, ($dep_imagem));
                                $imnfo = getimagesize($dep_imagem);
                                $img_w = $imnfo[0];      // largura
                                $img_h = $imnfo[1];      // altura
                                if ($img_w > 900 || $img_h > 900) {
                                    $image = WideImage::load($dep_imagem);
                                    $image = $image->resize(900, 900);
                                    $image->saveToFile($dep_imagem);
                                }
                                $sql = "UPDATE cadastro_departamentos SET 
									dep_imagem 	 = :dep_imagem
									WHERE dep_id = :dep_id ";
                                $stmt = $PDO->prepare($sql);
                                $stmt->bindParam(':dep_imagem', $dep_imagem);
                                $stmt->bindParam(':dep_id', $dep_id);
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
                $sql = "DELETE FROM cadastro_departamentos WHERE dep_id = :dep_id";
                $stmt = $PDO->prepare($sql);
                $stmt->bindParam(':dep_id', $dep_id);
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
                $sql = "UPDATE cadastro_departamentos SET dep_status = :dep_status WHERE dep_id = :dep_id ";
                $stmt = $PDO->prepare($sql);
                $stmt->bindValue(':dep_status', 1);
                $stmt->bindParam(':dep_id', $dep_id);
                $stmt->execute();
            }

            if ($action == 'desativar') {
                $sql = "UPDATE cadastro_departamentos SET dep_status = :dep_status WHERE dep_id = :dep_id ";
                $stmt = $PDO->prepare($sql);
                $stmt->bindValue(':dep_status', 0);
                $stmt->bindParam(':dep_id', $dep_id);
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
                $nome_query = " (dep_titulo LIKE :fil_nome1  ) ";
            }

            $sql = "SELECT * FROM cadastro_departamentos 
                WHERE " . $nome_query . "
                ORDER BY dep_ordem ASC
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
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_departamentos/view'>
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
                        <td class='titulo_tabela' align='left' colspan='2'>Responsável</td>
                        <td class='titulo_tabela' align='center'>Departamento</td>
                        <td class='titulo_tabela' align='center'>Ordem</td>
                        <td class='titulo_last' align='right' width='100'>Gerenciar</td>
                    </tr>";
                    $c = 0;
                    while ($result = $stmt->fetch()) {
                        $dep_id             = $result['dep_id'];
                        $dep_titulo         = $result['dep_titulo'];
                        $dep_imagem         = $result['dep_imagem'];
                        $dep_ordem          = $result['dep_ordem'];
                        $dep_responsavel    = $result['dep_responsavel'];

                        if ($c == 0) {
                            $c1 = "linhaimpar";
                            $c = 1;
                        } else {
                            $c1 = "linhapar";
                            $c = 0;
                        }
                        echo "<tr class='$c1'>
                                <td><img src='$dep_imagem' style='object-fit:cover; width:120px; height:80px'></td>
                                <td>$dep_responsavel</td>
                                <td align='center'>$dep_titulo</td>
                                <td align='center'>$dep_ordem</td>
                                <td align=center>
										<div class='g_excluir' title='Excluir' onclick=\"
											abreMask(
												'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
												'<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/view/excluir/$dep_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
												'<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
											\">	<i class='far fa-trash-alt'></i>
										</div>
										<div class='g_editar' title='Editar' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"" . $pagina_link . "/edit/$dep_id\");'><i class='fas fa-pencil-alt'></i></div>
								  </td>
                              </tr>";
                    }
                    echo "</table>";
                    $variavel = "&fil_nome=$fil_nome";
                    $cnt = "SELECT COUNT(*) FROM cadastro_departamentos WHERE " . $nome_query . "  ";
                    $stmt = $PDO->prepare($cnt);
                    $stmt->bindParam(':fil_nome1',     $fil_nome1);
                    include("../core/mod_includes/php/paginacao.php");
                } else {
                    echo "<br><br><br>Não há nenhum item cadastrado.";
                }
            }

            if ($pagina == 'add') {
                echo "	
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_departamentos/view/adicionar'>
                        <div class='titulo'> $page &raquo; Adicionar  </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Título*:</label> <input name='dep_titulo' id='dep_titulo' placeholder='Título' class='obg' >
                                <p><label>Responsável*:</label> <input name='dep_responsavel' id='dep_responsavel' placeholder='Responsável' class='obg' >
                                <p><label>Descrição*:</label> <div class='textarea'><textarea  name='dep_descricao' id='dep_descricao' placeholder='Descrição'></textarea></div>
                                <p><label>Ordem :</label> <input name='dep_ordem' id='dep_ordem' placeholder='Ordem'>
                                <p><label>Imagem:</label> <input type='file' name='dep_imagem[imagem]' id='dep_imagem'>
                                <p><label>Status:</label> <input type='radio' name='dep_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type='radio' name='dep_status' value='0'> Inativo<br>		
                                                
                            </div>                    
                        </div>
                        <br>
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_departamentos/view'; value='Cancelar'/></center>
                        </center>
                    </form>
                ";
            }

            if ($pagina == 'edit') {
                $sql = "SELECT * FROM cadastro_departamentos 
					WHERE dep_id = :dep_id";
                $stmt = $PDO->prepare($sql);
                $stmt->bindParam(':dep_id', $dep_id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if ($rows > 0) {
                    $result = $stmt->fetch();
                    echo "
                        <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_departamentos/view/editar/$dep_id'>
                            <div class='titulo'> $page &raquo; Editar </div>
                            <ul class='nav nav-tabs'>
                                <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                            </ul>
                            <div class='tab-content'>
                                <div id='dados_gerais' class='tab-pane fade in active'>
                                        <p><label>Título*:</label> <input name='dep_titulo' id='dep_titulo' value='" . $result['dep_titulo'] . "' placeholder='Título' class='obg' >
                                        <p><label>Responsável*:</label> <input name='dep_responsavel' id='dep_responsavel' value='" . $result['dep_responsavel'] . "' placeholder='Responsável' class='obg' >
                                        <p><label>Descrição*:</label> <div class='textarea'><textarea name='dep_descricao' id='dep_descricao' placeholder='Descrição'>" . $result['dep_descricao'] . "</textarea></div>
                                        <p><label>Ordem:</label> <input name='dep_ordem' id='dep_ordem' value='" . $result['dep_ordem'] . "' placeholder='Ordem'>

                                        <p><label>Imagem:</label> ";
                                        if ($result['dep_imagem'] != '') {
                                            echo "<img src='" . $result['dep_imagem'] . "' style='max-width:400px;'> ";
                                        }
                                        echo " &nbsp; 
                                        <p><label>Alterar Imagem:</label> <input type='file' name='dep_imagem[imagem]' id='dep_imagem'>
                                        <p><label>Status:</label>";
                                    if ($result['dep_status'] == 1) {
                                        echo "<input type='radio' name='dep_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type='radio' name='dep_status' value='0'> Inativo
                                                ";
                                    } else {
                                        echo "<input type='radio' name='dep_status' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type='radio' name='dep_status' value='0' checked> Inativo
                                                ";
                                    }
                                    echo "
                                </div>
                                <br>
                                <center>
                                <div id='erro' align='center'>&nbsp;</div>
                                <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_departamentos/view'; value='Cancelar'/></center>
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