<?php
//Охуевший Мистер Крабс
include('../inc/conf.php');
crabs_token_check($_POST['token']);
$u_id=intval($_SESSION['uid']); $nowusr = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT uid,ty FROM t_users WHERE uid='$u_id' AND pas = '$_SESSION[pass]' LIMIT 1")); $nowusr_ty = explode(' ',$nowusr['ty']);
if ($nowusr['ty'] == 0) { exit('demo'); } if (!in_array('2', $nowusr_ty)) { exit('error'); }

//Получение данных
$msg = intval($_POST['msg']);
$stream = intval($_POST['stream']);
$device = intval($_POST['device']);
$icou = $_POST['icou'];
$statgo = $_POST['statgo'];
$statend = $_POST['statend'];
$dtfrom_sql = mysqli_real_escape_string($connect_db, strtotime($statgo));
$dtto_sql = mysqli_real_escape_string($connect_db, strtotime($statend)+86400);

if ($msg == '-1') { exit('msg'); }

if ($_POST['checked'] == 'true') {
$dtst = DateTime::createFromFormat('d.m.Y H:i', $_POST['dtstart']);
$dtstart = $dtst->getTimestamp();
} else {
$dtstart = $dt-600;
}

if ($stream == '-1') { $where_stream = ''; } else { $where_stream = 'AND stream = "'.$stream.'"'; }
if (empty($icou)) { $where_cou = ''; } else {
$cous = explode(" ", $icou);
$cousee = '';
foreach($cous as $couse) {
$couse = trim($couse);
$cousee .= '"'.mysqli_real_escape_string($connect_db, $couse).'",';
}
$cousee = substr($cousee, 0, -1);
$where_cou = 'AND cou IN ('.$cousee.')';
}
if ($device == '-1') { $where_device = ''; } else { $where_device = 'AND device = "'.$device.'"'; }

$how = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT COUNT(id) AS `cnt` FROM `t_tokens` WHERE id > '-1' $where_stream $where_cou $where_device AND (dt >= $dtfrom_sql AND dt <= $dtto_sql)"));
$howe = $how['cnt'];

mysqli_query($connect_db, "INSERT INTO `t_gosend` (send_msg,send_stream,send_device,send_cou,send_from,send_to,send_lastid,send_total,send_progress,dt,dtstart) VALUES ('$msg','$stream','$device','$icou','$dtfrom_sql','$dtto_sql','-1','$howe','0','$dt','$dtstart')");

exit('1');
?>