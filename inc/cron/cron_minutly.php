<?php
if ($_GET['key'] != '777') { exit(); }
include('../conf.php');
set_time_limit(0);

$send_per_minit = '20000'; //Сколько отправлять в минуту

$adata = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT id,server_key FROM `t_data` LIMIT 1"));
$data = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT * FROM `t_gosend` WHERE dtstart <= '$dt' ORDER BY id ASC LIMIT 1"));

if (empty($data['send_from'])) { exit(); }

$tosend_msg = $data['send_msg'];
$last_token = $data['send_lastid'];
$stream = $data['send_stream'];
$icou = $data['send_cou'];
$device = $data['send_device'];
$dtfrom_sql = mysqli_real_escape_string($connect_db, $data['send_from']);
$dtto_sql = mysqli_real_escape_string($connect_db, $data['send_to']);
$msg = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT * FROM `t_messages` WHERE id = '$tosend_msg' LIMIT 1"));
//$stream_data = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT id,landing FROM `t_streams` WHERE id = '$stream' LIMIT 1"));

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

$wu_q = mysqli_query($connect_db, "SELECT id,tkn FROM `t_tokens` WHERE id > '$last_token' $where_stream $where_cou $where_device AND (dt >= $dtfrom_sql AND dt <= $dtto_sql) LIMIT $send_per_minit");
$i = 0;
$tokens = array();
$tokens_ids = array();
while($row = mysqli_fetch_assoc($wu_q)) {
$i++;
$tokens[] = $row["tkn"];
$tokens_ids[] = $row["id"];
}
$lasttkn = array_values(array_slice($tokens_ids, -1))[0];

$message = array(
		'title' => $msg['ti'],
        'body' => $msg['msg'],
        'icon' => 'https://'.$_SERVER['SERVER_NAME'].'/img/upl/'.$msg['img_sm'],
        'image' => 'https://'.$_SERVER['SERVER_NAME'].'/img/upl/'.$msg['img_big'],
        'click_action' => 'https://'.$_SERVER['SERVER_NAME'].'/inc/click?m='.$tosend_msg
		);

$regIdChunk = array_chunk($tokens,1000);
$total_sended = 0;
$total_unsubscribed = 0;
$total_tokens_to_del = array();

foreach($regIdChunk as $RegId){
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array(
        'registration_ids' => $RegId,
        'data' => $message
        );

        $headers = array(
        'Authorization:key = '.$adata['server_key'],
        'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
                die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);

$result = json_decode($result);
$total_sended += $result->success;
$total_unsubscribed += $result->failure;

foreach($result->results as $key => $value){
if (isset($value->error)) { $total_tokens_to_del[] = $RegId[$key]; }
}
}

//Статистика
mysqli_query($connect_db, "UPDATE `t_data` SET `p_allusers` = `p_allusers`-'$total_unsubscribed', `p_all_showed` = `p_all_showed`+'$total_sended' WHERE id = '1' LIMIT 1");

$nowday = date('Ymd',$dt);
$isday_stat = mysqli_num_rows(mysqli_query($connect_db, "SELECT id FROM t_stat_sended WHERE msg = '$tosend_msg' AND ymd = '$nowday' LIMIT 1"));

if ($isday_stat == 0) {
mysqli_query($connect_db, "INSERT INTO t_stat_sended (msg,ymd,shows,unsubs) VALUES ('$tosend_msg','$nowday','$total_sended','$total_unsubscribed')");
} else {
mysqli_query($connect_db, "UPDATE `t_stat_sended` SET `shows` = `shows`+'$total_sended', `unsubs` = `unsubs`+'$total_unsubscribed' WHERE msg = '$tosend_msg' AND ymd = '$nowday' LIMIT 1");
}

//Удаление неактивных токенов
foreach($total_tokens_to_del as $keyt => $valuet){
mysqli_query($connect_db, "DELETE FROM `t_tokens` WHERE tkn = '$valuet' LIMIT 1");
}

//Обновление статуса рассылки
$id = $data['id'];
if ($i < $send_per_minit) {
mysqli_query($connect_db, "DELETE FROM `t_gosend` WHERE id = '$id' LIMIT 1");
} else {
mysqli_query($connect_db, "UPDATE `t_gosend` SET `send_lastid` = '$lasttkn', `send_total` = `send_total`-'$total_unsubscribed', `send_progress` = `send_progress`+'$total_sended' WHERE id = '$id' LIMIT 1");
}

exit;