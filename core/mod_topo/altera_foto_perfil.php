<?php
if($pagina == "altera_foto_perfil")
{
	$erro=0;
	$usu_id = $_POST['usu_id'];
	function uploadImageFilePerfil() { // Note: GD library is required for this function
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$iWidth = 250; $iHeight = 250; // desired image result dimensions
			$iJpgQuality = 100;
	
			if ($_FILES) {
	
				// if no errors and size less than 250kb
				if (! $_FILES['usu_foto']['error'] && $_FILES['usu_foto']['size'] < 1024 * 1024) {
					if (is_uploaded_file($_FILES['usu_foto']['tmp_name'])) {
						$usu_id = $_POST['usu_id'];
		
						$caminho = "../admin/perfil/";
						if(!empty($_FILES['usu_foto']['name']))
						{
							if(!file_exists($caminho))
							{
								 mkdir($caminho, 0755, true); 
							}
							
						}
						// new unique filename
						$sTempFileName = $caminho . md5(time().rand());
	
						// move uploaded file into cache folder
						move_uploaded_file($_FILES['usu_foto']['tmp_name'], $sTempFileName);
	
						// change file permission to 644
						@chmod($sTempFileName, 0644);
	
						if (file_exists($sTempFileName) && filesize($sTempFileName) > 0) {
							$aSize = getimagesize($sTempFileName); // try to obtain image info
							if (!$aSize) {
								@unlink($sTempFileName);
								return;
							}
	
							// check for image type
							switch($aSize[2]) {
								case IMAGETYPE_JPEG:
									$sExt = '.jpg';
	
									// create a new image from file 
									$vImg = @imagecreatefromjpeg($sTempFileName);
									break;
								/*case IMAGETYPE_GIF:
									$sExt = '.gif';
	
									// create a new image from file 
									$vImg = @imagecreatefromgif($sTempFileName);
									break;*/
								case IMAGETYPE_PNG:
									$sExt = '.png';
	
									// create a new image from file 
									$vImg = @imagecreatefrompng($sTempFileName);
									imagealphablending($vImg, true);
									break;
								default:
									@unlink($sTempFileName);
									return;
							}
	
							// create a new true color image
							$vDstImg = @imagecreatetruecolor( $iWidth, $iHeight );
							imagesavealpha($vDstImg, true);
							imagealphablending($vDstImg, false);
							$transparent = imagecolorallocatealpha($vDstImg, 0, 0, 0, 127);
							imagefill($vDstImg, 0, 0, $transparent);
	
							// copy and resize part of an image with resampling
							imagecopyresampled($vDstImg, $vImg, 0, 0, (int)$_POST['x1_1'], (int)$_POST['y1_1'], $iWidth, $iHeight, (int)$_POST['w_1'], (int)$_POST['h_1']);
	
							// define a result image filename
							$sResultFileName = $sTempFileName . $sExt;
	
							// output image to file
							switch($aSize[2])
							{
								case IMAGETYPE_JPEG:
									imagejpeg($vDstImg, $sResultFileName, $iJpgQuality);
								case IMAGETYPE_PNG:
									imagepng($vDstImg, $sResultFileName, 2);
							}
							@unlink($sTempFileName);
	
							return $sResultFileName;
						}
					}
				}
			}
		}
	}
	$sql = "SELECT * FROM admin_usuarios WHERE usu_id = :id ";
	$stmt = $PDO->prepare($sql);
	$stmt->bindParam(':id', $usu_id);
	$stmt->execute();
	$rows = $stmt->rowCount();
	if($rows > 0)
	{			
		$usu_foto_antiga = $stmt->fetch(PDO::FETCH_OBJ)->usu_foto;
	}
	$arquivoPerfil = uploadImageFilePerfil();
	
	if($usu_id != '')
	{
		$sql = "UPDATE admin_usuarios SET 
			    usu_foto = :foto
			    WHERE usu_id = :id ";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':foto', $arquivoPerfil);
		$stmt->bindParam(':id', $usu_id);
		if($stmt->execute())
		{
		}
		else
		{
			$erro++;
		}
	}
	else
	{
		$sql = "INSERT INTO admin_usuarios
			   (usu_foto, usu_status) VALUES  (:foto, :status)
			   ";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':foto', $arquivoPerfil);
		$stmt->bindValue(':status', 1);
		if($stmt->execute())
		{
			$usu_id = $PDO->lastInsertId();
		}
		else
		{
			$erro++;
		}
	}
	
	if($erro == 0)
	{
		unlink($usu_foto_antiga);
		if(end(explode("/", $_SERVER['PHP_SELF'])) == 'meu_perfil.php')
		{
			echo "
			<SCRIPT language='JavaScript'>
				abreMask(
				'<img src=../imagens/ok.png> Foto inserida com sucesso.<br><br>'+
				'<input value=\' Ok \' type=\'button\' onclick=javascript:window.location.href=\'meu_perfil.php?pagina=meu_perfil&usu_id=$usu_id".$autenticacao."\';>' );
			</SCRIPT>
				";
		}
		elseif(end(explode("/", $_SERVER['PHP_SELF'])) == 'admin_usuarios.php')
		{
			echo "
			<SCRIPT language='JavaScript'>
				abreMask(
				'<img src=../imagens/ok.png> Foto inserida com sucesso.<br><br>'+
				'<input value=\' Ok \' type=\'button\' onclick=javascript:window.location.href=\'admin_usuarios.php?pagina=admin_usuarios_editar&usu_id=$usu_id".$autenticacao."\';>' );
			</SCRIPT>
				";
		}
	}
	else
	{
		echo "
		<SCRIPT language='JavaScript'>
			abreMask(
			'<img src=../imagens/x.gif> Erro ao salvar foto.<br><br>'+
			'<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back();>');
		</SCRIPT>
		";
	}
}
?>