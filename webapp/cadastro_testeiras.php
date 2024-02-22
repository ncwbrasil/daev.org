<?php

$pagina_link = 'cadastro_testeiras';

include_once("../core/mod_includes/php/connect.php");

include_once("../core/mod_includes/php/funcoes.php");

sec_session_start();

$page = "<a href='cadastro_testeiras/view'>Testeiras das Páginas</a>";



?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

    <?php include_once("header.php") ?>

    <script type="text/javascript">
        $(document).ready(function() {

            $("select[name=ct_menu]").change(function() {

                $("select[name=ct_submenu]").html('<option value="">Carregando...</option>');

                $.post("carrega_menu.php", {
                        action: 'submenu',
                        pg_menu: $(this).val()
                    },

                    function(valor) {

                        $("select[name=ct_submenu]").html(valor);

                    }

                )

            })

        })
    </script>


</head>

<body>

    <main class="cd-main-content">

        <div class="container">

            <?php include('../core/mod_menu/menu/menu.php') ?>

            <div class="wrapper">

                <div class='mensagem'></div>

                <?php

                if (isset($_GET['ct_id'])) {

                    $ct_id = $_GET['ct_id'];
                }

                if ($ct_id == '') {

                    $ct_id = $_POST['ct_id'];
                }



                //$ct_titulo         = $_POST['ct_titulo'];

                $ct_menu            = $_POST['ct_menu'];

                $ct_submenu        = $_POST['ct_submenu'];

                $ct_status         = $_POST['ct_status'];

                $dados = array(

                    //'ct_titulo'            => $ct_titulo,

                    'ct_submenu'           => $ct_submenu,

                    'ct_status'            => $ct_status,

                    'ct_menu'               => $ct_menu,

                    'ct_usuario'           => $_SESSION['usuario_id'],

                );

                if ($action == "adicionar") {

                    $sql = "INSERT INTO cadastro_testeiras SET " . bindFields($dados);

                    $stmt = $PDO->prepare($sql);

                    if ($stmt->execute($dados)) {

                        $ct_id = $PDO->lastInsertId();

                        //UPLOAD ARQUIVOS        

                        $caminho = "uploads/testeiras/";

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

                                    $ct_imagem    = $caminho;

                                    $ct_imagem .= "testeiras_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;

                                    move_uploaded_file($nomeTemporario, ($ct_imagem));

                                    $imnfo = getimagesize($ct_imagem);

                                    $sql = "UPDATE cadastro_testeiras SET 

                                            ct_imagem 	 = :ct_imagem

                                            WHERE ct_id = :ct_id ";

                                    $stmt = $PDO->prepare($sql);

                                    $stmt->bindParam(':ct_imagem', $ct_imagem);

                                    $stmt->bindParam(':ct_id', $ct_id);



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
                            mensagem("Ok", "<i class='fas fa-check-circle'></i> Operação realizada com sucessso!");
                        </script>

                    <?php

                    } else {

                        echo $err = $stmt->errorInfo();

                    ?>

                        <script>
                            mensagem("X", "<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                        </script>

                    <?php

                    }
                }



                if ($action == 'editar') {

                    $sql = "UPDATE cadastro_testeiras SET " . bindFields($dados) . " WHERE ct_id = :ct_id ";

                    $stmt = $PDO->prepare($sql);

                    $dados['ct_id'] =  $ct_id;

                    if ($stmt->execute($dados)) {

                        //UPLOAD ARQUIVOS

                        $caminho = "uploads/testeiras/";

                        foreach ($_FILES as $key => $files) {

                            $files_test = array_filter($files['name']);

                            if (!empty($files_test)) {

                                if (!file_exists($caminho)) {

                                    mkdir($caminho, 0755, true);
                                }

                                if (!empty($files["name"]["documento"])) {

                                    # EXCLUI ANEXO ANTIGO #

                                    $sql = "SELECT * FROM cadastro_testeiras WHERE ct_id = :ct_id";

                                    $stmt_antigo = $PDO->prepare($sql);

                                    $stmt_antigo->bindParam(':ct_id', $ct_id);

                                    $stmt_antigo->execute();

                                    $result_antigo = $stmt_antigo->fetch();

                                    $documento_antigo = $result_antigo['ct_imagem'];

                                    unlink($documento_antigo);



                                    $nomeArquivo     = $files["name"]["documento"];

                                    $nomeTemporario = $files["tmp_name"]["documento"];

                                    $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);

                                    $ct_imagem    = $caminho;

                                    $ct_imagem .= "testeiras_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;

                                    move_uploaded_file($nomeTemporario, ($ct_imagem));

                                    $imnfo = getimagesize($ct_imagem);

                                    $sql = "UPDATE cadastro_testeiras SET 

                                            ct_imagem 	 = :ct_imagem

                                            WHERE ct_id = :ct_id ";

                                    $stmt = $PDO->prepare($sql);

                                    $stmt->bindParam(':ct_imagem', $ct_imagem);

                                    $stmt->bindParam(':ct_id', $ct_id);

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



                if ($action == 'excluir') {

                    $sql = "DELETE FROM cadastro_testeiras WHERE ct_id = :ct_id ";

                    $stmt = $PDO->prepare($sql);

                    $stmt->bindParam(':ct_id', $ct_id);

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

                $num_por_pagina = 25;

                if (!$pag) {
                    $primeiro_registro = 0;
                    $pag = 1;
                } else {
                    $primeiro_registro = ($pag - 1) * $num_por_pagina;
                }



                $sql = "SELECT * FROM cadastro_testeiras 
                        LEFT JOIN aux_menu ON aux_menu.men_id = cadastro_testeiras.ct_menu
                        LEFT JOIN aux_submenu ON aux_submenu.sm_id = cadastro_testeiras.ct_submenu
                        ORDER BY ct_submenu ASC
                        LIMIT :primeiro_registro, :num_por_pagina ";

                $stmt = $PDO->prepare($sql);

                $stmt->bindParam(':primeiro_registro',     $primeiro_registro);

                $stmt->bindParam(':num_por_pagina',     $num_por_pagina);

                $stmt->execute();

                $rows = $stmt->rowCount();

                if ($pagina == "view") {

                    echo "

                            <div class='titulo'> $page  </div>

                            <div id='botoes'>

                                <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(" . $permissoes["add"] . ",\"cadastro_testeiras/add\");'><i class='fas fa-plus'></i></div>

                            </div>

                            ";

                    if ($rows > 0) {

                        echo "

                                <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>

                                    <tr>

                                        <td class='titulo_first'>Imagem</td>

                                        <!--<td class='titulo_tabela'>Titulo</td>--!>

                                        <td class='titulo_tabela'>Submenu</td>

                                        <td class='titulo_tabela'>Menu</td>

                                        <td class='titulo_last' align='right'>Gerenciar</td>

                                    </tr>";

                        $c = 0;

                        while ($result = $stmt->fetch()) {

                            $ct_titulo     = $result['ct_titulo'];

                            $ct_submenu     = $result['sm_titulo'];

                            $ct_menu         = $result['men_titulo'];

                            $ct_imagem        = $result['ct_imagem'];

                            $ct_id         = $result['ct_id'];



                            if ($c == 0) {
                                $c1 = "linhaimpar";
                                $c = 1;
                            } else {
                                $c1 = "linhapar";
                                $c = 0;
                            }

                            echo "<tr class='$c1'>

                                                <td><img src='$ct_imagem' width='150px'></td>

                                                <!--<td>$ct_titulo</td>--!>

                                                <td>$ct_submenu</td>

                                                <td>$ct_menu</td>

                                                <td align=center>

                                                    <div class='g_excluir' onclick=\"

                                                            abreMask(

                                                                'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+

                                                                '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'cadastro_testeiras/view/excluir/$ct_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+

                                                                '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');

                                                            \">	<i class='far fa-trash-alt'></i>

                                                    </div>

                                                    <div class='g_editar'  title='Editar' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"cadastro_testeiras/edit/$ct_id\");'><i class='fas fa-pencil-alt'></i></div>											

                                                </td>

                                            </tr>";
                        }

                        echo "</table>";

                        $variavel = "&pagina=cadastro_testeiras&ct_id=$ct_id" . $autenticacao . "";

                        $cnt = "SELECT COUNT(*) FROM cadastro_testeiras

                                            WHERE ct_id = :ct_id ";

                        $stmt = $PDO->prepare($cnt);

                        $stmt->bindParam(':ct_id', $ct_id);

                        include("../core/mod_includes/php/paginacao.php");
                    } else {

                        echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                    }
                }



                if ($pagina == 'add') {

                    echo "	

                        <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_testeiras/view/adicionar'>

                            <div class='titulo'> $page &raquo; Adicionar  </div>

                            <ul class='nav nav-tabs'>

                                <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>

                            </ul>

                            <div class='tab-content'>

                                <div id='dados_gerais' class='tab-pane fade in active'>

                                    <!--<label>Título:</label> <input name='ct_titulo' id='ct_titulo' placeholder='Título'>--!>

                                    <p><label>Menu:</label><select name='ct_menu' id='ct_menu'>
                                    <option value=''>Selecione </option>
                                    ";
                                        $sql = "SELECT * FROM aux_menu";
                                        $stmt = $PDO->prepare($sql);
                                        $stmt->execute();
                                        $rows = $stmt->rowCount();
                                        if ($rows > 0) {
                                            while ($result = $stmt->fetch()) {
                                                echo "<option value='" . $result['men_id'] . "'> " . $result['men_titulo'] . "</option>";
                                            }
                                        }
                                    echo "
                                    </select>
                                    <p><label>Submenu*:</label>

                                        <select name='ct_submenu' id='ct_submenu'>
                                            <option selected='selected'>Aguardando Submenu...</option>
                                        </select>

                                    <p><label>Imagem:</label> <input type='file' name='ct_imagem[documento]' id='ct_imagem' class='obg'> <br>

                                    <center><i style='font-size:14px'>*Recomendado usar imagens de 1920px de largura por 1080px de altura, evite imagens quadradas. </i></center>



                                    <p><label>Status:</label> <input type='radio' name='ct_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                    <input type='radio' name='ct_status' value='0'> Inativo<br>		

            



                                </div>

                                <center>

                                <div id='erro' align='center'>&nbsp;</div>

                                <input type='submit' id='bt_cadastro_testeiras' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 

                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_testeiras/view'; value='Cancelar'/></center>

                                </center>

                            </div>

                        </form>

                        ";
                }



                if ($pagina == 'edit') {

                    $sql = "SELECT * FROM cadastro_testeiras WHERE ct_id = :ct_id";

                    $stmt = $PDO->prepare($sql);

                    $stmt->bindParam(':ct_id', $ct_id);

                    $stmt->execute();

                    $rows = $stmt->rowCount();

                    if ($rows > 0) {

                        $result = $stmt->fetch();



                        echo "

                            <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_testeiras/view/editar/$ct_id'>

                                <div class='titulo'> $page &raquo; Editar </div>

                                <ul class='nav nav-tabs'>

                                    <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>

                                </ul>

                                <div class='tab-content'>

                                    <div id='dados_gerais' class='tab-pane fade in active'>

                                        <!--<label>Título:</label> <input name='ct_titulo' id='ct_titulo' value='" . $result['ct_titulo'] . "' placeholder='Título'>--!>

                                        <p><label>Posição:</label> <input name='ct_submenu' id='ct_submenu' placeholder='Posição' value='" . $result['ct_submenu'] . "' class='obg'>

                                        <p><label>URL:</label> <input name='ct_menu' id='ct_menu' placeholder='URL' value='" . $result['ct_menu'] . "'>



                                        <p><label>Imagem:</label> ";

                        if ($result['ct_imagem'] != '') {

                            echo "<img src='" . $result['ct_imagem'] . "' style='max-width:400px;'> ";
                        }

                        echo " &nbsp; 

                                            <p><label>Alterar Imagem:</label> <input type='file' name='ct_imagem[documento]' id='ct_imagem'>

                                            <center><i style='font-size:14px'>*Recomendado usar imagens de 1920px de largura por 1080px de altura, evite imagens quadradas. </i></center>

                                        <p><label>Status:</label>";

                        if ($result['ct_status'] == 1) {

                            echo "<input type='radio' name='ct_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                                    <input type='radio' name='ct_status' value='0'> Inativo

                                                    ";
                        } else {

                            echo "<input type='radio' name='ct_status' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                                    <input type='radio' name='ct_status' value='0' checked> Inativo

                                                    ";
                        }

                        echo "

                

                                    </div>

                                    <center>

                                    <div id='erro' align='center'>&nbsp;</div>

                                    <input type='submit' id='bt_cadastro_testeiras' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 

                                    <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_testeiras/view'; value='Cancelar'/></center>

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