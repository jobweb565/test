<?php
$pname = 'Редактирование пользователя';
include('inc/top.php');
if ($nowusr['ty'] != 0 && !in_array('7', $nowusr_ty)) { exit('error'); }
$getid = intval($_GET['id']);
$data = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT * FROM `t_users` WHERE uid='$getid' LIMIT 1"));
$tys = explode(' ',$data['ty']);
?>
<!-- Контент -->

 
<form enctype="multipart/form-data" action="javascript:void(null);" id="form_crabs_add" method="POST">
<input type="hidden" name="id" value="<?php echo $getid; ?>" />
<fieldset>
<legend>Редактирование пользователя</legend>

<div class="form-group">
<label for="ilgn">Логин:</label>
<input type="text" class="form-control" id="ilgn" name="ilgn" placeholder="Введите логин" value="<?php echo htmlspecialchars($data['log']); ?>" />
</div>

<div class="form-group">
<label for="ipass">Пароль:</label>
<input type="text" class="form-control" id="ipass" name="ipass" placeholder="Введите пароль" value="<?php if ($nowusr['ty'] != 0) { echo htmlspecialchars($data['pas']); } else { echo 'Скрыто в демо аккаунте'; } ?>" />
</div>

<div class="form-group">
<label>Права:</label>
<div class="custom-control custom-checkbox">
<input type="checkbox" class="custom-control-input" name="lbl_0" id="lbl_0" <?php if (in_array('0', $tys)) {echo ' checked="checked"';} ?>>
<label class="custom-control-label" for="lbl_0">Демо-режим (может только всё просматривать)</label>
</div>
<div class="custom-control custom-checkbox">
<input type="checkbox" class="custom-control-input" name="lbl_1" id="lbl_1" <?php if (in_array('1', $tys)) {echo ' checked="checked"';} ?>>
<label class="custom-control-label" for="lbl_1">Доступ к детальной статистике</label>
</div>
<div class="custom-control custom-checkbox">
<input type="checkbox" class="custom-control-input" name="lbl_2" id="lbl_2" <?php if (in_array('2', $tys)) {echo ' checked="checked"';} ?>>
<label class="custom-control-label" for="lbl_2">Рассылка сообщений</label>
</div>
<div class="custom-control custom-checkbox">
<input type="checkbox" class="custom-control-input" name="lbl_3" id="lbl_3" <?php if (in_array('3', $tys)) {echo ' checked="checked"';} ?>>
<label class="custom-control-label" for="lbl_3">Управление сообщениями</label>
</div>
<div class="custom-control custom-checkbox">
<input type="checkbox" class="custom-control-input" name="lbl_4" id="lbl_4" <?php if (in_array('4', $tys)) {echo ' checked="checked"';} ?>>
<label class="custom-control-label" for="lbl_4">Управление потоками</label>
</div>
<div class="custom-control custom-checkbox">
<input type="checkbox" class="custom-control-input" name="lbl_5" id="lbl_5" <?php if (in_array('5', $tys)) {echo ' checked="checked"';} ?>>
<label class="custom-control-label" for="lbl_5">Сброс статистики</label>
</div>
<div class="custom-control custom-checkbox">
<input type="checkbox" class="custom-control-input" name="lbl_6" id="lbl_6" <?php if (in_array('6', $tys)) {echo ' checked="checked"';} ?>>
<label class="custom-control-label" for="lbl_6">Просмотр и изменение настроек</label>
</div>
<div class="custom-control custom-checkbox">
<input type="checkbox" class="custom-control-input" name="lbl_7" id="lbl_7" <?php if (in_array('7', $tys)) {echo ' checked="checked"';} ?>>
<label class="custom-control-label" for="lbl_7">Управление пользователями</label>
</div>
</div>

<a href="javascript://" id="crabs_save" class="btn btn-primary">Сохранить</a>
</fieldset>
</form>


<script type='text/javascript'>
//Сохранение
$('#crabs_save').click(function(){
var data = new FormData($('#form_crabs_add')[0]);
data.append('token', crabs_tkn);
$.ajax({
type: 'POST',
processData: false,
contentType: false,
url: '/actions/admin_user_edit.php',
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
if (res == 'error') {
$.jGrowl('Ошибка', { theme: 'growl-error' });
}
if (res == '1') {
$.jGrowl('Сохранено', { theme: 'growl-success' });
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