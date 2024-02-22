<?php
$pagina_link = 'cadastro_noticias';
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start();
$page = "<a href='cadastro_noticias/view'>Na Mídia</a>";

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
                $page = "<a href='cadastro_noticias/view'>Notícias</a>";
                if (isset($_GET['nt_id'])) {
                    $nt_id = $_GET['nt_id'];
                }
                if ($nt_id == '') {
                    $nt_id = $_POST['nt_id'];
                }
                $nt_titulo          = $_POST['nt_titulo'];
                $nt_subtitulo       = $_POST['nt_subtitulo'];
                $nt_descricao       =  $_POST['nt_descricao'];
                $nt_url             = geradorTags($nt_titulo);
                $nt_status          = $_POST['nt_status'];
                $nt_usuario         =  $_SESSION['usuario_id'];
                $nt_subtitulo       = $_POST['nt_subtitulo'];
                $cni_titulo             = $_POST['cni_titulo'];
                $nt_categoria           = $_POST['nt_categoria'];
                $nt_meta_description    = $_POST['nt_meta_description'];
                $nt_meta_titulo         = $_POST['nt_meta_titulo'];

                if($_POST['nt_hora'] == ''){
                    $nt_hora = date('H:i:s');
                }else {
                    $nt_hora                =  $_POST['nt_hora'];
                }
                $nt_data            = implode("-", array_reverse(explode("/", $_POST['nt_data']))).' '.$nt_hora;

                $dados = array(
                    'nt_titulo'         => $nt_titulo,
                    'nt_subtitulo'      => $nt_subtitulo,
                    'nt_descricao'      => $nt_descricao,
                    'nt_url'            => $nt_url,
                    'nt_data'           => $nt_data,
                    'nt_status'         => $nt_status,
                    'nt_usuario'        => $nt_usuario,
                    'nt_categoria'      => $nt_categoria,
                    'nt_meta_description' => $nt_meta_description,
                    'nt_meta_titulo' => $nt_meta_titulo,
                    'nt_hora' => $nt_hora,
                );

                if ($action == "adicionar") {

                    $sql = "INSERT INTO cadastro_noticias SET " . bindFields($dados);
                    $stmt = $PDO->prepare($sql);
                    if ($stmt->execute($dados)) {
                        $nt_id = $PDO->lastInsertId();

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

                                    $nomeArquivo    = geradorTags($files["name"]["imagem"]);
                                    $nomeTemporario = $files["tmp_name"]["imagem"];

                                    $extensao = pathinfo($files["name"]["imagem"], PATHINFO_EXTENSION);
                                    $cni_foto    = $caminho;
                                    $cni_foto .= "imagem_" . $nomeArquivo . '.' . $extensao;
                                    $cni_foto2 = "imagem_" . $nomeArquivo . '.' . $extensao;
                                    move_uploaded_file($nomeTemporario, ($cni_foto));
                                    $imnfo = getimagesize($cni_foto);
                                    $img_w = $imnfo[0];      // largura
                                    $img_h = $imnfo[1];      // altura
                                    if ($img_w > 900 || $img_h > 900) {
                                        $image = WideImage::load($cni_foto);
                                        $image = $image->resize(900, 900);
                                        $image->saveToFile($cni_foto);
                                    }
                                    $sql1 = "INSERT INTO cadastro_noticias_imagens SET cni_titulo = :cni_titulo, cni_foto = :cni_foto ";
                                    $stmt1 = $PDO->prepare($sql1);
                                    $stmt1->bindParam(':cni_foto', $cni_foto2);
                                    $stmt1->bindParam(':cni_titulo', $cni_titulo);
                                    if ($stmt1->execute()) {
                                        $cni_id = $PDO->lastInsertId();
                                        $sql2 = "UPDATE cadastro_noticias SET 
                                    nt_foto = :nt_foto
                                    WHERE nt_id = :nt_id ";
                                        $stmt2 = $PDO->prepare($sql2);
                                        $stmt2->bindParam(':nt_foto', $cni_id);
                                        $stmt2->bindParam(':nt_id', $nt_id);
                                        if ($stmt2->execute()) {
                                        } else {
                                            $erro = 1;
                                            $err = $stmt2->errorInfo();
                                        }
                                    } else {
                                        $erro = 1;
                                        $err = $stmt1->errorInfo();
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
                        $erro = 1;
                        $err = $stmt->errorInfo();

                        print_r($err); 

                    ?>
                        <script>
                            mensagem("X", "<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                        </script>
                    <?php
                    }
                }

                if ($action == 'editar') {

                    $sql = "UPDATE cadastro_noticias SET " . bindFields($dados) . " WHERE nt_id = :nt_id ";
                    $stmt = $PDO->prepare($sql);
                    $dados['nt_id'] =  $nt_id;
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
                                    $sql = "SELECT * FROM cadastro_noticias WHERE nt_id = :nt_id";
                                    $stmt_antigo = $PDO->prepare($sql);
                                    $stmt_antigo->bindParam(':nt_id', $nt_id);
                                    $stmt_antigo->execute();
                                    $result_antigo = $stmt_antigo->fetch();
                                    $imagem_antigo = $result_antigo['cni_foto'];
                                    unlink($imagem_antigo);

                                    $nomeArquivo    = geradorTags($files["name"]["imagem"]);
                                    $nomeTemporario = $files["tmp_name"]["imagem"];

                                    $extensao = pathinfo($files["name"]["imagem"], PATHINFO_EXTENSION);
                                    $cni_foto    = $caminho;
                                    $cni_foto .= "imagem_" . $nomeArquivo . '.' . $extensao;
                                    $cni_foto2 = "imagem_" . $nomeArquivo . '.' . $extensao;
                                    move_uploaded_file($nomeTemporario, ($cni_foto));
                                    $imnfo = getimagesize($cni_foto);
                                    $img_w = $imnfo[0];      // largura
                                    $img_h = $imnfo[1];      // altura
                                    if ($img_w > 900 || $img_h > 900) {
                                        $image = WideImage::load($cni_foto);
                                        $image = $image->resize(900, 900);
                                        $image->saveToFile($cni_foto);
                                    }

                                    $sql = "SELECT nt_foto FROM cadastro_noticias
								    WHERE nt_id = :nt_id ";
                                    $stmt = $PDO->prepare($sql);
                                    $stmt->bindParam(':nt_id', $nt_id);
                                    if ($stmt->execute()) {
                                        $result = $stmt->fetch();
                                        $sql2 = "UPDATE cadastro_noticias_imagens SET 
                                        cni_foto 	 = :cni_foto, 
                                        cni_titulo = :cni_titulo
                                        WHERE cni_id = :cni_id ";
                                        $stmt2 = $PDO->prepare($sql2);
                                        $stmt2->bindParam(':cni_foto', $cni_foto2);
                                        $stmt2->bindParam(':cni_titulo', $cni_titulo);
                                        $stmt2->bindParam(':cni_id', $result['nt_foto']);
                                        if ($stmt2->execute()) {
                                        } else {
                                            $erro = 1;
                                            $err = $stmt2->errorInfo();
                                        }
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
                        $erro = 1;
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
                    $sql = "DELETE FROM cadastro_noticias WHERE nt_id = :nt_id";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam(':nt_id', $nt_id);
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
                    $sql = "UPDATE cadastro_noticias SET nt_status = :nt_status WHERE nt_id = :nt_id ";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindValue(':nt_status', 1);
                    $stmt->bindParam(':nt_id', $nt_id);
                    $stmt->execute();
                }
                if ($action == 'desativar') {
                    $sql = "UPDATE cadastro_noticias SET nt_status = :nt_status WHERE nt_id = :nt_id ";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindValue(':nt_status', 0);
                    $stmt->bindParam(':nt_id', $nt_id);
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
                    $nome_query = " (nt_titulo LIKE :fil_nome1  ) ";
                }

                $sql = "SELECT * FROM cadastro_noticias 
					LEFT JOIN cadastro_noticias_imagens ON cadastro_noticias_imagens.cni_id = cadastro_noticias.nt_foto
                    LEFT JOIN cadastro_categoria_noticias ON cadastro_categoria_noticias.cn_id = cadastro_noticias.nt_categoria
                WHERE " . $nome_query . "
                ORDER BY nt_id DESC
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
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_noticias/view'>
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
                        <td class='titulo_tabela' align='left' colspan='2' width='1'>Notícia</td>
                        <td class='titulo_tabela' align='center'>Categoria</td>
                        <td class='titulo_tabela' align='center'>Programado para</td>
                        <td class='titulo_last' align='right' width='100'>Gerenciar</td>
                    </tr>";
                        $c = 0;
                        while ($result = $stmt->fetch()) {
                            $nt_id         = $result['nt_id'];
                            $nt_titulo    = $result['nt_titulo'];
                            $data = date_create($result['nt_data']); 	
                            $nt_hora    = $result['nt_hora'];
                            $cni_foto    = $result['cni_foto'];
                            $cn_nome    = $result['cn_nome'];

                            if ($c == 0) {
                                $c1 = "linhaimpar";
                                $c = 1;
                            } else {
                                $c1 = "linhapar";
                                $c = 0;
                            }
                            echo "<tr class='$c1'>
                                <td width='1'><img src='uploads/noticias/$cni_foto' style='object-fit:cover; width:120px; height:80px'></td>
                                <td>$nt_titulo</td>
                                <td align='center'>$cn_nome</td>
                                <td align='center'> " . date_format($data,'d/m/Y - H:i') . " </td>
                                <td align=center>
                                    <div class='g_excluir' title='Excluir' onclick=\"
                                        abreMask(
                                            'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                            '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/view/excluir/$nt_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                            '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                        \">	<i class='far fa-trash-alt'></i>
                                    </div>
                                    <div class='g_editar' title='Editar' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"" . $pagina_link . "/edit/$nt_id\");'><i class='fas fa-pencil-alt'></i></div>
                                </td>
                            </tr>";
                        }
                        echo "</table>";
                        $variavel = "&fil_nome=$fil_nome";
                        $cnt = "SELECT COUNT(*) FROM cadastro_noticias WHERE " . $nome_query . "  ";
                        $stmt = $PDO->prepare($cnt);
                        $stmt->bindParam(':fil_nome1',     $fil_nome1);
                        include("../core/mod_includes/php/paginacao.php");
                    } else {
                        echo "<br><br><br>Não há nenhum item cadastrado.";
                    }
                }
                if ($pagina == 'add') {
                    echo "	
            <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_noticias/view/adicionar'>
                <div class='titulo'> $page &raquo; Adicionar  </div>
                <ul class='nav nav-tabs'>
                      <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                </ul>
                <div class='tab-content' id='datas_licitacao'>
                    <div id='dados_gerais' class='tab-pane fade in active'>
                        <p><label>Título*:</label> <input name='nt_titulo' id='nt_titulo' placeholder='Título' class='obg' >
                        <p><label>Subtitulo*:</label> <input name='nt_subtitulo' id='nt_subtitulo' placeholder='Subtitulo'>
                        <p><label>Categoria:</label> 
                        <select name='nt_categoria' id='nt_categoria'>
                            <option value='0'>Selecione </option>";
                    $sql_categorias = "SELECT * FROM cadastro_categoria_noticias";
                    $stmt_categorias = $PDO->prepare($sql_categorias);
                    $stmt_categorias->execute();
                    $rows_categorias = $stmt_categorias->rowCount();
                    if ($rows_categorias > 0) {
                        while ($result_categorias = $stmt_categorias->fetch()) {
                            echo "
                                        <option value='" . $result_categorias['cn_id'] . "'>" . $result_categorias['cn_nome'] . "</option>
                                    ";
                        }
                    }
                    echo " 
                            <option value='nova_categoria'><b>Nova Categoria*</b> </option>
                        </select>
                        <p id='cat' style='display:none'><label>Nova Categoria:</label> <input name='cn_nome' id='cn_nome' placeholder='Nova Categoria' >
                        <p><label>Descrição*:</label> <div class='textarea'><textarea  name='nt_descricao' id='nt_descricao' placeholder='Descrição'></textarea></div>
                        <p><label>Imagem:</label> <input type='file' name='cni_foto[imagem]' id='cni_foto' class='obg'>
                        <p><label>Título Imagem:</label> <input name='cni_titulo' id='cni_titulo' placeholder='Título da Imagem' >
                        <p><label>Meta Tag Descrição:</label> <input name='nt_meta_description' id='nt_meta_description' placeholder='Meta Tag Descrição'>
                        <p><label>Meta Tag Título:</label> <input name='nt_meta_titulo' id='nt_meta_titulo' placeholder='Meta Tag Título'>
                        <div class='bloco'>
                            <p><label>Data e Hora*:</label>
                            <input type='text' name='nt_data' id='nt_data'> 
                            <input type='time' name='nt_hora' id='nt_hora'>
                        </div>
                        <p><label>Status:</label> <input type='radio' name='nt_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type='radio' name='nt_status' value='0'> Inativo<br>		
                        				
                    </div>                    
				</div>
                <br>
                <center>
                <div id='erro' align='center'>&nbsp;</div>
                <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_noticias/view'; value='Cancelar'/></center>
                </center>
            </form>
            ";
                }
                if ($pagina == 'edit') {
                    $sql = "SELECT * FROM cadastro_noticias 
                    LEFT JOIN cadastro_noticias_imagens ON cadastro_noticias_imagens.cni_id = cadastro_noticias.nt_foto
                    LEFT JOIN cadastro_categoria_noticias ON cadastro_categoria_noticias.cn_id = cadastro_noticias.nt_categoria
					WHERE nt_id = :nt_id";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam(':nt_id', $nt_id);
                    $stmt->execute();
                    $rows = $stmt->rowCount();
                    if ($rows > 0) {
                        $result = $stmt->fetch();
                        $data = date_create($result['nt_data']); 	
                        echo "
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_noticias/view/editar/$nt_id'>
                    <div class='titulo'> $page &raquo; Editar </div>
                    <ul class='nav nav-tabs'>
                        <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                    </ul>
                    <div class='tab-content' id='datas_licitacao'>
                        <div id='dados_gerais' class='tab-pane fade in active'> 
                            <center> Última Atualização : " . date("d/m/Y, g:i a", strtotime($result['nt_hora_cadastro'])) . " </center>
                            <p><label>Título*:</label> <input name='nt_titulo' id='nt_titulo' value='" . $result['nt_titulo'] . "' placeholder='Título' class='obg' >
                            <p><label>Subtitulo:</label> <input name='nt_subtitulo' id='nt_subtitulo' value='" . $result['nt_subtitulo'] . "' placeholder='Subtitulo'>
                            <p><label>Categorias:</label> 
                            <select name='nt_categoria' id='nt_categoria'>
                                <option value='" . $result['cn_id'] . "'> " . $result['cn_nome'] . "</option>";
                                    $sql_categorias = "SELECT * FROM cadastro_categoria_noticias";
                                    $stmt_categorias = $PDO->prepare($sql_categorias);
                                    $stmt_categorias->execute();
                                    $rows_categorias = $stmt_categorias->rowCount();
                                    if ($rows_categorias > 0) {
                                        while ($result_categorias = $stmt_categorias->fetch()) {
                                            echo "
                                                            <option value='" . $result_categorias['cn_id'] . "'>" . $result_categorias['cn_nome'] . "</option>
                                                        ";
                                        }
                                    }
                                    echo " 
                                    <option value=''>Selecione </option>
                                    <option value='nova_categoria'><b>Nova Categoria*</b> </option>
                                </select>
                                <p id='cat' style='display:none'><label>Nova Categoria:</label> <input name='cn_nome' id='cn_nome' placeholder='Nova Categoria'> </p>

                                <p><label>Descrição*:</label> <div class='textarea'><textarea name='nt_descricao' id='nt_descricao' placeholder='Descrição'>" . $result['nt_descricao'] . "</textarea></div>

                                <p><label>Imagem:</label> ";
                        if ($result['cni_foto'] != '') {
                            echo "<img src='uploads/noticias/" . $result['cni_foto'] . "' style='max-width:400px;'> ";
                        }
                        echo " &nbsp; 
                                <p><label>Alterar Imagem:</label> <input type='file' name='cni_foto[imagem]' id='cni_foto'>
                                <p><label>Título da Imagem:</label> <input name='cni_titulo' id='cni_titulo' value='" . $result['cni_titulo'] . "' placeholder='Título da Imagem'>
                                <p><label>Meta Tag Descrição:</label> <input name='nt_meta_description' id='nt_meta_description' value='" . $result['nt_meta_description'] . "' placeholder='Meta Tag Descrição'>
                                <p><label>Meta Tag Título:</label> <input name='nt_meta_titulo' id='nt_meta_titulo' value='" . $result['nt_meta_titulo'] . "' placeholder='Meta Tag Título'>
                                <div class='bloco'>
                                    <p><label>Data e Hora*:</label>
                                    <input type='text' name='nt_data' id='nt_data' value='" . date_format($data,'d/m/Y') . "'> 
                                    <input type='time' name='nt_hora' id='nt_hora' value='" . $result['nt_hora'] . "'>
                                </div>

                                <p><label>Status:</label>";
                        if ($result['nt_status'] == 1) {
                            echo "<input type='radio' name='nt_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type='radio' name='nt_status' value='0'> Inativo
                                        ";
                        } else {
                            echo "<input type='radio' name='nt_status' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type='radio' name='nt_status' value='0' checked> Inativo
                                        ";
                        }
                        echo "
                        </div>
						<br>
						<center>
						<div id='erro' align='center'>&nbsp;</div>
						<input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
						<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_noticias/view'; value='Cancelar'/></center>
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

<script>
    $("#nt_categoria").change(function() {
        var valor = $(this).val();
        if (valor == 'nova_categoria') {
            $('#cat').css('display', 'block');
        } else {
            $('#cat').css('display', 'none');

        }

    });

    $("#cn_nome").change(function() {
        var valor = $(this).val();
        $.post("../carrega_conteudo.php", {
                pagina: 'cadastra_categoria',
                categoria: valor
            },
            function(dados) {
                if (dados != '') {
                    $('#cat').css('display', 'none');

                    $("#nt_categoria").append('<option value="' + dados + '" selected>' + valor + '</option>')
                }
            }
        )
    });
</script>