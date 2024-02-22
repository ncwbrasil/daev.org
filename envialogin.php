<?php
ob_start();	
include('header.php');
include("core/mod_includes/php/funcoes.php");
include("core/mod_includes/php/class.ipdetails.php");
session_start();
$ip = getIp();
$ipdetails = new ipdetails($ip); 
$ipdetails->scan();
$pais = $ipdetails->get_countrycode();
$regiao = $ipdetails->get_region();
$cidade = $ipdetails->get_city();

?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $ttl?> </title>

</head>
<div id='janela' class='janela' style='display:none;'> </div>

<?php
include("core/mod_includes/php/funcoes-jquery.php");

if(isset($_POST['email']) &&  isset($_POST['senha']))
{
	$email = $_POST['email'];
	$senha = md5($_POST['senha']);
	$result = selectLoginFornecedor($PDO, $email, $senha);
	if (!empty($result))
	{
		$fn_status 		= $result[0]['status'];
		$fn_id 			= $result[0]['id'];

		if ($fn_status == 0)
		{
			echo "
				<SCRIPT language='JavaScript'>
					abreMask(
					'<img src=core/imagens/x.png> Seu usuário está desativado, por favor contate o administrador do sistema.<br><br>'+
					'<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back();>' );
				</SCRIPT>
				";
			exit;
		}
		else
		{
			$ip_address 	= $_SERVER['REMOTE_ADDR']; // Pega o endereço IP do usuário. 
            $user_browser 	= $_SERVER['HTTP_USER_AGENT']; // Pega a string de agente do usuário.

			$_SESSION['site_daev'] 		= md5($senha.$ip_address.$user_browser);
			$_SESSION['fn_id'] 		= $fn_id;

			if ($_SESSION['fn_id'] != ''){
				header("location:../meu-perfil");
			}
			else {

				$_SESSION['site_daev'] = 'N';
				$observacao = "Falha login: $email | $senha";
				echo "
					<SCRIPT language='JavaScript'>
						abreMask(
						'<i class=\"fas fa-exclamation-triangle\"></i> <br><br> Login ou senha incorreta.<br>Por favor tente novamente.<br><br>'+
						'<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back();>' );
					</SCRIPT>
				";
			}
		}	
	}
	else
	{		
		$_SESSION['site_daev'] = 'N';
		$observacao = "Falha login: $email | $senha";
		echo "
			<SCRIPT language='JavaScript'>
				abreMask(
				'<i class=\"fas fa-exclamation-triangle\"></i> <br><br> Login ou senha incorreta.<br>Por favor tente novamente.<br><br>'+
				'<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back();>' );
			</SCRIPT>
		";
	
	}
}
else
{
	echo "
	<SCRIPT language='JavaScript'>
		abreMask(
		'<i class=\"fas fa-exclamation-triangle\"></i> <br><br> Requisição incorreta.<br><br>'+
		'<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back();>' );
	</SCRIPT>
	";
}
?>

