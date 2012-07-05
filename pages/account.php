
<?php
if ($_SESSION['on_page'])
  {
    if ($_SESSION['login'])
      {
	?>
	<div id="account">
	  <div class="account_title"> : <?php echo $_SESSION['login']; ?></div>

<?php 
     $req_info = $bdd->prepare('SELECT s.id_book, b.name name_book, u.id id_user, u.name name_user FROM stock s LEFT JOIN books b on s.id_book=b.id LEFT JOIN users u ON s.id_user = u.id WHERE id_book IN (SELECT id_book FROM wishlist WHERE id_user=?) ORDER BY rand()');
	$req_info->execute(array($_SESSION['id_user']));
	if ($req_info->rowCount())
	  {
?>
     <h3>Informations</h3>
<?php
	while ($info = $req_info->fetch())
	  {
	    ?>
	    <div class="info"><a href="?page=user&id=<?php echo $info['id_user']; ?>"><?php echo $info['name_user']; ?></a> a le livre <a href="?page=book&id=<?php echo $info['id_book']; ?>"><?php echo $info['name_book']; ?></a> que vous voudriez lire ! Pourquoi ne pas le contacter ?</div>
	    <?php
	  }
	  }
?>


     <h3 id="addbooks">Mes livres ajout&eacute;s</h3>
        <?php
				   $req_owner = $bdd->prepare('SELECT * FROM books WHERE id_user=? ORDER BY date DESC');
	$req_owner->execute(array($_SESSION['id_user']));
	if (!$req_owner->rowCount())
	  {
	    ?>
	  <div class="info">Aucun livre ajout&eacute; pour le moment. <a href="?page=newbook">Ajouter un livre.</a></div>
	    <?php
							}
	else
	  {
	  ?>
	    <div class="p">
	    <?php
	      $i = 0;
	      while ($book_owner = $req_owner->fetch())
		{
		if ($i && !($i % 4))
		  {
		    ?>
		    </div>
		    <div class="p">
		    <?php
		  }
		  ?>
	    <a href="?page=editbook&id=<?php echo $book_owner['id']; ?>">
	      <div class="book">
		  <?php if ($book_owner['img'] == "") { ?>
		<img class="thumb"
		     src="images/books/nopic.png"
		     alt="<?php echo $book_owner['name']; ?>" />
		  <?php } else { ?>
		<img class="thumb"
		     src="images/books/<?php echo $book_owner['img']; ?>"
		     alt="<?php echo $book_owner['name']; ?>" />
		    <?php } ?>
		  <div class="title"><?php echo $book_owner['name']; ?></div>
		<strong>Cat&eacute;gorie :</strong> 
				     <?php echo getcategbyid($book_owner['id_categ']); ?><br />
		<strong>Langue :</strong> 
    <?php echo $langs[$book_owner['lang']]; ?>
    <img src="images/interface/flag/<?php echo $book_owner['lang']; ?>.gif" />
<br />
	   <input type="submit" class="submit" value="&Eacute;diter" />
	      </div>
	    </a>
		  <?php
	   $i++;
		}
	      ?>
		  </div>
	      <?php
	      }
				   ?>
	  <h3 id="bibliotheque">Ma Biblioth&egrave;que</h3>
          <?php
	     if (isset($_POST['delstock']) && isset($_POST['delstock_id']))
		{
		  $req_remove_stock = $bdd->prepare('DELETE FROM stock WHERE id_user=? AND id=?');
		  $req_remove_stock->execute(array($_SESSION['id_user'],
						   intval($_POST['delstock_id'])));
		}
	     if (isset($_POST['stockstatus']) && isset($_POST['stockstatus_id']))
		{
		  $req_changestock = $bdd->prepare('UPDATE stock SET status=? WHERE id_user=? AND id=?');
		  $req_changestock->execute(array(intval($_POST['stockstatus']),
						  $_SESSION['id_user'],
						  intval($_POST['stockstatus_id'])));
		}

	     $req_stock = $bdd->prepare('SELECT * FROM stock WHERE id_user=? ORDER BY status');
	     $req_stock->execute(array($_SESSION['id_user']));
	     if (!$req_stock->rowCount())
	  {
	    ?>
	    <div class="info">Aucun livre repertori&eacute; pour le moment.</div>
	    <?php
	  }
	else
	  {
	    ?>
	    <div class="bibst">
	    <?php
	      include('includes/available_status.php');
	      $i = 0;
	    while ($stock = $req_stock->fetch())
	      {
		$req_stock_book = $bdd->prepare('SELECT * FROM books WHERE id=? LIMIT 1');
		$req_stock_book->execute(array(intval($stock['id_book'])));
		$book = $req_stock_book->fetch();		
		if ($i && !($i % 3))
		  {
		    ?>
		    </div>
	    <div class="bibst">
		    <?php
		  }
		?>
	      <div class="book_nl">
		 <a href="?page=book&id=<?php echo $book['id']; ?>" alt="<?php echo $book['name']; ?>">
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
    <img src="images/interface/flag/<?php echo $book['lang']; ?>.gif" /><br />
					   </a>
	   <strong>Statut :</strong>
<form method="post">
	   <input type="hidden" name="stockstatus_id" value="<?php echo $stock['id']; ?>" />
        <select name="stockstatus" onchange="this.form.submit();">
<?php
for ($j = 0 ; $j <= $total_status ; $j++)
{
  if ($j == $stock['status'])
    echo '<option selected value="'.$j.'">'.$status[$j].'</option>'."\n";
  else
    echo '<option value="'.$j.'">'.$status[$j].'</option>'."\n";
}
?>
</select>
</form>
<br />
		<form method="post">
	   <input type="hidden" name="delstock_id" value="<?php echo $stock['id']; ?>" />
		  <input type="submit" value="Supprimer" name="delstock" class="submit" />
		  </form>
	      </div>

<?php
	   $i++;
       }
	    ?>
</div>
	    <?php

	  }
          ?>

	  <h3 id="wishlist">Ma Wish List</h3>
          <?php
	     if (isset($_POST['delwishlist']) && isset($_POST['delwishlist_id']))
		{
		  $req_remove_wishlist = $bdd->prepare('DELETE FROM wishlist WHERE id_user=? AND id=?');
		  $req_remove_wishlist->execute(array($_SESSION['id_user'],
						   intval($_POST['delwishlist_id'])));
		}
	$req_wishlist = $bdd->prepare('SELECT * FROM wishlist WHERE id_user=? ORDER BY id');
	$req_wishlist->execute(array($_SESSION['id_user']));
	if (!$req_wishlist->rowCount())
	  {
	    ?>
	    <div class="info">Aucun livre en vue pour le moment.</div>
	    <?php
	  }
	else
	  {
	    ?>
	    <div class="p">
	    <?php
	      $i = 0;
	    while ($wishlist = $req_wishlist->fetch())
	      {
		$req_wishlist_book = $bdd->prepare('SELECT * FROM books WHERE id=? LIMIT 1');
		$req_wishlist_book->execute(array(intval($wishlist['id_book'])));
		$book = $req_wishlist_book->fetch();		
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
    <img src="images/interface/flag/<?php echo $book['lang']; ?>.gif" /><br />
<!--		<strong>Note :</strong> <img class="mark"
					     src="images/interface/mark/star4.gif"
					     alt="Vote : 4/5" />-->
		<form method="post">
	   <input type="hidden" name="delwishlist_id" value="<?php echo $wishlist['id']; ?>" />
		  <input type="submit" value="Supprimer" name="delwishlist" class="submit" />
		  </form>
	      </div>
	    </a>

<?php
	   $i++;
       }
	    ?>
	    </div>
	    <?php

	  }
          ?>

	  <h3 id="comments">Mes Commentaires</h3>
	  <?php
	if (isset($_POST['delcomment']) && isset($_POST['delcomment_id']))
	  {
	    $req_delcomment = $bdd->prepare('DELETE FROM comments WHERE id=? AND id_user=?');
	    if ($req_delcomment->execute(array(intval($_POST['delcomment_id']),
					       $_SESSION['id_user'])))
	      {
		?>
		<div class="ok">Le commentaire a &eacute;t&eacute; supprim&eacute;.</div>
		<?php
	      }
	  }

	$req_comments = $bdd->prepare('SELECT * FROM comments WHERE id_user=? ORDER BY date');
	$req_comments->execute(array($_SESSION['id_user']));
	if (!$req_comments->rowCount())
	  {
	    ?>
	    <div class="info">Aucun commentaire post&eacute; pour le moment.</div>
	    <?php
	  }
	else
	  {
	    while ($comment = $req_comments->fetch())
	      {
		$req_comment_book = $bdd->prepare('SELECT id, name FROM books WHERE id=? LIMIT 1');
		$req_comment_book->execute(array(intval($comment['id_book'])));
		$book = $req_comment_book->fetch();
		?>
	      <div class="comment">
		 <div class="date"><?php echo $comment['date']; ?></div>
		<div class="author">
		 Livre : 
		  <a href="?page=book&id=<?php echo $book['id']; ?>">
		 <?php echo $book['name']; ?></a>
		</div>
	          <?php echo $comment['content']; ?>
		  <div class="right">
                    <form method="post">
		      <input type="hidden"
			     name="delcomment_id"
			     value="<?php echo $comment['id']; ?>" />
		      <input type="submit" value="Supprimer" name="delcomment" class="submit" />
		    </form>
		  </div>
	      </div>
		<?
	      }
	  }
	     ?>
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
