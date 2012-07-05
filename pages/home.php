<?php
if ($_SESSION['on_page'])
  {
?>
<div id="pbook">
	  <h3>Le Concept</h3>
<div class="info">BiblioTECH est un gestionnaire de biblioth&egrave;que en ligne destin&eacute; aux &eacute;tudiants EPITECH.<br /><br />
Repertoriez les livres que vous avez chez vous et ceux que vous aimeriez avoir.<br />
Si quelqu&#039;un a un des livres que vous voulez, contactez-le pour lui emprunter !<?php if (isset($_SESSION['login'])) { ?><br />
<br />
Les versions PDF de certains livres sont aussi telechargeables directement sur le site.
													      <?php } ?></div>
<br />
	  <h3>Les livres</h3>
</div>
<div id="home">
	  <h3><img src="images/interface/nouveautes.png" alt="Nouveaut&eacute;s" /></h3>
	  
<?php
        $req_newbooks = $bdd->prepare('SELECT * FROM books WHERE img!=\'\' AND pdf_only=0 ORDER BY date DESC LIMIT 4');
	$req_newbooks->execute(array());
	if (!$req_newbooks->rowCount())
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
	      $i = 0;
	    while ($book = $req_newbooks->fetch())
	      {
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
	   $i++;
	      }
	    ?> </div> <?php
		   }
?>
<div class="right"><a href="?page=news">Plus de nouveaut&eacute;s...</a></div>








	  <h3><img src="images/interface/meilleures-notes.png" alt="Meilleures notes" /></h3>
<?php
        $req_newbooks = $bdd->prepare('SELECT * FROM books WHERE pdf_only=0 ORDER BY mark DESC LIMIT 4');
	$req_newbooks->execute(array());
	if (!$req_newbooks->rowCount())
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
	      $i = 0;
	    while ($book = $req_newbooks->fetch())
	      {
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
	   $i++;
	      }
	    ?> </div> <?php
		   }
?>
<div class="right"><a href="?page=marks">Plus de meilleures notes...</a></div>











	  <h3><img src="images/interface/plus-commente.png" alt="Plus comment&eacute;s" /></h3>
<?php
        $req_newbooks = $bdd->prepare('SELECT b.*, c.id_book, SUM(1) nb_com FROM comments c LEFT JOIN books b ON c.id_book=b.id WHERE pdf_only=0 GROUP BY c.id_book ORDER BY nb_com DESC LIMIT 4');
	$req_newbooks->execute(array());
	if (!$req_newbooks->rowCount())
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
	      $i = 0;
	    while ($book = $req_newbooks->fetch())
	      {
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
      <?php
       if ($book['mark'] != -1)
	 {
?>
		<br /><strong>Note :</strong> 

<?php 
showmark($book['mark']);
} ?>
<br />	   <?php echo $book['nb_com']; ?> Commentaire<?php if ($book['nb_com'] > 1) { echo 's'; } ?>
	      </div>
	    </a>

<?php
	   $i++;
	      }
	    ?></div> <?php
		   }
?>
<div class="right"><a href="?page=comments">Plus de livres comment&eacute;s...</a></div>







	  </div>
	      <?php } ?>
