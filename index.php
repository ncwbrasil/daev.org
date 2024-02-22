<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
<meta http-equiv="Content-Language" content="pt-br">

<head>
	<?php
	include('header.php');
	$pagina = 'home';
	?>
	<link rel="stylesheet" type="text/css" href="slick/slick.css">
	<link rel="stylesheet" type="text/css" href="slick/slick-theme.css">

	<title><?php echo $ttl; ?></title>
</head>

<body>
	<header>
		<?php
		#region MOD INCLUDES
		include('core/mod_topo/topo.php');
		include('core/mod_includes/php/funcoes-jquery.php');
		include('banner.php');
		#endregion
		?>
	</header>
	<main class="home" id='conteudo'>
		<section class='destaques'>
			<div class="wrapper">
				<div id='destaques'>
					<?php
					echo "<div class='bloco_l destaque1'>";
					$sql_destaques = "SELECT * FROM aux_submenu
								WHERE sm_home = :sm_home
								ORDER BY sm_posicao_home
								LIMIT :limite";
					$stmt_destaques = $PDO->prepare($sql_destaques);
					$stmt_destaques->bindValue(':sm_home', 1);
					$stmt_destaques->bindValue(':limite', 5);
					$stmt_destaques->execute();
					$rows_destaques = $stmt_destaques->rowCount();
					$i = 1;
					if ($rows_destaques > 0) {
						while ($result_destaques = $stmt_destaques->fetch()) {

							if ($i == 1) {
								$cor = 'verde';
							} else {
								$cor = "branco";
							}
							$link = substr($result_destaques['sm_link'], 0, 4);
							if ($link == "http" || $link == 'HTTP' || $link == 'Http') {

								echo "<div class='list' id='$cor'><a href='" . $result_destaques['sm_link'] . "' target='_blank'>" . $result_destaques['sm_titulo'] . "</a></div>";
							} else {
								echo "<div class='list' id='$cor'><a href='router/" . $result_destaques['sm_link'] . "'>" . $result_destaques['sm_titulo'] . "</a></div>";
							}
							$i++;
						}
					}

					echo "</div>
						";
					?>
					<a href='https://valinhos.strategos.com.br:8443/AgenciaVirtual/' target="_blank"><div class='bloco_r destaque2'>
						<h1> Acesse Sua Conta de Água </h1>
						<p class="azul2">Acesse de forma fácil e simples as informações de sua conta de água e esgoto.</p>
					</div></a>

				</div>
			</div>
		</section>

		<section id='home_servicos'>
			<div class="wrapper">
				<h2 class="azul"> Catálogo de Serviços </h2>
				<?php
				echo "<div class='slides3'>";
				$sql_serv = "SELECT sm_titulo, sm_link, sm_servico, sm_posicao_servico, sm_icone FROM aux_submenu WHERE sm_servico = :sm_servico
						ORDER BY sm_titulo";
				$stmt_serv = $PDO->prepare($sql_serv);
				$stmt_serv->bindValue(':sm_servico', 1);
				$stmt_serv->execute();
				$rows_serv = $stmt_serv->rowCount();
				if ($rows_serv > 0) {
					$cor = 1;
					while ($result_serv = $stmt_serv->fetch()) {
						if ($cor > 19) {
							$cor = 1;
						}
						$link = substr($result_serv['sm_link'], 0, 4);
						if ($link == "http" || $link == 'HTTP' || $link == 'Http') {
							$sm_link = $result_serv['sm_link']; 
						} else {
							$sm_link = "router/" . $result_serv['sm_link'];
						}
						echo "<a href='$sm_link'><div class='item color$cor'> 
									" . $result_serv['sm_icone'] . "
									<div class='desc'>
										" . $result_serv['sm_titulo'] . "
									</div>
								</div></a>";

						$cor++;
					}
				}
				echo "</div>";
				?>
			</div>
		</section>

		<section id='contato'>
			<div class="wrapper">
				<h1 class='titulo azul'> Entre em contato conosco </h1>
				<?php
					$sql_contato = "SELECT * FROM aux_configuracao";
					$stmt_contato = $PDO->prepare($sql_contato);
					$stmt_contato->execute();
					$result_contato = $stmt_contato->fetch();

					echo "
						<div class='bloco' id='bl1'>
							<h1>E-mail</h1>
							<h4>".$result_contato['conf_email']." </h4>
						</div>
						<div class='bloco' id='bl2'>
							<h1>WhatsApp </h1>
							<h4><i class='fab fa-whatsapp'></i> ".$result_contato['conf_whats']." </h4>
						</div>
						<div class='bloco' id='bl3'>
							<h1>Central de Atendimento</h1>
							<h4><i class='fas fa-phone-alt'></i> ".$result_contato['conf_contato']."</h4>
						</div>
						<div class='bloco' id='bl4'>
							<h1>Redes sociais</h1>
							<h4><a href='".$result_contato['conf_face']."' target='_blank'><i class='fab fa-facebook-square'></i> daev.valinhos </a><br>
							<a href='".$result_contato['conf_instagram']."' target='_blank'> <i class='fab fa-instagram-square'></i> daev.valinhos </a></h4>

						</div>
					"; 
				?>
			</div>
		</section>

		<section id='noticias'>
			<div class="wrapper">
				<h1 class='titulo azul'> Notícias e informações importantes </h1>
				<?php
				$sql_noticias = "SELECT * FROM cadastro_noticias 
					LEFT JOIN cadastro_noticias_imagens ON cadastro_noticias_imagens.cni_id = cadastro_noticias.nt_foto
					LEFT JOIN cadastro_categoria_noticias ON cadastro_categoria_noticias.cn_id = cadastro_noticias.nt_categoria
					WHERE nt_status = :nt_status AND nt_data <= :nt_data
					ORDER BY nt_data DESC
					LIMIT :limite";
				$stmt_noticias = $PDO->prepare($sql_noticias);
				$stmt_noticias->bindValue(':nt_status', 1);
				$stmt_noticias->bindValue(':nt_data', date('Y-m-d H:i:s'));
				$stmt_noticias->bindValue(':limite', 4);
				$stmt_noticias->execute();
				$rows_noticias = $stmt_noticias->rowCount();
				if ($rows_noticias > 0) {

					while ($result_noticias = $stmt_noticias->fetch()) {
						$data = date_create($result_noticias['nt_data']); 

						if ($result_noticias['cn_url'] == ''){
							$cat = 'materias';
						}else {
							$cat = $result_noticias['cn_url']; 
						}

						echo "<a href='noticias/$cat/" . $result_noticias['nt_url'] . "' title='Noticia: " . $result_noticias['nt_titulo'] . "'><div class='bloco'>
								<img src='webapp/uploads/noticias/" . $result_noticias['cni_foto'] . "' style='object-fit:cover; width:100%; height:200px; margin:0 auto; margin-bottom: 30px;'>
								<div class='desc'>" . $result_noticias['nt_titulo'] . " </div><br>
								<span class='data'><i class='fas fa-calendar-alt'></i> " . date_format($data,'d/m/Y H:i') . " </span>
							</div></a>";
						$i++;
					}
				}
				?>
				<div style='width:100%; display:table; font-size:18px; font-weight:bold'><center><a href="noticias/"> Veja mais </a></center></div>
			</div>
		</section>

		<section id='sala_situacao'>
			<div class="wrapper">
				<h1 class='titulo azul'> Sala de Situação </h1>
					<?php
						$sql_sala = "SELECT * FROM cadastro_sala 
						LEFT JOIN cadastro_sala_descricao ON cadastro_sala_descricao.csd_sala = cadastro_sala.cs_id
						WHERE cs_status = :cs_status AND cs_destaque = :cs_destaque
						ORDER BY csd_id DESC"; 
						$stmt_sala = $PDO->prepare($sql_sala);
						$stmt_sala->bindValue(':cs_status', 1);
						$stmt_sala->bindValue(':cs_destaque', 1);
						$stmt_sala->execute();
						$rows_sala = $stmt_sala->rowCount();
						if ($rows_sala > 0) {
							$result_sala = $stmt_sala->fetch(); 

							if ($result_sala['cs_bandeira'] == 1){
								echo "
									<div class='sala_destaque' style='background-color:".$result_sala['cs_cor']."'>
										" . $result_sala['cs_icone'] . " <h2> " . $result_sala['cs_titulo'] . "</h2>
										<p>" . $result_sala['csd_descricao'] . " </p>
										<span class='data'><i class='fas fa-calendar-alt'></i> " .date( "d/m/Y", strtotime($result_sala['csd_data'])). " </span>
									</div>
								";	
							}
							else {
								echo "
									<div class='sala_destaque'>
										" . $result_sala['cs_icone'] . " <h2> " . $result_sala['cs_titulo'] . "</h2>
										<p>" . $result_sala['csd_descricao'] . " </p>
										<span class='data'><i class='fas fa-calendar-alt'></i> " .date( "d/m/Y", strtotime($result_sala['csd_data'])). " </span>
									</div>
								";	
							}
						}
					?>			

				
					<?php
						$sql_sala = "SELECT * FROM cadastro_sala 
						WHERE cs_status = :cs_status AND cs_destaque = :cs_destaque
						ORDER BY cs_titulo ASC"; 
						$stmt_sala = $PDO->prepare($sql_sala);
						$stmt_sala->bindValue(':cs_status', 1);
						$stmt_sala->bindValue(':cs_destaque', 0);
						$stmt_sala->execute();
						$rows_sala = $stmt_sala->rowCount();
						if ($rows_sala > 0) {
							$i=1;
							while($result_sala = $stmt_sala->fetch()){
								$sql_desc = "SELECT * FROM cadastro_sala_descricao 
								WHERE csd_sala = :csd_sala 
								ORDER BY csd_id DESC"; 
								$stmt_desc = $PDO->prepare($sql_desc);
								$stmt_desc->bindValue(':csd_sala', $result_sala['cs_id']);
								$stmt_desc->execute();
								$rows_desc = $stmt_desc->rowCount();
								if ($rows_desc > 0) {
									$result_desc = $stmt_desc->fetch();
									if($i == 11){$i = 1;}
									echo "
										<div class='sala_bloco bl$i'>
											<div class='icone'>" . $result_sala['cs_icone'] . "</div>
											<div class='ttl'><h3>" . $result_sala['cs_titulo'] . "</h3></div>
											<div class='desc'><p>" . $result_desc['csd_descricao'] . " </p></div>
											<span class='data'><i class='fas fa-calendar-alt'></i> " . date( "d/m/Y", strtotime($result_desc['csd_data'])). " </span>
										</div>
									";
								}
								$i++;
							} 
						}
					?>



				
				<div style='width:100%; display:table; font-size:18px; font-weight:bold'><center><a href="sala-de-situacao/"> Veja mais </a></center></div>

			</div>
		</section>

		<section id='licitacoes'>
			<div class="wrapper">
				<h1 class='titulo branco'> Licitações e Contratos </h1>
				<?php
				$sql_lic = "SELECT * FROM licitacao_pregao WHERE exibir = :exibir
					ORDER BY data_abertura DESC
					LIMIT :limite";
				$stmt_lic = $PDO->prepare($sql_lic);
				$stmt_lic->bindValue(':exibir', 1);
				$stmt_lic->bindValue(':limite', 4);
				$stmt_lic->execute();
				$rows_lic = $stmt_lic->rowCount();
				if ($rows_lic > 0) {
					while ($result_lic = $stmt_lic->fetch()) {
						$data = date("d/m/Y", strtotime($result_lic['data_abertura']));
						echo "
								<div class='item'>
									<div class='data'>
									<i class='far fa-calendar-alt'></i>
										<h2>$data</h2> <br>
									</div>
									<div class='codigo'>
										<p class='subtitulo'>" . $result_lic['codigo'] . "</p>
										<p>" . $result_lic['titulo'] . "</p>
										<p>Etapas:";
						if ($result_lic['abertura_status'] == '3') {
							echo "<span class='situacao'> Aberto </span>";
						}
						if ($result_lic['habilitacao_status'] == '3') {
							echo "<span class='situacao'> Habilitado </span>";
						}
						if ($result_lic['julgamento_status'] == '3') {
							echo "<span class='situacao'> Em Julgamento </span>";
						}
						if ($result_lic['homologacao_status'] == '3') {
							echo "<span class='situacao'> Homologado </span>";
						}
						echo "</div>

									<div class='acessar'>
										<a href='licitacoes/" . $result_lic['lic_id'] . "'><button>
											Veja mais
										</button></a>
									</div>
								</div>
							";
					}
				}
				?>

				<center><a href="licitacoes/"><button> Mais Licitações  </button></a></center>
			</div>
		</section>

		<?php
		include('core/mod_rodape/rodape.php');
		?>
	</main>
</body>

</html>
<script src="https://code.jquery.com/jquery-2.2.0.min.js" type="text/javascript"></script>
<script src="slick/slick.js" type="text/javascript" charset="utf-8"></script>


<script>
	$(function() {
		switchable({
			$element: $('#slides2'),
			interval: 2000,
			effect: 'fade'
		});
	});

	$(document).ready(function() {
		var largura = $(window).width();

		if (largura > 1350) {
			$(".slides3").slick({
				dots: false,
				infinite: true,
				centerMode: false,
				slidesToShow: 6,
				slidesToScroll: 2, 
			});
		}
		if (largura < 1350 && largura > 970) {
			$(".slides3").slick({
				dots: false,
				infinite: true,
				centerMode: false,
				slidesToShow: 4,
				slidesToScroll: 2
			});
		}

		if (largura < 970 && largura > 690) {
			$(".slides3").slick({
				dots: false,
				infinite: true,
				centerMode: false,
				slidesToShow: 3,
				slidesToScroll: 2
			});
		}

		if (largura < 690 && largura > 590) {
			$(".slides3").slick({
				dots: false,
				infinite: true,
				centerMode: false,
				slidesToShow: 2,
				slidesToScroll: 2
			});
		}

		if (largura < 590) {
			$(".slides3").slick({
				dots: false,
				infinite: true,
				centerMode: false,
				slidesToShow: 1,
				slidesToScroll: 1,
				centerPadding: '9%',

			});
		}

	});
</script>