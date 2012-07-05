<?php

include('includes/available_categ_tab.php');

for ($i = 0 ; $i <= $total_categ ; $i++)
{
  if ($categories[$i]['categorie'] == 'Autre')
    $autre = $i;
  else
    echo '<option value="'.$categories[$i]['id'].'">'.$categories[$i]['categorie'].'</option>'."\n";
}


//getcategbyid($book['id_categ']);

?>
