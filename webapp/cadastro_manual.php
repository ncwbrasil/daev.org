<?php
$pagina_link = 'cadastro_manual';
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start();
$page = "<a href='cadastro_manual/view'>Manual do DAEV</a>";

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

                $page = " <a href='cadastro_manual/view'>Manual do DAEV</a>";
                if (isset($_GET['man_id'])) {
                    $man_id = $_GET['man_id'];
                }
                if ($man_id == '') {
                    $man_id = $_POST['man_id'];
                }
                $man_titulo     = $_POST['man_titulo'];
                $man_url        = geradorTags($man_titulo);

                $dados = array(
                    'man_titulo'    => $man_titulo,
                    'man_url'       => $man_url,
                    'man_usuario'   =>  $_SESSION['usuario_id'], 
                );

                if ($action == "adicionar") {
                    $sql = "INSERT INTO cadastro_manual SET " . bindFields($dados);
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

                    $sql = "UPDATE cadastro_manual SET " . bindFields($dados) . " WHERE man_id = :man_id ";
                    $stmt = $PDO->prepare($sql);
                    $dados['man_id'] =  $man_id;
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
                    $sql = "DELETE FROM cadastro_manual WHERE man_id = :man_id";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam(':man_id', $man_id);
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
                    $sql = "UPDATE cadastro_manual SET man_status = :man_status WHERE man_id = :man_id ";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindValue(':man_status', 1);
                    $stmt->bindParam(':man_id', $man_id);
                    $stmt->execute();
                }

                if ($action == 'desativar') {
                    $sql = "UPDATE cadastro_manual SET man_status = :man_status WHERE man_id = :man_id ";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindValue(':man_status', 0);
                    $stmt->bindParam(':man_id', $man_id);
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
                    $nome_query = " (man_nome LIKE :fil_nome1  ) ";
                }

                $sql = "SELECT * FROM cadastro_manual WHERE " . $nome_query . "
                    ORDER BY man_id ASC
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
                                <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_manual/view'>
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
                            <td class='titulo_tabela' align='left' colspan='2' width='1'>Titulo do Manual</td>
                            <td class='titulo_last' align='right'>Gerenciar</td>
                        </tr>";
                        $c = 0;
                        while ($result = $stmt->fetch()) {
                            $man_id         = $result['man_id'];
                            $man_titulo     = $result['man_titulo'];

                            if ($c == 0) {
                                $c1 = "linhaimpar";
                                $c = 1;
                            } else {
                                $c1 = "linhapar";
                                $c = 0;
                            }
                            echo "<tr class='$c1'>
                                    <td>$man_titulo</td>
                                    <td></td>
                                    <td align=center>
                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                abreMask(
                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/view/excluir/$man_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                \">	<i class='far fa-trash-alt'></i>
                                            </div>
                                            <div class='g_editar' title='Editar' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"" . $pagina_link . "/edit/$man_id\");'><i class='fas fa-pencil-alt'></i></div>
                                            <div class='g_status' title='Submenus' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"cadastro_manual_paginas/$man_id/view\");'><i class='fas fa-list-ul'></i></div>
                                    </td>
                                </tr>";
                        }
                        echo "</table>";
                        $variavel = "&fil_nome=$fil_nome";
                        $cnt = "SELECT COUNT(*) FROM cadastro_manual WHERE " . $nome_query . "  ";
                        $stmt = $PDO->prepare($cnt);
                        $stmt->bindParam(':fil_nome1', $fil_nome1);
                        include("../core/mod_includes/php/paginacao.php");
                    } else {
                        echo "<br><br><br>Não há nenhum menu cadastrado.";
                    }
                }

                if ($pagina == 'add') {
                    echo "	
                        <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_manual/view/adicionar'>
                            <div class='titulo'> $page &raquo; Adicionar  </div>
                            <ul class='nav nav-tabs'>
                                <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                            </ul>
                            <div class='tab-content'>
                                <div id='dados_gerais' class='tab-pane fade in active'>
                                    <p><label>Título*:</label> <input name='man_titulo' id='man_titulo' placeholder='Título' class='obg' >
                                </div>
                            </div>
                            <br>
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_manual/view'; value='Cancelar'/></center>
                            </center>
                        </form>
                    ";
                }

                if ($pagina == 'edit') {
                    $sql = "SELECT * FROM cadastro_manual 
                        WHERE man_id = :man_id";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam(':man_id', $man_id);
                    $stmt->execute();
                    $rows = $stmt->rowCount();
                    if ($rows > 0) {
                        $result = $stmt->fetch();
                        echo "
                            <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_manual/view/editar/$man_id'>
                                <div class='titulo'> $page &raquo; Editar </div>
                                <ul class='nav nav-tabs'>
                                    <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                                </ul>
                                <div class='tab-content'>
                                    <div id='dados_gerais' class='tab-pane fade in active'>
                                    <p><label>Título*:</label> <input name='man_titulo' id='man_titulo' value='" . $result['man_titulo'] . "' placeholder='Título' class='obg' >
                                    </div>
                                    <br>
                                    <center>
                                    <div id='erro' align='center'>&nbsp;</div>
                                    <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                    <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_manual/view'; value='Cancelar'/></center>
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