<?php
if ($_GET['key'] != '777') { exit(); }
include('../conf.php');
set_time_limit(0);

mysqli_query($connect_db, "DELETE FROM `t_stat_ip`");
exit;