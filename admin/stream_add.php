<?php
$pname = 'Добавление потока';
include('inc/top.php');
if ($nowusr['ty'] != 0 && !in_array('4', $nowusr_ty)) { exit('error'); }
?>
<!-- Контент -->

 
<form enctype="multipart/form-data" action="javascript:void(null);" id="form_crabs_add" method="POST">
<fieldset>
<legend>Добавление потока</legend>

<div class="form-group">
<label for="ity" class="form-control-label">Тип потока:</label>
<select id="ity" name="ity" class="form-control">
<option value="0" selected>Лендинг</option>
<option value="1">Сайт</option>
</select>
</div>

<div class="form-group">
<label for="iti">Название потока:</label>
<input type="text" class="form-control" id="iti" name="iti" placeholder="Введите название потока" />
</div>

<div class="form-group" id="show_lnd">
<label for="land" class="form-control-label">Лендинг:</label>
<div class="input-group">
<select class="form-control" id="land" name="land">
<option value="1" data-img="inc/landings/1/screen.jpg" selected="selected">Я не робот</option>
<option value="2" data-img="inc/landings/2/screen.jpg">Нет соединения с интернетом</option>
<option value="3" data-img="inc/landings/3/screen.jpg">Ваш файл готов к скачиванию</option>
<option value="4" data-img="inc/landings/4/screen.jpg">Эротический лендинг</option>
<option value="5" data-img="inc/landings/5/screen.jpg">Чтобы продолжить, нажмите...</option>
<option value="6" data-img="inc/landings/6/screen.jpg">FlashPlayer заблокирован</option>
<option value="7" data-img="inc/landings/7/screen.jpg">18+</option>
<option value="8" data-img="inc/landings/8/screen.jpg">Видео</option>
<option value="9" data-img="inc/landings/9/screen.jpg">FlashPlayer заблокирован с видео</option>
<option value="10" data-img="inc/landings/10/screen.jpg">Нажмите кнопку разрешить чтобы смотреть видео</option>
</select>
<div class="input-group-append">
<a type="button" href="javascript://" id="paytemplate_view" class="btn btn-primary" data-toggle="tooltip" data-placement="bottom" data-original-title="Предпросмотр"><i class="fa fa-eye"></i></a>
</div>
</div>
</div>

<div class="form-group">
<label for="trafback">Редирект после подписки<span id="show_tb" style="display:none;">&nbsp;(можно не заполнять - редиректа не будет)</span>:</label>
<input type="text" class="form-control" id="trafback" name="trafback" placeholder="http://" />
</div>

<div class="form-group">
<label for="trafback2">Редирект после отказа<span id="show_tb2" style="display:none;">&nbsp;(можно не заполнять - редиректа не будет)</span>:</label>
<input type="text" class="form-control" id="trafback2" name="trafback2" placeholder="http://" />
</div>

<div class="form-group">
<label>Проброс Get-параметров <span data-toggle="tooltip" data-placement="top" data-original-title="Все входящие в поток Get-параметры будут переданы в редирект">(?)</span>:</label>
<div class="custom-control custom-checkbox">
<input type="checkbox" class="custom-control-input" name="iget" id="iget">
<label class="custom-control-label" for="iget">Пробрасывать Get-параметры</label>
</div>
</div>

<a href="javascript://" id="crabs_add" class="btn btn-primary">Добавить</a>
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
$(function() {

$('#ity').change(function() {
var ity = $("#ity").val();
if (ity == 0) {
$('#show_lnd').show('slow');
$('#show_tb').hide('slow');
$('#show_tb2').hide('slow');
}
if (ity == 1) {
$('#show_lnd').hide('slow');
$('#show_tb').show('slow');
$('#show_tb2').show('slow');
}
});

});

function crabs_set_c(c){
$('#icou').val(c);
};

//Добавление
$('#crabs_add').click(function(){
var data = new FormData($('#form_crabs_add')[0]);
data.append('token', crabs_tkn);
$.ajax({
type: 'POST',
processData: false,
contentType: false,
url: '/actions/admin_stream_add.php',
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
$(location).attr('href','streams');
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