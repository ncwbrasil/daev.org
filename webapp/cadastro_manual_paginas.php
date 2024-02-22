<?php
$pagina_link = 'cadastro_manual';
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start(); 
$page = "<a href='cadastro_manual_paginas/view'>Páginas do Manual</a>";

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
                    if(isset($_GET['man_id'])){$man_id = $_GET['man_id'];}
                    if(isset($_GET['mp_id'])){$mp_id = $_GET['mp_id'];}

                    $mp_titulo       = $_POST['mp_titulo'];
                    $mp_manual       = $man_id;
                    $mp_url          = geradorTags($mp_titulo);
                    $mp_descricao    = $_POST['mp_descricao'];                    

                    $dados = array(
                        'mp_titulo'     => $mp_titulo,
                        'mp_url'        => $mp_url,
                        'mp_manual'     => $mp_manual, 
                        'mp_descricao'  => $mp_descricao, 
                        'mp_usuario'    => $_SESSION['usuario_id'],
                    );
    
                    if ($action == "adicionar") {
                        $sql = "INSERT INTO cadastro_manual_paginas SET " . bindFields($dados);
                        $stmt = $PDO->prepare($sql);
                        if ($stmt->execute($dados)) {
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

                        $sql = "UPDATE cadastro_manual_paginas SET " . bindFields($dados) . " WHERE mp_id = :mp_id ";
                        $stmt = $PDO->prepare($sql);
                        $dados['mp_id'] =  $mp_id;
                        if ($stmt->execute($dados)) {
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
                        $sql = "DELETE FROM cadastro_manual_paginas WHERE mp_id = :mp_id";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':mp_id', $mp_id);
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
                            
                    $num_por_pagina = 20;
                    if(!$pag){$primeiro_registro = 0; $pag = 1;}
                    else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
                    $sql = "SELECT * FROM cadastro_manual_paginas 
                            LEFT JOIN cadastro_manual ON cadastro_manual.man_id = cadastro_manual_paginas.mp_manual
                            WHERE man_id = :man_id
                            ORDER BY mp_titulo ASC
                            LIMIT :primeiro_registro, :num_por_pagina ";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam(':man_id', 			$man_id);
                    $stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
                    $stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
                    $stmt->execute();
                    $rows = $stmt->rowCount();
                    if($pagina == "view")
                    {
                        echo "
                            <div class='titulo'> $page  </div>
                            <div id='botoes'>
                                <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"cadastro_manual_paginas/$man_id/add\");'><i class='fas fa-plus'></i></div>
                            </div>
                            ";
                            if ($rows > 0)
                            {
                                echo "
                                <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                    <tr>
                                        <td class='titulo_first'>Nome</td>
                                        <td class='titulo_tabela' align='center'></td>
                                        <td class='titulo_last' align='right'>Gerenciar</td>
                                    </tr>";
                                    $c=0;
                                    while($result = $stmt->fetch())
                                    {
                                        $mp_id 	    = $result['mp_id'];
                                        $mp_titulo 	= $result['mp_titulo'];

                                        if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                        echo "<tr class='$c1'>
                                                <td>$mp_titulo</td>
                                                <td></td>
                                                <td align=center>
                                                    <div class='g_excluir' onclick=\"
                                                            abreMask(
                                                                'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'cadastro_manual_paginas/$man_id/view/excluir/$mp_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                            \">	<i class='far fa-trash-alt'></i>
                                                    </div>
                                                    <div class='g_editar'  title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"cadastro_manual_paginas/$man_id/edit/$mp_id\");'><i class='fas fa-pencil-alt'></i></div>											
                                                </td>
                                            </tr>";
                                    }
                                    echo "</table>";
                                    $variavel = "&pagina=cadastro_manual_paginas&mp_id=$mp_id".$autenticacao."";
                                    $cnt = "SELECT COUNT(*) FROM cadastro_manual
                                            LEFT JOIN cadastro_manual_paginas ON cadastro_manual_paginas.mp_manual = cadastro_manual.man_id 
                                            WHERE man_id = :man_id ";   
                                    $stmt = $PDO->prepare($cnt);
                                    $stmt->bindParam(':man_id', $man_id);
                                    include("../core/mod_includes/php/paginacao.php");
                            }
                            else
                            {
                                echo "<br><br><br><br><br>Não há nenhuma página cadastrada.";
                            }
                    }

                    if($pagina == 'add')
                    {
                        $sql = "SELECT * FROM cadastro_manual_paginas 
                            LEFT JOIN cadastro_manual ON cadastro_manual.man_id = cadastro_manual_paginas.mp_manual
                            WHERE man_id = :man_id
                            ORDER BY mp_id DESC";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':man_id', $man_id);
                        $stmt->execute();
                        $result = $stmt->fetch(); 
                        echo "	
                        <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_manual_paginas/$man_id/view/adicionar'>
                            <div class='titulo'> $page &raquo; Adicionar  </div>
                            <ul class='nav nav-tabs'>
                                <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            </ul>
                            <div class='tab-content'>
                                <div id='dados_gerais' class='tab-pane fade in active'>
                                    <p><label>Titulo:</label> <input name='mp_titulo' id='mp_titulo' placeholder='Titulo' class='obg'>
                                    <p><label>Descrição*:</label> <div class='textarea'><textarea  name='mp_descricao' id='mp_descricao' placeholder='Descrição'></textarea></div>
                                </div>
                                <center>
                                <div id='erro' align='center'>&nbsp;</div>
                                <input type='submit' id='bt_cadastro_manual_paginas' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_manual_paginas/$man_id/view'; value='Cancelar'/></center>
                                </center>
                            </div>
                        </form>
                        ";
                    }
                    
                    if($pagina == 'edit')
                    {
                        $sql = "SELECT * FROM cadastro_manual_paginas WHERE mp_id = :mp_id";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':mp_id', $mp_id);
                        $stmt->execute();
                        $rows = $stmt->rowCount();    	
                        if($rows > 0)
                        {
                            $result = $stmt->fetch();
                            $mp_titulo = $result['mp_titulo'];
                            $mp_id = $result['mp_id'];
                            $mp_descricao = $result['mp_descricao'];
                            echo "
                            <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_manual_paginas/$man_id/view/editar/$mp_id'>
                                <div class='titulo'> $page &raquo; Editar </div>
                                <ul class='nav nav-tabs'>
                                    <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                                </ul>
                                <div class='tab-content'>
                                    <div id='dados_gerais' class='tab-pane fade in active'>
                                        <p><label>Titulo do Submenu:</label> <input name='mp_titulo' id='mp_titulo' value='$mp_titulo' placeholder='Titulo do Submenu' class='obg'>
                                        <p><label>Descrição*:</label> <div class='textarea'><textarea  name='mp_descricao' id='mp_descricao' placeholder='Descrição'>$mp_descricao</textarea></div>
                                    </div>
                                    <center>
                                    <div id='erro' align='center'>&nbsp;</div>
                                    <input type='submit' id='bt_cadastro_manual_paginas' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                    <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_manual_paginas/$man_id/view'; value='Cancelar'/></center>
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