<footer>
<div class='rodape' id='mapa'>
    <div id='rodape_empresas'>
        <div class="wrapper">
            <div class="bloco">
                <a href="http://www.valinhos.sp.gov.br/" target='_blank'><img src="core/imagens/site/logo1.jpg"></a>
            </div>
            <div class="bloco">
                <img src="core/imagens/site/logo2.jpg">
            </div>
            <div class="bloco">
                <a href="http://www.arespcj.com.br/" target="_blank"><img src="core/imagens/site/logo3.jpg"></a>
            </div>
        </div>
    </div>
    <div id="rodape">
        <h2 class="azul2"> Mapa do Site </h2>
        <ul> 
            <?php
                $sql = "SELECT * FROM aux_menu 
                ORDER BY men_posicao ASC";
                $stmt = $PDO->prepare($sql);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if ($rows > 0)
                {	$array = array(); 
                    while( $result = $stmt->fetch()){

                        $link = substr($result['sm_link'], 0,4);
                        if($link == "http" || $link == 'HTTP' || $link == 'Http'){
                           $array['menu'][] = "<li class='men'><a href='".$result['men_link']."' target='_blank'> <i class='far fa-arrow-alt-circle-right'></i> ".$result['men_titulo']."</a></li>";
                        }else{
                            $array['menu'][]= "<li class='men'><a href='#'><i class='far fa-arrow-alt-circle-right'></i> ".$result['men_titulo']."</a></li>";
                            $sql_mapa1 = "SELECT * FROM aux_submenu 
                            WHERE sm_menu = :sm_menu
                            ORDER BY sm_posicao ASC";
                            $stmt_mapa1 = $PDO->prepare($sql_mapa1);
                            $stmt_mapa1->bindValue('sm_menu', $result['men_id']);
                            $stmt_mapa1->execute();
                            $rows_mapa1 = $stmt_mapa1->rowCount();
                            if ($rows_mapa1 > 0)
                            {	
                                while( $result_mapa1 = $stmt_mapa1->fetch()){
                                    $link = substr($result_mapa1['sm_link'], 0,4);
                                    if($link == "http" || $link == 'HTTP' || $link == 'Http'){
                                        $array['menu'][] = "<li class='sub'><a href='".$result_mapa1['sm_link']."' target='_blank'>".$result_mapa1['sm_titulo']."</a></li>";
                                    }else{
                                        $array['menu'][] = "<li class='sub'><a href='router/".$result_mapa1['sm_link']."'>".$result_mapa1['sm_titulo']."</a></li>";
                                    }
                                }
                            }      
        
                        }
                    }
                    
                    echo "<div class='bloco'>";
                        for($m1 = 0; $m1<20; $m1++){
                            print_r($array['menu'][$m1]); 
                        }
                    echo "</div>"; 


                    echo "<div class='bloco'>";
                        for($m2 = 20; $m2<40; $m2++){
                            print_r($array['menu'][$m2]); 
                        }
                    echo "</div>"; 

                    echo "<div class='bloco'>";
                        for($m3 = 40; $m3<60; $m3++){
                            print_r($array['menu'][$m3]); 
                        }
                    echo "</div>"; 
                        
                    echo "<div class='bloco'>";
                        for($m4 = 60; $m4<80; $m4++){
                            print_r($array['menu'][$m4]); 
                        }
                    echo "</div>"; 
                }   
            ?>
        </ul>
   </div>
    <div id="copy">
        <p>© <?php echo date('Y'); ?> Copyright - Todos os direitos reservados</p>
    </div>
</div>
</footer>