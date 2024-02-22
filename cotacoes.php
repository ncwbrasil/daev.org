<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
<meta http-equiv="Content-Language" content="pt-br">

<head>
	<?php
	include('header.php');
	$url = $_GET['p1'];

	$sql = "SELECT * FROM cadastro_cotacoes
		WHERE cot_url = :cot_url AND cot_status = :cot_status";
	$stmt = $PDO->prepare($sql);
	$stmt->bindValue(':cot_url', $url);
	$stmt->bindValue(':cot_status', 1);
	$stmt->execute();
	$rows = $stmt->rowCount();
	if ($rows > 0) {
		$result = $stmt->fetch();
		$cot_titulo = $result['cot_titulo'];
		$cot_documento = $result['cot_documento'];
		$cot_descricao = $result['cot_descricao'];
		$cot_data = "<i class='fas fa-calendar-alt'></i> " . date("d/m/Y", strtotime($result['cot_data']));
		$cot_id = $result['cot_id'];
	} else {
		$cot_titulo = "Cotações";
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
				<h1 class="titulo"><?php echo $cot_titulo ?></h1>
				<h4>
					<center><?php echo $cot_data ?></center>
				</h4>
			</div>
		</section>
		<section>
			<div class="wrapper noticias servicos" id='pagina'>
				<div class='conteudo'>
					<?php
					if ($url == '') {

						$sql = "SELECT * FROM cadastro_cotacoes
							WHERE cot_status = :cot_status 
							ORDER BY cot_id DESC";
						$stmt = $PDO->prepare($sql);
						$stmt->bindValue(':cot_status', 1);
						$stmt->execute();
						$i = 0;
						$c = 10;
						$p = 1;
						while ($result = $stmt->fetch()) {
							$i++;
							if ($i == $c) {
								$p++;
								$i = 0;
							}

							$cot_titulo 	= $result['cot_titulo'];
							$cot_documento 	= $result['cot_documento'];
							$data       = date("d/m/Y", strtotime($result['cot_data']));

							echo "
										
											<div class='descricao'>
												<a href='cotacoes/" . $result['cot_url'] . "'><div class='bloco bloco$p' style='display:none'>
												<p><span class='subtitulo azul'> $cot_titulo </span> </br></a>
												<span class='data'><i class='fas fa-calendar-alt'></i> $data </span> </p>
												<a href='webapp/" . $result['cot_documento'] . "' title='" . $result['cot_documento'] . "'>
													<div class='down'>
														<i class='fas fa-file-alt'></i> Download
													</div>
												</a>											
											</div>
										</div>
									";
						}

						echo "<div class='paginacao'>";
						for ($z = 1; $z <= $p; $z++) {
							echo "<button id='bloco$z' class='pg' value='bloco$z'> $z </button>";
						}
						echo "
							</div>";
					}

					if ($url != '') {
						echo "
								<div class='descricao'>
									$cot_descricao

									<a href='webapp/$cot_documento' title='$cot_documento'>
										<div class='down'>
											<i class='fas fa-file-alt'></i>  Download
										</div>
									</a>
									
									<a href='router/orcamento' title='$cot_documento'>
										<div class='down' style='background:#e63323;'>
											<i class='fas fa-money-check-alt'></i>  Faça um Orçamento
										</div>
									</a>


								</div>


							";
						include('compartilhe.php');
					}
					?>
				</div>

				<div class='veja_mais'>
					<div class='bloco'>
						<h3 class="vermelho"> Cotações Disponíveis </h3>
						<ul>
							<?php
							$sql = "SELECT * FROM cadastro_cotacoes
							WHERE cot_status = :cot_status limit 4";
							$stmt = $PDO->prepare($sql);
							$stmt->bindValue(':cot_status', 1);
							$stmt->execute();
							$rows = $stmt->rowCount();
							if ($rows > 0) {
								while ($result = $stmt->fetch()) {
									echo "<li><a href='cotacoes/".$result['cot_url']."'><i class='fas fa-cloud-download-alt'></i> " . $result['cot_titulo'] . "</a></li>";
								}
							}
							?>
						</ul>
						<center><a href='cotacoes/'>Veja Mais </a></center>
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
	$(document).ready(function() {
		$('.bloco1').css('display', 'table');
		$('button').click(function() {
			let valor = $(this).val();
			$('.conteudo .bloco').css('display', 'none');
			$('.' + valor).css('display', 'table');
			$('.pg').css('background', '#2f91ce');
			$('.pg').css('color', '#fff');

			$('#' + valor).css('color', '#2f91ce');
			$('#' + valor).css('background', '#fff');
		});
	});

	// $("#paginacao").change(function(){
	// 	let valor = $(this).val();  
	// 	$('.conteudo .bloco').css('display','none'); 
	// 	$('.'+valor).css('display','table'); 
	// })
</script>