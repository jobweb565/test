<?php
//Охуевший Мистер Крабс
include('../inc/conf.php');
crabs_token_check($_POST['token']);
$u_id=intval($_SESSION['uid']); $nowusr = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT uid,ty FROM t_users WHERE uid='$u_id' AND pas = '$_SESSION[pass]' LIMIT 1")); $nowusr_ty = explode(' ',$nowusr['ty']);
if ($nowusr['ty'] == 0) { exit('demo'); } if (!in_array('7', $nowusr_ty)) { exit('error'); }

//Получение данных
$login = mysqli_real_escape_string($connect_db, trim($_POST['ilgn']));
$pass = mysqli_real_escape_string($connect_db, trim($_POST['ipass']));

$used = mysqli_num_rows(mysqli_query($connect_db, "SELECT uid FROM `t_users` WHERE `log` = '$login' LIMIT 1"));
if ($used > 0) { exit('is'); }

$ty = '';
if (isset($_POST['lbl_0'])) {
$ty = '0';
} else {
if (isset($_POST['lbl_1'])) { $ty .= '1 '; }
if (isset($_POST['lbl_2'])) { $ty .= '2 '; }
if (isset($_POST['lbl_3'])) { $ty .= '3 '; }
if (isset($_POST['lbl_4'])) { $ty .= '4 '; }
if (isset($_POST['lbl_5'])) { $ty .= '5 '; }
if (isset($_POST['lbl_6'])) { $ty .= '6 '; }
if (isset($_POST['lbl_7'])) { $ty .= '7 '; }
$ty = substr($ty, 0, -1);
if (empty($ty)) { exit('ty'); }
}

if (empty($login)) { exit('login'); }
if (empty($pass)) { exit('pass'); }

mysqli_query($connect_db, "INSERT INTO `t_users` (log,pas,ty) VALUES ('$login','$pass','$ty')");

exit('1');
?>