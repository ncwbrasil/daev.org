<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
<meta http-equiv="Content-Language" content="pt-br">

<head>
	<?php 
		include('header.php'); 
		$url = $_GET['p1'];	
		$serv = $_GET['p2']; 
		if($serv == ''){
		$sql= "SELECT * FROM aux_menu WHERE men_link = :men_link";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':men_link', $url);
		$stmt->execute();
		$rows = $stmt->rowCount();    	
		if($rows> 0)
		{
			$result = $stmt->fetch(); 
			$titulo = $result['men_titulo']; 
		}
		else {
			$sql_sub= "SELECT * FROM aux_submenu WHERE sm_link = :sm_link";
			$stmt_sub = $PDO->prepare($sql_sub);
			$stmt_sub->bindParam(':sm_link', $url);
			$stmt_sub->execute();
			$rows_sub = $stmt_sub->rowCount(); 
			if($rows_sub> 0)
			{	
				$result = $stmt_sub->fetch(); 
				$titulo = $result['sm_titulo']; 
	
			}
			else {
				header('Location: /daev/404-pagina-nao-encontrada/');
			}
		}
	}else {
		$sql= "SELECT * FROM cadastro_downloads
		LEFT JOIN cadastro_downloads_categorias ON cadastro_downloads_categorias.cat_id = cadastro_downloads.id_categoria
		WHERE id = :id";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':id', $serv);
		$stmt->execute();
		$rows = $stmt->rowCount();    	
		if($rows> 0)
		{
			$result = $stmt->fetch(); 
			$titulo = $result['nome']; 
			$data = date("d/m/Y", strtotime($result['data']));
		}	
		else{
			header('Location: /daev/404-pagina-nao-encontrada/');
		} 
	}

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
				<p class="titulo"><?php echo $titulo."<br> <center>".$categoria."<br><i>".$data."</i></center>" ?></p>
			</div>
		</section>
		<section>
			<div class="wrapper servicos" id='pagina'>
				<div class='conteudo'>
					<?php 
						if($serv == ''){

							if($url == 'portarias-do-daev'){
								$ordem = "nome"; 
							}
							else {
								$ordem = "nome | data";
							}

							$sql= "SELECT * FROM cadastro_downloads
							LEFT JOIN cadastro_downloads_categorias ON cadastro_downloads_categorias.cat_id = cadastro_downloads.id_categoria
							LEFT JOIN aux_menu ON aux_menu.men_id = cadastro_downloads.menu
							LEFT JOIN aux_submenu ON aux_submenu.sm_id = cadastro_downloads.submenu
							WHERE men_link = :men_link OR sm_link = :sm_link
							ORDER BY $ordem DESC";
							$stmt = $PDO->prepare($sql);
							$stmt->bindParam(':men_link', $url);
							$stmt->bindParam(':sm_link', $url);
							$stmt->execute();
							$rows = $stmt->rowCount();    	
							if($rows> 0)
							{	
								$i = 0; 
								$c = 10; 
								$p = 1; 
								while($result = $stmt->fetch()){
									$i++; 
									if($i == $c){$p ++;$i= 0;}

									$nome 		= $result['nome']; 
									$categoria 	= $result['cat_nome']; 
									$data    = date("d/m/Y", strtotime( $result['data']));
									$descricao = $result['descricao']; 
									$id = $result['id']; 
									
									if($url == 'manual-do-daev'){
										echo "
											<a href='servicos/$url/$id'><div class='bloco bloco$p' style='display:none'>
												<p><span class='subtitulo azul'><i class='fas fa-cloud-download-alt'></i>  $nome </span> </br>
												$categoria <br>";
												echo"<span class='data'><i class='fas fa-calendar-alt'></i> $data </span> </p>
											</div></a>
										"; 	

									}else {
										echo "
											<div class='bloco bloco$p' style='display:none'>
												<p><span class='subtitulo azul'><i class='fas fa-cloud-download-alt'></i>  $nome </span> </br>
												$categoria <br>";
												if($descricao !==''){ echo "$descricao<br>";}
												echo"<span class='data'><i class='fas fa-calendar-alt'></i> $data </span> </p>
													";
												
												$sqld= "SELECT * FROM cadastro_downloads_documentos
												WHERE doc_download = :doc_download";
												$stmtd = $PDO->prepare($sqld);
												$stmtd->bindParam(':doc_download', $id);
												$stmtd->execute();
												$rowsd = $stmtd->rowCount();    	
												if($rowsd> 0)
												{	
													while($resultd = $stmtd->fetch()){
														echo "<a href='webapp/uploads/downloads/".$resultd['doc_arquivo']."' title='".$resultd['doc_arquivo']."'>
															<div class='down'>
																<i class='fas fa-file-alt'></i>
															</div>
														</a>";
													}
												}
											echo"
											</div>
										"; 	

									}
								}
								
								echo "<div class='paginacao'>";
									for($z=1; $z<=$p; $z++){
										echo"<button id='bloco$z' class='pg' value='bloco$z'> $z </button>";
									}
									echo"
								</div>";
											}
							else{
								header('Location: /daev/404-pagina-nao-encontrada/');
							} 

						}else {
							$sql2= "SELECT * FROM cadastro_downloads
							LEFT JOIN cadastro_downloads_categorias ON cadastro_downloads_categorias.cat_id = cadastro_downloads.id_categoria
							WHERE id = :id";
							$stmt2 = $PDO->prepare($sql2);
							$stmt2->bindParam(':id', $serv);
							$stmt2->execute();
							$rows2 = $stmt2->rowCount();    	
							if($rows2> 0)
							{
								$result2 = $stmt2->fetch(); 
								$titulo = $result2['nome']; 
								$data = date("d/m/Y", strtotime($result2['data']));
								echo "
								<p>".$result2['descricao']."</p> 							
								<p><a href='webapp/uploads/downloads/".$result2['arquivo']."'>".$result2['arquivo']."</a></p>";	
							}			
				
						}
					?>	
				</div>

				<div class='veja_mais'>									
					<div class='bloco'>
						<h3> Veja Mais </h3>
						<ul>
							<?php 
								$sql= "SELECT *	FROM aux_menu
								LEFT JOIN aux_submenu On aux_submenu.sm_menu = aux_menu.men_id
								WHERE men_id = :men_id
								ORDER BY men_posicao, sm_posicao DESC";
								$stmt = $PDO->prepare($sql);
								$stmt->bindValue(':men_id', 21);
								$stmt->execute();
								$rows = $stmt->rowCount(); 
								if($rows> 0)
								{						
									while($result = $stmt->fetch()){
										$link = substr($result['sm_link'], 0,4);
										if($link == "http" || $link == 'HTTP' || $link == 'Http'){
											echo "<li><a href='".$result['sm_link']."' target='_blank'><i class='far fa-arrow-alt-circle-right'></i> ".$result['sm_titulo']."</a></li>";

										}else{
											echo "<li><a href='router/".$result['sm_link']."'><i class='far fa-arrow-alt-circle-right'></i> ".$result['sm_titulo']."</a></li>";
										}
									}
								}								
							?>
						</ul>
					</div>
				</div>
			</div>
		</section>

		<section>
		</section>
		<?php
		include('core/mod_rodape/rodape.php');
		?>
	</main>
</body>

</html>

<script>
	$(document).ready(function(){
		$('.bloco1').css('display','table'); 
	});
	$('button').click(function() {
		let valor = $(this).val(); 
		$('.conteudo .bloco').css('display','none'); 
		$('.'+valor).css('display','table'); 
		$('.pg').css('background','#2f91ce'); 
		$('.pg').css('color','#fff');

		$('#'+valor).css('color','#2f91ce'); 
		$('#'+valor).css('background','#fff'); 
	});


	$("#paginacao").change(function(){
		let valor = $(this).val();  
		$('.conteudo .bloco').css('display','none'); 
		$('.'+valor).css('display','table'); 
	})
</script>

