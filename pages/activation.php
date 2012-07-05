<?php
if ($_SESSION['on_page'])
  {
?>
<h3><img src="images/interface/activer-un-compte.png" alt="Activer un compte" /><br /><br /></h3>

<?php

if (!isset($_SESSION['login']))
  {
    if (isset($_POST['activate_submit']))
     {
       if (!isset($_POST['activate_login']) || $_POST['activate_login'] == "" ||
	   !isset($_POST['activate_code']) || $_POST['activate_code'] == "")
	 {
	   ?>
	   <div class="warning">Au moins un des champs obligatoires n&#039;est pas rempli.<br /><br /></div>
	   <?php
	 }
       else
	 {
	   $req_exist = $bdd->prepare('SELECT name FROM users WHERE name=? AND activation_code=?');
	   $req_exist->execute(array(protect($_POST['activate_login']),
				     protect($_POST['activate_code'])));
	   if (!$req_exist->rowCount())
	     {
	     ?>
	       <div class="warning">Le compte utilisateur <?php echo protect($_POST['activate_login']); ?> n&#039;existe pas ou le code d&#039;activation est invalide.</div>
	     <?php
	     }
	   else
	     {
	       $req_activate = $bdd->prepare('UPDATE users SET activated=true WHERE name=?');
	       if ($req_activate->execute(array(protect($_POST['activate_login']))))
		 ?>
	     <div class="ok">Le compte est bien activ&eacute;.
	   Vous pouvez maintenant vous connecter gr&acirc;ce au menu &agrave; droite.</div>
	       <?php
	     }
	 }
     }
	      

    ?>
        <form method="post">
	  <div class="form_elem">
	    <div class="iput">
	      <input type="text" maxlenght="12" name="activate_login" 
		     value="" />
	    </div>
	    <div class="title">
	      * Login
	    </div>
	  </div>

	  <div class="form_elem">
	    <div class="iput">
	      <input type="text" maxlenght="14" name="activate_code" 
		     value="" />
	    </div>
	    <div class="title">
       * Code d&#039;activation
	    </div>
	  </div>

	  <div class="right">
	    <input type="submit" value="Activer le compte"
		   name="activate_submit" class="submit" />
	  </div>
	</form>

    <?php
  }
else
  {
    ?>
    <div class="warning">Vous &ecirc;tes d&eacute;j&agrave; inscrit !</div>
    <?php
  }
  }
?>

