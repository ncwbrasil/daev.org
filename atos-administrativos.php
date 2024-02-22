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
				<p class="titulo">Atos Administrativos</p>
			</div>
		</section>
		<section>
			<div class="wrapper atos-administrativos" id='pagina'>
				<div class='conteudo'>
					<?php 
						$sql= "SELECT * FROM cadastro_atos_administrativos
						ORDER BY cad_titulo ASC";
						$stmt = $PDO->prepare($sql);
						$stmt->execute();
						$rows = $stmt->rowCount();    
						if($rows> 0)
						{	
							while($result = $stmt->fetch()){
								echo "
									<a href='router/".$result['cad_compartilhamento']."' target='_blank' title='".$result['cad_titulo']."'>
										<div class='bloco'>
											<p class='subtitulo azul'> <i class='fas fa-external-link-alt'></i> ".$result['cad_titulo']." </p>
										</div>
									</a>
								"; 
							}
						}
						else{
							echo "Nenhum Ato Administrativo cadastrado no momento."; 
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
								$stmt->bindValue(':men_id', 24);
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
