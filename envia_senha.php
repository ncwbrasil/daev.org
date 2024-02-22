<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<?php 
		include('header.php'); 
		include ('core/mod_includes/php/funcoes-jquery.php'); 
	?>
	<title><?php echo $ttl ?> - Cadastro de Senha </title>	
</head>

<body>
	<div id='janela' class='janela' style='display:none;'> </div>
	<?php
		$email = $_POST['email'];
		$senha = md5($_POST['senha']);

		$sql = "UPDATE fornecedores SET password = :password 
		WHERE email = :email";
		$stmt = $PDO->prepare($sql);
		$stmt->bindValue('password', $senha);
		$stmt->bindValue('email', $email);
		if ($stmt->execute()) {
			echo "
				<SCRIPT language='JavaScript'>
					abreMask(
					'Sua senha foi cadastrada com sucesso!<br><br>'+
					'<center><input value=\' Ok \' type=\'button\' class=\'but_mask\' onclick=javascript:window.location.href=\'index\';></center>' );
				</SCRIPT>
			";

		}
		else {
			echo "
				<SCRIPT language='JavaScript'>
					abreMask(
					'Erro ao cadastrar senha, contate o Administrador.<br>'+
					'<center><input value=\' Ok \' type=\'button\' class=\'but_mask\' onclick=window.history.back();></center>' );
				</SCRIPT>
			";
		}

	?>
</body>

</html>