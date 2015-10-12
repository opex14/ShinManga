<?php
define('ROOT_DIR', dirname(getcwd()));
require_once ROOT_DIR."/config.php";

 if (isset($_GET['id']) && isset($_GET['img']) && !empty($_GET['img']) && !empty($_GET['id'])) {
	 
 $id = intval($_GET['id']);
 $img = $_GET['img'];
 $height = CFG_PREVIEW_HEIGHT;
 $path_parts = pathinfo($img);
 
 if(!preg_match('/^[A-Za-z0-9-_ ]+\.'.$path_parts['extension'].'$/', $img)) {
	die('wrong file name');
 }
 
$manga_dir = ROOT_DIR.'/manga/'.$id.'/';
if (!file_exists($manga_dir)) die('manga not exists');

$thumb_dir = $manga_dir.'thumbs/';

if (!file_exists($thumb_dir)) {
	mkdir($thumb_dir);
}
	
$main_file = $manga_dir.$img;
if (!file_exists($main_file)) die('manga not exists');
$thumb_file = $thumb_dir.$img;

if (file_exists($thumb_file)) {
Render($thumb_file);
} else {
	make_thumb($main_file, $thumb_file, CFG_PREVIEW_HEIGHT);
	Render($thumb_file);
}

} else {
	die('var not set');
}

 function Render($file) {
	header('Content-Type: '.mime_content_type($file) );
	header('Content-Length: ' .(string)(filesize($file)) );
	echo file_get_contents($file);
	exit;
	 }
function imageCreateFromAny($filepath) { 
    $type = exif_imagetype($filepath); // [] if you don't have exif you could use getImageSize() 
    $allowedTypes = array( 
        1,  // [] gif 
        2,  // [] jpg 
        3,  // [] png 
        6   // [] bmp 
    ); 
    if (!in_array($type, $allowedTypes)) { 
        return false; 
    } 
    switch ($type) { 
        case 1 : 
            $im = imageCreateFromGif($filepath); 
        break; 
        case 2 : 
            $im = imageCreateFromJpeg($filepath); 
        break; 
        case 3 : 
            $im = imageCreateFromPng($filepath); 
        break; 
        case 6 : 
            $im = imageCreateFromBmp($filepath); 
        break; 
    }    
    return $im;  
} 
function make_thumb($src, $dest, $h) {

	/* read the source image */
	$source_image = imageCreateFromAny($src);
	$width = imagesx($source_image);
	$height = imagesy($source_image);
	
	/* find the "desired height" of this thumbnail, relative to the desired width  */
	$w = floor($width * ($h / $height));
	
	/* create a new, "virtual" image */
	$virtual_image = imagecreatetruecolor($w, $h);
	
	/* copy source image at a resized size */
	imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $w, $h, $width, $height);
	
	/* create the physical thumbnail image to its destination */
	imagejpeg($virtual_image, $dest);
}
?>