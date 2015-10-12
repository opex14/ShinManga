<?php

    header("content-type: text/javascript");
	
    if (CFG_FAKKU_SECRET_CHECK) {
    if (isset($_GET['secret']) && !empty($_GET['secret'])) {
        if ($_GET['secret'] != 'frnmAqgm14afCfvwqvAqf22AF') {echo 'Wrong secret'; exit;}
    } else {echo 'Secret is not set!'; exit;}}
    
    if (CFG_FAKKU_IP_CHECK) {
    $client_ip = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : 'none';
    $allowed_ips = array("192.168.1.4", "192.168.1.2");
    
    if (!in_array($client_ip, $allowed_ips)) {
    echo 'IP is not allowed.';
    exit;
    }}
	$intags = (isset($_GET['tags'])) ? $_GET['tags'] : null ;
	$intitle = (isset($_GET['title'])) ? $_GET['title'] : null ;
	$inpages = (isset($_GET['pages'])) ? intval($_GET['pages']) : null ;
	$inurl = (isset($_GET['url'])) ? $_GET['url'] : null ;
	$inartisis = (isset($_GET['artists'])) ? $_GET['artists'] : null ;
	$inseries = (isset($_GET['series'])) ? $_GET['series'] : null ;
	
	$urls = array();
	
    if (!empty($inurl) && $inpages > 2 && strlen($intitle) > 1) {
	$similars = CheckDupe($intitle);
	if (!empty($similars)) {
		echo 'Same as '.$similars;
		exit;
	}
	
	$mid = NewManga($intitle);
	$fpath = ROOT_DIR.'/manga/'.$mid.'/';
	
	$ftags = $intags;
	
	for ($i = 1; $i <= $inpages; $i++) {
		$emg = str_pad($i, 3, "0", STR_PAD_LEFT);
		$url = 'http:'.$inurl.'images/'.$emg.'.jpg';
		grab_image($url, $fpath.$emg.'.jpg');
	}
		SetInitial($mid);
		
		$postData = array(
			"id" => $mid,
			"series" => $inseries,
			"authors" => $inartisis,
			"tags" => $intags,
			"lang" => "Английский",
			"mode" => "fakku",
		);
		
		MysqlPost($postData);
		
		echo 'ok';
		if (PB_ENABLED) {$pb->pushLink(PB_DEVICE_ID, 'Загрузка завершена!', 'http://shin.ayase.ru:1337/?mode=read&id='.$mid, 'Манга #'.$mid.' успешно импортироваена в '.CFG_SITENAME.' c #FK.');}
		
		} else {
			die('not enough data');
		}


?>