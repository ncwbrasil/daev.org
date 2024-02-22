<?php

$pagina_link = 'cadastro_sala';

include_once("../core/mod_includes/php/connect.php");

include_once("../core/mod_includes/php/funcoes.php");

sec_session_start();

$page = "<a href='cadastro_sala/view'>Sala de Situação</a>";



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

                $page = "<a href='cadastro_sala/view'>Sala de Situação</a>";

                if (isset($_GET['cs_id'])) {

                    $cs_id = $_GET['cs_id'];
                }

                if ($cs_id == '') {

                    $cs_id = $_POST['cs_id'];
                }

                $cs_titulo          = $_POST['cs_titulo'];

                $cs_descricao       =  $_POST['cs_descricao'];

                $cs_data            = implode("-", array_reverse(explode("/", $_POST['cs_data'])));

                $cs_status          = $_POST['cs_status'];

                $cs_icone           = $_POST['cs_icone'];

                $cs_usuario         =  $_SESSION['usuario_id'];

                $cs_destaque        =  $_POST['cs_destaque'];

                $cs_bandeira        = $_POST['cs_bandeira'];

                $cs_cor             = $_POST['cs_cor'];

                $dados = array(

                    'cs_titulo'         => $cs_titulo,

                    'cs_status'         => $cs_status,

                    'cs_usuario'        => $cs_usuario,

                    'cs_icone'          => $cs_icone,

                    'cs_destaque'       => $cs_destaque,

                    'cs_bandeira'       => $cs_bandeira,

                    'cs_cor'            => $cs_cor,

                );



                if ($action == "adicionar") {



                    $sql = "INSERT INTO cadastro_sala SET " . bindFields($dados);

                    $stmt = $PDO->prepare($sql);

                    if ($stmt->execute($dados)) {



                        $csd_sala = $PDO->lastInsertId();

                        $dados2 = array(

                            'csd_descricao'      => $cs_descricao,

                            'csd_data'           => $cs_data,

                            'csd_sala'           => $csd_sala,

                        );



                        $sql2 = "INSERT INTO cadastro_sala_descricao SET " . bindFields($dados2);

                        $stmt2 = $PDO->prepare($sql2);

                        if ($stmt2->execute($dados2)) {

                        ?>

                            <script>
                                mensagem("Ok", "<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                            </script>

                        <?php

                        }
                    } else {

                        ?>

                        <script>
                            mensagem("X", "<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                        </script>

                        <?php

                    }
                }

                if ($action == 'atualizar') {



                    $sql = "UPDATE cadastro_sala SET " . bindFields($dados) . " WHERE cs_id = :cs_id ";

                    $stmt = $PDO->prepare($sql);

                    $dados['cs_id'] =  $cs_id;

                    if ($stmt->execute($dados)) {

                        $dados2 = array(

                            'csd_descricao'      => $cs_descricao,

                            'csd_data'           => $cs_data,

                            'csd_sala'           => $cs_id,

                        );



                        $sql2 = "INSERT INTO cadastro_sala_descricao SET " . bindFields($dados2);

                        $stmt2 = $PDO->prepare($sql2);

                        if ($stmt2->execute($dados2)) {

                        ?>

                            <script>
                                mensagem("Ok", "<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                            </script>

                        <?php

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

                    $sql = "DELETE FROM cadastro_sala WHERE cs_id = :cs_id";

                    $stmt = $PDO->prepare($sql);

                    $stmt->bindParam(':cs_id', $cs_id);

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

                    $sql = "UPDATE cadastro_sala SET cs_status = :cs_status WHERE cs_id = :cs_id ";

                    $stmt = $PDO->prepare($sql);

                    $stmt->bindValue(':cs_status', 1);

                    $stmt->bindParam(':cs_id', $cs_id);

                    $stmt->execute();
                }

                if ($action == 'desativar') {

                    $sql = "UPDATE cadastro_sala SET cs_status = :cs_status WHERE cs_id = :cs_id ";

                    $stmt = $PDO->prepare($sql);

                    $stmt->bindValue(':cs_status', 0);

                    $stmt->bindParam(':cs_id', $cs_id);

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

                    $nome_query = " (cs_titulo LIKE :fil_nome1  ) ";
                }



                $sql = "SELECT * FROM cadastro_sala 

                LEFT JOIN cadastro_sala_descricao ON cadastro_sala_descricao.csd_sala = cadastro_sala.cs_id

                WHERE " . $nome_query . "

                GROUP BY cs_id

                ORDER BY csd_id DESC 

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

                            <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_sala/view'>

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

                            <td class='titulo_tabela' align='center' colspan='2' width='2'>Informação</td>

                            <td class='titulo_tabela' align='center'>Última Atualização</td>

                            <td class='titulo_last' align='right' width='100'>Gerenciar</td>

                        </tr>";

                            $c = 0;

                            while ($result = $stmt->fetch()) {

                                $cs_id         = $result['cs_id'];

                                $cs_titulo    = $result['cs_titulo'];

                                $cs_icone    = $result['cs_icone'];

                                $csd_data    = date("d/m/Y", strtotime($result['csd_data']));



                                if ($c == 0) {

                                    $c1 = "linhaimpar";

                                    $c = 1;
                                } else {

                                    $c1 = "linhapar";

                                    $c = 0;
                                }

                                echo "<tr class='$c1'>

                                <td style='font-size:30px; text-align:center'>$cs_icone</td>

                                <td>$cs_titulo</td>

                                <td align='center'>$csd_data</td>

                                <td align=center>

                                    <div class='g_excluir' title='Excluir' onclick=\"

                                        abreMask(

                                            'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+

                                            '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'" . $pagina_link . "/view/excluir/$cs_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+

                                            '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');

                                        \">	<i class='far fa-trash-alt'></i>

                                    </div>

                                    <div class='g_editar' title='Editar' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"" . $pagina_link . "/edit/$cs_id\");'><i class='fas fa-pencil-alt'></i></div>

                                </td>

                            </tr>";
                        }

                        echo "</table>";

                        $variavel = "&fil_nome=$fil_nome";

                        $cnt = "SELECT COUNT(*) FROM cadastro_sala WHERE " . $nome_query . "  ";

                        $stmt = $PDO->prepare($cnt);

                        $stmt->bindParam(':fil_nome1',     $fil_nome1);

                        include("../core/mod_includes/php/paginacao.php");
                    } else {

                        echo "<br><br><br>Não há nenhum item cadastrado.";
                    }
                }

                if ($pagina == 'add') {

                    echo "	

                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_sala/view/adicionar'>

                        <div class='titulo'> $page &raquo; Adicionar  </div>

                        <ul class='nav nav-tabs'>

                            <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>

                        </ul>

                        <div class='tab-content'>

                            <div id='dados_gerais' class='tab-pane fade in active'>

                                <p><label>Título*:</label> <input name='cs_titulo' id='cs_titulo' placeholder='Título' class='obg' >

                                <p><label>Descrição*:</label> <input name='cs_descricao' id='cs_descricao' placeholder='Descrição'>

                                <p><label>Ícone:</label> <input name='cs_icone' id='cs_icone' class='obg'>

                                <p><label>Data:</label> <input name='cs_data' id='cs_data' placeholder='Data' class='obg'>

                                <p><label>Status:</label> <input type='radio' name='cs_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                <input type='radio' name='cs_status' value='0'> Inativo<br>		

                                <p><label>Destaque:</label> <input type='radio' name='cs_destaque' value='1'> Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                <input type='radio' name='cs_destaque' value='0' checked> Não<br>	
                                
                                <p><label>Bandeira:</label> <input type='radio' name='cs_bandeira' value='1'> Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                <input type='radio' name='cs_bandeira' value='0' checked> Não<br>
                                
                                <div class='bandeiras'>
                                    <p><label>Selecione uma cor:</label> <input type='radio' name='cs_cor' value='#2D61EE'>  <b style='color:#2D61EE'>Azul  </b>
                                    &nbsp;&nbsp;&nbsp;<input type='radio' name='cs_cor' value='#56D615'>  <b style='color:#56D615' >Verde</b>
                                    &nbsp;&nbsp;&nbsp;<input type='radio' name='cs_cor' value='#edc536'>  <b style='color:#edc536'>Amarela</b>
                                    &nbsp;&nbsp;&nbsp;<input type='radio' name='cs_cor' value='#FF781D'>  <b style='color:#FF781D'>Laranja</b>
                                    &nbsp;&nbsp;&nbsp;<input type='radio' name='cs_cor' value='#FF0000'>  <b style='color:#FF0000'>Vermelha</b>
                                </div>

                            </div>                    

                        </div>

                        <br>

                        <center>

                        <div id='erro' align='center'>&nbsp;</div>

                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 

                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_sala/view'; value='Cancelar'/></center>

                        </center>

                    </form>

                ";
                }

                if ($pagina == 'edit') {

                    $sql = "SELECT * FROM cadastro_sala 

                    LEFT JOIN cadastro_sala_descricao ON cadastro_sala_descricao.csd_sala = cadastro_sala.cs_id

                    WHERE cs_id = :cs_id

                    ORDER BY csd_id DESC";

                    $stmt = $PDO->prepare($sql);

                    $stmt->bindParam(':cs_id', $cs_id);

                    $stmt->execute();

                    $rows = $stmt->rowCount();

                    if ($rows > 0) {

                        $result = $stmt->fetch();

                        echo "

                        <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_sala/view/atualizar/$cs_id'>

                            <div class='titulo'> $page &raquo; Editar </div>

                            <ul class='nav nav-tabs'>

                                <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>

                                <li><a data-toggle='tab' href='#historico'>Histórico</a></li>

                            </ul>

                            <div class='tab-content'>

                                <div id='dados_gerais' class='tab-pane fade in active'>

                                    <p><label>Título*:</label> <input name='cs_titulo' id='cs_titulo' value='" . $result['cs_titulo'] . "' placeholder='Título' class='obg' >

                                    <p><label>Descrição*:</label> <input name='cs_descricao' id='cs_descricao' placeholder='Descrição' value='" . $result['csd_descricao'] . "'>

                                    <p><label>Data:</label> <input name='cs_data' id='cs_data' value='" . date("d/m/Y", strtotime($result['csd_data'])) . "' placeholder='Data' class='obg'>

                                    <p><label>Ícone:</label> <input name='cs_icone' id='cs_icone' value='" . $result['cs_icone'] . "' class='obg'>

                                    <p><label>Status:</label>";

                        if ($result['cs_status'] == 1) {

                            echo "<input type='radio' name='cs_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                                <input type='radio' name='cs_status' value='0'> Inativo

                                                ";
                        } else {

                            echo "<input type='radio' name='cs_status' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                                <input type='radio' name='cs_status' value='0' checked> Inativo

                                                ";
                        }

                        echo "

                                    <p><label>Destaque:</label>";

                        if ($result['cs_destaque'] == 1) {

                            echo "<input type='radio' name='cs_destaque' value='1' checked> Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                                <input type='radio' name='cs_destaque' value='0'> Não

                                                ";
                        } else {

                            echo "<input type='radio' name='cs_destaque' value='1'> Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                                <input type='radio' name='cs_destaque' value='0' checked> Não

                                                ";
                        }

                        echo "

                        <p><label>Bandeira:</label>";

                        if ($result['cs_bandeira'] == 1) {
                            echo "<input type='radio' name='cs_bandeira' value='1' checked> Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type='radio' name='cs_bandeira' value='0'> Não
                                <div class='bandeiras' style='display:block'>
                                    <p><label>Selecione uma cor:</label> <input type='radio' name='cs_cor' value='#2D61EE'";if($result['cs_cor'] == '#2D61EE'){echo "checked />";}else{echo "/>";}; echo"<b style='color:#2D61EE'>Azul  </b>
                                    &nbsp;&nbsp;&nbsp;<input type='radio' name='cs_cor' value='#56D615' ";if($result['cs_cor'] == '#56D615'){echo "checked />";}else{echo "/>";}; echo"<b style='color:#56D615'>Verde</b>
                                    &nbsp;&nbsp;&nbsp;<input type='radio' name='cs_cor' value='#edc536' ";if($result['cs_cor'] == '#edc536'){echo "checked />";}else{echo "/>";}; echo"<b style='color:#edc536'>Amarela</b>
                                    &nbsp;&nbsp;&nbsp;<input type='radio' name='cs_cor' value='#FF781D' ";if($result['cs_cor'] == '#FF781D'){echo "checked />";}else{echo "/>";}; echo"<b style='color:#FF781D'>Laranja</b>
                                    &nbsp;&nbsp;&nbsp;<input type='radio' name='cs_cor' value='#FF0000' ";if($result['cs_cor'] == '#FF0000'){echo "checked />";}else{echo "/>";}; echo"<b style='color:#FF0000'>Vermelha</b>
                                </div>";
                        } else {
                            echo "<input type='radio' name='cs_bandeira' value='1'> Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type='radio' name='cs_bandeira' value='0' checked> Não
                            <div class='bandeiras'>
                                <p><label>Selecione uma cor:</label> <input type='radio' name='cs_cor' value='#2D61EE'/><b style='color:#2D61EE'>Azul  </b>
                                &nbsp;&nbsp;&nbsp;<input type='radio' name='cs_cor' value='#56D615' /><b style='color:#56D615'>Verde</b>
                                &nbsp;&nbsp;&nbsp;<input type='radio' name='cs_cor' value='#edc536' /><b style='color:#edc536'>Amarela</b>
                                &nbsp;&nbsp;&nbsp;<input type='radio' name='cs_cor' value='#FF781D' /><b style='color:#FF781D'>Laranja</b>
                                &nbsp;&nbsp;&nbsp;<input type='radio' name='cs_cor' value='#FF0000' /><b style='color:#FF0000'>Vermelha</b>
                            </div>";
                        }

                        echo "</div>

                                <div id='historico' class='tab-pane fade in'>

                                    <table class='doc'>

                                        <tr>

                                            <th width='80%'>Descrição</th>

                                            <th width='18%'>Data</th>

                                            <th width='18%'>Excluir</th>

                                        </tr>";



                        $sql = "SELECT * FROM cadastro_sala 

                                        LEFT JOIN cadastro_sala_descricao ON cadastro_sala_descricao.csd_sala = cadastro_sala.cs_id

                                        WHERE cs_id = :cs_id

                                        ORDER BY csd_id DESC";

                        $stmt = $PDO->prepare($sql);

                        $stmt->bindParam(':cs_id', $cs_id);

                        $stmt->execute();

                        $rows = $stmt->rowCount();

                        if ($rows > 0) {

                            while ($result = $stmt->fetch()) {

                                if ($c == 0) {
                                    $c1 = "linhaimpar";
                                    $c = 1;
                                } else {
                                    $c1 = "linhapar";
                                    $c = 0;
                                }

                                echo "<tr class='$c1' id='" . $result['csd_id'] . "'>

                                                        <td width='80%'>" . $result['csd_descricao'] . "</td>

                                                        <td width='10%'>" . date("d/m/Y", strtotime($result['csd_data'])) . "</td>

                                                        <td width='8%'><center><i class='fas fa-times' onclick='remover(" . $result['csd_id'] . ")' style='cursor:pointer;'></i></center></td>

                                                    </tr>";
                            }
                        }

                        echo "</table>

                             </div>

                                <br>

                                <center>

                                <div id='erro' align='center'>&nbsp;</div>

                                <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 

                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_sala/view'; value='Cancelar'/></center>

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
    function remover(csd_id) {

        abreMask('Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>' +

            '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=rm(' + csd_id + ');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +

            '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');

    };

    $('input[type=radio][name=cs_bandeira]').change(function() {
        if (this.value == '1') {
            $('.bandeiras').fadeIn('fast')
        }
        else if (this.value == '0') {
            $('.bandeiras').fadeOut('fast')
        }
    });

    function rm(csd_id) {

        $.post("excluir.php", {
                action: 'excluir_descricao_sala',
                csd_id: csd_id
            },

            function() {

                $('tr#' + csd_id).remove();

            }

        )

    }
</script>