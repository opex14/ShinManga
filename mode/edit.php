<?php if(!defined('SMG')) die('USE MAIN SCRIPT!');?>
<title>Редактирование | <?php echo CFG_SITENAME; ?></title>
<nav class="navbar navbar-default navbar-static-top">
  <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          </button>
          <a class="navbar-brand" href="<?php echo CFG_SITEURL; ?>"><?php echo CFG_SITENAME; ?></a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="?mode=list">Манга</a></li>
            <li><a href="?mode=add">Добавить</a></li>
            <li class="active"><a href="?mode=list">Редактирование</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
</nav>
<div class="container">
<?php
if (empty($getid)) {die('No ID!');};
if (!isset($user['admin'])) {die('Not Admin!');};
if ($user['admin'] == 0) {die('Not Admin!');};
$fromadd = (isset($_GET['nojs']) && $_GET['nojs'] == 'yes') ? 'yes' : 'no';
if ($post == 'setmangadata') {
$answer = MysqlPost($_POST);
    if ($answer == 'del') {
    header("Location: ?mode=list");
    }
    if ($answer == 'OK' && $fromadd == 'yes') {
    header("Location: ?mode=add");
    }
?>
<div class="alert alert-warning alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong><?php echo $answer;?></strong>
</div>
<?php
}

$manga = MangaData($getid);
if (empty($manga)) {die('Wrong ID!');};

$dataloc = ROOT_DIR.'/manga/'.$getid;
$files = GetRes($dataloc);
$maxpage = count($files);
//if ($manga['pages'] != $maxpage) {SetPages($manga['id'], $maxpage);}
?>



<div class="panel panel-default center-block" style="max-width: 650px;">
  <div class="panel-heading">
    <h3 class="panel-title">Редактирование</h3>
  </div>
  
  <div class="panel-body">
<form method="post">

<div class="form-group">
    <label>Название</label>
    <?php if ($fromadd != 'yes') { ?>
	<input type="checkbox" id="titlec">
    <?php } ?>
    <input id="title" type="text" class="form-control" name="title" value="<?php echo $manga['title']; ?>">
  </div>

<div class="form-group">
    <label>Описание</label>
    <?php if ($fromadd != 'yes') { ?>
	<input type="checkbox" id="descriptionc">
    <?php } ?>
    <textarea id="description" class="form-control" name="description" rows="4"><?php echo $manga['description']; ?></textarea>
  </div>

<div class="form-group">
    <label>Язык</label>
    <?php if ($fromadd != 'yes') { ?>
	<input type="checkbox" id="langc">
    <?php } ?>
	<select id="lang" name="lang" class="form-control">
	 <?php
	 $langsel = array(
		"Русский",
		"Английский",
		"Японский",
		"Другой",
	 );
 if (empty($manga['lang'])) {echo '<option value="" selected=selected">Нет</option>';}
 foreach ($langsel as $lang) {
 $selchk = ($lang == $manga['lang']) ? ' selected="selected"' : '';
	 echo '<option value="'.$lang.'"'.$selchk.'>'.$lang.'</option>';
 }

?>
	</select>
  </div>
<div class="form-group">
    <label>Предпросмотр</label>
    <?php if ($fromadd != 'yes') { ?>
	<input type="checkbox" id="previewc">
    <?php } ?>
 <select id="preview" name="preview" class="form-control">
 <?php
 if (empty($manga['preview'])) {echo '<option value="" selected=selected">Нет</option>';}
 foreach ($files as $file) {
 $selchk = ($file == $manga['preview']) ? ' selected="selected"' : '';
	 echo '<option value="'.$file.'"'.$selchk.'>'.$file.'</option>';
 }

?>
</select>
 </div>
 
<div class="form-group">
    <label>Предпросмотр 2</label>
    <?php if ($fromadd != 'yes') { ?>
	<input type="checkbox" id="previewc2">
    <?php } ?>
 <select id="preview2" name="preview2" class="form-control">
 <?php
 if (empty($manga['preview2'])) {echo '<option value="" selected=selected">Нет</option>';}
 foreach ($files as $file) {
 $selchk = ($file == $manga['preview2']) ? ' selected="selected"' : '';
	 echo '<option value="'.$file.'"'.$selchk.'>'.$file.'</option>';
 }

?>
</select>
 </div>

<div class="form-group">
    <label>Автор(ы)</label>
    <?php if ($fromadd != 'yes') { ?>
	<input type="checkbox" id="authorsc">
    <?php } ?>
    <input id="authors" type="text" class="form-control" name="authors" value="<?php echo implode(',', array_map(function ($entry) {return $entry['meta'];}, $manga['authors'])); ?>">
  </div>
  
<div class="form-group">
    <label>Серия(и)</label>
    <?php if ($fromadd != 'yes') { ?>
	<input type="checkbox" id="seriesc">
    <?php } ?>
	<a role="button" style="float:right;" onclick="SetOriginal()">Оригинал</a> 
	
    <input id="series" type="text" class="form-control" name="series" value="<?php echo implode(',', array_map(function ($entry) {return $entry['meta'];}, $manga['series'])); ?>">
  </div>
  
<div class="form-group">
    <label>Метки</label>
    <?php if ($fromadd != 'yes') { ?>
	<input type="checkbox" id="tagsc">
    <?php } ?>
	<br>
	<?php foreach (GetAllTags() as $tag) { ?>
	<a role="button" id="tag-<?php echo $tag['id'];?>" onclick="AddTag(<?php echo "'".$tag['tag']."','".$tag['id']."'"; ?>)"><?php echo $tag['tag']; ?></a> 
	<?php } ?>
    <input id="tags" type="text" class="form-control" name="tags" value="<?php echo implode(' ', array_map(function ($entry) {return $entry['meta'];}, $manga['tags'])); ?>">
  </div>
  
<input type="hidden" name="mode" value="setmangadata">
<input type="hidden" name="id" value="<?php echo $manga['id']; ?>">
<button type="submit" class="btn btn-default">Сохранить</button>
<table class="table" style="margin-top: 20px;">
<tr> <td>ID: <?php echo $manga['id']; ?></td> <td>Страницы: <?php echo $manga['pages']; ?></td> <td>Добавлено: <?php echo $manga['posted']; ?></td> </tr>
</table>

<div class="input-group">
      <input type="text" name="deletecf" class="form-control" placeholder="Yes/No">
      <span class="input-group-btn">
        <button class="btn btn-danger" type="submit" name="deletebt" value="true">Удалить</button>
      </span>
	  
    </div>
	
</form>
  </div>
</div>
</div>
<nav class="navbar navbar-default navbar-static-bottom" style="margin-bottom: 0;">
  <div class="container">
    <p class="navbar-text"><?php echo CFG_SITENAME.' v'.CFG_VERSION.' | Created by <b>Opex</b>.' ?></p>
  </div>
</nav>
<script>
function SetOriginal() {
	document.getElementById('series').value = "Оригинальные работы";
}
function AddTag(tag, id) {
	document.getElementById('tags').value = document.getElementById('tags').value + " " + tag;
	$('#tag-'+id).remove();
}
</script>
<?php if ($fromadd != 'yes') { ?>
<script>
var update_1 = function () {
    if ($("#titlec").is(":checked")) {
        $('#title').prop('disabled', false);
    }
    else {
        $('#title').prop('disabled', 'disabled');
    }
};
var update_2 = function () {
    if ($("#descriptionc").is(":checked")) {
        $('#description').prop('disabled', false);
    }
    else {
        $('#description').prop('disabled', 'disabled');
    }
};
var update_3 = function () {
    if ($("#langc").is(":checked")) {
        $('#lang').prop('disabled', false);
    }
    else {
        $('#lang').prop('disabled', 'disabled');
    }
};
var update_4 = function () {
    if ($("#previewc").is(":checked")) {
        $('#preview').prop('disabled', false);
    }
    else {
        $('#preview').prop('disabled', 'disabled');
    }
};
var update_5 = function () {
    if ($("#authorsc").is(":checked")) {
        $('#authors').prop('disabled', false);
    }
    else {
        $('#authors').prop('disabled', 'disabled');
    }
};
var update_6 = function () {
    if ($("#seriesc").is(":checked")) {
        $('#series').prop('disabled', false);
    }
    else {
        $('#series').prop('disabled', 'disabled');
    }
};
var update_7 = function () {
    if ($("#tagsc").is(":checked")) {
        $('#tags').prop('disabled', false);
    }
    else {
        $('#tags').prop('disabled', 'disabled');
    }
};
var update_8 = function () {
    if ($("#previewc2").is(":checked")) {
        $('#preview2').prop('disabled', false);
    }
    else {
        $('#preview2').prop('disabled', 'disabled');
    }
};

$(update_1);
$("#titlec").change(update_1);
$(update_2);
$("#descriptionc").change(update_2);
$(update_3);
$("#langc").change(update_3);
$(update_4);
$("#previewc").change(update_4);
$(update_5);
$("#authorsc").change(update_5);
$(update_6);
$("#seriesc").change(update_6);
$(update_7);
$("#tagsc").change(update_7);
$(update_8);
$("#previewc2").change(update_8);
</script>

<?php } ?>