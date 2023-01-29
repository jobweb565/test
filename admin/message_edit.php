<?php
$pname = 'Редактирование сообщения';
include('inc/top.php');
if ($nowusr['ty'] != 0 && !in_array('3', $nowusr_ty)) { exit('error'); }
$getid = intval($_GET['id']);
$data = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT * FROM `t_messages` WHERE id='$getid' LIMIT 1"));
?>
<!-- Контент -->

<link href="js/plugins/tagsinput/tagsinput.css" rel="stylesheet" type="text/css">

<form enctype="multipart/form-data" action="javascript:void(null);" id="form_crabs_add" method="POST">
<input type="hidden" name="id" value="<?php echo $getid; ?>" />
<fieldset>
<legend>Редактирование сообщения</legend>

<input type="hidden" name="id" value="<?php echo $getid; ?>" />

<div class="form-group">
<label for="iti">Заголовок:</label>
<input type="text" class="form-control" id="iti" name="iti" placeholder="Введите заголовок" value="<?php echo htmlspecialchars($data['ti']); ?>" />
</div>

<div class="form-group">
<label for="imsg">Сообщение:</label>
<input type="text" class="form-control" id="imsg" name="imsg" placeholder="Введите сообщение" value="<?php echo htmlspecialchars($data['msg']); ?>" />
</div>

<div class="form-group">
<label for="url">Ссылка:</label>
<input type="text" class="form-control" id="url" name="url" placeholder="Введите адрес ссылки" value="<?php echo htmlspecialchars($data['url']); ?>" />
</div>

<div class="form-group">
<label for="tag">Теги <span data-toggle="tooltip" data-placement="top" data-original-title="Нужны только для поиска сообщений по тегу. Ввод через запятую. Можно не заполнять">(?)</span>:</label>
<input type="text" class="form-control" data-role="tagsinput" id="tag" name="tag" placeholder="Введите тег" value="<?php echo htmlspecialchars($data['tags']); ?>" />
</div>

<div class="form-group">
<label for="img_sm" class="form-control-label">Иконка (мин 192x192):</label><br />
<input type="file" id="img_sm" name="img_sm" accept="image/*" />
</div>

<div class="form-group">
<label for="img_big" class="form-control-label">Изображение (мин 400x250):</label><br />
<input type="file" id="img_big" name="img_big" accept="image/*" />
</div>

<a href="javascript://" id="crabs_save" class="btn btn-primary">Сохранить</a>
</fieldset>
</form>

<script src="js/plugins/tagsinput/tagsinput.js"></script>

<script type='text/javascript'>
//Редактирование
$('#crabs_save').click(function(){
var data = new FormData($('#form_crabs_add')[0]);
data.append('token', crabs_tkn);
$.ajax({
type: 'POST',
processData: false,
contentType: false,
url: '/actions/admin_message_edit.php',
data: data,
cache: false,
success: function(res){
if (res == 'ti') {
$.jGrowl('Введите заголовок', { theme: 'growl-error' });
}
if (res == 'msg') {
$.jGrowl('Введите сообщение', { theme: 'growl-error' });
}
if (res == 'url') {
$.jGrowl('Введите адрес ссылки', { theme: 'growl-error' });
}
if (res == 'img_sm_big') {
$.jGrowl('Размер иконки должен быть не более 1 Мб', { theme: 'growl-error' });
}
if (res == 'img_sm_error') {
$.jGrowl('Выберите иконку, не менее 192x192 px', { theme: 'growl-error' });
}
if (res == 'img_big_big') {
$.jGrowl('Размер изображения должен быть не более 1 Мб', { theme: 'growl-error' });
}
if (res == 'img_big_error') {
$.jGrowl('Выберите изображение, не менее 400x250 px', { theme: 'growl-error' });
}
if (res == '1') {
$.jGrowl('Сохранено', { theme: 'growl-success' });
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
</script>

<!-- /Контент -->
<?php include('inc/bottom.php'); ?>