<?php
	require_once("core/mod_includes/php/ctracker.php");
	include_once("core/mod_includes/php/connect.php");
	include_once("core/mod_includes/php/funcoes.php");

	$pagina = $_POST['pagina'];

	if($pagina == 'licitacoes'){
		$ano = "%".$_POST['lic_ano']."%"; 
		$categoria = $_POST['categoria']; 

		if($categoria != ''){
			$query = " AND lc_id = :lc_id"; 
		}

		$sql = " SELECT * FROM licitacao_pregao 
		LEFT JOIN licitacao_categorias ON licitacao_categorias.lc_id = licitacao_pregao.id_categoria
		WHERE data_abertura like :data_abertura ".$query." AND exibir = :exibir
		order by case when ordem is null then 1 else 0 end, ordem, data_abertura DESC";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':data_abertura', $ano);
		$stmt->bindValue(':exibir', 1);
		$stmt->bindParam(':lc_id', $categoria);
		$stmt->execute();
		echo "
		<div class='cabecalho'>
			<h1> Licitações do ano de <span id='ano_atual' data='".$_POST['lic_ano']."'>".$_POST['lic_ano']." </span></h1>
			<select id='categoria' name='categoria'>";

			if($categoria == ''){
				echo "<option value=''>Selecione uma Categoria </option>"; 
				$sql2 = " SELECT * FROM licitacao_categorias 
				ORDER BY lc_titulo";
				$stmt2 = $PDO->prepare($sql2);
				$stmt2->execute();
				while($result2 = $stmt2->fetch())
				{
					echo "<option value='".$result2['lc_id']."'>".$result2['lc_titulo']."</option>"; 
				}
			}else {

				$sql2 = " SELECT * FROM licitacao_categorias WHERE lc_id = :lc_id";
				$stmt2 = $PDO->prepare($sql2);
				$stmt2->bindParam(':lc_id', $categoria);
				$stmt2->execute();
				$result2 = $stmt2->fetch();
				echo "<option value='".$result2['lc_id']."'>".$result2['lc_titulo']."</option>"; 
				$sql3 = " SELECT * FROM licitacao_categorias
				WHERE lc_id <> :lc_id
				ORDER BY lc_titulo";
				$stmt3 = $PDO->prepare($sql3);
				$stmt3->bindParam(':lc_id', $categoria);
				$stmt3->execute();
				while($result3 = $stmt3->fetch())
				{
					echo "<option value='".$result3['lc_id']."'>".$result3['lc_titulo']."</option>"; 
				}
			}
			echo "</select>
		</div>";
		$i = 0; 
		$c = 10; 
		$p = 1; 
		while($result = $stmt->fetch())
		{
			$i++; 
			if($i == $c){$p ++;$i= 0;}
			$date = date_create($result['data_abertura']);
			$data = date_format($date , 'd/m/Y');
			$hora_abertura = date_format($date , 'H:i');

			if($result['abertura_status'] != 0 && $result['abertura_status'] != '' && $result['abertura_status'] != Null){
				$status = "<span class='situacao'> Aberto </span>";
				$hora = 1; 
			}
			if($result['habilitacao_status'] != 0 && $result['habilitacao_status'] != '' && $result['habilitacao_status'] != Null){
				$status .="<span class='situacao'> Habilitado </span>";
				$hora = 0; 
			}
			if($result['julgamento_status'] != 0 && $result['julgamento_status'] != '' && $result['julgamento_status'] != Null){
				$status .= "<span class='situacao'> Em Julgamento </span>";
				$hora = 0; 
			}
			if($result['homologacao_status'] != 0 && $result['homologacao_status'] != '' && $result['homologacao_status'] != Null){
				$status .= "<span class='situacao'> Homologado </span>";
				$hora = 0;
			}
			if($result['situacao'] == 1){
				$status .= "<span class='situacao'> Cancelado </span>";
			}else{
				if ($result['situacao'] == 2) {
					$status .= "<span class='situacao'> Revogado </span>";
				}
			}

			echo " 
				<a href='licitacoes/".$result['lic_id']."'>
					<div class='item bloco$p' style='display:none'>
						<h3>".$result['codigo']."</h3>";
						if($result['numero_processo'] != ''){echo "Processo nº:".$result['numero_processo']; }
						echo"<p>".$result['titulo']." <br>
						<i class='fas fa-list-alt'></i> ".$result['lc_titulo']."<br>
						<span class='data'><i class='fas fa-calendar-alt'></i> $data ";if($hora == 1 ){echo " às $hora_abertura";} echo "</span>
						<p>Comunicados: $status</p>
					</div>
				</a>
			"; 
		}

		echo "<div class='paginacao'>";
				for($z=1; $z<=$p; $z++){
					echo"<button id='bloco$z' class='pg' value='bloco$z'> $z </button>";
				}
			echo"
		</div>
			<script>	
		
				$(document).ready(function(){
					$('.bloco1').css('display','table'); 

					$('button').click(function() {
						let valor = $(this).val(); 
						$('.licitacoes .item').css('display','none'); 
						$('.'+valor).css('display','table'); 
						$('.pg').css('background','#2f91ce'); 
						$('.pg').css('color','#fff');

						$('#'+valor).css('color','#2f91ce'); 
						$('#'+valor).css('background','#fff'); 
					});
				});
						
				$('select[name=categoria]').change(function(){

					var ano = $('#ano_atual').attr('data');

					$.post('carrega_conteudo.php',{pagina: 'licitacoes', categoria:$(this).val(), lic_ano: ano},
					function(valor){
						$('.ano').css('display','none');
						$('.licitacoes').html(valor);
					}
				)
				})				
			</script>
		"; 
		exit; 

	}

	if($pagina == 'carrega_endereco'){
		$cep = $_POST['cep'];

		$sql = " SELECT * FROM end_enderecos
		LEFT JOIN end_bairros ON end_bairros.bai_id = end_enderecos.end_bairro
		LEFT JOIN end_municipios ON end_municipios.mun_id = end_enderecos.end_municipio
		LEFT JOIN end_uf ON end_uf.uf_id = end_enderecos.end_uf
		WHERE end_cep = :end_cep";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':end_cep', $cep);
		$stmt->execute();
		while($result = $stmt->fetch())
		{
			echo "
			<div class='bloco_rg'>
				<label>Endereço</label>
				<input type='text'  name='address' id='address' value='".$result['end_endereco']."' class='obg'>
			</div>

			<div class='bloco_l'>
				<label>Número</label>
				<input type='text' name='number' id='number' class='obg'>
			</div>

			<div class='bloco_r'>
				<label>Bairro</label>
				<input type='text' name='bairro' id='bairro' value='".$result['bai_nome']."' class='obg'>
			</div>

			<div class='bloco_lm'>
				<label>Estado</label>
				<select name='state_id' id='state_id'>
					<option value='".$result['uf_nome']."'> ".$result['uf_nome']." </option>";
					$sql_uf = " SELECT * FROM end_uf";
					$stmt_uf = $PDO->prepare($sql_uf);
					$stmt_uf->execute();
					while($result_uf = $stmt_uf->fetch())
					{
						echo "<option value='".$result_uf['uf_nome']."'> ".$result_uf['uf_nome']." </option>";
					}
					echo "</select>
			</div>

			<div class='bloco_rg'>
				<label>Cidade</label>
				<input type='text' name='city' id='city' value='".$result['mun_nome']."' class='obg'>
			</div>

			";
		}
		exit; 


	}

	if($pagina == 'carrega_endereco_fornecedor'){
		$cep = $_POST['cep'];

		$sql = " SELECT * FROM end_enderecos
		LEFT JOIN end_bairros ON end_bairros.bai_id = end_enderecos.end_bairro
		LEFT JOIN end_municipios ON end_municipios.mun_id = end_enderecos.end_municipio
		LEFT JOIN end_uf ON end_uf.uf_id = end_enderecos.end_uf
		WHERE end_cep = :end_cep";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':end_cep', $cep);
		$stmt->execute();
		while($result = $stmt->fetch())
		{
			echo "
			<p><label>Endereço:</label> <input type='text' name='address' id='address'  value='".$result['end_endereco']."'>
			<p><label>Número:</label> <input type='text' name='number' id='number'  placeholder='Número'>
			<p><label>Bairro:</label> <input type='text' name='bairro' id='bairro'  value='".$result['bai_nome']."'>
			<p><label>Complemento:</label> <input type='text' name='complement' id='complement'  placeholder='Complemento'>
			<p><label>Cidade:</label> <input type='text' name='city' id='city'  value='".$result['mun_nome']."'>
			<p><label>Estado:</label> <select id='state_id' name='state_id'>
			<option value='".$result['uf_id']."'> ".$result['uf_nome']." </option>";
			$sql_uf = " SELECT * FROM end_uf";
			$stmt_uf = $PDO->prepare($sql_uf);
			$stmt_uf->execute();
			while($result_uf = $stmt_uf->fetch())
			{
				echo "<option value='".$result_uf['uf_nome']."'> ".$result_uf['uf_nome']." </option>";
			}
			echo "</select>";
		}
		exit; 
	}

	if($pagina == 'carrega_cnpj'){
		$cnpj = $_POST['cnpj'];

		$sql="SELECT id, fa_cnpj, fa_fornecedor FROM fornecedores
		LEFT JOIN fornecedor_atributos ON fornecedor_atributos.fa_fornecedor = fornecedores.id
		WHERE fa_cnpj = :fa_cnpj";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':fa_cnpj', $cnpj);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if ($rows > 0) {
			$result = $stmt->fetch(); 

			echo $result['id'];
			exit; 
		}
		else {
			echo "false"; 
			exit; 

		}
	}

	if($pagina == 'carrega_cpf'){

		$cpf = $_POST['cpf'];

		$sql="SELECT id, fa_cnpj, fa_fornecedor FROM fornecedores
		LEFT JOIN fornecedor_atributos ON fornecedor_atributos.fa_fornecedor = fornecedores.id
		WHERE fa_cpf = :fa_cpf";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':fa_cpf', $cpf);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if ($rows > 0) {
			$result = $stmt->fetch(); 
			echo $result['id']; 
			exit; 

		}
		else {
			echo "false"; 
			exit; 

		}
	}

	if($pagina == 'carrega_fornecedor'){
		$busca = "%".$_POST['busca']."%";

		$sql="SELECT id, fa_cnpj, fa_cpf, field1, field2, ldc_id,  fa_fornecedor FROM fornecedores
		LEFT JOIN fornecedor_atributos ON fornecedor_atributos.fa_fornecedor = fornecedores.id
		LEFT JOIN licitacao_documentacao ON licitacao_documentacao.id_licitante = fornecedores.id
		WHERE fa_cnpj LIKE :fa_cnpj AND ldc_id <> :ldc_id 
		GROUP BY fa_cnpj";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':fa_cnpj', $busca);	
		$stmt->bindParam(':field1', $busca);
		$stmt->bindParam(':field2', $busca);
		$stmt->bindValue(':ldc_id', '');
		$stmt->execute();
		$rows = $stmt->rowCount();
		if ($rows > 0) {	
			while($result = $stmt->fetch()){
				echo "<tr id='".$result['id']."'>
						<td><h3><b>".$result['field1']."</b></h3><i>".$result['field2']."</i> - ".$result['fa_cnpj']." </td>
						<td><input type='checkbox' class='licitante' id='id_fornecedor[]' name='id_fornecedor[]' value='".$result['id']."'></td>
					</tr>				
				";
			}
			exit; 

		}
		else {
			echo "Nenhum registro encontrado."; 
			exit; 

		}

	}

	if($pagina == 'carrega_fornecedor2'){
		$id = $_POST['id'];
		$sql="SELECT id, fa_cnpj, fa_cpf, field1, field2, fa_fornecedor FROM fornecedores
		LEFT JOIN fornecedor_atributos ON fornecedor_atributos.fa_fornecedor = fornecedores.id
		WHERE id = :id";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if ($rows > 0) {	
			while($result = $stmt->fetch()){
				echo "<tr id='".$result['id']."'>
						<td><h3><b>".$result['field1']."</b></h3><i>".$result['field2']."</i> - ".$result['fa_cnpj']." </td>
						<td><input type='checkbox' class='rm_licitante' id='id_fornecedor[]' name='id_fornecedor[]' value='".$result['id']."' checked></td>
					</tr>				
				";
			}
		}
		exit; 

	}

	if($pagina =='cadastra_categoria'){
		$cn_nome = $_POST['categoria'];
		$cn_url = geradorTags($cn_nome);
		$sql = "INSERT INTO cadastro_categoria_noticias set
			cn_nome = :cn_nome, 
			cn_url = :cn_url";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':cn_nome',     $cn_nome);
		$stmt->bindParam(':cn_url',     $cn_url);
		if($stmt->execute()){
			$id = $PDO->lastInsertId();
			echo $id; 
			exit; 
		}
	}

	if($pagina =='cadastra_categoria_servicos'){
		$cat_nome = $_POST['categoria'];

		$sql1 = "SELECT * FROM cadastro_downloads_categorias WHERE cat_nome = :cat_nome";
		$stmt1 = $PDO->prepare($sql1);
		$stmt1->bindParam(':cat_nome',     $cat_nome);
		if($stmt1->execute()){
			echo 'false'; 
			exit; 
		}else {
			$cat_url = geradorTags($cat_nome);
			$sql = "INSERT INTO cadastro_downloads_categorias set
				cat_nome = :cat_nome, 
				cat_url = :cat_url";
			$stmt = $PDO->prepare($sql);
			$stmt->bindParam(':cat_nome',     $cat_nome);
			$stmt->bindParam(':cat_url',     $cat_url);
			if($stmt->execute()){
				$id = $PDO->lastInsertId();
				echo $id; 
				exit; 
			}	
		}
	}

	if($pagina =='cadastra_categoria_licitacao'){
		$lc_titulo = $_POST['categoria'];
		$lc_url = geradorTags($lc_titulo);
		$sql = "INSERT INTO licitacao_categorias set
			lc_titulo = :lc_titulo, 
			lc_url = :lc_url";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':lc_titulo',     $lc_titulo);
		$stmt->bindParam(':lc_url',     $lc_url);
		if($stmt->execute()){
			$id = $PDO->lastInsertId();
			echo $id; 
			exit; 
		}
	}

	if($pagina =='cadastro_ramo_atuacao'){
		$fra_descricao = $_POST['categoria'];
		$sql = "INSERT INTO fornecedores_ramo_atuacao set
			fra_descricao = :fra_descricao";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':fra_descricao',     $fra_descricao);
		if($stmt->execute()){
			$id = $PDO->lastInsertId();
			echo $id; 
			exit; 
		}
	}
	
	if($pagina == 'downloads'){

		$categoria = $_POST['categoria'];
		if ($categoria == ''){
			$query ='1=1';
		}

		else{
			$query ='cadastro_downloads_categorias.cat_id = :categoria';
		}

		$sql_dow = "SELECT * FROM cadastro_downloads
		LEFT JOIN cadastro_downloads_categorias ON cadastro_downloads_categorias.cat_id = cadastro_downloads.id_categoria 
		LEFT JOIN cadastro_downloads_documentos ON cadastro_downloads_documentos.doc_download = cadastro_downloads.id
		WHERE cadastro_downloads.arquivo <> '' AND $query
		ORDER BY cadastro_downloads.nome ASC ";
		$stmt_dow = $PDO->prepare($sql_dow);
		$stmt_dow->bindParam(':categoria',     $categoria);
		$stmt_dow->execute();
		$rows_dow = $stmt_dow->rowCount(); 
		if($rows_dow> 0)
		{						
			$i = 0; 
			$c = 10; 
			$p = 1; 
			while($result_down = $stmt_dow->fetch()){
				$i++; 
				if($i == $c){$p ++;$i= 0;}

				$nome 		= $result_down['nome']; 
				$categoria 	= $result_down['cat_nome']; 
				$data    = date("d/m/Y", strtotime( $result_down['data']));
				$descricao = $result_down['descricao']; 
				$id = $result_down['id']; 

				echo "<div class='bloco bloco$p' style='display:none'>

					<p><span class='subtitulo azul'><i class='fas fa-cloud-download-alt'></i>  $nome </span> </br>
					$categoria <br>
					<span class='data'><i class='fas fa-calendar-alt'></i> $data </span> </p>";
					
					$sqld= "SELECT * FROM cadastro_downloads_documentos
					WHERE doc_download = :doc_download";
					$stmtd = $PDO->prepare($sqld);
					$stmtd->bindValue(':doc_download', $id);
					$stmtd->execute();
					$rowsd = $stmtd->rowCount();    	
					if($rowsd> 0)
					{	
						while($resultd = $stmtd->fetch()){
							echo "<a href='webapp/uploads/downloads/".$resultd['doc_arquivo']."' title='".$resultd['doc_arquivo']."' target='_blank'>
								<div class='down'>
									<i class='fas fa-file-alt'></i>
								</div>
							</a>";
						}
					}
				echo"</div>"; 	

			}
			echo "<div class='paginacao'>";
			for($z=1; $z<=$p; $z++){
				echo"<button id='bloco$z' class='pg' value='bloco$z'> $z </button>";
			}
			echo"</div>
			<script>

				$(document).ready(function(){
					$('.bloco1').css('display','table'); 
				});
				$('button').click(function() {
					let valor = $(this).val(); 
					$('.conteudo .bloco').css('display','none'); 
					$('.'+valor).css('display','table'); 
					$('.pg').css('background','#2f91ce'); 
					$('.pg').css('color','#fff');
			
					$('#'+valor).css('color','#2f91ce'); 
					$('#'+valor).css('background','#fff'); 
				});
			
			
				$('#paginacao').change(function(){
					let valor = $(this).val();  
					$('.conteudo .bloco').css('display','none'); 
					$('.'+valor).css('display','table'); 
				})
			</script>
			";
			
		}	
		else {
			echo "<br><br><center>Não há nenhum item para a categoria selecionada no momento!</center>"; 
		}
	}
?>

<script>
    $(".licitante").change(function() {
        if(this.checked) {
			var valor = this.value; 
			$('tr#'+valor).remove();
			$.post("../carrega_conteudo.php",{pagina: 'carrega_fornecedor2', id:valor},
				function(dados){
					$('#forn').append(dados); 
				}
			)
		}
    });

	$(".rm_licitante").change(function() {
        if(this.checked) {
		}else {
			var valor = this.value; 
			$('tr#'+valor).remove();

		}
    });
</script>
