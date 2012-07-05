
<?php
if ($_SESSION['on_page'])
  {

function showformlogin()
{
       ?>
  <br />
       <form method="post">
	 <div class="form_elem">
	   <div class="iput">
	     <input type="text" value="Login" name="login_login"
		    onfocus="if(this.value=='Login') this.value='';"
		    onblur="if(this.value=='') this.value='Login';"/>
	   </div>
	   <div class="title">
	     * Login
	   </div>
	 </div>
	 <div class="form_elem">
	   <div class="iput">
	     <input type="password" value="password" name="login_password"
		    onfocus="if(this.value=='password') this.value='';"
		    onblur="if(this.value=='') this.value='password';"/>
	   </div>
	   <div class="title">
	     * Mot de Passe
	   </div>
 	 </div>
	 <div class="right">
	   <input class="submit"
		  type="submit" value="Se connecter" name="login_submit" />
	 </div>
	 </form>
    <div class="info">Pas encore de compte ? <a href="?page=subscription">S&#039;inscrire</a>.</div>
	 <?php
}

if (!isset($_SESSION['login']))
  {
   if (isset($_POST['login_submit']))
     {
       if (!isset($_POST['login_login']) || $_POST['login_login'] == "" ||
	   !isset($_POST['login_password']) || $_POST['login_password'] == "" ||
	   $_POST['login_login'] == 'Login' || $_POST['login_password'] == 'password')
	 {
	   ?>
	   <div class="warning">Au moins un des champs obligatoires n&#039;est pas rempli.</div>
	     <?php
	     showformlogin();
	 }
       else
	 {
	   $hash_pass = md5($_POST['login_password']);
	   $req_get_user = $bdd->prepare('SELECT * FROM users WHERE name=? AND password=?');
	   $req_get_user->execute(array(protect($_POST['login_login']),
					$hash_pass));
	   if (!$req_get_user->rowCount())
	     {
	       ?>
	       <div class="warning">Mauvais login et/ou mot de passe.
		 <a href="?page=subscription">S&#039;inscrire ?</a></div>
		 <?php
		 showformlogin();	     
	     }
	   else
	     {
	       $userinfo = $req_get_user->fetch();
	       if ($userinfo['activated'])
		 {
		   $_SESSION['login'] = $userinfo['name'];
		   $_SESSION['id_user'] = $userinfo['id'];
		   $_SESSION['adm'] = $userinfo['adm'];
		   ?>
		     <div class="ok">Vous &ecirc;tes maintenant connect&eacute;.</div>
										    <a href=".">Redirection...</a>
										    <script language="JavaScript">
	       var obj = 'window.location.replace("?page=account");';
		   setTimeout(obj,0); 
		   </script> 
		       <?php
		       }
	       else
		 {
	       ?>
	       <div class="warning">Le compte n&#039;est pas activ&eacute;.<br />
		     Pour l&#039;activer, utilisez le code d&#039;activation envoy&eacute; &agrave; votre adresse mail Epitech.
		     </div>

		 <?php		   
		 }
	     }

	 }
     }
   else
     {
       showformlogin();
     }
  }
else
  {
    ?>
    <div class="warning">Vous &ecirc;tes d&eacute;j&agrave; connect&eacute; !</div>
    <?php
  }
  }
?>
