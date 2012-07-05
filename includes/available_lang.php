<?php

include('includes/available_lang_tab.php');

for ($i = 0 ; $i <= $total_lang ; $i++)
{
  echo '<option value="'.$i.'">'.$langs[$i].'</option>'."\n";
}

?>
