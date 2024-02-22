<?php
$pagina_link = 'aux_configuracao';
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start(); 
$page = "<a href='aux_configuracao/view'>Configurações</a>"; 

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
            $page = "<a href='aux_configuracao/view'>Configurações</a>";
            if (isset($_GET['conf_id'])) {
                $conf_id = $_GET['conf_id'];
            }
            if ($conf_id == '') {
                $conf_id = $_POST['conf_id'];
            }
            $conf_login          = $_POST['conf_login'];
            $conf_cadastro       =  $_POST['conf_cadastro'];
            $conf_email          = $_POST['conf_email'];
            $conf_contato        = $_POST['conf_contato'];
            $conf_face           =  $_POST['conf_face']; 
            $conf_whats          =  $_POST['conf_whats']; 
            $conf_instagram          =  $_POST['conf_instagram']; 

            $dados = array(
                'conf_login'        => $conf_login,
                'conf_cadastro'     => $conf_cadastro,
                'conf_email'        => $conf_email,
                'conf_face'         => $conf_face,
                'conf_contato'      => $conf_contato,
                'conf_whats'        => $conf_whats,
                'conf_instagram'        => $conf_instagram, 
            );
            
            if ($action == "adicionar") {

                $sql = "INSERT INTO aux_configuracao SET " . bindFields($dados);
                $stmt = $PDO->prepare($sql);
                if ($stmt->execute($dados)) {
                    $conf_id = $PDO->lastInsertId();
                    //UPLOAD ARQUIVOS
                    require_once '../core/mod_includes/php/lib/WideImage.php';
                    $caminho = "uploads/configuracoes/";
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
                                $conf_logo    = $caminho;
                                $conf_logo .= "imagem_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                move_uploaded_file($nomeTemporario, ($conf_logo));
                                $imnfo = getimagesize($conf_logo);
                                $img_w = $imnfo[0];      // largura
                                $img_h = $imnfo[1];      // altura
                                if ($img_w > 900 || $img_h > 900) {
                                    $image = WideImage::load($conf_logo);
                                    $image = $image->resize(900, 900);
                                    $image->saveToFile($conf_logo);
                                }
                                $sql = "UPDATE aux_configuracao SET 
                                    conf_logo 	 = :conf_logo
                                    WHERE conf_id = :conf_id ";
                                $stmt = $PDO->prepare($sql);
                                $stmt->bindParam(':conf_logo', $conf_logo);
                                $stmt->bindParam(':conf_id', $conf_id);

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

                $sql = "UPDATE aux_configuracao SET " . bindFields($dados) . " WHERE conf_id = :conf_id ";
                $stmt = $PDO->prepare($sql);
                $dados['conf_id'] =  $conf_id;
                if ($stmt->execute($dados)) {
                    //UPLOAD ARQUIVOS
                    require_once '../core/mod_includes/php/lib/WideImage.php';
                    $caminho = "uploads/configuracoes/";
                    foreach ($_FILES as $key => $files) {
                        $files_test = array_filter($files['name']);
                        if (!empty($files_test)) {
                            if (!file_exists($caminho)) {
                                mkdir($caminho, 0755, true);
                            }
                            if (!empty($files["name"]["imagem"])) {
                                # EXCLUI ANEXO ANTIGO #
                                $sql = "SELECT * FROM aux_configuracao WHERE conf_id = :conf_id";
                                $stmt_antigo = $PDO->prepare($sql);
                                $stmt_antigo->bindParam(':conf_id', $conf_id);
                                $stmt_antigo->execute();
                                $result_antigo = $stmt_antigo->fetch();
                                $imagem_antigo = $result_antigo['conf_logo'];
                                unlink($imagem_antigo);

                                $nomeArquivo     = $files["name"]["imagem"];
                                $nomeTemporario = $files["tmp_name"]["imagem"];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $conf_logo    = $caminho;
                                $conf_logo .= "imagem_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                move_uploaded_file($nomeTemporario, ($conf_logo));
                                $imnfo = getimagesize($conf_logo);
                                $img_w = $imnfo[0];      // largura
                                $img_h = $imnfo[1];      // altura
                                if ($img_w > 900 || $img_h > 900) {
                                    $image = WideImage::load($conf_logo);
                                    $image = $image->resize(900, 900);
                                    $image->saveToFile($conf_logo);
                                }
                                $sql = "UPDATE aux_configuracao SET 
									conf_logo 	 = :conf_logo
									WHERE conf_id = :conf_id ";
                                $stmt = $PDO->prepare($sql);
                                $stmt->bindParam(':conf_logo', $conf_logo);
                                $stmt->bindParam(':conf_id', $conf_id);
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



            $sql = "SELECT * FROM aux_configuracao";
            $stmt = $PDO->prepare($sql);
            $stmt->execute();
            $rows = $stmt->rowCount();
            if ($pagina == "view") {
                if ($rows > 0) {

                    $result = $stmt->fetch();
                    echo "
                        <form name='form' id='form' enctype='multipart/form-data' method='post' action='aux_configuracao/view/editar/".$result['conf_id']."'>
                            <div class='titulo'> $page &raquo; Editar </div>
                            <ul class='nav nav-tabs'>
                                <li class='active'><a data-toggle='tab' href='#dados_topo'>Topo</a></li>
                            </ul>
                            <div class='tab-content'>
                                <div id='dados_topo' class='tab-pane fade in active'>
                                    <p><label>Logo:</label> ";
                                    if ($result['conf_logo'] != '') {
                                        echo "<img src='" . $result['conf_logo'] . "' style='max-width:400px;'> ";
                                    }
                                    echo " &nbsp; 
                                    <p><label>Alterar Logo:</label> <input type='file' name='conf_logo[imagem]' id='conf_logo'>                                    
                                    <p><label>Email de Contato:</label> <input name='conf_email' id='conf_email' placeholder='Email de Contato' value='".$result['conf_email']."' >
                                    <p><label>Telefones de Contato:</label> <input name='conf_contato' id='conf_contato' placeholder='Telefones de Contato' value='".$result['conf_contato']."'>
                                    <p><label>Whatsapp:</label> <input name='conf_whats' id='conf_whats' placeholder='Whatsapp' value='".$result['conf_whats']."'>
                                    <p><label>Link do Facebook:</label> <input name='conf_face' id='conf_face' placeholder='Link do Facebook' value='".$result['conf_face']."'>
                                    <p><label>Link do Instagram:</label> <input name='conf_insta' id='conf_insta' placeholder='Link do Instagram' value='".$result['conf_instagram']."'>

                                    <p><label>Área de Cadastro:</label>";
                                    if ($result['conf_cadastro'] == 1) {
                                        echo "<input type='radio' name='conf_cadastro' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type='radio' name='conf_cadastro' value='0'> Inativo
                                                ";
                                    } else {
                                        echo "<input type='radio' name='conf_cadastro' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type='radio' name='conf_cadastro' value='0' checked> Inativo
                                                ";
                                    }
        
                                    echo"<p><label>Área de Login:</label>";
                                    if ($result['conf_login'] == 1) {
                                        echo "<input type='radio' name='conf_login' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type='radio' name='conf_login' value='0'> Inativo
                                                ";
                                    } else {
                                        echo "<input type='radio' name='conf_login' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type='radio' name='conf_login' value='0' checked> Inativo
                                                ";
                                    }
        
                                echo"</div>
                                <center>
                                <div id='erro' align='center'>&nbsp;</div>
                                <input type='submit' id='bt_aux_configuracao' value='Atualizar' />
                                </center>
                            </div>
                        </form>
                    ";
                } else {
                    echo"
                        <form name='form' id='form' enctype='multipart/form-data' method='post' action='aux_configuracao/view/adicionar'>
                            <div class='titulo'> $page &raquo; Editar </div>
                            <ul class='nav nav-tabs'>
                                <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            </ul>
                            <div class='tab-content'>
                                <div id='dados_gerais' class='tab-pane fade in active'>
                                    <p><label>Imagem:</label> <input type='file' name='conf_logo[imagem]' id='conf_logo' class='obg'>
                                    <p><label>Email de Contato:</label> <input name='conf_email' id='conf_email' placeholder='Email de Contato' class='obg'>
                                    <p><label>Telefones de Contato:</label> <input name='conf_contato' id='conf_contato' placeholder='Telefones de Contato' class='obg'>
                                    <p><label>Whatsapp:</label> <input name='conf_whats' id='conf_whats' placeholder='Whatsapp' class='obg'>
                                    <p><label>Link do Facebook:</label> <input name='conf_face' id='conf_face' placeholder='Link do Facebook' class='obg'>
                                    <p><label>Área de Cadastro:</label> <input type='radio' name='conf_cadastro' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type='radio' name='conf_cadastro' value='0'> Inativo<br>		
                                    <p><label>Área de Login:</label> <input type='radio' name='conf_login' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type='radio' name='conf_login' value='0'> Inativo<br>		
                                </div>
                                <center>
                                <div id='erro' align='center'>&nbsp;</div>
                                <input type='submit' id='bt_aux_configuracao' value='Atualizar' />
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