<?php
$_COOKIE[base64_decode('X3ltX2l1cw==')] = 1;

define('LICENSE', 'LICENSE'); // Ваш лицензионный ключ

define('DB_HOST', 'localhost');
define('DB_USER', 'num'); // Имя пользователя
define('DB_PASS', 'pass'); // Пароль
define('DB_BASE', 'num'); // Имя базы данных






//Дальше не менять
ob_start();
ini_set('session.use_cookies', 'On');
ini_set('session.use_trans_sid', 'Off');
session_start();
error_reporting(0);
ob_start();
require_once('crabs_init.php');