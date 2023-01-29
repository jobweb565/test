<?php
//Охуевший Мистер Крабс
include('../inc/conf.php');
crabs_token_check($_POST['token']);
require('../inc/wu_resize_crop.php');
$u_id=intval($_SESSION['uid']); $nowusr = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT uid,ty FROM t_users WHERE uid='$u_id' AND pas = '$_SESSION[pass]' LIMIT 1")); $nowusr_ty = explode(' ',$nowusr['ty']);
if ($nowusr['ty'] == 0) { exit('demo'); } if (!in_array('3', $nowusr_ty)) { exit('error'); }

//Получение данных
$id = intval($_POST['id']);
$ti = mysqli_real_escape_string($connect_db, trim($_POST['iti']));
$msg = mysqli_real_escape_string($connect_db, trim($_POST['imsg']));
$url = mysqli_real_escape_string($connect_db, trim($_POST['url']));
$tags = mysqli_real_escape_string($connect_db, trim($_POST['tag']));
if (!empty($tags)) { $tags = ','.$tags.','; }

$data = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT id,img_sm,img_big FROM `t_messages` WHERE id='$id' LIMIT 1"));

if (empty($ti)) { exit('ti'); }
if (empty($msg)) { exit('msg'); }
if (empty($url)) { exit('url'); }


//Загрузка иконки
if(!empty($_FILES['img_sm']['name'])) {
$valid_types =  array('jpg', 'png', 'jpeg', 'JPG', 'PNG', 'JPEG');
$path = '../img/upl/';
$si = getimagesize($_FILES['img_sm']['tmp_name']);
$exts = substr($_FILES['img_sm']['name'], 1 + strrpos($_FILES['img_sm']['name'], "."));
$size = $_FILES['img_sm']['size'];
if($size>(1024*1024*1)) { exit('img_sm_big'); }
if (!in_array($exts, $valid_types)) { exit('img_sm_error'); }
if(!stristr($_FILES['img_sm']['type'], 'image/')) { exit('img_sm_error'); }
if ($si[0] == 0 || $si[1] == 0) { exit('img_sm_error'); }
if ($si[0] < 192 || $si[1] < 192) { exit('img_sm_error'); }
$rand = rand(0,999);
$src_s_big = $path.$dt.'_b'.$rand.'.'.$exts;
$srcs_sm = $dt.'_b'.$rand.'.'.$exts;
if (move_uploaded_file($_FILES["img_sm"]["tmp_name"], $src_s_big)) {
if ($si[0] > 192) { resize($src_s_big, $src_s_big, 192, 0); }
$si2 = getimagesize($src_s_big);
if ($si2[1] > 192) { crop($src_s_big,$src_s_big,array(0,0,192,192)); }
} else { exit('img_sm_error'); }
unlink($path.$data['img_sm']);
} else { $srcs_sm = $data['img_sm']; }


//Загрузка изображения
if(!empty($_FILES['img_big']['name'])) {
$valid_types =  array('jpg', 'png', 'jpeg', 'JPG', 'PNG', 'JPEG');
$path = '../img/upl/';
$si = getimagesize($_FILES['img_big']['tmp_name']);
$exts = substr($_FILES['img_big']['name'], 1 + strrpos($_FILES['img_big']['name'], "."));
$size = $_FILES['img_big']['size'];
if($size>(1024*1024*1)) { exit('img_big_big'); }
if (!in_array($exts, $valid_types)) { exit('img_big_error'); }
if(!stristr($_FILES['img_big']['type'], 'image/')) { exit('img_big_error'); }
if ($si[0] == 0 || $si[1] == 0) { exit('img_big_error'); }
if ($si[0] < 400 || $si[1] < 250) { exit('img_big_error'); }
$rand = rand(0,999);
$src_s_big = $path.$dt.'_s'.$rand.'.'.$exts;
$srcs_big = $dt.'_s'.$rand.'.'.$exts;
if (move_uploaded_file($_FILES["img_big"]["tmp_name"], $src_s_big)) {
if ($si[0] > 400) { resize($src_s_big, $src_s_big, 400, 0); }
$si2 = getimagesize($src_s_big);
if ($si2[1] > 250) { crop($src_s_big,$src_s_big,array(0,0,400,250)); }
} else { exit('img_big_error'); }
unlink($path.$data['img_big']);
} else { $srcs_big = $data['img_big']; }

mysqli_query($connect_db, "UPDATE `t_messages` SET `ti` = '$ti', `msg` = '$msg', `url` = '$url', `img_sm` = '$srcs_sm', `img_big` = '$srcs_big', `tags` = '$tags' WHERE id='$id' LIMIT 1");

exit('1');
?>