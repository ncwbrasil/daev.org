<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
<meta http-equiv="Content-Language" content="pt-br">

<head>
	<?php 
		include('header.php'); 
		$serv = $_GET['p1']; 
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
				<p class="titulo">Servidores Comissionados</p>
			</div>
		</section>
		<section>
			<div class="wrapper servidores" id='pagina'>
				<div class='conteudo'>
					<?php 
						if($serv == ''){
							$sql= "SELECT * FROM cadastro_servidores
							WHERE cs_status = :cs_status
							ORDER BY cs_cargo ASC";
							$stmt = $PDO->prepare($sql);
							$stmt->bindValue(':cs_status', 1);
							$stmt->execute();
							$rows = $stmt->rowCount();    
							if($rows> 0)
							{	
								while($result = $stmt->fetch()){
									if($result['cs_imagem'] == ''){
										$imagem = 'core/imagens/logo.png'; 
									}
									else {
										$imagem = 'webapp/'.$result['cs_imagem']; 
									}

									echo "
										<a href='servidores-comissionados/".$result['cs_url']."'>
											<div class='bloco'>
												<div class='imagem'>
													<img src='".$imagem."' style='object-fit:cover; width:100%; height:200px'>
												</div>
												<div class='texto'>
													<h2 class='azul'> ".$result['cs_nome']."</h2>
													<p >Cargo: ".$result['cs_cargo']." </p>
												</div>
											</div>
										</a>
									"; 
								}
							}
							else{
								echo "Nenhum item cadastrado no momento."; 
							} 
		
						}else{
							$sql= "SELECT * FROM cadastro_servidores
							WHERE cs_url = :cs_url";
							$stmt = $PDO->prepare($sql);
							$stmt->bindValue(':cs_url', $serv);
							$stmt->execute();
							$rows = $stmt->rowCount();    
							if($rows> 0)
							{	
								$result = $stmt->fetch();
									if($result['cs_imagem'] == ''){
										$imagem = 'core/imagens/logo.png'; 
									}
									else {
										$imagem = 'webapp/'.$result['cs_imagem']; 
									}
									echo "
										<img src='".$imagem."' style='object-fit:cover; width:250px; height:300px' id='dp_img'>
										<p class='subtitulo azul'>".$result['cs_cargo']." </p>													
										<h3>Titular: ".$result['cs_nome']."</h3>
										<p>".$result['cs_curriculo']."</p>

										<p class='compartilhar'><b>Currículo: <a href='webapp/" . $result['cs_documento'] . "'><button class='imprimir'><i class='fa-solid fa-file-pdf'></i></button></a> </p>
									"; 
									include('compartilhe.php');
							}
							else{
								echo "Nenhum item cadastrado no momento."; 
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
