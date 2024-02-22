<?php
    include('header.php'); 
    $pg_url = $_GET['p1'];
    $sql= "SELECT * FROM aux_menu
    LEFT JOIN aux_submenu ON aux_submenu.sm_menu = aux_menu.men_id
    WHERE men_link = :men_link OR sm_link = :sm_link or sm_compartilhamento = :sm_compartilhamento
    GROUP BY men_link";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':men_link', $pg_url);
    $stmt->bindParam(':sm_link', $pg_url);
    $stmt->bindParam(':sm_compartilhamento', $pg_url);
    $stmt->execute();
    $rows = $stmt->rowCount();
    if($rows> 0)
    {	
        $result = $stmt->fetch();

        if($result['sm_link']!==''){          
               
            if($result['sm_tipo'] == 1){
                echo"<script>window.location.replace('".$result['sm_link']."');</script>"; 
            }
            
            if($result['sm_tipo'] == 2){
                echo"<script>window.location.replace('servicos/".$result['sm_link']."');</script>"; 

            }

            if($result['sm_compartilhamento'] !== 'Null'){
                echo"<script>window.location.replace('".$result['sm_link']."');</script>"; 
            }
            
            else {
                $sql_pg= "SELECT * FROM cadastro_paginas
                WHERE pg_submenu = :pg_submenu";
                $stmt_pg = $PDO->prepare($sql_pg);
                $stmt_pg->bindParam(':pg_submenu', $result['sm_id']);
                $stmt_pg->execute();
                $rows_pg = $stmt_pg->rowCount();
                if($rows_pg>0){


                    echo"<script>window.location.replace('pagina/".$result['sm_link']."');</script>"; 
                }
                else{
                    echo"<script>window.location.replace('404-pagina-nao-encontrada/');</script>"; 
                } 
            }
        }
        elseif($result['men_link']!=='') {
            $sql_pg= "SELECT * FROM cadastro_paginas
            WHERE pg_menu = :pg_menu";
            $stmt_pg = $PDO->prepare($sql_pg);
            $stmt_pg->bindParam(':pg_menu', $result['men_id']);
            $stmt_pg->execute();
            $rows_pg = $stmt_pg->rowCount();
            if($rows_pg>0){
                
                if($result['men_tipo'] == 1){
                    echo"<script>window.location.replace('pagina/".$result['men_link']."');</script>"; 

                }else {
                    echo"<script>window.location.replace('pagina/".$result['men_link']."');</script>"; 
                }
            }
            else{

                    echo"<script>window.location.replace('404-pagina-nao-encontrada/');</script>"; 
            } 

        }
        else {
            echo"<script>window.location.replace('404-pagina-nao-encontrada/');</script>"; 
        }

    }
    else{
        $sql= "SELECT * FROM cadastro_atos_administrativos
        WHERE cad_compartilhamento = :cad_compartilhamento";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':cad_compartilhamento', $pg_url);
        $stmt->execute();
        $rows = $stmt->rowCount();
        if($rows> 0){
            $result=$stmt->fetch(); 
            echo"<script>window.location.replace('".$result['cad_link']."');</script>"; 
        }
    
        echo"<script>window.location.replace('404-pagina-nao-encontrada/');</script>"; 
    } 
?>