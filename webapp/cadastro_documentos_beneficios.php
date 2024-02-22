<?php
$pagina_link = 'cadastro_documentos_beneficios';
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start(); 
$page = "<a href='cadastro_documentos_beneficios/view'>Documentos para benefícios</a>"; 

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
                    if (isset($_GET['bnd_id'])) {
                        $bnd_id = $_GET['bnd_id'];
                    }
                    if ($bnd_id == '') {
                        $bnd_id = $_POST['bnd_id'];
                    }

                    $bnd_titulo         = $_POST['bnd_titulo'];
                    $bnd_url            = geradorTags($_POST['bnd_titulo']);
                    $bnd_descricao      = $_POST['bnd_descricao']; 
                    $bnd_data           = $_POST['bnd_data']; 
                    $dados = array(
                        'bnd_titulo'            => $bnd_titulo,
                        'bnd_descricao'         => $bnd_descricao,
                        'bnd_url'               => $bnd_url,
                        'bnd_data'              => $bnd_data,
                        'bnd_usuario'           => $_SESSION['usuario_id'],
                    );
                    if($action == "adicionar")
                    {
                        $sql = "INSERT INTO cadastro_documentos_beneficios SET " . bindFields($dados);
                        $stmt = $PDO->prepare($sql);

                        if($stmt->execute($dados))
                        {	
                            $bnd_id = $PDO->lastInsertId();
                            //UPLOAD ARQUIVOS        
                            $caminho = "uploads/documentos_beneficios/";
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
                                        $bnd_documento    = $caminho;
                                        $bnd_documento .= "documento_beneficio_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                        move_uploaded_file($nomeTemporario, ($bnd_documento));
                                        $imnfo = getimagesize($bnd_documento);
                                        $sql = "UPDATE cadastro_documentos_beneficios SET 
                                            bnd_documento 	 = :bnd_documento
                                            WHERE bnd_id = :bnd_id ";
                                        $stmt = $PDO->prepare($sql);
                                        $stmt->bindParam(':bnd_documento', $bnd_documento);
                                        $stmt->bindParam(':bnd_id', $bnd_id);
        
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
                        $sql = "UPDATE cadastro_documentos_beneficios SET " . bindFields($dados) . " WHERE bnd_id = :bnd_id ";
                        $stmt = $PDO->prepare($sql);
                        $dados['bnd_id'] =  $bnd_id;
                        if ($stmt->execute($dados))
                        {
                            //UPLOAD ARQUIVOS
                            $caminho = "uploads/documentos_beneficios/";
                            foreach ($_FILES as $key => $files) {
                                $files_test = array_filter($files['name']);
                                if (!empty($files_test)) {
                                    if (!file_exists($caminho)) {
                                        mkdir($caminho, 0755, true);
                                    }
                                    if (!empty($files["name"]["documento"])) {
                                        # EXCLUI ANEXO ANTIGO #
                                        $sql = "SELECT * FROM cadastro_documentos_beneficios WHERE bnd_id = :bnd_id";
                                        $stmt_antigo = $PDO->prepare($sql);
                                        $stmt_antigo->bindParam(':bnd_id', $bnd_id);
                                        $stmt_antigo->execute();
                                        $result_antigo = $stmt_antigo->fetch();
                                        $documento_antigo = $result_antigo['bnd_documento'];
                                        unlink($documento_antigo);

                                        $nomeArquivo     = $files["name"]["documento"];
                                        $nomeTemporario = $files["tmp_name"]["documento"];
                                        $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                        $bnd_documento    = $caminho;
                                        $bnd_documento .= "documento_beneficio_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                        move_uploaded_file($nomeTemporario, ($bnd_documento));
                                        $imnfo = getimagesize($bnd_documento);
                                        $sql = "UPDATE cadastro_documentos_beneficios SET 
                                            bnd_documento 	 = :bnd_documento
                                            WHERE bnd_id = :bnd_id ";
                                        $stmt = $PDO->prepare($sql);
                                        $stmt->bindParam(':bnd_documento', $bnd_documento);
                                        $stmt->bindParam(':bnd_id', $bnd_id);
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
                        // PEGA CAMINHO DO ARQUIVO PARA FAZER EXCLUSÃO
                        $sql = "SELECT * FROM cadastro_documentos_beneficios WHERE bnd_id = :bnd_id ";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':bnd_id', $bnd_id);
                        if($stmt->execute())
                        {
                            $result = $stmt->fetch();
                            $arquivo = $result['bnd_documento'];
                        }

                        $sql = "DELETE FROM cadastro_documentos_beneficios WHERE bnd_id = :bnd_id ";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':bnd_id', $bnd_id);
                        if($stmt->execute())
                        {
                            // EXCLUI ARQUIVO DO FTP
                            unlink($arquivo);
                            
                            
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
                    $num_por_pagina = 10;
                    if(!$pag){$primeiro_registro = 0; $pag = 1;}
                    else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
                     
                    $sql = "SELECT * FROM cadastro_documentos_beneficios 
                            ORDER BY bnd_descricao ASC
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
                                <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"cadastro_documentos_beneficios/add\");'><i class='fas fa-plus'></i></div>
                            </div>
                            ";
                            if ($rows > 0)
                            {
                                echo "
                                <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                    <tr>
                                        <td class='titulo_first'>Titulo</td>
                                        <td class='titulo_tabela'>Data</td>
                                        <td class='titulo_last' align='right'>Gerenciar</td>
                                    </tr>";
                                    $c=0;
                                    while($result = $stmt->fetch())
                                    {
                                        $bnd_titulo 	= $result['bnd_titulo'];
                                        $bnd_data 	    = implode("/", array_reverse(explode("-", $result['bnd_data'])));
                                        $bnd_id 	    = $result['bnd_id'];
                                        
                                        if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                        echo "<tr class='$c1'>
                                                <td>$bnd_titulo</td>
                                                <td>$bnd_data</td>
                                                <td align=center>
                                                    <div class='g_excluir' onclick=\"
                                                            abreMask(
                                                                'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'cadastro_documentos_beneficios/view/excluir/$bnd_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                            \">	<i class='far fa-trash-alt'></i>
                                                    </div>
                                                    <div class='g_editar'  title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"cadastro_documentos_beneficios/edit/$bnd_id\");'><i class='fas fa-pencil-alt'></i></div>											
                                                </td>
                                            </tr>";
                                    }
                                    echo "</table>";
                                    $variavel = "&pagina=cadastro_documentos_beneficios&bnd_id=$bnd_id".$autenticacao."";
                                    $cnt = "SELECT COUNT(*) FROM cadastro_documentos_beneficios
                                            WHERE bnd_id = :bnd_id ";   
                                    $stmt = $PDO->prepare($cnt);
                                    $stmt->bindParam(':bnd_id', $bnd_id);
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
                        <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_documentos_beneficios/view/adicionar'>
                            <div class='titulo'> $page &raquo; Adicionar  </div>
                            <ul class='nav nav-tabs'>
                                <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            </ul>
                            <div class='tab-content'>
                                <div id='dados_gerais' class='tab-pane fade in active'>
                                    <label>Título:</label> <input name='bnd_titulo' id='bnd_titulo' placeholder='Título' class='obg'>
                                    <p><label>Descrição*:</label> <div class='textarea'><textarea  name='bnd_descricao' id='bnd_descricao' placeholder='Descrição'></textarea></div>
                                    <p><label>Data:</label> <input name='bnd_data' id='bnd_data' placeholder='Data' class='obg'>                                   
                                    <p><label>Documento:</label> <input type='file' name='bnd_documento[documento]' id='bnd_documento'>
                                </div>
                                <center>
                                <div id='erro' align='center'>&nbsp;</div>
                                <input type='submit' id='bt_cadastro_documentos_beneficios' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_documentos_beneficios/view'; value='Cancelar'/></center>
                                </center>
                            </div>
                        </form>
                        ";
                    }
                    
                    if($pagina == 'edit')
                    {
                        $sql = "SELECT * FROM cadastro_documentos_beneficios WHERE bnd_id = :bnd_id";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':bnd_id', $bnd_id);
                        $stmt->execute();
                        $rows = $stmt->rowCount();    	
                        if($rows > 0)
                        {
                            $result = $stmt->fetch();
                           
                            echo "
                            <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_documentos_beneficios/view/editar/$bnd_id'>
                                <div class='titulo'> $page &raquo; Editar </div>
                                <ul class='nav nav-tabs'>
                                    <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                                </ul>
                                <div class='tab-content'>
                                    <div id='dados_gerais' class='tab-pane fade in active'>
                                        <label>Título:</label> <input name='bnd_titulo' id='bnd_titulo' value='".$result['bnd_titulo']."' placeholder='Título' class='obg'>
                                        <p><label>Descrição*:</label> <div class='textarea'><textarea  name='bnd_descricao' id='bnd_descricao'>".$result['bnd_descricao']."</textarea></div>
                                        <p><label>Data:</label> <input name='bnd_data' id='bnd_data' value='". implode("/", array_reverse(explode("-", $result['bnd_data']))) ."' class='obg'>                                   
                                        <p><label>Documento:</label> <input type='file' name='bnd_documento[documento]' id='bnd_documento' value='".$result['bnd_documento']."'>
                                    </div>
                                    <center>
                                    <div id='erro' align='center'>&nbsp;</div>
                                    <input type='submit' id='bt_cadastro_documentos_beneficios' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                    <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_documentos_beneficios/view'; value='Cancelar'/></center>
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

