<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
<meta http-equiv="Content-Language" content="pt-br">

<head>
	<?php
		include('header.php');
		$pg = $_GET['p1']; 	
	?>
	<title><?php echo $ttl; ?></title>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>

</head>

<body>
<div id='janela' class='janela' style='display:none;'> </div>

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
				<p class="titulo">Orçamento </p>
			</div>
		</section>
		<section>
			<div class="wrapper orcamento" id='pagina'>
				<div class="conteudo">
					<h1 class="azul" id='revise'> Revise seus Dados </h1>

					<form name='form_contato' id='form' enctype='multipart/form-data' method='post' action='envia_orcamento/'>
						<div class='bl_contato' id='dados_contato'>
							<h3 class="azul"> Dados para Contato </h3>
							
							
								<div class='bloco_l'>
									<label>Nome ou Empresa</label>
									<input type='text' id='nome' name='nome' class="obg">
								</div>
								<div class='bloco_r'>
									<label>Nome para contato ou apelido</label>
									<input type='text' id='apelido' name='apelido' class="obg">
								</div>

								<label>E-mail</label>
								<input type='text' id='email' name='email' class="obg">

								<div class='bloco_l'>
									<label>Celular</label>
									<input type='text' id='celular' name='celular' class="obg">
								</div>

								<div class="bloco_r">
									<label>Telefone</label>
									<input type='text' id='telefone' name='telefone' class="obg">
								</div>
								<div class='botao'>
									<buttom class='bt_proximo' onclick="proximo('dados_endereco')"> Próximo </buttom>
								</div>
						</div>

						<div class='bl_contato' id='dados_endereco'>
							<h3 class='azul'> Endereço </h3>
							<div class="bloco_lm">
								<label>CEP</label>
								<input type='text' id='cep' name='cep' class="obg">
							</div>

							<div class='comp_end'>
								<div class='bloco_rg'>
									<label>Endereço</label>
									<input type='text'  name='address' id='address' class="obg">
								</div>

								<div class='bloco_l'>
									<label>Número</label>
									<input type='text' name='number' id='number'  class="obg">
								</div>

								<div class="bloco_r">
									<label>Bairro</label>
									<input type='text' name='bairro' id='bairro'  class="obg">
								</div>

								<div class="bloco_lm">
									<label>Estado</label>
										<select name='state_id' id='state_id' >
											<option value=''> Selecione </option>
											<?php
											$sql_uf = " SELECT * FROM end_uf";
											$stmt_uf = $PDO->prepare($sql_uf);
											$stmt_uf->execute();
											while($result_uf = $stmt_uf->fetch())
											{
												echo "<option value='".$result_uf['uf_nome']."'> ".$result_uf['uf_nome']." </option>";
											}
											?>
									</select>
								</div>

								<div class='bloco_rg'>
									<label>Cidade</label>
									<input type='text' name='city' id='city'  class="obg">
								</div>
							</div>

							<div class='botao'>
								<buttom class='bt_proximo' onclick="proximo('dados_descricao')"> Próximo </buttom>
								<buttom class='bt_proximo' onclick="proximo('dados_contato')"> Voltar </buttom>
							</div>
						</div>

						<div class='bl_contato' id='dados_descricao'>
							<h3 class="azul">Mensagem </h3>
							<label>Do que você precisa?</label>
							<input type='text' id='assunto' name='assunto' class="obg">
						
							<label>Descrição</label>
							<textarea id='mensagem' name='mensagem' class="obg"> </textarea>
							<input type="submit" id='bt_enviar' value='Enviar'>
							<buttom class='bt_proximo' onclick="proximo('finalizar')"> Revisar </buttom>
							
						</div>
					</form>
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
			</div>
		</section>
		<?php
		include('core/mod_rodape/rodape.php');
		?>
	</main>
</body>

</html>


<script>

function proximo(pagina){
	if(pagina == 'finalizar'){
		$('.bt_proximo').css('display','none');
		$('.bl_contato').css('display','table');
		$('#revise').css('display','table');
		$('#bt_enviar').css('display','table');
	}
	else {
		$('.bl_contato').css('display','none');
		$('#'+pagina).css('display','table');
	}
}

$("#celular").mask("(00) 00000-0000");
$("#telefone").mask("(00) 0000-0000");
$("#cep").mask("00000-000");


$( "#cep" ).change(function() {
	var valor = $(this).val();
	$.post("carrega_conteudo.php",{pagina: 'carrega_endereco', cep:valor},
		function(dados){
			$('.comp_end').html(dados); 
		}
	)
});


</script>

