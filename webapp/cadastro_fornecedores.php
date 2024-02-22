<?php
$pagina_link = 'cadastro_fornecedores';
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");
session_start();
$page = "<a href='cadastro_fornecedores/view'>Cadastro de Fornecedores</a>";

?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.ofa_rg/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.ofa_rg/1999/xhtml">

<head>
    <?php
    include_once("header.php");
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {

            $("select[name=categoria_empresa]").html('<option value="">Carregando...</option>');
            $.post("carrega_menu.php", {
                    action: 'categoria_empresa'
                },
                function(valor) {
                    $("select[name=categoria_empresa]").html(valor);
                }
            )
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

                $field1         = $_POST['field1'];
                $field2         = $_POST['field2'];
                $email          = $_POST['email'];
                $hash_email     = hash('sha512', $_POST['email']);
                $id_ramo_atuacao       = $_POST['id_ramo_atuacao'];
                $exibir       = $_POST['exibir'];

                $fa_responsavel             = $_POST['fa_responsavel'];
                $fa_celular                 = $_POST['fa_celular'];
                $fa_telefone                = $_POST['fa_telefone'];
                $fa_cnpj                    = $_POST['fa_cnpj'];
                $fa_cpf                     = $_POST['fa_cpf'];
                $fa_objetivo_licitante      = $_POST['fa_objetivo_licitante'];
                $fa_observacao_licitante   = $_POST['fa_observacao_licitante'];

                if ($_POST['data_emissao_crc']==''){
                    $fa_emissao_crc             = NULL;

                }else {
                    $fa_emissao_crc             = implode("-", array_reverse(explode("/", $_POST['data_emissao_crc'])));

                }
                $fa_status_crc              = 0;
                $fa_cod_licitante           = $_POST['fa_cod_licitante'];
                $fa_crc_numero_licitante    = $_POST['fa_crc_numero_licitante'];
                $fa_ie                      = $_POST['fa_ie'];
                $fa_tipo_fornecedor         = $_POST['fa_tipo_fornecedor'];
                $fa_certificado_licitante   = $_POST['fa_certificado_licitante'];

                $zip        = $_POST['zip'];
                $address       = $_POST['address'];
                $number       = $_POST['number'];
                $bairro       = $_POST['bairro'];
                $complement       = $_POST['complement'];
                $city       = $_POST['city'];
                $state_id       = $_POST['state_id'];

                $tipo_documento       = $_POST['tipo_documento'];

                $data_documento = $_POST['data_documento']; 
                $data_validade =  $_POST['data_validade'];
                
                $dados_gerais = array(
                    'field1' => $field1,
                    'field2' => $field2,  
                    'email' => $email,  
                    'hash_email' => $hash_email,
                    'id_ramo_atuacao' => $id_ramo_atuacao,  
                    'status' => $exibir,  
                );

                if ($action == "adicionar") {
                    $sql_geral = "INSERT INTO fornecedores SET " . bindFields($dados_gerais);
                    $stmt_geral = $PDO->prepare($sql_geral);

                    if ($stmt_geral->execute($dados_gerais)) {

                        $id_fornecedor = $PDO->lastInsertId();

                        $dados_endereco = array(
                            'user_id' => $id_fornecedor, 
                            'zip' => $zip,
                            'address' => $address,  
                            'bairro' => $bairro,  
                            'number' => $number,  
                            'complement' => $complement,  
                            'city' => $city,  
                            'state_id' => $state_id,  
                        );

                        $sql_endereco = "INSERT INTO fornecedores_endereco SET " . bindFields($dados_endereco);
                        $stmt_endereco = $PDO->prepare($sql_endereco);   
                        $stmt_endereco->execute($dados_endereco); 

                        $dados_atributos = array(
                            'fa_responsavel' => $fa_responsavel, 
                            'fa_celular' => $fa_celular, 
                            'fa_telefone' => $fa_telefone, 
                            'fa_cnpj' => $fa_cnpj, 
                            'fa_cpf' => $fa_cpf, 
                            'fa_objetivo_licitante' => $fa_objetivo_licitante, 
                            'fa_observacao_licitante' => $fa_observacao_licitante, 
                            'fa_emissao_crc' => $fa_emissao_crc, 
                            'fa_status_crc' => $fa_status_crc, 
                            'fa_cod_licitante' => $fa_cod_licitante, 
                            'fa_crc_numero_licitante' => $fa_crc_numero_licitante, 
                            'fa_ie' => $fa_ie, 
                            'fa_tipo_fornecedor' => $fa_tipo_fornecedor, 
                            'fa_certificado_licitante' => $fa_certificado_licitante, 
                            'fa_fornecedor' => $id_fornecedor, 
                        );

                        $sql_atributos = "INSERT INTO fornecedor_atributos SET ".bindFields($dados_atributos);
                        $stmt_atributos = $PDO->prepare($sql_atributos); 
                        $stmt_atributos->execute($dados_atributos);

                        $d = count($data_documento);
                        for ($z = 0; $z < $d; $z++) {
                            if(!empty($data_documento[$z])){
                                $dd[]=$data_documento[$z]; 
                            }

                            if(!empty($data_validade[$z])){
                                $dv[]=$data_validade[$z]; 
                            }
                            }
                        $c = count($tipo_documento);
                        for ($i = 0; $i < $c; $i++) {
                            $id_documento = $tipo_documento[$i];
                            $data = implode("-", array_reverse(explode("/", $dd[$i])));
                            $validade = implode("-", array_reverse(explode("/", $dv[$i])));

                            $sql = "INSERT INTO licitacao_documentacao (id_licitante, id_documento, validade, data) VALUES (:id_licitante, :id_documento, :validade, :data)";
                            $stmt = $PDO->prepare($sql);
                            $stmt->bindParam(':id_licitante', $id_fornecedor);
                            $stmt->bindParam(':id_documento', $id_documento);
                            $stmt->bindParam(':data', $data);
                            $stmt->bindParam(':validade', $validade);
                            if ($stmt->execute()) {
        
                            } else {
                                $erro = 1;
                                $err = $stmt->errorInfo();
                                echo "Erro Documento"; 
                                print_r($err);
                            }                                    
                        }

                        ?>
                            <script>
                                abreMask('Operação realizada com sucesso! <br><br>' +
                                '<input value=\' Voltar \' type=\'button\' class=\'close_janela\' onclick=sucess(<?php echo $id_fornecedor?>);>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                                function sucess(id){window.location.replace('cadastro_fornecedores/edit/'+id)};
                            </script>
                        <?php

                    } else {
                        $err = $stmt->errorInfo();
                        echo "Erro ao cadastrar Fornecedor"; 

                        print_r($err);
                    ?>
                         <script>
                            mensagem("X", "<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                        </script>
                     <?php
                    }
                }

                if ($action == 'editar') {
                    $sql = "UPDATE fornecedores SET " . bindFields($dados_gerais) . " WHERE id = :id ";
                    $stmt = $PDO->prepare($sql);
                    $dados_gerais['id'] =  $id;
                    if($stmt->execute($dados_gerais)){
                        $dados_endereco = array(
                            'zip' => $zip,
                            'address' => $address,  
                            'bairro' => $bairro,  
                            'number' => $number,  
                            'complement' => $complement,  
                            'city' => $city,  
                            'state_id' => $state_id,  
                        );
    
                        $sql_ce = "SELECT * FROM fornecedores_endereco WHERE user_id = :user_id";
                        $stmt_ce = $PDO->prepare($sql_ce);  
                        $stmt_ce->bindParam(':user_id', $id);
                        $stmt_ce->execute();
                        $rows_ce = $stmt_ce->rowCount();
    
                        if($rows_ce > 0){
                            $sql_endereco = "UPDATE fornecedores_endereco SET " . bindFields($dados_endereco) ."WHERE user_id = :user_id";
                            $stmt_endereco = $PDO->prepare($sql_endereco);   
                            $dados_endereco['user_id'] =  $id; 
                            $stmt_endereco->execute($dados_endereco);
    
                        }else {   
                            $dados_endereco['user_id'] =  $id;
                            $sql_endereco2 = "INSERT INTO fornecedores_endereco SET " . bindFields($dados_endereco);
                            $stmt_endereco2 = $PDO->prepare($sql_endereco2);                           
                            $stmt_endereco2->execute($dados_endereco);
                        }
                        
                        $dados_atributos = array(
                            'fa_responsavel' => $fa_responsavel, 
                            'fa_celular' => $fa_celular, 
                            'fa_telefone' => $fa_telefone, 
                            'fa_cnpj' => $fa_cnpj, 
                            'fa_cpf' => $fa_cpf, 
                            'fa_objetivo_licitante' => $fa_objetivo_licitante, 
                            'fa_observacao_licitante' => $fa_observacao_licitante, 
                            'fa_emissao_crc' => $fa_emissao_crc, 
                            'fa_status_crc' => $fa_status_crc, 
                            'fa_cod_licitante' => $fa_cod_licitante, 
                            'fa_crc_numero_licitante' => $fa_crc_numero_licitante, 
                            'fa_ie' => $fa_ie, 
                            'fa_tipo_fornecedor' => $fa_tipo_fornecedor, 
                            'fa_certificado_licitante' => $fa_certificado_licitante, 
                        );
                        $sql_atributos = "UPDATE fornecedor_atributos SET ".bindFields($dados_atributos). "WHERE fa_fornecedor = :fa_fornecedor";
                        $stmt_atributos = $PDO->prepare($sql_atributos); 
                        $dados_atributos['fa_fornecedor'] =  $id; 
                        $stmt_atributos->execute($dados_atributos);
    
                        $sql_dd="DELETE FROM licitacao_documentacao WHERE id_licitante = :id_licitante"; 
                        $stmt_dd = $PDO->prepare($sql_dd);
                        $stmt_dd->bindParam(':id_licitante', $id);
                        $stmt_dd->execute();
                        $d = count($data_documento);
                        for ($z = 0; $z < $d; $z++) {
                            if(!empty($data_documento[$z])){
                                $dd[]=$data_documento[$z]; 
                            }
    
                            if(!empty($data_validade[$z])){
                                $dv[]=$data_validade[$z]; 
                            }
                        }
                        $c = count($tipo_documento);
                        for ($i = 0; $i < $c; $i++) {
                            $id_documento = $tipo_documento[$i];
                            $data = implode("-", array_reverse(explode("/", $dd[$i])));
                            $validade = implode("-", array_reverse(explode("/", $dv[$i])));
    
                            $sqlid = "INSERT INTO licitacao_documentacao (id_licitante, id_documento, validade, data) VALUES (:id_licitante, :id_documento, :validade, :data)";
                            $stmtid = $PDO->prepare($sqlid);
                            $stmtid->bindParam(':id_licitante', $id);
                            $stmtid->bindParam(':id_documento', $id_documento);
                            $stmtid->bindParam(':data', $data);
                            $stmtid->bindParam(':validade', $validade);
                            $stmtid->execute();
                        }
                        ?>
                            <script>
                                abreMask('Operação realizada com sucesso! <br><br>' +
                                '<input value=\' Voltar \' type=\'button\' class=\'close_janela\' onclick=sucess(<?php echo $id?>);>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                                function sucess(id){window.location.replace('cadastro_fornecedores/edit/'+id)};
                            </script>
                        <?php

    
                    }else {

                        ?>
                            <script>
                                abreMask('Erro ao realizar operação! <br><br>' +
                                '<input value=\' Voltar \' type=\'button\' class=\'close_janela\' onclick=sucess(<?php echo $id?>);>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                                function sucess(id){window.location.replace('cadastro_fornecedores/edit/'+id)};
                            </script>
                        <?php
                    }                       
                }

                if ($action == 'excluir') {
                    // PEGA CAMINHO DO ARQUIVO PARA FAZER EXCLUSÃO
                    $sql = "SELECT * FROM fornecedores WHERE id = :id ";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam(':id', $id);
                    if ($stmt->execute()) {
                        $result = $stmt->fetch();
                        $arquivo = $result['lic_documento'];
                    }


                    $sql = "DELETE FROM fornecedores WHERE id = :id ";
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
                $fil_categoria = $_REQUEST['categoria_empresa'];

                if ($fil_nome == '' && $fil_categoria == '') {
                    $nome_query = " 1 = 1 ";
                } elseif ($fil_nome != '' && $fil_categoria == '') {
                    $fil_nome1 = "%" . $fil_nome . "%";
                    $nome_query = " (field1 LIKE :fil_nome1  ) ";
                } elseif ($fil_nome != '' && $fil_categoria != '') {
                    $fil_nome1 = "%" . $fil_nome . "%";
                    $nome_query = " (field1 LIKE :fil_nome1  AND fra_id = :fil_categoria) ";
                } elseif ($fil_nome == '' && $fil_categoria != '') {
                    $fil_nome1 = "%" . $fil_nome . "%";
                    $nome_query = " (fra_id = :fil_categoria) ";
                }

                $sql = "SELECT * FROM fornecedores       
                            LEFT JOIN fornecedores_ramo_atuacao ON fornecedores_ramo_atuacao.fra_id = fornecedores.id_ramo_atuacao
                            LEFT JOIN licitacao_documentacao ON licitacao_documentacao.id_licitante = fornecedores.id
                            WHERE " . $nome_query . " AND licitacao_documentacao.ldc_id is not null 
                            GROUP BY fornecedores.id
                            ORDER BY creation  DESC
                            LIMIT :primeiro_registro, :num_por_pagina ";
                $stmt = $PDO->prepare($sql);                
                $stmt->bindParam(':fil_nome1',     $fil_nome1);
                $stmt->bindParam(':fil_categoria',     $fil_categoria);
                $stmt->bindParam(':primeiro_registro',     $primeiro_registro);
                $stmt->bindParam(':num_por_pagina',     $num_por_pagina);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if ($pagina == "view") {
                    echo "
                        <div class='titulo'> $page  </div>
                        <div id='botoes'>
                            <div class='filtro2'>
                            <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(" . $permissoes["add"] . ",\"cadastro_fornecedores/add\");'><i class='fas fa-plus'></i></div>

                                <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_fornecedores/view'>
                                    <input type='text' name='fil_nome' id='fil_nome' value='$fil_nome' placeholder='Título'>
                                    <select name='categoria_empresa' id='categoria_empresa'>
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
                                        <td class='titulo_first'>Empresa</td>
                                        <td class='titulo_tabela'> Ramo de Atuacão </td>
                                        <td class='titulo_tabela'> Data de Criação </td>
                                        <td class='titulo_last' align='right'>Gerenciar</td>
                                    </tr>";
                        $c = 0;
                        while ($result = $stmt->fetch()) {
                            $data       = date("d/m/Y", strtotime($result['creation']));
                            $nome         = $result['field1'];
                            if($result['fra_id'] == 0 || $result['fra_id'] == ''){
                                $cat_nome   = 'Não Consta';
                            }else {
                                $cat_nome   = $result['fra_descricao'];
                            }

                            $id     = $result['id'];

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
                                                <td>$data</td>
                                                <td align=center>
                                                    <div class='g_excluir' onclick=\"
                                                            abreMask(
                                                                'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(" . $permissoes["excluir"] . ",\'cadastro_fornecedores/view/excluir/$id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                            \">	<i class='far fa-trash-alt'></i>
                                                    </div>
                                                    <div class='g_editar'  title='Editar' onclick='verificaPermissao(" . $permissoes["edit"] . ",\"cadastro_fornecedores/edit/$id\");'><i class='fas fa-pencil-alt'></i></div>											
                                                </td>
                                            </tr>";
                        }
                        echo "</table>";
                        $variavel = "&fil_nome=$fil_nome&fil_categoria=$fil_categoria";
                        $cnt = "SELECT COUNT(contador) from (SELECT COUNT(*) as contador FROM fornecedores       
                        LEFT JOIN fornecedores_ramo_atuacao ON fornecedores_ramo_atuacao.fra_id = fornecedores.id_ramo_atuacao
                        LEFT JOIN licitacao_documentacao ON licitacao_documentacao.id_licitante = fornecedores.id
                        WHERE " . $nome_query . " AND licitacao_documentacao.ldc_id is not null 
                        GROUP BY fornecedores.id) src";
                        $stmt = $PDO->prepare($cnt);
                        $stmt->bindParam(':fil_nome1',     $fil_nome1);
                        $stmt->bindParam(':fil_categoria',     $fil_categoria);
                        include("../core/mod_includes/php/paginacao.php");
                    } else {
                        echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                    }
                }

                if ($pagina == 'add') {
                    echo "	
                        <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_fornecedores/view/adicionar'>
                            <div class='titulo'> $page &raquo; Adicionar  </div>
                            <ul class='nav nav-tabs'>
                                <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                                <li><a data-toggle='tab' href='#endereco'>Endereço</a></li>
                                <li><a data-toggle='tab' href='#informacoes'>Informações</a></li>
                                <li><a data-toggle='tab' href='#documentacao'>Documentos</a></li>
                            </ul>
                            <div class='tab-content'>

                                <div id='dados_gerais' class='tab-pane fade in active'>
                                    <p><label>CNPJ:</label> <input name='fa_cnpj' id='fa_cnpj' placeholder='CNPJ'>
                                    <p><label>CPF:</label> <input name='fa_cpf' id='fa_cpf' placeholder='CPF'>
                                    <p><label>Nome Fantasia*:</label> <input name='field1' id='field1' placeholder='Nome Fantasia' class='obg' >
                                    <p><label>Razão Social*:</label> <input name='field2' id='field2' placeholder='Razão Social' class='obg' >
                                    <p><label>E-mail:</label> <input name='email' id='email' placeholder='E-mail' class='obg' >
                                    <p><label>Pessoa para Contato:</label> <input name='fa_responsavel' id='fa_responsavel' placeholder='Pessoa para Contato' >
                                    <p><label>Celular:</label> <input name='fa_celular' id='fa_celular' placeholder='Celular' >
                                    <p><label>Telefone:</label> <input name='fa_telefone' id='fa_telefone' placeholder='Telefone' >
                                    <p><label>Ramo Atuação:</label> 
                                    <select id='id_ramo_atuacao' name='id_ramo_atuacao'>    
                                        <option value='534'> Não Consta </option>";
                    $sql = "SELECT * FROM fornecedores_ramo_atuacao";
                    $stmt = $PDO->prepare($sql);
                    $stmt->execute();
                    $rows = $stmt->rowCount();
                    if ($rows > 0) {
                        while ($result = $stmt->fetch()) {
                            echo "<option value='" . $result['fra_id'] . "'>" . $result['fra_descricao'] . "</option>";
                        }
                    }
                    echo "</select> 
                                    <p><label>Status:</label> <input type='radio' name='exibir' value='1' > Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type='radio' name='exibir' value='0' checked> Inativo<br>		
                                </div>

                                <div id='endereco' class='tab-pane fade in'>
                                    <p><label>CEP:</label> <input type='text' name='zip' id='zip'  placeholder='CEP' >
                                    <div class='comp_end'>
                                    <p><label>Endereço:</label> <input type='text' name='address' id='address'  placeholder='Endereço'>
                                    <p><label>Número:</label> <input type='text' name='number' id='number'  placeholder='Número'>
                                    <p><label>Bairro:</label> <input type='text' name='bairro' id='bairro'  placeholder='Bairro'>
                                    <p><label>Complemento:</label> <input type='text' name='complement' id='complement'  placeholder='Complemento'>
                                    <p><label>Cidade:</label> <input type='text' name='city' id='city'  placeholder='Cidade'>
                                    <p><label>Estado:</label> <select id='state_id' name='state_id'>
                                    <option value='0'>Selecione </option> ";
                                        $sql = "SELECT * FROM end_uf";
                                        $stmt = $PDO->prepare($sql);
                                        $stmt->execute();
                                        $rows = $stmt->rowCount();
                                        if ($rows > 0) {
                                            while ($result = $stmt->fetch()) {
                                                echo "<option value='" . $result['uf_id'] . "'>" . $result['uf_nome'] . "</option>";
                                            }
                                        }

                                        echo "</select>
                                    </div>
                                </div>

                                <div id='informacoes' class='tab-pane fade in'>
                                    <p><label>Objetivo*:</label> <div class='textarea'><textarea  name='fa_objetivo_licitante' id='fa_objetivo_licitante'></textarea></div>
                                    <p><label>Observação*:</label> <div class='textarea'><textarea  name='fa_observacao_licitante' id='fa_observacao_licitante'></textarea></div>
                                    <p><label>Certificado:</label> <input type='text' name='fa_certificado_licitante' id='fa_certificado_licitante'  placeholder='Certificado'>
                                    <p><label>Emissão do CRC:</label> <input type='text' name='data_emissao_crc' id='data_emissao_crc'>
                                    <p><label>Tipo fornecedor:</label> <select name='fa_tipo_fornecedor' id='fa_tipo_fornecedor'>
                                        <option value=''>Nenhum </option>
                                        <option value='1'>Servicos </option>
                                        <option value='2'>Produtos </option>
                                        <option value='3'>Produtos e Servicos </option>
                                    </select>
                                </div>


                                <div id='documentacao' class='tab-pane fade in'>
                                    <table class='doc'> 
                                        <tr>
                                            <th>Selecionar</th>
                                            <th>Documento</th>
                                            <th>Data de emissão</th>
                                            <th>Validade</th>
                                        </tr>";
                                            $sql = "SELECT * FROM licitacao_documentos";
                                            $stmt = $PDO->prepare($sql);
                                            $stmt->execute();
                                            $rows = $stmt->rowCount();
                                            if ($rows > 0) {
                                                while ($result = $stmt->fetch()) {
                                                    echo "<tr>
                                                            <td><center><input type='checkbox' name='tipo_documento[]' id='tipo_documento[]' value='".$result['ld_id']."'></center></td>
                                                            <td>".$result['titulo']."</td>
                                                            <td><input name='data_documento[]' name='data_documento[]'></td>
                                                            <td><input name='data_validade[]' name='data_validade[]'></td>
                                                        </tr>                                                  
                                                    ";
                                                }
                                            }


                                    echo "</table>

                                </div>

                                <center>
                                    <div id='erro' align='center'>&nbsp;</div>
                                    <input type='submit' id='bt_cadastro_fornecedores' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                    <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_fornecedores/view'; value='Cancelar'/>&nbsp;&nbsp;&nbsp;&nbsp;
                                </center>
                            </div>
                        </form>
                        ";
                }

                if ($pagina == 'edit') {
                    $sql = "SELECT * FROM fornecedores 
                        LEFT JOIN fornecedores_endereco ON fornecedores_endereco.user_id = fornecedores.id
                        LEFT JOIN fornecedores_ramo_atuacao ON fornecedores_ramo_atuacao.fra_id = fornecedores.id_ramo_atuacao
                        LEFT JOIN fornecedor_atributos ON fornecedor_atributos.fa_fornecedor = fornecedores.id
                        LEFT JOIN end_uf ON end_uf.uf_id = fornecedores_endereco.state_id
                        WHERE fornecedores.id = :id";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                    $rows = $stmt->rowCount();
                    if ($rows > 0) {
                        $result = $stmt->fetch();

                        
                            if($result['fa_emissao_crc'] == ''){
                                $data = '00/00/000';
                            }else {
                               $data = date("d/m/Y", strtotime($result['fa_emissao_crc']));
                            }

                           echo "
                            <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_fornecedores/view/editar/$id'>
                                <div class='titulo'> $page &raquo; Editar  </div>
                                    <ul class='nav nav-tabs'>
                                        <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                                        <li><a data-toggle='tab' href='#endereco'>Endereço</a></li>
                                        <li><a data-toggle='tab' href='#informacoes'>Informações</a></li>
                                        <li><a data-toggle='tab' href='#documentacao'>Documentos</a></li>
                                    </ul>
                                    <div class='tab-content'>

                                        <div id='dados_gerais' class='tab-pane fade in active'>
                                            <p><label>CNPJ:</label> <input name='fa_cnpj' id='fa_cnpj' placeholder='CNPJ' value='".$result['fa_cnpj']."' >
                                            <p><label>CPF:</label> <input name='fa_cpf' id='fa_cpf' placeholder='CPF' value='".$result['fa_cpf']."' >
                                            <p><label>Nome Fantasia*:</label> <input name='field1' id='field1' placeholder='Nome Fantasia' value='" . $result['field1'] . "' >
                                            <p><label>Razão Social*:</label> <input name='field2' id='field2' placeholder='Razão Social' value='" . $result['field2'] . "'>
                                            <p><label>E-mail:</label> <input name='email' id='email' placeholder='E-mail' value='" . $result['email'] . "' >
                                            <p><label>Pessoa para Contato:</label> <input name='fa_responsavel' id='fa_responsavel' placeholder='Pessoa para Contato'  value='".$result['fa_responsavel']."'>
                                            <p><label>Celular:</label> <input name='fa_celular' id='fa_celular' placeholder='Celular' value='".$result['fa_celular']."' >
                                            <p><label>Telefone:</label> <input name='fa_telefone' id='fa_telefone' placeholder='Telefone' value='".$result['fa_telefone']."' >";
                        }

                        echo "
                        <p><label>Ramo Atuação:</label> 
                        <select id='id_ramo_atuacao' name='id_ramo_atuacao'>";
                        if($result['fra_id'] == 0 || $result['fra_id'] == ''){
                            echo "<option value='534'> Não Consta </option>";
                        }
                        else {
                            echo "<option value='".$result['fra_id']."'> ".$result['fra_descricao']." </option>";
                        }
                        $sql_atuacao = "SELECT * FROM fornecedores_ramo_atuacao";
                        $stmt_atuacao = $PDO->prepare($sql_atuacao);
                        $stmt_atuacao->execute();
                        $rows_atuacao = $stmt_atuacao->rowCount();
                        if ($rows_atuacao > 0) {
                            while ($result_atuacao = $stmt_atuacao->fetch()) {
                                echo "<option value='" . $result_atuacao['fra_id'] . "'>" . $result_atuacao['fra_descricao'] . "</option>";
                            }
                        }
                        echo "</select> 
                                <p><label>Status:</label>";                                
                                if($result['status'] == 1){
                                    echo"                                
                                        <input type='radio' name='exibir' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type='radio' name='exibir' value='0'> Inativo<br>
                                    ";
                                }else {
                                    echo"                                
                                        <input type='radio' name='exibir' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type='radio' name='exibir' value='0' checked> Inativo<br>
                                    ";
                                }

                            echo"</div>

                            <div id='endereco' class='tab-pane fade in'>
                                <p><label>CEP:</label> <input type='text' name='zip' id='zip'  placeholder='CEP' value='".$result['zip']."'>
                                <div class='comp_end'>
                                    <p><label>Endereço:</label> <input type='text' name='address' id='address'  placeholder='Endereço' value='".$result['address']."'>
                                    <p><label>Número:</label> <input type='text' name='number' id='number'  placeholder='Número' value='".$result['number']."'>
                                    <p><label>Bairro:</label> <input type='text' name='bairro' id='bairro'  placeholder='Bairro' value='".$result['bairro']."'>
                                    <p><label>Complemento:</label> <input type='text' name='complement' id='complement'  placeholder='Complemento' value='".$result['complement']."'>
                                    <p><label>Cidade:</label> <input type='text' name='city' id='city'  placeholder='Cidade' value='".$result['city']."'>
                                    <p><label>Estado:</label> <select id='state_id' name='state_id'>
                                    <option value='".$result['uf_id']."'>".$result['uf_nome']." </option> ";
                                            $sql_estado = "SELECT * FROM end_uf";
                                            $stmt_estado = $PDO->prepare($sql_estado);
                                            $stmt_estado->execute();
                                            $rows_estado = $stmt_estado->rowCount();
                                            if ($rows_estado > 0) {
                                                while ($result_estado = $stmt_estado->fetch()) {
                                                    echo "<option value='" . $result_estado['uf_id'] . "'>" . $result_estado['uf_nome'] . "</option>";
                                                }
                                            }

                                    echo "</select>
                                </div>
                            </div>

                                        <div id='informacoes' class='tab-pane fade in'>
                                            <p><label>Objetivo*:</label> <div class='textarea'><textarea  name='fa_objetivo_licitante' id='fa_objetivo_licitante'>".$result['fa_objetivo_licitante']."</textarea></div>
                                            <p><label>Observação*:</label> <div class='textarea'><textarea  name='fa_observacao_licitante' id='fa_observacao_licitante'>".$result['fa_observacao_licitante']."</textarea></div>
                                            <p><label>Certificado:</label> <input type='text' name='fa_certificado_licitante' id='fa_certificado_licitante'  placeholder='Certificado' value='".$result['fa_certificado_licitante']."'>
                                            <p><label>Emissão do CRC:</label> <input type='text' name='data_emissao_crc' id='data_emissao_crc'  value='$data'>
                                            <p><label>Tipo fornecedor:</label> <select name='fa_tipo_fornecedor' id='fa_tipo_fornecedor'>";
                                                switch ($result['fa_tipo_fornecedor']) {
                                                    case 1:
                                                        $tipo = "Servicos"; 
                                                        break;
                                                    case 2:
                                                        $tipo = "Produtos"; 
                                                        break;
                                                    case 3:
                                                        $tipo = "Produtos e Servicos"; 
                                                        break;
                                                    case '':
                                                        $tipo = "Nenhum"; 
                                                        break;    
                                                }
    
                                                echo "<option value='".$result['fa_tipo_fornecedor']."'>$tipo</option>
                                                <option value='1'>Servicos </option>
                                                <option value='2'>Produtos </option>
                                                <option value='3'>Produtos e Servicos </option>
                                            </select>
                                        </div>


                                        <div id='documentacao' class='tab-pane fade in'>
                                            <table class='doc'>
                                                <tr>
                                                    <th>Selecionar</th>
                                                    <th>Documento</th>
                                                    <th>Data de emissão</th>
                                                    <th>Validade</th>
                                                </tr>
                                            ";
                                            $sql_doc = "SELECT * FROM licitacao_documentos";
                                            $stmt_doc = $PDO->prepare($sql_doc);
                                            $stmt_doc->execute();
                                            $rows_doc = $stmt_doc->rowCount();
                                            if ($rows_doc > 0) {
                                                while($result_doc=$stmt_doc->fetch()){
                                                    $sql_doc2 = "SELECT * FROM licitacao_documentacao
                                                    LEFT JOIN licitacao_documentos ON licitacao_documentos.ld_id = licitacao_documentacao.id_documento
                                                    LEFT JOIN fornecedores ON fornecedores.id = licitacao_documentacao.id_licitante
                                                    WHERE licitacao_documentacao.id_licitante = :id_licitante AND ld_id = :ld_id";
                                                    $stmt_doc2 = $PDO->prepare($sql_doc2);
                                                    $stmt_doc2->bindParam(':id_licitante', $id);
                                                    $stmt_doc2->bindParam(':ld_id', $result_doc['ld_id']);
                                                    $stmt_doc2->execute();
                                                    $rows_doc2 = $stmt_doc2->rowCount();
                                                    if ($rows_doc2 > 0) {
                                                        $result_doc2=$stmt_doc2->fetch(); 
                                                        echo "<tr>
                                                                <td><center><input type='checkbox' name='tipo_documento[]' id='tipo_documento[]' value='".$result_doc['ld_id']."' checked></center></td>
                                                                <td>".$result_doc['titulo']."</td>
                                                                <td><input name='data_documento[]' name='data_documento[]' value='".date("d/m/Y", strtotime($result_doc2['data']))."'></td>
                                                                <td><input name='data_validade[]' name='data_validade[]' value='".date("d/m/Y", strtotime($result_doc2['validade']))."'></td>
                                                            </tr>";    
                                                    }
                                                    else {
                                                        echo "<tr>
                                                            <td><center><input type='checkbox' name='tipo_documento[]' id='tipo_documento[]' value='".$result_doc['ld_id']."' ></center></td>
                                                            <td>".$result_doc['titulo']."</td>
                                                            <td><input name='data_documento[]' name='data_documento[]'></td>
                                                            <td><input name='data_validade[]' name='data_validade[]'></td>
                                                        </tr>";
                                                    }
                                                }
                                            }

                                        echo"
                                        
                                            </table>
                                        </div>

                                        <center>
                                        <div id='erro' align='center'>&nbsp;</div>
                                        <input type='submit' id='bt_cadastro_fornecedores' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_fornecedores/view'; value='Cancelar'/>&nbsp;&nbsp;&nbsp;&nbsp; 
                                        <input type='button' id='botao_cancelar' onclick='notificar()'; value='Notificar Fornecedor'/>

                                        </center>
                                    </div>
                                </div>
                            </form>
                            ";
                }
                ?>
            </div>
        </div> <!-- .content-wrapper -->
    </main> <!-- .cd-main-content -->
</body>

</html>

<script>
    function addDoc() {
        var tipo_documento = $("#tp_doc").val();
        var data_documento = $("#data_doc").val();
        var data_validade = $("#data_val").val();
        $('.doc').append("<tr><td style='width:60%'><input name='tipo_documento[]' name='tipo_documento[]' value='" + tipo_documento + "'></td> <td style='width:30%'><input name='data_documento[]' name='data_documento[]' value='" + data_documento + "'></td><td style='width:10%; text-align:center'><input name='data_validade[]' name='data_validade[]' value='" + data_validade + "'></td><td style='width:10%; text-align:center'> <i class='fas fa-times' id='remover' style='cursor:pointer'></i> </td></tr>");
    }

    function remover(le_id) {
        abreMask('Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>' +
            '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=rm(' + le_id + ');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
            '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');

    };

    function rm(id_documento) {
        if (id_documento == '') {} else {
            $.post("excluir.php", {
                    action: 'excluir_documento_licitante',
                    id_documento: id_documento
                },
                function() {
                    $('tr#' + id_documento).remove();
                }
            )
        }
    }

    $(".doc").on("click", "#remover", function(e) {
        $(this).closest('tr').remove();
    });

	$("#fa_celular").mask("(00) 00000-0000");
	$("#fa_telefone").mask("(00) 0000-0000");
    $("#fa_cnpj").mask("00.000.000/0000-00");
    $("#fa_cpf").mask("000.000.000-00");
    $("#fa_rg").mask("00.000.000-0");
    
	$("#zip").mask("00000-000");

	$( "#zip" ).change(function() {
		var valor = $(this).val();
		$.post("../carrega_conteudo.php",{pagina: 'carrega_endereco_fornecedor', cep:valor},
			function(dados){
				$('.comp_end').html(dados); 
			}
		)
	});

    $( "#fa_cnpj" ).change(function() {
		var valor = $(this).val();
		$.post("../carrega_conteudo.php",{pagina: 'carrega_cnpj', cnpj:valor},
			function(dados){
                if(dados != 'false'){
                    abreMask('Este CNPJ já esta cadastrado, deseja editar este Fornecedor? <br><br>' +
                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=editar('+dados+');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                }
			}
		)
	});

	$( "#fa_cpf" ).change(function() {
		var valor = $(this).val();
		$.post("../carrega_conteudo.php",{pagina: 'carrega_cpf', cpf:valor},
			function(dados){
                if(dados != 'false'){
                    abreMask('Este CPF já esta cadastrado, deseja editar este Fornecedor? <br><br>' +
                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=editar('+dados+');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                }
			}
		)
	});

    function editar(id){
        location.replace("cadastro_fornecedores/edit/"+id); 
    }

    function notificar(){
        abreMask('Enviando <br><br>');

        var valor = $("#email").val();
		$.post("envia_senha.php",{email:valor},
			function(dados){
                if(dados == 'Enviado'){
                    abreMask('Fornecedor notificado com sucesso! <br><br>' +
                    '<input value=\' OK \' type=\'button\' class=\'close_janela\'>');
                }
			}
		)
    }

</script>