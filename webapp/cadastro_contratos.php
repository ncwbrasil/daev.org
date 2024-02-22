<?php
$pagina_link = 'cadastro_contratos';
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");
session_start(); 
$page = "<a href='cadastro_contratos/view'>Contratos</a>"; 

?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php 
        include_once("header.php"); 
        $sql = "SELECT con_ano FROM  cadastro_contratos
        GROUP BY con_ano ORDER BY con_ano DESC";
        $stmt = $PDO->prepare($sql);
        $stmt->execute();
        $rows = $stmt->rowCount();    	
        if($rows > 0)
        {	
            while($result = $stmt->fetch())
            {
                $ano[] = $result['con_ano']; 
            }	
        }					

    ?>
    <script type="text/javascript">
	 	recupera(); 

		$(document).ready(function(){
			$("select[name=ano_contrato]").change(function(){
				$.post("carrega_ano.php",{conteudo: 'contratos', con_ano:$(this).val(), pagina_link: '<?php echo $pagina_link?>'},
					function(valor){
						$("#contratos").html(valor);
					}
				)
			})
		})

		function recupera(){
			ano = '<?php echo $ano[0] ?>'; 
			$.post("carrega_ano.php",{conteudo: 'contratos', con_ano:ano, pagina_link: '<?php echo $pagina_link?>'},
				function(valor){
					$("#contratos").html(valor);
				}
			)
		}	
	</script>

</head>
<body>
	<main class="cd-main-content">
        <div class="container">
            <?php include('../core/mod_menu/menu/menu.php')?>
			<div class="wrapper">
                <div class='mensagem'></div>
                <?php
                    if (isset($_GET['con_id'])) {
                        $con_id = $_GET['con_id'];
                    }
                    if ($con_id == '') {
                        $con_id = $_POST['con_id'];
                    }

                    $con_titulo         = $_POST['con_titulo'];
                    $con_url            = geradorTags($_POST['con_titulo']);
                    $con_ano            = $_POST['con_ano']; 
                    $con_mes            = $_POST['con_mes']; 
                    $dados = array(
                        'con_titulo'            => $con_titulo,
                        'con_ano'               => $con_ano,
                        'con_mes'               => $con_mes,
                        'con_url'               => $con_url,
                        'con_usuario'           => $_SESSION['usuario_id'],
                    );
                    if($action == "adicionar")
                    {
                        $sql = "INSERT INTO cadastro_contratos SET " . bindFields($dados);
                        $stmt = $PDO->prepare($sql);

                        if($stmt->execute($dados))
                        {	
                            $con_id = $PDO->lastInsertId();
                            //UPLOAD ARQUIVOS        
                            $caminho = "uploads/beneficios/";
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
                                        $con_documento    = $caminho;
                                        $con_documento .= "beneficios_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                        move_uploaded_file($nomeTemporario, ($con_documento));
                                        $imnfo = getimagesize($con_documento);
                                        $sql = "UPDATE cadastro_contratos SET 
                                            con_documento 	 = :con_documento
                                            WHERE con_id = :con_id ";
                                        $stmt = $PDO->prepare($sql);
                                        $stmt->bindParam(':con_documento', $con_documento);
                                        $stmt->bindParam(':con_id', $con_id);
        
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
                                mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucessso!");
                            </script>
                            <?php
                        }
                        else
                        {
                            echo $err = $stmt->errorInfo();
                            ?>
                            <script>
                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                            </script>
                            <?php 
                        }	
                    }
                    
                    if($action == 'editar')
                    {
                        $sql = "UPDATE cadastro_contratos SET " . bindFields($dados) . " WHERE con_id = :con_id ";
                        $stmt = $PDO->prepare($sql);
                        $dados['con_id'] =  $con_id;
                        if ($stmt->execute($dados))
                        {
                            //UPLOAD ARQUIVOS
                            $caminho = "uploads/investimentos/";
                            foreach ($_FILES as $key => $files) {
                                $files_test = array_filter($files['name']);
                                if (!empty($files_test)) {
                                    if (!file_exists($caminho)) {
                                        mkdir($caminho, 0755, true);
                                    }
                                    if (!empty($files["name"]["documento"])) {
                                        # EXCLUI ANEXO ANTIGO #
                                        $sql = "SELECT * FROM cadastro_contratos WHERE con_id = :con_id";
                                        $stmt_antigo = $PDO->prepare($sql);
                                        $stmt_antigo->bindParam(':con_id', $con_id);
                                        $stmt_antigo->execute();
                                        $result_antigo = $stmt_antigo->fetch();
                                        $documento_antigo = $result_antigo['con_documento'];
                                        unlink($documento_antigo);

                                        $nomeArquivo     = $files["name"]["documento"];
                                        $nomeTemporario = $files["tmp_name"]["documento"];
                                        $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                        $con_documento    = $caminho;
                                        $con_documento .= "beneficios_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                        move_uploaded_file($nomeTemporario, ($con_documento));
                                        $imnfo = getimagesize($con_documento);
                                        $sql = "UPDATE cadastro_contratos SET 
                                            con_documento 	 = :con_documento
                                            WHERE con_id = :con_id ";
                                        $stmt = $PDO->prepare($sql);
                                        $stmt->bindParam(':con_documento', $con_documento);
                                        $stmt->bindParam(':con_id', $con_id);
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
                                mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                            </script>
                            <?php
                        }
                        else
                        {
                            ?>
                            <script>
                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                            </script>
                            <?php
                        }
                    }
                    
                    if($action == 'excluir')
                    {
                        // PEGA CAMINHO DO ARQUIVO PARA FAZER EXCLUSÃO
                        $sql = "SELECT * FROM cadastro_contratos WHERE con_id = :con_id ";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':con_id', $con_id);
                        if($stmt->execute())
                        {
                            $result = $stmt->fetch();
                            $arquivo = $result['con_documento'];
                        }

                        
                        $sql = "DELETE FROM cadastro_contratos WHERE con_id = :con_id ";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':con_id', $con_id);
                        if($stmt->execute())
                        {
                            // EXCLUI ARQUIVO DO FTP
                            unlink($arquivo);
                            
                            
                            ?>
                            <script>
                                mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                            </script>
                            <?php
                        }
                        else
                        {
                            ?>
                            <script>
                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                            </script>
                            <?php
                        }
                    }

                    if($pagina == "view")
                    {
                        echo "
                            <div class='titulo'> $page  </div>
                            <div class='cadastro'>
                                <div id='botoes'>
                                    <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"cadastro_contratos/add\");'><i class='fas fa-plus'></i></div>
                                </div>
                            </div>
                            <div class='selecione'>
                                <select name='ano_contrato' id='ano_contrato'>";
                                    foreach($ano as &$con_ano){
                                        echo "<option value='$con_ano'> $con_ano</option>"; 
                                    }
                                echo "</select>
                            </div>
                            <div id='contratos'></div>
                        ";
                    }

                    if($pagina == 'add')
                    {
                        echo "	
                        <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_contratos/view/adicionar'>
                            <div class='titulo'> $page &raquo; Adicionar  </div>
                            <ul class='nav nav-tabs'>
                                <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            </ul>
                            <div class='tab-content'>
                                <div id='dados_gerais' class='tab-pane fade in active'>
                                    <label>Título:</label> <input name='con_titulo' id='con_titulo' placeholder='Título' class='obg'>
                                    <p><label>Ano:</label> <input name='con_ano' id='con_ano' placeholder='Ano' class='obg'>
                                    <p><label>Mês:</label> 
                                        <select id='con_mes' name='con_mes'>    
                                            <option value='01'> Janeiro </option> 
                                            <option value='02'> Fevereiro </option> 
                                            <option value='03'> Março </option> 
                                            <option value='04'> Abril </option>  
                                            <option value='05'> Maio </option> 
                                            <option value='06'> Junho </option> 
                                            <option value='07'> Julho </option> 
                                            <option value='08'> Agosto </option> 
                                            <option value='09'> Setembro </option> 
                                            <option value='10'> Outubro </option> 
                                            <option value='11'> Novembro </option> 
                                            <option value='12'> Dezembro </option> 
                                        </select> 

                                    <p><label>Documento:</label> <input type='file' name='con_documento[documento]' id='con_documento' class='obg'>
                                </div>
                                <center>
                                <div id='erro' align='center'>&nbsp;</div>
                                <input type='submit' id='bt_cadastro_contratos' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_contratos/view'; value='Cancelar'/></center>
                                </center>
                            </div>
                        </form>
                        ";
                    }
                    
                    if($pagina == 'edit')
                    {
                        $sql = "SELECT * FROM cadastro_contratos WHERE con_id = :con_id";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':con_id', $con_id);
                        $stmt->execute();
                        $rows = $stmt->rowCount();    	
                        if($rows > 0)
                        {
                            $result = $stmt->fetch();
                            switch ($result['con_mes']) {
                                case 1:
                                    $mes = "Janeiro";
                                    break;
                                case 2:
                                    $mes = "Fevereiro";
                                    break;
                                case 3:
                                    $mes = "Março";
                                    break;
                                case 4:
                                    $mes = "Abril";
                                    break;
                                case 5:
                                    $mes = "Maio";
                                    break;
                                case 6:
                                    $mes = "Junho";
                                    break;
                                case 7:
                                    $mes = "Julho";
                                    break;
                                case 8:
                                    $mes = "Agosto";
                                    break;
                                case 9:
                                    $mes = "Setembro";
                                    break;
                                case 10:
                                    $mes = "Outubro";
                                    break;
                                case 11:
                                    $mes = "Novembro";
                                    break;
                                case 12:
                                    $mes = "Dezembro";
                                    break;
                            }
                           
                            echo "
                            <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_contratos/view/editar/$con_id'>
                                <div class='titulo'> $page &raquo; Editar </div>
                                <ul class='nav nav-tabs'>
                                    <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                                </ul>
                                <div class='tab-content'>
                                    <div id='dados_gerais' class='tab-pane fade in active'>
                                        <label>Título:</label> <input name='con_titulo' id='con_titulo' value='".$result['con_titulo']."' placeholder='Título' class='obg'>
                                        <p><label>Ano:</label> <input name='con_ano' id='con_ano' placeholder='Ano' value='".$result['con_ano']."' class='obg'>
                                        <p><label>Mês:</label> <select id='con_mes' name='con_mes'>    
                                                <option value='".$result['con_mes']."'> $mes </option> 
                                                <option value='01'> Janeiro </option> 
                                                <option value='02'> Fevereiro </option> 
                                                <option value='03'> Março </option> 
                                                <option value='04'> Abril </option>  
                                                <option value='05'> Maio </option> 
                                                <option value='06'> Junho </option> 
                                                <option value='07'> Julho </option> 
                                                <option value='08'> Agosto </option> 
                                                <option value='09'> Setembro </option> 
                                                <option value='10'> Outubro </option> 
                                                <option value='11'> Novembro </option> 
                                                <option value='12'> Dezembro </option> 
                                            </select> 

                                        <p><label>Documento:</label> <input type='file' name='con_documento[documento]' id='con_documento' value='".$result['con_documento']."'>
                                    </div>
                                    <center>
                                    <div id='erro' align='center'>&nbsp;</div>
                                    <input type='submit' id='bt_cadastro_contratos' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                    <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_contratos/view'; value='Cancelar'/></center>
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
