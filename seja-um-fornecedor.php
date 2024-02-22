<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
<meta http-equiv="Content-Language" content="pt-br">

<head>
	<?php
		include('header.php');
		$pagina = 'mapa_do_site';
	?>
	<title><?php echo $ttl; ?></title>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>

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
				<h1 class="titulo">Seja um de nossos fornecedores </h1>
			</div>
		</section>
		<section>
			<div class="wrapper fornecedor" id='pagina'>
				<div class="conteudo">
					<div class="cadastro_fornecedor">
						<h2 class="azul">Cadastre-se e se torne um de nossos fornecedores</h2>

						<h3 class="azul"> Dados para Contato </h3>
						<form name='form_contato' id='form' enctype='multipart/form-data' method='post' action='cadastro_fornecedor/'>
							<div class='bloco_l'>
								<label>Nome Fantasia</label>
								<input type='text' id='nome_fantasia' name='nome_fantasia' class="obg">
							</div>
							<div class='bloco_r'>
								<label>Razão Social</label>
								<input type='text' id='razao_social' name='razao_social' class="obg">
							</div>

							<div class="bloco_lm">
								<label>CNPJ</label>
								<input type='text' id='cnpj' name='cnpj' class="obg">
							</div>
						
							<div class='bloco_rg'>
								<label>E-mail</label>
								<input type='text' id='email' name='email' class="obg">
							</div>

							<div class='bloco_l'>
								<label>Celular</label>
								<input type='text' id='celular' name='celular' class="obg">
							</div>

							<div class="bloco_r">
								<label>Telefone</label>
								<input type='text' id='telefone' name='telefone' class="obg">
							</div>

							<h3 class='azul'> Endereço </h3>

							<div class="bloco_lm">
								<label>CEP</label>
								<input type='text' id='cep' name='cep' class="obg">
							</div>
						
							<div class='comp_end'>
								<div class='bloco_rg'>
									<label>Endereço</label>
									<input type='text' id='endereco' name='endereco' class="obg">
								</div>

								<div class='bloco_l'>
									<label>Número</label>
									<input type='text' id='numero' name='numero' class="obg">
								</div>

								<div class="bloco_r">
									<label>Bairro</label>
									<input type='text' id='bairro' name='bairro' class="obg">
								</div>

								<div class="bloco_lm">
									<label>Estado</label>
									<select id='estado' name='estado'>
										<option value=''> Selecione </option>
										<?php
										$sql_uf = " SELECT * FROM end_uf";
										$stmt_uf = $PDO->prepare($sql_uf);
										$stmt_uf->execute();
										while ($result_uf = $stmt_uf->fetch()) {
											echo "<option value='" . $result_uf['uf_nome'] . "'> " . $result_uf['uf_nome'] . " </option>";
										}
										?>
									</select>
								</div>

								<div class='bloco_rg'>
									<label>Cidade</label>
									<input type='text' id='cidade' name='cidade' class="obg">
								</div>
							</div>

							<input type="submit" id='bt_enviar' value='Enviar'>

						</form>
					</div>

					<h2 class="azul"> Tipos de Serviços </h2>
					<div class='tipos'>
						<?php
						$sql = "SELECT * FROM fornecedores_tipo
							ORDER BY ft_descricao DESC";
						$stmt = $PDO->prepare($sql);
						$stmt->execute();
						$rows = $stmt->rowCount();
						if ($rows > 0) {
							while ($result = $stmt->fetch()) {
								echo "<div class='bloco'>
										<h3 class='azul2'>" . $result['ft_titulo'] . "</h3>
										<p>" . $result['ft_descricao'] . "</p>
									</div>";
							}
						}
						?>
					</div>
				</div>

				<div class='veja_mais'>
					<div class='bloco'>
						<h3> Veja Mais </h3>
						<ul>
							<?php
							$sql = "SELECT *	FROM aux_menu
								LEFT JOIN aux_submenu On aux_submenu.sm_menu = aux_menu.men_id
								WHERE men_id = :men_id
								ORDER BY men_posicao, sm_posicao DESC";
							$stmt = $PDO->prepare($sql);
							$stmt->bindValue(':men_id', 20);
							$stmt->execute();
							$rows = $stmt->rowCount();
							if ($rows > 0) {
								while ($result = $stmt->fetch()) {
									$link = substr($result['sm_link'], 0, 4);
									if ($link == "http" || $link == 'HTTP' || $link == 'Http') {
										echo "<li><a href='" . $result['sm_link'] . "' target='_blank'><i class='far fa-arrow-alt-circle-right'></i> " . $result['sm_titulo'] . "</a></li>";
									} else {
										echo "<li><a href='router/" . $result['sm_link'] . "'><i class='far fa-arrow-alt-circle-right'></i> " . $result['sm_titulo'] . "</a></li>";
									}
								}
							}
							?>
						</ul>
					</div>
				</div>

				<div class='veja_mais'>
					<div class='bloco'>
						<h3> Cotações Disponíveis </h3>
						<ul>
							<?php
							$sql = "SELECT * FROM cadastro_cotacoes
								WHERE cot_status = :cot_status";
							$stmt = $PDO->prepare($sql);
							$stmt->bindValue(':cot_status', 1);
							$stmt->execute();
							$rows = $stmt->rowCount();
							if ($rows > 0) {
								while ($result = $stmt->fetch()) {
									echo "<li><a href='webapp/" . $result['cot_documento'] . "'><i class='fas fa-cloud-download-alt'></i> " . $result['cot_titulo'] . "</a></li>";
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
	$("#celular").mask("(00) 00000-0000");
	$("#telefone").mask("(00) 0000-0000");
	$("#cep").mask("00000-000");
	$("#cnpj").mask("00.000.000/0000-00");

	$( "#cep" ).change(function() {
		var valor = $(this).val();
		$.post("carrega_conteudo.php",{pagina: 'carrega_endereco', cep:valor},
			function(dados){
				$('.comp_end').html(dados); 
			}
		)
	});
</script>