<?php
$pagina_link = 'cadastro_galeria';
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start(); 
$page = "<a href='cadastro_galeria/view'>Galeria</a>"; 

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
            $page = "Cadastros &raquo; <a href='cadastro_galeria/view'>Galeria</a>";
            if (isset($_GET['gal_id'])) {
                $gal_id = $_GET['gal_id'];
            }
            if ($gal_id == '') {
                $gal_id = $_POST['gal_id'];
            }
            $gal_titulo             = $_POST['gal_titulo'];
            $gal_descricao          =  $_POST['gal_descricao'];
            $gal_url                = geradorTags($_POST['gal_titulo']);
            $gal_data               = implode("-", array_reverse(explode("/", $_POST['gal_data'])));
            $gal_status             = $_POST['gal_status'];
            $gal_categoria          = $_POST['gal_categoria'];
            $gal_meta_titulo        = $_POST['gal_meta_titulo']; 
            $gal_meta_description   = $_POST['gal_meta_description']; 

            $dados = array(
                'gal_titulo'            => $gal_titulo,
                'gal_descricao'         => $gal_descricao,
                'gal_url'               => $gal_url,
                'gal_data'              => $gal_data,
                'gal_usuario'           => $_SESSION['usuario_id'],
                'gal_status'            => $gal_status, 
                'gal_categoria'         => $gal_categoria, 
                'gal_meta_titulo'       => $gal_meta_titulo, 
                'gal_meta_description'  => $gal_meta_description, 
            );

            if ($action == "adicionar") {
                $sql = "INSERT INTO cadastro_galeria SET " . bindFields($dados);
                $stmt = $PDO->prepare($sql);
                if ($stmt->execute($dados)) {
                    $gal_id = $PDO->lastInsertId();
                    //UPLOAD ARQUIVOS
                    require_once '../core/mod_includes/php/lib/WideImage.php';

                    $caminho = "uploads/galeria/";
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
                                $gal_imagem    = $caminho;
                                $gal_imagem .= "imagem_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                move_uploaded_file($nomeTemporario, ($gal_imagem));                                
                                $imnfo = getimagesize($gal_imagem);
                                $img_w = $imnfo[0];      // largura
                                $img_h = $imnfo[1];      // altura
                                if ($img_w > 1200 || $img_h > 2000) {
                                    $image = WideImage::load($gal_imagem);
                                    $image = $image->resize(1200, 1200);
                                    $image->saveToFile($gal_imagem);
                                }

                                $sql = "UPDATE cadastro_galeria SET 
                                    gal_imagem 	    = :gal_imagem
                                    WHERE gal_id = :gal_id ";
                                $stmt = $PDO->prepare($sql);
                                $stmt->bindParam(':gal_imagem', $gal_imagem);
                                $stmt->bindParam(':gal_id', $gal_id);

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

            if ($action == 'editar') {

                $sql = "UPDATE cadastro_galeria SET " . bindFields($dados) . " WHERE gal_id = :gal_id ";
                $stmt = $PDO->prepare($sql);
                $dados['gal_id'] =  $gal_id;
                if ($stmt->execute($dados)) {
                    //UPLOAD ARQUIVOS
                    require_once '../core/mod_includes/php/lib/WideImage.php';
                    $caminho = "uploads/galeria/";
                    foreach ($_FILES as $key => $files) {
                        $files_test = array_filter($files['name']);
                        if (!empty($files_test)) {
                            if (!file_exists($caminho)) {
                                mkdir($caminho, 0755, true);
                            }
                            if (!empty($files["name"]["imagem"])) {
                                # EXCLUI ANEXO ANTIGO #
                                $sql = "SELECT * FROM cadastro_galeria WHERE gal_id = :gal_id";
                                $stmt_antigo = $PDO->prepare($sql);
                                $stmt_antigo->bindParam(':gal_id', $gal_id);
                                $stmt_antigo->execute();
                                $result_antigo = $stmt_antigo->fetch();
                                $imagem_antigo = $result_antigo['gal_imagem'];
                                unlink($imagem_antigo);
                                $nomeArquivo     = $files["name"]["imagem"];
                                $nomeTemporario = $files["tmp_name"]["imagem"];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $gal_imagem    = $caminho;
                                $gal_imagem .= "imagem_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                move_uploaded_file($nomeTemporario, ($gal_imagem));
                                $imnfo = getimagesize($gal_imagem);
                                $img_w = $imnfo[0];      // largura
                                $img_h = $imnfo[1];      // altura
                                if ($img_w > 1200 || $img_h > 2000) {
                                    $image = WideImage::load($gal_imagem);
                                    $image = $image->resize(1200, 1200);
                                    $image->saveToFile($gal_imagem);
                                }
                                $sql = "UPDATE cadastro_galeria SET 
									gal_imagem 	 = :gal_imagem
									WHERE gal_id = :gal_id ";
                                $stmt = $PDO->prepare($sql);
                                $stmt->bindParam(':gal_imagem', $gal_imagem);
                                $stmt->bindParam(':gal_id', $gal_id);
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
                $sql = "DELETE FROM cadastro_galeria WHERE gal_id = :gal_id";
                $stmt = $PDO->prepare($sql);
                $stmt->bindParam(':gal_id', $gal_id);
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
                $sql = "UPDATE cadastro_galeria SET gal_status = :gal_status WHERE gal_id = :gal_id ";
                $stmt = $PDO->prepare($sql);
                $stmt->bindValue(':gal_status', 1);
                $stmt->bindParam(':gal_id', $gal_id);
                $stmt->execute();
            }

            if ($action == 'desativar') {
                $sql = "UPDATE cadastro_galeria SET gal_status = :gal_status WHERE gal_id = :gal_id ";
                $stmt = $PDO->prepare($sql);
                $stmt->bindValue(':gal_status', 0);
                $stmt->bindParam(':gal_id', $gal_id);
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
                $nome_query = " (gal_nome LIKE :fil_nome1  ) ";
            }

            $sql = "SELECT * FROM cadastro_galeria 
                WHERE " . $nome_query . "
                ORDER BY gal_id DESC
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
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_galeria/view'>
                        <input name='fil_nome' id='fil_nome' value='$fil_nome' placeholder='Título'>                    
                        <input type='submit' value='Filtrar'> 
                        </form>            
                    </div>
                </div>";

                if ($rows > 0) {
                    echo "
                <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                    <tr>
                        <td class='titulo_tabela' >Imagem Principal</td>
                        <td class='titulo_tabela' align='center'>Titulo da Galeria</td>
                        <td class='titulo_tabela' align='center'>Data</td>
                        <td class='titulo_last' align='right' width='200'>Gerenciar</td>
                    </tr>";
                    $c = 0;
                    while ($result = $stmt->fetch()) {
                        $gal_id         = $result['gal_id'];
                        $gal_titulo     = $result['gal_titulo'];
                        $gal_data       = implode("/", array_reverse(explode("-", $result['gal_data'])));
                        $gal_imagem     = $result['gal_imagem'];

                        if ($c == 0) {
                            $c1 = "linhaimpar";
                            $c = 1;
                        } else {
                            $c1 = "linhapar";
                            $c = 0;
                        }
                        echo "<tr class='$c1'>
                                <td><div style='height:85px; width:85px; background:url($gal_imagem) center center no-repeat; background-size:cover'></div></td>
                                <td align='center'>$gal_titulo</td>
                                  <td align='center'>$gal_data</td>
                                  <td align=center>
										<div class='g_excluir' title='Excluir' onclick=\"
											abreMask(
												'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
												'<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/view/excluir/$gal_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
												'<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
											\">	<i class='far fa-trash-alt'></i>
										</div>
                                        <div class='g_editar' title='Editar' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"" . $pagina_link . "/edit/$gal_id\");'><i class='fas fa-pencil-alt'></i></div>
                                        <div class='g_adicionar' title='Adicionar Fotos' onclick='verificaPermissao(" . $permissoes["fotos"] . ",\"" . $pagina_link . "/fotos/$gal_id\");'><i class='fas fa-camera'></i></div>
								  </td>
                              </tr>";
                    }
                    echo "</table>";
                    $variavel = "&fil_nome=$fil_nome";
                    $cnt = "SELECT COUNT(*) FROM cadastro_galeria WHERE " . $nome_query . "  ";
                    $stmt = $PDO->prepare($cnt);
                    $stmt->bindParam(':fil_nome1',     $fil_nome1);
                    include("../core/mod_includes/php/paginacao.php");
                } else {
                    echo "<br><br><br>Não há nenhum item cadastrado.";
                }
            }
            if ($pagina == 'add') {
                echo "	
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_galeria/view/adicionar'>
                    <div class='titulo'> $page &raquo; Adicionar  </div>
                    <ul class='nav nav-tabs'>
                        <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                    </ul>
                    <div class='tab-content'>
                        <div id='dados_gerais' class='tab-pane fade in active'>
                            <p><label>Título*:</label> <input name='gal_titulo' id='gal_titulo' placeholder='Título' class='obg' >
                            <p><label>Descrição*:</label> <div class='textarea'><textarea  name='gal_descricao' id='gal_descricao' placeholder='Descrição'></textarea></div>

                            <p><label>Meta Tag Descrição:</label> <input name='gal_meta_titulo' id='gal_meta_titulo' placeholder='Meta Tag Descrição'>
                            <p><label>Meta Tag Título:</label> <input name='gal_meta_description' id='gal_meta_description' placeholder='Meta Tag Título'>

                            <p><label>Imagem de Destaque:</label> <input type='file' name='gal_imagem[imagem]' id='gal_imagem' class='obg'>
                            <p><label>Data:</label> <input name='gal_data' id='gal_data' placeholder='Data' class='obg'>
                            <p><label>Status:</label> <input type='radio' name='gal_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type='radio' name='gal_status' value='0'> Inativo<br>	
                        </div>
                    </div>
                    <br>
                    <center>
                    <div id='erro' align='center'>&nbsp;</div>
                    <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                    <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_galeria/view'; value='Cancelar'/></center>
                    </center>
                </form>
                ";
            }
            if ($pagina == 'edit') {
                $sql = "SELECT * FROM cadastro_galeria 
				WHERE gal_id = :gal_id";
                $stmt = $PDO->prepare($sql);
                $stmt->bindParam(':gal_id', $gal_id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if ($rows > 0) {
                    $result = $stmt->fetch();
                    echo "
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_galeria/view/editar/$gal_id'>
                    <div class='titulo'> $page &raquo; Editar </div>
                    <ul class='nav nav-tabs'>
                    	<li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                    </ul>
                    <div class='tab-content'>
                        <div id='dados_gerais' class='tab-pane fade in active'>
                        <p><label>Título*:</label> <input name='gal_titulo' id='gal_titulo' value='" . $result['gal_titulo'] . "' placeholder='Título' class='obg' >
						<p><label>Descrição*:</label> <div class='textarea'><textarea name='gal_descricao' id='gal_descricao' placeholder='Descrição'>" . $result['gal_descricao'] . "</textarea></div>

                        <p><label>Meta Tag Descrição:</label> <input name='gal_meta_titulo' id='gal_meta_titulo' placeholder='Meta Tag Descrição' value='" . $result['gal_meta_titulo'] . "'>
                        <p><label>Meta Tag Título:</label> <input name='gal_meta_description' id='gal_meta_description' placeholder='Meta Tag Título' value='" . $result['gal_meta_description'] . "'>

                        <p><label>Data:</label> <input name='gal_data' id='gal_data' value='" . implode("/", array_reverse(explode("-", $result['gal_data']))) . "' placeholder='Data' class='obg'>
                        <p><label>Imagem de Destaque:</label> ";
                    if ($result['gal_imagem'] != '') {
                        echo "<img src='" . $result['gal_imagem'] . "' style='max-width:400px;'> ";
                    }
                    echo " &nbsp; 
                        <p><label>Alterar Imagem:</label> <input type='file' name='gal_imagem[imagem]' id='gal_imagem'>
                        <p><label>Status:</label>";
                    if ($result['gal_status'] == 1) {
                        echo "<input type='radio' name='gal_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                  <input type='radio' name='gal_status' value='0'> Inativo
                                 ";
                    } else {
                        echo "<input type='radio' name='gal_status' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                  <input type='radio' name='gal_status' value='0' checked> Inativo
                                 ";
                    }
                    echo "
                        </div>
						<br>
						<center>
						<div id='erro' align='center'>&nbsp;</div>
						<input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
						<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_galeria/view'; value='Cancelar'/></center>
						</center>
                    </div>
                </form>
                ";
                }
            }
            if($pagina == 'fotos')
            {           
                $sql = "SELECT * FROM cadastro_galeria 
                WHERE gal_id = :gal_id";
                $stmt = $PDO->prepare($sql);
                $stmt->bindParam(':gal_id', $gal_id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if ($rows > 0) {
                    $result = $stmt->fetch();
                                      
                    echo "
                    <div class='titulo'> $page &raquo; Upload de Fotos </div>
                    <ul class='nav nav-tabs'>   
                        <li class='active'><a data-toggle='tab' href='#dados_gerais'>Upload de fotos</a></li>                        
                    </ul>
                    <div class='tab-content'>
                        <div id='dados_gerais' class='tab-pane fade in active'>                                
                            <form action='upload_dragdrop.php?gal_id=$gal_id' class='dropzone'>
                                <div class='dz-message' data-dz-message>
                                <i class='fas fa-cloud-upload-alt' style='font-size:80px; color:#999'></i>
                                <br><br>
                                    <span style='font-size:22px;'>Arreste as fotos pra cá </span>
                                    <p>
                                    ou clique para selecionar
                                </div>
                            </form>
                            <div class='agenda_fotos'>
                            <br/>
                            <p class='titulo'>Fotos cadastradas</p>
                            ";
                            $sql = "SELECT * FROM cadastro_fotos_galeria                                
                                    WHERE fg_galeria = :gal_id ";
                            $stmt_foto = $PDO->prepare($sql);            
                            $stmt_foto->bindParam(':gal_id', $gal_id);
                            $stmt_foto->execute();
                            $rows_foto = $stmt_foto->rowCount();
                            if($rows_foto > 0)
                            {
                                while($result_foto = $stmt_foto->fetch())
                                {
                                    echo "
                                        <div class='foto' style='background:url(".$result_foto['fg_imagem'].") center center; background-size: cover;'>
                                            <i class='fas fa-times excluirFoto hand' id='".$result_foto['fg_id']."' onclick='excluirFoto(".$result_foto['fg_id'].")';></i>
                                        </div>

                                        ";
                                }
                            }
                            echo "
                            </div>
                        </div>	
                        
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>                    
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_galeria/view'; value='Voltar'/></center>
                        </center>
                    </div>                
                    ";
                }
            }	
    

            ?>
        </div> <!-- .content-wrapper -->
    </main> <!-- .cd-main-content -->
</body>

</html>