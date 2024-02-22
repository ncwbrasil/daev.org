<?php require_once("ctracker.php");

require_once("parametros.php");

error_reporting(1);

date_default_timezone_set('America/Sao_Paulo');



//FUNÇÃO JSON PARA O APP

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, OPTIONS, POST');

header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');

class Json
{

	public function encode($vetor)
	{

		echo json_encode($vetor, 128);
	}
}



$JSON = new JSON;



define('MYSQL_HOST', 'localhost');

define('MYSQL_PORT', '3306');

define('MYSQL_USER', 'daev_site');

define('MYSQL_PASSWORD', 'da@ev#2021');

define('MYSQL_DB_NAME', 'daev_site');



// define( 'MYSQL_HOST', 'localhost' );

// define( 'MYSQL_PORT', '3030' );

// define( 'MYSQL_USER', 'root' );

// define( 'MYSQL_PASSWORD', '' );

// define( 'MYSQL_DB_NAME', 'daev' );



try {

	$PDO 				= new PDO('mysql:host=' . MYSQL_HOST . ';port=' . MYSQL_PORT . ';dbname=' . MYSQL_DB_NAME, MYSQL_USER, MYSQL_PASSWORD);
} catch (PDOException $e) {

	echo 'Erro ao conectar com o MySQL: ' . $e->getMessage();
}

$PDO->exec("SET CHARACTER SET utf8");

$PDO->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
