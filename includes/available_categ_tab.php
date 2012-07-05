<?php

$req_categ = $bdd->prepare('SELECT * FROM categories');

$req_categ->execute();

$categories = array();

while ($categ = $req_categ->fetch())
  $tmp[$categ['categorie']] = $categ;

ksort($tmp);

foreach ($tmp as $categ)
  $categories[] = $categ;

$total_categ = sizeof($categories);

function	getcategbyid($id)
{
  global $categories;

  foreach ($categories as $categ)
  {
     if ($categ['id'] == $id)
       return ($categ['categorie']);
  }
  return '';
}

?>
