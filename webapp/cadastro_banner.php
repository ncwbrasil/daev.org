<?php
$pagina_link = 'cadastro_banner';
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start(); 
$page = "<a href='cadastro_banner/view'>Banners</a>"; 

?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include_once("header.php")?>
</head>
<body>
	<main class="cd-main-content">
        <div class="container">
            <?php include('../core/mod_menu/menu/menu.php')?>
			<div class="wrapper">
                <div class='mensagem'></div>
                <?php
                    if (isset($_GET['bn_id'])) {
                        $bn_id = $_GET['bn_id'];
                    }
                    if ($bn_id == '') {
                        $bn_id = $_POST['bn_id'];
                    }

                    $bn_titulo         = $_POST['bn_titulo'];
                    $bn_url            = $_POST['bn_url'];
                    $bn_posicao        = $_POST['bn_posicao']; 
                    $bn_status         = $_POST['bn_status']; 
                    $dados = array(
                        'bn_titulo'            => $bn_titulo,
                        'bn_posicao'           => $bn_posicao,
                        'bn_status'            => $bn_status,
                        'bn_url'               => $bn_url,
                        'bn_usuario'           => $_SESSION['usuario_id'],
                    );
                    if($action == "adicionar")
                    {
                        $sql = "INSERT INTO cadastro_banner SET " . bindFields($dados);
                        $stmt = $PDO->prepare($sql);
                        if($stmt->execute($dados))
                        {	
                            $bn_id = $PDO->lastInsertId();
                            //UPLOAD ARQUIVOS        
                            $caminho = "uploads/banners/";
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
                                        $bn_imagem    = $caminho;
                                        $bn_imagem .= "banners_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                        move_uploaded_file($nomeTemporario, ($bn_imagem));
                                        $imnfo = getimagesize($bn_imagem);
                                        $sql = "UPDATE cadastro_banner SET 
                                            bn_imagem 	 = :bn_imagem
                                            WHERE bn_id = :bn_id ";
                                        $stmt = $PDO->prepare($sql);
                                        $stmt->bindParam(':bn_imagem', $bn_imagem);
                                        $stmt->bindParam(':bn_id', $bn_id);
        
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
                                mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucessso!");
                            </script>
                            <?php
                        }
                        else
                        {
                            echo $err = $stmt->errorInfo();
                            ?>
                            <script>
                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                            </script>
                            <?php 
                        }	
                    }
                    
                    if($action == 'editar')
                    {
                        $sql = "UPDATE cadastro_banner SET " . bindFields($dados) . " WHERE bn_id = :bn_id ";
                        $stmt = $PDO->prepare($sql);
                        $dados['bn_id'] =  $bn_id;
                        if ($stmt->execute($dados))
                        {
                            //UPLOAD ARQUIVOS
                            $caminho = "uploads/banners/";
                            foreach ($_FILES as $key => $files) {
                                $files_test = array_filter($files['name']);
                                if (!empty($files_test)) {
                                    if (!file_exists($caminho)) {
                                        mkdir($caminho, 0755, true);
                                    }
                                    if (!empty($files["name"]["documento"])) {
                                        # EXCLUI ANEXO ANTIGO #
                                        $sql = "SELECT * FROM cadastro_banner WHERE bn_id = :bn_id";
                                        $stmt_antigo = $PDO->prepare($sql);
                                        $stmt_antigo->bindParam(':bn_id', $bn_id);
                                        $stmt_antigo->execute();
                                        $result_antigo = $stmt_antigo->fetch();
                                        $documento_antigo = $result_antigo['bn_imagem'];
                                        unlink($documento_antigo);

                                        $nomeArquivo     = $files["name"]["documento"];
                                        $nomeTemporario = $files["tmp_name"]["documento"];
                                        $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                        $bn_imagem    = $caminho;
                                        $bn_imagem .= "banners_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                        move_uploaded_file($nomeTemporario, ($bn_imagem));
                                        $imnfo = getimagesize($bn_imagem);
                                        $sql = "UPDATE cadastro_banner SET 
                                            bn_imagem 	 = :bn_imagem
                                            WHERE bn_id = :bn_id ";
                                        $stmt = $PDO->prepare($sql);
                                        $stmt->bindParam(':bn_imagem', $bn_imagem);
                                        $stmt->bindParam(':bn_id', $bn_id);
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
                                mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                            </script>
                            <?php
                        }
                        else
                        {
                            ?>
                            <script>
                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                            </script>
                            <?php
                        }
                    }
                    
                    if($action == 'excluir')
                    {
                        $sql = "DELETE FROM cadastro_banner WHERE bn_id = :bn_id ";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':bn_id', $bn_id);
                        if($stmt->execute())
                        {
                            ?>
                            <script>
                                mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                            </script>
                            <?php
                        }
                        else
                        {
                            ?>
                            <script>
                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                            </script>
                            <?php
                        }
                    }
                    $num_por_pagina = 25;
                    if(!$pag){$primeiro_registro = 0; $pag = 1;}
                    else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
                     
                    $sql = "SELECT * FROM cadastro_banner 
                            ORDER BY bn_posicao ASC
                            LIMIT :primeiro_registro, :num_por_pagina ";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
                    $stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
                    $stmt->execute();
                    $rows = $stmt->rowCount();
                    if($pagina == "view")
                    {
                        echo "
                            <div class='titulo'> $page  </div>
                            <div id='botoes'>
                                <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"cadastro_banner/add\");'><i class='fas fa-plus'></i></div>
                            </div>
                            ";
                            if ($rows > 0)
                            {
                                echo "
                                <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                    <tr>
                                        <td class='titulo_first'>Imagem</td>
                                        <td class='titulo_tabela'>Titulo</td>
                                        <td class='titulo_tabela'>Posição</td>
                                        <td class='titulo_tabela'>Link</td>
                                        <td class='titulo_last' align='right'>Gerenciar</td>
                                    </tr>";
                                    $c=0;
                                    while($result = $stmt->fetch())
                                    {
                                        $bn_titulo 	= $result['bn_titulo'];
                                        $bn_posicao 	= $result['bn_posicao'];
                                        $bn_url 	    = $result['bn_url'];
                                        $bn_imagem	    = $result['bn_imagem'];
                                        $bn_id 	    = $result['bn_id'];
                                        
                                        if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                        echo "<tr class='$c1'>
                                                <td><img src='$bn_imagem' width='150px'></td>
                                                <td>$bn_titulo</td>
                                                <td>$bn_posicao</td>
                                                <td>$bn_url</td>
                                                <td align=center>
                                                    <div class='g_excluir' onclick=\"
                                                            abreMask(
                                                                'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'cadastro_banner/view/excluir/$bn_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                            \">	<i class='far fa-trash-alt'></i>
                                                    </div>
                                                    <div class='g_editar'  title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"cadastro_banner/edit/$bn_id\");'><i class='fas fa-pencil-alt'></i></div>											
                                                </td>
                                            </tr>";
                                    }
                                    echo "</table>";
                                    $variavel = "&pagina=cadastro_banner&bn_id=$bn_id".$autenticacao."";
                                    $cnt = "SELECT COUNT(*) FROM cadastro_banner
                                            WHERE bn_id = :bn_id ";   
                                    $stmt = $PDO->prepare($cnt);
                                    $stmt->bindParam(':bn_id', $bn_id);
                                    include("../core/mod_includes/php/paginacao.php");
                            }
                            else
                            {
                                echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                            }
                    }

                    if($pagina == 'add')
                    {
                        echo "	
                        <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_banner/view/adicionar'>
                            <div class='titulo'> $page &raquo; Adicionar  </div>
                            <ul class='nav nav-tabs'>
                                <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            </ul>
                            <div class='tab-content'>
                                <div id='dados_gerais' class='tab-pane fade in active'>
                                    <label>Título:</label> <input name='bn_titulo' id='bn_titulo' placeholder='Título' class='obg'>
                                    <p><label>Posição:</label> <input name='bn_posicao' id='bn_posicao' placeholder='Posição' class='obg'>
                                    <p><label>URL:</label> <input name='bn_url' id='bn_url' placeholder='URL' >
                                    <p><label>Imagem:</label> <input type='file' name='bn_imagem[documento]' id='bn_imagem' class='obg'> <br>
                                    <center><i style='font-size:14px'>*Recomendado usar imagens de 1920px de largura por 600px de altura, evite imagens quadradas. </i></center>

                                    <p><label>Status:</label> <input type='radio' name='bn_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type='radio' name='bn_status' value='0'> Inativo<br>		
            

                                </div>
                                <center>
                                <div id='erro' align='center'>&nbsp;</div>
                                <input type='submit' id='bt_cadastro_banner' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_banner/view'; value='Cancelar'/></center>
                                </center>
                            </div>
                        </form>
                        ";
                    }
                    
                    if($pagina == 'edit')
                    {
                        $sql = "SELECT * FROM cadastro_banner WHERE bn_id = :bn_id";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':bn_id', $bn_id);
                        $stmt->execute();
                        $rows = $stmt->rowCount();    	
                        if($rows > 0)
                        {
                            $result = $stmt->fetch();
                           
                            echo "
                            <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_banner/view/editar/$bn_id'>
                                <div class='titulo'> $page &raquo; Editar </div>
                                <ul class='nav nav-tabs'>
                                    <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                                </ul>
                                <div class='tab-content'>
                                    <div id='dados_gerais' class='tab-pane fade in active'>
                                        <label>Título:</label> <input name='bn_titulo' id='bn_titulo' value='".$result['bn_titulo']."' placeholder='Título' class='obg'>
                                        <p><label>Posição:</label> <input name='bn_posicao' id='bn_posicao' placeholder='Posição' value='".$result['bn_posicao']."' class='obg'>
                                        <p><label>URL:</label> <input name='bn_url' id='bn_url' placeholder='URL' value='".$result['bn_url']."'>

                                        <p><label>Imagem:</label> ";
                                        if ($result['bn_imagem'] != '') {
                                            echo "<img src='" . $result['bn_imagem'] . "' style='max-width:400px;'> ";
                                        }
                                        echo " &nbsp; 
                                            <p><label>Alterar Imagem:</label> <input type='file' name='bn_imagem[documento]' id='bn_imagem'>
                                            <center><i style='font-size:14px'>*Recomendado usar imagens de 1920px de largura por 600px de altura, evite imagens quadradas. </i></center>
                                        <p><label>Status:</label>";
                                        if ($result['bn_status'] == 1) {
                                            echo "<input type='radio' name='bn_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <input type='radio' name='bn_status' value='0'> Inativo
                                                    ";
                                        } else {
                                            echo "<input type='radio' name='bn_status' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <input type='radio' name='bn_status' value='0' checked> Inativo
                                                    ";
                                        }
                                        echo "
                
                                    </div>
                                    <center>
                                    <div id='erro' align='center'>&nbsp;</div>
                                    <input type='submit' id='bt_cadastro_banner' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                    <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_banner/view'; value='Cancelar'/></center>
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