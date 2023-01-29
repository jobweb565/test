<?php
$pname = 'Сообщения';
include('inc/top.php');
if ($nowusr['ty'] != 0 && !in_array('3', $nowusr_ty)) { exit('error'); }
include('../inc/wu_pagination.php');
?>
<!-- Контент -->

<link href="js/plugins/tagsinput/tagsinput.css" rel="stylesheet" type="text/css">

<h4>Сообщения <sup class="badge badge-primary badge-pill" style="cursor:pointer;" id="isearch"><i class="fa fa-search"></i></sup> <button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#modal_add">Добавить сообщение</button></h4>

<div id="crabsshow" style="display:<?php if (isset($_GET['tag'])) { echo 'block'; } else { echo 'none'; } ?>;">
<div class="input-daterange" style="max-width:200px;display:inline-block">
<span class="add-on">Поиск по тегу:</span>
<input type="text" class="form-control" id="itag" value="<?php echo htmlspecialchars($_GET['tag']); ?>" placeholder="Введите тег" />
</div>
<a href="javascript://" id="retag" class="btn btn-primary" style="margin-top: -4px;"><i class="fa fa-check"></i></a>
</div>
<br />

<div id="modal_add" class="modal fade">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title">Добавление сообщения</h4>
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
</div>
<div class="modal-body">

<form enctype="multipart/form-data" action="javascript:void(null);" id="form_crabs_add" method="POST">
<fieldset>

<div class="form-group">
<label for="iti">Заголовок:</label>
<input type="text" class="form-control" id="iti" name="iti" placeholder="Введите заголовок" />
</div>

<div class="form-group">
<label for="imsg">Сообщение:</label>
<input type="text" class="form-control" id="imsg" name="imsg" placeholder="Введите сообщение" />
</div>

<div class="form-group">
<label for="url">Ссылка:</label>
<input type="text" class="form-control" id="url" name="url" placeholder="Введите адрес ссылки" />
</div>

<div class="form-group">
<label for="tag">Теги <span data-toggle="tooltip" data-placement="top" data-original-title="Нужны только для поиска сообщений по тегу. Ввод через запятую. Можно не заполнять">(?)</span>:</label>
<input type="text" class="form-control" data-role="tagsinput" id="tag" name="tag" placeholder="Введите тег" />
</div>

<div class="form-group">
<label for="img_sm" class="form-control-label">Иконка (мин 192x192):</label><br />
<input type="file" id="img_sm" name="img_sm" accept="image/*" />
</div>

<div class="form-group">
<label for="img_big" class="form-control-label">Изображение (мин 400x250):</label><br />
<input type="file" id="img_big" name="img_big" accept="image/*" />
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
<th scope="col">Дата</th>
<th scope="col">Сообщение</th>
<th scope="col">Действия</th>
</tr>
</thead>
<tbody>
<?php
if (isset($_GET['tag'])) { $imtag = mysqli_real_escape_string($connect_db, trim($_GET['tag'])); $where_add = "WHERE tags LIKE '%,".$imtag.",%'"; } else { $where_add = ""; }
$num = 30;
if (isset($_GET['page'])) { $page = intval($_GET['page']); } else { $page = 0; } if ($page==0) { $page=1; }
$qu = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT count(`id`) AS `cnt` FROM `t_messages` $where_add"));
$co = $qu['cnt'];
$total = intval(($co - 1) / $num) + 1;
$page = intval($page);
if(empty($page) or $page < 0) $page = 1;
if($page > $total) $page = $total;
$start = $page * $num - $num;
$qr = mysqli_query($connect_db, "SELECT id,ti,msg,url,img_sm,img_big,tags,dt FROM `t_messages` $where_add ORDER BY id DESC LIMIT $start, $num");
if ($co > 0) {
?>
<?php
while($row = mysqli_fetch_assoc($qr)) {
?>
<tr class="table-light">
<td>
<span data-toggle="tooltip" data-placement="bottom" data-original-title="#<?php echo $row['id']; ?>">
<?php echo wudate($row['dt']); ?>
</span><br />
<?php
$tagsarr = explode(",", $row['tags']);
foreach($tagsarr as $key => $value) {    
echo '<span class="badge badge-pill badge-dark" style="cursor:pointer;" onclick="crabtag(\''.$value.'\');">'.$value.'</span> ';    
}
?>
</td>
<td>
<div style="float: left;margin-right: 10px;" class="imt">
<img src="/img/upl/<?php echo $row['img_sm']; ?>" style="max-width:50px" />
<span><img src="/img/upl/<?php echo $row['img_big']; ?>"></span>
</div>
<div>
<b><?php echo htmlspecialchars($row['ti']); ?></b><br />
<?php echo htmlspecialchars($row['msg']); ?>
</div>
</td>
<td>
<a href="gosend?msg_m=<?php echo $row['id']; ?>" target="_blank" class="btn btn-primary" data-toggle="tooltip" data-placement="bottom" data-original-title="В массовую рассылку"><i class="fa fa-send"></i></a>
<a href="gosend?msg=<?php echo $row['id']; ?>" target="_blank" class="btn btn-primary" data-toggle="tooltip" data-placement="bottom" data-original-title="В рассылку"><i class="fa fa-send-o"></i></a>
<a href="message_edit?id=<?php echo $row['id']; ?>" class="btn btn-primary" data-toggle="tooltip" data-placement="bottom" data-original-title="Редактировать"><i class="fa fa-pencil"></i></a>
<button type="button" onclick="idel('<?php echo $row['id']; ?>');" class="btn btn-danger" data-toggle="tooltip" data-placement="bottom" data-original-title="Удалить"><i class="fa fa-trash-o"></i></button>
</td>
</tr>
<?php
}
} else {
?>
<tr class="table-light"><td colspan="3"><center>Сообщений нет</center></td></tr>
<?php } ?>
</tbody>
</table>
</div>
<?php
$pageNav = new SimPageNav();
$pages = ceil($co/$num);
echo $pageNav->getLinks($pages, 1, $page, 10, 'page');
?>

<script src="js/plugins/tagsinput/tagsinput.js"></script>

<script type='text/javascript'>
$('#retag').click(function(){
var tag = $('#itag').val();
var nowurl = window.location.href;
var newurl = updateURLParameter(nowurl,'tag',tag);
$(location).attr('href',newurl);
});

function crabtag(tag){
var nowurl = window.location.href;
var newurl = updateURLParameter(nowurl,'tag',tag);
$(location).attr('href',newurl);
}

$('#isearch').click(function(){
$('#crabsshow').toggle('slow');
});

//Добавление
$('#crabs_add').click(function(){
var data = new FormData($('#form_crabs_add')[0]);
data.append('token', crabs_tkn);
$.ajax({
type: 'POST',
processData: false,
contentType: false,
url: '/actions/admin_message_add.php',
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
$(location).attr('href','messages');
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
url: '/actions/admin_message_del.php',
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