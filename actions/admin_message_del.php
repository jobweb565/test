<?php
include('../inc/conf.php');
crabs_token_check($_POST['token']);
$u_id=intval($_SESSION['uid']); $nowusr = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT uid,ty FROM t_users WHERE uid='$u_id' AND pas = '$_SESSION[pass]' LIMIT 1")); $nowusr_ty = explode(' ',$nowusr['ty']);
if ($nowusr['ty'] == 0) { exit('demo'); } if (!in_array('3', $nowusr_ty)) { exit('error'); }
    
if(!empty($_POST['id'])){
$id = intval($_POST['id']);

$data = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT id,img_sm,img_big FROM `t_messages` WHERE id='$id' LIMIT 1"));
unlink('../img/upl/'.$data['img_sm']);
unlink('../img/upl/'.$data['img_big']);
mysqli_query($connect_db, "DELETE FROM `t_messages` WHERE id = '$id' LIMIT 1");

exit('1');
} else { exit('0'); }
?>