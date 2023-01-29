<?php
//Охуевший Мистер Крабс
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
include('conf.php');

$id = intval($_GET['id']);
$landing_id = '0';

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
$isip = mysqli_num_rows(mysqli_query($connect_db, "SELECT id FROM t_stat_ip WHERE stream = '$id' AND ip = '$ip' LIMIT 1"));
if ($isip == 0) {
mysqli_query($connect_db, "INSERT INTO t_stat_ip (stream,ip) VALUES ('$id','$ip')");

$nowday = date('Ymd',$dt);
$isday_stat = mysqli_num_rows(mysqli_query($connect_db, "SELECT id FROM t_stat WHERE lnd = '$landing_id' AND stream = '$id' AND ymd = '$nowday' LIMIT 1"));

if ($isday_stat == 0) {
mysqli_query($connect_db, "INSERT INTO t_stat (lnd,stream,ymd,clk) VALUES ('$landing_id','$id','$nowday','1')");
} else {
mysqli_query($connect_db, "UPDATE `t_stat` SET `clk` = `clk`+1 WHERE lnd = '$landing_id' AND stream = '$id' AND ymd = '$nowday' LIMIT 1");
}

if (isset($_GET['label'])) {
$label = mysqli_real_escape_string($connect_db, $_GET['label']);
$islabelstat = mysqli_num_rows(mysqli_query($connect_db, "SELECT id FROM t_stat_labels WHERE lbl = '$label' AND ymd = '$nowday' LIMIT 1"));

if ($islabelstat == 0) {
mysqli_query($connect_db, "INSERT INTO t_stat_labels (lbl,ymd,clk) VALUES ('$label','$nowday','1')");
} else {
mysqli_query($connect_db, "UPDATE `t_stat_labels` SET `clk` = `clk`+1 WHERE lbl = '$label' AND ymd = '$nowday' LIMIT 1");
}

}
}
?>