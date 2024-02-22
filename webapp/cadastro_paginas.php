<?php

$pagina_link = 'cadastro_paginas';

include_once("../core/mod_includes/php/connect.php");

include_once("../core/mod_includes/php/funcoes.php");

sec_session_start();

$page = "<a href='cadastro_paginas/view'>Páginas Institucionais</a>"; 



?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>

<html xmlns="http://www.w3.org/1999/xhtml">



<head>

    <?php include_once("header.php") ?>



    <!-- TINY -->

    <script src="../core/mod_includes/js/tinymce/tinymce.min.js"></script>

    <script>

        tinymce.init({

            selector: 'textarea',

            plugins: "image code jbimages imagetools advlist link table textcolor media",

            toolbar: "undo redo format bold italic forecolor backcolor alignleft aligncenter alignright alignjustify bullist numlist outdent indent table link media image jbimages",

            imagetools_toolbar: "rotateleft rotateright | flipv fliph | editimage imageoptions",

            paste_data_images: true,

            media_live_embeds: true,

            relative_urls: false,

        });

    </script>

    <!-- TINY -->

<script type="text/javascript">

$(document).ready(function(){

	$("select[name=pg_menu]").change(function(){

		$("select[name=pg_submenu]").html('<option value="">Carregando...</option>');

		$.post("carrega_menu.php",{action:'submenu', pg_menu:$(this).val()},

			function(valor){

			 $("select[name=pg_submenu]").html(valor);

			}

		)

	})

})

</script>

</head>

<body>

	<main class="cd-main-content">

        <div class="container">

            <?php include('../core/mod_menu/menu/menu.php')?>

			<div class="wrapper">

            <div class='mensagem'></div>

            <?php

            $page = " <a href='cadastro_paginas/view'>Páginas Institucionais</a>";

            if (isset($_GET['pg_id'])) {

                $pg_id = $_GET['pg_id'];

            }

            if ($pg_id == '') {

                $pg_id = $_POST['pg_id'];

            }

            $pg_titulo              = $_POST['pg_titulo'];

            $pg_descricao           =  $_POST['pg_descricao'];

            $pg_url                 = geradorTags($_POST['pg_titulo']);


            $pg_menu                = $_POST['pg_menu'];

            $pg_submenu             = $_POST['pg_submenu'];

            $pg_status              = $_POST['pg_status'];

            $pg_link                = $_POST['pg_link']; 

            $pg_meta_description    = $_POST['pg_meta_description']; 

            $pg_meta_titulo         = $_POST['pg_meta_titulo']; 

            

            $dados = array(

                'pg_titulo'             => $pg_titulo,

                'pg_descricao'          => $pg_descricao,

                'pg_url'                => $pg_url,

                'pg_menu'               => $pg_menu,

                'pg_submenu'            => $pg_submenu,

                'pg_usuario'            => $_SESSION[$sis_url]['usuario_id'],

                'pg_status'             => $pg_status, 

                'pg_link'               => $pg_link, 

                'pg_meta_titulo'        => $pg_meta_titulo, 

                'pg_meta_description'   => $pg_meta_description,

            );



            if ($action == "adicionar") {

                $sql = "INSERT INTO cadastro_paginas SET " . bindFields($dados);

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

                $sql = "UPDATE cadastro_paginas SET " . bindFields($dados) . " WHERE pg_id = :pg_id ";

                $stmt = $PDO->prepare($sql);

                $dados['pg_id'] =  $pg_id;

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

                $sql = "DELETE FROM cadastro_paginas WHERE pg_id = :pg_id";

                $stmt = $PDO->prepare($sql);

                $stmt->bindParam(':pg_id', $pg_id);

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

                $sql = "UPDATE cadastro_paginas SET pg_status = :pg_status WHERE pg_id = :pg_id ";

                $stmt = $PDO->prepare($sql);

                $stmt->bindValue(':pg_status', 1);

                $stmt->bindParam(':pg_id', $pg_id);

                $stmt->execute();

            }

            if ($action == 'desativar') {

                $sql = "UPDATE cadastro_paginas SET pg_status = :pg_status WHERE pg_id = :pg_id ";

                $stmt = $PDO->prepare($sql);

                $stmt->bindValue(':pg_status', 0);

                $stmt->bindParam(':pg_id', $pg_id);

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

                $nome_query = " (pg_titulo LIKE :fil_nome1  ) ";

            }



            $sql = "SELECT * FROM cadastro_paginas 

                    LEFT JOIN aux_menu ON aux_menu.men_id = cadastro_paginas.pg_menu

                    LEFT JOIN aux_submenu ON aux_submenu.sm_id = cadastro_paginas.pg_submenu

                WHERE " . $nome_query . "

                ORDER BY pg_id DESC

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

                    <div class='filtrar'><span class='f'><i class='fas fa-search'></i></span> </div>

                    <div class='filtro'>

                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_paginas/view'>

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

                        <td class='titulo_tabela' align='left'>Página</td>

                        <td class='titulo_tabela' align='center'>Menu</td>

                        <td class='titulo_last' align='right' width='100'>Gerenciar</td>

                    </tr>";

                    $c = 0;

                    while ($result = $stmt->fetch()) {

                        $pg_id          = $result['pg_id'];

                        $pg_titulo      = $result['pg_titulo'];

                        $pg_submenu     = $result['men_titulo'] .' > '. $result['sm_titulo'] ;

                        $pg_data        = implode("/", array_reverse(explode("-", $result['pg_data'])));



                        if ($c == 0) {

                            $c1 = "linhaimpar";

                            $c = 1;

                        } else {

                            $c1 = "linhapar";

                            $c = 0;

                        }

                        echo "<tr class='$c1'>

                                  <td>$pg_titulo</td>

                                  <td align=center>$pg_submenu</td>

                                  <td align=center>

										<div class='g_excluir' title='Excluir' onclick=\"

											abreMask(

												'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+

												'<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/view/excluir/$pg_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+

												'<input value=\' Não \' type=\'button\' class=\'close_janela\'>');

											\">	<i class='far fa-trash-alt'></i>

										</div>

										<div class='g_editar' title='Editar' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"" . $pagina_link . "/edit/$pg_id\");'><i class='fas fa-pencil-alt'></i></div>

								  </td>

                              </tr>";

                    }

                    echo "</table>";

                    $variavel = "&fil_nome=$fil_nome";

                    $cnt = "SELECT COUNT(*) FROM cadastro_paginas WHERE " . $nome_query . "  ";

                    $stmt = $PDO->prepare($cnt);

                    $stmt->bindParam(':fil_nome1',     $fil_nome1);

                    include("../core/mod_includes/php/paginacao.php");

                } else {

                    echo "<br><br><br>Não há nenhum item cadastrado.";

                }

            }

            if ($pagina == 'add') {

                echo "	

                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_paginas/view/adicionar'>

                        <div class='titulo'> $page &raquo; Adicionar  </div>

                        <ul class='nav nav-tabs'>

                            <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>

                        </ul>

                        <div class='tab-content'>

                            <div id='dados_gerais' class='tab-pane fade in active'>

                                <p><label>Título*:</label> <input name='pg_titulo' id='pg_titulo' placeholder='Título' class='obg' >

                                <p><label>Menu*:</label>

                                    <select name='pg_menu' id='pg_menu'>";

                                        $sql_menu = "SELECT * FROM aux_menu"; 

                                        $stmt_m = $PDO->prepare($sql_menu);

                                        $stmt_m->execute();

                                        $rows_m = $stmt_m->rowCount();

                                        if($rows_m > 0){

                                            while($result_m = $stmt_m->fetch()) {

                                                echo "<option value='".$result_m['men_id']."'> ".$result_m['men_titulo']." </option>";

                                            }

                                        }                                       

                                    echo "</select>



                                <p><label>Submenu*:</label>

                                    <select name='pg_submenu' id='pg_submenu'>

                                        <option selected='selected'>Aguardando Submenu...</option>";

                                echo"</select>

                                <p><label>Descrição*:</label> <div class='textarea'><textarea  name='pg_descricao' id='pg_descricao' placeholder='Descrição'></textarea></div>



                                <p><label>Meta Tag Descrição:</label> <input name='pg_meta_titulo' id='pg_meta_titulo' placeholder='Meta Tag Descrição'>

                                <p><label>Meta Tag Título:</label> <input name='pg_meta_description' id='pg_meta_description' placeholder='Meta Tag Título'>

                

                                <p><label>Link Externo:</label> <input name='pg_link' id='pg_link' placeholder='Link Externo'>

                                <p><label>Status:</label> <input type='radio' name='pg_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                <input type='radio' name='pg_status' value='0'> Inativo<br>						

                            </div>

                        </div>

                        <br>

                        <center>

                        <div id='erro' align='center'>&nbsp;</div>

                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 

                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_paginas/view'; value='Cancelar'/></center>

                        </center>

                    </form>

                ";

            }

            if ($pagina == 'edit') {

                $sql = "SELECT * FROM cadastro_paginas 

                    LEFT JOIN aux_menu ON aux_menu.men_id = cadastro_paginas.pg_menu

                    LEFT JOIN aux_submenu ON aux_submenu.sm_id = cadastro_paginas.pg_submenu

					WHERE pg_id = :pg_id";

                $stmt = $PDO->prepare($sql);

                $stmt->bindParam(':pg_id', $pg_id);

                $stmt->execute();

                $rows = $stmt->rowCount();

                if ($rows > 0) {

                    $result = $stmt->fetch();

                    echo "

                <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_paginas/view/editar/$pg_id'>

                    <div class='titulo'> $page &raquo; Editar </div>

                    <ul class='nav nav-tabs'>

                    	<li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>

                    </ul>

                    <div class='tab-content'>

                        <div id='dados_gerais' class='tab-pane fade in active'>

                        <p><label>Título*:</label> <input name='pg_titulo' id='pg_titulo' value='" . $result['pg_titulo'] . "' placeholder='Título' class='obg' >

                        <p><label>Menu*:</label>

                        <select name='pg_menu' id='pg_menu'>

                            <option value='".$result['men_id']."'> ".$result['men_titulo']." </option>";

                            $sql_menu = "SELECT * FROM aux_menu"; 

                            $stmt_m = $PDO->prepare($sql_menu);

                            $stmt_m->execute();

                            $rows_m = $stmt_m->rowCount();

                            if($rows_m > 0){

                                while($result_m = $stmt_m->fetch()) {

                                    echo "<option value='".$result_m['men_id']."'> ".$result_m['men_titulo']." </option>";

                                }

                            }                                       

                        echo"</select>



                        <p><label>Submenu*:</label>

                            <select name='pg_submenu' id='pg_submenu'>

                                <option value='".$result['sm_id']."'> ".$result['sm_titulo']." </option>";

                            echo"</select>



						<p><label>Descrição*:</label> <div class='textarea'><textarea name='pg_descricao' id='pg_descricao' placeholder='Descrição'>" . $result['pg_descricao'] . "</textarea></div>



                        <p><label>Meta Tag Descrição:</label> <input name='pg_meta_titulo' id='pg_meta_titulo' placeholder='Meta Tag Descrição' value='" . $result['pg_meta_description'] . "'>

                        <p><label>Meta Tag Título:</label> <input name='pg_meta_description' id='pg_meta_description' placeholder='Meta Tag Título' value='" . $result['pg_meta_titulo'] . "'>



                        <p><label>Link Externo:</label> <input name='pg_link' id='pg_link' value='".$result['pg_link']. "' placeholder='Link Externo'>



                        <p><label>Status:</label>";

                        if ($result['pg_status'] == 1) {

                            echo "<input type='radio' name='pg_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                    <input type='radio' name='pg_status' value='0'> Inativo

                                    ";

                        } else {

                            echo "<input type='radio' name='pg_status' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                    <input type='radio' name='pg_status' value='0' checked> Inativo

                                    ";

                        }

                        echo "

                        </div>

						<br>

						<center>

						<div id='erro' align='center'>&nbsp;</div>

						<input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 

						<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_paginas/view'; value='Cancelar'/></center>

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