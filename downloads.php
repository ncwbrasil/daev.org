<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
<meta http-equiv="Content-Language" content="pt-br">

<head>
	<?php 
		include('header.php'); 
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
				<p class="titulo"> Downloads </p>
			</div>
		</section> 
		<section>
			<div class="wrapper downloads servicos" id='pagina'>
				<div class='conteudo'>
					<div class="busca_downloads">
						<select id='categoria' name='categoria'>
							<option>Filtre por uma Categoria </option>
							<?php 
								$sql_cat = "SELECT * FROM cadastro_downloads
								INNER JOIN cadastro_downloads_categorias ON cadastro_downloads_categorias.cat_id = cadastro_downloads.id_categoria
								GROUP BY cat_id
								ORDER BY cat_nome";
								$stmt_cat = $PDO->prepare($sql_cat);
								$stmt_cat->execute();
								$rows_cat = $stmt_cat->rowCount(); 
								if($rows_cat> 0)
								{		
													
									while($result_cat = $stmt_cat->fetch()){
										echo "
											<option value='".$result_cat['cat_id']."'>".$result_cat['cat_nome']."</option>
										";
									}
								}								

							?> 
						</select> 
					</div>

					<div class="lista_downloads">				
					</div>

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

		<?php
		include('core/mod_rodape/rodape.php');
		?>
	</main>
</body>

</html>

<script>
	$(document).ready(function(){
		recuperaDownloads(); 
	});

	$('select[name=categoria]').change(function(){
		var cat = $(this).val(); 
		recuperaDownloads(cat);
	})				


	function recuperaDownloads(cat){
		$(".lista_downloads").html('<center><img src="core/imagens/loading.gif"></center>');
		$.post("carrega_conteudo.php",{pagina: 'downloads', categoria: cat},
			function(valor){
				$(".lista_downloads").html(valor);
			}
		)
	}

</script>
