<?php
$pagina_link = 'cadastro_downloads_categorias';
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start();
$page = "<a href='cadastro_downloads_categorias/view'>Categorias</a>"; 

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
                    if (isset($_GET['cat_id'])) {
                        $cat_id = $_GET['cat_id'];
                    }
                    if ($action == "adicionar") {
                        $cat_nome = $_POST['cat_nome'];
                        $cat_url = geradorTags($cat_nome);

                        $sql = "INSERT INTO cadastro_downloads_categorias set
                            cat_nome = :cat_nome, 
                            cat_url = :cat_url";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':cat_nome',     $cat_nome);
                        $stmt->bindParam(':cat_url',     $cat_url);
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
                        $cat_nome = $_POST['cat_nome'];
                        $cat_url = $_POST['cat_url'];

                        $sql = "UPDATE cadastro_downloads_categorias SET 
                            cat_nome = :cat_nome, 
                            cat_url = :cat_url
                            WHERE cat_id = :cat_id";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':cat_nome', $cat_nome);
                        $stmt->bindParam(':cat_url',    $cat_url);
                        $stmt->bindParam(':cat_id',     $cat_id);
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
                        $sql = "DELETE FROM cadastro_downloads_categorias WHERE cat_id = :cat_id ";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':cat_id', $cat_id);
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
                    $sql = "SELECT * FROM cadastro_downloads_categorias 
                        ORDER BY cat_nome ASC
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
                                $cat_id         = $result['cat_id'];
                                $cat_nome     = $result['cat_nome'];
                                if ($c == 0) {
                                    $c1 = "linhaimpar";
                                    $c = 1;
                                } else {
                                    $c1 = "linhapar";
                                    $c = 0;
                                }
                                echo "<tr class='$c1'>
                                            <td>$cat_nome</td>
                                            <td align=center>
                                                <div class='g_excluir' title='Excluir' onclick=\"
                                                    abreMask(
                                                        'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                        '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/view/excluir/$cat_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                        '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                    \">	<i class='far fa-trash-alt'></i>
                                                </div>
                                                <div class='g_editar' title='Editar' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"" . $pagina_link . "/edit/$cat_id\");'><i class='fas fa-pencil-alt'></i></div>
                                            </td>
                                        </tr>";
                            }
                            echo "</table>";
                            $variavel = "&pagina=cadastro_downloads_categorias" . $autenticacao . "";
                            $cnt = "SELECT COUNT(*) FROM cadastro_downloads_categorias ";
                            $stmt = $PDO->prepare($cnt);
                            include("../core/mod_includes/php/paginacao.php");
                        } else {
                            echo "<br><br><br>Não há nenhuma página cadastrada.";
                        }
                    }
                    if ($pagina == 'add') {
                        echo "	
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_downloads_categorias/view/adicionar'>
                        <div class='titulo'> Adicionar  </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <label>Nome:</label> <input name='cat_nome' id='cat_nome' placeholder='Nome' class='obg'>
                            </div>
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' id='bt_cadastro_downloads_categorias' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_downloads_categorias/view'; value='Cancelar'/></center>
                            </center>
                        </div>
                    </form>
                    ";
                    }

                    if ($pagina == 'edit') {
                        $sql = "SELECT * FROM cadastro_downloads_categorias WHERE cat_id = :cat_id ";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':cat_id', $cat_id);
                        $stmt->execute();
                        $rows = $stmt->rowCount();
                        if ($rows > 0) {
                            $result = $stmt->fetch();
                            $cat_nome = $result['cat_nome'];

                            echo "
                        <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_downloads_categorias/view/editar/$cat_id'>
                            <div class='titulo'> Editar </div>
                            <ul class='nav nav-tabs'>
                                <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            </ul>
                            <div class='tab-content'>
                                <div id='dados_gerais' class='tab-pane fade in active'>
                                    <label>Nome:</label> <input name='cat_nome' id='cat_nome' value='$cat_nome' placeholder='Nome' class='obg'>	
                                </div>
                                <center>
                                <div id='erro' align='center'>&nbsp;</div>
                                <input type='submit' id='bt_cadastro_downloads_categorias' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_downloads_categorias/view'; value='Cancelar'/></center>
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