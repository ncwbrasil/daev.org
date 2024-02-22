<link rel="stylesheet" type="text/css" href="core/css/menu_resp.css">
<nav>
    <label for="drop" class="toggle barras" style="border:none;"><i class="fas fa-bars"></i></label>
    <input type="checkbox" id="drop" />
    
    <ul class="menu_resp">
        <?php
            echo"<li><a href='index/' class='tp'>Home</a></li>";

            $sql = "SELECT men_id, men_titulo, men_link, men_posicao, men_topo FROM aux_menu
                    WHERE men_topo = :men_topo 
                    ORDER BY men_posicao ASC";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(':men_topo',     1);
            $stmt->execute();
            $rows = $stmt->rowCount();
            $i = 0; 
            if($rows >0 ){
                while ($result = $stmt->fetch()) {
                    $i++; 
                    $sql_sub = "SELECT * FROM aux_submenu
                    WHERE sm_topo = :sm_topo AND sm_menu = :sm_menu
                    ORDER BY sm_posicao ASC";
                    $stmt_sub = $PDO->prepare($sql_sub);
                    $stmt_sub->bindValue(':sm_topo',     1);
                    $stmt_sub->bindValue(':sm_menu', $result['men_id']);
                    $stmt_sub->execute();
                    $rows_sub = $stmt_sub->rowCount();

                    if($rows_sub >0 ){ 
                        echo"<li>
                                <label for='drop-$i' class='toggle submenu'><a class='tp'>".$result['men_titulo']." <i class='fas fa-caret-down'></i> </a></label> <!--  ITEM QUE APARECE NO MENU RESPONSÍVO -->
                                <a href='#' class='tp'>".$result['men_titulo']." <i class='fas fa-caret-down'></i></a><!--  ITEM QUE APARECE NO MONITOR -->
                                <input type='checkbox' id='drop-$i'/> 
                                <ul>";
                                    while ($result_sub = $stmt_sub->fetch()) {
                                            $link = substr($result_sub['sm_link'], 0,4);
                                            if($link == "http" || $link == 'HTTP' || $link == 'Http'){
                                                echo"<li><a href='router/".$result_sub['sm_compartilhamento']."' target='_blank'>".$result_sub['sm_titulo']."</a></li>";
                                            }else{
                                                echo"<li><a href='router/".$result_sub['sm_link']."'>".$result_sub['sm_titulo']."</a></li>";
                                            }
                                    }
                        echo "</ul>
                        </li>";

                    }
                    else {
                        $link = substr($result['men_link'], 0,4);
                        if($link == "http" || $link == 'HTTP' || $link == 'Http'){
                            echo"<li><a href='".$result['men_link']."' class='tp'>".$result['men_titulo']."</a></li>";
                        }else{
                            echo"<li><a href='router/".$result['men_link']."' target='_blank' class='tp'>".$result['men_titulo']."</a></li>";
                        }

                    }
                }
            }
        ?>
    </ul>
</nav>

