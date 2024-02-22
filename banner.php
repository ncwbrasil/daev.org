<link href="http://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="imgs/style.css" />

<div class="g_slide" id="slides1">
  <div class="switch_main bnn">
      <?php
        $sql = "SELECT * FROM cadastro_banner WHERE bn_status = :bn_status
        ORDER BY bn_posicao ASC";
        $stmt = $PDO->prepare($sql);
        $stmt->bindValue(':bn_status', 1);
        $stmt->execute();
        $rows = $stmt->rowCount();
        if ($rows > 0)
        {
          while( $result = $stmt->fetch()){
            echo "
              <a class='item switch_item' href='".$result['bn_url']."' target='_parent' alt='".$result['bn_titulo']."'>
                <div style='background:url(webapp/".$result['bn_imagem'].") center center no-repeat; width:100%;height: 30vw;background-size:100%;'>
                </div>
              </a>
            
            ";
          }
          
        }
      ?>
  </div>
</div>

<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="core/mod_includes/js/switchable.js"></script>

<script>
  $(function() {
    switchable({
      $element: $('#slides1'),
      interval: 5000,
      effect: 'slide'
    });
  });
</script>