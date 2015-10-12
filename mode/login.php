<?php if(!defined('SMG')) die('USE MAIN SCRIPT!');?>
<title><?php echo CFG_SITENAME; ?> | Вход</title>

 <div class="container">

      <form class="form-signin panel" method="post"  action="">
        <h2 class="form-signin-heading" style="margin-top: 0px;"><?php echo CFG_SITENAME; ?></h2>
            <p>Система находиися в приватном режиме. Вход только для пользователей.<p>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input name="login" type="text" class="form-control" placeholder="Логин" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input name="password" type="password" class="form-control" placeholder="Пароль" required>
        <br>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Войти</button>
      </form>

    </div> <!-- /container -->
    
    <style>
    .form-signin {
	text-align: center;
    max-width: 330px;
    padding: 15px;
    margin: 0 auto;
    }
    body {
    background-color: #F3F3F3;
      padding-top: 80px;
    }
    </style>