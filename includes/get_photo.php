<?php
function		get_photo($login)
{
  if (file_exists('images/photos/'.$login.'.png'))
    echo 'images/photos/'.$login.'.png';
  elseif (file_exists('images/photos/'.$login.'.jpg'))
    echo 'images/photos/'.$login.'.jpg';
  elseif (($test_img = @fopen('http://www.epitech.eu/intra/photos/'.$login.'.jpg', 'r')))
    echo 'http://www.epitech.eu/intra/photos/'.$login.'.jpg';
  else
    echo 'images/books/nopic.png';
}
?>
