<?php
$pagina_link = 'cadastro_menu';
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start(); 
$page = "<a href='cadastro_submenu/view'>Submenu</a>";

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
                        $page = "<a href='cadastro_menu/view'>Links das Páginas </a> &raquo; <a href='cadastro_submenu/view'>Submenu</a>";

                    if(isset($_GET['men_id'])){$men_id = $_GET['men_id'];}
                    if(isset($_GET['sm_id'])){$sm_id = $_GET['sm_id'];}

                    $sm_titulo              = $_POST['sm_titulo'];
                    $sm_topo                = $_POST['sm_topo'];
                    $sm_posicao             = $_POST['sm_posicao'];
                    $sm_menu                = $men_id;
                    $sm_link                = $_POST['sm_link'];
                    $sm_home                = $_POST['sm_home'];
                    $sm_posicao_home        = $_POST['sm_posicao_home'];
                    $sm_destaque_home       = $_POST['sm_destaque_home'];
                    $sm_servico             = $_POST['sm_servico'];
                    $sm_posicao_servico     = $_POST['sm_posicao_servico'];
                    $sm_icone               = $_POST['sm_icone'];
                    $sm_link_rapido         = $_POST['sm_link_rapido'];

                    if($sm_link==''){
                        $sm_link = geradorTags($sm_titulo);
                        $sm_compartilhamento = 'Null';
                    } 
                    else{

                        $link = substr($sm_link, 0,4);
                        if($link == "http" || $link == 'HTTP' || $link == 'Http'){
                            $sm_compartilhamento = geradorTags($sm_titulo);
                        }else{
                            $sm_compartilhamento = 'Null';
                            $sm_link = geradorTags($sm_titulo);
                        }
                    } 

                    if ($sm_servico == 0){
                        $sm_posicao_servico = Null; 
                        $sm_icone = Null; 
                    }

                    $dados = array(
                        'sm_titulo'             => $sm_titulo,
                        'sm_topo'               => $sm_topo,
                        'sm_posicao'            => $sm_posicao,
                        'sm_menu'               => $sm_menu, 
                        'sm_link'               => $sm_link, 
                        'sm_compartilhamento'   => $sm_compartilhamento, 
                        'sm_home'               => $sm_home, 
                        'sm_posicao_home'       => $sm_posicao_home, 
                        'sm_servico'            => $sm_servico, 
                        'sm_posicao_servico'    => $sm_posicao_servico,
                        'sm_icone'              => $sm_icone,
                        'sm_destaque_home'      => $sm_destaque_home, 
                        'sm_link_rapido'        => $sm_link_rapido, 
                    );
    
                    if ($action == "adicionar") {
                        $sql = "INSERT INTO aux_submenu SET " . bindFields($dados);
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

                        $sql = "UPDATE aux_submenu SET " . bindFields($dados) . " WHERE sm_id = :sm_id ";
                        $stmt = $PDO->prepare($sql);
                        $dados['sm_id'] =  $sm_id;
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
                        $sql = "DELETE FROM aux_submenu WHERE sm_id = :sm_id";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':sm_id', $sm_id);
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
                        $sql = "UPDATE aux_submenu SET sm_topo = :sm_topo WHERE sm_id = :sm_id ";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindValue(':sm_topo', 1);
                        $stmt->bindParam(':sm_id', $sm_id);
                        $stmt->execute();
                    }
    
                    if ($action == 'desativar') {
                        $sql = "UPDATE aux_submenu SET sm_topo = :sm_topo WHERE sm_id = :sm_id ";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindValue(':sm_topo', 0);
                        $stmt->bindParam(':sm_id', $sm_id);
                        $stmt->execute();
                    }
                        
                    $num_por_pagina = 20;
                    if(!$pag){$primeiro_registro = 0; $pag = 1;}
                    else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
                    $sql = "SELECT * FROM aux_submenu 
                            LEFT JOIN aux_menu ON aux_menu.men_id = aux_submenu.sm_menu
                            WHERE men_id = :men_id
                            ORDER BY sm_titulo ASC
                            LIMIT :primeiro_registro, :num_por_pagina ";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam(':men_id', 			$men_id);
                    $stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
                    $stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
                    $stmt->execute();
                    $rows = $stmt->rowCount();
                    if($pagina == "view")
                    {
                        echo "
                            <div class='titulo'> $page  </div>
                            <div id='botoes'>
                                <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"cadastro_submenu/$men_id/add\");'><i class='fas fa-plus'></i></div>
                            </div>
                            ";
                            if ($rows > 0)
                            {
                                echo "
                                <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                    <tr>
                                        <td class='titulo_first'>Nome</td>
                                        <td class='titulo_first'>Link</td>
                                        <td class='titulo_first'>Posição</td>
                                        <td class='titulo_last' align='right'>Gerenciar</td>
                                    </tr>";
                                    $c=0;
                                    while($result = $stmt->fetch())
                                    {
                                        $sm_id 	    = $result['sm_id'];
                                        $sm_titulo 	= $result['sm_titulo'];
                                        $sm_posicao = $result['sm_posicao'];
                                        $sm_link 	= $result['sm_link'];

                                        if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                        echo "<tr class='$c1'>
                                                <td>$sm_titulo</td>
                                                <td>$sm_link</td>
                                                <td>$sm_posicao</td>
                                                <td align=center>
                                                    <div class='g_excluir' onclick=\"
                                                            abreMask(
                                                                'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'cadastro_submenu/$men_id/view/excluir/$sm_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                            \">	<i class='far fa-trash-alt'></i>
                                                    </div>
                                                    <div class='g_editar'  title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"cadastro_submenu/$men_id/edit/$sm_id\");'><i class='fas fa-pencil-alt'></i></div>											
                                                </td>
                                            </tr>";
                                    }
                                    echo "</table>";
                                    //$variavel = "&pagina=aux_submenu&men_id=$men_id".$autenticacao."";
                                    $cnt = "SELECT COUNT(*) FROM aux_menu
                                            LEFT JOIN aux_submenu ON aux_submenu.sm_menu = aux_menu.men_id 
                                            WHERE men_id = :men_id ";   
                                    $stmt = $PDO->prepare($cnt);
                                    $stmt->bindParam(':men_id', $men_id);
                                    include("../core/mod_includes/php/paginacao.php");
                            }
                            else
                            {
                                echo "<br><br><br><br><br>Não há nenhum submódulo cadastrado.";
                            }
                    }

                    if($pagina == 'add')
                    {
                        
                        $sql = "SELECT * FROM aux_submenu 
                            LEFT JOIN aux_menu ON aux_menu.men_id = aux_submenu.sm_menu
                            WHERE men_id = :men_id
                            ORDER BY sm_posicao DESC";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':men_id', 			$men_id);
                        $stmt->execute();
                        $result = $stmt->fetch(); 

                        $sm_posicao = $result['sm_posicao']+1; 
                        $sm_posicao_home = $result['sm_posicao_home']+1; 
                        $sm_posicao_servico = $result['sm_posicao_servico']+1; 

                        echo "	
                        <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_submenu/$men_id/view/adicionar'>
                            <div class='titulo'> $page &raquo; Adicionar  </div>
                            <ul class='nav nav-tabs'>
                                <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            </ul>
                            <div class='tab-content'>
                                <div id='dados_gerais' class='tab-pane fade in active'>
                                    <p><label>Titulo do Submenu:</label> <input name='sm_titulo' id='sm_titulo' placeholder='Titulo do Submenu' class='obg'>
                                    <p><label>Posição:</label> <input name='sm_posicao' id='sm_posicao' placeholder='Posição' value='$sm_posicao' class='obg'>
                                    <p><label>Link Externo:</label> <input name='sm_link' id='sm_link' placeholder='Link Externo'>
                                    <p><label>Link de Compartilhamento:</label> <input name='sm_compartilhamento' id='sm_compartilhamento' placeholder='Link de Compartilhamento' value=''>
                                    <p><label>Exibir no Topo:</label> <input type='radio' name='sm_topo' value='1' checked> Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type='radio' name='sm_topo' value='0'> Não	

                                    <p><label>Exibir na Home:</label> <input type='radio' name='sm_home' value='1' > Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type='radio' name='sm_home' value='0' checked> Não	
                                    <div id='psh' style='display:none'>
                                        <p><label>Posição:</label> <input name='sm_posicao_home' id='sm_posicao_home' placeholder='Posição' value='$sm_posicao_home'> </p>
                                        <p><label>Destaque:</label> <input type='radio' name='sm_destaque_home' value='1' > Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type='radio' name='sm_destaque_home' value='0' checked> Não
                                    </div>


                                    <p><label>Exibir como Serviço:</label> <input type='radio' name='sm_servico' value='1' > Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type='radio' name='sm_servico' value='0'checked> Não	

                                    <div id='pss' style='display:none'>
                                        <p><label>Posição:</label> <input name='sm_posicao_servico' id='sm_posicao_servico' placeholder='Posição' value='$sm_posicao_servico' > </p>
                                        <p><label>Ícone:</label> <input name='sm_icone' id='sm_icone' placeholder='Ícone'></p=>
                                    </div>

                                    <p><label>Link Rápido:</label> <input type='radio' name='sm_link_rapido' value='1' > Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type='radio' name='sm_link_rapido' value='0' checked> Não	

                                </div>
                                <center>
                                <div id='erro' align='center'>&nbsp;</div>
                                <input type='submit' id='bt_cadastro_submenu' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_submenu/$men_id/view'; value='Cancelar'/></center>
                                </center>
                            </div>
                        </form>
                        ";
                    }
                    
                    if($pagina == 'edit')
                    {
                        $sql = "SELECT * FROM aux_submenu WHERE sm_id = :sm_id";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':sm_id', $sm_id);
                        $stmt->execute();
                        $rows = $stmt->rowCount();    	
                        if($rows > 0)
                        {
                            $result = $stmt->fetch();
                            $sm_titulo = $result['sm_titulo'];
                            $sm_link = $result['sm_link'];
                            $sm_posicao = $result['sm_posicao'];
                            $sm_posicao_home = $result['sm_posicao_home'];
                            $sm_posicao_servico = $result['sm_posicao_servico'];
                            $sm_destaque_home = $result['sm_destaque_home']; 
                            $sm_link_rapido = $result['sm_link_rapido']; 

                            if($result['sm_compartilhamento'] !== NULL){
                                $sm_compartilhamento = "https://daev.org.br/router/".$result['sm_compartilhamento']; 
                            }
                            else {
                                $sm_compartilhamento ='';
                            }

                            echo "
                            <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_submenu/$men_id/view/editar/$sm_id'>
                                <div class='titulo'> $page &raquo; Editar </div>
                                <ul class='nav nav-tabs'>
                                    <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                                </ul>
                                <div class='tab-content'>
                                    <div id='dados_gerais' class='tab-pane fade in active'>
                                        <p><label>Titulo do Submenu:</label> <input name='sm_titulo' id='sm_titulo' value='$sm_titulo' placeholder='Titulo do Submenu' class='obg'>
                                        <p><label>Posição:</label> <input name='sm_posicao' id='sm_posicao' value='$sm_posicao' placeholder='Posição' class='obg'>
                                        <p><label>Link Externo:</label> <input name='sm_link' id='sm_link' value='$sm_link' placeholder='Caso precise direcionar esse link para uma página externa'>
                                        <p><label>Link de Compartilhamento:</label> <input name='sm_compartilhamento' id='sm_compartilhamento' value='$sm_compartilhamento' placeholder='Link para Compartilhamento'>
                                        <p><label>Exibir no Topo:</label>";
                                        if ($result['sm_topo'] == 1) {
                                            echo "<input type='radio' name='sm_topo' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <input type='radio' name='sm_topo' value='0'> Inativo
                                                    ";
                                        } else {
                                            echo "<input type='radio' name='sm_topo' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <input type='radio' name='sm_topo' value='0' checked> Inativo
                                                    ";
                                        }
                                        echo "

                                        <p><label>Exibir na Home:</label>";
                                        if ($result['sm_home'] == 1) {
                                            echo "<input type='radio' name='sm_home' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                  <input type='radio' name='sm_home' value='0'> Inativo
                                                  <p><label>Posição:</label> <input name='sm_posicao_home' id='sm_posicao_home' placeholder='Posição' value='$sm_posicao_home'> </p>";

                                                  if($sm_destaque_home == 1){
                                                    echo"                                                  
                                                        <p><label>Destaque:</label> <input type='radio' name='sm_destaque_home' value='1' checked> Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <input type='radio' name='sm_destaque_home' value='0'> Não
                                                    ";

                                                  }else{
                                                    echo"                                                  
                                                        <p><label>Destaque:</label> <input type='radio' name='sm_destaque_home' value='1' > Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <input type='radio' name='sm_destaque_home' value='0' checked> Não
                                                    ";
                                                  }
          
                                                  
                                        } else {
                                            echo "<input type='radio' name='sm_home' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                  <input type='radio' name='sm_home' value='0' checked> Inativo
                                                  <div id='psh' style='display:none'>
                                                    <p><label>Posição:</label> <input name='sm_posicao_home' id='sm_posicao_home' placeholder='Posição' value='$sm_posicao_home'> </p>
                                                    <p><label>Destaque:</label> <input type='radio' name='sm_destaque_home' value='1' > Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <input type='radio' name='sm_destaque_home' value='0' checked> Não
                                                   </div>";
                                        }
                                        echo "
                                        <p><label>Exibir como Serviço:</label>";
                                        if ($result['sm_servico'] == 1) {
                                            echo "<input type='radio' name='sm_servico' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                  <input type='radio' name='sm_servico' value='0'> Inativo
                                                  <p><label>Posição:</label> <input name='sm_posicao_servico' id='sm_posicao_servico' placeholder='Posição' value='$sm_posicao_servico' > </p>
                                                  <p><label>Ícone:</label> <input name='sm_icone' id='sm_icone' placeholder='Ícone' value='".$result['sm_icone']."'></p>";
                                        } else {
                                            echo "<input type='radio' name='sm_servico' value='1' > Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type='radio' name='sm_servico' value='0'checked> Não	
        
                                            <div id='pss' style='display:none'>
                                                <p><label>Posição:</label> <input name='sm_posicao_servico' id='sm_posicao_servico' placeholder='Posição' value='$sm_posicao_servico' > </p>
                                                <p><label>Ícone:</label> <input name='sm_icone' id='sm_icone' placeholder='Ícone' value=''></p>
                                            </div>";
                                        }
                                        if($sm_link_rapido == 1){
                                            echo"                                                  
                                                <p><label>Link Rápido:</label> <input type='radio' name='sm_link_rapido' value='1' checked> Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type='radio' name='sm_link_rapido' value='0'> Não
                                            ";

                                          }else{
                                            echo"                                                  
                                                <p><label>Link Rápido:</label> <input type='radio' name='sm_link_rapido' value='1' > Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type='radio' name='sm_link_rapido' value='0' checked> Não
                                            ";
                                          }

                                        echo " 
                                    </div>
                                    <center>
                                    <div id='erro' align='center'>&nbsp;</div>
                                    <input type='submit' id='bt_cadastro_submenu' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                    <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_submenu/$men_id/view'; value='Cancelar'/></center>
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
<script>
$('input[type=radio][name=sm_home]').change(function() {
    if(this.value== 1){
        $('#psh').fadeIn('fast');
    } else {
        $('#psh').fadeOut('fast');
    }
})

$('input[type=radio][name=sm_servico]').change(function() {
    if(this.value== 1){
        $('#pss').fadeIn('fast');
    } else {
        $('#pss').fadeOut('fast'); 
    }
})

$("#sm_link").change(function() {
        var valor = $('#sm_titulo').val();
        var nome2 = valor.normalize('NFD').replace(/[\u0300-\u036f]/g, '') // Remove acentos
		.replace(/([^\w]+|\s+)/g, '-') // Substitui espaço e outros caracteres por hífen
		.replace(/\-\-+/g, '-')	// Substitui multiplos hífens por um único hífen
		.replace(/(^-+|-+$)/, ''); 
        $('#sm_compartilhamento').val("https://daev.org.br/router/"+nome2); 
    });


</script>