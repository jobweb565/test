<?php
header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
require_once('conf.php');

$msg = intval($_GET['m']);
$message = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT id,url FROM `t_messages` WHERE id = '$msg' LIMIT 1"));

//Статистика
mysqli_query($connect_db, "UPDATE `t_data` SET `p_all_clicks` = `p_all_clicks`+'1' WHERE id = '1' LIMIT 1");

$nowday = date('Ymd',$dt);
$isday_stat = mysqli_num_rows(mysqli_query($connect_db, "SELECT id FROM t_stat_sended WHERE msg = '$msg' AND ymd = '$nowday' LIMIT 1"));

if ($isday_stat == 0) {
mysqli_query($connect_db, "INSERT INTO t_stat_sended (msg,ymd,clk) VALUES ('$msg','$nowday','1')");
} else {
mysqli_query($connect_db, "UPDATE `t_stat_sended` SET `clk` = `clk`+'1' WHERE msg = '$msg' AND ymd = '$nowday' LIMIT 1");
}

$url = $message['url'];
header ("Location: $url"); exit;