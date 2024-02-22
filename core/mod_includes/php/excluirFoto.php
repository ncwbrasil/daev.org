<?php
include('connect.php');
require_once("funcoes.php");
sec_session_start(); 

// WEB ALERTA
$fg_id = $_POST['fg_id'];

$sql = "DELETE FROM cadastro_fotos_galeria                               
        WHERE fg_id = :fg_id ";
$stmt_foto = $PDO->prepare($sql);            
$stmt_foto->bindParam(':fg_id', $fg_id);
if($stmt_foto->execute())
{
    echo "true";
}
else
{
    echo "false";
}

?>