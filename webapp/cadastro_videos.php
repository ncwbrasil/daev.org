<?php
$pagina_link = 'cadastro_videos';
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start(); 
$page = "<a href='cadastro_videos/view'>Vídeos</a>"; 

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
            $page = "<a href='cadastro_videos/view'>Vídeos</a>";
            if (isset($_GET['vid_id'])) {
                $vid_id = $_GET['vid_id'];
            }
            if ($vid_id == '') {
                $vid_id = $_POST['vid_id'];
            }
            $vid_titulo             = $_POST['vid_titulo'];
            $vid_url                = geradorTags($vid_titulo);
            $vid_data               = implode("-", array_reverse(explode("/", $_POST['vid_data'])));
            $vid_descricao          =  $_POST['vid_descricao'];
            $vid_link               = $_POST['vid_link'];
            $vid_status             = $_POST['vid_status'];
            $vid_usuario            =  $_SESSION['usuario_id']; 

            $vid_hora               = $_POST['vid_hora'];
            $vid_meta_titulo        = $_POST['vid_meta_titulo'];
            $vid_meta_description   = $_POST['vid_meta_description'];

            $dados = array(
                'vid_titulo'         => $vid_titulo,
                'vid_data'           => $vid_data,
                'vid_descricao'      => $vid_descricao,
                'vid_link'           => $vid_link,
                'vid_url'            => $vid_url,
                'vid_status'         => $vid_status,
                'vid_usuario'               => $vid_usuario,
                'vid_hora'                  => $vid_hora,
                'vid_meta_titulo'           => $vid_meta_titulo,
                'vid_meta_description'      => $vid_meta_description,
            );
            
            if ($action == "adicionar") {

                $sql = "INSERT INTO cadastro_videos SET " . bindFields($dados);
                $stmt = $PDO->prepare($sql);
                if ($stmt->execute($dados)) {
                    // $vid_id = $PDO->lastInsertId();

                    // //UPLOAD ARQUIVOS
                    // require_once '../core/mod_includes/php/lib/WideImage.php';
                    // $caminho = "uploads/videos/";
                    // foreach ($_FILES as $key => $files) {
                    //     $files_test = array_filter($files['name']);
                    //     if (!empty($files_test)) {
                    //         if (!file_exists($caminho)) {
                    //             mkdir($caminho, 0755, true);
                    //         }
                    //         if (!empty($files["name"]["imagem"])) {
                    //             $nomeArquivo     = $files["name"]["imagem"];
                    //             $nomeTemporario = $files["tmp_name"]["imagem"];
                    //             $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                    //             $vid_foto    = $caminho;
                    //             $vid_foto .= "imagem_" . $nomeArquivo . '.' . $extensao;
                    //             $vid_foto2 = "imagem_" . $nomeArquivo . '.' . $extensao;
                    //             move_uploaded_file($nomeTemporario, ($vid_foto));
                    //             $imnfo = getimagesize($vid_foto);
                    //             $img_w = $imnfo[0];      // largura
                    //             $img_h = $imnfo[1];      // altura
                    //             if ($img_w > 900 || $img_h > 900) {
                    //                 $image = WideImage::load($vid_foto);
                    //                 $image = $image->resize(900, 900);
                    //                 $image->saveToFile($vid_foto);
                    //             }

                    //             $cni_id = $PDO->lastInsertId();
                    //             $sql2= "UPDATE cadastro_videos SET 
                    //             vid_foto = :vid_foto
                    //             WHERE vid_id = :vid_id ";
                    //             $stmt2 = $PDO->prepare($sql2);
                    //             $stmt2->bindParam(':vid_foto', $cni_id);
                    //             $stmt2->bindParam(':vid_id', $vid_id);
                    //             if ($stmt2->execute()) {
                    //             }
                    //             else {
                    //                 $erro = 1;
                    //                 $err = $stmt2->errorInfo();
                    //             }

                    //         }
                    //     }
                    // }
   
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

                $sql = "UPDATE cadastro_videos SET " . bindFields($dados) . " WHERE vid_id = :vid_id ";
                $stmt = $PDO->prepare($sql);
                $dados['vid_id'] =  $vid_id;
                if ($stmt->execute($dados)) {
                    // //UPLOAD ARQUIVOS
                    // require_once '../core/mod_includes/php/lib/WideImage.php';
                    // $caminho = "uploads/videos/";
                    // foreach ($_FILES as $key => $files) {
                    //     $files_test = array_filter($files['name']);
                    //     if (!empty($files_test)) {
                    //         if (!file_exists($caminho)) {
                    //             mkdir($caminho, 0755, true);
                    //         }
                    //         if (!empty($files["name"]["imagem"])) {
                    //             # EXCLUI ANEXO ANTIGO #
                    //             $sql = "SELECT * FROM cadastro_videos WHERE vid_id = :vid_id";
                    //             $stmt_antigo = $PDO->prepare($sql);
                    //             $stmt_antigo->bindParam(':vid_id', $vid_id);
                    //             $stmt_antigo->execute();
                    //             $result_antigo = $stmt_antigo->fetch();
                    //             $imagem_antigo = $result_antigo['vid_foto'];
                    //             unlink($imagem_antigo);

                    //             $nomeArquivo     = $files["name"]["imagem"];
                    //             $nomeTemporario = $files["tmp_name"]["imagem"];
                    //             $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                    //             $vid_foto    = $caminho;
                    //             $vid_foto .= "imagem_" . $nomeArquivo . '.' . $extensao;
                    //             $vid_foto2 = "imagem_" . $nomeArquivo . '.' . $extensao;                                
                    //             move_uploaded_file($nomeTemporario, ($vid_foto));
                    //             $imnfo = getimagesize($vid_foto);
                    //             $img_w = $imnfo[0];      // largura
                    //             $img_h = $imnfo[1];      // altura
                    //             if ($img_w > 900 || $img_h > 900) {
                    //                 $image = WideImage::load($vid_foto);
                    //                 $image = $image->resize(900, 900);
                    //                 $image->saveToFile($vid_foto);
                    //             }

                    //             $sql = "SELECT vid_foto FROM cadastro_videos
					// 			WHERE vid_id = :vid_id ";
                    //             $stmt = $PDO->prepare($sql);
                    //             $stmt->bindParam(':vid_id', $vid_id);
                    //             if ($stmt->execute()) {

                    //                 $result = $stmt->fetch(); 
                    //                 $sql2 = "UPDATE cadastro_videos SET 
					// 				vid_foto 	 = :vid_foto
					// 				WHERE cni_id = :cni_id ";
                    //                 $stmt2 = $PDO->prepare($sql2);
                    //                 $stmt2->bindParam(':vid_foto', $vid_foto2);
                    //                 $stmt2->bindParam(':cni_id', $result['vid_foto']);
                    //                 if ($stmt2->execute()) {
                    //                 } else {
                    //                     $erro = 1;
                    //                     $err = $stmt2->errorInfo();
                    //                 }

                    //             } else {
                    //                 $erro = 1;
                    //                 $err = $stmt->errorInfo();
                    //             }
                    //         }
                    //     }
                    // }
                    // //
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
                $sql = "DELETE FROM cadastro_videos WHERE vid_id = :vid_id";
                $stmt = $PDO->prepare($sql);
                $stmt->bindParam(':vid_id', $vid_id);
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
                $sql = "UPDATE cadastro_videos SET vid_status = :vid_status WHERE vid_id = :vid_id ";
                $stmt = $PDO->prepare($sql);
                $stmt->bindValue(':vid_status', 1);
                $stmt->bindParam(':vid_id', $vid_id);
                $stmt->execute();
            }
            if ($action == 'desativar') {
                $sql = "UPDATE cadastro_videos SET vid_status = :vid_status WHERE vid_id = :vid_id ";
                $stmt = $PDO->prepare($sql);
                $stmt->bindValue(':vid_status', 0);
                $stmt->bindParam(':vid_id', $vid_id);
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
                $nome_query = " (vid_titulo LIKE :fil_nome1  ) ";
            }

            $sql = "SELECT * FROM cadastro_videos 
                WHERE " . $nome_query . "
                ORDER BY vid_id DESC
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
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_videos/view'>
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
                        <td class='titulo_tabela' align='left' colspan='2' width='1'>Vídeo</td>
                        <td class='titulo_tabela' align='center'>Data</td>
                        <td class='titulo_last' align='right' width='100'>Gerenciar</td>
                    </tr>";
                    $c = 0;
                    while ($result = $stmt->fetch()) {
                        $vid_id         = $result['vid_id'];
                        $vid_titulo    = $result['vid_titulo'];
                        $vid_data    = implode("/", array_reverse(explode("-", $result['vid_data'])));

                        if ($c == 0) {
                            $c1 = "linhaimpar";
                            $c = 1;
                        } else {
                            $c1 = "linhapar";
                            $c = 0;
                        }
                        echo "<tr class='$c1'>
                                <td width='1'></td>
                                <td>$vid_titulo</td>
                                <td align='center'>$vid_data</td>
                                <td align=center>
                                    <div class='g_excluir' title='Excluir' onclick=\"
                                        abreMask(
                                            'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                            '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/view/excluir/$vid_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                            '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                        \">	<i class='far fa-trash-alt'></i>
                                    </div>
                                    <div class='g_editar' title='Editar' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"" . $pagina_link . "/edit/$vid_id\");'><i class='fas fa-pencil-alt'></i></div>
                                </td>
                            </tr>";
                    }
                    echo "</table>";
                    $variavel = "&fil_nome=$fil_nome";
                    $cnt = "SELECT COUNT(*) FROM cadastro_videos WHERE " . $nome_query . "  ";
                    $stmt = $PDO->prepare($cnt);
                    $stmt->bindParam(':fil_nome1',     $fil_nome1);
                    include("../core/mod_includes/php/paginacao.php");
                } else {
                    echo "<br><br><br>Não há nenhum item cadastrado.";
                }
            }
            if ($pagina == 'add') {
                echo "	
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_videos/view/adicionar'>
                        <div class='titulo'> $page &raquo; Adicionar  </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Título*:</label> <input name='vid_titulo' id='vid_titulo' placeholder='Título' class='obg' >
                                <p><label>Descrição*:</label> <div class='textarea'><textarea  name='vid_descricao' id='vid_descricao' placeholder='Descrição'></textarea></div>
                                <p><label>URL do Vídeo*:</label> <input name='vid_link' id='vid_link' placeholder='URL do Vídeo' class='obg' >
                                <p><label>Data:</label> <input name='vid_data' id='vid_data' placeholder='Data' class='obg'>

                                <p><label>Meta Tag Descrição:</label> <input name='vid_meta_titulo' id='vid_meta_titulo' placeholder='Meta Tag Descrição'>
                                <p><label>Meta Tag Título:</label> <input name='vid_meta_description' id='vid_meta_description' placeholder='Meta Tag Título'>

                                <div class='bloco'>
                                    <p><label>Data e Hora*:</label>
                                    <input type='text' name='nt_data' id='nt_data'> 
                                    <input type='time' name='nt_hora' id='nt_hora'>
                                </div>

    
                                <p><label>Status:</label> <input type='radio' name='vid_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type='radio' name='vid_status' value='0'> Inativo<br>                        				
                            </div>                    
                        </div>
                        <br>
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_videos/view'; value='Cancelar'/></center>
                        </center>
                    </form>
                ";
            }
            if ($pagina == 'edit') {
                $sql = "SELECT * FROM cadastro_videos 
				WHERE vid_id = :vid_id";
                $stmt = $PDO->prepare($sql);
                $stmt->bindParam(':vid_id', $vid_id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if ($rows > 0) {
                    $result = $stmt->fetch();
                    echo "
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_videos/view/editar/$vid_id'>
                    <div class='titulo'> $page &raquo; Editar </div>
                    <ul class='nav nav-tabs'>
                        <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                    </ul>
                    <div class='tab-content'>
                        <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Título*:</label> <input name='vid_titulo' id='vid_titulo' value='" . $result['vid_titulo'] . "' placeholder='Título' class='obg' >
                                <p><label>Descrição*:</label> <div class='textarea'><textarea name='vid_descricao' id='vid_descricao' placeholder='Descrição'>" . $result['vid_descricao'] . "</textarea></div>
                                <p><label>URL do Vídeo*:</label> <input name='vid_link' id='vid_link' value='" . $result['vid_link'] . "' placeholder='URL do Vídeo'>";
                                $vid_link   = $result['vid_link'];
                                preg_match('/[\\?\\&]v=([^\\?\\&]+)/',$vid_link,$matches);
        

                               echo"<center><iframe width='500px' height='300px' src='https://www.youtube.com/embed/$matches[1]' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe> </center>
                                <!--<p><label>Imagem:</label> -->";
                            // if ($result['vid_foto'] != '') {
                            //     echo "<img src='uploads/videos/" . $result['vid_foto'] . "' style='max-width:400px;'> ";
                            // }
                            // echo " &nbsp; 
                            //     <p><label>Alterar Imagem:</label> <input type='file' name='vid_foto[imagem]' id='vid_foto'>
                            
                            echo"
                            <p><label>Meta Tag Descrição:</label> <input name='vid_meta_titulo' id='vid_meta_titulo' placeholder='Meta Tag Descrição' value='" . $result['vid_meta_titulo'] . "'>
                            <p><label>Meta Tag Título:</label> <input name='vid_meta_description' id='vid_meta_description' placeholder='Meta Tag Título'  value='" . $result['vid_meta_description'] . "'>

                            <div class='bloco'>
                                <p><label>Data e Hora*:</label>
                                <input type='text' name='vid_data' id='vid_data' value='" . implode("/", array_reverse(explode("-", $result['vid_data']))) . "'> 
                                <input type='time' name='vid_hora' id='vid_hora' value='" . $result['vid_hora'] . "'>
                            </div>

                            <p><label>Status:</label>";
                            if ($result['vid_status'] == 1) {
                                echo "<input type='radio' name='vid_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type='radio' name='vid_status' value='0'> Inativo
                                        ";
                            } else {
                                echo "<input type='radio' name='vid_status' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type='radio' name='vid_status' value='0' checked> Inativo
                                        ";
                            }
                            echo "
                        </div>
						<br>
						<center>
						<div id='erro' align='center'>&nbsp;</div>
						<input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
						<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_videos/view'; value='Cancelar'/></center>
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