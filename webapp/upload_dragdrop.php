<?php
include_once("../core/mod_includes/php/connect.php");
include_once("../core/mod_includes/php/funcoes.php");
require_once '../core/mod_includes/php/lib/WideImage.php';

$ev_id = $_GET['ev_id'];
$gal_id = $_GET['gal_id'];
$nt_id = $_GET['nt_id'];

//$ds          = DIRECTORY_SEPARATOR;  //1


if (!empty($ev_id)){
    $storeFolder = "uploads/eventos/".$ev_id ."";
 
    if (!empty($_FILES)) 
    {
        if(!file_exists($storeFolder)){mkdir($storeFolder, 0755, true);}
    
        $tempFile = $_FILES['file']['tmp_name'];          //3             
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        //$targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;  //4
        $targetPath = $storeFolder."/";  //4
        $targetFile =  $targetPath.date("YmdHis")."_". limpaStringAll(str_replace($ext,"",$_FILES['file']['name'])).'.'.$ext;  //5
     
        move_uploaded_file($tempFile,$targetFile); //6
    
        $imnfo = getimagesize($targetFile);
        $img_w = $imnfo[0];	  // largura
        $img_h = $imnfo[1];	  // altura
        if($img_w > 600 || $img_h > 600)
        {
            $image = WideImage::load($targetFile);
            $image = $image->resize(600, 600);
            $image->saveToFile($targetFile);
        }
        $sql = "INSERT INTO cadastro_fotos SET 
                foto_evento 	 = :foto_evento,
                foto_imagem 	 = :foto_imagem";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':foto_evento',$ev_id);
        $stmt->bindParam(':foto_imagem',$targetFile);
        if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
    }
}

if (!empty($gal_id)){
    $storeFolder = "uploads/galeria/".$gal_id ."";
 
    if (!empty($_FILES)) 
    {
        if(!file_exists($storeFolder)){mkdir($storeFolder, 0755, true);}
    
        $tempFile = $_FILES['file']['tmp_name'];          //3             
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        //$targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;  //4
        $targetPath = $storeFolder."/";  //4
        $targetFile =  $targetPath.date("YmdHis")."_". limpaStringAll(str_replace($ext,"",$_FILES['file']['name'])).'.'.$ext;  //5
     
        move_uploaded_file($tempFile,$targetFile); //6
    
        $imnfo = getimagesize($targetFile);
        $img_w = $imnfo[0];	  // largura
        $img_h = $imnfo[1];	  // altura
        if($img_w > 600 || $img_h > 600)
        {
            $image = WideImage::load($targetFile);
            $image = $image->resize(600, 600);
            $image->saveToFile($targetFile);
        }
        $sql = "INSERT INTO cadastro_fotos_galeria SET 
                fg_galeria 	 = :fg_galeria,
                fg_imagem  = :fg_imagem";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':fg_galeria',$gal_id);
        $stmt->bindParam(':fg_imagem',$targetFile);
        if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
    }
}

if (!empty($nt_id)){
    $storeFolder = "uploads/noticia/".$nt_id."";
 
    if (!empty($_FILES)) 
    {
        if(!file_exists($storeFolder)){mkdir($storeFolder, 0755, true);}
    
        $tempFile = $_FILES['file']['tmp_name'];          //3             
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        //$targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;  //4
        $targetPath = $storeFolder."/";  //4
        $targetFile =  $targetPath.date("YmdHis")."_". limpaStringAll(str_replace($ext,"",$_FILES['file']['name'])).'.'.$ext;  //5
     
        move_uploaded_file($tempFile,$targetFile); //6
    
        $imnfo = getimagesize($targetFile);
        $img_w = $imnfo[0];	  // largura
        $img_h = $imnfo[1];	  // altura
        if($img_w > 600 || $img_h > 600)
        {
            $image = WideImage::load($targetFile);
            $image = $image->resize(600, 600);
            $image->saveToFile($targetFile);
        }
        $sql = "INSERT INTO cadastro_fotos_noticia SET 
                fn_noticia 	 = :fn_noticia,
                fn_imagem  = :fn_imagem";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':fn_noticia',$nt_id);
        $stmt->bindParam(':fn_imagem',$targetFile);
        if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
    }
}



?>     