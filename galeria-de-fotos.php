<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
<meta http-equiv="Content-Language" content="pt-br">

<head>
	<?php 
		include('header.php'); 
		$url = $_GET['p1'];	

		$sql= "SELECT * FROM cadastro_galeria
		WHERE gal_url = :gal_url";
		$stmt = $PDO->prepare($sql);
		$stmt->bindValue(':gal_url', $url);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows> 0)
		{	
			$result = $stmt->fetch(); 
			$gal_titulo = $result['gal_titulo']; 
			$gal_descricao = $result['gal_descricao'];
			$gal_data = "<i class='fas fa-calendar-alt'></i> ".implode("/", array_reverse(explode("-", $result['gal_data'])));
		}
		else {
			$gal_titulo = "Galeria de Fotos"; 
		}

	?>
	<link rel="stylesheet" type="text/css" href="core/mod_includes/js/shadowbox/shadowbox.css">
	<script type="text/javascript" src="core/mod_includes/js/shadowbox/shadowbox.js"></script>
	<script type="text/javascript">
		Shadowbox.init();
	</script>

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
				<h1 class="titulo"><?php echo $gal_titulo ?></h1>
				<h4><center><?php echo $gal_data ?></center></h4>
			</div>
		</section>
		<section>
			<div class="wrapper noticias servicos" id='pagina'>
				<div class='conteudo'>
					<?php 
						if($url == ''){

							$sql= "SELECT * FROM cadastro_galeria
							WHERE gal_status = :gal_status 
							ORDER BY gal_data DESC";
							$stmt = $PDO->prepare($sql);
							$stmt->bindValue(':gal_status', 1);
							$stmt->execute();
								$i = 0; 
								$c = 10; 
								$p = 1; 
								while($result = $stmt->fetch()){
									$i++; 
									if($i == $c){$p ++;$i= 0;}

									$gal_titulo 	= $result['gal_titulo']; 
									$gal_imagem 	= $result['gal_imagem']; 
									$data       = implode("/", array_reverse(explode("-", $result['gal_data'])));

									echo "
										<a href='galeria-de-fotos/".$result['gal_url']."'><div class='bloco bloco$p' style='display:none'>
											<div class='imagem'>
												<img src='webapp/$gal_imagem' style='object-fit:cover; width:100%; height:150px' >
											</div>
											<div class='descricao'>
												<p><span class='subtitulo azul'> $gal_titulo </span> </br>
												<span class='data'><i class='fas fa-calendar-alt'></i> $data </span> </p>
											</div>
										</div></a>
									"; 
								}
								
								echo "<div class='paginacao'>
									<select id='paginacao'>";
										for($z=1; $z<=$p; $z++){
											echo"<option value='bloco$z'> $z </option>";
										}
									echo"</select>
									<i> <center>Páginas</center> </i>
								</div>"; 
						}

						if($url != ''){
							echo "<div class='desc'> $gal_descricao </div>";

							$sql2= "SELECT * FROM cadastro_galeria
							LEFT JOIN cadastro_fotos_galeria ON cadastro_fotos_galeria.fg_galeria = cadastro_galeria.gal_id
							WHERE gal_url = :gal_url";
							$stmt2 = $PDO->prepare($sql2);
							$stmt2->bindValue(':gal_url', $url);
							$stmt2->execute();
							$rows2 = $stmt2->rowCount();
							if($rows2> 0)
							{	
								while($result2 = $stmt2->fetch()){
									echo "
									<a href='webapp/".$result2['fg_imagem']."'  rel='shadowbox[fotos]' title='$gal_titulo'>
										<img src='webapp/".$result2['fg_imagem']."' title='$gal_titulo' style='object-fit:cover; width:31%; height:200px; float:left; margin:1%;'>
									</a>";
								} 								
							}
							else {
								echo "<p>Não há fotos cadastradas nesta galeria.";
							}
						}
					?>	
				</div>

				<div class='veja_mais'>									
					<div class='bloco'>
						<h3> Veja Mais  </h3>
						<ul>
							<li><a href='videos'><i class='far fa-arrow-alt-circle-right'></i> Vídeos </a></li>
							<li><a href='noticias'><i class='far fa-arrow-alt-circle-right'></i> Notícias </a></li>
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

	$("#paginacao").change(function(){
		let valor = $(this).val();  
		$('.conteudo .bloco').css('display','none'); 
		$('.'+valor).css('display','table'); 
	})
</script>

