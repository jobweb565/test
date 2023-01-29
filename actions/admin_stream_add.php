<?php
//Охуевший Мистер Крабс
include('../inc/conf.php');
crabs_token_check($_POST['token']);
$u_id=intval($_SESSION['uid']); $nowusr = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT uid,ty FROM t_users WHERE uid='$u_id' AND pas = '$_SESSION[pass]' LIMIT 1")); $nowusr_ty = explode(' ',$nowusr['ty']);
if ($nowusr['ty'] == 0) { exit('demo'); } if (!in_array('4', $nowusr_ty)) { exit('error'); }

//Получение данных
$ti = mysqli_real_escape_string($connect_db, trim($_POST['iti']));
$ty = intval($_POST['ity']);
$land = intval($_POST['land']);
$trafback = mysqli_real_escape_string($connect_db, trim($_POST['trafback']));
$trafback2 = mysqli_real_escape_string($connect_db, trim($_POST['trafback2']));
if (isset($_POST['iget'])) { $iget = '1'; } else { $iget = '0'; }

if ($ty == 1) {
$land = 0;
}

if (empty($ti)) { exit('ti'); }
if ($ty == 0 && $land < 0) { exit('land'); }
if ($ty == 0 && empty($trafback)) { exit('tb'); }
if ($ty == 0 && empty($trafback2)) { exit('tb'); }
if (!empty($trafback) && !preg_match( '/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,10}'.'((:[0-9]{1,5})?\\/.*)?$/i' ,$trafback)) { exit('tbe'); }
if (!empty($trafback2) && !preg_match( '/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,10}'.'((:[0-9]{1,5})?\\/.*)?$/i' ,$trafback2)) { exit('tbe'); }

mysqli_query($connect_db, "INSERT INTO `t_streams` (ti,ty,landing,tb,tb2,iget,dt) VALUES ('$ti','$ty','$land','$trafback','$trafback2','$iget','$dt')");

exit('1');
?>