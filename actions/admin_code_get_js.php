<?php
//Охуевший Мистер Крабс
include('../inc/conf.php');
$u_id=intval($_SESSION['uid']); $nowusr = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT uid,ty FROM t_users WHERE uid='$u_id' AND pas = '$_SESSION[pass]' LIMIT 1")); $nowusr_ty = explode(' ',$nowusr['ty']);
if (empty($u_id)) { exit('error'); }

$adata = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT id,sender_id FROM `t_data` LIMIT 1"));

$string = 'importScripts(\'https://www.gstatic.com/firebasejs/5.2.0/firebase-app.js\');
importScripts(\'https://www.gstatic.com/firebasejs/5.2.0/firebase-messaging.js\');
var config = {
messagingSenderId: "'.$adata['sender_id'].'"
};
firebase.initializeApp(config);
self.addEventListener(\'notificationclick\', e => {
let found = false;
let f = clients.matchAll({
includeUncontrolled: true,
type: \'window\'
})
.then(function (clientList) {
for (let i = 0; i < clientList.length; i ++) {
if (clientList[i].url === e.notification.data.click_action) {
found = true;
clientList[i].focus();
break;
}
}
if (! found) {
clients.openWindow(e.notification.data.click_action).then(function (windowClient) {});
}
});
e.notification.close();
e.waitUntil(f);
});
var messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload){
return self.registration.showNotification(payload.data.title,
Object.assign({data: payload.data}, payload.data));
});';

if(isset($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'],'MSIE')) {
Header('Content-Type: application/force-download'); 
} else {
Header('Content-Type: application/octet-stream'); 
}
Header('Accept-Ranges: bytes'); 
Header('Content-Length: '.strlen($string)); 
Header('Content-disposition: attachment; filename="firebase-messaging-sw.js"'); 
echo $string;
exit();
?>