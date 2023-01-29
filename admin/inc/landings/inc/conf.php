<?php
error_reporting(0);
ob_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'user'); // Имя пользователя
define('DB_PASS', 'pass'); // Пароль
define('DB_BASE', 'base'); // Имя базы данных
define('DB_CHARSET', 'utf8');
$connect_db = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_BASE) or die('Error: '.mysqli_connect_error());
mysqli_set_charset ($connect_db, DB_CHARSET) or die('Error');