<?php
//Охуевший Мистер Крабс
include('../inc/conf.php');
$u_id=intval($_SESSION['uid']); $nowusr = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT uid,ty FROM t_users WHERE uid='$u_id' AND pas = '$_SESSION[pass]' LIMIT 1")); $nowusr_ty = explode(' ',$nowusr['ty']);
if (empty($u_id)) { exit('error'); }

$adata = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT id,sender_id FROM `t_data` LIMIT 1"));

$string = '{"gcm_sender_id":"'.$adata['sender_id'].'"}';

if(isset($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'],'MSIE')) {
Header('Content-Type: application/force-download'); 
} else {
Header('Content-Type: application/octet-stream'); 
}
Header('Accept-Ranges: bytes'); 
Header('Content-Length: '.strlen($string)); 
Header('Content-disposition: attachment; filename="manifest.json"'); 
echo $string;
exit();
?>