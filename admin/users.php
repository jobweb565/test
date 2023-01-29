<?php
$pname = 'Пользователи';
include('inc/top.php');
if ($nowusr['ty'] != 0 && !in_array('7', $nowusr_ty)) { exit('error'); }
include('../inc/wu_pagination.php');
$tuarr = array(
'0' => 'Демо-режим (может только всё просматривать)',
'1' => 'Доступ к детальной статистике',
'2' => 'Рассылка сообщений',
'3' => 'Управление сообщениями',
'4' => 'Управление потоками',
'5' => 'Сброс статистики',
'6' => 'Просмотр и изменение настроек',
'7' => 'Управление пользователями'
);
?>
<!-- Контент -->


<h4>Пользователи <button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#modal_add">Добавить пользователя</button></h4>
<br />

<div id="modal_add" class="modal fade">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title">Добавление пользователя</h4>
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
</div>
<div class="modal-body">

<form enctype="multipart/form-data" action="javascript:void(null);" id="form_crabs_add" method="POST">
<fieldset>

<div class="form-group">
<label for="ilgn">Логин:</label>
<input type="text" class="form-control" id="ilgn" name="ilgn" placeholder="Введите логин" />
</div>

<div class="form-group">
<label for="ipass">Пароль:</label>
<input type="text" class="form-control" id="ipass" name="ipass" placeholder="Введите пароль" />
</div>

<div class="form-group">
<label>Права:</label>
<div class="custom-control custom-checkbox">
<input type="checkbox" class="custom-control-input" name="lbl_0" id="lbl_0">
<label class="custom-control-label" for="lbl_0">Демо-режим (может только всё просматривать)</label>
</div>
<div class="custom-control custom-checkbox">
<input type="checkbox" class="custom-control-input" name="lbl_1" id="lbl_1">
<label class="custom-control-label" for="lbl_1">Доступ к детальной статистике</label>
</div>
<div class="custom-control custom-checkbox">
<input type="checkbox" class="custom-control-input" name="lbl_2" id="lbl_2">
<label class="custom-control-label" for="lbl_2">Рассылка сообщений</label>
</div>
<div class="custom-control custom-checkbox">
<input type="checkbox" class="custom-control-input" name="lbl_3" id="lbl_3">
<label class="custom-control-label" for="lbl_3">Управление сообщениями</label>
</div>
<div class="custom-control custom-checkbox">
<input type="checkbox" class="custom-control-input" name="lbl_4" id="lbl_4">
<label class="custom-control-label" for="lbl_4">Управление потоками</label>
</div>
<div class="custom-control custom-checkbox">
<input type="checkbox" class="custom-control-input" name="lbl_5" id="lbl_5">
<label class="custom-control-label" for="lbl_5">Сброс статистики</label>
</div>
<div class="custom-control custom-checkbox">
<input type="checkbox" class="custom-control-input" name="lbl_6" id="lbl_6">
<label class="custom-control-label" for="lbl_6">Просмотр и изменение настроек</label>
</div>
<div class="custom-control custom-checkbox">
<input type="checkbox" class="custom-control-input" name="lbl_7" id="lbl_7">
<label class="custom-control-label" for="lbl_7">Управление пользователями</label>
</div>
</div>

</fieldset>
</form>

</div>
<div class="modal-footer">
<button type="button" id="crabs_add" class="btn btn-primary">Добавить</button>
<button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
</div>
</div>
</div>
</div>



<div class="table-responsive">
<table class="table table-hover">
<thead>
<tr>
<th scope="col">Логин</th>
<th scope="col">Пароль</th>
<th scope="col">Права</th>
<th scope="col">Действия</th>
</tr>
</thead>
<tbody>
<?php
$num = 30;
if (isset($_GET['page'])) { $page = intval($_GET['page']); } else { $page = 0; } if ($page==0) { $page=1; }
$qu = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT count(`uid`) AS `cnt` FROM `t_users`"));
$co = $qu['cnt'];
$total = intval(($co - 1) / $num) + 1;
$page = intval($page);
if(empty($page) or $page < 0) $page = 1;
if($page > $total) $page = $total;
$start = $page * $num - $num;
$qr = mysqli_query($connect_db, "SELECT * FROM `t_users` ORDER BY uid DESC LIMIT $start, $num");
if ($co > 0) {
?>
<?php
while($row = mysqli_fetch_assoc($qr)) {
?>
<tr class="table-light">
<td>
<span data-toggle="tooltip" data-placement="bottom" data-original-title="#<?php echo $row['uid']; ?>">
<?php echo htmlspecialchars($row['log']); ?>
</span>
</td>
<td>
<?php if ($nowusr['ty'] != 0) { echo htmlspecialchars($row['pas']); } else { echo 'Скрыто в демо аккаунте'; } ?>
</td>
<td>
<?php
$tys = explode(' ',$row['ty']);
foreach($tys as $key) {
echo $tuarr[$key].'<br/>';    
}
?>
</td>
<td>
<a href="user_edit?id=<?php echo $row['uid']; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
<button type="button" onclick="idel('<?php echo $row['uid']; ?>');" class="btn btn-danger" data-toggle="tooltip" data-placement="bottom" data-original-title="Удалить"><i class="fa fa-trash-o"></i></button>
</td>
</tr>
<?php
}
} else {
?>
<tr class="table-light"><td colspan="3"><center>Пользователей нет</center></td></tr>
<?php } ?>
</tbody>
</table>
</div>
<?php
$pageNav = new SimPageNav();
$pages = ceil($co/$num);
echo $pageNav->getLinks($pages, 1, $page, 10, 'page');
?>


<script type='text/javascript'>
//Добавление
$('#crabs_add').click(function(){
var data = new FormData($('#form_crabs_add')[0]);
data.append('token', crabs_tkn);
$.ajax({
type: 'POST',
processData: false,
contentType: false,
url: '/actions/admin_user_add.php',
data: data,
cache: false,
success: function(res){
if (res == 'login') {
$.jGrowl('Введите логин', { theme: 'growl-error' });
}
if (res == 'pass') {
$.jGrowl('Введите пароль', { theme: 'growl-error' });
}
if (res == 'ty') {
$.jGrowl('Выберите права', { theme: 'growl-error' });
}
if (res == 'is') {
$.jGrowl('Пользователь уже существует', { theme: 'growl-error' });
}
if (res == '1') {
$(location).attr('href','users');
}
if (res == 'error') {
$.jGrowl('Ошибка', { theme: 'growl-error' });
}
if (res == 'demo') {
$.jGrowl('Недоступно в демо аккаунте', { theme: 'growl-error' });
}
},
error: function(){
$.jGrowl('Ошибка сервера', { theme: 'growl-error' });
}
});
});

function idel(id){
if (confirm("Удалить?")) {
$.ajax({
type: 'POST',
url: '/actions/admin_user_del.php',
data: {'id': id, 'token': crabs_tkn},
cache: false,
success: function(result){
if (result == '1') {
location.reload();
}
if (result == 'error') {
$.jGrowl('Ошибка', { theme: 'growl-error' });
}
if (result == 'admin') {
$.jGrowl('Данного пользователя удалить нельзя', { theme: 'growl-error' });
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

$('.custom-control-input').bind("change keyup paste input", function(e) {
var checked = $(this).prop('checked');
var tid = e.target.id;
if (tid === 'lbl_0' && checked == true) {
$("#lbl_1").prop( "checked", false );
$("#lbl_2").prop( "checked", false );
$("#lbl_3").prop( "checked", false );
$("#lbl_4").prop( "checked", false );
$("#lbl_5").prop( "checked", false );
$("#lbl_6").prop( "checked", false );
$("#lbl_7").prop( "checked", false );
} else {
$("#lbl_0").prop( "checked", false );
}
});
</script>

<!-- /Контент -->
<?php include('inc/bottom.php'); ?>