<?php

	include_once("../core/mod_includes/php/connect.php");

	include_once("../core/mod_includes/php/funcoes.php");

	sec_session_start();

	$page = "<a href='dashboard'>Dashboard</a>";



?>	

<!DOCTYPE html>

<html lang="en" class="no-js">

	<head>

		<title>DAEV - Departamento de Águas e Esgoto de Valinhos | Painel de Controle</title>

		<meta name="viewport" content="width=device-width, initial-scale=1">

		<meta name="author" content="MogiComp">

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<link rel="shortcut icon" href="../core/imagens/favicon.png">

		<link href="../core/css/style.css" rel="stylesheet" type="text/css" />

		<script src="../core/mod_includes/js/jquery-2.1.4.js" type="text/javascript"></script>

		<script src="../core/mod_includes/js/funcoes.js" type="text/javascript"></script>
		<script src="https://kit.fontawesome.com/650f618ca2.js" crossorigin="anonymous"></script>



	</head>

	<body>

		<?php

			require_once('../core/mod_includes/php/funcoes-jquery.php');

			require_once('../core/mod_includes/php/verificalogin.php');

		?>



		<div class="container">

			<?php include('../core/mod_menu/menu/menu.php')?>

			<div class="wrapper">

				<?php

				

				

				

				

				

				?>



			</div>

		</div><!-- /container -->

	</body>

</html>