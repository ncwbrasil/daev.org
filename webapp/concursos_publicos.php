<?php
$pagina_link = 'concursos_publicos';
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");
session_start(); 
$page = "<a href='concursos_publicos/view'>Concursos Públicos</a>"; 

?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php 
        include_once("header.php"); 
        $sql = "SELECT cp_ano FROM  concursos_publicos
        GROUP BY cp_ano ORDER BY cp_ano DESC";
        $stmt = $PDO->prepare($sql);
        $stmt->execute();
        $rows = $stmt->rowCount();    	
        if($rows > 0)
        {	
            while($result = $stmt->fetch())
            {
                $ano[] = $result['cp_ano']; 
            }	
        }						
    
    ?>
    <script type="text/javascript">
	 	recupera(); 

		$(document).ready(function(){
			$("select[name=cp_ano]").change(function(){
				$.post("carrega_ano.php",{conteudo: 'concurso_publico', cp_ano:$(this).val(), pagina_link: '<?php echo $pagina_link?>'},
					function(valor){
						$("#concurso_publico").html(valor);
					}
				)
			})
		})

		function recupera(){
			ano = '<?php echo $ano[0]?>' 
			$.post("carrega_ano.php",{conteudo: 'concurso_publico', cp_ano:ano, pagina_link: '<?php echo $pagina_link?>'},
				function(valor){
					$("#concurso_publico").html(valor);
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
                    if (isset($_GET['cp_id'])) {
                        $cp_id = $_GET['cp_id'];
                    }
                    if ($cp_id == '') {
                        $cp_id = $_POST['cp_id'];
                    }

                    $cp_titulo         = $_POST['cp_titulo'];
                    $cp_url            = geradorTags($_POST['cp_titulo']);
                    $cp_data           = $_POST['cp_data']; 
                    $cp_link            = $_POST['cp_link']; 
                    $cp_ano            = $_POST['cp_ano']; //date('Y')
                    $dados = array(
                        'cp_titulo'            => $cp_titulo,
                        'cp_data'               => $cp_data,
                        'cp_link'               => $cp_link,
                        'cp_url'               => $cp_url,
                        'cp_ano'               => $cp_ano,
                        'cp_usuario'           => $_SESSION['usuario_id'],
                    );
                    if($action == "adicionar")
                    {
                        $sql = "INSERT INTO concursos_publicos SET " . bindFields($dados);
                        $stmt = $PDO->prepare($sql);

                        if($stmt->execute($dados))
                        {	
                            $cp_id = $PDO->lastInsertId();
                            //UPLOAD ARQUIVOS        
                            $caminho = "uploads/concursos/";
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
                                        $cp_documento    = $caminho;
                                        $cp_documento .= "concursos_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                        move_uploaded_file($nomeTemporario, ($cp_documento));
                                        $imnfo = getimagesize($cp_documento);
                                        $sql = "UPDATE concursos_publicos SET 
                                            cp_documento 	 = :cp_documento
                                            WHERE cp_id = :cp_id ";
                                        $stmt = $PDO->prepare($sql);
                                        $stmt->bindParam(':cp_documento', $cp_documento);
                                        $stmt->bindParam(':cp_id', $cp_id);
        
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
                        $sql = "UPDATE concursos_publicos SET " . bindFields($dados) . " WHERE cp_id = :cp_id ";
                        $stmt = $PDO->prepare($sql);
                        $dados['cp_id'] =  $cp_id;
                        if ($stmt->execute($dados))
                        {
                            //UPLOAD ARQUIVOS
                            $caminho = "uploads/concursos/";
                            foreach ($_FILES as $key => $files) {
                                $files_test = array_filter($files['name']);
                                if (!empty($files_test)) {
                                    if (!file_exists($caminho)) {
                                        mkdir($caminho, 0755, true);
                                    }
                                    if (!empty($files["name"]["documento"])) {
                                        # EXCLUI ANEXO ANTIGO #
                                        $sql = "SELECT * FROM concursos_publicos WHERE cp_id = :cp_id";
                                        $stmt_antigo = $PDO->prepare($sql);
                                        $stmt_antigo->bindParam(':cp_id', $cp_id);
                                        $stmt_antigo->execute();
                                        $result_antigo = $stmt_antigo->fetch();
                                        $documento_antigo = $result_antigo['cp_documento'];
                                        unlink($documento_antigo);

                                        $nomeArquivo     = $files["name"]["documento"];
                                        $nomeTemporario = $files["tmp_name"]["documento"];
                                        $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                        $cp_documento    = $caminho;
                                        $cp_documento .= "concursos_" . md5(mt_rand(1, 10000) . $nomeArquivo) . '.' . $extensao;
                                        move_uploaded_file($nomeTemporario, ($cp_documento));
                                        $imnfo = getimagesize($cp_documento);
                                        $sql = "UPDATE concursos_publicos SET 
                                            cp_documento 	 = :cp_documento
                                            WHERE cp_id = :cp_id ";
                                        $stmt = $PDO->prepare($sql);
                                        $stmt->bindParam(':cp_documento', $cp_documento);
                                        $stmt->bindParam(':cp_id', $cp_id);
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
                        $sql = "SELECT * FROM concursos_publicos WHERE cp_id = :cp_id ";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':cp_id', $cp_id);
                        if($stmt->execute())
                        {
                            $result = $stmt->fetch();
                            $arquivo = $result['cp_documento'];
                        }
                        
                        
                        $sql = "DELETE FROM concursos_publicos WHERE cp_id = :cp_id ";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':cp_id', $cp_id);
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
                    
                    $sql = "SELECT * FROM concursos_publicos 
                            ORDER BY cp_ano DESC, cp_data DESC";
                    $stmt = $PDO->prepare($sql);
                    $stmt->execute();
                    $rows = $stmt->rowCount();
                    if($pagina == "view")
                    {
                        echo "
                        <div class='titulo'> $page  </div>
                             
                        <b> Utilize o filtro ao lado para selecionar um ano </b>
                        <div class='cadastro'>
                            <div id='botoes'>
                                <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"cadastro_formularios_apr/add\");'><i class='fas fa-plus'></i></div>
                            </div>
                        </div>
                        <div class='selecione'>
                            <select name='cp_ano' id='cp_ano'>";
                                foreach($ano as &$cp_ano){
                                    echo "<option value='$cp_ano'> $cp_ano</option>"; 
                                }
                            echo "</select>
                        </div>
                        <div id='concurso_publico'></div> ";
                    }

                    if($pagina == 'add')
                    {
                        echo "	
                        <form name='form' id='form' enctype='multipart/form-data' method='post' action='concursos_publicos/view/adicionar'>
                            <div class='titulo'> $page &raquo; Adicionar  </div>
                            <ul class='nav nav-tabs'>
                                <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            </ul>
                            <div class='tab-content'>
                                <div id='dados_gerais' class='tab-pane fade in active'>
                                    <label>Título:</label> <input name='cp_titulo' id='cp_titulo' placeholder='Título' class='obg'>
                                    <p><label>Ano:</label> <input name='cp_ano' id='cp_ano' placeholder='Ano' class='obg'>
                                    <p><label>Data do Concurso:</label> <input name='cp_data' id='cp_data' placeholder='Dada do Concurso' class='obg'>
                                    <p><label>Link da Empresa:</label> <input name='cp_link' id='cp_link' placeholder='Link da Empresa' >
                                    <p><label>Documento:</label> <input type='file' name='cp_documento[documento]' id='cp_documento' class='obg'>
                                </div>
                                <center>
                                <div id='erro' align='center'>&nbsp;</div>
                                <input type='submit' id='bt_concursos_publicos' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='concursos_publicos/view'; value='Cancelar'/></center>
                                </center>
                            </div>
                        </form>
                        ";
                    }
                    
                    if($pagina == 'edit')
                    {
                        $sql = "SELECT * FROM concursos_publicos WHERE cp_id = :cp_id";
                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam(':cp_id', $cp_id);
                        $stmt->execute();
                        $rows = $stmt->rowCount();    	
                        if($rows > 0)
                        {
                            $result = $stmt->fetch();

                            $data = implode("/", array_reverse(explode("-", $result['cp_data'])));
                           
                            echo "
                            <form name='form' id='form' enctype='multipart/form-data' method='post' action='concursos_publicos/view/editar/$cp_id'>
                                <div class='titulo'> $page &raquo; Editar </div>
                                <ul class='nav nav-tabs'>
                                    <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                                </ul>
                                <div class='tab-content'>
                                    <div id='dados_gerais' class='tab-pane fade in active'>
                                        <label>Título:</label> <input name='cp_titulo' id='cp_titulo' value='".$result['cp_titulo']."' placeholder='Título' class='obg'>
                                        <p><label>Ano:</label> <input name='cp_ano' id='cp_ano' value='".$result['cp_ano']."' placeholder='Ano' class='obg'>
                                        <p><label>Data do Concurso:</label> <input name='cp_data' id='cp_data' placeholder='Ano' value='$data' class='obg'>
                                        <p><label>Link da Empresa:</label> <input name='cp_link' id='cp_link' placeholder='Ano' value='".$result['cp_link']."'>
                                        <p><label>Documento:</label> <input type='file' name='cp_documento[documento]' id='cp_documento' value='".$result['cp_documento']."'>
                                    </div>
                                    <center>
                                    <div id='erro' align='center'>&nbsp;</div>
                                    <input type='submit' id='bt_concursos_publicos' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                                    <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='concursos_publicos/view'; value='Cancelar'/></center>
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
