<?php
require_once('../inc/conf.php');
if(!empty($_SESSION['uid']) && !empty($_SESSION['login']) && !empty($_SESSION['pass'])) {
define('USER_LOGGED',true);
$u_id=intval($_SESSION['uid']);
$u_login=mysqli_real_escape_string($connect_db, $_SESSION['login']);
$nowusr = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT uid,ty FROM t_users WHERE uid='$u_id' AND pas = '$_SESSION[pass]' LIMIT 1")); $nowusr_ty = explode(' ',$nowusr['ty']); macros_city();
if (empty($nowusr['uid'])) { session_unset(); define('USER_LOGGED',false); }
} else { define('USER_LOGGED',false); $u_id = 0; $_SESSION['pass'] = ''; $_SESSION['uid'] = ''; }
if (!USER_LOGGED) {
?>
<html>
<head>
<title>Вход в админ панель</title>
<meta charset="utf-8">
<meta name="keywords" content="Вход в админ панель" />
<meta name="description" content="Вход в админ панель" />
<meta name="viewport" content="width=device-width" />
<link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
<link rel="stylesheet" href="css/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="css/custom.min.css">
<script src="js/jquery.min.js"></script>
</head>
<body sip-shortcut-listen="true" cz-shortcut-listen="true">
<br />
<center>
<div class="alert alert-info" style="width: 79%;">Введите логин и пароль для входа.</div>
<form id="checkc" action="/actions/checkadmin.php" method="POST" style="margin-top:10px">
<input name="login" type="text" class="form-control" placeholder="Логин" style="width:200px;display: inline-block;" /><br />
<input name="pass" type="password" class="form-control" placeholder="Пароль" style="width:200px;display: inline-block;margin-top: 10px;" />
<br /><br />
<input type="submit" value="Вход" class="btn btn-default">
</form>
</center>
</br />
<?php
exit;
}
$adm = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT id,apath FROM `t_data` LIMIT 1"));
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title><?php echo $pname; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
    <link rel="stylesheet" href="css/custom.min.css">
<script src="js/jquery.min.js"></script>
<link rel="stylesheet" href="css/font-awesome.min.css">
  </head>
  <body>
    <div class="navbar navbar-expand-lg fixed-top navbar-dark bg-primary">
      <div class="container">
        <a href="/<?php echo $adm['apath']; ?>/" class="navbar-brand">Главная</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav">
		  <?php if ($nowusr['ty'] == 0 || in_array('1', $nowusr_ty)) { ?>
            <li class="nav-item">
              <a class="nav-link" href="stat">Статистика</a>
            </li>
		  <?php } ?>
		  <?php if ($nowusr['ty'] == 0 || in_array('1', $nowusr_ty)) { ?>
            <li class="nav-item">
              <a class="nav-link" href="statcou">Статистика по странам</a>
            </li>
		  <?php } ?>
		  <?php if ($nowusr['ty'] == 0 || in_array('2', $nowusr_ty)) { ?>
            <li class="nav-item">
              <a class="nav-link" href="gosend">Рассылка</a>
            </li>
		  <?php } ?>
		  <?php if ($nowusr['ty'] == 0 || in_array('3', $nowusr_ty)) { ?>
            <li class="nav-item">
              <a class="nav-link" href="messages">Сообщения</a>
            </li>
		  <?php } ?>
		  <?php if ($nowusr['ty'] == 0 || in_array('4', $nowusr_ty)) { ?>
            <li class="nav-item">
              <a class="nav-link" href="streams">Потоки</a>
            </li>
		  <?php } ?>
		  <?php if ($nowusr['ty'] == 0 || in_array('5', $nowusr_ty)) { ?>
            <li class="nav-item">
              <a class="nav-link" href="clear">Сброс</a>
            </li>
		  <?php } ?>
		  <?php if ($nowusr['ty'] == 0 || in_array('6', $nowusr_ty)) { ?>
            <li class="nav-item">
              <a class="nav-link" href="settings">Настройки</a>
            </li>
		  <?php } ?>
		  <?php if ($nowusr['ty'] == 0 || in_array('7', $nowusr_ty)) { ?>
            <li class="nav-item">
              <a class="nav-link" href="users">Пользователи</a>
            </li>
		  <?php } ?>

          </ul>

          <ul class="nav navbar-nav ml-auto">
            <li class="nav-item">
              <a class="nav-link" href="/inc/exit">Выход</a>
            </li>
          </ul>

        </div>
      </div>
    </div>


    <div class="container">