<?php
include('connect.php');
require_once("funcoes.php");
sec_session_start(); 

// WEB ALERTA
$foto_id = $_POST['foto_id'];

$sql = "DELETE FROM cadastro_fotos                                
        WHERE foto_id = :foto_id ";
$stmt_foto = $PDO->prepare($sql);            
$stmt_foto->bindParam(':foto_id', $foto_id);
if($stmt_foto->execute())
{
    echo "true";
}
else
{
    echo "false";
}

?>