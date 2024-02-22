<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
<meta http-equiv="Content-Language" content="pt-br">

<head>
	<?php 
		include('header.php'); 
		$dep = $_GET['p1']; 
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
				<p class="titulo">Presidente e Diretores</p>
			</div>
		</section>
		<section>
			<div class="wrapper servidores" id='pagina'>
				<div class='conteudo'>
					<?php 
						if($dep == ''){
							$sql= "SELECT * FROM cadastro_departamentos
							WHERE dep_status = :dep_status
							ORDER BY dep_ordem ASC";
							$stmt = $PDO->prepare($sql);
							$stmt->bindValue(':dep_status', 1);
							$stmt->execute();
							$rows = $stmt->rowCount();    
							if($rows> 0)
							{	
								while($result = $stmt->fetch()){
									if($result['dep_imagem'] == ''){
										$imagem = 'core/imagens/logo.png'; 
									}
									else {
										$imagem = 'webapp/'.$result['dep_imagem']; 
									}

									echo "
										<a href='presidente-e-diretores/".$result['dep_url']."'>
											<div class='bloco'>
												<div class='imagem'>
													<img src='".$imagem."' style='object-fit:cover; width:90%; height:190px'>
												</div>
												<div class='texto'>
													<p class='subtitulo azul'>".$result['dep_titulo']." </p>
													<p>Titular: ".$result['dep_responsavel']."</p>
												</div>
											</div>
										</a>
									"; 
								}
							}
							else{
								echo "Nenhum Departamento cadastrado no momento."; 
							} 
		
						}else{
							$sql= "SELECT * FROM cadastro_departamentos
							WHERE dep_url = :dep_url";
							$stmt = $PDO->prepare($sql);
							$stmt->bindValue(':dep_url', $dep);
							$stmt->execute();
							$rows = $stmt->rowCount();    
							if($rows> 0)
							{	
								while($result = $stmt->fetch()){
									if($result['dep_imagem'] == ''){
										$imagem = 'core/imagens/logo.png'; 
									}
									else {
										$imagem = 'webapp/'.$result['dep_imagem']; 
									}
									echo "
										<img src='".$imagem."' style='object-fit:cover; width:300px; height:250px' id='dp_img'>
										<p class='subtitulo azul'>".$result['dep_titulo']." </p>													
										<p>Titular: ".$result['dep_responsavel']."</p>
										<p>".$result['dep_descricao']."</p>
									"; 
									include('compartilhe.php');
								}
							}
							else{
								echo "Nenhum Departamento cadastrado no momento."; 
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
								$stmt->bindValue(':men_id', 18);
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
		<?php
		include('core/mod_rodape/rodape.php');
		?>
	</main>
</body>

</html>
