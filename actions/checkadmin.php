<?php
include('../inc/conf.php');
$adm = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT id,apath FROM `t_data` LIMIT 1"));

if(isset($_POST['login']) && isset($_POST['pass'])){
if (!empty($_POST['login']) && !empty($_POST['pass'])) {

$login = mysqli_real_escape_string($connect_db, $_POST['login']);
$pass = mysqli_real_escape_string($connect_db, $_POST['pass']);
$user = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT uid,log,pas FROM t_users WHERE log='$login' LIMIT 1"));

if($user['pas'] == $pass) {

session_regenerate_id(true);
$_SESSION['uid']=$user['uid'];
$_SESSION['login']=$user['log'];
$_SESSION['pass']=$user['pas'];
header ("Location: /".$adm['apath']); exit;
} else {
header ("Location: /".$adm['apath']); exit;
}
} else { header ("Location: /".$adm['apath']); exit; }
} else { header ("Location: /".$adm['apath']); exit; }