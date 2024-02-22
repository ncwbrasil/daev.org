<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
<meta http-equiv="Content-Language" content="pt-br">

<head>
	<?php 
		include('header.php'); 
        $pagina = 'busca'; 
        $busca = '%'.$_POST['busca'].'%';
	?>
	<title><?php echo $ttl; ?></title>
</head>

<body>
	<header>
		<?php
			#region MOD INCLUDES
			include('core/mod_topo/topo.php');
			include('core/mod_includes/php/funcoes-jquery.php');
			#endregion
		?>
	</header>
	<main>
        <section>
            <div class="banner-top">
                <h1 class="titulo"><i class="fas fa-search"></i> Resultado da busca</h1>
            </div>
        </section>

		<section class="busca">
			<div class="wrapper">
                <?php
                    $sql1 = "SELECT * FROM  cadastro_paginas
                    WHERE pg_status = :pg_status AND pg_titulo like :pg_titulo OR pg_descricao like :pg_descricao ";
                    $stmt1 = $PDO->prepare($sql1);
                    $stmt1->bindValue(':pg_status', 1);
                    $stmt1->bindValue(':pg_titulo', $busca); 
                    $stmt1->bindValue(':pg_descricao', $busca); 
                    $stmt1->execute();
                    while($result1 = $stmt1->fetch())
                    {
                        echo "
                            <a href='router/".$result1['pg_url']."' target='_blank'>
                                <div class='bloco'>
                                    <h2 class='subtitulo'><i class='far fa-file-alt'></i> ".$result1['pg_titulo']."</h2>
                                    <p class='descricao'>" . truncate(strip_tags(preg_replace("/<img[^>]+\>/i", " ", str_replace("<br />", " ", str_replace("</p>", " ", str_replace("<p>", " ", $result1['pg_descricao']))))), 250) . " </span>
                                </div>
                            </a>						
                        ";
                    }	


                    $sql2 = "SELECT * FROM  cadastro_departamentos
                    WHERE dep_status = :dep_status AND dep_titulo like :dep_titulo OR dep_descricao like :dep_descricao OR dep_responsavel like :dep_responsavel";
                    $stmt2 = $PDO->prepare($sql2);
                    $stmt2->bindValue(':dep_status', 1);
                    $stmt2->bindValue(':dep_titulo', $busca); 
                    $stmt2->bindValue(':dep_descricao', $busca); 
                    $stmt2->bindValue(':dep_responsavel', $busca); 
                    $stmt2->execute();
                    while($result2 = $stmt2->fetch())
                    {
                        echo "
                            <a href='departamentos/".$result2['dep_url']."' target='_blank'>
                                <div class='bloco'>
                                    <h2 class='subtitulo'><i class='fas fa-sitemap'></i> ".$result2['dep_titulo']."</h2>
                                    <p class='descricao'>" . truncate(strip_tags(preg_replace("/<img[^>]+\>/i", " ", str_replace("<br />", " ", str_replace("</p>", " ", str_replace("<p>", " ", $result2['dep_descricao']))))), 250) . " </span>
                                </div>
                            </a>						
                        ";
                    }	

                    $sql3 = "SELECT * FROM  cadastro_downloads
                    WHERE nome like :nome OR descricao like :descricao";
                    $stmt3 = $PDO->prepare($sql3);
                    $stmt3->bindValue(':nome', $busca); 
                    $stmt3->bindValue(':descricao', $busca); 
                    $stmt3->execute();
                    while($result3 = $stmt3->fetch())
                    {
                        echo "
                        <div class='bloco documento'>
                            <h2 class='subtitulo'><i class='fas fa-cloud-download-alt'></i>".$result3['nome']."</h2>
                            <p class='descricao'>" . truncate(strip_tags(preg_replace("/<img[^>]+\>/i", " ", str_replace("<br />", " ", str_replace("</p>", " ", str_replace("<p>", " ", $result3['descricao']))))), 250) . " </span>";
                            if($result3['arquivo'] == ''){
                                $sql_doc ='SELECT * FROM cadastro_downloads_documentos WHERE doc_download = :doc_download'; 
                                $stmt_doc = $PDO->prepare($sql_doc);
                                $stmt_doc->bindValue(':doc_download', $result3['id']); 
                                $stmt_doc->execute();
                                while($result_doc = $stmt_doc->fetch()){
                                    echo "<a href='webapp/uploads/downloads/".$result_doc['doc_arquivo']."' title='".$result_doc['doc_arquivo']."'>
                                        <div class='down'>
                                            <i class='fas fa-file-alt'></i>
                                        </div>
                                    </a>";
                                }
                            }else {
                                echo "<a href='webapp/uploads/downloads/".$result3['arquivo']."' title='".$result_doc['arquivo']."'>
                                        <div class='down'>
                                            <i class='fas fa-file-alt'></i>
                                        </div>
                                    </a>";
                            }          
                        echo "</div>

                        ";
                    }	

                    $sql5 = "SELECT * FROM cadastro_noticias 
                    LEFT JOIN cadastro_categoria_noticias ON cadastro_categoria_noticias.cn_id = cadastro_noticias.nt_categoria
                    WHERE nt_titulo like :nt_titulo OR nt_descricao like :nt_descricao AND nt_status = :nt_status
					ORDER BY nt_titulo ASC";
                    $stmt5 = $PDO->prepare($sql5);
                    $stmt5->bindValue(':nt_status', 1);
                    $stmt5->bindValue(':nt_titulo', $busca); 
                    $stmt5->bindValue(':nt_descricao', $busca);
                    $stmt5->execute();
                    $rows5 = $stmt5->rowCount();
                    if ($rows5 > 0) {

                        while ($result5 = $stmt5->fetch()) {

                            if ($result5['cn_url'] == ''){
                                $cat = 'materias';
                            }else {
                                $cat = $result5['cn_url']; 
                            }

                            echo "<a href='noticias/$cat/" . $result5['nt_url'] . "' title='Noticia: " . $result5['nt_titulo'] . "'>
                                    <div class='bloco'>
                                        <h2 class='subtitulo'> <i class='far fa-newspaper'></i> " . $result5['nt_titulo'] . " </h2>
                                        <p class='data'><i class='fas fa-calendar-alt'></i> " . implode("/", array_reverse(explode("-", $result5['nt_data']))) . " </p>
                                    </div>
                                </a>";
                        }
                    }


                    $sql6= "SELECT *	FROM licitacao_pregao 
                    LEFT JOIN licitacao_categorias ON licitacao_categorias.lc_id = licitacao_pregao.id_categoria
                    WHERE codigo like :codigo OR titulo like :titulo OR objetivo like objetivo AND exibir = :exibir";
                    $stmt6 = $PDO->prepare($sql6);
                    $stmt6->bindValue(':exibir', 1);
                    $stmt6->bindValue(':codigo', $busca); 
                    $stmt6->bindValue(':titulo', $busca);
                    $stmt6->bindValue(':objetivo', $busca);
                    $stmt6->execute();
                    $rows6 = $stmt6->rowCount(); 
                    if($rows6> 0)
                    {		
                        while($result6 = $stmt6->fetch())
                        {
                            $data = date("d/m/Y", strtotime($result6['data_abertura'])); 
                            echo "
                                <a href='licitacoes/".$result6['lic_id']."'>
                                    <div class='bloco'>
                                        <h2><i class='fas fa-file-contract'></i> ".$result6['codigo']."</h2>
                                        <p>".$result6['titulo']." <br>
                                        <i class='fas fa-list-alt'></i> ".$result6['lc_titulo']."<br>
                                        <span class='data'><i class='fas fa-calendar-alt'></i> $data </span></p>
                                    </div>
                                </a>
                            ";
                        }
                    }
                ?>
		    </div>
		</section>
		<?php
		include('core/mod_rodape/rodape.php');
		?>
	</main>
</body>

</html>
