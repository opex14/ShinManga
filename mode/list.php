<?php if(!defined('SMG')) die('USE MAIN SCRIPT!');?>
<title>Манга | <?php echo CFG_SITENAME; ?></title>
<nav class="navbar navbar-default navbar-static-top">
  <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo CFG_SITEURL; ?>"><?php echo CFG_SITENAME; ?></a>
		  
        </div>
		<?php if ($auth->isAuth()) { ?>
		<ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
		  
		  <?php 
		  	echo (isset($user['admin']) && $user['admin'] == 1) ? '<b style="color:red;"><span class="glyphicon glyphicon-flash" aria-hidden="true"></span> ' : '<b><span class="glyphicon glyphicon-user" aria-hidden="true"></span> '; 
			echo $user['user_login'];
		  ?>
		  </b>
		  <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
				<li><a role="button"><?php echo (isset($user['admin']) && $user['admin'] == 1) ? 'Администратор' : 'Пользователь'; ?></a></li>
				<li class="divider"></li>
            <li><a href="?is_exit=1">Выход</a></li>
          </ul>
        </li>
      </ul>
		<?php } ?>
		
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="?mode=list">Манга</a></li>
            <?php if (isset($user['admin']) && $user['admin'] == 1) { ?> <li><a href="?mode=add">Добавить</a></li> <?php } ?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
</nav>
<div class="container">

      <div class="blog-header">
	  <h4 style="float: right;">
	  <?php echo getSymbolByQuantity($freespace); ?> Свободно
	  </h4>
	  <h2><?php echo CFG_SITENAME; ?></h2>
      </div>
      
        <div class="row">
        <div class="col-sm-10">
        <p class="lead blog-description"><?php echo MetaHead(); ?></p>
<?php
 $reqaddr = array();
 foreach ($_GET as $elm => $val) {
	 if ($elm != 'page'){
	 $reqaddr[] = $elm.'='.$val;}
 }
 $reqaddr[] = 'page=';
 $requrl = implode('&', $reqaddr);
//Main Page
$onpage = CFG_ONPAGE;

if (isset($_GET["page"]) && !empty($_GET["page"])) { $page  = (int) $_GET["page"]; } else { $page=1; }; 
if ($page <= 0) { $page = 1; };
$start_from = ($page-1) * $onpage; 

$mangas = ListManga($tags, $series, $authors, $search);
$pagescount = ceil($pagesmax/$onpage);
$paginator = MakePages($page, $pagescount, $requrl);
    if ($mangas != null) {
 foreach ($mangas as $manga) {
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
    ?>

            <div class="content-row-fk">
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
   <p style="float: right;">
 
   <?php if($manga['pages'] != null) {echo $manga['pages'].' стр.';} 
   if (isset($user['admin']) && $user['admin'] == 1) {echo '  <a href="?mode=edit&id='.$manga['id'].'" style="color: black;"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>';}
   ?>
 </p>
 <h3><a href="?mode=read&id=<?php echo $manga['id']; ?>"><?php echo $manga['title'];?></a></h3>
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
    <?php
 }
 } else { ?>
 <div class="alert alert-danger" role="alert">
  По вашему запросу ничего не найдено! <a href="?mode=list" class="alert-link">Вернуться на главную</a>.
</div>
<?php
}
?>

<nav style="text-align: center;">
  <ul class="pagination">
    
    <?php echo $paginator ?>
  </ul>
</nav>

</div>
 <div class="col-sm-2">
 <div class="sidebar-module">
        <form method="get">
     <div class="input-group">
      <input type="text" name="search" class="form-control" placeholder="Поиск...">
      <span class="input-group-btn">
        <button class="btn btn-default" type="submit">Искать</button>
      </span>
    </div>
      </form><!-- /input-group -->
 </div>
            <?php if (!$auth->isAuth()) {?>
 <div class="sidebar-module sidebar-module-inset">
            <h4>Вход</h4>
<form method="post" action="">
    <input class="form-control" type="text" name="login" placeholder="Логин" /><br/>
    <input class="form-control" type="password" name="password" placeholder="Пароль" /><br/>
    <input class="btn btn-primary btn-block" type="submit" value="Войти" />
</form>
          </div>
<?php } ?>
          <div class="sidebar-module">
		  
            <h4>Метки</h4>
            <ol class="list-unstyled">
			<?php foreach (GetAllTags('tags', 'no') as $tag) {
				if($tag['id'] != 260){
				echo '<li><a href="?'.AddToQuerry('tags', $tag['id']).'">+</a> <a href="?mode=list&tags='.$tag['id'].'">'.str_replace('_', ' ', $tag['tag']).'</a></li>';
			}
			
			if ($tag['id'] == 260 && $user['user_login'] == 'Opex') {
				echo '<li><a href="?'.AddToQuerry('tags', $tag['id']).'">+</a> <a href="?mode=list&tags='.$tag['id'].'">'.str_replace('_', ' ', $tag['tag']).'</a></li>';
			}}
			?>
              
            </ol>
          </div>
        </div><!-- /.blog-sidebar -->
</div>

</div>
<nav class="navbar navbar-default navbar-static-bottom" style="margin-bottom: 0;">
  <div class="container">
    <p class="navbar-text"><?php echo CFG_SITENAME.' v'.CFG_VERSION.' | Created by <b>Opex</b>. <span class="badge">'.$mangasonsite.'</span>' ?></p>
  </div>
</nav>
<style>
.sidebar-module-inset {
  padding: 10px;
  background-color: #f5f5f5;
  border-radius: 4px;
}
</style>