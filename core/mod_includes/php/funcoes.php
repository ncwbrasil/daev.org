<?php

function bindFields($fields)
{

    end($fields);
    $lastField = key($fields);

    $bindString = ' ';
    foreach ($fields as $field => $data) {
        $bindString .= $field . '=:' . $field;
        $bindString .= ($field === $lastField ? ' ' : ',');
    }
    return $bindString;
}
function selectLoginUsuario($PDO, $email, $senha)
{
    $sql = "SELECT * FROM cadastro_usuarios 
			INNER JOIN admin_setores ON admin_setores.set_id = cadastro_usuarios.usu_setor
            WHERE usu_email = :email AND usu_senha = :senha
		";
    $stmt = $PDO->prepare($sql);

    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $senha);
    $stmt->execute();
    $rows = $stmt->rowCount();
    if ($rows > 0) {
        while ($field = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $field;
        }
    }

    return $result;
}

function selectLoginFornecedor($PDO, $email, $senha)
{
    $sql = "SELECT * FROM fornecedores 
            WHERE email = :email AND password = :senha";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $senha);
    $stmt->execute();
    $rows = $stmt->rowCount();
    if ($rows > 0) {
        while ($field = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $field;
        }
    }
    return $result;
}

function insertLogUsuario($PDO, $dados)
{

    $dados = array_filter($dados);

    $sql = " INSERT INTO log_login_usuarios SET " . bindFields($dados) . " ";

    $stmt = $PDO->prepare($sql);
    $stmt->execute($dados);
    $rows = $stmt->rowCount();
    return $rows;
}

function alertaWeb($PDO, $remetente, $destinatario, $ale_descricao, $ale_link)
{

    $sql = "SELECT * FROM cadastro_usuarios
            LEFT JOIN admin_setores ON admin_setores.set_id = cadastro_usuarios.usu_setor
            WHERE set_nome IN ( $destinatario )";
    $stmt = $PDO->prepare($sql);

    $stmt->execute();
    $rows = $stmt->rowCount();
    if ($rows > 0) {
        while ($result = $stmt->fetch()) {
            $sql_alerta = "INSERT INTO social_alertas SET 
                            ale_remetente = :ale_remetente,  
                            ale_destinatario = :ale_destinatario,
                            ale_descricao = :ale_descricao, 
                            ale_lida = :ale_lida,
                            ale_arquivado = :ale_arquivado,
                            ale_link = :ale_link ";
            $stmt_alerta = $PDO->prepare($sql_alerta);
            $stmt_alerta->bindParam(':ale_remetente', $remetente);
            $stmt_alerta->bindParam(':ale_destinatario', $result['usu_id']);
            $stmt_alerta->bindParam(':ale_descricao', $ale_descricao);
            $stmt_alerta->bindValue(':ale_lida', 0);
            $stmt_alerta->bindValue(':ale_arquivado', 0);
            $stmt_alerta->bindParam(':ale_link', $ale_link);
            if ($stmt_alerta->execute()) {
            } else {
                $erro = 1;
            }
        }
        if ($erro != 1) {
            return "true";
        } else {
            return "false";
        }
    }
}
function sec_session_start()
{
    //$session_name = 'sec_session_id'; // Define um nome padrão de sessão
    $secure = false; // Defina como true (verdadeiro) caso esteja utilizando https.
    $httponly = true; // Isto impede que o javascript seja capaz de acessar a id de sessão. 

    ini_set('session.use_only_cookies', 1); // Força as sessões a apenas utilizarem cookies. 
    $cookieParams = session_get_cookie_params(); // Recebe os parâmetros atuais dos cookies.
    session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
    //session_name($session_name); // Define o nome da sessão como sendo o acima definido.
    session_start(); // Inicia a sessão php.
    //session_regenerate_id(true); // regenerada a sessão, deleta a outra.
}
function reverteData($campo)
{
    if (strpos($campo, '/') === false) {
        $nova_data = implode("/", array_reverse(explode("-", $campo)));
    } else {
        $nova_data = implode("-", array_reverse(explode("/", $campo)));
    }
    return $nova_data;
}
function rearrange($arr)
{
    foreach ($arr as $key => $all) {
        foreach ($all as $i => $val) {
            $new[$i][$key] = $val;
        }
    }
    return $new;
}
function verificaPermissao($acao, $permissoes, $pagina)
{
    $_SESSION['negado'] = 0;
    if ($permissoes[$acao] != 1) {
        $_SESSION['negado'] = 1;
        exit;
        return false;
    } else {
        return true;
    }
}

function geradorTags($valor)
{
    $array1 = array(
        "á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç",
        "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç",
        "/", "- ", ",", "%", "?", "&", "º", "ª", "|", "'", "(", ")", ":", "´", '"', ".", '”', "!", "*", "`", "+", "--", "[","]", "{","}", " ", "  ","¨","#", "=", "$","_", "   "
    );

    $array2 = array(
        "a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c",
        "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C",
        "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "","", "", "", "", "","","","","-","-", "", "","","","",""
    );

    $tags = str_replace($array1, $array2, $valor);
    $tags = strtolower($tags);
    $tags = str_replace('--', '-', $tags);
    return $tags;
}

function limpaString($valor)
{
    $array1 = array(
        "á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç",
        "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç",
        "/", "- ", ",", "?", "&", "º", "ª", "|", "'", "(", ")", ":"
    );

    $array2 = array(
        "a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c",
        "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C",
        "", "", "", "", "", "", "", "", "", "", "", ""
    );

    $tags = str_replace($array1, $array2, utf8_decode($valor));

    return $tags;
}

function limpaStringAll($VString)
{
    $VNovo = "";
    for ($i = 0; $i < mb_strlen($VString); $i++) {
        if (preg_match("/[a-zA-Z0-9]/", substr($VString, $i, 1)) == 1)
            $VNovo .= substr($VString, $i, 1);
    }
    return $VNovo;
}

function bindFields2($fields)
{
    end($fields);
    $lastField = key($fields);

    $bindString = ' ';
    foreach ($fields as $field => $data) {
        $bindString .= ':' . $data;
        $bindString .= ($field === $lastField ? ' ' : ',');
    }
    return $bindString;
}

function RetirarAcentos($frase)
{
    $frase = str_replace(
        array("à", "á", "â", "ã", "ä", "è", "é", "ê", "ë", "ì", "í", "î", "ï", "ò", "ó", "ô", "õ", "ö", "ù", "ú", "û", "ü", "À", "Á", "Â", "Ã", "Ä", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ò", "Ó", "Ô", "Õ", "Ö", "Ù", "Ú", "Û", "Ü", "ç", "Ç", "ñ", "Ñ"),
        array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "c", "C", "n", "N"),
        $frase
    );

    return $frase;
}


//########## FUNCAO PROXIMO DIA UTIL ###########
function getDayOfWeek($timestamp)
{
    $date = getdate($timestamp);
    $diaSemana = $date['weekday'];
    if (preg_match('/(sunday|domingo)/mi', $diaSemana))
        $diaSemana = 'Domingo';
    else if (preg_match('/(monday|segunda)/mi', $diaSemana))
        $diaSemana = 'Segunda';
    else if (preg_match('/(tuesday|terça)/mi', $diaSemana))
        $diaSemana = 'Terça';
    else if (preg_match('/(wednesday|quarta)/mi', $diaSemana))
        $diaSemana = 'Quarta';
    else if (preg_match('/(thursday|quinta)/mi', $diaSemana))
        $diaSemana = 'Quinta';
    else if (preg_match('/(friday|sexta)/mi', $diaSemana))
        $diaSemana = 'Sexta';
    else if (preg_match('/(saturday|sábado)/mi', $diaSemana))
        $diaSemana = 'Sábado';

    return $diaSemana;
}

function diaUtil($data)
{
    while (true) {
        if (getDayOfWeek($data) == 'Sábado') {

            $data = $data + (86400 * 2);
            return diaUtil($data);
        } else if (getDayOfWeek($data) == 'Domingo') {

            $data = $data + (86400 * 1);
            return diaUtil($data);
        } else {
            return $data;
        }
    }
}
function getIp()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

//############# FIM FUNCAO PROXIMO DIA UTIL ###############



//FUNÇÃO PARA ENVIAR EMAIL //

function enviaSenha($email)
{    $agora = time();
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

    // $stmt->execute();
    require("phpmailer/class.phpmailer.php");
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Host = "mail.mogicomp.com.br"; // Endereço do servidor SMTP (caso queira utilizar a autenticação, utilize o host smtp.seudomínio.com.br)
    $mail->SMTPAuth = true; // Usa autenticação SMTP? (opcional)
    $mail->Username = 'autenticacao@mogicomp.com.br'; // Usuário do servidor SMTP
    $mail->Password = 'Infomogi123#'; // Senha do servidor SMTP
    $mail->From = "autenticacao@mogicomp.com.br"; // Seu e-mail
    $mail->FromName = "DAEV"; // Seu nome
    $mail->AddAddress($email);
    $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
    $mail->SMTPDebug = 1;
    $mail->CharSet = 'utf-8'; // Charset da mensagem (opcional)
    $assunto = 'DAEV - Formulário de Contato';
    $mail->Subject  = '=?utf-8?B?' . base64_encode($assunto) . '?='; // Assunto da mensagem
    $mail->Body = "
        <head>
            <style type='text/css'>
                .margem 		{ padding-top:20px; padding-bottom:20px; padding-left:20px;padding-right:20px;}
                a:link 			{color:#fff}
                a:visited 		{}
                a:hover 		{ color:##2f91ce; }
                a:active 		{ text-decoration: none; }
                .texto			{ font-family:'Calibri'; color:#666; font-size:14px; text-align:justify; font-weight:normal;}
                hr				{ border:none; border-top:1px solid #2C4E67;}
                .rodape			{ font-family:Calibri; color:#727272; font-size:12px; text-align:justify; font-weight:normal; }

                .botao          { padding: 20px 40px; background:#2f91ce; color:#fff; border-radius:15px; transition:0.3s;}
                .botao:hover    { background: #fff;}
                        
            </style>
        </head>
        <body>
            <h1>Cadastro Confirmado! </h1>

            <h3>Finalizamos o cadastro da sua empresa</h3> 

            <p>para concluir clique no link à baixo para confimar o seu acesso e cadastrar uma senha para acessar nosso painel </p> 
            
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

    // if ($enviado) {
    //     echo "Enviado";
    //     exit; 
    //     echo "
    //         <SCRIPT language='JavaScript'>
    //             abreMask(
    //             '<font color:#e5b35a><b>$nome</b></font>, sua mensagem foi enviada com sucesso, em breve responderemos.<br><br>'+
    //             '<center><input value=\' Ok \' type=\'button\' class=\'but_mask\' onclick=javascript:window.location.href=\'index\';></center>' );
    //         </SCRIPT>
    //     ";
    // } 
    // else {
    //     echo 'Erro ao enviar mensagem. <br>'.$mail->ErrorInfo.'<br><br>'; 
    //     exit; 

    //     echo "
    //         <SCRIPT language='JavaScript'>
    //             abreMask(
    //             'Erro ao enviar mensagem. <br>$mail->ErrorInfo.<br><br>'+
    //             '<center><input value=\' Ok \' type=\'button\' class=\'but_mask\' onclick=window.history.back();></center>' );
    //         </SCRIPT>
    //     ";
    // }
}
