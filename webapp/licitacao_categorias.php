<?php
$pagina_link = 'licitacao_categorias';
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start();
$page = "<a href='licitacao_categorias/view'>Categorias</a>"; 

?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <?php include_once("header.php") ?>
</head>

<body>

    <main class="cd-main-content">
        <div class="container">
			<?php include('../core/mod_menu/menu/menu.php')?>
			<div class="wrapper">
                <div class='mensagem'></div>
                <?php
                    if (isset($_GET['lc_id'])) {
                        $lc_id = $_GET['lc_id'];
                    }
                    if ($action == "adicionar") {
                        $lc_titulo = $_POST['lc_titulo'];
                        $lc_url = geradorTags($lc_titulo);

                        $sql = "INSERT INTO licitacao_categorias set
                            lc_titulo = :lc_titulo, 
                            lc_url = :lc_url";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':lc_titulo',     $lc_titulo);
                        $stmt->bindParam(':lc_url',     $lc_url);
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

                    if ($action == 'editar') {
                        $lc_titulo = $_POST['lc_titulo'];
                        $lc_url = $_POST['lc_url'];

                        $sql = "UPDATE licitacao_categorias SET 
                            lc_titulo = :lc_titulo, 
                            lc_url = :lc_url
                            WHERE lc_id = :lc_id";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':lc_titulo', $lc_titulo);
                        $stmt->bindParam(':lc_url',    $lc_url);
                        $stmt->bindParam(':lc_id',     $lc_id);
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

                    if ($action == 'excluir') {
                        $sql = "DELETE FROM licitacao_categorias WHERE lc_id = :lc_id ";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':lc_id', $lc_id);
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
                    if (!$pag) {
                        $primeiro_registro = 0;
                        $pag = 1;
                    } else {
                        $primeiro_registro = ($pag - 1) * $num_por_pagina;
                    }
                    $sql = "SELECT * FROM licitacao_categorias 
                        ORDER BY lc_titulo ASC
                        LIMIT :primeiro_registro, :num_por_pagina ";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam(':primeiro_registro', $primeiro_registro);
                    $stmt->bindParam(':num_por_pagina', $num_por_pagina);
                    $stmt->execute();
                    $rows = $stmt->rowCount();
                    if ($pagina == "view") {
                        echo "
                        <div id='botoes'>
                            <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(" . $permissoes["add"] . ",\"" . $pagina_link . "/add\");'><i class='fas fa-plus'></i></div>
                        </div>
                        ";
                        if ($rows > 0) {
                            echo "
                            <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                <tr>
                                    <td class='titulo_first'>Nome</td>
                                    <td class='titulo_last' align='right'>Gerenciar</td>
                                </tr>";
                            $c = 0;
                            while ($result = $stmt->fetch()) {
                                $lc_id         = $result['lc_id'];
                                $lc_titulo     = $result['lc_titulo'];
                                if ($c == 0) {
                                    $c1 = "linhaimpar";
                                    $c = 1;
                                } else {
                                    $c1 = "linhapar";
                                    $c = 0;
                                }
                                echo "<tr class='$c1'>
                                            <td>$lc_titulo</td>
                                            <td align=center>
                                                <div class='g_excluir' title='Excluir' onclick=\"
                                                    abreMask(
                                                        'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                        '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/view/excluir/$lc_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                        '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                    \">	<i class='far fa-trash-alt'></i>
                                                </div>
                                                <div class='g_editar' title='Editar' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"" . $pagina_link . "/edit/$lc_id\");'><i class='fas fa-pencil-alt'></i></div>
                                            </td>
                                        </tr>";
                            }
                            echo "</table>";
                            $variavel = "&pagina=licitacao_categorias" . $autenticacao . "";
                            $cnt = "SELECT COUNT(*) FROM licitacao_categorias ";
                            $stmt = $PDO->prepare($cnt);
                            include("../core/mod_includes/php/paginacao.php");
                        } else {
                            echo "<br><br><br>Não há nenhuma página cadastrada.";
                        }
                    }
                    if ($pagina == 'add') {
                        echo "	
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='licitacao_categorias/view/adicionar'>
                        <div class='titulo'> Adicionar  </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <label>Nome:</label> <input name='lc_titulo' id='lc_titulo' placeholder='Nome' class='obg'>
                            </div>
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' id='bt_licitacao_categorias' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='licitacao_categorias/view'; value='Cancelar'/></center>
                            </center>
                        </div>
                    </form>
                    ";
                    }

                    if ($pagina == 'edit') {
                        $sql = "SELECT * FROM licitacao_categorias WHERE lc_id = :lc_id ";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':lc_id', $lc_id);
                        $stmt->execute();
                        $rows = $stmt->rowCount();
                        if ($rows > 0) {
                            $result = $stmt->fetch();
                            $lc_titulo = $result['lc_titulo'];

                            echo "
                        <form name='form' id='form' enctype='multipart/form-data' method='post' action='licitacao_categorias/view/editar/$lc_id'>
                            <div class='titulo'> Editar </div>
                            <ul class='nav nav-tabs'>
                                <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            </ul>
                            <div class='tab-content'>
                                <div id='dados_gerais' class='tab-pane fade in active'>
                                    <label>Nome:</label> <input name='lc_titulo' id='lc_titulo' value='$lc_titulo' placeholder='Nome' class='obg'>	
                                </div>
                                <center>
                                <div id='erro' align='center'>&nbsp;</div>
                                <input type='submit' id='bt_licitacao_categorias' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='licitacao_categorias/view'; value='Cancelar'/></center>
                                </center>
                            </div>                           
                        </form>
                        ";
                        }
                    }
                ?>
			</div>
		</div><!-- /container -->
    </main> <!-- .cd-main-content -->
</body>

</html>