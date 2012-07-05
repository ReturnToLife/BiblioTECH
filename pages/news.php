<?php
if ($_SESSION['on_page'])
  {
?>
    <div id="home">
	  <h3><img src="images/interface/nouveautes.png" alt="Nouveaut&eacute;s" /></h3>
	  
<?php
        $req_newbooks = $bdd->prepare('SELECT * FROM books WHERE img!=\'\' AND pdf_only=0 ORDER BY date DESC LIMIT 40');
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
</div>

<?php
      }
?>