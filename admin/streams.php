<?php
$pname = 'Потоки';
include('inc/top.php');
if ($nowusr['ty'] != 0 && !in_array('4', $nowusr_ty)) { exit('error'); }
include('../inc/wu_pagination.php');
$adata = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT id,pdomain FROM `t_data` LIMIT 1"));
?>
<!-- Контент -->


<h4>Потоки <a href="stream_add" class="btn btn-success pull-right">Добавить поток</a></h4>
<br />

<div class="table-responsive">
<table class="table table-hover">
<thead>
<tr>
<th scope="col">Поток</th>
<th scope="col">Тип</th>
<th scope="col">Лендинг</th>
<th scope="col">Редирект</th>
<th scope="col">Ссылка/код</th>
<th scope="col">Действия</th>
</tr>
</thead>
<tbody>
<?php
$num = 30;
if (isset($_GET['page'])) { $page = intval($_GET['page']); } else { $page = 0; } if ($page==0) { $page=1; }
$qu = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT count(`id`) AS `cnt` FROM `t_streams`"));
$co = $qu['cnt'];
$total = intval(($co - 1) / $num) + 1;
$page = intval($page);
if(empty($page) or $page < 0) $page = 1;
if($page > $total) $page = $total;
$start = $page * $num - $num;
$qr = mysqli_query($connect_db, "SELECT * FROM `t_streams` ORDER BY id DESC LIMIT $start, $num");
if ($co > 0) {
?>
<?php
while($row = mysqli_fetch_assoc($qr)) {
?>
<tr class="table-light">
<td>
<span data-toggle="tooltip" data-placement="bottom" data-original-title="#<?php echo $row['id']; ?>, <?php echo wudate($row['dt'],0); ?>">
<?php echo htmlspecialchars($row['ti']); ?>
</span>
</td>
<td>
<?php if ($row['ty'] == 0) { echo 'Лендинг'; } else { echo 'Сторонний сайт'; } ?>
</td>
<td>
<?php if ($row['ty'] == 1) { echo 'Нет'; } else {
if ($row['landing'] == 1) { echo 'Я не робот'; }
if ($row['landing'] == 2) { echo 'Нет соединения с интернетом'; }
if ($row['landing'] == 3) { echo 'Ваш файл готов к скачиванию'; }
if ($row['landing'] == 4) { echo 'Эротический лендинг'; }
if ($row['landing'] == 5) { echo 'Чтобы продолжить, нажмите...'; }
if ($row['landing'] == 6) { echo 'FlashPlayer заблокирован'; }
if ($row['landing'] == 7) { echo '18+'; }
if ($row['landing'] == 8) { echo 'Видео'; }
if ($row['landing'] == 9) { echo 'FlashPlayer заблокирован с видео'; }
if ($row['landing'] == 10) { echo 'Нажмите кнопку разрешить чтобы смотреть видео'; }
} ?>
</td>
<td>
<span data-toggle="tooltip" data-placement="top" data-original-title="Редирект после подписки"><?php if (!empty($row['tb'])) { echo htmlspecialchars($row['tb']); } else { echo 'Нет'; } ?></span>
<br />
<span data-toggle="tooltip" data-placement="top" data-original-title="Редирект после отказа"><?php if (!empty($row['tb2'])) { echo htmlspecialchars($row['tb2']); } else { echo 'Нет'; } ?></span>
</td>
<td>
<button type="button" onclick="getcode('<?php echo $row['id']; ?>');" class="btn btn-success"><i class="fa fa-<?php if ($row['ty'] == 0) { echo 'link'; } else { echo 'code'; } ?>"></i></button>
</td>
<td>
<a href="stream_edit?id=<?php echo $row['id']; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
<button type="button" onclick="idel('<?php echo $row['id']; ?>');" class="btn btn-danger" data-toggle="tooltip" data-placement="bottom" data-original-title="Удалить"><i class="fa fa-trash-o"></i></button>
</td>
</tr>
<?php
}
} else {
?>
<tr class="table-light"><td colspan="6"><center>Потоков нет</center></td></tr>
<?php } ?>
</tbody>
</table>
</div>
<?php
$pageNav = new SimPageNav();
$pages = ceil($co/$num);
echo $pageNav->getLinks($pages, 1, $page, 10, 'page');
?>

<div id="modal_code_view" class="modal fade">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title">Ссылка/код #<span id="nsc"></span></h4>
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
</div>
<div class="modal-body" id="loaded_data">
</div>
<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
</div>
</div>
</div>
</div>

<script type='text/javascript'>
function getcode(id){
$("#nsc").text(id);
$.ajax({
type: 'POST',
url: '/actions/admin_code_load.php',
data: {'id': id, 'token': crabs_tkn},
cache: false,
success: function(res){
if (res == 'error') {
$.jGrowl('Ошибка', { theme: 'growl-error' });
} else {
$("#loaded_data").html(res);
$('#modal_code_view').modal('show');
}
},
error: function(){
$.jGrowl('Ошибка сервера', { theme: 'growl-error' });
}
});
};

function idel(id){
if (confirm("Удалить?")) {
$.ajax({
type: 'POST',
url: '/actions/admin_stream_del.php',
data: {'id': id, 'token': crabs_tkn},
cache: false,
success: function(result){
if (result == '1') {
location.reload();
}
if (result == 'error') {
$.jGrowl('Ошибка', { theme: 'growl-error' });
}
if (result == 'demo') {
$.jGrowl('Недоступно в демо аккаунте', { theme: 'growl-error' });
}
},
error: function(){
$.jGrowl('Ошибка сервера', { theme: 'growl-error' });
}
});
}
}
</script>

<!-- /Контент -->
<?php include('inc/bottom.php'); ?>