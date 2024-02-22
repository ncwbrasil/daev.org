<?php
$pagina_link = 'cadastro_certificados';
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start();
$page = "<a href='cadastro_certificados/view'>Cadastro de Certificados</a>"; 

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
                    if (isset($_GET['ld_id'])) {
                        $ld_id = $_GET['ld_id'];
                    }
                    if ($action == "adicionar") {
                        $ld_titulo = $_POST['ld_titulo'];
                        $ld_data = $_POST['ld_data'];
                        $sql = "INSERT INTO cadastro_certificados set
                            ld_titulo = :ld_titulo,
                            ld_data = :ld_data 
                            ";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':ld_titulo',     $ld_titulo);
                        $stmt->bindParam(':ld_data',     $ld_data);
                        
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
                        $ld_titulo = $_POST['ld_titulo'];
                        $ld_data = $_POST['ld_data'];
                        $sql = "UPDATE cadastro_certificados SET 
                            ld_titulo = :ld_titulo,
                            ld_data = :ld_data 
                            WHERE ld_id = :ld_id";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':ld_titulo',     $ld_titulo);
                        $stmt->bindParam(':ld_data',     $ld_data);
                        $stmt->bindParam(':ld_id',     $ld_id);
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
                        $sql = "DELETE FROM cadastro_certificados WHERE ld_id = :ld_id ";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':ld_id', $ld_id);
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
                    $sql = "SELECT * FROM cadastro_certificados 
                        ORDER BY ld_titulo ASC
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
                                    <td class='titulo_tabela'>Data</td>
                                    <td class='titulo_last' align='right'>Gerenciar</td>
                                </tr>";
                            $c = 0;
                            while ($result = $stmt->fetch()) {
                                $ld_id      = $result['ld_id'];
                                $ld_titulo  = $result['ld_titulo'];
                                $data3 = date_create($result['ld_data']);
                                $ld_data = date_format($data3,"d/m/Y");


                                if ($c == 0) {
                                    $c1 = "linhaimpar";
                                    $c = 1;
                                } else {
                                    $c1 = "linhapar";
                                    $c = 0;
                                }
                                echo "<tr class='$c1'>
                                            <td> $ld_titulo</td>
                                            <td> $ld_data</td>
                                            <td align=center>
                                                    <div class='g_excluir' title='Excluir' onclick=\"
                                                        abreMask(
                                                            'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                            '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/view/excluir/$ld_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                            '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                        \">	<i class='far fa-trash-alt'></i>
                                                    </div>
                                                    <div class='g_editar' title='Editar' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"" . $pagina_link . "/edit/$ld_id\");'><i class='fas fa-pencil-alt'></i></div>
                                            </td>
                                        </tr>";
                            }
                            echo "</table>";
                            $variavel = "&pagina=cadastro_certificados" . $autenticacao . "";
                            $cnt = "SELECT COUNT(*) FROM cadastro_certificados ";
                            $stmt = $PDO->prepare($cnt);
                            include("../core/mod_includes/php/paginacao.php");
                        } else {
                            echo "<br><br><br>Não há nenhum módulo cadastrado.";
                        }
                    }
                    if ($pagina == 'add') {
                        echo "	
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_certificados/view/adicionar'>
                        <div class='titulo'> Adicionar  </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <label>Título:</label> <input name='ld_titulo' id='ld_titulo' placeholder='Título' class='obg'>
                                <p><label>Data:</label> <input name='ld_data' id='ld_data' placeholder='Data'>
                            </div>
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' id='bt_cadastro_certificados' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_certificados/view'; value='Cancelar'/></center>
                            </center>
                        </div>
                    </form>
                    ";
                    }

                    if ($pagina == 'edit') {
                        $sql = "SELECT * FROM cadastro_certificados WHERE ld_id = :ld_id ";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':ld_id', $ld_id);
                        $stmt->execute();
                        $rows = $stmt->rowCount();
                        if ($rows > 0) {
                            $result = $stmt->fetch();
                            $ld_titulo = $result['ld_titulo'];
                            $data3 = date_create($result['ld_data']);
                            $ld_data = date_format($data3,"d/m/Y");

                            echo "
                        <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_certificados/view/editar/$ld_id'>
                            <div class='titulo'> Editar </div>
                            <ul class='nav nav-tabs'>
                                <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            </ul>
                            <div class='tab-content'>
                                <div id='dados_gerais' class='tab-pane fade in active'>
                                    <label>Titulo:</label> <input name='ld_titulo' id='ld_titulo' value='$ld_titulo' placeholder='Nome do Módulo' class='obg'>	
                                    <p><label>Data:</label> <input name='ld_data' id='ld_data' value='$ld_data' placeholder='Ícone'>	
                                </div>
                                <center>
                                <div id='erro' align='center'>&nbsp;</div>
                                <input type='submit' id='bt_cadastro_certificados' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_certificados/view'; value='Cancelar'/></center>
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