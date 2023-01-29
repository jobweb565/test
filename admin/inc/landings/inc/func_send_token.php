<?php
if (!isset($_POST['token'])) { print ('Error: no token'); exit; }
include('conf.php');
$ip_client = @$_SERVER['HTTP_CLIENT_IP'];
$ip_forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
$ip_remote = $_SERVER['REMOTE_ADDR'];
if(filter_var($ip_client, FILTER_VALIDATE_IP))
{
$ip = $ip_client;
}
elseif(filter_var($ip_forward, FILTER_VALIDATE_IP))
{
$ip = $ip_forward;
}
else
{
$ip = $ip_remote;
}
$ip = mysqli_real_escape_string($connect_db, $ip);
$dt = time();
include("geo/getcou.php");
include('detect.php');
$detect = new Mobile_Detect;
$now_device = '0';
if($detect->isMobile()){ $now_device = '1'; }
if($detect->isTablet()){ $now_device = '2'; }

$token = mysqli_real_escape_string($connect_db, $_POST['token']);
$sid = intval($_POST['sid']);
$landing = intval($_POST['landing']);

mysqli_query($connect_db, "INSERT INTO `t_tokens` (tkn,stream,cou,device,dt) VALUES ('$token','$sid','$crabs_country','$now_device','$dt')");
mysqli_query($connect_db, "UPDATE `t_data` SET `p_allusers` = `p_allusers`+1 WHERE id = '1' LIMIT 1");

$nowday = date('Ymd',$dt);
$isday_stat = mysqli_num_rows(mysqli_query($connect_db, "SELECT id FROM t_stat WHERE lnd = '$landing' AND stream = '$sid' AND ymd = '$nowday' LIMIT 1"));

if ($isday_stat == 0) {
mysqli_query($connect_db, "INSERT INTO t_stat (lnd,stream,ymd,subs) VALUES ('$landing','$sid','$nowday','1')");
} else {
mysqli_query($connect_db, "UPDATE `t_stat` SET `subs` = `subs`+1 WHERE lnd = '$landing' AND stream = '$sid' AND ymd = '$nowday' LIMIT 1");
}


if (!empty($_POST['label'])) {
$label = mysqli_real_escape_string($connect_db, $_POST['label']);
$islabelstat = mysqli_num_rows(mysqli_query($connect_db, "SELECT id FROM t_stat_labels WHERE lbl = '$label' AND ymd = '$nowday' LIMIT 1"));

if ($islabelstat == 0) {
mysqli_query($connect_db, "INSERT INTO t_stat_labels (lbl,ymd,subs) VALUES ('$label','$nowday','1')");
} else {
mysqli_query($connect_db, "UPDATE `t_stat_labels` SET `subs` = `subs`+1 WHERE lbl = '$label' AND ymd = '$nowday' LIMIT 1");
}

}

exit;