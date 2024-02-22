<?php
    require_once("../core/mod_includes/php/ctracker.php");
    include_once("../core/mod_includes/php/connect.php");
    include_once("../core/mod_includes/php/funcoes.php");

    $email = $_POST['email'];

    $email2 = hash('sha512', $_POST['email']);

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
    
    require("../core/mod_includes/php/phpmailer/class.phpmailer.php");
	// Inicia a classe PHPMailer
	$mail = new PHPMailer();
	// Define os dados do servidor e tipo de conexão
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$mail->IsSMTP();
	// $mail->Host = "mail.ncwbrasil.com.br"; // Endereço do servidor SMTP (caso queira utilizar a autenticação, utilize o host smtp.seudomínio.com.br)
	$mail->SMTPAuth = false; // Usa autenticação SMTP? (opcional)
	// $mail->Username = 'autenticacao@daev.org.br'; // Usuário do servidor SMTP
	// $mail->Password = 'daev@2021'; // Senha do servidor SMTP

	// Define o remetente
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$mail->From = "compras@daev.org.br"; // Seu e-mail
	// $mail->Sender = "autenticacao@daev.org.br"; // Seu e-mail
	$mail->FromName = "$nome"; // Seu nome


	// Define os destinatário(s)
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	// $mail->AddAddress('anderson.zorzato@daev.org.br');
    // $mail->AddCC('compras@daev.org.br');
	$mail->AddAddress($email);
    $mail->AddCC('compras@daev.org.br');


	// Define os dados técnicos da Mensagem
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$mail->IsHTML(true); // Define que o e-mail será enviado como HTML

	$mail->CharSet = 'utf-8'; // Charset da mensagem (opcional)

    $assunto = 'DAEV - Formulário de Contato';
    $mail->Subject  = '=?utf-8?B?' . base64_encode($assunto) . '?='; // Assunto da mensagem
    $mail->Body = "
        <head>
            <style type='text/css'>
                .margem 		{ padding-top:20px; padding-bottom:20px; padding-left:20px;padding-right:20px;}
                a:link 			{color:#999}
                a:visited 		{}
                a:hover 		{ color:#2f91ce; }
                a:active 		{ text-decoration: none; }
                .texto			{ font-family:'Calibri'; color:#666; font-size:14px; text-align:justify; font-weight:normal;}
                hr				{ border:none; border-top:1px solid #2C4E67;}
                .rodape			{ font-family:Calibri; color:#727272; font-size:12px; text-align:justify; font-weight:normal; }

                .botao          { padding: 20px 40px; background:#2f91ce; color:#fff; border-radius:15px; transition:0.3s;}
                .botao:hover    { background: #fff;}

                p               {font-size:20px}
                        
            </style>
        </head>
        <body>
            <h1>Cadastro Confirmado! </h1>

            <h2>Finalizamos o cadastro da sua empresa</h2> 

            <p>Para concluir seu cadastro clique no link à baixo, você será direcionado para uma página onde ira cadastrar uma senha de acesso para o nosso sistema! </p>

            <p><a href='https://www.daev.org.br/cadastre_senha/$email2'> https://www.daev.org.br/cadastre_senha</a></p>

            <p> Em caso de dúvidas, entre em contato com o setor de compras.</p>
            
            
            <span class='rodape'>
                <font size='1' color='#2C4E67'><b>Mensagem enviada:</b></font> " . $datap . "<br>
                Este é um email gerado automaticamente pelo sistema.<br><br>
                As informações contidas nesta mensagem e nos arquivos anexados são para uso restrito, sendo seu sigilo protegido por lei, não havendo ainda garantia legal quanto à integridade de seu conteúdo. Caso não seja o destinatário, por favor desconsidere essa mensagem. O uso indevido dessas informações será tratado conforme as normas da empresa e a legislação em vigor.
            </font>
        </body>
    ";
    $enviado = $mail->Send();
    $mail->ClearAllRecipients();
    $mail->ClearAttachments();

    if ($enviado) {
        echo "Enviado";
        exit;
    } 
    else {
        echo $mail->ErrorInfo;
        exit;
    }

?>


