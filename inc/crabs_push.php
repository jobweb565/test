<?php
//Охуевший Мистер Крабс
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
include('conf.php');

$data = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT id,sender_id FROM `t_data` LIMIT 1"));
$id = intval($_GET['id']);
$data_stream = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT id,tb,tb2,iget FROM `t_streams` WHERE id = '$id' LIMIT 1"));
$landing_id = '0';

if (!empty($data_stream['tb'])) { $tb = 1; } else { $tb = 0; }
if (!empty($data_stream['tb2'])) { $tb2 = 1; } else { $tb2 = 0; }

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
var crabs_url = window.location.search.replace('?', '');
if ('<?php echo $iget; ?>' == '0') { var crabs_go = '<?php echo $trafback; ?>'; var crabs_go2 = '<?php echo $trafback2; ?>'; } else { var crabs_go = '<?php echo $trafback; ?>'+crabs_url; var crabs_go2 = '<?php echo $trafback2; ?>'+crabs_url; }

var qs = (function(a) {
    if (a == "") return {};
    var b = {};
    for (var i = 0; i < a.length; ++i)
    {
        var p=a[i].split('=', 2);
        if (p.length == 1)
            b[p[0]] = "";
        else
            b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
    }
    return b;
})(window.location.search.substr(1).split('&'));

function csetCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}
function cgetCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}
function ceraseCookie(name) {   
    document.cookie = name+'=; Max-Age=-99999999;';  
}

var isvisited = cgetCookie('crabs_visited');
if (qs["label"] == null){
if (isvisited == null){
var label = '';
} else {
var label = isvisited;
}
var staturl = 'https://<?php echo SITE; ?>/inc/crabs_push_stat/<?php echo $id; ?>';
} else {
var label = qs["label"];
var staturl = 'https://<?php echo SITE; ?>/inc/crabs_push_stat/<?php echo $id; ?>/?label='+label;
}

if (isvisited == null){
csetCookie('crabs_visited',label,1);
$.getScript(staturl);
}

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
										if (<?php echo $tb2; ?> == 1) {
										location.href = crabs_go2;
										}
									}
								})
								.catch(function(err){
										if (<?php echo $tb2; ?> == 1) {
										location.href = crabs_go2;
										}
								});
						})
						.catch(function (err) {
										if (<?php echo $tb2; ?> == 1) {
										location.href = crabs_go2;
										}
						});

				} else {
										if (<?php echo $tb2; ?> == 1) {
										location.href = crabs_go2;
										}
				}
			});
			function sendTokenToServer(currentToken){
				if(!isTokenSentToServer(currentToken)) {
					setTokenSentToServer(currentToken);
					data = {'landing': '<?php echo $landing_id; ?>', 'sid': '<?php echo $id; ?>', 'label': label, 'token': currentToken};
				$.ajax({
					url: 'https://<?php echo $_SERVER['HTTP_HOST']; ?>/inc/func_send_token.php',
					data: data,
					method: 'post',
					cache: false,
					beforeSend: function(){
					},
					success: function(data){
										if (<?php echo $tb; ?> == 1) {
										location.href = crabs_go;
										}
					},
					error: function(){
						 console.log('Проверьте подключение к интернету ...');
					},
					complete: function(){
					}
				});
				} else {
						if (<?php echo $tb; ?> == 1) {
						location.href = crabs_go;
						}
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