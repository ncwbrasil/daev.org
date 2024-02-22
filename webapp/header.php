<?php
	include('url.php'); 
?>
<!-- META TAGS -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!-- ESTILO E JQUERY -->
<link rel="shortcut icon" href="../core/imagens/favicon.png">
<link href="../core/mod_menu/css/reset.css" rel="stylesheet"> <!-- CSS reset -->
<link href="../core/css/style.css" rel="stylesheet" type="text/css" />
<script src="../core/mod_includes/js/jquery-2.1.4.js" type="text/javascript"></script>
<script src="../core/mod_includes/js/funcoes.js" type="text/javascript"></script>

<!-- TOOLBAR -->
<link href="../core/mod_includes/js/toolbar/jquery.toolbars.css" rel="stylesheet" />
<link href="../core/mod_includes/js/toolbar/bootstrap.icons.css" rel="stylesheet">
<script src="../core/mod_includes/js/toolbar/jquery.toolbar.js"></script>
<link href="../core/mod_includes/js/janela/jquery-ui.css" rel="stylesheet">
<script src="../core/mod_includes/js/janela/jquery-ui.js"></script>

<!-- ABAS -->
<link href="../core/mod_includes/js/abas/bootstrap.css" rel="stylesheet">
<script src="../core/mod_includes/js/abas/bootstrap.js"></script>


<?php
	require_once('../core/mod_includes/php/funcoes-jquery.php');
	require_once('../core/mod_includes/php/verificalogin.php');
	require_once('../core/mod_includes/php/verificapermissao.php');	
	include("../core/mod_menu/barra.php");
?>

<title>DAEV - Departamento de Águas e Esgoto de Valinhos | Painel de Controle</title>
    <!-- TINY -->
    <script src="../core/mod_includes/js/tinymce/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector: 'textarea',
            plugins: "image code jbimages imagetools advlist link table textcolor media",
            toolbar: "undo redo format bold italic forecolor backcolor alignleft aligncenter alignright alignjustify bullist numlist outdent indent table link media image jbimages",
            imagetools_toolbar: "rotateleft rotateright | flipv fliph | editimage imageoptions",
            paste_data_images: true,
            media_live_embeds: true,
            relative_urls: false,
        });
    </script>
    <!-- TINY -->
