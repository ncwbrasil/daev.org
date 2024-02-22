<?php

$pagina_link = 'cadastro_menu';

include_once("../core/mod_includes/php/connect.php");

include_once("../core/mod_includes/php/funcoes.php");

sec_session_start();

$page = "<a href='cadastro_menu/view'>Links das Páginas</a>";



?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>

<html xmlns="http://www.w3.org/1999/xhtml">



<head>

    <?php include ('header.php')?>

</head>



<body>

    <main class="cd-main-content">

        <div class="container">

            <?php include('../core/mod_menu/menu/menu.php')?>

			<div class="wrapper">

                <div class='mensagem'></div>

                <?php



                $page = " <a href='cadastro_menu/view'>Links das Páginas</a>";

                if (isset($_GET['men_id'])) {

                    $men_id = $_GET['men_id'];

                }

                if ($men_id == '') {

                    $men_id = $_POST['men_id'];

                }

                $men_titulo             = $_POST['men_titulo'];

                $men_topo               = $_POST['men_topo'];

                $men_posicao            = $_POST['men_posicao'];

                $men_link               = $_POST['men_link'];

                $men_home               = $_POST['men_home']; 

                if($men_home == 0){
                    $men_posicao_home       = 0; 

                }else {
                    $men_posicao_home       = $_POST['men_posicao_home'];
                }
                
                $men_servico            = $_POST['men_servico']; 
                
                if($men_home == 0){
                    $men_posicao_servico       = 0; 

                }else {
                    $men_posicao_servico    = $_POST['men_posicao_servico']; 
                }

                $men_icone              = $_POST['men_icone']; 

                $men_destaque_home       = $_POST['men_destaque_home'];



                if($men_link==''){

                    $men_link = geradorTags($men_titulo);

                }else {

                    $men_compartilhamento = geradorTags($men_titulo);  

                }



                $dados = array(

                    'men_titulo'            => $men_titulo,

                    'men_link'              => $men_link,

                    'men_compartilhamento'  => $men_compartilhamento, 

                    'men_topo'              => $men_topo,

                    'men_posicao'           => $men_posicao, 

                    'men_home'              => $men_home, 

                    'men_servico'           => $men_servico, 

                    'men_icone'             => $men_icone, 

                    'men_posicao_home'      => $men_posicao_home, 

                    'men_destaque_home'      => $men_destaque_home, 

                    'men_posicao_servico'   => $men_posicao_servico, 

                );



                if ($action == "adicionar") {

                    $sql = "INSERT INTO aux_menu SET " . bindFields($dados);

                    $stmt = $PDO->prepare($sql);

                    if ($stmt->execute($dados)) {

                    ?>

                        <script>

                            mensagem("Ok", "<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");

                        </script>

                    <?php



                    } else {
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



                    $sql = "UPDATE aux_menu SET " . bindFields($dados) . " WHERE men_id = :men_id ";

                    $stmt = $PDO->prepare($sql);

                    $dados['men_id'] =  $men_id;

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

                    $sql = "DELETE FROM aux_menu WHERE men_id = :men_id";

                    $stmt = $PDO->prepare($sql);

                    $stmt->bindParam(':men_id', $men_id);

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

                    $sql = "UPDATE aux_menu SET men_topo = :men_topo WHERE men_id = :men_id ";

                    $stmt = $PDO->prepare($sql);

                    $stmt->bindValue(':men_topo', 1);

                    $stmt->bindParam(':men_id', $men_id);

                    $stmt->execute();

                }



                if ($action == 'desativar') {

                    $sql = "UPDATE aux_menu SET men_topo = :men_topo WHERE men_id = :men_id ";

                    $stmt = $PDO->prepare($sql);

                    $stmt->bindValue(':men_topo', 0);

                    $stmt->bindParam(':men_id', $men_id);

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

                    $nome_query = " (men_nome LIKE :fil_nome1  ) ";

                }



                $sql = "SELECT * FROM aux_menu 

                    WHERE " . $nome_query . "

                    ORDER BY men_posicao ASC

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

                                <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_menu/view'>

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

                            <td class='titulo_tabela' align='left' colspan='2' width='1'>Menu</td>

                            <td class='titulo_tabela' align='center'>Link</td>

                            <td class='titulo_tabela' align='center'>Posição</td>

                            <td class='titulo_last' align='right'>Gerenciar</td>

                        </tr>";

                        $c = 0;

                        while ($result = $stmt->fetch()) {

                            $men_id         = $result['men_id'];

                            $men_titulo     = $result['men_titulo'];

                            $men_link       = $result['men_link'];

                            $men_posicao    = $result['men_posicao'];



                            if ($c == 0) {

                                $c1 = "linhaimpar";

                                $c = 1;

                            } else {

                                $c1 = "linhapar";

                                $c = 0;

                            }

                            echo "<tr class='$c1'>

                                    <td>$men_titulo</td>

                                    <td></td>

                                    <td align='center'>$men_link</td>

                                    <td align='center'>$men_posicao</td>

                                    <td align=center>

                                            <div class='g_excluir' title='Excluir' onclick=\"

                                                abreMask(

                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+

                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/view/excluir/$men_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+

                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');

                                                \">	<i class='far fa-trash-alt'></i>

                                            </div>

                                            <div class='g_editar' title='Editar' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"" . $pagina_link . "/edit/$men_id\");'><i class='fas fa-pencil-alt'></i></div>

                                            <div class='g_status' title='Submenus' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"cadastro_submenu/$men_id/view\");'><i class='fas fa-list-ul'></i></div>

                                    </td>

                                </tr>";

                        }

                        echo "</table>";

                        $variavel = "&fil_nome=$fil_nome";

                        $cnt = "SELECT COUNT(*) FROM aux_menu WHERE " . $nome_query . "  ";

                        $stmt = $PDO->prepare($cnt);

                        $stmt->bindParam(':fil_nome1', $fil_nome1);

                        include("../core/mod_includes/php/paginacao.php");

                    } else {

                        echo "<br><br><br>Não há nenhum menu cadastrado.";

                    }

                }



                if ($pagina == 'add') {

                    echo "	

                        <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_menu/view/adicionar'>

                            <div class='titulo'> $page &raquo; Adicionar  </div>

                            <ul class='nav nav-tabs'>

                                <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>

                            </ul>

                            <div class='tab-content'>

                                <div id='dados_gerais' class='tab-pane fade in active'>

                                    <p><label>Título*:</label> <input name='men_titulo' id='men_titulo' placeholder='Título' class='obg' >

                                    <p><label>Posição*:</label> <input name='men_posicao' id='men_posicao' placeholder='Posição' class='obg' >

                                    <p><label>Link Externo:</label> <input name='men_link' id='men_link' placeholder='Caso precise direcionar esse link para uma página externa'>

                                    <p><label>Link de Compartilhamento:</label> <input name='men_compartilhamento' id='men_compartilhamento' placeholder='Link de Compartilhamento'>

                                    <p><label>Exibir no topo:</label> <input type='radio' name='men_topo' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                    <input type='radio' name='men_topo' value='0'> Inativo



                                    <p><label>Exibir na Home:</label> <input type='radio' name='men_home' value='1' > Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                    <input type='radio' name='men_home' value='0' checked> Não	

                                    <div id='psh' style='display:none'>

                                        <p><label>Posição:</label> <input name='men_posicao_home' id='men_posicao_home' placeholder='Posição' value='$men_posicao_home'> </p>

                                        <p><label>Destaque:</label> <input type='radio' name='men_destaque_home' value='1' > Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                        <input type='radio' name='men_destaque_home' value='0' checked> Não

                                    </div>



                                    <p><label>Exibir como Serviço:</label> <input type='radio' name='men_servico' value='1' > Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                    <input type='radio' name='men_servico' value='0'checked> Não	



                                    <div id='pss' style='display:none'>

                                        <p><label>Posição:</label> <input name='men_posicao_servico' id='men_posicao_servico' placeholder='Posição' value='$men_posicao_servico' > </p>

                                        <p><label>Ícone:</label> <input name='men_icone' id='men_icone' placeholder='Ícone'></p=>

                                    </div>



                                </div>

                            </div>

                            <br>

                            <center>

                            <div id='erro' align='center'>&nbsp;</div>

                            <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 

                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_menu/view'; value='Cancelar'/></center>

                            </center>

                        </form>

                    ";

                }



                if ($pagina == 'edit') {

                    $sql = "SELECT * FROM aux_menu 

                        WHERE men_id = :men_id";

                    $stmt = $PDO->prepare($sql);

                    $stmt->bindParam(':men_id', $men_id);

                    $stmt->execute();

                    $rows = $stmt->rowCount();

                    if ($rows > 0) {

                        $result = $stmt->fetch();

                        echo "

                            <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_menu/view/editar/$men_id'>

                                <div class='titulo'> $page &raquo; Editar </div>

                                <ul class='nav nav-tabs'>

                                    <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>

                                </ul>

                                <div class='tab-content'>

                                    <div id='dados_gerais' class='tab-pane fade in active'>

                                    <p><label>Título*:</label> <input name='men_titulo' id='men_titulo' value='" . $result['men_titulo'] . "' placeholder='Título' class='obg' >

                                    <p><label>Posição*:</label> <input name='men_posicao' id='men_posicao' value='" . $result['men_posicao'] . "' placeholder='Posição' class='obg' >

                                    <p><label>Link Externo:</label> <input name='men_link' id='men_link' value='" . $result['men_link'] . "' placeholder='Caso precise direcionar esse link para uma página externa' >

                                    <p><label>Link de Compartilhamento:</label> <input name='men_compartilhamento' id='men_compartilhamento' value='" . $result['men_compartilhamento'] . "' placeholder='Link de Compartilhamento' >

                                        <p><label>Exibir no Topo:</label>";

                                        if ($result['men_topo'] == 1) {

                                            echo "<input type='radio' name='men_topo' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                                    <input type='radio' name='men_topo' value='0'> Inativo

                                                    ";

                                        } else {

                                            echo "<input type='radio' name='men_topo' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                                    <input type='radio' name='men_topo' value='0' checked> Inativo

                                                    ";

                                        }

                                        echo "



                                        <p><label>Exibir na Home:</label>";

                                        if ($result['men_home'] == 1) {

                                            echo "<input type='radio' name='men_home' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                                  <input type='radio' name='men_home' value='0'> Inativo

                                                  <p><label>Posição:</label> <input name='men_posicao_home' id='men_posicao_home' placeholder='Posição' value='".$result['men_posicao_home']."'> </p>";

                                                  if($men_destaque_home == 1){

                                                    echo"                                                  

                                                        <p><label>Destaque:</label> <input type='radio' name='men_destaque_home' value='1' checked> Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                                        <input type='radio' name='men_destaque_home' value='0'> Não

                                                    ";



                                                  }else{

                                                    echo"                                                  

                                                        <p><label>Destaque:</label> <input type='radio' name='men_destaque_home' value='1' > Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                                        <input type='radio' name='men_destaque_home' value='0' checked> Não

                                                    ";

                                                  }



                                        } else {

                                            echo "<input type='radio' name='men_home' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                                  <input type='radio' name='men_home' value='0' checked> Inativo

                                                  <div id='psh' style='display:none'>

                                                    <p><label>Posição:</label> <input name='men_posicao_home' id='men_posicao_home' placeholder='Posição' value='".$result['men_posicao_home']."'> </p>

                                                    <p><label>Destaque:</label> <input type='radio' name='men_destaque_home' value='1' > Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                                    <input type='radio' name='men_destaque_home' value='0' checked> Não



                                                   </div>";

                                        }

                                        echo "

                                        <p><label>Exibir como Serviço:</label>";

                                        if ($result['men_servico'] == 1) {

                                            echo "<input type='radio' name='men_servico' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                                  <input type='radio' name='men_servico' value='0'> Inativo

                                                  <p><label>Posição:</label> <input name='men_posicao_servico' id='men_posicao_servico' placeholder='Posição' value='".$result['men_posicao_servico']."' > </p>

                                                  <p><label>Ícone:</label> <input name='men_icone' id='men_icone' placeholder='Ícone' value='".$result['men_icone']."'></p>";

                                        } else {

                                            echo "<input type='radio' name='men_servico' value='1' > Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                            <input type='radio' name='men_servico' value='0'checked> Não	

        

                                            <div id='pss' style='display:none'>

                                                <p><label>Posição:</label> <input name='men_posicao_servico' id='men_posicao_servico' placeholder='Posição' value='".$result['men_posicao_servico']."' > </p>

                                                <p><label>Ícone:</label> <input name='men_icone' id='men_icone' placeholder='Ícone' value=''></p>

                                            </div>";

                                        }

                                        echo " 

                                    </div>

                                    <br>

                                    <center>

                                    <div id='erro' align='center'>&nbsp;</div>

                                    <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 

                                    <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_menu/view'; value='Cancelar'/></center>

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

$('input[type=radio][name=men_home]').change(function() {

    if(this.value== 1){

        $('#psh').fadeIn('fast');

    } else {

        $('#psh').fadeOut('fast');

    }

})



$('input[type=radio][name=men_servico]').change(function() {

    if(this.value== 1){

        $('#pss').fadeIn('fast');

    } else {

        $('#pss').fadeOut('fast');

    }

})



$("#men_link").change(function() {

        var valor = $('#men_titulo').val();

        var nome2 = valor.normalize('NFD').replace(/[\u0300-\u036f]/g, '') // Remove acentos

		.replace(/([^\w]+|\s+)/g, '-') // Substitui espaço e outros caracteres por hífen

		.replace(/\-\-+/g, '-')	// Substitui multiplos hífens por um único hífen

		.replace(/(^-+|-+$)/, ''); 

        $('#men_compartilhamento').val("https://daev.org.br/router/"+nome2); 

    });





</script>