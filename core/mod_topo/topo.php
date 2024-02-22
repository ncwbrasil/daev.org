<div class="topo">
    <div class="faixa">
        <div id='faixa'>
            <div class='acess'>
                <div class='bloco1'>
                    <a onclick="busca()"  accesskey="1" title='Ir Para o Mapa do Site' class='unico' style='cursor:pointer'> <i class="fas fa-arrow-circle-down"></i> Ir Para a Busca [1]</a>
                    <a href='#conteudo' accesskey="2" title='Ir Para Conteúdo' class='unico'> <i class="fas fa-arrow-circle-down"></i> Ir para o Conteúdo [2]</a>
                    <a href='#mapa' accesskey="3" title='Ir Para o Mapa do Site' class='unico'> <i class="fas fa-arrow-circle-down"></i> Ir Para o Mapa do Site [3]</a>
                </div>
                <div class="bloco2">
                    <a href='#' accesskey="4" onclick="modContrast(1)" title='Contraste Escuro'><i class="fas fa-moon" title="Contraste Escuro"></i> [4] </a> 
                    <a href='#' accesskey="5" onclick="modContrast(2)" title='Contraste Claro'><i class="fas fa-sun"  title="Contraste Claro"></i> [5]</a>
                    <a href='#' accesskey="6" onClick="fonte('a');" title='Aumentar Fonte'><i class="fas fa-search-plus"  title="Aumentar Fonte"></i> [6]</a>
                    <a href='#' accesskey="7" onClick="fonte('d');" title='Diminuir Fonte'><i class="fas fa-search-minus"  title="Diminuir Fonte"></i> [7]</a>
                    <a href='#' accesskey="8" onClick="fonte('n');" title='Restaurar Padrão de Fonte'><i class="fas fa-sync-alt"  title="Restaurar Padrão de Fonte"></i> [8]</a>
                    <a href='http://smarapd.daev.org.br:90/esic/#!/login' accesskey="9"  title='Sistema Eletrônico de Informação do DAEV' target="_blank" class='unico'><i class="fas fa-info-circle"></i> Acesso à Informação [9] </a>
                </div>
            </div>
            <?php
            $sql = "SELECT * FROM aux_configuracao";
            $stmt = $PDO->prepare($sql);
            $stmt->execute();
            $rows = $stmt->rowCount();
            $i = 0;
            if ($rows > 0) {
                $result = $stmt->fetch();
                $cadastro = $result['conf_cadastro'];
                $login = $result['conf_login'];
                $facebook = $result['conf_face']; 
                $whats = substr(preg_replace('/[^0-9]/', '', $result['conf_whats']), 0, -5);
                echo "
                    <div class='tp_cad'>

                    ";
                if ($_SESSION['fn_id'] == 0){
                    if ($login == 1) {
                        echo "
                            <div class='log'>
                                <a href='login/'>Logar</a>
                            </div>
                        ";
                    }
                    if ($cadastro == 1) {
                        echo "
                            <div class='cad'>
                                <a href='seja-um-fornecedor'>Cadastre-se</a>
                            </div>             
                        ";
                    }
                }else {
                    if ($login == 1) {
                        echo "
                            <div class='log'>
                                <a href='meu-perfil/'>Meu Perfil</a>
                            </div>
                            <div class='cad'>
                                <a href='sair/'>Sair</a>
                            </div>             

                        ";
                    }
                }
                echo "</div>
                    </div>
                </div>                     
                <div id='topo'>
                    <div class='logo'>
                        <a href='index/'><img src='webapp/" . $result['conf_logo'] . "' alt='DAEV - Departamento de Águas e Esgoto de Valinhos' class='logo_c'></a>
                    </div>                     
                ";
            }
            ?>

            <div class="busca">
                <form name='form_busca' id='form_busca' enctype='multipart/form-data' method='post' action='busca/'>
                    <input type='text' placeholder="Pesquisa" name="busca" id="busca"> <button type="submit" value='Pesquisar' title="Realizar Pesquisa"><i class="fa fa-search"></i></button>
                </form>
            </div>

            <div class='menu'>
                <?php include("core/mod_menu/menu_site.php");  ?>
            </div>
        </div>
    </div>


<div class="btn-wp">
    <div id='setas'>
        <i class="fas fa-angle-double-left" id='abre' onclick="abre()"></i>
        <i class="fas fa-angle-double-right" id='fecha' onclick="fecha()"></i>
    </div>
    <div id='sociais'>
        <a href="https://wa.me/+55<?php echo $whats?>" target="_blank" rel="noopener noreferrer"> <i class="fab fa-whatsapp"></i> </a>
        <a href="<?php echo $facebook;?>" target="_blank" rel="noopener noreferrer"> <i class="fab fa-facebook"></i> </a>
    </div>
</div>


<script>
function busca() {
   $("#busca").focus();
   return false;
};

</script>