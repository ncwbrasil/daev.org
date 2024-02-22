<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
<meta http-equiv="Content-Language" content="pt-br">

<head>
	<?php
		include('header.php');
		$email = $_GET['p1']; 

		$sql = "SELECT * FROM fornecedores       
		WHERE hash_email = :hash_email";
		$stmt = $PDO->prepare($sql);                
		$stmt->bindParam(':hash_email',     $email);
		if($stmt->execute()){

			$result = $stmt->fetch();
			$usuario = $result['email']; 
		}



	?>
	<title><?php echo $ttl; ?> - Cadastre sua Senha</title>
</head>

<body style='background:#f9f9f9;'>
	<header>
		<?php
		#region MOD INCLUDES
		include('core/mod_includes/php/funcoes-jquery.php');
		#endregion
		?>
	</header>
	<main>
		<section>
			<div class="wrapper" id='cadastro_senha'>
				<center><img src='core/imagens/logo.png' width='150px'></center>
				<h1 class='titulo'> Cadastre sua Senha </h1>
				<form name='form_contato' id='form' enctype='multipart/form-data' method='post' action='envia_senha/'>
					<label>Email Cadastrado</label>
					<input type='text' id='email' name='email' value='<?php echo $usuario?>' readonly>
					<label>Senha</label>
					<input type='password' id='senha' name='senha' class="obg">
					<label>Repita Senha</label>
					<input type='password' id='senha2' name='senha2' class="obg">
					<p class='erro'></p>
					
					<input type="submit" id='bt_enviar' value='Cadastrar' disabled='true'>

				</form>
			</div>
		</section>
	</main>
</body>

</html>

<script>


	$( "#senha2" ).change(function() {
		NovaSenha = document.getElementById('senha').value;
		CNovaSenha = document.getElementById('senha2').value;
		if (NovaSenha != CNovaSenha) {
			$('.erro').html('Senhas Diferentes');
			$('#senha').css('border','1px solid #f00');
			$('#senha2').css('border','1px solid #f00');
		}
		else {
			$('#bt_enviar').attr("disabled", false);
			$('#bt_enviar').css("cursor", 'pointer');

		}
	});

</script>