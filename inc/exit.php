<?php
include('conf.php');

$qu = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT id,apath FROM `t_data` LIMIT 1"));

session_start();
session_unset();
header('Location: /'.$qu['apath']);
exit;
?>