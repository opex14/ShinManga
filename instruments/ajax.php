<?php
    header("content-type: text/javascript");
    if(isset($_GET['id']) && isset($_GET['callback']))
    {
		$dataloc = ROOT_DIR.'/manga/'.$getid;
		$files = GetRes($dataloc);
        $maxpage = count($files);
        echo $_GET['callback']. '(' . json_encode($files) . ');';
    }
?>