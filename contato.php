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
		<div class="banner-top">
			<p class="titulo">Contato </p>
		</div>

		<div class="wrapper" id='contato'>

			<p class='subtitulo'> Entre em contato conosco através dos canais de atendimento: </p>

			<p>Rua: Alda Lourenço Francisco, 160 - Remanso Campineiro - Hortolândia / São Paulo</p>

			<p>Fone/Fax: (19) 3897-3739 <br>
			E-mail: superintendencia@daev.hortolandia.sp.gov.br</p>

			<div class='bloco_l'>
				<form name='form_contato' id='form' enctype='multipart/form-data' method='post' action='fale_conosco/'>
					<label>Nome</label>
					<input type='text' id='nome' name='nome'class="obg">
					<label>Email</label>
					<input type='text' id='email' name='email' class="obg">
					<label>Assunto</label>
					<input type='text' id='assunto' name='assunto' class="obg">
			</div>

			<div class='bloco_r'>
					<label>Mensagem</label>
					<textarea id='mensagem' name='mensagem' class="obg"> </textarea>
					<input type="submit" id='bt_enviar' value='Enviar'> 
				</form>
			</div>
			
		</div>

		<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d1838.1355191654786!2d-47.214938!3d-22.866444000000005!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94c8bbffc2eb27e7%3A0x6d4d6671ee8de904!2sR.%20Alda%20Louren%C3%A7o%20Francisco%2C%20160%20-%20Lot.%20Remanso%20Campineiro%2C%20Hortol%C3%A2ndia%20-%20SP%2C%2013184-310!5e0!3m2!1spt-BR!2sbr!4v1591040355883!5m2!1spt-BR!2sbr" width="100%" height="450" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>

		<?php
		include('core/mod_rodape/rodape.php');
		?>
	</main>
</body>

</html>