
<?php
if ($_SESSION['on_page'])
  {
?>

<div id="newbook">

      <h3><img src="images/interface/modifier-le-livre.png" alt="Modifier le Livre" /><br /><br /></h3>
<?php

      if (isset($_SESSION['login']))
	{
	  if (isset($_GET['id']))
	    {
	      if ($_SESSION['adm'])
		{
		  $req_getbook = $bdd->prepare('SELECT * FROM books WHERE id=?');
		  $req_getbook->execute(array(intval($_GET['id'])));
		}
	      else
		{
		  $req_getbook = $bdd->prepare('SELECT * FROM books WHERE id=? AND id_user=?');
		  $req_getbook->execute(array(intval($_GET['id']),
					      $_SESSION['id_user']));
		}
	      if (!$req_getbook->rowCount())
		{
		  ?>
		  <div class="warning">Ce livre n&#039;existe pas ou vous n&#039;&ecirc;tes pas le propri&eacute;taire.</div>
		  <?php
		}
	      else
		{
		  $book = $req_getbook->fetch();
		  ?>
		  <div class="right"><a href="?page=book&id=<?php echo $book['id']; ?>"><img src="images/interface/icons/loupe.png" /> Voir le livre</a></div>
		  <?php
		  if (isset($_POST['book_submit_del']) && isset($_POST['del_verif']))
		    {
		      $req_delete_stock = $bdd->prepare('DELETE FROM stock WHERE id_book=?');
		      $req_delete_stock->execute(array($book['id']));
		      $req_delete_wishlist = $bdd->prepare('DELETE FROM wishlist WHERE id_book=?');
		      $req_delete_wishlist->execute(array($book['id']));
		      $req_delete_comments = $bdd->prepare('DELETE FROM comments WHERE id_book=?');
		      $req_delete_comments->execute(array($book['id']));
		      if ($book['img'] != "")
			@unlink('images/books/'.$book['img']);
		      if ($book['pdf'] != "")
			@unlink('pdf/'.$book['pdf']);
		      $req_deletebook = $bdd->prepare('DELETE FROM books WHERE id=?');
		      $req_deletebook->execute(array($book['id']));
		      ?>
			<script language="JavaScript">
			   window.location="?page=account";
		      </script>
			<?php
		    }
		  if (isset($_POST['book_submit']))
		    {
		      $_POST['book_title'] = protect($_POST['book_title']);
		      $_POST['book_author'] = protect($_POST['book_author']);
		      $_POST['book_isbn'] = protect($_POST['book_isbn']);
		      $_POST['book_categ'] = intval($_POST['book_categ']);
		      $_POST['book_lang'] = intval($_POST['book_lang']);
		      $_POST['book_pages'] = intval($_POST['book_pages']);
		      $_POST['book_buy'] = protect($_POST['book_buy']);
		      $_POST['book_resume'] = protect($_POST['book_resume']);

		      $err=0;
		      if (!isset($_POST['book_title']) || $_POST['book_title'] == "")
			{?>
			  <div class="warning">Il manque le titre du livre.</div>
			    <?php $err=1;
			}
		      if (!isset($_POST['book_author']) || $_POST['book_author'] == "")
			{
			  $_POST['book_author'] = "";
			}
		      if (!isset($_POST['book_isbn']))
			{
			  $_POST['book_isbn'] = $book['isbn'];
			}

		      if (!isset($_POST['book_categ']) ||
			  !($_POST['book_categ'] >= 0 && $_POST['book_categ'] <= $total_categ))
			{?>
			 <div class="warning">La cat&eacute;gorie du livre est incorrecte.</div>
			 <?php $err=1;
			}
		      
		      if (!isset($_POST['book_lang']) ||
			  !($_POST['book_lang'] >= 0 && $_POST['book_lang'] <= $total_lang))
			{?>
			 <div class="warning">La langue du livre est incorrecte.</div>
			 <?php $err=1;
			}
		      if ((isset($_POST['book_buy']) && $_POST['book_buy'] != "" && $_POST['book_buy'] != "http://"))
			{
			  if (!($test_url = @fopen($_POST['book_buy'], 'r')))
			    {
			      ?>
			      <div class="warning">L&quot;URL d&quot;achat est invalide.</div>
			      <?php $err=1;
			    }
			  else
			    @fclose($test_url);
			}
		      elseif ($book['buylink'] != "" && isset($_POST['book_isbn']) && $_POST['book_isbn'] != "")
		      {
			if ($_POST['lang'] == 0)
			  $_POST['book_buy'] = 'http://www.amazon.fr/dp/'.$_POST['book_isbn'];
			else
			  $_POST['book_buy'] = 'http://www.amazon.com/dp/'.$_POST['book_isbn'];
		      }
		      else
			$_POST['book_buy'] = $book['buylink'];
		      
		      if (!isset($_POST['book_resume']))
			$_POST['book_resume'] = $book['info'];

		      if ($err == 0)
			{
			  if (isset($_FILES['book_img']) && $_FILES['book_img']['error'] == 0)
			    {
			      if ($_FILES['book_img']['size'] <= 102400) // 100 Ko
				{
				  $infosfichier = pathinfo($_FILES['book_img']['name']);
				  $extension_upload = $infosfichier['extension'];
				  $extensions_autorisees = array('jpg', 'png', 'gif');
				  if (in_array($extension_upload, $extensions_autorisees))
				    {
				      if ($book['img'] != "")
					unlink('images/books/'.$book['img']);
				      $book_img_name = toalphanum($_POST['book_title']).'.'.$extension_upload;
				      move_uploaded_file($_FILES['book_img']['tmp_name'],
							 'images/books/'.$book_img_name);
				    }
				  else
				    {
				      ?>
				      <div class="warning">Mauvaise extension de fichier image.</div>
					<?php $err=1;
				    }
				}
			      else
				{
				  ?>
				  <div class="warning">Le fichier image est trop gros.</div>
				    <?php $err=1;
				}
			    }
			  else
			    $book_img_name = $book['img'];
			  
			  if (isset($_FILES['book_pdf']) && $_FILES['book_pdf']['error'] == 0)
			    {
			      if ($_FILES['book_pdf']['size'] <= 31457280) // 30 Mo
				{
				  $infosfichier = pathinfo($_FILES['book_pdf']['name']);
				  $extension_upload = $infosfichier['extension'];
				  if ($extension_upload == "pdf" && $err == 0)
				    {
				      if ($book['pdf'] != "")
				      unlink('pdf/'.$book['pdf']);
				      $book_pdf_name = toalphanum($_POST['book_title']).'.'.$extension_upload;
				      move_uploaded_file($_FILES['book_pdf']['tmp_name'],
							 'pdf/'.$book_pdf_name);
				    }
				  else
				    {
				      ?>
				      <div class="warning">Mauvaise extension de fichier PDF.</div>
					<?php $err=1;
				    }
				}
			      else
				{
				  ?>
				  <div class="warning">Le fichier PDF est trop gros.</div>
				    <?php $err=1;
				}	  
			    }
			  else
			    $book_pdf_name = $book['pdf'];
			  
			  if ($err == 0)
			    {
			      $req_update = $bdd->prepare('UPDATE books SET name=?, id_categ=?, date=NOW(), lang=?, pages=?, author=?, isbn=?, pdf=?, img=?, buylink=?, info=? WHERE id=?');
			      $req_update->execute(array($_POST['book_title'],
							 intval($_POST['book_categ']),
							 intval($_POST['book_lang']),
							 intval($_POST['book_pages']),
							 $_POST['book_author'],
							 $_POST['book_isbn'],
							 protect($book_pdf_name),
							 protect($book_img_name),		    
							 $_POST['book_buy'],
							 str_replace("\n", '<br />', $_POST['book_resume']),
							 intval($_GET['id'])));
				  ?>
				<div class="ok">Le livre a bien &eacute;t&eacute; modifi&eacute; :
				  <a href="?page=book&id=<?php echo intval($_GET['id']); ?>">lien</a>.</div>
				     <?php
				     }
			}
		    }
		  ?>
		  
<br />
  <form method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="209715200" />
    <div class="form_elem">
      <div class="iput">
	<input type="text" maxlenght="255" name="book_title" 
	       value="<?php if (isset($_POST['book_submit']) && isset($_POST['book_title'])) { echo $_POST['book_title']; } else { echo $book['name']; } ?>" />
      </div>
      <div class="title">
	* Titre
      </div>
    </div>

    <div class="form_elem">
      <div class="iput">
	<input type="text" maxlenght="255" name="book_author"
	       value="<?php if (isset($_POST['book_submit']) && isset($_POST['book_author'])) { echo $_POST['book_author']; } else { echo $book['author']; } ?>" />
      </div>
      <div class="title">
	Auteur
      </div>
    </div>

    <div class="form_elem">
      <div class="iput">
	<select name="book_categ">
		  <?php
		  for ($i = 0 ; $i <= $total_categ ; $i++)
		    {
		      if (isset($_POST['book_submit']) && isset($_POST['book_categ']) && $i == intval($_POST['book_categ']))
			echo '<option selected value="'.$categories[$i]['id'].'">'.$categories[$i]['categorie'].'</option>'."\n";
		      else if ($categories[$i]['id'] == $book['id_categ'])
			echo '<option selected value="'.$categories[$i]['id'].'">'.$categories[$i]['categorie'].'</option>'."\n";
		      else
			echo '<option value="'.$categories[$i]['id'].'">'.$categories[$i]['categorie'].'</option>'."\n";
		    }
		  ?>
	</select>
      </div>
      <div class="select">
	* Cat&eacute;gorie
      </div>
    </div>
    
    <div class="form_elem">
      <div class="iput">
	<select name="book_lang">
		  <?php
		  for ($i = 0 ; $i <= $total_lang ; $i++)
		    {
		      if (isset($_POST['book_submit']) && isset($_POST['book_lang']) && $i == intval($_POST['book_lang']))
			echo '<option selected value="'.$i.'">'.$langs[$i].'</option>'."\n";
		      else if ($i == $book['lang'])
			echo '<option selected value="'.$i.'">'.$langs[$i].'</option>'."\n";
		      else
			echo '<option value="'.$i.'">'.$langs[$i].'</option>'."\n";
		    }
		  ?>
	</select>
      </div>
      <div class="select">
	* Langue
      </div>
    </div>

		  <?php if ($book['pdf_only'] == 0) { ?>
    <div class="form_elem">
      <div class="iput">
	<input type="text" maxlenght="255" name="book_isbn"
	       value="<?php if (isset($_POST['book_submit']) && isset($_POST['book_isbn'])) { echo $_POST['book_isbn']; } else { echo $book['isbn']; } ?>" />
      </div>
      <div class="title">
	ISBN-10
      </div>
    </div>
						      <?php } ?>

    <div class="form_elem">
      <div class="iput">
	<input type="text" maxlenght="255" name="book_pages"
	       value="<?php if (isset($_POST['book_submit']) && isset($_POST['book_pages']) && $_POST['book_pages'] != 0) { echo $_POST['book_pages']; } else if ($book['pages'] != 0) { echo $book['pages']; } ?>" />
      </div>
      <div class="title">
		  Nombre de pages
      </div>
    </div>

		  <?php if ($book['pdf_only'] == 0) { ?>
    <div class="form_elem">
      <div class="iput">
		  <?php if (isset($_POST['book_submit']) && isset($_POST['book_buy']) && $_POST['book_buy'] != "") { ?>
	<input type="text" maxlenght="255" name="book_buy"
	       value="<?php echo $_POST['book_buy']; ?>" />
	<?php } else { ?>
	<input type="text" maxlenght="255" name="book_buy"
	       value="<?php echo $book['buylink']; ?>" />
	<?php } ?>
      </div>
      <div class="title">
       Lien Achat (exemple : Amazon)
      </div>
    </div>

		  <?php if ($book['buylink'] != "")
	  { ?>
<div class="info">Lien actuel : <a href="<?php echo $book['buylink']; ?>"
	    target="_blank">lien</a>.</div>
	      <br />
	      <?php } ?>
						      <?php } ?>


    <div class="form_elem">
      <div class="iput">
		  <textarea name="book_resume"><?php
		  if (isset($_POST['book_submit']) && isset($_POST['book_resume']))
		    {
		      echo $_POST['book_resume'];
		    }
		  else
		    {
		      echo str_replace('<br />', "\n", $book['info']);
		    } ?></textarea>
      </div>
      <div class="text">
	R&eacute;sum&eacute;
      </div>
    </div>

    <div class="form_elem">
      <div class="iput">
	<input class="file" type="file" name="book_img" />
      </div>
      <div class="sfile">
	Image (< 100Ko)
      </div>
    </div>
		  <?php if ($book['img'] != "")
	  { ?>
<div class="info">Image actuelle : <a href="images/books/<?php echo $book['img']; ?>"
		  alt="<?php echo $book['name']; ?>" target="_blank">lien</a>.</div>
	      <br />
	      <?php } else { ?>
<div class="info">Aucune Image actuellement.</div>
	      <br />
	      <?php } ?>

    <div class="form_elem">
      <div class="iput">
	<input class="file" type="file" name="book_pdf" />
      </div>
      <div class="sfile">
		  PDF (< 30 Mo)
      </div>
    </div>

		  <?php if ($book['pdf'] != "")
	  { ?>
<div class="info">PDF actuel : <a href="pdf/<?php echo $book['pdf']; ?>"
		  alt="<?php echo $book['name']; ?>" target="_blank">lien</a>.</div>
	      <br />
	      <?php } else { ?>
<div class="info">Aucun PDF actuellement.</div>
	      <br />
	      <?php } ?>

    <div class="right">
      <input type="submit" value="Modifier le livre" name="book_submit" class="book_submit" />
    </div>
  </form>

<br /><br />

<form method="post">

    <div class="right">
      <input type="checkbox" name="del_verif" />
		  Cocher cette case pour supprimer d&eacute;finitivement le livre.
    </div>
    <div class="right">
      <input type="submit" value="Supprimer d&eacute;finitivement le livre" name="book_submit_del" class="book_submit" />
    </div>

</form>

<?php
		}
	    }
	  else
	    {
	?>
	    <div class="warning">Ce livre n&#039;existe pas ou plus.</div>
	<?php	    
	    }
	}
    else
      {
	?>
	<div class="warning">Il faut &ecirc;tre connect&eacute; pour modifier un livre !</div>
	<?php
	include('pages/login.php');
      }
?>

</div>


	  <?php } ?>

