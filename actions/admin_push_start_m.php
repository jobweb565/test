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

$rassylka_dt_s = $_POST['rstatgo'];
$rassylka_time_s = $_POST['rstatgotime'];
$rassylka_dt_po = $_POST['rstatend'];
$rassylka_time_po = $_POST['rstatgotime2'];
$interval = intval($_POST['interval']);

$dtfrom_sql = mysqli_real_escape_string($connect_db, strtotime($statgo));
$dtto_sql = mysqli_real_escape_string($connect_db, strtotime($statend)+86400);

if ($msg == '-1') { exit('msg'); }
if (empty($rassylka_dt_s) || ($timestampz = strtotime($rassylka_dt_s)) === false) { exit('nodts'); }
if (empty($rassylka_time_s)) { exit('notimes'); }
if (empty($rassylka_dt_po) || ($timestampz = strtotime($rassylka_dt_po)) === false) { exit('nodtpo'); }
if (empty($rassylka_time_po)) { exit('notimepo'); }
if (empty($interval)) { exit('nointerval'); }

$rassylka_s = strtotime($rassylka_dt_s.' '.$rassylka_time_s.':00');
$rassylka_po = strtotime($rassylka_dt_po.' '.$rassylka_time_po.':00');
$intervalh = $interval*3600;

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

$dtgom_ch = $rassylka_s;
while ($dtgom_ch < $rassylka_po)
{
mysqli_query($connect_db, "INSERT INTO `t_gosend` (send_msg,send_stream,send_device,send_cou,send_from,send_to,send_lastid,send_total,send_progress,dt,dtstart) VALUES ('$msg','$stream','$device','$icou','$dtfrom_sql','$dtto_sql','-1','$howe','0','$dt','$dtgom_ch')");
$dtgom_ch = $dtgom_ch+$intervalh;
}

exit('1');
?>