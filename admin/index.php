<?php
$pname = 'Главная';
include('inc/top.php');
$data = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT id,note,p_allusers,p_all_showed,p_all_clicks FROM `t_data` LIMIT 1"));

$all_streams = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT COUNT(`id`) AS `cnt` FROM `t_streams`"));
$all_messages = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT COUNT(`id`) AS `cnt` FROM `t_messages`"));

//Вывод статистики
$timeline = array();
$statq = mysqli_query($connect_db, "
select id, ymd, sum(n_clk) AS `n_clk`, sum(n_subs) AS `n_subs`, sum(o_shows) AS `o_shows`, sum(o_clk) AS `o_clk`, sum(o_unsubs) AS `o_unsubs` 
  from (
  SELECT id,
	ymd,
	sum(clk) AS `n_clk`,
	sum(subs) AS `n_subs`,
	0 o_shows,
	0 o_clk,
	0 o_unsubs
    from t_stat GROUP BY ymd ASC
       union all
    SELECT id,
	ymd,
	0 n_clk,
	0 n_subs,
	sum(shows) AS `o_shows`,
	sum(clk) AS `o_clk`,
	sum(unsubs) AS `o_unsubs`
    from t_stat_sended GROUP BY ymd ASC
	  ) x
GROUP BY ymd ORDER BY ymd ASC
");
while($rowi = mysqli_fetch_assoc($statq)) {
$sdt = date("d.m.Y", strtotime($rowi['ymd']));
$timeline[$sdt]['n_clk'] = $rowi['n_clk'];
$timeline[$sdt]['n_subs'] = $rowi['n_subs'];
$timeline[$sdt]['o_shows'] = $rowi['o_shows'];
$timeline[$sdt]['o_clk'] = $rowi['o_clk'];
$timeline[$sdt]['o_unsubs'] = $rowi['o_unsubs'];
$timeline[$sdt]['cr'] = number_format($rowi['n_subs']/$rowi['n_clk']*100,2,'.','');
$timeline[$sdt]['ctr'] = number_format($rowi['o_clk']/$rowi['o_shows']*100,2,'.','');
}

//Создание строк
$timeline_e = '';
$n_clk = '';
$n_subs = '';
$o_shows = '';
$o_clk = '';
$o_unsubs = '';
$cr = '';
$ctr = '';

foreach ($timeline as $key => $val)
{
$timeline_e .= "'".$key."', ";
$n_clk .= $timeline[$key]['n_clk'].", ";
$n_subs .= $timeline[$key]['n_subs'].", ";
$o_shows .= $timeline[$key]['o_shows'].", ";
$o_clk .= $timeline[$key]['o_clk'].", ";
$o_unsubs .= $timeline[$key]['o_unsubs'].", ";
$cr .= $timeline[$key]['cr'].", ";
$ctr .= $timeline[$key]['ctr'].", ";
}
$timeline_e = substr($timeline_e,0,-2);
$n_clk = substr($n_clk,0,-2);
$n_subs = substr($n_subs,0,-2);
$o_shows = substr($o_shows,0,-2);
$o_clk = substr($o_clk,0,-2);
$o_unsubs = substr($o_unsubs,0,-2);
$cr = substr($cr,0,-2);
$ctr = substr($ctr,0,-2);
?>
<!-- Контент -->

<h4><a href="javascript://" class="btn btn-success" data-toggle="modal" data-target="#modal_note">Блокнот</a></h4>

<div id="modal_note" class="modal fade">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title">Блокнот</h4>
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
</div>
<div class="modal-body">
<div class="form-group">
<textarea rows="5" cols="5" class="form-control" style="min-height: 300px;" placeholder="Введите текст" id="note"><?php echo $data['note']; ?></textarea>
</div>
</div>
<div class="modal-footer">
<button type="button" id="crabs_note_save" class="btn btn-primary">Сохранить</button>
<button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
</div>
</div>
</div>
</div>

<br />

<div class="row mb-4">
<div class="col-md-2">
<div class="card border-secondary">
<div class="card-header">База подписчиков</div>
<div class="card-body">
<h4 class="card-title text-center"><?php echo $data['p_allusers']; ?></h4>
</div>
</div>
</div>

<div class="col-md-2">
<div class="card border-secondary">
<div class="card-header">Всего отправлено</div>
<div class="card-body">
<h4 class="card-title text-center"><?php echo $data['p_all_showed']; ?></h4>
</div>
</div>
</div>

<div class="col-md-2">
<div class="card border-secondary">
<div class="card-header">Всего кликов</div>
<div class="card-body">
<h4 class="card-title text-center"><?php echo $data['p_all_clicks']; ?></h4>
</div>
</div>
</div>

<div class="col-md-2">
<div class="card border-secondary">
<div class="card-header">CTR</div>
<div class="card-body">
<h4 class="card-title text-center"><?php echo number_format($data['p_all_clicks']/$data['p_all_showed']*100,2,'.',','); ?></h4>
</div>
</div>
</div>

<div class="col-md-2">
<div class="card border-secondary">
<div class="card-header">Всего потоков</div>
<div class="card-body">
<h4 class="card-title text-center"><?php echo $all_streams['cnt']; ?></h4>
</div>
</div>
</div>

<div class="col-md-2">
<div class="card border-secondary">
<div class="card-header">Всего сообщений</div>
<div class="card-body">
<h4 class="card-title text-center"><?php echo $all_messages['cnt']; ?></h4>
</div>
</div>
</div>
</div>


<div id="crabs-stat"></div>

<script src="/<?php echo $adm['apath']; ?>/js/crabs_graph.js"></script>
<script src="/<?php echo $adm['apath']; ?>/js/crabs_graph_export_ru.js"></script>

<script type='text/javascript'>
//Сохранение блокнота
$('#crabs_note_save').click(function(){
var note = $('#note').val();
$.ajax({
type: 'POST',
url: '/actions/admin_note_save.php',
data: {'note': note, 'token': crabs_tkn},
cache: false,
success: function(result){
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

$('#crabs-stat').highcharts({
title: {
text: 'Общая статистика по дням',
x: -20,
"style": {"color": "#fff" }
},
xAxis: {
title: {
text: 'Дата',
"style": {"color": "#fff" }
},
categories: [<?php echo $timeline_e; ?>],
"labels": {
"style": {"color": "#fff" }
}
},
yAxis: {
title: {
text: 'Количество',
"style": {"color": "#fff" }
},
plotLines: [{
value: 0,
width: 1,
color: '#808080'
}],
"labels": {
"style": {"color": "#fff" }
}
},
tooltip: {
valueSuffix: '',
shared: true,
crosshairs: false
},
legend: {
borderWidth: 0,
"itemStyle": {"color": "#fff" },
"itemHoverStyle": {
"color": "#d7d7d7"
}
},
series: [
{
name: 'Переходов',
data: [<?php echo $n_clk; ?>]
},
{
name: 'Подписок',
data: [<?php echo $n_subs; ?>]
},
{
name: 'Отписок',
data: [<?php echo $o_unsubs; ?>]
},
{
name: 'Отправлено',
data: [<?php echo $o_shows; ?>]
},
{
name: 'Кликов',
data: [<?php echo $o_clk; ?>]
},
{
name: 'CR подписок',
data: [<?php echo $cr; ?>]
},
{
name: 'CTR кликов',
data: [<?php echo $ctr; ?>]
}
],
"chart": {
"backgroundColor": "#303030",
"zoomType": "x"
}
});

var chart = $('#crabs-stat').highcharts();
</script>

<!-- /Контент -->
<?php include('inc/bottom.php'); ?>