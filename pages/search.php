<?php

function search_result($bdd, $frtype, $req, $text)
    {
      $req_search = $bdd->prepare($req);
      $req_search->execute(array($text));
      if ($text != '%')
	{
      ?>
  <h3>R&eacute;sultats dans <?php echo $frtype; ?> du livre</h3>
	<?php
						     }
      if ($req_search->rowCount())
	{
	?>
<div class="p">
<?php
	    include_once('includes/available_categ_tab.php');
	    include_once('includes/available_lang_tab.php');
								$i = 0;
	  while ($book = $req_search->fetch())
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
	  ?>
	    </div>
		<?php
		}
      else
	{
	  ?>
	    <div class="info">La recherche n&#039;a donn&eacute; aucun r&eacute;sultat.</div>
	    <br />
	    <?php
	    }
	    }

if (isset($_SESSION['on_page']))
  {
    if (isset($_GET['lang']) || isset($_GET['categ']))
      {
	$_POST['search_submit'] = true;
	$_POST['search_text'] = '*';
	if (isset($_GET['lang']))
	  $_POST['search_lang'] = $_GET['lang'];
	else
	  $_POST['search_lang'] = -1;
	if (isset($_GET['categ']))
	  $_POST['search_categ'] = $_GET['categ'];
	else
	  $_POST['search_categ'] = -1;
      }
    if (isset($_POST['search_submit']))
      {
	?>
	<div id="pbook">
	  <h1>R&eacute;sultats de la Recherche</h1>
															      <br />
															      <?php
															      
        $_POST['search_text'] = protect($_POST['search_text']);
	$_POST['search_categ'] = intval($_POST['search_categ']);
	$_POST['search_lang'] = intval($_POST['search_lang']);
	
    if ($_POST['search_text'] == '*' || $_POST['search_text'] == "")
      $_POST['search_text'] = '%';
    elseif ($_POST['search_text'] == 'c' || $_POST['search_text'] == 'C')
      $_POST['search_text'] = '%'.$_POST['search_text'].' %';
    else
      $_POST['search_text'] = '%'.$_POST['search_text'].'%';    

    if ($_POST['search_categ'] != -1 && $_POST['search_lang'] != -1)
      {
	search_result($bdd, 'le titre', 'SELECT * FROM books WHERE name LIKE ? AND id_categ='.intval($_POST['search_categ']).' AND lang='.intval($_POST['search_lang']), $_POST['search_text']);
	if ($_POST['search_text'] != '%')
	  {
	    search_result($bdd, 'l&#039;auteur', 'SELECT * FROM books WHERE author LIKE ? AND id_categ='.intval($_POST['search_categ']).' AND lang='.intval($_POST['search_lang']), $_POST['search_text']);
	    search_result($bdd, 'le r&eacute;sum&eacute;', 'SELECT * FROM books WHERE info LIKE ? AND id_categ='.intval($_POST['search_categ']).' AND lang='.intval($_POST['search_lang']),
		      $_POST['search_text']); 
	  }
      }
    else if ($_POST['search_lang'] != -1)
      {
	search_result($bdd, 'le titre', 'SELECT * FROM books WHERE name LIKE ? AND lang='.intval($_POST['search_lang']), $_POST['search_text']);
	if ($_POST['search_text'] != '%')
	  {
	search_result($bdd, 'l&#039;auteur', 'SELECT * FROM books WHERE author LIKE ? AND lang='.intval($_POST['search_lang']), $_POST['search_text']);
	search_result($bdd, 'le r&eacute;sum&eacute;', 'SELECT * FROM books WHERE info LIKE ? AND lang='.intval($_POST['search_lang']),
		      $_POST['search_text']); 	
	  }
      }
    else if ($_POST['search_categ'] != -1)
      {
	search_result($bdd, 'le titre', 'SELECT * FROM books WHERE name LIKE ? AND id_categ='.intval($_POST['search_categ']), $_POST['search_text']);
	if ($_POST['search_text'] != '%')
	  {
	search_result($bdd, 'l&#039;auteur', 'SELECT * FROM books WHERE author LIKE ? AND id_categ='.intval($_POST['search_categ']), $_POST['search_text']);
	search_result($bdd, 'le r&eacute;sum&eacute;', 'SELECT * FROM books WHERE info LIKE ? AND id_categ='.intval($_POST['search_categ']),
		      $_POST['search_text']); 
	  }
      }
    else
      {
	search_result($bdd, 'le titre', 'SELECT * FROM books WHERE name LIKE ?', $_POST['search_text']);
	if ($_POST['search_text'] != '%')
	  {
	search_result($bdd, 'l&#039;auteur', 'SELECT * FROM books WHERE author LIKE ?', $_POST['search_text']);
	search_result($bdd, 'le r&eacute;sum&eacute;', 'SELECT * FROM books WHERE info LIKE ?',
		      $_POST['search_text']); 
	  }
      }

      ?>
	</div>
<?php	    
   
      }
  }
?>
