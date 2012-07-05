
<?php
include_once('conf.php');
include('includes/get_photo.php');
if ($_SESSION['on_page'])
  {
    if (isset($_SESSION['login']))
      {
	if (isset($_GET['id']))
	  {
	    $id_user = intval($_GET['id']);
	    $req_getname = $bdd->prepare('SELECT name FROM users WHERE id=?');
	    $req_getname->execute(array($id_user));
	    if (!($req_getname->rowCount()))
	      {
		?>
		<div class="warning">Cet utilisateur n&#039;existe pas.</div>
		<?php
	      }
	    else
	      {
		$tmp = $req_getname->fetch();
		$name = $tmp['name'];
		$req_getuser = $bdd->prepare('SELECT * FROM userinfo WHERE id_user=?');
		$req_getuser->execute(array($id_user));
		$user = $req_getuser->fetch();

		include('includes/available_promo.php');
		include('includes/available_city.php');
		?>
		  <div id="pbook">
		     <h1><?php echo $name; ?></h1>
		    <img src="<?php get_photo($name); ?>" alt="<?php echo $name; ?>"
		     class="user_img" /><br />
<ul class="bookinfoo">
<?php
 if ($name != "")
   {
?>
  <li>
    <span class="lab">Login :</span>
    <?php echo $name; ?>
  </li>
<? } ?>
<?php
 if ($user['pseudo'] != "")
   {
?>
  <li>
    <span class="lab">Pseudo :</span>
    <?php echo $user['pseudo']; ?>
  </li>
<? } ?>
<?php
 if ($user['fullname'] != "")
   {
?>
  <li>
    <span class="lab">Nom complet :</span>
    <?php echo $user['fullname']; ?>
  </li>
<? } ?>
<?php
 if ($user['promo'] != 0 && $user['promo'] != -1)
   {
?>
  <li>
    <span class="lab">Promo :</span>
    <?php echo $promos[$user['promo']]; ?>
  </li>
<? } ?>
<?php
    if ($user['city'] != 0 && $user['city'] != -1)
   {
?>
  <li>
    <span class="lab">Ville :</span>
    <?php echo $citys[$user['city']]; ?>
  </li>
<? } ?>
<?php
 if ($user['phone'] != "")
   {
?>
  <li>
      <span class="lab">T&eacute;l&eacute;phone :</span>
    <?php echo $user['phone']; ?>
  </li>
<? } ?>
  <li>
      <span class="lab">Rapport intra :</span>
    <a href="http://www.epitech.eu/intra/index.php?section=all&page=rapport&login=<?php echo $name; ?>" target="_blank">Voir son rapport sur l&#039;intra</a>
  </li>
<?php
 if ($user['descr'] != "")
   {
?>
<li>
  <span class="lab">Description :</span><br />
	    <?php echo min_lenght($user['descr'], 350); ?><br />
	<a href="#" onClick="show('resume_comp');return(false)" id="plus">Lire la description compl&egrave;te</a>
	    <div id="resume_comp"  style="display:none">
	      <?php echo $user['descr']; ?><br />
	    </div>
</li>
<? } ?>
</ul>

    <h3>Biblioth&egrave;que</h3>
<?php
        $req_stock = $bdd->prepare('SELECT * FROM stock WHERE id_user=?');
	$req_stock->execute(array($id_user));
	if (!$req_stock->rowCount())
	  {
	    ?>
	    <div class="info">Aucun livre repertori&eacute; pour le moment.</div>
	    <?php
	  }
	else
	  {
	    ?>
	    <div class="p">
	    <?php
	      include('includes/available_status.php');
	      $i = 0;
	    while ($stock = $req_stock->fetch())
	      {
		$req_stock_book = $bdd->prepare('SELECT * FROM books WHERE id=? LIMIT 1');
		$req_stock_book->execute(array(intval($stock['id_book'])));
		$book = $req_stock_book->fetch();		
		if ($i && !($i % 4))
		  {
		    ?>
		    </div>
		    <div class="p">
		    <?php
		  }
		?>
	    <a href="?page=book&id=<?php echo $book['id']; ?>">
	      <div class="book">
		  <?php if ($book['img'] == "") { ?>
		<img class="thumb"
		     src="images/books/nopic.png"
		     alt="<?php echo $book['name']; ?>" />
		  <?php } else { ?>
		<img class="thumb"
		     src="images/books/<?php echo $book['img']; ?>"
		     alt="<?php echo $book['name']; ?>" />
		    <?php } ?>
		  <div class="title"><?php echo $book['name']; ?></div>
		<strong>Cat&eacute;gorie :</strong> 
				     <?php echo getcategbyid($book['id_categ']); ?><br />
		<strong>Langue :</strong> 
    <?php echo $langs[$book['lang']]; ?>
    <img src="images/interface/flag/<?php echo $book['lang']; ?>.gif" />
	   <br />
	   <strong>Statut :</strong>
	   <?php
	   echo $status[$stock['status']];
?>
	      </div>
	    </a>

<?php
	   $i++;
	   }
	    ?> </div> <?php
		   }
?>
    <h3>Wish List</h3>
<?php
        $req_wishlist = $bdd->prepare('SELECT * FROM wishlist WHERE id_user=?');
	$req_wishlist->execute(array($id_user));
	if (!$req_wishlist->rowCount())
	  {
	    ?>
	    <div class="info">Aucun livre repertori&eacute; pour le moment.</div>
	    <?php
	  }
	else
	  {
	    ?>
	    <div class="smallp">
	    <?php
	      $i = 0;
	    while ($wishlist = $req_wishlist->fetch())
	      {
		$req_wishlist_book = $bdd->prepare('SELECT * FROM books WHERE id=? LIMIT 1');
		$req_wishlist_book->execute(array(intval($wishlist['id_book'])));
		$book = $req_wishlist_book->fetch();		
		if ($i && !($i % 5))
		  {
		    ?>
		    </div>
		    <div class="smallp">
		    <?php
		  }
		?>
	    <a href="?page=book&id=<?php echo $book['id']; ?>">
	      <div class="book_small">
		  <?php if ($book['img'] == "") { ?>
		<img class="thumb"
		     src="images/books/nopic.png"
		     alt="<?php echo $book['name']; ?>" />
		  <?php } else { ?>
		<img class="thumb"
		     src="images/books/<?php echo $book['img']; ?>"
		     alt="<?php echo $book['name']; ?>" />
		    <?php } ?>
		  <div class="title"><?php echo $book['name']; ?></div>
		<strong>Cat&eacute;gorie :</strong> 
				     <?php echo getcategbyid($book['id_categ']); ?><br />
		<strong>Langue :</strong> 
    <?php echo $langs[$book['lang']]; ?>
    <img src="images/interface/flag/<?php echo $book['lang']; ?>.gif" />
	      </div>
	    </a>

<?php
	   $i++;
	   }
	    ?> </div> <?php
		   }
?>
    <h3>.Plan</h3>

<?php
       if (!(file_exists('plan/'.$name)))
	 {
	   $connection = @ssh2_connect('ssh.epitech.eu', 22);
	   @ssh2_auth_password($connection, $epitech_login, $epitech_unix_pass);
	   if (!(@ssh2_scp_recv($connection, '/u/all/'.$name.'/public/.plan', 'plan/'.$name)))
	     {
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
		  </div>
		  <?php
		      }
?>

<?php
	  }
	else
	  {
	    ?>
	    <div class="warning">Aucun utilisateur specifi&eacute;.</div>
	    <?php
	  }
      }
    else
      {
	?>
	<div class="warning">Il faut &ecirc;tre connect&eacute; pour voir les fiches utilisateurs !</div>
	<?php
      }
    }
?>
