<?php
$pagina_link = 'cadastro_atos_administrativos';
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start();
$page = "<a href='cadastro_atos_administrativos/view'>Atos Administrativos</a>";

?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <?php include_once("header.php") ?>

    <!-- DRAGDROP -->
    <link href="../core/mod_includes/js/dragdrop/dropzone.css" type="text/css" rel="stylesheet" />
    <script src="../core/mod_includes/js/dragdrop/dropzone.js"></script>

</head>

<body>
    <main class="cd-main-content">
        <div class="container">
            <?php include('../core/mod_menu/menu/menu.php') ?>
            <div class="wrapper">
                <div class='mensagem'></div>
                <?php
                $page = "<a href='cadastro_atos_administrativos/view'>Notícias</a>";
                if (isset($_GET['cad_id'])) {
                    $cad_id = $_GET['cad_id'];
                }
                if ($cad_id == '') {
                    $cad_id = $_POST['cad_id'];
                }
                $cad_titulo          = $_POST['cad_titulo'];
                $cad_descricao       =  $_POST['cad_descricao'];
                $cad_link             = $_POST['cad_link'];
                $cad_status          = $_POST['cad_status'];

                if($cad_link==''){
                    $cad_link = geradorTags($cad_titulo);
                } 
                else{
                    $cad_compartilhamento = geradorTags($cad_titulo);
                } 

                $dados = array(
                    'cad_titulo'            => $cad_titulo,
                    'cad_descricao'         => $cad_descricao,
                    'cad_link'              => $cad_link,
                    'cad_status'            => $cad_status,
                    'cad_compartilhamento'  => $cad_compartilhamento, 
                );

                if ($action == "adicionar") {

                    $sql = "INSERT INTO cadastro_atos_administrativos SET " . bindFields($dados);
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

                    $sql = "UPDATE cadastro_atos_administrativos SET " . bindFields($dados) . " WHERE cad_id = :cad_id ";
                    $stmt = $PDO->prepare($sql);
                    $dados['cad_id'] =  $cad_id;
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
                    $sql = "DELETE FROM cadastro_atos_administrativos WHERE cad_id = :cad_id";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam(':cad_id', $cad_id);
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
                    $sql = "UPDATE cadastro_atos_administrativos SET cad_status = :cad_status WHERE cad_id = :cad_id ";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindValue(':cad_status', 1);
                    $stmt->bindParam(':cad_id', $cad_id);
                    $stmt->execute();
                }
                if ($action == 'desativar') {
                    $sql = "UPDATE cadastro_atos_administrativos SET cad_status = :cad_status WHERE cad_id = :cad_id ";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindValue(':cad_status', 0);
                    $stmt->bindParam(':cad_id', $cad_id);
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
                    $nome_query = " (cad_titulo LIKE :fil_nome1  ) ";
                }

                $sql = "SELECT * FROM cadastro_atos_administrativos 
                WHERE " . $nome_query . "
                ORDER BY cad_id DESC
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
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_atos_administrativos/view'>
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
                        <td class='titulo_tabela' align='left' colspan='2' width='1'>Ato Administrativo</td>
                        <td class='titulo_last' align='right' width='100'>Gerenciar</td>
                    </tr>";
                        $c = 0;
                        while ($result = $stmt->fetch()) {
                            $cad_id         = $result['cad_id'];
                            $cad_titulo    = $result['cad_titulo'];

                            if ($c == 0) {
                                $c1 = "linhaimpar";
                                $c = 1;
                            } else {
                                $c1 = "linhapar";
                                $c = 0;
                            }
                            echo "<tr class='$c1'>
                                <td>$cad_titulo</td>
                                <td></td>
                                <td align=center>
                                    <div class='g_excluir' title='Excluir' onclick=\"
                                        abreMask(
                                            'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                            '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/view/excluir/$cad_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                            '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                        \">	<i class='far fa-trash-alt'></i>
                                    </div>
                                    <div class='g_editar' title='Editar' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"" . $pagina_link . "/edit/$cad_id\");'><i class='fas fa-pencil-alt'></i></div>
                                </td>
                            </tr>";
                        }
                        echo "</table>";
                        $variavel = "&fil_nome=$fil_nome";
                        $cnt = "SELECT COUNT(*) FROM cadastro_atos_administrativos WHERE " . $nome_query . "  ";
                        $stmt = $PDO->prepare($cnt);
                        $stmt->bindParam(':fil_nome1',     $fil_nome1);
                        include("../core/mod_includes/php/paginacao.php");
                    } else {
                        echo "<br><br><br>Não há nenhum item cadastrado.";
                    }
                }
                if ($pagina == 'add') {
                    echo "	
            <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_atos_administrativos/view/adicionar'>
                <div class='titulo'> $page &raquo; Adicionar  </div>
                <ul class='nav nav-tabs'>
                      <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                </ul>
                <div class='tab-content' id='datas_licitacao'>
                    <div id='dados_gerais' class='tab-pane fade in active'>
                        <p><label>Título*:</label> <input name='cad_titulo' id='cad_titulo' placeholder='Título' class='obg' >
                        <p><label>Descrição*:</label> <div class='textarea'><textarea  name='cad_descricao' id='cad_descricao' placeholder='Descrição'></textarea></div>

                        <p><label>Link Externo:</label> <input name='cad_link' id='cad_link' placeholder='Link Externo'>
                        <p><label>Link para Compartilhamento:</label> <input name='cad_compartilhamento' id='cad_compartilhamento' placeholder='Link para Compartilhamento'>


                        <p><label>Status:</label> <input type='radio' name='cad_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type='radio' name='cad_status' value='0'> Inativo<br>		
                        				
                    </div>                    
				</div>
                <br>
                <center>
                <div id='erro' align='center'>&nbsp;</div>
                <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_atos_administrativos/view'; value='Cancelar'/></center>
                </center>
            </form>
            ";
                }
                if ($pagina == 'edit') {
                    $sql = "SELECT * FROM cadastro_atos_administrativos 
					WHERE cad_id = :cad_id";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam(':cad_id', $cad_id);
                    $stmt->execute();
                    $rows = $stmt->rowCount();
                    if ($rows > 0) {
                        $result = $stmt->fetch();
                        echo "
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_atos_administrativos/view/editar/$cad_id'>
                    <div class='titulo'> $page &raquo; Editar </div>
                    <ul class='nav nav-tabs'>
                        <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                    </ul>
                    <div class='tab-content' id='datas_licitacao'>
                        <div id='dados_gerais' class='tab-pane fade in active'> 
                            <p><label>Título*:</label> <input name='cad_titulo' id='cad_titulo' value='" . $result['cad_titulo'] . "' placeholder='Título' class='obg' >
                            <p><label>Descrição*:</label> <div class='textarea'><textarea name='cad_descricao' id='cad_descricao' placeholder='Descrição'>" . $result['cad_descricao'] . "</textarea></div>
                            <p><label>Link Externo:</label> <input name='cad_link' id='cad_link' placeholder='Título' value='".$result['cad_link']."'>
                            <p><label>Link para Compartilhamento:</label> <input name='cad_compartilhamento' id='cad_compartilhamento' value='".$result['cad_compartilhamento']."'>

                            <p><label>Status:</label>";
                        if ($result['cad_status'] == 1) {
                            echo "<input type='radio' name='cad_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type='radio' name='cad_status' value='0'> Inativo
                                        ";
                        } else {
                            echo "<input type='radio' name='cad_status' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type='radio' name='cad_status' value='0' checked> Inativo
                                        ";
                        }
                        echo "
                        </div>
						<br>
						<center>
						<div id='erro' align='center'>&nbsp;</div>
						<input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
						<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_atos_administrativos/view'; value='Cancelar'/></center>
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

<script>
    $("#cad_categoria").change(function() {
        var valor = $(this).val();
        if (valor == 'nova_categoria') {
            $('#cat').css('display', 'block');
        } else {
            $('#cat').css('display', 'none');

        }

    });

    $("#cn_nome").change(function() {
        var valor = $(this).val();
        $.post("../carrega_conteudo.php", {
                pagina: 'cadastra_categoria',
                categoria: valor
            },
            function(dados) {
                if (dados != '') {
                    $('#cat').css('display', 'none');

                    $("#cad_categoria").append('<option value="' + dados + '" selected>' + valor + '</option>')
                }
            }
        )
    });

    $("#cad_link").change(function() {
        var valor = $('#cad_titulo').val();
        var nome2 = valor.normalize('NFD').replace(/[\u0300-\u036f]/g, '') // Remove acentos
		.replace(/([^\w]+|\s+)/g, '-') // Substitui espaço e outros caracteres por hífen
		.replace(/\-\-+/g, '-')	// Substitui multiplos hífens por um único hífen
		.replace(/(^-+|-+$)/, ''); 
        $('#cad_compartilhamento').val("https://daev.org.br/router/"+nome2); 
    });

</script>