<?php
ob_start();
include_once("header.php");
include_once("core/mod_includes/php/connect.php");
include_once("core/mod_includes/php/funcoes.php");
sec_session_start();
include_once('core/mod_includes/php/funcoes-jquery.php');
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <title><?php echo $ttl?> </title>
  <meta name="viewport" content="width=device-width, initial-scale=1 user-scalable=no">
  <meta name="author" content="MogiComp">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="shortcut icon" href="core/imagens/favicon.png">
  <link href="core/css/style.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="core/mod_includes/js/jquery-2.1.4.js"></script>
</head>

<body class='login'>
  <div id='login'>
    <div class="logo">
      <img src='core/imagens/logo.png' alt="Logo" />
    </div>
    <div class='box'>
      <form name='form_login' id='form_login' enctype='multipart/form-data' method='post' autocomplete='off' action='envialogin/'>
        <p class="titulo">ACESSO DO FORNECEDOR</p>
        <input name='email' class='login' placeholder='Email'>
        <p>
          <input type='password' name='senha' class='login' placeholder='Senha'>
          <p>
            <input type='submit' value=' Entrar no Sistema ' class='login'> </br></br>

            <a href='cadastre-se/' target="_blank"> Ou Cadastre-se </a>           
      </form>
    </div>
    <p>
      © <?php echo date('Y'); ?> Copyright
      <br />
      Versão <span class='versao'>1.0</span>
  </div>
</body>

</html>