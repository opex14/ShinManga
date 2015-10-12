<?php if(!defined('SMG')) die('USE MAIN SCRIPT!'); ?>
<title><?php echo CFG_SITENAME; ?> | Добавление</title>
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
            <li class="active"><a href="?mode=add">Добавить</a></li>
          </ul>
        </div>
      </div>
</nav>
<div class="container">
<?php  
if (!isset($user['admin'])) {die('Not Admin!');};
if ($user['admin'] == 0) {die('Not Admin!');};

if ($post == 'addmanga') {
	
	$answer = AddMangaFile($_POST);
	
	if ($answer != 'fail') {
		header("Location: ?mode=edit&nojs=yes&id=".$answer);
	}
	
	if ($answer == 'fail') { ?>
<div class="alert alert-danger alert-dismissible" role="alert">
  <strong><?php echo $answer;?></strong>
</div>
<?php }} ?>

<div class="panel panel-default center-block" style="max-width: 650px;">
  <div class="panel-heading">
    <h3 class="panel-title">Добавление</h3>
  </div>
  <div class="panel-body">
  <form method="post" enctype="multipart/form-data">

<div class="form-group">
    <label>Название</label>
    <input id="title" type="text" class="form-control" name="title">
  </div>
  
  <div class="form-group">
    <label for="exampleInputFile">Выберите файл</label>
    <input type="file" id="file" name="files[]" multiple="multiple"/>
    <p class="help-block">
	(.zip/.jpg/.jpeg/.gif/.png)<br>
	<?php 
	echo 'Максимум '.ini_get('max_file_uploads').' файлов.<br>'; 
	echo 'Размером по '.ini_get('upload_max_filesize').'.<br>'; 
	echo (ini_get('post_max_size') > 0) ? 'и общем размером менее '.ini_get('post_max_size').'.<br>' : ''; 
	
	?></p>
  </div>
  
  <input type="hidden" name="mode" value="addmanga">
  <button type="submit" class="btn btn-default">Загрузить</button>
  </form>
  
  </div>
</div>
</div>
<nav class="navbar navbar-default navbar-static-bottom" style="margin-bottom: 0;">
  <div class="container">
    <p class="navbar-text"><?php echo CFG_SITENAME.' v'.CFG_VERSION.' | Created by <b>Opex</b>.' ?></p>
  </div>
</nav>