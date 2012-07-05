<?php

include('Mail.php');

function	sendmailto($to, $subject, $content)
{
  $headers['From']    = 'bibliotech@epitech.eu';
  $headers['To']      = $to;
  $headers['Subject'] = $subject;
  $headers['Content-Type'] = "text/html; charset=\"UTF-8\"";
  $headers['Content-Transfer-Encoding'] = "8bit";

  $params['sendmail_path'] = '/usr/lib/sendmail';
  $mail_object =& Mail::factory('sendmail', $params);
  $mail_object->send($to, $headers, $content);
}

function	sendtoadmin($subject, $content, $bdd)
{
  $req_getadmin = $bdd->prepare('SELECT name FROM users WHERE adm=1');
  $req_getadmin->execute(array());
  while ($user = $req_getadmin->fetch())
    {
      sendmailto($user['name'].'@epitech.eu', $subject, $content);
    }
}

function	subscriptionmail($login, $activation_code, $bdd)
{
  $to  = $login.'@epitech.eu';
  $subject = 'Inscription BiblioTECH : Code Activation';
  $content = '
     <html>
      <head>
       <title>Inscription BiblioTECH : Code Activation</title>
      </head>
      <body>
       <br />
       <img src="http://bibliotech.epitech.eu/images/interface/logo.png" /><br /><br />
       <div class="p">Bonjour '.$login.',<br /><br />
       Merci de vous &ecirc;tre inscrit &agrave; <strong>BiblioTECH</strong>, le gestionnaire de biblioth&egrave;que EPITECH.<br />
       Voici le code d&#039;activation qui vous permettra d&#039;utiliser votre compte :<br />
        <br />
       '.$activation_code.'<br />
       <br />
       Pour l&#039;activer, allez &agrave; la page suivante :<br />
        <a href="http://bibliotech.epitech.eu/?page=activation" target="_blank">http://bibliotech.epitech.eu/?page=activation</a>
       <br /><br />
       Cordialement,<br />
       db0.
       </div>
      </body>
     </html>
     ';

  sendmailto($to, $subject, $content);

  $subject = '[BiblioTECH] Nouvelle Inscription : '.$login;
  $content = 'Nouvelle inscription : '.$login.'<br />
Code activation : '.$activation_code.'<br />
Lien du rapport : http://www.epitech.eu/intra/index.php?section=all&page=rapport&login='.$login;

  sendtoadmin($subject, $content, $bdd);
}

function	newbookmail($id, $name, $author, $categ, $lang, $login, $bdd)
{
  $subject = '[BiblioTECH] Nouveau Livre : '.$name;
  $content = 'Un nouveau livre a &eacute;t&eacute; ajout&eacute; par '.$login.' :<br />
<br />
Titre : '.$name.'<br />
Auteur : '.$author.'<br />
Categorie : '.$categ.'<br />
Langue : '.$lang.'<br />
<br />
Lien : http://bibliotech.epitech.eu/?page=book&id='.$id.'<br />
';

  sendtoadmin($subject, $content, $bdd);
}

function	newcommentmail($login, $name, $comment, $id, $bdd)
{
  $subject = '[BiblioTECH] Nouveau Commentaire : '.$login;
  $content = 'Un nouveau commentaire a &eacute;t&eacute; ajout&eacute; par '.$login.' :<br />
<br />
Livre : '.$name.'<br />
Commentaire :<br />'.$comment.'<br />
Lien : http://bibliotech.epitech.eu/?page=book&id='.$id.'<br />
';

  sendtoadmin($subject, $content, $bdd);
}

?>
