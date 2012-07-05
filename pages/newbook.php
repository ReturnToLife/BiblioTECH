
<?php
if ($_SESSION['on_page'])
  {
?>

<div id="newbook">

  <h3><img src="images/interface/ajouter-un-livre.png" alt="Ajouter un Livre" /><br /><br /></h3>


<div class="info">Avant d&#039;ajouter un livre, verifiez qu&#039;il n&#039;est pas d&eacute;j&agrave; sur le site en utilisant la fonction recherche !</div>

<?php

      if (isset($_SESSION['login']))
	{

$_POST['book_title'] = protect($_POST['book_title']);
$_POST['book_author'] = protect($_POST['book_author']);
$_POST['book_isbn'] = protect($_POST['book_isbn']);
$_POST['book_categ'] = intval($_POST['book_categ']);
$_POST['book_lang'] = intval($_POST['book_lang']);
$_POST['book_pages'] = intval($_POST['book_pages']);
$_POST['book_buy'] = protect($_POST['book_buy']);
$_POST['book_resume'] = protect($_POST['book_resume']);

if (isset($_POST['book_submit']))
  {
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
    elseif (isset($_POST['book_isbn']) && $_POST['book_isbn'] != "")
      {
	$_POST['book_buy'] = 'http://www.amazon.'.
	  ($_POST['book_lang'] == 0 ? 'fr' : 'com').'/dp/'.$_POST['book_isbn'];
      }
    else
      $_POST['book_buy'] = "";

    if (!isset($_POST['book_isbn']))
      {
	$_POST['book_isbn'] = "";
      }

    if (!isset($_POST['book_resume']))
      $_POST['book_resume'] == "";
    
    if ($err == 0)
      {
	// check already in base
	if (isset($_FILES['book_img']) && $_FILES['book_img']['error'] == 0)
	  {
	    if ($_FILES['book_img']['size'] <= 102400) // 100 Ko
	      {
		$infosfichier = pathinfo($_FILES['book_img']['name']);
		$extension_upload = $infosfichier['extension'];
		$extensions_autorisees = array('jpg', 'png', 'gif');
		if (in_array($extension_upload, $extensions_autorisees))
		  {
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
	  $book_img_name = "";
	
	if (isset($_FILES['book_pdf']) && $_FILES['book_pdf']['error'] == 0)
	  {
	    if ($_FILES['book_pdf']['size'] <= 31457280) // 30 Mo
	      {
		$infosfichier = pathinfo($_FILES['book_pdf']['name']);
		$extension_upload = $infosfichier['extension'];
		if ($extension_upload == "pdf" && $err == 0)
		  {
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
	  $book_pdf_name = "";
	
	if ($err == 0)
	  {
	    if ($book_img_name == "")
	      {
		$req_already = $bdd->prepare('SELECT id FROM books WHERE name=?');
		$req_already->execute(array(protect($_POST['book_title'])));
	      }
	    else
	      {
		$req_already = $bdd->prepare('SELECT id FROM books WHERE img=?');
		$req_already->execute(array($book_img_name));
	      }
	    if (!$req_already->rowCount())
	      {
		$req_insert = $bdd->prepare('INSERT INTO books(name, id_categ, id_user, date, lang, pages, mark, author, isbn, pdf, img, buylink, info) VALUES(?, ?, ?, NOW(), ?, ?, -1, ?, ?, ?, ?, ?, ?)');
		$req_insert->execute(array($_POST['book_title'],
					   intval($_POST['book_categ']),
					   $_SESSION['id_user'],
					   intval($_POST['book_lang']),
					   intval($_POST['book_pages']),
					   $_POST['book_author'],
					   $_POST['book_isbn'],
					   protect($book_pdf_name),
					   protect($book_img_name),			    
					   $_POST['book_buy'],
					   str_replace("\n", '<br />', $_POST['book_resume'])));
		include('includes/sendmail.php');
		$lastid = $bdd->lastInsertId();
		newbookmail($lastid, $_POST['book_title'], $_POST['book_author'],
			    getcategbyid(intval($_POST['book_categ'])),
			    $langs[intval($_POST['book_lang'])],
			    $_SESSION['login'],
			    $bdd);
		?>
		  <div class="ok">Le livre a bien &eacute;t&eacute; ajout&eacute; :
		<a href="?page=book&id=<?php echo $lastid; ?>">lien</a>.</div>
		   <?php
		   }
	    else
	      {
		$already = $req_already->fetch();
		?>
		<div class="warning">Ce livre existe d&eacute;j&agrave; : <a href="?page=book&id=<?php echo $already['id'] ?>">lien</a></div>
		  <?php $err=1;
	      }
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
	       value="<?php echo $_POST['book_title']; ?>" />
      </div>
      <div class="title">
	* Titre
      </div>
    </div>

    <div class="form_elem">
      <div class="iput">
	<input type="text" maxlenght="255" name="book_author"
	       value="<?php echo $_POST['book_author']; ?>" />
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

    <div class="form_elem">
      <div class="iput">
	<input type="text" maxlenght="255" name="book_isbn"
	       value="<?php echo $_POST['book_isbn']; ?>" />
      </div>
      <div class="title">
	ISBN-10
      </div>
    </div>

    <div class="form_elem">
      <div class="iput">
	<input type="text" maxlenght="255" name="book_pages"
	       value="<?php if (intval($_POST['book_pages']) != 0) { echo $_POST['book_pages']; } ?>" />
      </div>
      <div class="title">
        Nombre de pages
      </div>
    </div>

    <div class="form_elem">
      <div class="iput">
		       <?php if (isset($_POST['book_buy']) && $_POST['book_buy'] != "") { ?>
	<input type="text" maxlenght="255" name="book_buy"
	       value="<?php echo $_POST['book_buy']; ?>" />
	<?php } else { ?>
	<input type="text" maxlenght="255" name="book_buy"
	       value="http://"
	       onfocus="if(this.value=='http://') this.value='';"
	       onblur="if(this.value=='') this.value='http://';"/>
	<?php } ?>
      </div>
      <div class="title">
       Lien Achat (exemple : Amazon)
      </div>
    </div>

    <div class="form_elem">
      <div class="iput">
	<textarea name="book_resume"><?php echo $_POST['book_resume']; ?></textarea>
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

    <div class="form_elem">
      <div class="iput">
	<input class="file" type="file" name="book_pdf" />
      </div>
      <div class="sfile">
PDF (< 30 Mo)
      </div>
    </div>

    <div class="right">
      <input type="submit" value="Ajouter un livre" name="book_submit" class="book_submit" />
    </div>

  </form>

<?php
	}
    else
      {
	?>
	<div class="warning">Il faut &ecirc;tre connect&eacute; pour ajouter un livre !</div>
	<?php
	include('pages/login.php');
      }
?>

</div>


	  <?php } ?>

