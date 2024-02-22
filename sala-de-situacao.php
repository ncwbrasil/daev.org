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
				<h1 class="titulo">Sala de Situação</h1>
			</div>
		</section>
		<section>
			<div class="wrapper sala servicos" id='pagina'>
				<div class='conteudo'>
					<?php 
						$sql= "SELECT * FROM cadastro_sala
						WHERE cs_status = :cs_status 
						ORDER BY cs_bandeira DESC";
						$stmt = $PDO->prepare($sql);
						$stmt->bindValue(':cs_status', 1);
						$stmt->execute();
						$rows = $stmt->rowCount();
						if($rows> 0)
						{		
							while($result = $stmt->fetch()){
								echo "
									<div class='bloco'>
										<div id='sala'>
											<p>".$result['cs_icone']."</br>
											<span class='destaque azul'> ".$result['cs_titulo']." </span> 
										</div>
										<div class='descricao'>";
											$sql_desc = "SELECT * FROM cadastro_sala_descricao 
											WHERE csd_sala = :csd_sala 
											GROUP BY csd_data
											ORDER BY csd_data DESC"; 
											$stmt_desc = $PDO->prepare($sql_desc);
											$stmt_desc->bindValue(':csd_sala', $result['cs_id']);
											$stmt_desc->execute();
											$rows_desc = $stmt_desc->rowCount();
											if ($rows_desc > 0) {
												echo "<table>
														<thead>
															<tr>
																<th> Descrição </th>
																<th> Data </th>
															</tr>
														</thead>
													";

												while($result_desc = $stmt_desc->fetch()){
													echo "
														<tr>
															<td>".$result_desc['csd_descricao']."</td>
															<td>" . date( "d/m/Y", strtotime($result_desc['csd_data'])). "</td>
														</tr>
													"; 
												}
												echo "</table>";
											}
										echo"</div>
									</div>
								";
							}
							
						}
						else{
							echo"<p> Não há nenhum item registrado no momento.";
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

