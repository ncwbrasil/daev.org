<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-p">
<meta http-equiv="Content-Language" content="pt-p">

<head>
	<?php 
		include('header.php'); 
		$lic_id = $_GET['p1'];
	?>
	<title><?php echo $ttl; ?></title>
	<script>
	        $(document).ready(function() {
				var ano = localStorage.getItem('ano'); 
				if(ano !== null){
					recupera_ano(ano); 
				}
				localStorage.removeItem('ano');     
			});
	</script> 
</head>

	<header>
		<?php
			#region MOD INCLUDES
			include('core/mod_topo/topo.php');
			include('core/mod_includes/php/funcoes-jquery.php');
			#endregion
		?>
	</header>
	<main>
		<?php
			if($lic_id != ''){
				$sql= "SELECT *	FROM licitacao_pregao 
				LEFT JOIN licitacao_categorias ON licitacao_categorias.lc_id = licitacao_pregao.id_categoria
				WHERE lic_id = :lic_id";
				$stmt = $PDO->prepare($sql);
				$stmt->bindParam(':lic_id', $lic_id);
				$stmt->execute();
				$rows = $stmt->rowCount(); 
				if($rows> 0)
				{						
					$result = $stmt->fetch();
					$date = date_create($result['data_abertura']);
					$data_abertura = date_format($date , 'd/m/Y');
					$hora_abertura = date_format($date , 'H:i:s');
					$ano = date_format($date , 'Y');
					echo"
						<section>
							<div class='banner-top'>
								<h1 class='titulo'>".$result['codigo']."</h1>
								<center>".$result['titulo']."</center>
							</div>
						</section>

						<section>
							<div class='wrapper' id='licitacao'>
								
								<div class='detalhes'>
								
									<h1 class='titulo1'> Detalhes </h1>
									<p><b>Categoria:</b>  ".$result['lc_titulo']." </p>
									<p><b>Código DAEV:</B>  ".$result['codigo']." </p>";
									if($result['numero_processo'] != ''){echo "<p><b>Processo nº:</b>".$result['numero_processo']."</p>"; }
									echo"<p><b>Título da Licitação:</B>  ".$result['titulo']." </p>
									<p><b>Data de Abertura:</B>  $data_abertura às $hora_abertura </p>
									<p><b>Objetivo:</B>  ".$result['objetivo']." </p>";
									if($result['comunicado'] != ''){
										echo "<p><b>Comunicado:</B>  ".$result['comunicado']." </p>";
									}
									echo ($result['situacao'] == '1' ? '<p><b>Situação:</b> Cancelado' : ($result['situacao'] == '2' ? '<p><b>Situação:</b> Revogado' : ''));

									if($result['habilitacao_status'] != 0 && $result['habilitacao_status'] != '' && $result['habilitacao_status'] != Null){
										$date1 = date_create($result['data_habilitacao']);
										$data_habilitacao = date_format($date1 , 'd/m/Y');
										$hora_habilitacao = date_format($date1 , 'H:i:s');

										echo "
											<p><b>Data habilitação:</b> $data_habilitacao às $hora_habilitacao </p>							
											<p><b>Comunicado habilitação:</b> ".$result['comunicado_habilitacao']."</p>							
										"; 
									}

									if($result['julgamento_status'] != 0 && $result['julgamento_status'] != '' && $result['julgamento_status'] != Null){
										$date2 = date_create($result['data_julgamento']);
										$data_julgamento = date_format($date2 , 'd/m/Y');
										$hora_julgamento = date_format($date2 , 'H:i:s');
										echo "
											<p><b>Data julgamento:</b> $data_julgamento às $hora_julgamento </p>							
											<p><b>Comunicado julgamento:</b> ".$result['comunicado_julgamento']."</p>							
										"; 
									}

									if($result['homologacao_status'] != 0 && $result['homologacao_status'] != '' && $result['homologacao_status'] != Null){
										$date3 = date_create($result['data_homologacao']);
										$data_homologacao = date_format($date3 , 'd/m/Y');
										$hora_homologacao = date_format($date3 , 'H:i:s');
										echo "
											<p><b>Data homologação:</b> $data_homologacao às $hora_homologacao</p>							
											<p><b>Comunicado homologação:</b> ".$result['comunicado_homologacao']."</p>							
										"; 
									}

								echo "
									<div class='compartilhar'>
										<h3> Compartilhe: </h3>
										<a href='https://www.facebook.com/sharer/sharer.php?u=localhost".$_SERVER["REQUEST_URI"]."' target='_blank'><button class='imprimir' title='Compartilhar no Facebook'><i class='fab fa-facebook-f'></i></button></a>

										<a href='https://twitter.com/intent/tweet?url=localhost".$_SERVER["REQUEST_URI"]."' target='_blank'><button class='imprimir' title='Compartilhar no Twitter'><i class='fab fa-twitter'></i></button></a>

										<a href='https://www.linkedin.com/shareArticle?mini=true&url=localhost".$_SERVER["REQUEST_URI"]."' target='_blank'><button class='imprimir' title='Compartilhar no Linkedin'><i class='fab fa-linkedin-in'></i></button></a>

										<a href='https://api.whatsapp.com/send?text=$nt_titulo - localhost".$_SERVER["REQUEST_URI"]."' target='_blank'><button class='imprimir' title='Compartilhar no Whatsapp'><i class='fab fa-whatsapp'></i></button>
										<button class='imprimir' onclick='window.print()' title='Imprimir Tela'><i class='fas fa-print'></i></button> </a>
									</div>	
									<span class='voltar' onclick='voltarAno($ano)'> <i class='fas fa-arrow-left'></i> Voltar à listagem </span><br><br>		
								
								</div>

								<div class='veja_mais'>
									<div class='bloco'>
										<h3 class='vermelho'> Downloads </h3>";
										$sql_d = "SELECT *	FROM licitacao_pregao 
										LEFT JOIN licitacao_edital ON licitacao_edital.id_licitacao = licitacao_pregao.lic_id
										WHERE lic_id = :lic_id";
										$stmt_d = $PDO->prepare($sql_d);
										$stmt_d->bindParam(':lic_id', $lic_id);
										$stmt_d->execute();
										$rows_d = $stmt_d->rowCount(); 
										if($rows_d> 0)
										{	
											echo "<ul>"; 
											
											while($result_d = $stmt_d->fetch()){
												
												if($result_d['le_titulo'] ==''){
													echo "<li> <a href='webapp/uploads/licitacoes/".$result_d['documento']."' target='_blank'><i class='fas fa-cloud-download-alt'></i> ".$result_d['documento']."</li></a>";
												}else {
													echo "<li> <a href='webapp/uploads/licitacoes/".$result_d['documento']."' target='_blank'><i class='fas fa-cloud-download-alt'></i> ".$result_d['le_titulo']."</li></a>";
												}


											}
											echo "</ul>"; 

										}
								echo "</div>

									<div class='bloco'>
										<h3> Veja Mais </h3>
										<ul>";
											$sql= "SELECT *	FROM aux_menu
											LEFT JOIN aux_submenu On aux_submenu.sm_menu = aux_menu.men_id
											WHERE men_id = :men_id
											ORDER BY men_posicao, sm_posicao DESC";
											$stmt = $PDO->prepare($sql);
											$stmt->bindValue(':men_id', 20);
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
										echo"</ul>
									</div>

									<div class='bloco'>
									<h3 class='vermelho'> Cotações Disponíveis </h3>
									<ul>";
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
									echo"</ul>
									<center><a href='cotacoes/'>Veja Mais </a></center>

								</div>

								</div>
							</div>
						</section>

					";
				}
				else{
					header('Location: /daev/404-pagina-nao-encontrada/');
				} 
			}	
			else {
				$sql= "SELECT *	FROM licitacao_pregao 
				ORDER BY CASE WHEN ordem IS NULL THEN 1 ELSE 0 END, lic_id DESC";
				$stmt = $PDO->prepare($sql);
				$stmt->execute();
				$rows = $stmt->rowCount(); 
				if($rows> 0)
				{						
					echo"
						<section>
							<div class='banner-top'>
								<h1 class='titulo'>Licitações</h1>
							</div>
						</section>	

						<section>
							<div class='wrapper' id='licitacao'>
								<div class='detalhes'>
									<div class='ano'>
										<h1 class='titulo1'> Selecione um Ano </h1>";
										while($result = $stmt->fetch()){
											$date = date_create($result['data_abertura']);
											$a = date_format($date , 'Y');
											$ano[]= $a;  
										}
										$anos = array_values(array_unique($ano));
										for($i=0;  $i < count($anos); $i++ ){
											echo "
												<div class='bl_ano' onclick='recupera_ano($anos[$i])'>
													$anos[$i] 
												</div>
											";
										}	
								echo "</div>
								
									<div class='licitacoes'></div>
								</div>

								<div class='veja_mais'>
									<div class='bloco'>
										<h3> Veja Mais </h3>
										<ul>";
											$sql= "SELECT *	FROM aux_menu
											LEFT JOIN aux_submenu On aux_submenu.sm_menu = aux_menu.men_id
											WHERE men_id = :men_id
											ORDER BY men_posicao, sm_posicao DESC";
											$stmt = $PDO->prepare($sql);
											$stmt->bindValue(':men_id', 20);
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
										echo"</ul>
									</div>

									<div class='bloco' id='bl_ano' style='display:none'>
										<h3> Selecione um Ano</h3>
										<ul>";
										for($i=0;  $i < count($anos); $i++ ){
											echo "
												<li onclick='recupera_ano($anos[$i])'><i class='far fa-arrow-alt-circle-right'></i> $anos[$i] </li>
											";
										}	
										echo"</ul>
									</div>

									<div class='bloco'>
										<h3 class='vermelho'> Cotações Disponíveis </h3>
										<ul>";
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
										echo"</ul>
										<center><a href='cotacoes/'>Veja Mais </a></center>
									</div>
								</div>
							</div>
						</section>

					";
				}
				else{
					header('Location: /daev/404-pagina-nao-encontrada/');
				} 
			}		
		?>
		<?php
		include('core/mod_rodape/rodape.php');
		?>
	</main>
</body>

</html>

<script>
	function recupera_ano(ano){
		$.post("carrega_conteudo.php",{pagina: 'licitacoes', lic_ano:ano},
			function(valor){
				$(".ano").css('display','none');
				$("#bl_ano").css('display','table');
				$(".licitacoes").html(valor);
			}
		)
	}

	function voltarAno(ano){
		localStorage.setItem('ano',ano); 
		window.location.href='licitacoes/'; 
	}



</script>

