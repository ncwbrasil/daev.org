<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<?php
	include('header.php');
	$pagina = $_GET['p'];
	include('core/mod_includes/php/funcoes-jquery.php');
	include_once("core/mod_includes/php/funcoes.php");

	?>
	<title><?php echo $ttl ?> - Contato </title>
</head>

<body>
	<div id='janela' class='janela' style='display:none;'> </div>
	<?php
	$nome = $_POST['nome'];
	$apelido = $_POST['apelido'];
	$email = $_POST['email'];
	$celular = $_POST['celular'];
	$telefone = $_POST['telefone'];
	$cep = $_POST['cep'];
	$endereco = $_POST['address'];
	$numero = $_POST['number'];
	$bairro = $_POST['bairro'];
	$estado = $_POST['state_id'];
	$cidade = $_POST['city'];

	$assunto = $_POST['assunto'];
	$mensagem = nl2br($_POST['mensagem']);

	$dados = array(
		'orc_nome'     		=> $nome,
		'orc_apelido'  	 	=> $apelido,
		'orc_telefone'    	=> $telefone,
		'orc_email'  		=> $email,
		'orc_celular'    	=> $celular,
		'orc_endereco' 		=> $endereco, 
		'orc_numero' 		=> $numero, 
		'orc_bairro' 		=> $bairro,
		'orc_cidade' 		=> $cidade, 
		'orc_cep'    		=> $cep,
		'orc_estado' 		=> $estado, 
		'orc_assunto' 		=> $assunto, 
		'orc_mensagem' 		=> $mensagem, 
	);
	$sql = "INSERT INTO cadastro_orcamento SET " . bindFields($dados); 
	$stmt = $PDO->prepare($sql);	
	$stmt->execute($dados);
	
	$agora = time();
	$data = getdate($agora);
	$dia_semana = $data[wday];
	$dia_mes = $data[mday];
	$mes = $data[mon];
	$ano = $data[year];
	switch ($dia_semana) {
		case 0:
			$dia_semana = "Domingo";
			break;
		case 1:
			$dia_semana = "Segunda-feira";
			break;
		case 2:
			$dia_semana = "Terça-feira";
			break;
		case 3:
			$dia_semana = "Quarta-feira";
			break;
		case 4:
			$dia_semana = "Quinta-feira";
			break;
		case 5:
			$dia_semana = "Sexta-feira";
			break;
		case 6:
			$dia_semana = "Sábado";
			break;
	}
	switch ($mes) {
		case 1:
			$mes = "Janeiro";
			break;
		case 2:
			$mes = "Fevereiro";
			break;
		case 3:
			$mes = "Março";
			break;
		case 4:
			$mes = "Abril";
			break;
		case 5:
			$mes = "Maio";
			break;
		case 6:
			$mes = "Junho";
			break;
		case 7:
			$mes = "Julho";
			break;
		case 8:
			$mes = "Agosto";
			break;
		case 9:
			$mes = "Setembro";
			break;
		case 10:
			$mes = "Outubro";
			break;
		case 11:
			$mes = "Novembro";
			break;
		case 12:
			$mes = "Dezembro";
			break;
	}
	$datap = $dia_semana . ', ' . $dia_mes . ' de ' . $mes . ' de ' . $ano;
	require("core/mod_includes/php/phpmailer/class.phpmailer.php");
	// Inicia a classe PHPMailer
	$mail = new PHPMailer();
	//$mail -> SMTPDebug = 3;
	// Define os dados do servidor e tipo de conexão
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$mail->IsSMTP();
	$mail->Host = "mail.ncwbrasil.com.br"; // Endereço do servidor SMTP (caso queira utilizar a autenticação, utilize o host smtp.seudomínio.com.br)
	$mail->SMTPAuth = true; // Usa autenticação SMTP? (opcional)
	$mail->Username = 'autenticacao@daev.org.br'; // Usuário do servidor SMTP
	$mail->Password = 'daev@2021'; // Senha do servidor SMTP

	// Define o remetente
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$mail->From = "$email"; // Seu e-mail
	$mail->Sender = "autenticacao@daev.org.br"; // Seu e-mail
	$mail->FromName = "$nome"; // Seu nome


	// Define os destinatário(s)
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$mail->AddAddress('compras@daev.org.br');

	// Define os dados técnicos da Mensagem
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$mail->IsHTML(true); // Define que o e-mail será enviado como HTML

	$mail->CharSet = 'utf-8'; // Charset da mensagem (opcional)


	$topo = 'DAEV - Formulário de Orçamento';
	$mail->Subject  = '=?utf-8?B?' . base64_encode($topo) . '?='; // Assunto da mensagem
	$mail->Body = "
			<head>
				<style type='text/css'>
					.margem 		{ padding-top:20px; padding-bottom:20px; padding-left:20px;padding-right:20px;}
					a:link 			{}
					a:visited 		{}
					a:hover 		{ text-decoration: underline; color:#2C4E67; }
					a:active 		{ text-decoration: none; }
					.texto			{ font-family:'Calibri'; color:#666; font-size:14px; text-align:justify; font-weight:normal;}
					hr				{ border:none; border-top:1px solid #2C4E67;}
					.rodape			{ font-family:Calibri; color:#727272; font-size:12px; text-align:justify; font-weight:normal; }
							
				</style>
			</head>
			<body>
				<table style='font-family:Calibri;' align='center' border='0' width='100%' cellspacing='0' cellpadding='0'>
				<tr>
					<td align='left'>
						<table class='texto'>
							<tr>
								<td align='right'>
									<b>Nome:</b>
								</td>
								<td align='left'>$nome</td>
							</tr>
							<tr>
								<td align='right'>
									<b>Apelido:</b>
								</td>
								<td align='left'>$apelido</td>
							</tr>
							<tr>
								<td align='right'>
									<b>E-mail:</b>
								</td>
								<td align='left'>
									$email
								</td>
							</tr>
							<tr>
								<td align='right'>
									<b>Celular:</b>
								</td>
								<td align='left'>$celular</td>
							</tr>
							<tr>
								<td align='right'>
									<b>Telefone:</b>
								</td>
								<td align='left'>
									$telefone
								</td>
							</tr>

							<tr>
								<td align='right'>
									<b>CEP:</b>
								</td>
								<td align='left'>$cep</td>
							</tr>
							<tr>
								<td align='right'>
									<b>Endereço:</b>
								</td>
								<td align='left'>$endereco</td>
							</tr>


							<tr>
								<td align='right'>
									<b>Número:</b>
								</td>
								<td align='left'>$numero</td>
							</tr>

							<tr>
								<td align='right'>
									<b>Bairro:</b>
								</td>
								<td align='left'>$bairro</td>
							</tr>
							
							<tr>
								<td align='right'>
									<b>Cidade:</b>
								</td>
								<td align='left'>$cidade</td>
							</tr>

							<tr>
								<td align='right'>
									<b>Assunto:</b>
								</td>
								<td align='left'>$assunto</td>
							</tr>						

							<tr>
								<td align='right'>
									<b>Mensagem:</b>
								</td>
								<td align='left' valign='top'>
									$mensagem
								</td>
							</tr>
						</table>
						<hr>
						<span class='rodape'>
							<font size='1' color='#2C4E67'><b>Mensagem enviada:</b></font> " . $datap . "<br>
							Este é um email gerado automaticamente pelo sistema.<br><br>
							As informações contidas nesta mensagem e nos arquivos anexados são para uso restrito, sendo seu sigilo protegido por lei, não havendo ainda garantia legal quanto à integridade de seu conteúdo. Caso não seja o destinatário, por favor desconsidere essa mensagem. O uso indevido dessas informações será tratado conforme as normas da empresa e a legislação em vigor.
						</font>	
				
					</td>
				</tr>
				</table>
			</body>
		";
	$enviado = $mail->Send();
	$mail->ClearAllRecipients();
	$mail->ClearAttachments();

	if ($enviado) {
		echo "
				<SCRIPT language='JavaScript'>
					abreMask(
					'<font color:#e5b35a><b>$nome</b></font>, sua mensagem foi enviada com sucesso, em breve responderemos.<br><br>'+
					'<center><input value=\' Ok \' type=\'button\' class=\'but_mask\' onclick=javascript:window.location.href=\'index\';></center>' );
				</SCRIPT>
			";
	} else {
		echo "
				<SCRIPT language='JavaScript'>
					abreMask(
					'Erro ao enviar mensagem. <br>$mail->ErrorInfo.<br><br>'+
					'<center><input value=\' Ok \' type=\'button\' class=\'but_mask\' onclick=window.history.back();></center>' );
				</SCRIPT>
			";
	}

	?>
</body>

</html>