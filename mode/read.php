<?php if(!defined('SMG')) die('USE MAIN SCRIPT!');

$manga = MangaData($getid);

    $elmseries = array();
    $elmauthors = array();
    $elmtags = array();
    foreach ($manga['series'] as $ent) {
    $elmseries[] = '<a href="?mode=list&series='.$ent['id'].'">'.$ent['meta'].'</a>';
    }
    foreach ($manga['authors'] as $ent) {
    $elmauthors[] = '<a href="?mode=list&authors='.$ent['id'].'">'.$ent['meta'].'</a>';
	}
    foreach ($manga['tags'] as $ent) {
		if ($ent['id'] != 260) {
    $elmtags[] = '<a class="tags" href="?mode=list&tags='.$ent['id'].'">'.$ent['meta'].'</a>';
    } 
	if ($ent['id'] == 260 && $user['user_login'] == 'Opex') {
    $elmtags[] = '<a class="tags" href="?mode=list&tags='.$ent['id'].'">'.$ent['meta'].'</a>';
	}
		
	}

$dataloc = ROOT_DIR.'/manga/'.$getid;
if(!file_exists($dataloc)) {die('Папка манги с ID '.$manga['id'].'не найдена!');}
$files = GetRes($dataloc);
$pgurl = CFG_SITEURL.'manga/'.$getid.'/';
$maxpage = count($files);
if ($manga['pages'] != $maxpage) {SetPages($manga['id'], $maxpage);}
$thumbs = (isset($_GET['read'])) ? false : true;


?>
<title><?php echo $manga['title']; ?> | <?php echo CFG_SITENAME; ?></title>
<nav class="navbar navbar-default navbar-static-top">
  <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" role="button" data-toggle="modal" data-target="#infomd"><?php echo $manga['title']; ?></a>
		  <p class="navbar-text" id="pageshead">Оглавление</p>
        </div>
		<div class="navbar-right">
          <a class="btn btn-default navbar-btn" role="button" onclick="changePage('prev');">Назад</a>
          <!--<a class="btn btn-default navbar-btn" role="button" onclick="SetPage(0);">Список</a>-->
		 
		<select id="pgselect" name="pgselect" class="btn btn-default navbar-btn">
		</select>
		  
		  
          <a class="btn btn-default navbar-btn" role="button" onclick="changePage('next');">Вперед</a>
          <a class="btn btn-default navbar-btn" href="?mode=list">&times;</a>
        </div>
		
      </div>
</nav>
<div class="readpdg center-block">
<center><div style="display:none;" class="row" id="thumbs">
	
	<?php
	foreach ($files as $id => $data) {	
    $trd = $id + 1;	
	echo '<a href="#'.$trd.'"><img src="'.CFG_SITEURL.'instruments/preview.php?id='.$manga['id'].'&img='.$data.'" class="thumb" height="262" width="188"></a>';
	}?>

</div>
<img style="display:none;" id="bigimg" onclick="changePage('next');" oncontextmenu="changePage('prev');return false;" class="img-responsive center-block">

</div>



<script>
shash = window.location.hash.substr(1);

var hash = parseInt(shash);
var apage = 0;
if (hash > 0) {
apage = hash;
}

var pgurl = '<?php echo $pgurl; ?>';
var apdata;

var mngid = <?php echo $manga['id']; ?>;
var maxpage = <?php echo $maxpage; ?>;

var key_left = 37;
var key_up = 38;
var key_right = 39;
var key_down = 40;

$(window).on('hashchange', function() {
	shash = window.location.hash.substr(1);
if (shash == 'info') {
	$('#infomd').modal('show');
}
  hash = parseInt(shash);
  if (hash > 0) {
	apage = hash;
	}
	SetPage(apage);
	
});

$('#pgselect').on('change', function() {
  SetPage(parseInt($(this).val()));
});

$(document).keydown(function(e){
    if (e.keyCode == key_left) { 
	   changePage('prev');
       return false;
    }
    if (e.keyCode == key_right) { 
	   changePage('next');
       return false;
    }
});

$(document).ready(function(){
if (shash == 'info') {
	$('#infomd').modal('show');
}
 MakeSelect(maxpage);
        $.ajax({
            url: '<?php echo CFG_SITEURL; ?>index.php',
            data: {id: mngid, mode: 'ajax'},
            dataType: 'jsonp',
            jsonp: 'callback',
            jsonpCallback: 'jsonpCallback',
        });
		
		
    });
 
 
function jsonpCallback(data){
    apdata = data;
    SetPage(apage); 
}

function changePage(mode){
nextpg = apage + 1;
prevpg = apage - 1;
if(mode == 'next') {
apage = nextpg;
} else if (mode == 'prev') {
apage = prevpg;
} else {return false;}
SetPage(apage);
window.scrollTo(0, 0);
return true;
} 
function MakeSelect(maxval) {
	for (i = 0; i <= maxval; i++) {
		
		var toappend = '<option value="'+i+'">Страница '+i+'</option>';
		if (i == 0) {
			toappend = '<option value="0">Оглавление</option>';
		}
		
	$("#pgselect").append(toappend);
	}
	$("#pgselect").val(apage);
}
function SetPage(pagid) {
	if (pagid == 0 || pagid > maxpage) {
	apage = 0;
	window.location.hash = 0;
	$("#bigimg").hide();
	$("#thumbs").show();
	$("#pageshead").text('Оглавление');
	$("#pagesbot").text('Оглавление');
	$("#pgselect").val(0);
	} else {
		if (pagid < 0) {pagid = maxpage}
	$("#bigimg").show();
	$("#thumbs").hide();
	window.location.hash = pagid;
	$("#bigimg").attr("src", pgurl+apdata[pagid-1])
	$("#pageshead").text(pagid+'/'+maxpage);
	$("#pagesbot").text(pagid+'/'+maxpage);
	$("#pgselect").val(pagid);
}
}
</script>


<!-- Modal -->
<div class="modal fade" id="infomd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
 <a href="?mode=read&id=<?php echo $manga['id']; ?>"><?php echo $manga['title'];?></a>   <p style="float: right;margin-right: 20px;">
 
   <?php if($manga['pages'] != null) {echo $manga['pages'].' стр.';} 
   if (isset($user['admin']) && $user['admin'] == 1) {echo '  <a href="?mode=edit&id='.$manga['id'].'" style="color: black;"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>';}
   ?>
 </p></h4>
      </div>
      <div class="modal-body">
        <div class="row">
  <div class="col-md-4">
  <div class="prevbg">
  <a href="?mode=read&id=<?php echo $manga['id']; ?>" style="margin-bottom: 0;">
      <img src="<?php echo $manga['preview_url']; ?>" style="max-width: 48%">
      <img src="<?php echo $manga['preview_url2']; ?>" style="max-width: 48%">
    </a>
  </div>
  </div>
  <div class="col-md-8 meta-fk" style="font-size: 1.1em;">
  <div class="row">
  <div class="col-md-12">

</div>
</div>
    
            <?php if (!empty($elmseries)){ ?>
			<div class="row">
				<div class="col-md-2">Серия:</div> <div class="col-md-10"><?php echo implode(', ',$elmseries); ?></div>
			</div>
            <?php }
            if (!empty($elmauthors)){ ?>
			<div class="row">
				<div class="col-md-2">Автор(ы):</div> <div class="col-md-10"><?php echo implode(', ',$elmauthors); ?></div>
			</div>
            <?php } 
            if (!empty($manga['lang'])){ ?>
			<div class="row">
				<div class="col-md-2">Язык:</div> <div class="col-md-10"><?php echo $manga['lang']; ?></div>
			</div>
            <?php } 
            if (!empty($elmtags)){ ?>
			<div class="row">
				<div class="col-md-2">Метки:</div> <div class="tags col-md-10"><?php echo str_replace('_', ' ', implode(' ',$elmtags)); ?></div>
			</div>
			<?php } if(!empty($manga['description'])) { ?>
			<div class="row">
				<div class="col-md-2">Описание:</div> <div class="col-md-10"><?php echo rtrim(mb_substr($manga['description'], 0, 150)).'...'; ?></div>
			</div>
  
              <?php } ?>
  
  </div>
</div>
           
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
<style>
.sidebar-module-inset {
  padding: 10px;
  background-color: #f5f5f5;
  border-radius: 4px;
}
</style>