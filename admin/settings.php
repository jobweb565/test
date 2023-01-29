<?php
$pname = 'Настройки';
include('inc/top.php');
if ($nowusr['ty'] != 0 && !in_array('6', $nowusr_ty)) { exit('error'); }
$data = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT * FROM `t_data` LIMIT 1"));
?>
<!-- Контент -->

<form>
<fieldset>
<legend>Настройки Push</legend>
<hr />
<div class="form-group">
<label for="idmcrabs">Текущий домен лендингов <span data-toggle="tooltip" data-placement="top" data-original-title="Необходим SSL сертификат">(?)</span>:</label>
<input type="text" class="form-control" id="idmcrabs" placeholder="example.ru" value="<?php echo htmlspecialchars($data['pdomain']); ?>">
</div>

<div class="form-group">
<label for="sendid">Идентификатор отправителя:</label>
<input type="text" class="form-control" id="sendid" placeholder="Идентификатор отправителя" value="<?php echo htmlspecialchars($data['sender_id']); ?>">
</div>

<div class="form-group">
<label for="srvk">Ключ сервера:</label>
<input type="text" class="form-control" id="srvk" placeholder="Ключ сервера" value="<?php if ($nowusr['ty'] != 0) { echo htmlspecialchars($data['server_key']); } else { echo 'Скрыто в демо аккаунте'; } ?>">
</div>

<a href="javascript://" id="b_save_d" class="btn btn-primary">Сохранить</a>
</fieldset>
</form>
<br /><br />
<form>
<fieldset>
<legend>Настройки панели</legend>
<hr />
<div class="form-group">
<label for="ipath">Путь:</label>
<input type="text" class="form-control" id="ipath" placeholder="Путь" value="<?php echo htmlspecialchars($data['apath']); ?>">
</div>
<a href="javascript://" id="b_save" class="btn btn-primary">Сохранить</a>
</fieldset>
</form>

<script type='text/javascript'>
//Сохранение настроек админки
$('#b_save').click(function(){
var path = $('#ipath').val();
$.ajax({
type: 'POST',
url: '/actions/admin_data_save.php',
data: {'path': path, 'token': crabs_tkn},
cache: false,
success: function(result){
if (result == '0') {
$.jGrowl('Заполните все поля', { theme: 'growl-error' });
}
if (result == '1') {
$.jGrowl('Сохранено', { theme: 'growl-success' });
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
});

//Сохранение домена сбора
$('#b_save_d').click(function(){
var domain = $('#idmcrabs').val();
var sendid = $('#sendid').val();
var srvk = $('#srvk').val();
$.ajax({
type: 'POST',
url: '/actions/admin_data_pdomain_save.php',
data: {'domain': domain, 'sendid': sendid, 'srvk': srvk, 'token': crabs_tkn},
cache: false,
success: function(result){
if (result == '0') {
$.jGrowl('Заполните все поля', { theme: 'growl-error' });
}
if (result == '1') {
$.jGrowl('Сохранено', { theme: 'growl-success' });
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
});

</script>

<!-- /Контент -->
<?php include('inc/bottom.php'); ?>