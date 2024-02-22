<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
<meta http-equiv="Content-Language" content="pt-br">

<head>
	<?php 
		include('header.php'); 
		$cea = $_GET['p1']; 

		$sql= "SELECT * FROM cadastro_educacao_ambiental
		WHERE cea_url = :cea_url";
		$stmt = $PDO->prepare($sql);
		$stmt->bindValue(':cea_url', $cea);
		$stmt->execute();
		$rows = $stmt->rowCount();    
		if($rows> 0)
		{	
			$result = $stmt->fetch();
			$imagem = $result['cea_imagem']; 
			$cea_descricao = $result['cea_descricao']; 
			$cea_titulo = $result['cea_titulo']; 
		}

		if($cea_titulo == ''){
			$cea_titulo ='Educação Ambiental'; 
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
				<p class="titulo"><?php echo $cea_titulo?></p>
			</div>
		</section>
		<section>
			<div class="wrapper educacao-ambiental" id='pagina'>
				<div class='conteudo'>
					<?php 
						if($cea == ''){
							$sql= "SELECT * FROM cadastro_educacao_ambiental
							WHERE cea_status = :cea_status
							ORDER BY cea_id ASC";
							$stmt = $PDO->prepare($sql);
							$stmt->bindValue(':cea_status', 1);
							$stmt->execute();
							$rows = $stmt->rowCount();    
							if($rows> 0)
							{	
								while($result = $stmt->fetch()){
									if($result['cea_imagem'] == ''){
										$imagem = 'core/imagens/logo.png'; 
									}
									else {
										$imagem = 'webapp/'.$result['cea_imagem']; 
									}
		
									echo "
										<a href='educacao-ambiental/".$result['cea_url']."'><div class='bloco'>
											<div class='imagem'>
												<img src='".$imagem."' style='object-fit:cover; width:85%; height:100px'>
											</div>
											<div class='texto'>
												<p class='subtitulo azul'>".$result['cea_titulo']." </p>
												<p>".$result['cea_responsavel']."</p>
											</div>
										</div></a>
									"; 
								}
							}
						}
						if($cea != ''){
								echo"<div class='descricao'>
									$cea_descricao
								</div>
								"; 
								include('compartilhe.php'); 
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
