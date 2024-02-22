<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
<meta http-equiv="Content-Language" content="pt-br">

<head>
	<?php 
		include('header.php'); 
		$url = $_GET['p2'];	
		$categoria = $_GET['p1'];	

		$sql= "SELECT * FROM cadastro_noticias
		LEFT JOIN cadastro_noticias_imagens ON cadastro_noticias_imagens.cni_id = cadastro_noticias.nt_foto
		WHERE nt_url = :nt_url";
		$stmt = $PDO->prepare($sql);
		$stmt->bindValue(':nt_url', $url);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows> 0)
		{	
			$result = $stmt->fetch(); 
			$data = date_create($result['nt_data']); 
			$nt_titulo = $result['nt_titulo']; 
			$nt_subtitulo = $result['nt_subtitulo']; 
			$nt_imagem = $result['cni_foto'];
			$nt_descricao = $result['nt_descricao'];
			$nt_data = "<i class='fas fa-calendar-alt'></i> ".date_format($data,'d/m/Y H:i');
			$nt_ttl_imagem = $result['cni_titulo']; 
			$nt_hora_cadastro = $result['nt_hora_cadastro']; 
			$nt_id = $result['nt_id']; 
		}
		else {
			$nt_titulo = "Notícias"; 
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
				<h1 class="titulo"><?php echo $nt_titulo ?></h1>
				<h4><center><?php echo $nt_data ?></center></h4>
				<i><center> <?php if($nt_hora_cadastro !=''){ echo "Última Atualização :  ". date("d/m/Y, g:i a", strtotime($nt_hora_cadastro));}?></i>
			</div>
		</section>
		<section>
			<div class="wrapper noticias servicos" id='pagina'>
				<div class='conteudo'>
					<?php 
						if($url == '' && $categoria == ''){
							 
							$data = date('Y-m-d'); 
							$hora = date('H:i:s'); 

							$sql= "SELECT * FROM cadastro_noticias
							LEFT JOIN cadastro_noticias_imagens ON cadastro_noticias_imagens.cni_id = cadastro_noticias.nt_foto
							LEFT JOIN cadastro_categoria_noticias ON cadastro_categoria_noticias.cn_id = cadastro_noticias.nt_categoria
							WHERE nt_status = :nt_status AND nt_data <= :nt_data
							ORDER BY nt_data DESC";
							$stmt = $PDO->prepare($sql);
							$stmt->bindValue(':nt_status', 1);
							$stmt->bindValue(':nt_data', date('Y-m-d H:i:s'));
							$stmt->execute();
								$i = 0; 
								$c = 10; 
								$p = 1; 
								while($result = $stmt->fetch()){								
									$i++; 
									if($i == $c){$p ++;$i= 0;}	
									$nt_titulo 	= $result['nt_titulo']; 
									$nt_subtitulo 	= $result['nt_subtitulo']; 
									$nt_imagem 	= $result['cni_foto']; 
									$data = date_create($result['nt_data']); 
									if ($result['cn_url'] == ''){
										$cat = 'materias';
									}else {
										$cat = $result['cn_url']; 
									}

									echo "
										<a href='noticias/$cat/".$result['nt_url']."'><div class='bloco bloco$p' style='display:none'>
											<div class='imagem'>
												<img src='webapp/uploads/noticias/$nt_imagem' style='object-fit:cover; width:100%; height:150px' >
											</div>
											<div class='descricao'>
												<p><span class='subtitulo azul'> $nt_titulo </span> </br>
												<p class='cinza'>$nt_subtitulo</p>
												<span class='data'><i class='fas fa-calendar-alt'></i>  ". date_format($data,'d/m/Y H:i') ." </span> </p>
											</div>
										</div></a>
									"; 

								}
								
								echo "<div class='paginacao' style='border:none; '>
									<select id='paginacao' style='padding:10px; border: 1px solid #2f91ce; color :#2f91ce; font-size:15px'>";
										for($z=1; $z<=$p; $z++){
											echo"<option value='bloco$z'> $z </option>";
										}
									echo"</select>
									<i> <center>Páginas</center> </i>
								</div>"; 
						}

						if($categoria !== '' && $url =='' ){
							$sql= "SELECT * FROM cadastro_noticias
							LEFT JOIN cadastro_noticias_imagens ON cadastro_noticias_imagens.cni_id = cadastro_noticias.nt_foto
							LEFT JOIN cadastro_categoria_noticias ON cadastro_categoria_noticias.cn_id = cadastro_noticias.nt_categoria
							WHERE nt_status = :nt_status AND cn_url = :cn_url
							ORDER BY nt_data DESC";
							$stmt = $PDO->prepare($sql);
							$stmt->bindValue(':nt_status', 1);
							$stmt->bindValue(':cn_url', $categoria);
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

									$nt_titulo 	= $result['nt_titulo']; 
									$nt_imagem 	= $result['cni_foto']; 
									$data = date_create($result['nt_data']); 

									if ($result['cn_url'] == ''){
										$cat = 'materias';
									}else {
										$cat = $result['cn_url']; 
									}

									echo "
										<a href='noticias/$cat/".$result['nt_url']."'><div class='bloco bloco$p' style='display:none'>
											<div class='imagem'>
												<img src='webapp/uploads/noticias/$nt_imagem' style='object-fit:cover; width:100%; height:150px' >
											</div>
											<div class='descricao'>
												<p><span class='subtitulo azul'> $nt_titulo </span> </br>
												<span class='data'><i class='fas fa-calendar-alt'></i> ". date_format($data,'d/m/Y H:i') ."  </span> </p>
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
							else {
								echo "<center>Não há notícias cadastradas nessa categoria no momento!</center>";
							}
						}

						if($url != ''){
							echo"
								<h3 class='azul'> $nt_subtitulo </h3>
								<div class='imagem'>
									<img src='webapp/uploads/noticias/$nt_imagem' style='width:100%; max-width:620px; margin:0 auto'>
									<i>$nt_ttl_imagem</i>
								</div><br>

								<div class='descricao'>
									$nt_descricao
								</div>
							"; 
							include('compartilhe.php');

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
							<li><a href='videos'><i class='far fa-arrow-alt-circle-right'></i> Vídeos </a></li>
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
