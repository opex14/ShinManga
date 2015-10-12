<?php
if(!defined('SMG')) die('USE MAIN SCRIPT!');

function getSymbolByQuantity($bytes) {
    $symbols = array('B', 'KB', 'MB', 'GB', 'T', 'P', 'E', 'Z', 'Y');
    $exp = floor(log($bytes)/log(1024));

    return sprintf('%.2f '.$symbols[$exp], ($bytes/pow(1024, floor($exp))));
}

function CountAll() {
	global $link, $tab;
	
	$querry = 'SELECT COUNT(*) as count FROM `'.$tab['mangas'].'`;';
	
	if ($result = mysqli_query($link, $querry)) { 
		$row = mysqli_fetch_assoc($result);
		return intval($row['count']);
	} else {
		return 0;
	}
}
function CheckDupe($title, $pages = 0) {
	global $link, $tab;
	
	$querry = 'SELECT id FROM `'.$tab['mangas'].'` WHERE `title` like "'.$title.'"';
	if ($pages > 0) {$querry .= ' AND `pages` = '.$pages;}
	$querry .= ';';
	
	if ($result = mysqli_query($link, $querry)) { 
		$row = mysqli_fetch_assoc($result);
		if (!empty($row)) {
			return $row['id'];
		} else {
			return null;
		}
	} else {
		return 'mysql error!';
	}
}
function unzipf($src, $dist){
   $zip = new ZipArchive;
     if ($zip->open($src) === true) {
		 $fail = true;
          for($i = 0; $i < $zip->numFiles; $i++) { 
             $entry = $zip->getNameIndex($i);
               if(preg_match('#\.(jpg|jpeg|gif|png)$#i', $entry))
                {
				$fail = false;
                ////This copy function will move the entry to the root of "txt_files" without creating any sub-folders unlike "ZIP->EXTRACTO" function.
                 copy('zip://'.$src.'#'.$entry, $dist.$entry); 
                } 
              }  
			  
             $zip->close();
			 unlink($src);
			 if($fail){return 'fail';}
         return 'ok';
			 
            }
    else{
         return 'fail';
        }
		}
		
function AddMangaFile($post) {
	global $link, $tab, $_FILES;
	if (isset($post['title']) && strlen($post['title']) > 1) {
		mysqli_query($link, 'INSERT INTO `'.$tab['mangas'].'` (`title`) VALUES ("'.trim($post['title']).'");');
		if ($result = mysqli_query($link, 'SELECT LAST_INSERT_ID();')) {
			$row = mysqli_fetch_assoc($result);
				$id = $row['LAST_INSERT_ID()'];
				$path = ROOT_DIR.'/manga/'.$id.'/';
				if (!file_exists($path)) {
					mkdir($path, 0770, true);
				}
				$allowed = array('gif', 'png', 'jpg', 'jpeg', 'zip');
				$fail = true;
				
				foreach ($_FILES['files']['name'] as $f => $name) {
					 $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
					 if(in_array($ext,	$allowed) ) {
					 $fail = false;
					 move_uploaded_file($_FILES["files"]["tmp_name"][$f], $path.$name);
					 if($ext == 'zip'){
						 $uzip = unzipf($path.$name, $path);
						 if($uzip == 'fail') {MysqlPost(array('id' => $id,'deletebt' => 'true','deletecf' => 'Yes'));return 'fail';}
					 }
					}
					
					if($fail) {
					MysqlPost(array(
					'id' => $id,
					'deletebt' => 'true',
					'deletecf' => 'Yes'));
					return 'fail';
					}
				}
				
				SetInitial($id);
		
				return $id;
		}
		
		
	}
	
}

function NewManga($title) {
	global $link, $tab, $_FILES;
	if (strlen($title) > 1) {
		mysqli_query($link, 'INSERT INTO `'.$tab['mangas'].'` (`title`) VALUES ("'.trim($title).'");');
		if ($result = mysqli_query($link, 'SELECT LAST_INSERT_ID();')) {
			$row = mysqli_fetch_assoc($result);
				$id = $row['LAST_INSERT_ID()'];
				$path = ROOT_DIR.'/manga/'.$id.'/';
				if (!file_exists($path)) {
					mkdir($path, 0770, true);
				}
				return $id;
		}
		
		
	}
	
}
function grab_image($url,$saveto){
    $ch = curl_init($url);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    $raw=curl_exec($ch);
    curl_close ($ch);
    if(file_exists($saveto)){
        unlink($saveto);
    }
    $fp = fopen($saveto,'x');
    fwrite($fp, $raw);
    fclose($fp);
}
function MakePages($page, $pagescount, $requrl) {
if ($pagescount > 1) {
$extra['num'] = 2;
$extra['pages'] = $extra['num']*2+1;
$output = '';
$imax = ($pagescount < $extra['pages']) ? $pagescount : $extra['pages'];
$prev = $page - 1;
$next = $page + 1;
$maxif = $pagescount-$extra['num'];

if ($page != 1) {
$output .= '<li><a href="?'.$requrl.'1" aria-label="Previous"><span aria-hidden="true"><b>1</b></span></a></li>';
$output .= '<li><a href="?'.$requrl.$prev.'" aria-label="Previous"><span aria-hidden="true" class="glyphicon glyphicon-menu-left"></span></a></li>';
}

if ($page <= $extra['num'] || $pagescount < $extra['pages']) {

		for ($i=1; $i<=$imax; $i++) {
            $active = ($page == $i) ? ' class="active"' : '';
			$output .= '<li'. $active .'><a href="?'.$requrl.$i.'">'.$i.'</a></li>';
		}

}
elseif ($page > $extra['num'] && $page <= $maxif) {
        $minpage = $page - $extra['num'];
		$maxpage = $page + $extra['num'];
        		for ($i=$minpage; $i<= $maxpage; $i++) {
            $active = ($page == $i) ? ' class="active"' : '';
			$output .= '<li'. $active .'><a href="?'.$requrl.$i.'">'.$i.'</a></li>';
		}
}
elseif ($page > $maxif) {
        
		for ($i=$pagescount-$extra['pages']+1; $i<=$pagescount; $i++) {
            $active = ($page == $i) ? ' class="active"' : '';
			$output .= '<li'. $active .'><a href="?'.$requrl.$i.'">'.$i.'</a></li>';
		}

}

if ($page != $pagescount) {
$output .= '<li><a href="?'.$requrl.$next.'" aria-label="Next"><span aria-hidden="true" class="glyphicon glyphicon-menu-right"></span></a></li>';
$output .= '<li><a href="?'.$requrl.$pagescount.'" aria-label="Next"><span aria-hidden="true"><b>'.$pagescount.'</b></span></a></li>';
}
return $output;
} else {
return null;
}
}

function MysqlPost($post) {
	global $tab, $link;
$i = array();
$i['id'] = (isset($post['id']) && !empty($post['id'])) ? $post['id'] : null;
if ($i['id'] == null) {return 'id_fail';} 
$i['title'] = (isset($post['title']) && !empty($post['title'])) ? trim($post['title']) : null;
$i['desc'] = (isset($post['description']) && !empty($post['description'])) ? trim($post['description']) : null;
$i['lang'] = (isset($post['lang']) && !empty($post['lang'])) ? trim($post['lang']) : null;
$i['preview'] = (isset($post['preview']) && !empty($post['preview'])) ? trim($post['preview']) : null;
$i['preview2'] = (isset($post['preview2']) && !empty($post['preview2'])) ? trim($post['preview2']) : null;
$i['authors'] = (isset($post['authors']) && !empty($post['authors'])) ? trim($post['authors']) : null;
$i['series'] = (isset($post['series']) && !empty($post['series'])) ? trim($post['series']) : null;
$i['tags'] = (isset($post['tags']) && !empty($post['tags'])) ? trim(str_replace(',', ' ', $post['tags'])) : null;
$i['mode'] = (isset($post['mode']) && !empty($post['mode'])) ? $post['mode'] : null;
$i['deletecf'] = (isset($post['deletecf']) && !empty($post['deletecf'])) ? $post['deletecf'] : null;
$i['deletebt'] = (isset($post['deletebt']) && !empty($post['deletebt'])) ? $post['deletebt'] : null;

if ($i['deletebt']){
if ($i['deletecf'] == 'Yes') {
$querry = 'DELETE FROM '.$tab['mangas'].' WHERE id = "'.$i['id'].'";';
if (mysqli_query($link, $querry)){
 rrmdir(ROOT_DIR.'/manga/'.$i['id']);
}
return 'del';
} else {
return 'deltext';
} 
} else {

if (!empty($i['title']) || !empty($i['desc']) || !empty($i['lang']) || !empty($i['preview']) || !empty($i['preview2']))
{
$querry = 'UPDATE '.$tab['mangas'].' SET ';
$imp = array();

if (!empty($i['title'])){$imp[] = 'title="'.mysqli_real_escape_string($link, $i['title']).'"';}
if (!empty($i['desc'])){$imp[] = 'description="'.mysqli_real_escape_string($link, $i['desc']).'"';}
if (!empty($i['lang'])){$imp[] = 'lang="'.mysqli_real_escape_string($link, $i['lang']).'"';}
if (!empty($i['preview'])){$imp[] = 'preview="'.mysqli_real_escape_string($link, $i['preview']).'"';}
if (!empty($i['preview2'])){$imp[] = 'preview2="'.mysqli_real_escape_string($link, $i['preview2']).'"';}

$querry .= implode(', ', $imp);

$querry .= ' WHERE id = '.$i['id'].';';
		 mysqli_query($link, $querry);
}

if (!empty($i['tags'])) {
		$first = true;
		$tagarre = array_unique(array_filter(explode(" ", $i['tags'])));
					foreach ($tagarre as $meta) {
				SetMeta('tags', $i['id'], $meta, $first, $i['mode']);
				$first = false;
					}	
		}
		
if (!empty($i['series'])) {
		$first = true;
		$tagarre = array_unique(array_filter(explode(",", $i['series'])));
					foreach ($tagarre as $meta) {
				SetMeta('series', $i['id'], $meta, $first, $i['mode']);
				$first = false;
					}	
		}
		
if (!empty($i['authors'])) {
		$first = true;
		$tagarre = array_unique(array_filter(explode(",", $i['authors'])));
					foreach ($tagarre as $meta) {
				SetMeta('authors', $i['id'], $meta, $first, $i['mode']);
				$first = false;
					}	
		}

//MetaCleaner();
        
return 'OK';
}
}

function SetMeta($mode, $manggid, $tagin, $first = true, $from = null){
	global $tab, $link;
	$mode = mysqli_real_escape_string($link, $mode);
    $mystag = mysqli_real_escape_string($link, $tagin);
	$metaname = ($mode == 'series') ? $mode : substr($mode, 0, -1);
	$metamanga = 'manga_'.$mode;
	
	if ($from == 'fakku') {
        if ($request = mysqli_query($link, 'SELECT '.$metaname.' FROM `'.$tab[$mode].'` where fakku = "'.$mystag.'"')) {
		$rower = mysqli_fetch_assoc($request);
        if (isset($rower[$metaname]) && !empty($rower[$metaname])) {$tagnm = $rower[$metaname];
        } else {$tagnm = $tagin;}
        } else {$tagnm = $tagin;}
	} else {$tagnm = $tagin;}
	
	
	if (mysqli_query($link, '
	INSERT INTO `'.$tab[$mode].'` (`'.$metaname.'`) VALUES ("'.$tagnm.'")
	ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id), `'.$metaname.'`="'.$tagnm.'";')) { 
			$result = mysqli_query($link, 'SELECT LAST_INSERT_ID();');
			$row = mysqli_fetch_assoc($result);
			$tagid = $row['LAST_INSERT_ID()'];
			if($first){mysqli_query($link, 'DELETE FROM `'.$tab[$metamanga].'` WHERE `manga_id` = '.$manggid.';');}
			mysqli_query($link, 'INSERT INTO `'.$tab[$metamanga].'` (`manga_id`,`'.$mode.'_id`) VALUES ("'.$manggid.'", "'.$tagid.'");');
				  
			 mysqli_free_result($result); 
			return 'OK';
			} else {
			return 'Mysql Error';
			}
}

function rrmdir($dir) { 
   if (is_dir($dir)) { 
     $objects = scandir($dir); 
     foreach ($objects as $object) { 
       if ($object != "." && $object != "..") { 
         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object); 
       } 
     } 
     reset($objects); 
     rmdir($dir); 
   } 
 }
function SetPages($id, $pages) {
	global $tab, $link;
		mysqli_query($link, 'UPDATE '.$tab['mangas'].' SET pages = '.$pages.' WHERE id = '.$id.';');
}
function SetInitial($id) {
	global $tab, $link;
	
		$dataloc = ROOT_DIR.'/manga/'.$id.'/';
		$files = GetRes($dataloc);
		$maxpage = count($files);
		$pr2d = ceil($maxpage/2);
		$querry = 'UPDATE '.$tab['mangas'].' SET preview="'.mysqli_real_escape_string($link, $files[0]).'",preview2="'.mysqli_real_escape_string($link, $files[$pr2d]).'",pages='.$maxpage.' WHERE id = '.$id.';';
		
		mysqli_query($link, $querry);
}

function AddToQuerry($meta, $id) {
global $tags, $series, $authors, $search;
    if (empty($tags) && empty($series) && empty($authors)) {
        return $meta.'='.$id;
    }
	
    $output = 'mode=list';
    
	$tagsvar = (empty($tags)) ? null : implode(',', $tags);
	$seriesvar = (empty($series)) ? null : implode(',', $series);
	$authorsvar = (empty($authors)) ? null : implode(',', $authors); 
	
	
    if ($meta == 'tags') {
            $output .= '&tags='.$tagsvar.','.$id;
            if (!empty($seriesvar)) {$output .= '&series='.$seriesvar;}
            if (!empty($authorsvar)) {$output .= '&authors='.$authorsvar;}
            }
    elseif ($meta == 'series') {
            if (!empty($tagsvar)) {$output .= '&tags='.$tagsvar;}
            $output .= '&series='.$seriesvar.','.$id;
            if (!empty($authorsvar)) {$output .= '&authors='.$authorsvar;}
            }
    elseif ($meta == 'authors') {
            if (!empty($tagsvar)) {$output .= '&tags='.$tagsvar;}
            if (!empty($seriesvar)) {$output .= '&series='.$seriesvar;}
            $output .= '&authors='.$authorsvar.','.$id;
            }
    return $output;
}

function MetaHead() {
    global $tags, $series, $authors, $search, $mangasonsite;
    if ($tags == null && $series == null && $authors == null && $search == null) {
    return 'Весь список <span class="badge">'.$mangasonsite.'</span>';
    }
    $meta = '<table class="table"><tr>';
    
    if ($tags != null){
    $meta .= '<td>Метки:<b>';
        foreach ($tags as $tag) {
        $meta .= ' '.IdRead('tags', $tag);
        }
    $meta .= '</b></td>';
    }
    
    if ($series != null){
    $meta .= '<td>Серии:<b>';
        foreach ($series as $tag) {
        $meta .= ' '.IdRead('series', $tag);
        }
    $meta .= '</b></td>';
    }
    
    if ($authors != null){
    $meta .= '<td>Авторы:<b>';
        foreach ($authors as $tag) {
        $meta .= ' '.IdRead('authors', $tag);
        }
    $meta .= '</b></td>';
    }
    
     if ($search != null){
    $meta .= '<td>Поиск: <b>';
    $meta .= $search;
    $meta .= '</b></td>';
    }
    
    $meta .= '</tr></table>';
    return $meta;
}
function MetaCleaner(){
	global $tab, $link;
	
	$tags = GetAllTags('tags');
	$authors = GetAllTags('authors');
	$series = GetAllTags('series');
	
	foreach($tags as $meta) {
		if ($result = mysqli_query($link, 'SELECT COUNT(*) FROM '.$tab['manga_tags'].' WHERE tags_id = '.$meta['id'].';')) {
			$row = mysqli_fetch_assoc($result);
			if ($row['COUNT(*)'] <= 0) {
				if (empty($meta['fakku'])){
				mysqli_query($link, 'DELETE FROM '.$tab['tags'].' WHERE id = "'.$meta['id'].'";');}
			}
			}
	}
	
	foreach($authors as $meta) {
		if ($result = mysqli_query($link, 'SELECT COUNT(*) FROM '.$tab['manga_authors'].' WHERE authors_id = '.$meta['id'].';')) {
			$row = mysqli_fetch_assoc($result);
			if ($row['COUNT(*)'] <= 0) {
				if (empty($meta['fakku'])){
				mysqli_query($link, 'DELETE FROM '.$tab['authors'].' WHERE id = "'.$meta['id'].'";');}
			}
			}
	}
	
	foreach($series as $meta) {
		if ($result = mysqli_query($link, 'SELECT COUNT(*) FROM '.$tab['manga_series'].' WHERE series_id = '.$meta['id'].';')) {
			$row = mysqli_fetch_assoc($result);
			if ($row['COUNT(*)'] <= 0) {
				if (empty($meta['fakku'])){
				mysqli_query($link, 'DELETE FROM '.$tab['series'].' WHERE id = "'.$meta['id'].'";');}
			}
			}
	}
	
}
function GetAllTags($mode = 'tags', $all = 'yes') {
global $tab, $link;
$xraw = array();

		if ($result = mysqli_query($link, 'SELECT * FROM '.$tab[$mode].' ORDER BY '.$tab["col"][$mode].';')) { 
		
        if ($all == 'no') {
         while( $row = mysqli_fetch_assoc($result) ){
             $seres = mysqli_query($link, 'SELECT COUNT(*) FROM '.$tab['manga_'.$mode].' WHERE '.$mode.'_id = '.$row['id'].';');
             $rowse = mysqli_fetch_assoc($seres); mysqli_free_result($seres); 
             if (intval($rowse['COUNT(*)']) > 0) {$xraw[] = $row; }}	
             } else {
		 while( $row = mysqli_fetch_assoc($result) ){$xraw[] = $row;} 
		 mysqli_free_result($result); 
             } 
		}
        return $xraw;
}

function GetUserData($uid) {
global $tab, $link;
$user = null;
		if ($result = mysqli_query($link, 'SELECT user_login, admin FROM '.CFG_MYSQL_PREFIX.'users WHERE user_id = '.$uid.' LIMIT 1;')) { 
		 $user = mysqli_fetch_assoc($result);
		 mysqli_free_result($result); 
		}
        return $user;
}

function GetRes($dir) {
$dirs = scandir($dir);
sort($dirs, SORT_NATURAL);
$fs = array();
 foreach ( $dirs as $ff ){
	 if ( $ff != '.' && $ff != '..' ){
		 if( substr($ff, -4) == '.jpg' || substr($ff, -5) == '.jpeg' || substr($ff, -4) == '.png' )
		 {
			 $fs[] .= $ff;
		 }
	 }
 }
 return $fs;
 }
 
function IdRead($mode, $id){
if ($mode == 'tags' || $mode == 'series' || $mode == 'authors') {
if ($mode == 'series'){
$rowname = $mode;
} else {$rowname = substr($mode, 0, -1);}

global $tab, $link;

		if ($result = mysqli_query($link, 'SELECT * FROM '.$tab[$mode].' WHERE id = '.$id.';')) { 
		$row = mysqli_fetch_assoc($result);
		 
		 mysqli_free_result($result); 
		}
        return $row[$rowname];
        } else {return null;}
}

function MetaSelector($mode, $id = null) {
global $tab, $link, $start_from;

if ($mode == 'list') {
$querrysel = 'SELECT id FROM '.$tab['mangas'];
} elseif ($mode == 'search') {
$querrysel = 'SELECT id FROM '.$tab['mangas'].' WHERE `title` like "%'.$id.'%"';
} else {
if ($mode == 'tags') {
$manga_meta = 'manga_tags'; $meta_id = 'tags_id';
} elseif ($mode == 'series') {
$manga_meta = 'manga_series'; $meta_id = 'series_id';
} elseif ($mode == 'authors') {
$manga_meta = 'manga_authors'; $meta_id = 'authors_id';
} elseif ($mode == 'authors') {
$manga_meta = 'manga_authors'; $meta_id = 'authors_id';
}  else {return null;}

$querrysel = 'SELECT id FROM `'.$tab[$manga_meta].'` AS q
			LEFT JOIN `'.$tab['mangas'].'` AS i ON (
				q.manga_id = i.id
			) WHERE '.$meta_id.' = '.$id;
} 
$querrysel .=  ' ORDER BY posted DESC;';
$mangas = null;
 if ($result = mysqli_query($link, $querrysel)) { 
     while( $manga = mysqli_fetch_assoc($result) ){ 
        $mangas[$manga['id']] = $manga['id'];
	 }
     mysqli_free_result($result); 
 }
 return $mangas;

}

function ListManga($tags, $series, $authors, $search){
global $tab, $link, $start_from, $pagesmax;
$mangalst = array();
if ($tags == null && $series == null && $authors == null && $search == null) {
    $mangalst = MetaSelector('list');
}
$first = True;

if ($search != null) {
        
        $mangalst = MetaSelector('search', mysqli_real_escape_string($link, $search));
}

if (count($tags) > 0) {
    foreach ($tags as $fetag) {
        if ($first) { $first = False;
        $mangalst = MetaSelector('tags', $fetag);
        } else {
            $selected = MetaSelector('tags', $fetag);
                $mngx = null;
                foreach ($selected as $sel) {
                    foreach ($mangalst as $mgls) {
                        if ($sel == $mgls) {
                            $mngx[$sel] = $sel;
                        }
                    }
                }
                $mangalst = $mngx;
            }
    }
}


if ($authors != null) {
    foreach ($authors as $fetag) {
        if ($first) { $first = False;
        $mangalst = MetaSelector('authors', $fetag);
        } else {
            $selected = MetaSelector('authors', $fetag);
                $mngx = null;
                foreach ($selected as $sel) {
                    foreach ($mangalst as $mgls) {
                        if ($sel == $mgls) {
                            $mngx[$sel] = $sel;
                        }
                    }
                }
                $mangalst = $mngx;
            }
    }
}

if ($series != null) {
    foreach ($series as $fetag) {
        if ($first) { $first = False;
        $mangalst = MetaSelector('series', $fetag);
        } else {
            $selected = MetaSelector('series', $fetag);
                $mngx = null;
                foreach ($selected as $sel) {
                    foreach ($mangalst as $mgls) {
                        if ($sel == $mgls) {
                            $mngx[$sel] = $sel;
                        }
                    }
                }
                $mangalst = $mngx;
            }
    }
}
 if ($mangalst == null) {return null;}
$mangapg = array_slice($mangalst, $start_from, CFG_ONPAGE);
$pagesmax = count($mangalst);
$mangas = array();
    foreach ($mangapg as $mangaid) {    
        $mangas[] = MangaData($mangaid);
        }
 return $mangas;
 }

function GetMangaData($tagid) {
	
}
 
function GetData($mode,$id){
if ($mode == 'tags' || $mode == 'series' || $mode == 'authors') {
$table_manga = 'manga_'.$mode;$table_data = $mode;$table_id = $mode.'_id';
if ($mode == 'series'){
$rowname = $mode;
} else {$rowname = substr($mode, 0, -1);}
global $tab, $link;
     $mnggs = array();
		  if ($result2 = mysqli_query($link, 'SELECT * FROM '.$tab[$table_manga].' WHERE manga_id = '.$id.';')) { 
		 while( $row3 = mysqli_fetch_assoc($result2) ){ 
			
			if ($result3 = mysqli_query($link, 'SELECT * FROM '.$tab[$table_data].' WHERE id = '.$row3[$table_id].';')) { 
			 while( $row4 = mysqli_fetch_assoc($result3) ){ 
				$id = $row4['id'];
				$mnggs[] = array("id" => $row4['id'], "meta" => $row4[$rowname]);
			 }
			 
			 mysqli_free_result($result3); 
			 }}
			 
			 usort($mnggs, function ($a, $b) {
				if ($a['meta'] == $b['meta']) return 0;
				return ($a['meta'] < $b['meta']) ? -1 : 1;
			});
		 
		 mysqli_free_result($result2); 
		}
        return $mnggs;
        } else {return null;}
        }

function MangaData($id){
global $tab, $link;
 if ($result = mysqli_query($link, 'SELECT * FROM '.$tab['mangas'].' WHERE id = '.$id.';')) { 
     $mangaonpage = mysqli_fetch_assoc($result);
     mysqli_free_result($result); 
 }
 if (empty($mangaonpage)) {return null;};
	if ($mangaonpage['preview'] == null) {
		$previewmg = CFG_SITEURL.'style/img/no_preview.jpg';
	} else {
		$previewmg = CFG_SITEURL.'instruments/preview.php?id='.$mangaonpage['id'].'&img='.$mangaonpage['preview'];
	}
	if ($mangaonpage['preview2'] == null) {
		$previewmg2 = CFG_SITEURL.'style/img/no_preview.jpg';
	} else {
		$previewmg2 = CFG_SITEURL.'instruments/preview.php?id='.$mangaonpage['id'].'&img='.$mangaonpage['preview2'];
	}
		return array(
			"title" => $mangaonpage['title'],
			"id" => $mangaonpage['id'],
			"lang" => $mangaonpage['lang'],
			"pages" => $mangaonpage['pages'],
			"description" => $mangaonpage['description'],
			"posted" => $mangaonpage['posted'],
			"preview" => $mangaonpage['preview'],
			"preview2" => $mangaonpage['preview2'],
			"preview_url" => $previewmg,
			"preview_url2" => $previewmg2,
			"tags" => GetData('tags',$mangaonpage['id']),
			"series" => GetData('series',$mangaonpage['id']),
			"authors" => GetData('authors',$mangaonpage['id']),
				);
                }
?>