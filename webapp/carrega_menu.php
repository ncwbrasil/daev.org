<?php

	require_once("../core/mod_includes/php/ctracker.php");

	include_once("../core/mod_includes/php/connect.php");

	include_once("../core/mod_includes/php/funcoes.php");

	

	$action = $_POST['action'];

	$pg_menu = $_POST['pg_menu'];





	if($action == 'menu'){

		$sql = " SELECT * FROM aux_menu ORDER BY men_titulo";

		$stmt = $PDO->prepare($sql);

		$stmt->execute();

		while($result = $stmt->fetch())

		{

			echo "<option value='".$result['men_id']."'>".$result['men_titulo']."</option>";

		}
		echo "<option value=''>Carregar Todos os Arquivos</option>";	

	}



	if($action == 'submenu'){

		$sql = " SELECT * FROM aux_submenu WHERE sm_menu = :pg_menu ORDER BY sm_titulo";

		$stmt = $PDO->prepare($sql);

		$stmt->bindParam(':pg_menu', $pg_menu);

		$stmt->execute();

		

		while($result = $stmt->fetch())

		{

			echo "<option value='".$result['sm_id']."'>".$result['sm_titulo']."</option>";

		}

	}



	if($action == 'categorias'){



		$sql = " SELECT * FROM licitacao_categorias 

		ORDER BY lc_titulo";

		$stmt = $PDO->prepare($sql);

		$stmt->execute();

		echo"<option value=''>Selecione </option>";

		while($result = $stmt->fetch())

		{

			echo "<option value='".$result['lc_id']."'>".$result['lc_titulo']."</option>";

		}

	}



	if($action == 'categoria_empresa'){



		$sql = " SELECT * FROM fornecedores_ramo_atuacao 

		ORDER BY fra_descricao";

		$stmt = $PDO->prepare($sql);

		$stmt->execute();

		echo"<option value=''>Selecione </option>";

		while($result = $stmt->fetch())

		{

			echo "<option value='".$result['fra_id']."'>".$result['fra_descricao']."</option>";

		}

	}



	

?>