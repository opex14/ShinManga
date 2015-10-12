<?php //Root define and cfg read
define('SMG', true);
define('ROOT_DIR', getcwd());
$freespace = disk_free_space("/");
require_once ROOT_DIR."/config.php";
require_once ROOT_DIR."/user.php";
$pagesmax = 0;
if (PB_ENABLED) {
    include_once ROOT_DIR.'/instruments/Pushbullet.php';
	$pb = new Pushbullet(PB_API_KEY);
	}
$auth = new AuthClass();
mb_internal_encoding("UTF-8");

//get vars
$post = (isset($_POST['mode']) && $_POST['mode']) ? $_POST['mode'] : null;
if(isset($_GET['mode'])) {$mode = $_GET['mode'];} else {$mode = 'list';}
if(isset($_GET['id'])) {$getid = $_GET['id'];} else {$getid = null;}
if(isset($_GET['page'])) {$getpg = $_GET['page'];} else {$getpg = null;}
if(isset($_GET['tags'])) {
	$tags = array_map('intval', array_unique(array_filter(explode(",", $_GET['tags']))));
	} else {$tags = null;}
if(isset($_GET['series'])) {
	$series = array_map('intval', array_unique(array_filter(explode(",", $_GET['series']))));
	} else {$series = null;}
if(isset($_GET['authors'])) {
	$authors = array_map('intval', array_unique(array_filter(explode(",", $_GET['authors']))));
	} else {$authors = null;}
if(isset($_GET['search']) && strlen($_GET['search']) <= 25 && strlen($_GET['search']) >= 3) {
	$search = $_GET['search'] ;
	} else {$search = null;}

//BD TABLES
$tab = array(
	"mangas" => CFG_MYSQL_PREFIX.'mangas',
	"tags" => CFG_MYSQL_PREFIX.'tags',
	"series" => CFG_MYSQL_PREFIX.'series',
	"authors" => CFG_MYSQL_PREFIX.'authors',
	"manga_tags" => CFG_MYSQL_PREFIX.'manga_tags',
	"manga_series" => CFG_MYSQL_PREFIX.'manga_series',
	"manga_authors" => CFG_MYSQL_PREFIX.'manga_authors',
	"col" => array(
	"tags" => "tag",
	"authors" => "author",
	"series" => "series",
	),
);



//MySQL

$link = mysqli_connect(CFG_MYSQL_HOST,CFG_MYSQL_USER,CFG_MYSQL_PASSWORD,CFG_MYSQL_DATABASE);
mysqli_set_charset($link, "utf8");
if (!$link) { 
   printf("Невозможно подключиться к базе данных. Код ошибки: %s\n", mysqli_connect_error()); 
   exit; 
} 

//Functions
include_once ROOT_DIR."/instruments/functions.php";

$mangasonsite = CountAll();
if ($mode == 'ajax') {
include_once ROOT_DIR."/instruments/ajax.php";
exit();
} elseif ($mode == 'fakku') {
include_once ROOT_DIR."/instruments/fakku.php";
exit();
} elseif ($post == 'hcr') {
include_once ROOT_DIR."/instruments/hentaichan.php";
exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
 
 
<link rel="stylesheet" href="style/css/bootstrap.min.css">
<link rel="stylesheet" href="style/css/style.css">

<!-- Optional theme 
<link rel="stylesheet" href="style/css/bootstrap-theme.min.css">
-->
<!-- Latest compiled and minified JavaScript -->

<script src="style/js/jquery-1.11.3.min.js"></script>
<script src="style/js/jquery-migrate-1.2.1.min.js"></script>

<script src="style/js/bootstrap.js"></script>

</head>
<body>
 <?php


if (!$auth->isAuth() && CFG_LOCKDOWN) {
include_once ROOT_DIR."/mode/login.php";
exit();
}


if ($auth->isAuth()) {
$user = GetUserData($_SESSION["user_id"]);
}


//MainPage
?>
<?php
if ($mode == 'list') {
	include_once ROOT_DIR."/mode/list.php";
} elseif ($mode == 'edit' || $post == 'setmangadata') {
	include_once ROOT_DIR."/mode/edit.php";
} elseif ($mode == 'add' || $post == 'addmanga') {
	include_once ROOT_DIR."/mode/add.php";
} elseif ($mode == 'read') {
	include_once ROOT_DIR."/mode/read.php";
} else {echo 'Wrong Mode!';}

mysqli_close($link);
//End
?>
</body>
</html>
