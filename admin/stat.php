<?php
$pname = 'Статистика';
include('inc/top.php');
if ($nowusr['ty'] != 0 && !in_array('1', $nowusr_ty)) { exit('error'); }
include('../inc/wu_pagination.php');
if (isset($_GET['date'])) {
$dte = explode('-', $_GET['date']);
$dtfrom = $dte[0];
$dtto = $dte[1];
$dtfrom_sql = mysqli_real_escape_string($connect_db, convdate($dtfrom));
$dtto_sql = mysqli_real_escape_string($connect_db, convdate($dtto));
} else {
$dtfrom = date("d.m.Y", strtotime("-1 month", $dt));
$dtto = date('d.m.Y', $dt);
$dtfrom_sql = mysqli_real_escape_string($connect_db, date('Ymd', strtotime("-1 month", $dt)));
$dtto_sql = mysqli_real_escape_string($connect_db, date('Ymd', $dt));
}
?>
<!-- Контент -->

<h4>Статистика</h4>
<br />

<select class="form-control" id="statty" style="max-width:407px;display:inline-block;margin-bottom:15px;">
<option value="0" <?php if (!isset($_GET['ty'])) { echo 'selected="selected"'; } ?>>Общая статистика</option>
<option value="1" <?php if (isset($_GET['ty']) && $_GET['ty'] == 'lnd') { echo 'selected="selected"'; } ?>>Статистика по лендингам (переходы/подписки)</option>
<option value="2" <?php if (isset($_GET['ty']) && $_GET['ty'] == 'str') { echo 'selected="selected"'; } ?>>Статистика по потокам (переходы/подписки)</option>
<option value="3" <?php if (isset($_GET['ty']) && $_GET['ty'] == 'msg') { echo 'selected="selected"'; } ?>>Статистика по сообщениям (показы/клики/отписки)</option>
<option value="4" <?php if (isset($_GET['ty']) && $_GET['ty'] == 'lbl') { echo 'selected="selected"'; } ?>>Статистика по меткам</option>
</select>
<br />

<div class="input-daterange" id="crabsdatepicker" style="max-width:200px;display:inline-block;">
<span class="add-on">Статистика с:</span>
<input type="text" class="form-control" id="statgo" value="<?php echo htmlspecialchars($dtfrom); ?>" />
</div>
<div class="input-daterange" id="crabsdatepicker2" style="max-width:200px;display:inline-block;">
<span class="add-on">По:</span>
<input type="text" class="form-control" id="statend" value="<?php echo htmlspecialchars($dtto); ?>" />
</div>
<a href="javascript://" id="statre" class="btn btn-primary" style="margin-top: -4px;"><i class="fa fa-check"></i></a>
<br />
<br />
<br />

<?php
//Общая статистика
if (!isset($_GET['ty'])) {
?>
<div class="table-responsive">
<table class="table table-hover table-sm tblvalign" id="tb_stat">
<thead>
<tr>
<th scope="col">Дата</th>
<th scope="col"><span data-toggle="tooltip" data-placement="top" data-original-title="Учитываются только уникальные переходы">Переходов</span></th>
<th scope="col"><span data-toggle="tooltip" data-placement="top" data-original-title="Push-подписки">Подписок</span></th>
<th scope="col"><span data-toggle="tooltip" data-placement="top" data-original-title="Отписки от push-уведомлений">Отписок</span></th>
<th scope="col"><span data-toggle="tooltip" data-placement="top" data-original-title="Отправлено сообщений">Отправлено</span></th>
<th scope="col"><span data-toggle="tooltip" data-placement="top" data-original-title="Кликов по сообщениям">Кликов</span></th>
<th scope="col"><span data-toggle="tooltip" data-placement="top" data-original-title="Показатель подписываемости">CR подписок</span></th>
<th scope="col"><span data-toggle="tooltip" data-placement="top" data-original-title="Показатель кликов по сообщениям">CTR кликов</span></th>
</tr>
</thead>
<tbody>
<?php
$num = 30;
if (isset($_GET['page'])) { $page = intval($_GET['page']); } else { $page = 0; } if ($page==0) { $page=1; }
$qu = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT count(distinct ymd) AS cnt FROM ( SELECT id, ymd FROM t_stat WHERE (ymd >= $dtfrom_sql AND ymd <= $dtto_sql) GROUP BY ymd UNION ALL SELECT id, ymd FROM t_stat_sended WHERE (ymd >= $dtfrom_sql AND ymd <= $dtto_sql) GROUP BY ymd ) x"));
$co = $qu['cnt'];
$total = intval(($co - 1) / $num) + 1;
$page = intval($page);
if(empty($page) or $page < 0) $page = 1;
if($page > $total) $page = $total;
$start = $page * $num - $num;
$qr = mysqli_query($connect_db, "select id, ymd, sum(n_clk) AS `n_clk`, sum(n_subs) AS `n_subs`, sum(o_shows) AS `o_shows`, sum(o_clk) AS `o_clk`, sum(o_unsubs) AS `o_unsubs` 
  from (
  SELECT id,
	ymd,
	sum(clk) AS `n_clk`,
	sum(subs) AS `n_subs`,
	0 o_shows,
	0 o_clk,
	0 o_unsubs
    from t_stat WHERE (ymd >= $dtfrom_sql AND ymd <= $dtto_sql) GROUP BY ymd DESC
       union all
    SELECT id,
	ymd,
	0 n_clk,
	0 n_subs,
	sum(shows) AS `o_shows`,
	sum(clk) AS `o_clk`,
	sum(unsubs) AS `o_unsubs`
    from t_stat_sended WHERE (ymd >= $dtfrom_sql AND ymd <= $dtto_sql) GROUP BY ymd DESC
	  ) x
GROUP BY ymd ORDER BY ymd DESC LIMIT $start, $num");
if ($co > 0) {
?>
<?php
while($row = mysqli_fetch_assoc($qr)) {
?>
<tr class="table-light">
<td><?php echo date("d.m.Y", strtotime($row['ymd'])); ?></td>
<td><?php echo $row['n_clk']; ?></td>
<td><?php echo $row['n_subs']; ?></td>
<td><?php echo $row['o_unsubs']; ?></td>
<td><?php echo $row['o_shows']; ?></td>
<td><?php echo $row['o_clk']; ?></td>
<td>
<?php if ($row['n_subs'] > 0) { echo number_format($row['n_subs']/$row['n_clk']*100,2,'.',','); } else { echo '0.00'; } ?>
</td>
<td>
<?php if ($row['o_clk'] > 0) { echo number_format($row['o_clk']/$row['o_shows']*100,2,'.',','); } else { echo '0.00'; } ?>
</td>
</tr>
<?php
}
} else {
?>
<tr class="table-light"><td colspan="8"><center>Нет данных</center></td></tr>
<?php } ?>
</tbody>
</table>
</div>
<?php
$pageNav = new SimPageNav();
$pages = ceil($co/$num);
echo $pageNav->getLinks($pages, 1, $page, 10, 'page');
}
?>


<?php
//Статистика по лендингам
if (isset($_GET['ty']) && $_GET['ty'] == 'lnd') {
$arr_landings = array(
'1' => 'Я не робот',
'2' => 'Нет соединения с интернетом',
'3' => 'Ваш файл готов к скачиванию',
'4' => 'Эротический лендинг',
'5' => 'Чтобы продолжить, нажмите...',
'6' => 'FlashPlayer заблокирован',
'7' => '18+',
'8' => 'Видео',
'9' => 'FlashPlayer заблокирован с видео',
'10' => 'Нажмите кнопку разрешить чтобы смотреть видео'
);
?>
<div class="table-responsive">
<table class="table table-hover table-sm tblvalign" id="tb_stat">
<thead>
<tr>
<th scope="col">Лендинг</th>
<th scope="col"><span data-toggle="tooltip" data-placement="top" data-original-title="Учитываются только уникальные переходы">Переходов</span></th>
<th scope="col"><span data-toggle="tooltip" data-placement="top" data-original-title="Push-подписки">Подписок</span></th>
<th scope="col"><span data-toggle="tooltip" data-placement="top" data-original-title="Показатель подписываемости">CR подписок</span></th>
</tr>
</thead>
<tbody>
<?php
$num = 30;
if (isset($_GET['page'])) { $page = intval($_GET['page']); } else { $page = 0; } if ($page==0) { $page=1; }
$qu = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT count(id) AS `cnt` FROM t_stat WHERE lnd > 0 AND (ymd >= $dtfrom_sql AND ymd <= $dtto_sql) GROUP BY lnd"));
$co = $qu['cnt'];
$total = intval(($co - 1) / $num) + 1;
$page = intval($page);
if(empty($page) or $page < 0) $page = 1;
if($page > $total) $page = $total;
$start = $page * $num - $num;
$qr = mysqli_query($connect_db, "
SELECT id,
	lnd,
	sum(clk) AS `n_clk`,
	sum(subs) AS `n_subs`
    from t_stat WHERE lnd > 0 AND (ymd >= $dtfrom_sql AND ymd <= $dtto_sql) GROUP BY lnd ORDER BY n_clk DESC LIMIT $start, $num");
if ($co > 0) {
?>
<?php
while($row = mysqli_fetch_assoc($qr)) {
?>
<tr class="table-light">
<td><?php echo $arr_landings[$row['lnd']]; ?></td>
<td><?php echo $row['n_clk']; ?></td>
<td><?php echo $row['n_subs']; ?></td>
<td>
<?php if ($row['n_subs'] > 0) { echo number_format($row['n_subs']/$row['n_clk']*100,2,'.',','); } else { echo '0.00'; } ?>
</td>
</tr>
<?php
}
} else {
?>
<tr class="table-light"><td colspan="4"><center>Нет данных</center></td></tr>
<?php } ?>
</tbody>
</table>
</div>
<?php
$pageNav = new SimPageNav();
$pages = ceil($co/$num);
echo $pageNav->getLinks($pages, 1, $page, 10, 'page');
}
?>


<?php
//Статистика по потокам
if (isset($_GET['ty']) && $_GET['ty'] == 'str') {
$arr_streams = array();
$wu_q = mysqli_query($connect_db, "SELECT id,ti FROM t_streams ORDER BY id ASC");
while($row = mysqli_fetch_assoc($wu_q)) {
$arr_streams[$row['id']] = $row['ti'];
}
?>
<div class="table-responsive">
<table class="table table-hover table-sm tblvalign" id="tb_stat">
<thead>
<tr>
<th scope="col">Поток</th>
<th scope="col"><span data-toggle="tooltip" data-placement="top" data-original-title="Учитываются только уникальные переходы">Переходов</span></th>
<th scope="col"><span data-toggle="tooltip" data-placement="top" data-original-title="Push-подписки">Подписок</span></th>
<th scope="col"><span data-toggle="tooltip" data-placement="top" data-original-title="Показатель подписываемости">CR подписок</span></th>
</tr>
</thead>
<tbody>
<?php
$num = 30;
if (isset($_GET['page'])) { $page = intval($_GET['page']); } else { $page = 0; } if ($page==0) { $page=1; }
$qu = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT count(id) AS `cnt` FROM t_stat WHERE (ymd >= $dtfrom_sql AND ymd <= $dtto_sql) GROUP BY stream"));
$co = $qu['cnt'];
$total = intval(($co - 1) / $num) + 1;
$page = intval($page);
if(empty($page) or $page < 0) $page = 1;
if($page > $total) $page = $total;
$start = $page * $num - $num;
$qr = mysqli_query($connect_db, "
SELECT id,
	stream,
	sum(clk) AS `n_clk`,
	sum(subs) AS `n_subs`
    from t_stat WHERE (ymd >= $dtfrom_sql AND ymd <= $dtto_sql) GROUP BY stream ORDER BY n_clk DESC LIMIT $start, $num");
if ($co > 0) {
?>
<?php
while($row = mysqli_fetch_assoc($qr)) {
?>
<tr class="table-light">
<td><span data-toggle="tooltip" data-placement="top" data-original-title="#<?php echo $row['stream']; ?>"><?php if (isset($arr_streams[$row['stream']])) { echo $arr_streams[$row['stream']]; } else { echo 'Удалённый поток'; } ?></span></td>
<td><?php echo $row['n_clk']; ?></td>
<td><?php echo $row['n_subs']; ?></td>
<td>
<?php if ($row['n_subs'] > 0) { echo number_format($row['n_subs']/$row['n_clk']*100,2,'.',','); } else { echo '0.00'; } ?>
</td>
</tr>
<?php
}
} else {
?>
<tr class="table-light"><td colspan="4"><center>Нет данных</center></td></tr>
<?php } ?>
</tbody>
</table>
</div>
<?php
$pageNav = new SimPageNav();
$pages = ceil($co/$num);
echo $pageNav->getLinks($pages, 1, $page, 10, 'page');
}
?>


<?php
//Статистика по сообщениям
if (isset($_GET['ty']) && $_GET['ty'] == 'msg') {
$arr_msgs = array();
$wu_q = mysqli_query($connect_db, "SELECT id,ti FROM t_messages ORDER BY id ASC");
while($row = mysqli_fetch_assoc($wu_q)) {
$arr_msgs[$row['id']] = $row['ti'];
}
?>
<div class="table-responsive">
<table class="table table-hover table-sm tblvalign" id="tb_stat">
<thead>
<tr>
<th scope="col">Сообщение</th>
<th scope="col"><span data-toggle="tooltip" data-placement="top" data-original-title="Отправлено сообщений">Отправлено</span></th>
<th scope="col"><span data-toggle="tooltip" data-placement="top" data-original-title="Кликов по сообщениям">Кликов</span></th>
<th scope="col"><span data-toggle="tooltip" data-placement="top" data-original-title="Отписки от push-уведомлений">Отписок</span></th>
<th scope="col"><span data-toggle="tooltip" data-placement="top" data-original-title="Показатель кликов по сообщениям">CTR кликов</span></th>
</tr>
</thead>
<tbody>
<?php
$num = 30;
if (isset($_GET['page'])) { $page = intval($_GET['page']); } else { $page = 0; } if ($page==0) { $page=1; }
$qu = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT count(id) AS `cnt` FROM t_stat_sended WHERE (ymd >= $dtfrom_sql AND ymd <= $dtto_sql) GROUP BY msg"));
$co = $qu['cnt'];
$total = intval(($co - 1) / $num) + 1;
$page = intval($page);
if(empty($page) or $page < 0) $page = 1;
if($page > $total) $page = $total;
$start = $page * $num - $num;
$qr = mysqli_query($connect_db, "
SELECT id,
	msg,
	sum(shows) AS `o_shows`,
	sum(clk) AS `o_clk`,
	sum(unsubs) AS `o_unsubs`
    from t_stat_sended WHERE (ymd >= $dtfrom_sql AND ymd <= $dtto_sql) GROUP BY msg ORDER BY shows DESC LIMIT $start, $num");
if ($co > 0) {
?>
<?php
while($row = mysqli_fetch_assoc($qr)) {
?>
<tr class="table-light">
<td><span data-toggle="tooltip" data-placement="top" data-original-title="#<?php echo $row['msg']; ?>"><?php if (isset($arr_msgs[$row['msg']])) { echo $arr_msgs[$row['msg']]; } else { echo 'Сообщение удалено'; } ?></span></td>
<td><?php echo $row['o_shows']; ?></td>
<td><?php echo $row['o_clk']; ?></td>
<td><?php echo $row['o_unsubs']; ?></td>
<td>
<?php if ($row['o_clk'] > 0) { echo number_format($row['o_clk']/$row['o_shows']*100,2,'.',','); } else { echo '0.00'; } ?>
</td>
</tr>
<?php
}
} else {
?>
<tr class="table-light"><td colspan="5"><center>Нет данных</center></td></tr>
<?php } ?>
</tbody>
</table>
</div>
<?php
$pageNav = new SimPageNav();
$pages = ceil($co/$num);
echo $pageNav->getLinks($pages, 1, $page, 10, 'page');
}
?>












<?php
//Статистика по меткам
if (isset($_GET['ty']) && $_GET['ty'] == 'lbl') {
?>

<div class="table-responsive">
<table class="table table-hover table-sm tblvalign" id="tb_stat">
<thead>
<tr>
<th scope="col">Метка</th>
<th scope="col"><span data-toggle="tooltip" data-placement="top" data-original-title="Учитываются только уникальные переходы">Переходов</span></th>
<th scope="col"><span data-toggle="tooltip" data-placement="top" data-original-title="Push-подписки">Подписок</span></th>
<th scope="col"><span data-toggle="tooltip" data-placement="top" data-original-title="Показатель подписываемости">CR подписок</span></th>
</tr>
</thead>
<tbody>
<?php
$num = 30;
if (isset($_GET['page'])) { $page = intval($_GET['page']); } else { $page = 0; } if ($page==0) { $page=1; }
$qu = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT count(id) AS `cnt` FROM t_stat_labels WHERE (ymd >= $dtfrom_sql AND ymd <= $dtto_sql) GROUP BY lbl"));
$co = $qu['cnt'];
$total = intval(($co - 1) / $num) + 1;
$page = intval($page);
if(empty($page) or $page < 0) $page = 1;
if($page > $total) $page = $total;
$start = $page * $num - $num;
$qr = mysqli_query($connect_db, "
SELECT id,
	lbl,
	sum(clk) AS `n_clk`,
	sum(subs) AS `n_subs`
    from t_stat_labels WHERE (ymd >= $dtfrom_sql AND ymd <= $dtto_sql) GROUP BY lbl ORDER BY n_clk DESC LIMIT $start, $num");
if ($co > 0) {
?>
<?php
while($row = mysqli_fetch_assoc($qr)) {
?>
<tr class="table-light">
<td><?php echo htmlspecialchars($row['lbl']); ?></td>
<td><?php echo $row['n_clk']; ?></td>
<td><?php echo $row['n_subs']; ?></td>
<td>
<?php if ($row['n_subs'] > 0) { echo number_format($row['n_subs']/$row['n_clk']*100,2,'.',','); } else { echo '0.00'; } ?>
</td>
</tr>
<?php
}
} else {
?>
<tr class="table-light"><td colspan="4"><center>Нет данных</center></td></tr>
<?php } ?>
</tbody>
</table>
</div>
<?php
$pageNav = new SimPageNav();
$pages = ceil($co/$num);
echo $pageNav->getLinks($pages, 1, $page, 10, 'page');
}
?>


<script type='text/javascript'>
$(document).ready(function() {
<?php if ($co > 0) { ?>
$('#tb_stat').DataTable({
"paging": false,
"searching": false,
"autoWidth": true,
"info": false,
"order": [[ 0, "desc" ]],
 columnDefs: [
{ type: 'de_date', targets: 0 }
]
});
<?php } ?>

$('#statre').click(function(){
var dt1 = $('#statgo').val();
var dt2 = $('#statend').val();
if (dt1 === "" || dt2 === "") { ndt = ''; } else { ndt = dt1+'-'+dt2; }
var nowurl = window.location.href;
var newurl = updateURLParameter(nowurl,'date',ndt);
$(location).attr('href',newurl);
});

$('#crabsdatepicker').datepicker({
format: "dd.mm.yyyy",
orientation: "bottom auto",
language: "ru",
todayHighlight: true
});

$('#crabsdatepicker2').datepicker({
format: "dd.mm.yyyy",
orientation: "bottom auto",
language: "ru",
todayHighlight: true
});

$('#statty').change(function() {
var clrval = $("#statty").val();

if (clrval == 0) {
var nowurl = '<?php echo crabs_protocol().SITE; ?>/<?php echo $adm['apath']; ?>/stat';
}
if (clrval == 1) {
var nowurl = '<?php echo crabs_protocol().SITE; ?>/<?php echo $adm['apath']; ?>/stat?ty=lnd';
}
if (clrval == 2) {
var nowurl = '<?php echo crabs_protocol().SITE; ?>/<?php echo $adm['apath']; ?>/stat?ty=str';
}
if (clrval == 3) {
var nowurl = '<?php echo crabs_protocol().SITE; ?>/<?php echo $adm['apath']; ?>/stat?ty=msg';
}
if (clrval == 4) {
var nowurl = '<?php echo crabs_protocol().SITE; ?>/<?php echo $adm['apath']; ?>/stat?ty=lbl';
}
$(location).attr('href',nowurl);
});

});
</script>

<!-- /Контент -->
<?php include('inc/bottom.php'); ?>