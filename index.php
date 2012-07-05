<?php

session_start();

$_SESSION['on_page'] = true;

include('includes/sql.php');
include('includes/header.php');
include('includes/menu.php');

$available_pages = array('home',
			 'book',
			 'user',
			 'search',
			 'allbooks',
			 'news',
			 'comments',
			 'marks',
			 'newbook',
			 'newpdf',
			 'editbook',
			 'subscription',
			 'activation',
			 'login',
			 'logout',
			 'account',
			 'accountparam',
			 'admin',
			 'thanks',
			 'faq');

if (isset($_GET['page']) &&
    in_array($_GET['page'], $available_pages) &&
    file_exists('pages/'.$_GET['page'].'.php'))
  include_once('pages/'.$_GET['page'].'.php');
else
  include('pages/home.php');

include('includes/footer.php');

$_SESSION['on_page'] = false;
