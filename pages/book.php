<?php
include('includes/get_photo.php');
if ($_SESSION['on_page'])
  {
?>
<div id="pbook">

<?php

function no_such_book()
{
    ?>
  <div class="warning">Ce livre n&#039;existe pas ou plus.</div>
    <?php
}

if (isset($_GET['id']))
  {
    if (isset($_SESSION['login']) && isset($_POST['vote']))
      {
	$vote = intval($_POST['vote']);
	$req_addvote = $bdd->prepare('SELECT mark, count FROM books WHERE id=?');
	$req_addvote->execute(array(intval($_GET['id'])));
	if ($votet = $req_addvote->fetch())
	  {
	    $mark = $votet['mark'];
	    $count = intval($votet['count']);
	    if ($mark == -1)
	      {
		$count++;
		$mark = $vote;
	      }
	    else
	      {
		$mark = $mark * $count;
		$mark += $vote;
		$count++;
		$mark = $mark / $count;
	      }
	    $req_updatevote = $bdd->prepare('UPDATE books SET mark=?, count=? WHERE id=?');
	    $req_updatevote->execute(array($mark, $count, intval($_GET['id'])));
	  }
      }

    include('includes/available_status.php');
    $req_book = $bdd->prepare('SELECT * FROM books WHERE id=? LIMIT 1');
    $req_book->execute(array(intval($_GET['id'])));
    if ($book = $req_book->fetch())
      {
?>
    <h1><?php
	  if ((isset($_SESSION['login']) && $book['id_user'] == $_SESSION['id_user']) ||
	      (isset($_SESSION['adm']) && $_SESSION['adm'] == 1))
	    {
	    ?>
	      <div style="float: right; font-size: 11px; color: #383838;"><a href="?page=editbook&id=<?php echo $book['id']; ?>">Modifier le livre</a></div>
<?
	    }
?><?php echo $book['name']; ?></h1>

<div class="center">
<?php
	if ($book['img'] != "")
	  {
	?>    
    <img src="images/books/<?php echo $book['img']; ?>" class="book_img" />
	      <?php
	      }
	else
	  {
	?>
	    <img src="images/books/nopic.png" class="book_img" />
	    <?php
	      }
	      ?>
</div>
<ul class="bookinfoo">
  <li>
    <span class="lab">Titre :</span>
    <?php echo $book['name']; ?>
  </li>

  <li>
    <span class="lab">Auteur :</span> 
      <?php if ($book['author'] != "") { echo $book['author']; } else { echo 'Inconnu'; } ?><br />
  <li>

  <li>
    <span class="lab">Cat&eacute;gorie :</span>
    <?php echo getcategbyid($book['id_categ']); ?>
  </li>

  <li>
    <span class="lab">Nombre de pages :</span>
      <?php
      if ($book['pages'] == 0)
	{
	  ?>
	  Inconnu.
	  <?php
	}
	else
	  {
	    echo $book['pages'];
	  }
?>
  </li>
  
      <?php if ($book['pdf_only'] == 0) { ?>
  <li>
    <span class="lab">ISBN-10 :</span>
      <?php
      if ($book['isbn'] == "")
	{
	  echo 'Inconnu';
	}
      else
	{
	  echo $book['isbn']; 
	}
	?>
  </li>
      <?php } ?>

  <li>  
    <span class="lab">Langue :</span> 
    <?php echo $langs[$book['lang']]; ?>
    <img src="images/interface/flag/<?php echo $book['lang']; ?>.gif" />
  </li>  
    
    <?php
       if (isset($_SESSION['login']))
	 {
	   ?>
  <li>
    <span class="lab">Version PDF :</span> 
    <img src="images/interface/icons/pdf.gif" /> 
	   <?php
       if ($book['pdf'] == "")
       {
       ?>
    La version PDF de ce livre n&#039;est pas disponible.
    <?php
       }
       else
       {
       ?>
    <a href="pdf/<?php echo $book['pdf']; ?>" target="_blank" alt="pdf book" />
       T&eacute;l&eacute;charger la version PDF (<?php echo ByteSize(filesize('pdf/'.$book['pdf'])); ?>)</a>

<?php
       } 
?>
</li>
<?php
	 }
	if ($book['pdf_only'] == 0)
	  {
?>
<li>
  <span class="lab">Lien d&#039;Achat :</span> 
  <img src="images/interface/icons/buy.gif" /> 
  <?php
     if ($book['buylink'] == "")
     {
     ?>
  Aucun lien d&#039;achat disponible.
  <?php
     }
     else
     {
     ?>
  <a href="<?php echo $book['buylink']; ?>" target="_blank" alt="buy book" />
  Acheter ce livre.</a>

<?php } ?>
</li>
    <?php } ?>
  <span class="lab">Note :</span> 
    <?php
    if ($book['mark'] == -1)
      {
	?>
	Aucun vote pour le moment.
	<?php
      }
	else
	  {
	    showmark($book['mark']);
	    ?>
	    (<?php echo $book['mark']; ?>/5)
	      <?php
	  }
    ?>
</li>

    <?php if (isset($_SESSION['login']))
    {
?>
<li>
	 <form method="post">
  <span class="lab">Voter :</span> 
	 <select name="vote">
	 <?php for ($i = 1 ; $i <= 5 ; $i++)
	 { ?>
	   <option name="<?php echo $i; ?>"><?php echo $i; ?></option>
	   <?php } ?>
	 </select>
	 <input type="submit" class="submit" value="Voter" />
	 </form>
</li>
	     <?php } ?>
<li>
  <span class="lab">R&eacute;sum&eacute; :</span><br />
<?php
	if ($book['info'] == "")
	  {
?>
	    Le r&eacute;sum&eacute; n&#039;est pas disponible.
<?php
	  }
	else
	  {
?>
	    <?php echo min_lenght($book['info'], 350); ?><br />
							    <a href="#" onClick="show('resume_comp');return(false)" id="plus">Lire le R&eacute;sum&eacute; complet</a>
	    <div id="resume_comp"  style="display:none">
	      <?php echo $book['info']; ?><br />
	    </div>

    <?php } ?>
</li>
</ul>

<?php

    if ($book['pdf_only'] == 0 && isset($_SESSION['login']))
      {
?>
<h3>Ils l&#039;ont dans leurs biblioth&egrave;que</h3>

<?php
	$req_checkstock = $bdd->prepare('SELECT * FROM stock WHERE id_book=? AND id_user=?');
	$req_checkstock->execute(array($book['id'], $_SESSION['id_user']));
	if (!$req_checkstock->rowCount())
	  {
	    if (isset($_POST['addstock']) && isset($_POST['stockstatus']))
	  {
	    $req_addstock = $bdd->prepare('INSERT INTO stock(id_book, id_user, status) VALUES(?, ?, ?)');
	    if ($req_addstock->execute(array(intval($book['id']),
					     $_SESSION['id_user'],
					     intval($_POST['stockstatus']))))
	      {
		?>
		<div class="ok">Le livre a bien &eacute;t&eacute; ajout&eacute; &agrave; la collection.</div><br />
		<?php
	      }
	    ?>
<div class="right">
	  <form method="post">
	   <input type="hidden" name="delstock_id" value="<?php echo $book['id']; ?>" />
	<input class="submit"
	   type="submit"
	   value="Retirer ce livre de ma collection"
	   name="delstock" />
</form>    
</div>
		   <?
	    
	  }
	else
	  {
?>
<div class="right">
	  <form method="post">
        <select name="stockstatus">
<?php
for ($i = 0 ; $i <= $total_status ; $i++)
{
  echo '<option value="'.$i.'">'.$status[$i].'</option>'."\n";
}
?>
	      </select><br />
	<input class="submit"
	   type="submit"
	   value="Ajouter ce livre &agrave; ma collection"
	   name="addstock" />
</form>    
</div>
   <?php 
	      }
	  }
	else
	  {
	     if (isset($_POST['delstock']) && isset($_POST['delstock_id']))
		{
		  $req_remove_stock = $bdd->prepare('DELETE FROM stock WHERE id_user=? AND id_book=?');
		  $req_remove_stock->execute(array($_SESSION['id_user'],
						   intval($_POST['delstock_id'])));
?>
<div class="right">
	  <form method="post">
        <select name="stockstatus">
<?php
for ($i = 0 ; $i <= $total_status ; $i++)
{
  echo '<option value="'.$i.'">'.$status[$i].'</option>'."\n";
}
?>
	      </select><br />
	<input class="submit"
	   type="submit"
	   value="Ajouter ce livre &agrave; ma collection"
	   name="addstock" />
</form>    
</div>
   <?php 
		}
	     else
	       {
	    ?>
<div class="right">
	  <form method="post">
	   <input type="hidden" name="delstock_id" value="<?php echo $book['id']; ?>" />
	<input class="submit"
	   type="submit"
	   value="Retirer ce livre de ma collection"
	   name="delstock" />
</form>    
</div>
		   <? }
	  } ?>
<?php
    $req_stock = $bdd->prepare('SELECT * FROM stock WHERE id_book=? LIMIT 7');
	$req_stock->execute(array($book['id']));
	if (!$req_stock->rowCount())
	  {
	    ?>
	    <div class="info">Personne ne poss&egrave;de ce livre pour le moment.</div>
	    <?php
							}
	else
	  {
?>
	    <div class="userlist">
<?php
	    while ($stock = $req_stock->fetch())
	      {
		$req_user = $bdd->prepare('SELECT name FROM users WHERE id=?');
		$req_user->execute(array($stock['id_user']));
		$user = $req_user->fetch();
		?>
		  <a href="?page=user&id=<?php echo $stock['id_user']; ?>">
		  <div class="userstock">
		    <img src="<?php get_photo($user['name']); ?>" alt="<?php echo $user['name']; ?>"
		     class="photointra" /><br />
		<?php echo $user['name']; ?><br /><br />
		<?php echo $status[$stock['status']]; ?>
		     </div>
		  </a>
		  <?php
	      }
		?>
	      </div>
	      <?php
	  }
?>
<h3>Ils l&#039;ont dans leurs Wish List</h3>

<?php
	$req_checkwishlist = $bdd->prepare('SELECT * FROM wishlist WHERE id_book=? AND id_user=?');
	$req_checkwishlist->execute(array($book['id'], $_SESSION['id_user']));
	if (!$req_checkwishlist->rowCount())
	  {
	if (isset($_POST['addwishlist']))
	  {
	    $req_addwishlist = $bdd->prepare('INSERT INTO wishlist(id_book, id_user) VALUES(?, ?)');
	    if ($req_addwishlist->execute(array(intval($book['id']),
					     $_SESSION['id_user'])))
	      {
		?>
		<div class="ok">Le livre a bien &eacute;t&eacute; ajout&eacute; &agrave; la Wish List.</div><br />
		<?php
	      }
	    ?>
<div class="right">
	  <form method="post">
	   <input type="hidden" name="delwishlist_id" value="<?php echo $book['id']; ?>" />
	<input class="submit"
	   type="submit"
	   value="Retirer ce livre de ma Wish List"
	   name="delwishlist" />
</form>    
</div>
		   <?
	    
	  }
	else
	  {
?>
<div class="right">
	  <form method="post">
	<input class="submit"
	   type="submit"
	   value="Ajouter ce livre &agrave; ma Wish List"
	   name="addwishlist" />
</form>    
</div>
   <?php 
	      }
	  }
	else
	  {
	     if (isset($_POST['delwishlist']) && isset($_POST['delwishlist_id']))
		{
		  $req_remove_wishlist = $bdd->prepare('DELETE FROM wishlist WHERE id_user=? AND id_book=?');
		  $req_remove_wishlist->execute(array($_SESSION['id_user'],
						   intval($_POST['delwishlist_id'])));
?>
<div class="right">
	  <form method="post">
	<input class="submit"
	   type="submit"
	   value="Ajouter ce livre &agrave; ma Wish List"
	   name="addwishlist" />
</form>    
</div>
   <?php 
		}
	     else
	       {
	    ?>
<div class="right">
	  <form method="post">
	   <input type="hidden" name="delwishlist_id" value="<?php echo $book['id']; ?>" />
	<input class="submit"
	   type="submit"
	   value="Retirer ce livre de ma Wish List"
	   name="delwishlist" />
</form>    
</div>
		   <? }
	  } ?>
<?php
    $req_wishlist = $bdd->prepare('SELECT * FROM wishlist WHERE id_book=? LIMIT 7');
	$req_wishlist->execute(array($book['id']));
	if (!$req_wishlist->rowCount())
	  {
	    ?>
	    <div class="info">Personne n&#039;a ce livre dans sa Wish List pour le moment.</div>
	    <?php
							}
	else
	  {
?>
	    <div class="userwish_list">
<?php
	    while ($wishlist = $req_wishlist->fetch())
	      {
		$req_user = $bdd->prepare('SELECT name FROM users WHERE id=?');
		$req_user->execute(array($wishlist['id_user']));
		$user = $req_user->fetch();
		?>
		  <a href="?page=user&id=<?php echo $wishlist['id_user']; ?>">
		  <div class="userwishlist">
		<?php echo $user['name']; ?>
		     </div>
		  </a>
		  <?php
	      }
?>
	      </div>
<?php
		  }
      }
      ?>

<h3>Commentaires</h3>

<?php
   if (isset($_SESSION['login']) && isset($_POST['comment_submit']) && isset($_POST['comment'])
       && $_POST['comment'] != "")
     {
       include('includes/sendmail.php');
       newcommentmail($_SESSION['login'], $book['name'],
		      str_replace("\n", '<br />', protect($_POST['comment'])),
		      $book['id'], $bdd);
       $req_newcomment = $bdd->prepare('INSERT INTO comments(id_book, id_user, date, content) VALUES(?, ?, NOW(),?)');
       $req_newcomment->execute(array($book['id'],
				      $_SESSION['id_user'],
				      str_replace("\n", '<br />', protect($_POST['comment']))));
       ?>
	 <div class="ok">Le commentaire a bien &eacute;t&eacute; ajout&eacute;.</div>
	 <?php
     }


	$req_comments = $bdd->prepare('SELECT * FROM comments WHERE id_book=? ORDER BY date LIMIT 30');
	$req_comments->execute(array($book['id']));
	$flag=0;
	while ($comment = $req_comments->fetch())
	  {
	    $flag = 1;
	    $req_user = $bdd->prepare('SELECT id, name FROM users WHERE id=? LIMIT 1');
	    $req_user->execute(array(intval($comment['id_user'])));
	    $user = $req_user->fetch();
	    ?>
	      <div class="comment">
		<div class="author">
		 <div class="date"><?php echo $comment['date']; ?></div>
								      <?php if ($user['name'] == "")
								      {
									?>
									Quelqu&#039;un
									<?php
									  }
	    else
	      {
		?>
<a href="?page=user&id=<?php echo $user['id']; ?>"><?php echo $user['name']; ?></a>
		<?php
	      }
	    ?>
 a dit :
		</div>
	          <?php echo $comment['content']; ?>
	      </div>
	        <?php
	  }
    if ($flag == 0)
	  {
	  ?>
	    <div class="center">Aucun commentaire pour le moment.<br />
	      Soyez le premier &agrave; donner votre avis !</div>
	  <?php
	      }
    if (isset($_SESSION['login']))
      {
?>
	<br />
	<form method="post">
	    <div class="form_elem">
      <div class="iput">
	<textarea name="comment"></textarea>
      </div>
      <div class="text">
	  * Nouveau Commentaire
      </div>
    </div>

    <div class="right">
      <input type="submit"
	  value="Ajouter un Commentaire"
	  name="comment_submit"
	  class="submit" />
    </div>

	  </form>
	<?php
	  }
    else
      {
	?>
	<br />
	<div class="info">Il faut &ecirc;tre connect&eacute; pour poster un commentaire.</div>
<?php
      include('pages/login.php');
      }
    ?>
      <h3>Dans la m&ecirc;me cat&eacute;gorie</h3>
<div class="p">
<?php
  $req_getscateg = $bdd->prepare('SELECT * FROM books WHERE id_categ=? AND id!=? ORDER BY date LIMIT 4');
    $req_getscateg->execute(array($book['id_categ'], $book['id']));
    while ($book = $req_getscateg->fetch())
      {
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
       <?php
       if ($book['mark'] != -1)
	 {
?>
		<br /><strong>Note :</strong> 

<?php 
showmark($book['mark']);
} ?>
	      </div>
	    </a>
	<?php
      }
?>
</div>
<?php
      }
    else
      no_such_book();
  }
else
  {
    no_such_book();
  }

?>

</div>
    <?php } ?>
