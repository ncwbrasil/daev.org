<div id="foto_perfil" class="dialog" title="Alterar Foto Perfil">
	<div class="perfil">
        <div id='img-padrao'>
        Imagem atual:<br />
        <?php 
        if($usu_foto != ''){echo "<img src='$usu_foto' border='0' width='250' />";}else{echo "<img src='../imagens/perfil.png' border='0' width='250' />";}
        ?>
        </div>
        <div id='img_perfil'> 
            Nova Imagem:<br />
            <center><img id='jcrop_perfil'/></center>
        </div>
        <div id='botoes'>
        	<?php
			if(end(explode("/", $_SERVER['PHP_SELF'])) == 'meu_perfil.php')
			{
				?>
                <form name='form_foto_perfil' id='form_foto_perfil' enctype='multipart/form-data' method='post' action='meu_perfil.php?pagina=altera_foto_perfil&usu_id=<?php echo $usu_id;?><?php echo $autenticacao;?>'>
        		<?php
			}
			elseif(end(explode("/", $_SERVER['PHP_SELF'])) == 'admin_usuarios.php')
			{
				?>
            	<form name='form_foto_perfil' id='form_foto_perfil' enctype='multipart/form-data' method='post' action='admin_usuarios.php?pagina=altera_foto_perfil&usu_id=<?php echo $usu_id;?><?php echo $autenticacao;?>'>
        		<?php
			}
				?>
                <input type='hidden' id='usu_id' name='usu_id' value='<?php echo $usu_id;?>' />
                
                <input type='hidden' id='x1_1' name='x1_1' />
                <input type='hidden' id='y1_1' name='y1_1' />
                <input type='hidden' id='x2_1' name='x2_1' />
                <input type='hidden' id='y2_1' name='y2_1' />
                <input type='file' name='usu_foto' id='usu_foto' onchange='fileSelectHandler_1()' /> Alterar Foto
                    <input type='hidden' id='filesize_1' name='filesize_1' />
                    <input type='hidden' id='filetype_1' name='filetype_1' />
                    <input type='hidden' id='filedim_1' name='filedim_1' />
                    <input type='hidden' id='w_1' name='w_1' />
                    <input type='hidden' id='h_1' name='h_1' />
               	<br />
                <div id='usu_foto_erro' ></div>
                <div id='usu_foto_erro2' ></div>
                <br />
                <br />
                <input type='button' id='bt_foto_perfil' value=' Salvar ' />&nbsp;&nbsp;&nbsp;&nbsp;
                <input type='button' class='close_janela_foto' value=' Cancelar'>
            </form>
        </div>
    </div>
</div>