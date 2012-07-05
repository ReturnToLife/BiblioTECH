
<?php
if ($_SESSION['on_page'])
  {

if (isset($_SESSION['login']))
  {
    session_destroy();
    ?>
      <div class="ok">Vous &ecirc;tes maintenant d&eacute;connect&eacute;.</div>
	          <a href=".">Redirection vers la page d&#039;accueil...</a>
		 <script language="JavaScript">
	       var obj = 'window.location.replace(".");';
	       setTimeout(obj,1000); 
		</script> 
		 <?php

  }
else
  {
    ?>
    <div class="warning">Vous n&#039;&ecirc;tes pas connect&eacute; !</div>
    <?php
  }
  }
?>

