<?php
include('includes/get_photo.php');
if ($_SESSION['on_page'])
  {
    if (isset($_SESSION['login']))
      {
	if ($_SESSION['adm'])
	  {
	    ?>
	    <div id="pbook">
	      <h2><img src="images/interface/admin.png" alt="Administration" /></h2>
	      <br />
	      <ul>
	      <li><a href="?page=admin&books">Livres</a></li>
	      <li><a href="?page=admin&categories">Cat&eacute;gories</a></li>
	      <li><a href="?page=admin&users">Utilisateurs</a></li>
	      <li><a href="?page=admin&comments">Commentaires</a></li>
	      <li><a href="?page=admin&captcha">Captcha</a></li>
	      </ul>

	      <?php
	      if (isset($_GET['books']))
		{
	      ?>
	    <h3>Modifier les livres</h3>
	      <table>
	      <tr>
		<td style="width: 33px;">Image</td>
		<td>Nom du livre</td>
		<td>Owner</td>
		<td>Infos manquantes</td>
		<td>PDF</td>
		<td>Aper&ccedil;u</td>
		<td>Modifier</td>
	      </tr>
	      <?php
	      $req_getbooks = $bdd->prepare('SELECT b.*, u.name AS username, u.id AS id_user FROM books AS b JOIN users AS u WHERE b.id_user=u.id ORDER BY b.name');
	    $req_getbooks->execute(array());
	    while ($book = $req_getbooks->fetch())
	      {
		?>
		<tr>
		  <td>
		  <?php if ($book['img'] == "") { ?>
		<img class="mthumb"
		     src="images/books/nopic.png"
		     alt="<?php echo $book['name']; ?>" />
		  <?php } else { ?>
		<img class="mthumb"
		     src="images/books/<?php echo $book['img']; ?>"
		     alt="<?php echo $book['name']; ?>" />
		    <?php } ?>
		  </td>
		  <td>
		    <a href="?page=editbook&id=<?php echo $book['id']; ?>">
		      <?php echo $book['name']; ?></a></td>
		  <td>
		    <a href="?page=user&id=<?php echo $book['id_user']; ?>">
		      <?php echo $book['username']; ?></a></td>
                  <td>
		    <a href="?page=editbook&id=<?php echo $book['id']; ?>">
		    <?php
		      if ($book['img'] == "")
			echo '<img src="images/interface/icons/img.png" /> ';
		      if ($book['pdf'] == "")
			echo '<img src="images/interface/icons/pdf.gif" /> ';
		      if ($book['isbn'] == "" && $book['pdf_only'] == 0)
			echo '<img src="images/interface/icons/isbn.png" /> ';
		      if ($book['buylink'] == "" && $book['pdf_only'] == 0)
			echo '<img src="images/interface/icons/buy.gif" /> ';
		      if ($book['info'] == "")
			echo '<img src="images/interface/icons/text.png" /> ';
		      if ($book['pages'] == 0)
			echo '<img src="images/interface/icons/count.png" /> ';
			?>
		    </a>
		  </td>
			<td><?php if ($book['pdf_only'] == 1) { ?><img src="images/interface/icons/pdf.gif" /><?php } ?></td>
		  <td>
		    <a href="?page=book&id=<?php echo $book['id']; ?>">
		      <img src="images/interface/icons/loupe.png" /> Aper&ccedil;u</a></td>
		  <td>
		    <a href="?page=editbook&id=<?php echo $book['id']; ?>">
		      <img src="images/interface/icons/edit.gif" /> Modifier</a></td>
		  </tr>
		<?php
	      }
	      ?>
	      </table>
<?php
		}
	    elseif (isset($_GET['users']))
	      {
?>
	    <h3>Utilisateurs</h3>
	      <table>
	      <tr>
		<td style="width: 33px;">Photo</td>
		<td>Utilisateur</td>
		<td>Profil</td>
		  <td style="width: 10px;">Activ&eacute;</td>
		<td style="width: 10%;">Log As</td>
		<td style="width: 10px;">Supprimer</td>
	      </tr>
	      <?php
		  if (isset($_POST['deluser']) && isset($_POST['id']) && isset($_POST['name']))
		   {
		     $req_user = $bdd->prepare('DELETE FROM userinfo WHERE id_user=?');
		     $req_user->execute(array(intval($_POST['id'])));
		     $req_user = $bdd->prepare('DELETE FROM stock WHERE id_user=?');
		     $req_user->execute(array(intval($_POST['id'])));
		     $req_user = $bdd->prepare('DELETE FROM wishlist WHERE id_user=?');
		     $req_user->execute(array(intval($_POST['id'])));
		     @unlink('plan/'.$_POST['name']); 
		     $req_user = $bdd->prepare('DELETE FROM users WHERE id=?');
		     $req_user->execute(array(intval($_POST['id'])));
		   }
		  if (isset($_POST['logas']) && isset($_POST['id']))
		   {
		     $req_user = $bdd->prepare('SELECT id, name, adm FROM users WHERE id=?');
		     $req_user->execute(array(intval($_POST['id'])));
		     $userinfo = $req_user->fetch();
		     $_SESSION['login'] = $userinfo['name'];
		     $_SESSION['id_user'] = $userinfo['id'];
		     $_SESSION['adm'] = $userinfo['adm'];
		     ?>
		     <SCRIPT language="JavaScript">
		     window.location="?page=account";
		     </SCRIPT>
		     <?php
		   }
	      $req_getusers = $bdd->prepare('SELECT * FROM users ORDER BY name');
	    $req_getusers->execute(array());
	    while ($user = $req_getusers->fetch())
	      {
		?>
		<tr>
		  <td>
		    <img src="<?php get_photo($user['name']); ?>" alt="<?php echo $name; ?>"
		     class="mthumb" />
		  </td>
		  <td>
		  <?php
		  if ($user['adm'])
		    {
		      ?>
		      <img src="images/interface/icons/admin.png" alt="admin" />
		      <?php
		    }
		?>
		  <a href="?page=user&id=<?php echo $user['id']; ?>"><?php echo $user['name']; ?></a>
												     </td>
		  <td ><a href="?page=user&id=<?php echo $user['id']; ?>">
		  <img src="images/interface/icons/loupe.png" /> Profil</a></td>
													  <td ><img src="images/interface/icons/<?php
													  if (!$user['activated'])
													  {
													    echo 'warning.png';
}
else
{
echo 'checked.gif';
}

		?>" /></td>
		  <td >
                    <form method="post">
		      <input type="hidden" value="<?php echo $user['id']; ?>" name="id" />
		      <input type="submit" class="submit" value="Se loguer en tant que <?php echo $user['name']; ?>" name="logas" />
		    </form>
		  </td>
		  <td >
                    <form method="post">
		      <input type="hidden" value="<?php echo $user['id']; ?>" name="id" />
		      <input type="hidden" value="<?php echo $user['name']; ?>" name="name" />
		      <input type="submit" class="submit" value="Supprimer" name="deluser" />
		    </form>
		  </td>
		  </tr>
		<?php
	      }
	      ?>
	      </table>
<?php
		  }
		  elseif (isset($_GET['comments']))
		    {
?>
	    <h3>Commentaires</h3>
		  <?php

	if (isset($_POST['delcomment']) && isset($_POST['delcomment_id']))
	  {
	    $req_delcomment = $bdd->prepare('DELETE FROM comments WHERE id=?');
	    if ($req_delcomment->execute(array(intval($_POST['delcomment_id']))))
	      {
		?>
		<div class="ok">Le commentaire a &eacute;t&eacute; supprim&eacute;.</div>
		<?php
	      }
	  }

		  $req_comments = $bdd->prepare('SELECT c.id AS id_comment, c.id_book, c.date, b.img AS img_book, u.name AS username, b.name AS bookname, c.id_user, c.content FROM comments AS c JOIN books AS b ON c.id_book=b.id JOIN users AS u ON c.id_user=u.id ORDER BY c.date');
	    $req_comments->execute();
		  ?>
		  <table>
		  <tr>
		  <td style="width: 33px;">Livre</td>
		     <td style="width: 10px;">Titre</td>
		  <td style="width: 10px;">Date</td>
		     <td style="width: 60px;">Post&eacute; par :</td>
		  <td>Contenu</td>
							     <td>Supprimer</td>
		  </tr>
		     <?php
		     while ($comment = $req_comments->fetch())
		       {
			 ?>
			 <tr>
			   <td>
			    <a href="?page=book&id=<?php echo $comment['id_book']; ?>">
			     <img src="images/books/<?php if ($comment['img_book'] == "") { echo 'nopic.png'; } else { echo $comment['img_book']; } ?>" class="mthumb" />
			    </a>
			   </td>
			 <td><a href="?page=book&id=<?php echo $comment['id_book']; ?>"><?php echo min_lenght_($comment['bookname'], 10, false); ?></a></td>
			 <td><?php echo $comment['date']; ?></td>
			 <td><a href="?page=user&id=<?php echo $comment['id_user']; ?>"><?php echo $comment['username']; ?></a></td>
															       <td><?php echo min_lenght($comment['content'], 255); ?></td>
<td>
<form method="post">
<input type="hidden" name="delcomment_id" value="<?php echo $comment['id_comment']; ?>" />
<input type="submit" class="submit" value="Supprimer" name="delcomment" />
</form>
</td>
			   </tr>
			 <?php
		       }
		     ?>
		  </table>

<?php
		      }
		  elseif (isset($_GET['categories']))
		    {
		      if (isset($_POST['newcateg']))
			{
			  $req_newcateg = $bdd->prepare('INSERT INTO categories(categorie) VALUES(?)');
			  $req_newcateg->execute(array(protect($_POST['newcateg'])));
			}

?>
	    <h3>Cat&eacute;gories de livres</h3>
		      <table>
 <?php
 $req_getcateg = $bdd->prepare('SELECT * FROM categories ORDER BY id');
 $req_getcateg->execute();
 while ($categ = $req_getcateg->fetch())
   {
     ?>
     <tr>
       <td><?php echo $categ['categorie']; ?></td>
     </tr>
     <?php
   }
?>
			</table>
<form method="post">
<input type="text" value="" style="width: 70%" name="newcateg" />
<input type="submit" value="Ajouter une cat&eacute;gorie" class="submit" />
</form>

<?php
		      }
		  elseif (isset($_GET['captcha']))
		    {
?>
	    <h3>Captcha</h3>
		  <?php

		      if (isset($_POST['captcha_submit'])
			  && isset($_POST['captcha_question'])
			  && isset($_POST['captcha_answer']))
			{
			  $req_add = $bdd->prepare('INSERT INTO captcha(question, answer) VALUES(?, ?)');
			  $req_add->execute(array(protect($_POST['captcha_question']),
						  protect($_POST['captcha_answer'])));

			}
	    if (isset($_POST['delcaptcha']) && isset($_POST['idcaptcha']))
	      {
		$req_delcaptcha = $bdd->prepare('DELETE FROM captcha WHERE id=?');
		$req_delcaptcha->execute(array(intval($_POST['idcaptcha'])));
	      }


		      $req_captcha = $bdd->prepare('SELECT * FROM captcha ORDER BY rand()');
	    $req_captcha->execute();
		  ?>
		  <table>
		  <tr>
		  <td>Question</td>
		     <td>R&eacute;ponse</td>
		     <td>Supprimer</td>
		  </tr>
		     <?php
		     while ($captcha = $req_captcha->fetch())
		       {
			 ?>
			 <tr>
			   <td><?php echo $captcha['question']; ?></td>
								      <td><?php echo $captcha['answer']; ?></td>
			   <td>
			   <form method="post">
			   <input type="hidden" name="idcaptcha" value="<?php echo $captcha['id']; ?>" />
			   <input type="submit" class="submit" value="Supprimer" name="delcaptcha" />
			   </form>
			   </td>
			   </tr>
			 <?php
		       }
		     ?>
		  </table>


<br /><br />

				    <form method="post">
	 <div class="form_elem">
	   <div class="iput">
	     <input type="text" value="" name="captcha_question" />
	   </div>
	   <div class="title">
	     * Question
	   </div>
	 </div>
	 <div class="form_elem">
	   <div class="iput">
	     <input type="text" value="" name="captcha_answer" />
	   </div>
	   <div class="title">
				       * R&eacute;ponse
	   </div>
	 </div>

	 <div class="right">
	   <input class="submit"
		  type="submit" value="Ajouter" name="captcha_submit" />
	 </div>
				       </form>





<?php
						    }
?>


		  </div>
	    <?php
	  }
	else
	  {
	    ?>
	    <div class="warning">Seul les administrateurs ont acc&eacute;s &agrave; cette page.</div>
	      <?php
	      }
      }
    else
      include('pages/login.php');
  }
?>

