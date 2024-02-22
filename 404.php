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
		<div class="wrapper">
            <p class="titulo azul2">404  </p>
			<center>Página Não Encontrada</center>
		</div>
	</main>
	<?php
		include('core/mod_rodape/rodape.php');
	?>

</body>