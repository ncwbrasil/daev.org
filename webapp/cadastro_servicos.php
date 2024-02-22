<?php
$pagina_link = 'cadastro_servicos';
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start();
$page = "<a href='cadastro_servicos/view'>Downloads</a>";

?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <?php include_once("header.php") ?>
    <script type="text/javascript">
        $(document).ready(function() {

            $("select[name=men]").html('<option value="">Carregando...</option>');
            $.post("carrega_menu.php", {
                    action: 'menu',
                    pg_menu: $(this).val()
                },
                function(valor) {
                    $("select[name=men]").html(valor);
                }
            )

            $("select[name=men]").change(function() {
                $("select[name=sm]").html('<option value="">Carregando...</option>');
                $.post("carrega_menu.php", {
                        action: 'submenu',
                        pg_menu: $(this).val()
                    },
                    function(valor) {
                        $("select[name=sm]").html(valor);
                    }
                )
            })

            // $("select[name=menu]").html('<option value="">Carregando...</option>');
            // $.post("carrega_menu.php", {
            //         action: 'menu',
            //         pg_menu: $(this).val()
            //     },
            //     function(valor) {
            //         $("select[name=menu]").html(valor);
            //     }
            // )

            $("select[name=menu]").change(function() {
                $("select[name=submenu]").html('<option value="">Carregando...</option>');
                $.post("carrega_menu.php", {
                        action: 'submenu',
                        pg_menu: $(this).val()
                    },
                    function(valor) {
                        $("select[name=submenu]").html(valor);
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
                if (isset($_GET['id'])) {
                    $id = $_GET['id'];
                }
                if ($id == '') {
                    $id = $_POST['id'];
                }

                $nome           = $_POST['nome'];
                $id_categoria   = $_POST['id_categoria'];
                $descricao      = $_POST['descricao'];
                $menu           = $_POST['menu'];
                $submenu        = $_POST['submenu'];
                $down_url       = geradorTags($nome);
                $data           = implode("-", array_reverse(explode("/", $_POST['data'])));

                $documento = $_FILES['documento'];
                $doc_id = $_POST['doc_id'];

                $dados = array(
                    'nome'      => $nome,
                    'usuario'   => $_SESSION['usuario_id'],
                    'id_categoria' => $id_categoria,
                    'descricao' => $descricao,
                    'menu'      => $menu,
                    'submenu'   => $submenu,
                    'down_url'       => $down_url,
                    'data'          =>$data, 
                );
                if ($action == "adicionar") {
                    $sql = "INSERT INTO cadastro_downloads SET " . bindFields($dados);
                    $stmt = $PDO->prepare($sql);

                    if ($stmt->execute($dados)) {
                        $id = $PDO->lastInsertId();
                        //UPLOAD ARQUIVOS        
                        $c = count($documento["name"]);
                        $caminho = "uploads/downloads/";
                        for ($i = 0; $i < $c; $i++) {
                            $nomeArquivo = $_FILES["documento"]["name"][$i];
                            $nomeTemporario = $_FILES["documento"]["tmp_name"][$i];
                            $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);

                            $titulo = $le_titulo[$i];

                            if (!empty($nomeArquivo)) {
                                if (!file_exists($caminho)) {
                                    mkdir($caminho, 0755, true);
                                }
                                $lic_documento  = $caminho;
                                $arq= geradorTags($nomeArquivo); 
                                $lic_documento .= $arq . '.' . $extensao;
                                $lic_documento2 = $arq . '.' . $extensao;

                                move_uploaded_file($nomeTemporario, ($caminho . $arq . '.' . $extensao));
                                $sql = "INSERT INTO cadastro_downloads_documentos (doc_download, doc_arquivo) VALUES (:doc_download, :doc_arquivo)";
                                $stmt = $PDO->prepare($sql);
                                $stmt->bindParam(':doc_arquivo', $lic_documento2);
                                $stmt->bindParam(':doc_download', $id);
                                if ($stmt->execute()) {
                                } else {
                                    $erro = 1;
                                    $err = $stmt->errorInfo();
                                    print_r($err);
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
                    $sql = "UPDATE cadastro_downloads SET " . bindFields($dados) . " WHERE id = :id ";
                    $stmt = $PDO->prepare($sql);
                    $dados['id'] =  $id;
                    if ($stmt->execute($dados)) {
                        $c = count($documento["name"]);
                        $caminho = "uploads/downloads/";
                        for ($i = 0; $i < $c; $i++) {
                            $nomeArquivo = $_FILES["documento"]["name"][$i];
                            $nomeTemporario = $_FILES["documento"]["tmp_name"][$i];
                            $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);

                            if($doc_id[$i] == ''){
                                if (!empty($nomeArquivo)) {
                                    if (!file_exists($caminho)) {
                                        mkdir($caminho, 0755, true);
                                    }
                                    $lic_documento  = $caminho;
                                    $arq= geradorTags($nomeArquivo); 
                                    $lic_documento .= $arq . '.' . $extensao;
                                    $lic_documento2 = $arq . '.' . $extensao;
        
                                    move_uploaded_file($nomeTemporario, ($caminho . $arq . '.' . $extensao));
                                    $sql = "INSERT INTO cadastro_downloads_documentos (doc_download, doc_arquivo) VALUES (:doc_download, :doc_arquivo)";
                                    $stmt = $PDO->prepare($sql);
                                    $stmt->bindParam(':doc_arquivo', $lic_documento2);
                                    $stmt->bindParam(':doc_download', $id);
                                    if ($stmt->execute()) {
                                    } else {
                                        $erro = 1;
                                        $err = $stmt->errorInfo();
                                        print_r($err);
                                    }
                                }
                            }
                            else {

                                $doc = $doc_id[$i]; 

                                if (!empty($nomeArquivo)) {
                                    if (!file_exists($caminho)) {
                                        mkdir($caminho, 0755, true);
                                    }
                                    # EXCLUI ANEXO ANTIGO #
                                    $sql = "SELECT * FROM cadastro_downloads_documentos WHERE doc_id = :doc_id";
                                    $stmt_antigo = $PDO->prepare($sql);
                                    $stmt_antigo->bindParam(':doc_id', $doc);
                                    $stmt_antigo->execute();
                                    $result_antigo = $stmt_antigo->fetch();
                                    $documento_antigo = $result_antigo['down_arquivo'];
                                    unlink($documento_antigo);                                    
                                    
                                    $lic_documento  = $caminho;
                                    $arq= geradorTags($nomeArquivo); 
                                    $lic_documento .= $arq . '.' . $extensao;
                                    $lic_documento2 = $arq . '.' . $extensao;
        
                                    move_uploaded_file($nomeTemporario, ($caminho . $arq . '.' . $extensao));
                                    $sql = "UPDATE cadastro_downloads_documentos SET 
                                    doc_arquivo = :doc_arquivo
                                    WHERE doc_id = :doc_id";
                                    $stmt = $PDO->prepare($sql);
                                    $stmt->bindParam(':doc_arquivo', $lic_documento2);
                                    $stmt->bindParam(':doc_id', $doc);
                                    if ($stmt->execute()) {
                                    } else {
                                        $erro = 1;
                                        $err = $stmt->errorInfo();
                                        print_r($err);
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
                    // PEGA CAMINHO DO ARQUIVO PARA FAZER EXCLUSÃO
                    $sql = "SELECT * FROM cadastro_downloads WHERE id = :id ";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam(':id', $id);
                    if ($stmt->execute()) {
                        $result = $stmt->fetch();
                        $arquivo = $result['arquivo'];
                    }

                    $sql = "DELETE FROM cadastro_downloads WHERE id = :id ";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam(':id', $id);
                    if ($stmt->execute()) {
                        // EXCLUI ARQUIVO DO FTP
                        unlink($arquivo);


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

                $fil_nome = $_REQUEST['fil_nome'];
                $fil_menu = $_REQUEST['men'];
                $fil_submenu = $_REQUEST['sm'];

                if ($fil_nome == '' && $fil_menu == '' && $fil_submenu == '') {
                    $nome_query = " 1 = 1 ";
                } elseif ($fil_nome != '' && $fil_menu == '' && $fil_submenu == '') {
                    $fil_nome1 = "%" . $fil_nome . "%";
                    $nome_query = " (nome LIKE :fil_nome1  ) ";
                } elseif ($fil_nome != '' && $fil_menu != '' && $fil_submenu == '') {
                    $fil_nome1 = "%" . $fil_nome . "%";
                    $nome_query = " (nome LIKE :fil_nome1  AND menu = :fil_menu) ";
                } elseif ($fil_nome == '' && $fil_menu != '' && $fil_submenu !== '') {
                    $fil_nome1 = "%" . $fil_nome . "%";
                    $nome_query = " (menu = :fil_menu AND submenu = :fil_submenu) ";
                } elseif ($fil_nome != '' && $fil_menu != '' && $fil_submenu != '') {
                    $fil_nome1 = "%" . $fil_nome . "%";
                    $nome_query = " (nome LIKE :fil_nome1  AND menu = :fil_menu AND submenu = :fil_submenu) ";
                }

                $sql = "SELECT * FROM cadastro_downloads 
                            LEFT JOIN cadastro_downloads_categorias ON cadastro_downloads_categorias.cat_id = cadastro_downloads.id_categoria
                            LEFT JOIN aux_menu ON aux_menu.men_id = cadastro_downloads.menu
                            LEFT JOIN aux_submenu ON aux_submenu.sm_id = cadastro_downloads.submenu
                            WHERE " . $nome_query . " ORDER BY id DESC
                            LIMIT :primeiro_registro, :num_por_pagina ";
                $stmt = $PDO->prepare($sql);
                $stmt->bindParam(':fil_nome1',     $fil_nome1);
                $stmt->bindParam(':fil_menu',     $fil_menu);
                $stmt->bindParam(':fil_submenu',     $fil_submenu);
                $stmt->bindParam(':primeiro_registro',     $primeiro_registro);
                $stmt->bindParam(':num_por_pagina',     $num_por_pagina);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if ($pagina == "view") {
                    echo "
                            <div class='titulo'> $page  </div>
                            <div id='botoes'>
                                <div class='filtro2'>
                                <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(" . $permissoes["add"] . ",\"cadastro_servicos/add\");'><i class='fas fa-plus'></i></div>

                                    <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_servicos/view'>
                                        <input type='text' name='fil_nome' id='fil_nome' value='$fil_nome' placeholder='Título'>
                                        <select name='men' id='men'></select>                                    
                                        <select name='sm' id='sm'>
                                            <option value='' selected='selected'>Aguardando Menu...</option>
                                        </select>
                                        <input type='submit' value='Filtrar'> 
                                    </form> 

                                </div>           
                            </div>
                            ";
                    if ($rows > 0) {
                        echo "
                                <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                    <tr>
                                        <td class='titulo_first'>Titulo</td>
                                        <td class='titulo_tabela'> Categoria </td>
                                        <td class='titulo_tabela'> Página </td>
                                        <td class='titulo_last' align='right'>Gerenciar</td>
                                    </tr>";
                        $c = 0;
                        while ($result = $stmt->fetch()) {
                            $nome             = $result['nome'];
                            $cat_nome         = $result['cat_nome'];
                            $menu             = $result['men_titulo'];
                            $submenu          = $result['sm_titulo'];
                            $id               = $result['id'];

                            if ($c == 0) {
                                $c1 = "linhaimpar";
                                $c = 1;
                            } else {
                                $c1 = "linhapar";
                                $c = 0;
                            }
                            echo "<tr class='$c1'>
                                                <td>$nome</td>
                                                <td>$cat_nome</td>
                                                <td>$menu > $submenu</td>
                                                <td align=center>
                                                    <div class='g_excluir' onclick=\"
                                                            abreMask(
                                                                'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'cadastro_servicos/view/excluir/$id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                            \">	<i class='far fa-trash-alt'></i>
                                                    </div>
                                                    <div class='g_editar'  title='Editar' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"cadastro_servicos/edit/$id\");'><i class='fas fa-pencil-alt'></i></div>											
                                                </td>
                                            </tr>";
                        }
                        echo "</table>";


                        $variavel = "&men=$fil_menu&sm=$fil_submenu";
                        $cnt = "SELECT COUNT(*) FROM cadastro_downloads WHERE " . $nome_query . "";
                        $stmt = $PDO->prepare($cnt);
                        $stmt->bindParam(':fil_menu',     $fil_menu);
                        $stmt->bindParam(':fil_submenu',     $fil_submenu);        

                        include("../core/mod_includes/php/paginacao.php");
                    } else {
                        echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                    }
                }

                if ($pagina == 'add') {
                    echo "	
                        <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_servicos/view/adicionar'>
                            <div class='titulo'> $page &raquo; Adicionar  </div>
                            <ul class='nav nav-tabs'>
                                <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                                <li><a data-toggle='tab' href='#documentos'>Documentos</a></li>
                            </ul>
                            <div class='tab-content'>

                                <div id='dados_gerais' class='tab-pane fade in active'>
                                    <label>Título:</label> <input name='nome' id='nome' placeholder='Título' class='obg'>
                                    <p><label>Categoria:</label> <select name='id_categoria' id='id_categoria'>
                                        <option value=''>Selecione </option>
                                    ";
                                        $sql = "SELECT * FROM cadastro_downloads_categorias ORDER BY cat_nome";
                                        $stmt = $PDO->prepare($sql);
                                        $stmt->execute();
                                        $rows = $stmt->rowCount();
                                        if ($rows > 0) {
                                            while ($result = $stmt->fetch()) {
                                                echo "<option value='" . $result['cat_id'] . "'> " . $result['cat_nome'] . "</option>";
                                            }
                                        }
                                        echo "
                                        <option value='nova_categoria'><b>Nova Categoria*</b> </option>
                                    </select>
                                    <p id='cat' style='display:none'><label>Nova Categoria:</label> <input name='cat_nome' id='cat_nome' placeholder='Nova Categoria' >

                                    <p><label>Menu*:</label>
                                    <select name='menu' id='menu' class='menu'>
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
                                    <select name='submenu' id='submenu'>
                                        <option value='0' selected='selected'>Aguardando Menu...</option>
                                    </select>
        
                                    <p><label>Descrição:</label> <div class='textarea'><textarea  name='descricao' id='descricao' placeholder='Descrição'></textarea></div>
                                    <p><label>Data:</label> <input name='data' id='data' placeholder='Data' class='obg'>

                                </div>
                                
                                <div id='documentos' class='tab-pane fade in'>
                                    <table class='doc'>
                                        <tr>
                                            <th width='90%'> Documento </th>
                                            <th width='10%'> Excluir </th>
                                        </tr>
                                        <tr>
                                            <td style='width:90%'><input type='file' name='documento[]' id='documento[]' style='width:100%' ></td>
                                            <td style='width:10%; text-align:center'> <i class='fas fa-times' id='remover'></i> </td>                                        
                                        </tr>
                                    </table>

                                    <center onclick='addDoc()' style='font-size:18px; font-weight:bold; cursor:pointer'>Adicionar </center> <br><br>

                                </div>

                                <center>
                                <div id='erro' align='center'>&nbsp;</div>
                                <input type='submit' id='bt_cadastro_servicos' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_servicos/view'; value='Cancelar'/></center>
                                </center>
                            </div>
                        </form>
                        ";
                }

                if ($pagina == 'edit') {
                    $sql = "SELECT * FROM cadastro_downloads 
                            LEFT JOIN cadastro_downloads_categorias ON cadastro_downloads_categorias.cat_id = cadastro_downloads.id_categoria
                            LEFT JOIN aux_menu ON aux_menu.men_id = cadastro_downloads.menu
                            LEFT JOIN aux_submenu ON aux_submenu.sm_id = cadastro_downloads.submenu                       
                            WHERE id = :id";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                    $rows = $stmt->rowCount();
                    if ($rows > 0) {
                        $result = $stmt->fetch();
                        $arquivo = $result['arquivo'];
                        $descricao = $result['descricao'];
                        $data = date("d/m/Y", strtotime($result['data']));

                        echo "
                            <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_servicos/view/editar/$id'>
                                <div class='titulo'> $page &raquo; Editar </div>
                                <ul class='nav nav-tabs'>
                                    <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                                    <li><a data-toggle='tab' href='#documentos'>Documentos</a></li>
                                </ul>
                                <div class='tab-content'>
                                    <div id='dados_gerais' class='tab-pane fade in active'>
                                        <label>Título:</label> <input name='nome' id='nome' value='" . $result['nome'] . "' placeholder='Título' class='obg'>
                                        <p><label>Categoria:</label> <select name='id_categoria' id='id_categoria'>
                                        <option value ='" . $result['cat_id'] . "'>" . $result['cat_nome'] . "</option>";
                        $sqlc = "SELECT * FROM cadastro_downloads_categorias";
                        $stmtc = $PDO->prepare($sqlc);
                        $stmtc->execute();
                        $rowsc = $stmtc->rowCount();
                        if ($rowcs > 0) {
                            while ($resultc = $stmtc->fetch()) {
                                echo "<option value='" . $resultc['cat_id'] . "'> " . $result['cat_nome'] . "</option>";
                            }
                        }
                        echo "
                        <option value='nova_categoria'><b>Nova Categoria*</b> </option>
                        </select>
                        <p id='cat' style='display:none'><label>Nova Categoria:</label> <input name='cat_nome' id='cat_nome' placeholder='Nova Categoria' >


                                        <p><label>Menu:</label><select name='menu' id='menu'>
                                            <option value ='" . $result['men_id'] . "'>" . $result['men_titulo'] . "</option>";
                        $sqlm = "SELECT * FROM aux_menu";
                        $stmtm = $PDO->prepare($sqlm);
                        $stmtm->execute();
                        $rowsm = $stmtm->rowCount();
                        if ($rowsm > 0) {
                            while ($resultm = $stmtm->fetch()) {
                                echo "<option value='" . $resultm['men_id'] . "'> " . $resultm['men_titulo'] . "</option>";
                            }
                        }
                        echo "
                                        </select>
                                        <p><label>Submenu*:</label>
                                        <select name='submenu' id='submenu'>                                        
                                        <option value ='" . $result['sm_id'] . "'>" . $result['sm_titulo'] . "</option>";
                                        $sqlsm = "SELECT * FROM aux_submenu
                                        WHERE sm_menu = :sm_menu";
                                        $stmtsm = $PDO->prepare($sqlsm);
                                        $stmtsm->bindParam(':sm_menu', $result['men_id']);
                                        $stmtsm->execute();
                                        $rowssm = $stmtsm->rowCount();
                                        if ($rowssm > 0) {
                                            while ($resultsm = $stmtsm->fetch()) {
                                                echo "<option value='" . $resultsm['sm_id'] . "'> " . $resultsm['sm_titulo'] . "</option>";
                                            }
                                        }
                                        echo "
                                        </select>  
                                        <p><label>Descrição:</label> <div class='textarea'><textarea  name='descricao' id='descricao' placeholder='Descrição'>$descricao</textarea></div>
                                        <p><label>Data:</label> <input name='data' id='data' placeholder='Data' value='$data' class='obg'>

                                    </div>

                                    <div id='documentos' class='tab-pane fade in'>
                                        <table class='doc'>
                                            <tr>
                                                <th width='90%'> Documento </th>
                                                <th width='10%'> Excluir </th>
                                            </tr>";
                                            $sql3 = "SELECT * FROM cadastro_downloads_documentos WHERE doc_download = :doc_download";
                                            $stmt3 = $PDO->prepare($sql3);
                                            $stmt3->bindParam(':doc_download', $id);
                                            $stmt3->execute();
                                            $rows3 = $stmt3->rowCount();
                                            if ($rows3 > 0) {
                                                while ($result3 = $stmt3->fetch()) {
                                                    echo "                                            
                                                        <tr id='doc" . $result3['doc_id'] . "'>
                                                            <td style='width:90%'>
                                                                <input type='hidden' name='doc_id[]' id='doc_id[]' value='" . $result3['doc_id'] . "'>
                                                                <input type='file' name='documento[]' id='documento[]' style='width:100%' >
                                                                <br><br><p><a href='uploads/downloads/" . $result3['doc_arquivo'] . "' target='_blank'><i class='fas fa-file-alt' style='font-size:20px'></i> - " . $result3['doc_arquivo'] . "</a> 
                                                            </td>
                                                            <td style='width:10%; text-align:center'> <i class='fas fa-times' onclick='remover(" . $result3['doc_id'] . ")'></i> </td>                                        
                                                        </tr>
                                                    ";
                                                }
                                            }
                    
                                        echo"</table>

                                        <center onclick='addDoc()' style='font-size:18px; font-weight:bold; cursor:pointer'>Adicionar </center> <br><br>

                                    </div>

                                    <center>
                                    <div id='erro' align='center'>&nbsp;</div>
                                    <input type='submit' id='bt_cadastro_servicos' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                    <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_servicos/view'; value='Cancelar'/></center>
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
    $("#id_categoria").change(function() {
        var valor = $(this).val();
        if (valor == 'nova_categoria') {
            $('#cat').css('display', 'block');
        }
        else {
            $('#cat').css('display', 'none');
  
        }

    });

    $("#cat_nome").change(function() {
        var valor = $(this).val();
        $.post("../carrega_conteudo.php", {
                pagina: 'cadastra_categoria_servicos',
                categoria: valor
            },
            function(dados) {
                if (dados != '') {
                    if(dados == 'false'){
                        abreMask('Essa categoria já foi cadastrada anteriormente! <br>Por favor verifique a listagem <br><br>' +
                        '<input value=\' OK \' type=\'button\' class=\'close_janela\'>');
                    }else {
                        $('#cat').css('display', 'none');
                        $("#id_categoria").append('<option value="'+dados+'" selected>'+valor+'</option>');
                    }
                }
            }
        )
    });

    function addDoc() {
        var titulo = $("#ttl_doc").val();
        var doc = $("#doc").val();
        $('.doc').append("<tr><td style='width:90%'><input type='hidden' name='doc_id[]' id='doc_id[]' value=''><input type='file' name='documento[]' id='documento[]' style='width:100%' ></td><td style='width:10%; text-align:center'> <i class='fas fa-times' id='remover'></i> </td></tr>");
    }

    function remover(doc) {
        abreMask('Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>' +
            '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=rm(' + doc + ');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
            '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
    };

    function rm(doc) {
        if (doc == '') {} else {
            $.post("excluir.php", {
                    action: 'excluir_download',
                    doc_id: doc
                },
                function() {
                    $('tr#doc'+doc).remove();
                }
            )
        }
    }

    $(".doc").on("click", "#remover", function(e) {
        $(this).closest('tr').remove();
    });


</script>