<?php
include_once('conf.php');

if ($_SESSION['on_page'])
  {
    if ($_SESSION['login'])
      {
	?>
	<div id="account">
	  <div class="account_title"> : <?php echo $_SESSION['login']; ?></div>

<div class="right">
<a href="?page=user&id=<?php echo $_SESSION['id_user']; ?>">Voir mon profil</a>
</div>

<h3>Mon profil</h3>

 <?php
    include('includes/available_promo.php');
    include('includes/available_city.php');
	if (isset($_POST['user_submit']))
	  {
	    $_POST['user_pseudo'] = protect($_POST['user_pseudo']);
	    $_POST['user_fullname'] = protect($_POST['user_fullname']);
	    $_POST['user_phone'] = protect($_POST['user_phone']);
	    $_POST['user_promo'] = intval($_POST['user_promo']);
	    $_POST['user_city'] = intval($_POST['user_city']);
	    $_POST['user_descr'] = protect($_POST['user_descr']);

	    $err = 0;
	    if (!isset($_POST['user_promo']) ||
		!($_POST['user_promo'] >= 0 && $_POST['user_promo'] <= $total_promo))
	      {?>
		<div class="warning">La promo est invalide.</div>
		  <?php $err=1;
	      }

	    if (!isset($_POST['user_city']) ||
		!($_POST['user_city'] >= 0 && $_POST['user_city'] <= $total_city))
	      {?>
		<div class="warning">La ville est invalide.</div>
		  <?php $err=1;
	      }

	    if ($err == 0)
	      {
	    $req_update = $bdd->prepare('UPDATE userinfo SET pseudo=?, fullname=?, phone=?, promo=?, city=?, descr=? WHERE id_user=?');
	    if ($req_update->execute(array($_POST['user_pseudo'],
					   $_POST['user_fullname'],
					   $_POST['user_phone'],
					   $_POST['user_promo'],
					   $_POST['user_city'],
					   str_replace("\n", '<br />', $_POST['user_descr']),
					   $_SESSION['id_user'])))
	      {
	    ?>
		<div class="ok">Les informations ont bien &eacute;t&eacute; modifi&eacute;e.</div>
	      <?php
		  }
	      }
	  }
	$req_getuserinfo = $bdd->prepare('SELECT * FROM userinfo WHERE id_user=?');
	$req_getuserinfo->execute(array($_SESSION['id_user']));
	$userinfo = $req_getuserinfo->fetch();
?>
  <form method="post">
    <div class="form_elem">
      <div class="iput">
	<input type="text" maxlenght="255" name="user_pseudo" 
	       value="<?php if (isset($_POST['user_submit']) && isset($_POST['user_pseudo'])) { echo $_POST['user_pseudo']; } else { echo $userinfo['pseudo']; } ?>" />
      </div>
      <div class="title">
	Pseudo
      </div>
    </div>

    <div class="form_elem">
      <div class="iput">
	<input type="text" maxlenght="255" name="user_fullname" 
	       value="<?php if (isset($_POST['user_submit']) && isset($_POST['user_fullname'])) { echo $_POST['user_fullname']; } else { echo $userinfo['fullname']; } ?>" />
      </div>
      <div class="title">
	Nom complet
      </div>
    </div>

    <div class="form_elem">
      <div class="iput">
	<select name="user_promo">
		  <?php
		  for ($i = 0 ; $i <= $total_promo ; $i++)
		    {
		      if (isset($_POST['user_submit']) && isset($_POST['user_promo']) && $i == intval($_POST['user_promo']))
			echo '<option selected value="'.$i.'">'.$promos[$i].'</option>'."\n";
		      else if ($i == $userinfo['promo'])
			echo '<option selected value="'.$i.'">'.$promos[$i].'</option>'."\n";
		      else
			echo '<option value="'.$i.'">'.$promos[$i].'</option>'."\n";
		    }
		  ?>
	</select>
      </div>
      <div class="select">
	Promo
      </div>
    </div>


    <div class="form_elem">
      <div class="iput">
	<select name="user_city">
		  <?php
		  for ($i = 0 ; $i <= $total_city ; $i++)
		    {
		      if (isset($_POST['user_submit']) && isset($_POST['user_city']) && $i == intval($_POST['user_city']))
			echo '<option selected value="'.$i.'">'.$citys[$i].'</option>'."\n";
		      else if ($i == $userinfo['city'])
			echo '<option selected value="'.$i.'">'.$citys[$i].'</option>'."\n";
		      else
			echo '<option value="'.$i.'">'.$citys[$i].'</option>'."\n";
		    }
		  ?>
	</select>
      </div>
      <div class="select">
	Ville
      </div>
    </div>



    <div class="form_elem">
      <div class="iput">
	<input type="text" maxlenght="255" name="user_phone" 
	       value="<?php if (isset($_POST['user_submit']) && isset($_POST['user_phone'])) { echo $_POST['user_phone']; } else { echo $userinfo['phone']; } ?>" />
      </div>
      <div class="title">
     T&eacute;l&eacute;phone
      </div>
    </div>

    <div class="form_elem">
      <div class="iput">
		  <textarea name="user_descr"><?php
		  if (isset($_POST['user_submit']) && isset($_POST['user_descr']))
		    {
		      echo $_POST['user_descr'];
		    }
		  else
		    {
		      echo str_replace('<br />', "\n", $userinfo['descr']);
		    } ?></textarea>
      </div>
      <div class="text">
	Description
      </div>
    </div>

         <div class="right">
      <input type="submit" value="Modifier mes informations" name="user_submit" class="submit" />
    </div>
</form>

<h3>Mon mot de passe</h3>

<?php
    $req_getuser = $bdd->prepare('SELECT * FROM users WHERE id=?');
	$req_getuser->execute(array($_SESSION['id_user']));
	$user = $req_getuser->fetch();
	$name = $user['name'];

	$err = 0;
	if (isset($_POST['pass_submit']))
	  {
	    if ((!isset($_POST['pass_old'])) || ($_POST['pass_old'] == "") || ($_POST['pass_old'] == 'password') ||
		(!isset($_POST['pass_new'])) || ($_POST['pass_new'] == "") || ($_POST['pass_new'] == 'password') ||
		(!isset($_POST['pass_new2'])) || ($_POST['pass_new2'] == "") || ($_POST['pass_new2'] == 'password'))
	      {
	  ?>
	  <div class="warning">Au moins un des champs obligatoires n&#039;est pas rempli.</div>
	     <?php $err = 1;
	      }
	if ($err == 0)
	  {
	    if (md5($_POST['pass_old']) == $user['password'])
	      {
		if ($_POST['pass_new'] == $_POST['pass_new2'])
		  {
		    $req_changepass = $bdd->prepare('UPDATE users SET password=? WHERE id=?');
		    $req_changepass->execute(array(md5($_POST['pass_new']),
						   $_SESSION['id_user']));
		    ?>
		      <div class="ok">Le mot de passe a bien &eacute;t&eacute; modifi&eacute;.</div>
		      <?php
		  }
		else
		  {
	  ?>
		    <div class="warning">Les deux mots de passes sont diff&eacute;rents.</div>
	     <?php $err = 1;
		  }
	      }
	    else
	      {
	  ?>
	  <div class="warning">L&#039;ancien mot de passe est incorrect.</div>
	     <?php $err = 1;
	      }
	  }
	  }
?>
<form method="post">
    <div class="form_elem">
   <div class="iput">
   <input type="password" value="password" name="pass_old"
   onfocus="if(this.value=='password') this.value='';"
   onblur="if(this.value=='') this.value='password';"/>
      </div>
      <div class="title">
     * Ancien mot de passe
      </div>
    </div>
    <div class="form_elem">
   <div class="iput">
   <input type="password" value="password" name="pass_new"
   onfocus="if(this.value=='password') this.value='';"
   onblur="if(this.value=='') this.value='password';"/>
      </div>
      <div class="title">
     * Nouveau mot de passe
      </div>
    </div>
    <div class="form_elem">
   <div class="iput">
   <input type="password" value="password" name="pass_new2"
   onfocus="if(this.value=='password') this.value='';"
   onblur="if(this.value=='') this.value='password';"/>
      </div>
      <div class="title">
   * V&eacute;rification nouveau mot de passe
      </div>
    </div>
         <div class="right">
      <input type="submit" value="Modifier mon mot de passe" name="pass_submit" class="submit" />
    </div>
</form>

<h3>Mon .plan</h3>
<?php
   if (!(file_exists('plan/'.$name)) || isset($_POST['reloadplan']))
	 {
	   if (!($connection = ssh2_connect('ssh.epitech.eu', 22)))
	     echo '<div class="warning">La connexion avec EPITECH a &eacute;chou&eacute;e.</div>';
	   if (!(@ssh2_auth_password($connection, $epitech_login, $epitech_unix_pass)))
	     echo '<div class="warning">L&#039;authentification avec EPITECH a &eacute;chou&eacute;e.</div>';
	   if (!(@ssh2_scp_recv($connection, '/u/all/'.$name.'/public/.plan', 'plan/'.$name)))
	     {
	       echo '<div class="warning">Le fichier .plan n&#039;a pas &eacute;t&eacute; trouv&eacute;.</div>';
	       $fp = fopen('plan/'.$name, "a+");
	       fclose($fp);
	     }
	 }
	echo '<pre>';
	$fichier = @file('plan/'.$name);
	$total = count($fichier);
	for($i = 0; $i < $total; $i++) 
	  {
	    echo protect($fichier[$i]);
	  }
	echo '</pre>';

?>
<div class="right">
	<form method="post">
	   <input type="submit" class="submit" name="reloadplan" value="Mettre &agrave; jour mon .plan" />
	   </form>
</div>
<div class="info">Le .plan est le fichier qui se trouve dans le public de votre compte Epitech.</div>

</div>
<?php
      }
    else
      {
	?>
	<div class="warning">Il faut &ecirc;tre connect&eacute; pour acc&eacute;der &agrave; cette page !</div>
	<?php
       include('pages/login.php');
      }
  }
?>
