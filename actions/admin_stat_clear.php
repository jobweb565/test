<?php
include('../inc/conf.php');
crabs_token_check($_POST['token']);
$u_id=intval($_SESSION['uid']); $nowusr = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT uid,ty FROM t_users WHERE uid='$u_id' AND pas = '$_SESSION[pass]' LIMIT 1")); $nowusr_ty = explode(' ',$nowusr['ty']);
if ($nowusr['ty'] == 0) { exit('demo'); } if (!in_array('5', $nowusr_ty)) { exit('error'); }

$wclr = intval($_POST['wclr']);

//Очистка всё
if ($wclr == 1) {
mysqli_query($connect_db, "UPDATE `t_data` SET `p_all_showed` = '0', `p_all_clicks` = '0' LIMIT 1");
mysqli_query($connect_db, "DELETE FROM `t_stat`");
mysqli_query($connect_db, "DELETE FROM `t_stat_sended`");
mysqli_query($connect_db, "DELETE FROM `t_stat_labels`");
}

//Очистка меток
if ($wclr == 2) {
mysqli_query($connect_db, "DELETE FROM `t_stat_labels`");
}
exit('1');
?>