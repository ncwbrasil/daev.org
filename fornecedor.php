
<?php
include('header.php');
include_once("core/mod_includes/php/funcoes.php");
require_once('core/mod_includes/php/funcoes-jquery.php');



	// $sql = "SELECT * FROM cadastro_noticias";
	// $stmt = $PDO->prepare($sql);
	// $stmt->execute();
	// $rows = $stmt->rowCount();
	// if ($rows > 0) {

	// 	while($result=$stmt->fetch()){

	// 		$data = date('Y-m-d H:i:s', strtotime($result['nt_hora_cadastro'].'+ 3 hours')); 

	// 		$s = "UPDATE cadastro_noticias SET nt_hora_cadastro = :nt_hora_cadastro WHERE nt_id = :nt_id"; 
	// 		$st = $PDO->prepare($s);
	// 		$st->bindParam(':nt_hora_cadastro', $data); 
	// 		$st->bindParam(':nt_id', $result['nt_id']);
	// 		$st->execute();		

	// 	}

	// } 

?>
