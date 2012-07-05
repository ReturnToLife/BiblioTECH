
	<div id="subheader">
	  <form action="?page=search" method="post">
	    <input id="search_input" type="text" name="search_text" />
	    <select id="select_input" name="search_categ">
	      <option value="-1">Toutes cat&eacute;gories</option>
	      <?php include('includes/available_categ.php'); ?>
	    </select>
	    <select id="select_input" name="search_lang">
	    <option value="-1">Toutes les langues</option>
	    <?php include('includes/available_lang.php'); ?>
	    </select>
	    <input type="submit" id="search_img" name="search_submit" value="" /> 
	    </form>
	</div>

	<div id="menu">
	  <ul>
	    <li><a href=".">Accueil</a></li>
	    <li><a href="?page=faq">F.A.Q.</a></li>
	    <li><a href="?page=thanks">Remerciements</a></li>
</ul>
<ul>
     <li><h4>Livres disponibles :</h4></li>

	    <li><a href="?page=allbooks">Tous</a></li>
<?php
for ($i = 0 ; $i <= $total_categ ; $i++)
{
    echo '<li><a href="?page=search&categ='.$categories[$i]['id'].'">'.$categories[$i]['categorie'].'</a></li>'."\n";
}

?>
	  </ul>
	  <?php
	     if (isset($_SESSION['login']))
	     {
	      ?>
		<ul>
	      <li><h4><?php echo $_SESSION['login']; ?></h4></li>
	    <li><a href="?page=newbook">Ajouter un livre</a></li>
	    <li><a href="?page=newpdf">Ajouter un PDF</a></li>
	    <li><a href="?page=account">Mon Compte</a></li>
	      <li><a href="?page=account#addbooks">Mes livres ajout&eacute;s</a></li>
	      <li><a href="?page=account#bibliotheque">Ma Biblioth&egrave;que</a></li>
	      <li><a href="?page=account#wishlist">Ma Wish List</a></li>
	      <li><a href="?page=account#comments">Mes commentaires</a></li>
	      <li><a href="?page=user&id=<?php echo $_SESSION['id_user']; ?>">Mon profil</a></li>
	      <li><a href="?page=accountparam">Param&egrave;tres du profil</a></li>
	      <li><a href="?page=accountparam">Param&egrave;tres du compte</a></li>
	      <?php
	      if ($_SESSION['adm'])
		{
		  ?>
		  <li><a href="?page=admin">Administration</a></li>
		  <?php
		}
	      ?>
		  <li><a href="?page=logout">D&eacute;connexion</a></li>
		</ul>
	      <?php
	     }
	     else
	     {
		 ?>
	           <form method="post" action="?page=login">
		     <input type="text" value="Login" name="login_login"
			    onfocus="if(this.value=='Login') this.value='';"
			    onblur="if(this.value=='') this.value='Login';"/><br />
		     <input type="password" value="password" name="login_password"
			    onfocus="if(this.value=='password') this.value='';"
			    onblur="if(this.value=='') this.value='password';"/><br />
		 <a href="?page=subscription">S&#039;inscrire</a>
		     <input class="submit"
			    type="submit" value="Se connecter" name="login_submit" />
		   </form>
	         <?php
             }
	     
	     ?>
	</div>
	<div id="page">
	  
