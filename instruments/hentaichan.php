<?php
if (CFG_FAKKU_SECRET_CHECK) {
    if (isset($_POST['secret']) && !empty($_POST['secret'])) {
        if ($_POST['secret'] != 'frnmAqgm14afCfvwqvAqf22AF') {echo 'Wrong secret'; exit;}
    } else {echo 'Secret is not set!'; exit;}}
	
if (!isset($_POST['step'])) die('step');
$step = intval($_POST['step']);
$tempfile = 'instruments/mid.tmp';
	

	
	
if ($step == 1) {
	if (!isset($_POST['tags']) || !isset($_POST['title']) || !isset($_POST['artists']) || !isset($_POST['series'])) die('wrong post');
	$similars = CheckDupe($_POST['title']);
	if (!empty($similars)) {
		echo 'Same as '.$similars;
		exit;
	}
	$mid = NewManga($_POST['title']);
	$tags = str_replace(' ', '_', $_POST['tags']);
	$postData = array(
			"id" => $mid,
			"series" => $_POST['series'],
			"authors" => $_POST['artists'],
			"tags" => $tags,
			"lang" => "Русский",
		);
		MysqlPost($postData);
		
		file_put_contents($tempfile, $mid);
		echo 'ok';
		
} elseif ($step == 2) {
	if (!isset($_POST['pages'])) {die('no pages');}
	if (!file_exists($tempfile)) {die('no temp file');}
	
	$imgs = explode(',', $_POST['pages']);
	
	$mid = file_get_contents($tempfile);
	unlink($tempfile);
	$fpath = ROOT_DIR.'/manga/'.$mid.'/';
	
	foreach ($imgs as $url) {
		$path_parts = pathinfo($url);
		grab_image($url, $fpath.$path_parts['basename']);
	}
	
	SetInitial($mid);
	
	echo 'ok';
	if (PB_ENABLED) {$pb->pushLink(PB_DEVICE_ID, 'Загрузка завершена!', 'http://shin.ayase.ru:1337/?mode=read&id='.$mid, 'Манга #'.$mid.' успешно импортироваена в '.CFG_SITENAME.' c #HC.');}
} else {
	die('wrong step');
}

?>