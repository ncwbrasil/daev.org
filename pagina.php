<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
<meta http-equiv="Content-Language" content="pt-br">


<script>
	function recoverUrlNotParams() { 
 var params = window.location.search;
 var url = window.location.pathname;
 if (window.history.replaceState) {
                    window.history.replaceState('Object', this.title,  url);
                } else {
                    //para o IE vc tem que redirecionar
                     if (url.indexOf(params) != -1) {
                        window.location.href = url;
                    }
                }
}
</script>

<head>
	<?php 
		include('header.php'); 
		$pg_url = $_GET['p1'];

		echo'<script>
			if(typeof window.history.pushState == "function") {
				window.history.pushState({}, "Hide", "https://daev.org.br/'.$pg_url.'");
		}
		</script>';

		if($pg_url != ''){
			$sql= "SELECT * FROM aux_menu
			LEFT JOIN aux_submenu ON aux_submenu.sm_menu = men_id
			LEFT JOIN cadastro_paginas ON cadastro_paginas.pg_submenu = aux_submenu.sm_id
			WHERE sm_link = :sm_link OR men_link = :men_link";
			$stmt = $PDO->prepare($sql);
			$stmt->bindParam(':sm_link', $pg_url);
			$stmt->bindParam(':men_link', $pg_url);
			$stmt->execute();
			$rows = $stmt->rowCount();    	
			if($rows> 0)
			{	
				$result = $stmt->fetch();
				$pg_titulo = $result['pg_titulo']; 
				$pg_descricao = $result['pg_descricao']; 
				$pg_data    = implode("/", array_reverse(explode("-", $result['pg_data'])));
				$pg_menu = $result['pg_menu']; 
			}
			else{
				header('Location: /daev/404-pagina-nao-encontrada/');
			} 
		}			

	?>
	<title><?php echo $ttl; ?></title>
</head>

<body>
	<header>
		<?php
			#region MOD INCLUDES
			include('core/mod_topo/topo.php');
			include('core/mod_includes/php/funcoes-jquery.php');
			#endregion
		?>
	</header>
	<main>
		<section>
			<div class="banner-top">
				<h1 class="titulo"><?php echo $pg_titulo?></h1>
			</div>
		</section>
		<section>
			<div class="wrapper" id='pagina'>
			
				<?php 
					echo"
					<div class='conteudo'>
						$pg_descricao";

						include('compartilhe.php');
					echo"</div> 
					<div class='veja_mais'>									
						<div class='bloco'>
							<h3> Veja Mais </h3>
							<ul>";
								$sql= "SELECT *	FROM aux_menu
								LEFT JOIN aux_submenu On aux_submenu.sm_menu = aux_menu.men_id
								WHERE men_id = :men_id
								ORDER BY men_posicao, sm_posicao DESC";
								$stmt = $PDO->prepare($sql);
								$stmt->bindValue(':men_id', $pg_menu );
								$stmt->execute();
								$rows = $stmt->rowCount(); 
								if($rows> 0)
								{						
									while($result = $stmt->fetch()){
										$link = substr($result['sm_link'], 0,4);
										if($link == "http" || $link == 'HTTP' || $link == 'Http'){
											echo "<li><a href='".$result['sm_link']."' target='_blank'><i class='far fa-arrow-alt-circle-right'></i> ".$result['sm_titulo']."</a></li>";

										}else{
											echo "<li><a href='router/".$result['sm_link']."'><i class='far fa-arrow-alt-circle-right'></i> ".$result['sm_titulo']."</a></li>";
										}
									}
								}
							echo"</ul>
						</div>
					</div>
					";				
				?>
			</div>
		</section>
		<?php
			include('core/mod_rodape/rodape.php');
		?>
	</main>
</body>

</html>