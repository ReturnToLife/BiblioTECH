<?php
if ($_SESSION['on_page'])
  {
?>
<div id="pbook">

<img src="images/interface/livres_disponibles.png" alt="Livres disponibles" />
<br />	  <br />	  
<?php
        $req_allbooks = $bdd->prepare('SELECT * FROM books ORDER BY id_categ');
	$req_allbooks->execute(array());
	if (!$req_allbooks->rowCount())
	  {
	    ?>
	    <div class="info">Aucun livre repertori&eacute; pour le moment.</div>
	    <?php
	  }
	else
	  {
	    ?>
	    <?php
	    $categ = -1;
	    $i = 0;
	    while ($book = $req_allbooks->fetch())
	      {
		if ($book['id_categ'] != $categ)
		  {
		    $categ = $book['id_categ'];
		    if ($i)
		      {
		   ?>
		     </div>
			  <?php
			  }
		    ?>
		    <h3><?php echo getcategbyid($categ); ?></h3>
		     <div class="p">
		    <?php
							      $i = 0;
		  }
		else if ($i && !($i % 4))
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
	      <?php } ?>
