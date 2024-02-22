<?php
$pagina_link = 'cadastro_servidores';
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start();
$page = "<a href='cadastro_servidores/view'>Departamentos</a>";

?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <?php include_once("header.php") ?>

    <!-- DRAGDROP -->
    <link href="../core/mod_includes/js/dragdrop/dropzone.css" type="text/css" rel="stylesheet" />
    <script src="../core/mod_includes/js/dragdrop/dropzone.js"></script>

</head>

<body>
    <main class="cd-main-content">
        <div class="container">
            <?php include('../core/mod_menu/menu/menu.php') ?>
            <div class="wrapper">
                <div class='mensagem'></div>
                <?php
                $page = "<a href='cadastro_servidores/view'>Departamentos</a>";
                if (isset($_GET['cs_id'])) {
                    $cs_id = $_GET['cs_id'];
                }
                if ($cs_id == '') {
                    $cs_id = $_POST['cs_id'];
                }
                $cs_cargo          = $_POST['cs_cargo'];
                $cs_curriculo       =  $_POST['cs_curriculo'];
                $cs_nome            = $_POST['cs_nome'];
                $cs_url             = geradorTags($cs_nome.'-'.$cs_cargo);
                $cs_status          = $_POST['cs_status'];
                $cs_usuario         =  $_SESSION['usuario_id'];
                $cs_nome            = $_POST['cs_nome'];

                $dados = array(
                    'cs_cargo'          => $cs_cargo,
                    'cs_curriculo'      => $cs_curriculo,
                    'cs_url'            => $cs_url,
                    'cs_status'         => $cs_status,
                    'cs_usuario'        => $cs_usuario,
                    'cs_nome'           => $cs_nome,
                );

                if ($action == "adicionar") {

                    $sql = "INSERT INTO cadastro_servidores SET " . bindFields($dados);
                    $stmt = $PDO->prepare($sql);
                    if ($stmt->execute($dados)) {
                        $cs_id = $PDO->lastInsertId();
                        //UPLOAD ARQUIVOS
                        require_once '../core/mod_includes/php/lib/WideImage.php';
                        $caminho = "uploads/servidores/";
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
                                    $cs_imagem    = $caminho;
                                    $cs_imagem .= "imagem_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                    move_uploaded_file($nomeTemporario, ($cs_imagem));
                                    $imnfo = getimagesize($cs_imagem);
                                    $img_w = $imnfo[0];      // largura
                                    $img_h = $imnfo[1];      // altura
                                    if ($img_w > 900 || $img_h > 900) {
                                        $image = WideImage::load($cs_imagem);
                                        $image = $image->resize(900, 900);
                                        $image->saveToFile($cs_imagem);
                                    }
                                    $sql = "UPDATE cadastro_servidores SET 
                                    cs_imagem 	 = :cs_imagem
                                    WHERE cs_id = :cs_id ";
                                    $stmt = $PDO->prepare($sql);
                                    $stmt->bindParam(':cs_imagem', $cs_imagem);
                                    $stmt->bindParam(':cs_id', $cs_id);

                                    if ($stmt->execute()) {
                                    } else {
                                        $erro = 1;
                                        $err = $stmt->errorInfo();
                                    }
                                }
                                if (!empty($files["name"]["documento"])) {
                                    $nomeArquivo     = $files["name"]["documento"];
                                    $nomeTemporario = $files["tmp_name"]["documento"];
                                    $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                    $cs_documento    = $caminho;
                                    $cs_documento .= "documento_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                    move_uploaded_file($nomeTemporario, ($cs_documento));
                                    $sql = "UPDATE cadastro_servidores SET 
                                    cs_documento 	 = :cs_documento
                                    WHERE cs_id = :cs_id ";
                                    $stmt = $PDO->prepare($sql);
                                    $stmt->bindParam(':cs_documento', $cs_documento);
                                    $stmt->bindParam(':cs_id', $cs_id);

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

                    $sql = "UPDATE cadastro_servidores SET " . bindFields($dados) . " WHERE cs_id = :cs_id ";
                    $stmt = $PDO->prepare($sql);
                    $dados['cs_id'] =  $cs_id;
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
                                    $sql = "SELECT * FROM cadastro_servidores WHERE cs_id = :cs_id";
                                    $stmt_antigo = $PDO->prepare($sql);
                                    $stmt_antigo->bindParam(':cs_id', $cs_id);
                                    $stmt_antigo->execute();
                                    $result_antigo = $stmt_antigo->fetch();
                                    $imagem_antigo = $result_antigo['cs_imagem'];
                                    unlink($imagem_antigo);

                                    $nomeArquivo     = $files["name"]["imagem"];
                                    $nomeTemporario = $files["tmp_name"]["imagem"];
                                    $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                    $cs_imagem    = $caminho;
                                    $cs_imagem .= "imagem_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                    move_uploaded_file($nomeTemporario, ($cs_imagem));
                                    $imnfo = getimagesize($cs_imagem);
                                    $img_w = $imnfo[0];      // largura
                                    $img_h = $imnfo[1];      // altura
                                    if ($img_w > 900 || $img_h > 900) {
                                        $image = WideImage::load($cs_imagem);
                                        $image = $image->resize(900, 900);
                                        $image->saveToFile($cs_imagem);
                                    }
                                    $sql = "UPDATE cadastro_servidores SET 
									cs_imagem 	 = :cs_imagem
									WHERE cs_id = :cs_id ";
                                    $stmt = $PDO->prepare($sql);
                                    $stmt->bindParam(':cs_imagem', $cs_imagem);
                                    $stmt->bindParam(':cs_id', $cs_id);
                                    if ($stmt->execute()) {
                                    } else {
                                        $erro = 1;
                                        $err = $stmt->errorInfo();
                                    }
                                }
                                if (!empty($files["name"]["documento"])) {
                                    # EXCLUI ANEXO ANTIGO #
                                    $sql = "SELECT * FROM cadastro_servidores WHERE cs_id = :cs_id";
                                    $stmt_antigo = $PDO->prepare($sql);
                                    $stmt_antigo->bindParam(':cs_id', $cs_id);
                                    $stmt_antigo->execute();
                                    $result_antigo = $stmt_antigo->fetch();
                                    $documento_antigo = $result_antigo['cs_documento'];
                                    unlink($documento_antigo);

                                    $nomeArquivo     = $files["name"]["documento"];
                                    $nomeTemporario = $files["tmp_name"]["documento"];
                                    $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                    $cs_documento    = $caminho;
                                    $cs_documento .= "documento_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                    move_uploaded_file($nomeTemporario, ($cs_documento));
                                    $sql = "UPDATE cadastro_servidores SET 
                                    cs_documento 	 = :cs_documento
                                    WHERE cs_id = :cs_id ";
                                    $stmt = $PDO->prepare($sql);
                                    $stmt->bindParam(':cs_documento', $cs_documento);
                                    $stmt->bindParam(':cs_id', $cs_id);

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
                    $sql = "DELETE FROM cadastro_servidores WHERE cs_id = :cs_id";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam(':cs_id', $cs_id);
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
                    $sql = "UPDATE cadastro_servidores SET cs_status = :cs_status WHERE cs_id = :cs_id ";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindValue(':cs_status', 1);
                    $stmt->bindParam(':cs_id', $cs_id);
                    $stmt->execute();
                }

                if ($action == 'desativar') {
                    $sql = "UPDATE cadastro_servidores SET cs_status = :cs_status WHERE cs_id = :cs_id ";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindValue(':cs_status', 0);
                    $stmt->bindParam(':cs_id', $cs_id);
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
                    $nome_query = " (cs_cargo LIKE :fil_nome1  ) ";
                }

                $sql = "SELECT * FROM cadastro_servidores 
                WHERE " . $nome_query . "
                ORDER BY cs_cargo ASC
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
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_servidores/view'>
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
                        <td class='titulo_tabela' align='center'>Cargo</td>
                        <td class='titulo_last' align='right' width='100'>Gerenciar</td>
                    </tr>";
                        $c = 0;
                        while ($result = $stmt->fetch()) {
                            $cs_id              = $result['cs_id'];
                            $cs_cargo           = $result['cs_cargo'];
                            $cs_imagem          = $result['cs_imagem'];
                            $cs_ordem           = $result['cs_ordem'];
                            $cs_nome            = $result['cs_nome'];

                            if ($c == 0) {
                                $c1 = "linhaimpar";
                                $c = 1;
                            } else {
                                $c1 = "linhapar";
                                $c = 0;
                            }
                            echo "<tr class='$c1'>
                                <td><img src='$cs_imagem' style='object-fit:cover; width:120px; height:80px'></td>
                                <td>$cs_nome</td>
                                <td align='center'>$cs_cargo</td>
                                <td align=center>
										<div class='g_excluir' title='Excluir' onclick=\"
											abreMask(
												'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
												'<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/view/excluir/$cs_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
												'<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
											\">	<i class='far fa-trash-alt'></i>
										</div>
										<div class='g_editar' title='Editar' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"" . $pagina_link . "/edit/$cs_id\");'><i class='fas fa-pencil-alt'></i></div>
								  </td>
                              </tr>";
                        }
                        echo "</table>";
                        $variavel = "&fil_nome=$fil_nome";
                        $cnt = "SELECT COUNT(*) FROM cadastro_servidores WHERE " . $nome_query . "  ";
                        $stmt = $PDO->prepare($cnt);
                        $stmt->bindParam(':fil_nome1',     $fil_nome1);
                        include("../core/mod_includes/php/paginacao.php");
                    } else {
                        echo "<br><br><br>Não há nenhum item cadastrado.";
                    }
                }

                if ($pagina == 'add') {
                    echo "	
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_servidores/view/adicionar'>
                        <div class='titulo'> $page &raquo; Adicionar  </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Cargo*:</label> <input name='cs_cargo' id='cs_cargo' placeholder='Cargo' class='obg' >
                                <p><label>Responsável*:</label> <input name='cs_nome' id='cs_nome' placeholder='Responsável' class='obg' >
                                <p><label>Descrição*:</label> <div class='textarea'><textarea  name='cs_curriculo' id='cs_curriculo' placeholder='Descrição'></textarea></div>
                                <p><label>Imagem:</label> <input type='file' name='cs_imagem[imagem]' id='cs_imagem'>
                                <p><label>Currículo:</label> <input type='file' name='cs_documento[documento]' id='cs_documento'>
                                <p><label>Status:</label> <input type='radio' name='cs_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type='radio' name='cs_status' value='0'> Inativo<br>		
                                                
                            </div>                    
                        </div>
                        <br>
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_servidores/view'; value='Cancelar'/></center>
                        </center>
                    </form>
                ";
                }

                if ($pagina == 'edit') {
                    $sql = "SELECT * FROM cadastro_servidores 
					WHERE cs_id = :cs_id";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam(':cs_id', $cs_id);
                    $stmt->execute();
                    $rows = $stmt->rowCount();
                    if ($rows > 0) {
                        $result = $stmt->fetch();
                        echo "
                        <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_servidores/view/editar/$cs_id'>
                            <div class='titulo'> $page &raquo; Editar </div>
                            <ul class='nav nav-tabs'>
                                <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                            </ul>
                            <div class='tab-content'>
                                <div id='dados_gerais' class='tab-pane fade in active'>
                                        <p><label>Cargo*:</label> <input name='cs_cargo' id='cs_cargo' value='" . $result['cs_cargo'] . "' placeholder='Cargo' class='obg' >
                                        <p><label>Responsável*:</label> <input name='cs_nome' id='cs_nome' value='" . $result['cs_nome'] . "' placeholder='Responsável' class='obg' >
                                        <p><label>Descrição*:</label> <div class='textarea'><textarea name='cs_curriculo' id='cs_curriculo' placeholder='Descrição'>" . $result['cs_curriculo'] . "</textarea></div>

                                        <p><label>Imagem:</label> ";
                        if ($result['cs_imagem'] != '') {
                            echo "<img src='" . $result['cs_imagem'] . "' style='max-width:400px;'> ";
                        }
                        echo " &nbsp; 
                                        <p><label>Alterar Imagem:</label> <input type='file' name='cs_imagem[imagem]' id='cs_imagem'>
                                        <p><label>Currículo:</label>";
                                        if ($result['cs_documento'] != '') {
                                            echo " <a href='" . $result['cs_documento'] . "'><i class='fa-solid fa-file-pdf'></i></a> ";
                                        }
                                        echo " &nbsp; 
                                        <p><label>Alterar Currículo:</label> <input type='file' name='cs_documento[documento]' id='cs_documento'>
                                        <p><label>Status:</label>";
                        if ($result['cs_status'] == 1) {
                            echo "<input type='radio' name='cs_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type='radio' name='cs_status' value='0'> Inativo
                                                ";
                        } else {
                            echo "<input type='radio' name='cs_status' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type='radio' name='cs_status' value='0' checked> Inativo
                                                ";
                        }
                        echo "
                        
                                </div>
                                <br>
                                <center>
                                <div id='erro' align='center'>&nbsp;</div>
                                <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_servidores/view'; value='Cancelar'/></center>
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