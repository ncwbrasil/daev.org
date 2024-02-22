<!-- INCLUDES PHP -->
<?php ob_start();
setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
require_once("core/mod_includes/php/ctracker.php");
require_once("core/mod_includes/php/parametros.php");
include('core/mod_includes/php/connect.php');
$ttl = "DAEV - Departamento de Águas e Esgoto de Valinhos";
session_start();
?>
<meta name="adopt-website-id" content="4c955eb1-5046-4235-b201-d0278ed60b83" />
<script src="//tag.goadopt.io/injector.js?website_code=4c955eb1-5046-4235-b201-d0278ed60b83" class="adopt-injector"></script>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5.0">

<!-- <base href="https://<?php echo $_SERVER['HTTP_HOST']; ?>/" /> -->
<base href="https://<?php echo $_SERVER['HTTP_HOST']; ?>/" />

<!--META TAGS-->
<meta charset="utf-8">
<meta property="og:type" content="website" />
<meta property="og:title" content="DAEV - Departamento de Águas e Esgoto de Valinhos" />
<meta property="og:description" content=" " />
<meta name="copyright" content="NCW Brasil">
<meta name="description" content="">

<!-- ESTILO CSS -->
<link rel="stylesheet" type="text/css" href="core/css/estilo.css">
<link rel="stylesheet" type="text/css" href="core/css/animate.css">
<link rel="shortcut icon" href="core/imagens/favicon.png">
<!-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.3/css/all.css"> -->
<script src="https://kit.fontawesome.com/650f618ca2.js" crossorigin="anonymous"></script>

<!-- JAVASCRITP -->
<script src="core/mod_includes/js/funcoes.js"></script>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="core/mod_includes/js/wow.min.js"></script>

<script>
    new WOW().init();
</script>
<script>
    $(document).ready(function() {
        $('.bt_topo').css('display', 'none');
        $(window).scroll(function() {
            if ($(this).scrollTop() > 200) {
                $('.bt_topo').fadeIn();
            } else {
                $('.bt_topo').fadeOut();
            }
        });

        $('.bt_topo').click(function() {
            $('html, body').animate({
                scrollTop: 0
            }, 800);
            return false;
        });
    });

    function fonte(e) {
        var elemento = $("body");


        var fonte = elemento.css('font-size');


        if (e == 'a') {
            elemento.css("fontSize", parseInt(fonte) + 2);

        } else if (e == 'd') {
            elemento.css("fontSize", parseInt(fonte) - 2);

        } else if (e == 'n') {
            elemento.css("fontSize", 16);

        }
    }

    function modContrast(dataContraste) {
        var setId;
        var cont = dataContraste;
        if (cont == 1) {
            setId = 'contrastePreto';
        } else {
            setId = ''
        }
        document.querySelector("body").setAttribute("id", setId);
        $.post('contraste.php', {
            contraste: cont
        }, function(contraste) {})
    }

    $(document).ready(function() {
        var variavel = "<?php echo $_SESSION['contraste']; ?>";
        if (variavel == 1) {
            modContrast(variavel)
        } else {
            modContrast(variavel)
        }

    });
</script>

<div vw class="enabled">
    <div vw-access-button class="active"></div>
    <div vw-plugin-wrapper>
        <div class="vw-plugin-top-wrapper"></div>
    </div>
</div>
<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script>
    new window.VLibras.Widget('https://vlibras.gov.br/app');
</script>

<script>
    function abre() {
        $(".btn-wp").css('margin-right', '0');
        $("#abre").css('display', 'none');
        $("#fecha").css('display', 'table');
    }

    function fecha() {
        $(".btn-wp").css('margin-right', '-60px');
        $("#fecha").css('display', 'none');
        $("#abre").css('display', 'table');
    }
</script>

<?php
    $bn = explode("/", $_SERVER['REQUEST_URI']); 

    if($bn[1]=='pagina'){
        $banner = $bn[2]; 
    }
    else {
        $banner = $bn[1]; 
    }
    $sql_banner= "SELECT * FROM cadastro_testeiras 
    LEFT JOIN aux_menu ON aux_menu.men_id = cadastro_testeiras.ct_menu
    LEFT JOIN aux_submenu ON aux_submenu.sm_id = cadastro_testeiras.ct_submenu
    WHERE sm_link = :sm_link OR men_link = :men_link";
    $stmt_banner = $PDO->prepare($sql_banner);
    $stmt_banner->bindValue(':sm_link', $banner);
    $stmt_banner->bindValue(':men_link', $banner);
    $stmt_banner->execute();
    $rows_banner = $stmt_banner->rowCount();
    if($rows_banner> 0)
    {	
        $result_banner = $stmt_banner->fetch(); 
        $ct_banner = "webapp/".$result_banner['ct_imagem']; 
        echo "
            <style>
                .banner-top {
                    width: 100%;
                    padding: 80px 0;
                    color: #fff;
                    background: #000;
                    position:relative; 
                    background: linear-gradient(90deg, rgba(0,90,141,1) 61%, rgba(255,255,255,1) 100%, rgba(201,240,247,1) 100%);
                    z-index: 9999;   
                }
                
                .banner-top::before {
                    content: '';
                    background: url('".$ct_banner."') center center;
                    background-size:cover; 
                    mix-blend-mode: multiply;
                    opacity: 1;
                    top: 0;
                    left: 0;
                    bottom: 0;
                    right: 0;
                    position: absolute;
                    z-index: -1;   
                  }
            </style>
        ";

    }
    else {
        echo "
            <style>
                .banner-top {
                    width: 100%;
                    padding: 80px 0;
                    background: url('core/imagens/banner/banner-top.jpg') center center;
                    background-size: cover;
                    color: #fff;
                }
            </style>
        ";
    }

?>

