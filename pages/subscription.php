
<?php
if ($_SESSION['on_page'])
  {
?>
<h3><img src="images/interface/inscription.png" alt="Inscription" /><br /><br /></h3>

<?php

if (!isset($_SESSION['login']))
  {
    include('includes/sendmail.php');

   if (isset($_POST['subscription_submit']))
     {
       if (!isset($_POST['subscription_login']) || $_POST['subscription_login'] == "" ||
	   !isset($_POST['subscription_pass']) || $_POST['subscription_pass'] == "" ||
	   !isset($_POST['subscription_pass_verif']) || $_POST['subscription_pass_verif'] == "" ||
	   !isset($_POST['subscription_captcha']) || $_POST['subscription_captcha'] == "")
	 {
	   ?>
	   <div class="warning">Au moins un des champs obligatoires n&#039;est pas rempli.</div>
	   <?php
	 }
       else
	 {
	   if ($_POST['subscription_pass'] != $_POST['subscription_pass_verif'])
	     {
	       ?>
	       <div class="warning">Les deux mots de passe ne sont pas identiques.</div>
	       <?php
	     }
	   elseif (trim(strtolower($_POST['subscription_captcha'])) != $_SESSION['captcha'])
	   {
	       ?>
	       <div class="warning">La r&eacute;ponse &agrave; la question est incorrecte.</div>
	       <?php	     
	   }
	   elseif (!findtextinfile(protect($_POST['subscription_login']), 'includes/list.txt'))
	   {
	       ?>
	       <div class="warning">Le login n&#039;est pas dans la liste des inscrits &agrave; l'EPITECH.</div>
	       <?php	     
	   }
	   else
	     {
     	       $activation_code = randomstring(12);
     	       $req_newuser = $bdd->prepare('INSERT INTO users(name, password, activated, activation_code) VALUES(?, ?, false, ?)');
     	       if ($req_newuser->execute(array(protect($_POST['subscription_login']),
     					       md5($_POST['subscription_pass']),
     					       $activation_code)))
     		 {
		   subscriptionmail(protect($_POST['subscription_login']), $activation_code, $bdd);
		   $req_userinfo = $bdd->prepare('INSERT INTO userinfo(id_user, city, promo) VALUES(?, -1, -1)');
		   $req_userinfo->execute(array($bdd->lastInsertId()));		   
		   ?>
     <div class="ok">Vous &ecirc;tes maintenant inscrit.<br />
		     Pour activer votre compte, r&eacute;cuperez le code d&#039;activation envoy&eacute; &agrave; l'adresse <?php echo $_POST['subscription_login']; ?>@epitech.eu puis allez &agrave; cette page : <a href="?page=activation">lien</a>.</div>
		     <?php
     
							   }
	       else
		 {
		   ?>
		   <div class="warning">L&#039;utilisateur <?php echo protect($_POST['subscription_login']); ?> existe d&eacute;j&agrave;.</div>
		   <?php
		 }
	     
	     }
	 }
     
     }     


   $req_getcaptcha = $bdd->prepare('SELECT * FROM captcha ORDER BY rand() LIMIT 1');
   $req_getcaptcha->execute(array());
   $captcha = $req_getcaptcha->fetch();
   $_SESSION['captcha'] = $captcha['answer'];


?>
    <form method="post">

      <div class="form_elem">
	<div class="iput">
	  <input type="text" maxlenght="12" name="subscription_login" 
		 value="" />
	</div>
	<div class="title">
	  * Login
	</div>
      </div>

      <div class="info">Un code d'activation sera envoy&eacute; &agrave; l'adresse e-mail login@epitech.eu !</div>
      <br />

      <div class="form_elem">
	<div class="iput">
	  <input type="password" maxlenght="255" name="subscription_pass" 
		 value="" />
	</div>
	<div class="title">
	  * Mot de passe
	</div>
      </div>

      <div class="form_elem">
	<div class="iput">
	  <input type="password" maxlenght="255" name="subscription_pass_verif" 
		 value="" />
	</div>
	<div class="title">
	  * V&eacute;rification mot de passe
	</div>
      </div>


      <div class="form_elem">
	<div class="iput">
	  <input type="text" maxlenght="255" name="subscription_captcha" 
		 value="" />
	</div>
	<div class="title">
   * <?php echo $captcha['question'];?>
	</div>
      </div>

      <div class="right">
	<input type="submit" value="S'inscrire" name="subscription_submit" class="submit" />
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




