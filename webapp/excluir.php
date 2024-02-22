<?php
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");

$action = $_POST['action']; 

if($action == 'excluir_documento'){
    $le_id = $_POST['le_id']; 
    $sql = "DELETE FROM licitacao_edital WHERE le_id =:le_id ";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':le_id', $le_id);
    if($stmt->execute())
    {
    }
}

if($action == 'excluir_download'){
    $doc_id = $_POST['doc_id']; 
    $sql = "DELETE FROM cadastro_downloads_documentos WHERE doc_id =:doc_id ";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':doc_id', $doc_id);
    if($stmt->execute())
    {
    }
}

if($action == 'excluir_descricao_sala'){
    $csd_id = $_POST['csd_id']; 
    $sql = "DELETE FROM cadastro_sala_descricao WHERE csd_id =:csd_id ";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':csd_id', $csd_id);
    if($stmt->execute())
    {
    }
}

if($action == 'excluir_descricao_licitante'){
    $csd_id = $_POST['csd_id']; 
    $sql = "DELETE FROM cadastro_sala_descricao WHERE csd_id =:csd_id ";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':csd_id', $csd_id);
    if($stmt->execute())
    {
    }
}

if($action == 'excluir_documento_licitante'){
    $ldc_id = $_POST['id_documento']; 

    $sql = "DELETE FROM licitacao_documentacao WHERE ldc_id = :ldc_id ";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':ldc_id', $ldc_id);
    if($stmt->execute())
    {
    }

}




?>