<?php
$pname = 'Сброс';
include('inc/top.php');
if ($nowusr['ty'] != 0 && !in_array('5', $nowusr_ty)) { exit('error'); }
$data = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT * FROM `t_data` LIMIT 1"));
?>
<!-- Контент -->

<form>
<fieldset>
<legend>Сброс статистики</legend>
<hr />
<div class="form-group">
<label for="wclr" class="form-control-label">Область очистки:</label>
<select id="wclr" name="wclr" class="form-control">
<option value="1" selected="selected">Вся статистика</option>
<option value="2">Статистика по меткам</option>
</select>
</div>

<a href="javascript://" id="crabs_clear" class="btn btn-primary">Очистить</a>
</fieldset>
</form>



<script type='text/javascript'>
//Очистка
$('#crabs_clear').click(function(){
var wclr = $('#wclr').val();
$.ajax({
type: 'POST',
url: '/actions/admin_stat_clear.php',
data: {'wclr': wclr, 'token': crabs_tkn},
cache: false,
success: function(result){
if (result == '1') {
$.jGrowl('Очищено', { theme: 'growl-success' });
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