<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.ofa_rg/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.ofa_rg/1999/xhtml">

<head>
    <?php 
        include_once("header.php"); 
        include_once("core/mod_includes/php/funcoes.php");
        include ('core/mod_includes/php/funcoes-jquery.php'); 
    ?>
</head>

<body>
<div id='janela' class='janela' style='display:none;'> </div>

    <main class="cd-main-content">
        <div class="wrapper">
            <?php
                $field1         = $_POST['nome_fantasia'];
                $field2         = $_POST['razao_social'];
                $fa_cnpj        = $_POST['cnpj'];
                $email          = $_POST['email'];
                $fa_celular     = $_POST['celular'];
                $fa_telefone    = $_POST['telefone'];
                $zip            = $_POST['cep'];
                $address        = $_POST['endereco'];
                $number         = $_POST['numero'];
                $bairro         = $_POST['bairro'];
                $city           = $_POST['cidade'];
                $state_id       = $_POST['estado'];


                $dados_gerais = array(
                    'field1' => $field1,
                    'field2' => $field2,
                    'email' => $email,
                    'status' => 0,
                );

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
                        'city' => $city,
                        'state_id' => $state_id,
                    );

                    $sql_endereco = "INSERT INTO fornecedores_endereco SET " . bindFields($dados_endereco);
                    $stmt_endereco = $PDO->prepare($sql_endereco);
                    if ($stmt_endereco->execute($dados_endereco)) {

                        $dados_atributos = array(
                            'fa_celular' => $fa_celular,
                            'fa_telefone' => $fa_telefone,
                            'fa_cnpj' => $fa_cnpj,
                            'fa_fornecedor' => $id_fornecedor,
                        );

                        $sql_atributos = "INSERT INTO fornecedor_atributos SET " . bindFields($dados_atributos);

                        $stmt_atributos = $PDO->prepare($sql_atributos);
                        if ($stmt_atributos->execute($dados_atributos)) {
                            ?>
                                <script>
                                    var razao = '<?php echo $field1;?>'; 
                                    abreMask(
                                    '<font color:#e5b35a><b>'+razao+'</b></font>, recebemos seu cadastro, em breve entraremos em contato.<br><br>'+
                                    '<center><input value=\' Ok \' type=\'button\' class=\'but_mask\' onclick=javascript:window.location.href=\'index\';></center>' );
                                </script>
                            <?php

                        } else {
                            $erro = 1;
                            $err = $stmt->errorInfo();
                            print_r($err);
                        }
                    }
                } else {
                    $err = $stmt->errorInfo();

                    print_r($err);
                    ?>
                    <script>
                        mensagem("X", "<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                    </script>
                <?php
                }
            ?>
        </div>
    </main>
</body>

</html>