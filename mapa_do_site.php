<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
<meta http-equiv="Content-Language" content="pt-br">

<head>
	<?php 
		include('header.php'); 
		$pagina = 'mapa_do_site'; 
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
				<p class="titulo">Mapa do Site </p>
			</div>
		</section>
		<section>
			<div class="wrapper">
				<div class='mapa'>
					<?php
						$sql = "SELECT men_id, men_titulo, men_link, men_posicao FROM aux_menu
								ORDER BY men_posicao ASC";
						$stmt = $PDO->prepare($sql);
						$stmt->execute();
						$rows = $stmt->rowCount();
						if($rows >0 ){
							while ($result = $stmt->fetch()) {
								$sql_sub = "SELECT * FROM aux_submenu
								WHERE sm_menu = :sm_menu
								ORDER BY sm_titulo ASC";
								$stmt_sub = $PDO->prepare($sql_sub);
								$stmt_sub->bindValue(':sm_menu', $result['men_id']);
								$stmt_sub->execute();
								$rows_sub = $stmt_sub->rowCount();

								if($rows_sub >0 ){
									echo"<div class='bloco'>
											<span class='destaque'>".$result['men_titulo']." </span>
											<ul>";
												while ($result_sub = $stmt_sub->fetch()) {
													$link = substr($result_sub['sm_link'], 0,4);
													if($link == "http" || $link == 'HTTP' || $link == 'Http'){
														echo"<li><i class='far fa-arrow-alt-circle-right'></i> <a href='".$result_sub['sm_link']."' target='_blank'>".$result_sub['sm_titulo']."</a></li>";
													}else{
														echo"<li><i class='far fa-arrow-alt-circle-right'></i> <a href='router/".$result_sub['sm_link']."'>".$result_sub['sm_titulo']."</a></li>";
													}
												}
									echo "</ul>
									</div>";
								}
								else {
									echo"<div class='bloco'>";
									$link = substr($result['men_link'], 0,4);
									if($link == "http" || $link == 'HTTP' || $link == 'Http'){
										echo"<li><i class='far fa-arrow-alt-circle-right'></i> <a href='".$result['men_link']."'>".$result['men_titulo']."</a></li>";
									}else{
										echo"<li><i class='far fa-arrow-alt-circle-right'></i> <a href='router/".$result['men_link']."' target='_blank'>".$result['men_titulo']."</a></li>";
									}
									echo"</div>";
								}
							}
						}
					?>
				</div>
			</div>
		</section>
		<?php
		include('core/mod_rodape/rodape.php');
		?>
	</main>
</body>

</html>