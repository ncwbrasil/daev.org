<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
<meta http-equiv="Content-Language" content="pt-br">

<head>
	<?php 
		include('header.php'); 
		$url = $_GET['p1'];	

		$sql= "SELECT * FROM cadastro_videos
		WHERE vid_url = :vid_url";
		$stmt = $PDO->prepare($sql);
		$stmt->bindValue(':vid_url', $url);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows> 0)
		{	
			$result = $stmt->fetch(); 
			$vid_titulo = $result['vid_titulo']; 
			$vid_imagem = $result['cni_foto'];
			$vid_descricao = $result['vid_descricao'];
			$vid_data = "<i class='fas fa-calendar-alt'></i> ".implode("/", array_reverse(explode("-", $result['vid_data'])));
			$vid_link   = $result['vid_link'];
			preg_match('/[\\?\\&]v=([^\\?\\&]+)/',$vid_link,$matches);

		}
		else {
			$vid_titulo = "Vídeos"; 
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
				<h1 class="titulo"><?php echo $vid_titulo ?></h1>
				<h4><center><?php echo $vid_data ?></center></h4>
			</div>
		</section>
		<section>
			<div class="wrapper noticias servicos" id='pagina'>
				<div class='conteudo'>
					<?php 
						if($url == ''){

							$sql= "SELECT * FROM cadastro_videos
							WHERE vid_status = :vid_status 
							ORDER BY vid_data DESC";
							$stmt = $PDO->prepare($sql);
							$stmt->bindValue(':vid_status', 1);
							$stmt->execute();
								$i = 0; 
								$c = 10; 
								$p = 1; 
								while($result = $stmt->fetch()){
									$i++; 
									if($i == $c){$p ++;$i= 0;}

									$vid_titulo 	= $result['vid_titulo']; 
									$data       = implode("/", array_reverse(explode("-", $result['vid_data'])));
									$vid_link   = $result['vid_link'];
									preg_match('/[\\?\\&]v=([^\\?\\&]+)/',$vid_link,$matches);
						
									echo "
										<a href='videos/".$result['vid_url']."'><div class='bloco bloco$p' style='display:none'>
											<div class='imagem'>
												<iframe width='100%' height='100px' src='https://www.youtube.com/embed/$matches[1]' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>	
											</div>
											<div class='descricao'>
												<p><span class='subtitulo azul'> $vid_titulo </span> </br>
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
							echo"
								<div class='imagem'>
								<center><iframe width='90%' height='450px' src='https://www.youtube.com/embed/$matches[1]' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe></center>
								</div><br>

								<div class='descricao'>
									$vid_descricao
								</div>
							"; 
						}
					?>	
				</div>

				<div class='veja_mais'>									
					<div class='bloco'>
						<h3> Categorias </h3>
						<ul>
							<?php 
								$sql= "SELECT *	FROM cadastro_categoria_noticias";
								$stmt = $PDO->prepare($sql);
								$stmt->execute();
								$rows = $stmt->rowCount(); 
								if($rows> 0)
								{			
									while($result = $stmt->fetch()){

											echo "<li><a href='noticias/".$result['cn_url']."'><i class='far fa-arrow-alt-circle-right'></i> ".$result['cn_nome']."</a></li>";
									}
								}								
							?>
						</ul>
					</div>

					<div class='bloco'>
						<h3> Veja Mais  </h3>
						<ul>
							<li><a href='noticias'><i class='far fa-arrow-alt-circle-right'></i> Notícias </a></li>
							<li><a href='galeria-de-fotos'><i class='far fa-arrow-alt-circle-right'></i> Galeria de Fotos </a></li>
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

