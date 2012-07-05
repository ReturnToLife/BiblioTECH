<?php
include_once('conf.php');
try
{
  $bdd = new PDO('mysql:host='.$mysql_host.';dbname='.$mysql_dbname,
		 $mysql_login, $mysql_pass);
}
catch (Exception $e)
{
  die('Erreur : ' . $e->getMessage());
}

function protect($string)
{
  return (htmlspecialchars(stripslashes($string)));
}

function toalphanum($str)
{
  $str = strtolower(trim($str));
  $str = preg_replace('/[^a-z0-9-]/', '-', $str);
  $str = preg_replace('/-+/', "-", $str);
  return $str;
}

function    min_lenght_($str, $len, $st)
{
  if (strlen($str) < $len)
    return ($str);
  elseif (preg_match("/(.{1,$len})\s./ms", $str, $match))
    {
      if ($st)
	return ($match[1]."...");
      else
	return ($match[1]);
    }
  else
    {
      if ($st)
	return (substr($str, 0, $len)."...");
      else
	return (substr($str, 0, $len));
    }
}

function min_lenght($str, $len)
{
  return (min_lenght_($str, $len, true));
}

function	randomstring($length = "")
{
  $code = md5(uniqid(rand(), true));
  if ($length != "")
    return substr($code, 0, $length);
  else
    return $code;
}

function	showmark($mark)
{
  if ($mark != -1)
    {
    if ($mark <= 1)
      {
	?>
	<img src="images/interface/mark/star1.gif" alt="1 &eacute;toile" />
	<?php
      }
    elseif ($mark <= 2)
      {
	?>
	<img src="images/interface/mark/star2.gif" alt="2 &eacute;toiles" />
	<?php
      }
    elseif ($mark <= 3)
      {
	?>
	<img src="images/interface/mark/star3.gif" alt="3 &eacute;toiles" />
	<?php
      }
    elseif ($mark <= 4)
      {
	?>
	<img src="images/interface/mark/star4.gif" alt="4 &eacute;toiles" />
	<?php
      }
    else
      {
	?>
	<img src="images/interface/mark/star5.gif" alt="5 &eacute;toiles" />
	<?php
      }
    }
}


function ByteSize($bytes) 
{
  $size = $bytes / 1024;
  if($size < 1024)
    {
      $size = number_format($size, 2);
      $size .= ' KB';
    } 
    else 
      {
        if($size / 1024 < 1024) 
	  {
            $size = number_format($size / 1024, 2);
            $size .= ' MB';
	  } 
        else if ($size / 1024 / 1024 < 1024)  
	  {
            $size = number_format($size / 1024 / 1024, 2);
            $size .= ' GB';
	  } 
      }
  return $size;
}


function	findtextinfile($text, $filename)
{
  $files = file($filename);
  return (in_array($text."\n", $files));
}



?>
