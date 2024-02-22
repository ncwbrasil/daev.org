<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<?php 
		include('header.php'); 
		include ('core/mod_includes/php/funcoes-jquery.php'); 
		unset($_SESSION['fn_id']); 
		$_SESSION['fn_id'] = 0; 
	?>
	<title><?php echo $ttl ?> - Sair </title>	
</head>

<body>
<div id='janela' class='janela' style='display:none;'> </div>

<SCRIPT language='JavaScript'>
    abreMask(
    ' Você saiu do sistema.<br><br>'+
    '<center><input value=\' Ok \' type=\'button\' class=\'but_mask\' onclick=javascript:window.location.href=\'index\';></center>' );
</SCRIPT>
