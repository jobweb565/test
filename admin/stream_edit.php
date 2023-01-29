<?php
$pname = 'Редактирование потока';
include('inc/top.php');
if ($nowusr['ty'] != 0 && !in_array('4', $nowusr_ty)) { exit('error'); }
$getid = intval($_GET['id']);
$data = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT * FROM `t_streams` WHERE id='$getid' LIMIT 1"));
?>
<!-- Контент -->

 
<form enctype="multipart/form-data" action="javascript:void(null);" id="form_crabs_add" method="POST">
<input type="hidden" name="id" value="<?php echo $getid; ?>" />
<fieldset>
<legend>Редактирование потока</legend>
<?php if ($data['ty'] == 0) { ?>
<div class="alert alert-primary">
При изменения лендинга Вам нужно будет у себя обновить ссылку, на которую идёт трафик.
</div>
<?php } ?>
<div class="form-group">
<label for="ity" class="form-control-label">Тип потока:</label>
<select id="ity" class="form-control" disabled="disabled">
<option value="0"<?php if ($data['ty'] == 0) { echo ' selected="selected"'; } ?>>Лендинг</option>
<option value="1"<?php if ($data['ty'] == 1) { echo ' selected="selected"'; } ?>>Сайт</option>
</select>
</div>

<div class="form-group">
<label for="iti">Название потока:</label>
<input type="text" class="form-control" id="iti" name="iti" placeholder="Введите название потока" value="<?php echo htmlspecialchars($data['ti']); ?>" />
</div>

<div class="form-group" <?php if ($data['ty'] == 1) { echo 'style="display:none;"'; } ?>>
<label for="land" class="form-control-label">Лендинг:</label>
<div class="input-group">
<select class="form-control" id="land" name="land">
<option value="1"<?php if ($data['landing'] == 1) { echo ' selected="selected"'; } ?> data-img="inc/landings/1/screen.jpg" selected="selected">Я не робот</option>
<option value="2"<?php if ($data['landing'] == 2) { echo ' selected="selected"'; } ?> data-img="inc/landings/2/screen.jpg">Нет соединения с интернетом</option>
<option value="3"<?php if ($data['landing'] == 3) { echo ' selected="selected"'; } ?> data-img="inc/landings/3/screen.jpg">Ваш файл готов к скачиванию</option>
<option value="4"<?php if ($data['landing'] == 4) { echo ' selected="selected"'; } ?> data-img="inc/landings/4/screen.jpg">Эротический лендинг</option>
<option value="5"<?php if ($data['landing'] == 5) { echo ' selected="selected"'; } ?> data-img="inc/landings/5/screen.jpg">Чтобы продолжить, нажмите...</option>
<option value="6"<?php if ($data['landing'] == 6) { echo ' selected="selected"'; } ?> data-img="inc/landings/6/screen.jpg">FlashPlayer заблокирован</option>
<option value="7"<?php if ($data['landing'] == 7) { echo ' selected="selected"'; } ?> data-img="inc/landings/7/screen.jpg">18+</option>
<option value="8"<?php if ($data['landing'] == 8) { echo ' selected="selected"'; } ?> data-img="inc/landings/8/screen.jpg">Видео</option>
<option value="9"<?php if ($data['landing'] == 9) { echo ' selected="selected"'; } ?> data-img="inc/landings/9/screen.jpg">FlashPlayer заблокирован с видео</option>
<option value="10"<?php if ($data['landing'] == 10) { echo ' selected="selected"'; } ?> data-img="inc/landings/10/screen.jpg">Нажмите кнопку разрешить чтобы смотреть видео</option>
</select>
<div class="input-group-append">
<a type="button" href="javascript://" id="paytemplate_view" class="btn btn-primary" data-toggle="tooltip" data-placement="bottom" data-original-title="Предпросмотр"><i class="fa fa-eye"></i></a>
</div>
</div>
</div>

<div class="form-group">
<label for="trafback">Редирект после подписки<span id="show_tb" <?php if ($data['ty'] == 0) { echo 'style="display:none;"'; } ?>> (можно не заполнять - редиректа не будет)</span>:</label>
<input type="text" class="form-control" id="trafback" name="trafback" placeholder="http://" value="<?php echo htmlspecialchars($data['tb']); ?>" />
</div>

<div class="form-group">
<label for="trafback2">Редирект после отказа<span id="show_tb2" <?php if ($data['ty'] == 0) { echo 'style="display:none;"'; } ?>> (можно не заполнять - редиректа не будет)</span>:</label>
<input type="text" class="form-control" id="trafback2" name="trafback2" placeholder="http://" value="<?php echo htmlspecialchars($data['tb2']); ?>" />
</div>

<div class="form-group">
<label>Проброс Get-параметров <span data-toggle="tooltip" data-placement="top" data-original-title="Все входящие в поток Get-параметры будут переданы в редирект">(?)</span>:</label>
<div class="custom-control custom-checkbox">
<input type="checkbox" class="custom-control-input" name="iget" id="iget" <?php if ($data['iget'] == 1) { echo 'checked="checked"'; } ?> />
<label class="custom-control-label" for="iget">Пробрасывать Get-параметры</label>
</div>
</div>

<a href="javascript://" id="crabs_save" class="btn btn-primary">Сохранить</a>
</fieldset>
</form>


<div id="modal_template_view" class="modal fade">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title">Лендинг <span id="template_ti"></span></h4>
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
</div>
<div class="modal-body" id="template_img">
</div>
<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
</div>
</div>
</div>
</div>


<script type='text/javascript'>
//Сохранение
$('#crabs_save').click(function(){
var data = new FormData($('#form_crabs_add')[0]);
data.append('token', crabs_tkn);
$.ajax({
type: 'POST',
processData: false,
contentType: false,
url: '/actions/admin_stream_edit.php',
data: data,
cache: false,
success: function(res){
if (res == 'ti') {
$.jGrowl('Введите название потока', { theme: 'growl-error' });
}
if (res == 'land') {
$.jGrowl('Выберите лендинг', { theme: 'growl-error' });
}
if (res == 'tb') {
$.jGrowl('Введите ссылку на редирект', { theme: 'growl-error' });
}
if (res == 'tbe') {
$.jGrowl('Неверная ссылка на редирект', { theme: 'growl-error' });
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

$('#paytemplate_view').click(function(){
var crabsimg = $('#land').find(':selected').attr('data-img');
var tmplt = $("#land option:selected").text();
$('#template_img').html('<center><img src="'+crabsimg+'" class="ptimg"></center>');
$('#template_ti').text(tmplt);
$('#modal_template_view').modal('show');
});
</script>

<!-- /Контент -->
<?php include('inc/bottom.php'); ?>