<?php
$pname = 'Рассылка';
include('inc/top.php');
if ($nowusr['ty'] != 0 && !in_array('2', $nowusr_ty)) { exit('error'); }
include('../inc/wu_pagination.php');
$dt_first = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT id,dt FROM `t_tokens` ORDER BY id ASC LIMIT 1"));
$dt_last = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT id,dt FROM `t_tokens` ORDER BY id DESC LIMIT 1"));

//Осталось времени
function wu_time_left($check_time){
if ($check_time > 0) {
$days = floor($check_time/86400);
$hours = floor(($check_time%86400)/3600);
$minutes = floor(($check_time%3600)/60);
$seconds = $check_time%60; 
$str = '';
if($days > 0) $str .= wu_time_left_2($days,array('день','дня','дней')).' ';
if($hours > 0) $str .= wu_time_left_2($hours,array('час','часа','часов')).' ';
if($minutes > 0) $str .= wu_time_left_2($minutes,array('минуту','минуты','минут')).' ';
if($seconds > 0) $str .= wu_time_left_2($seconds,array('секунду','секунды','секунд'));
return 'Через '.$str;
} else { return 'Сейчас'; }
}
function wu_time_left_2($digit,$expr,$onlyword=false){
if(!is_array($expr)) $expr = array_filter(explode(' ', $expr));
if(empty($expr[2])) $expr[2]=$expr[1];
$i=preg_replace('/[^0-9]+/s','',$digit)%100;
if($onlyword) $digit='';
if($i>=5 && $i<=20) $res=$digit.' '.$expr[2];
else
{
$i%=10;
if($i==1) $res=$digit.' '.$expr[0];
elseif($i>=2 && $i<=4) $res=$digit.' '.$expr[1];
else $res=$digit.' '.$expr[2];
}
return trim($res);
}
?>
<!-- Контент -->

<script type="text/javascript" src="js/plugins/crabs_datetime/moment.min.js"></script>
<script type="text/javascript" src="js/plugins/crabs_datetime/ru.js"></script>
<script type="text/javascript" src="js/plugins/crabs_datetime/tempusdominus-bootstrap-4.min.js"></script>
<link rel="stylesheet" href="js/plugins/crabs_datetime/tempusdominus-bootstrap-4.min.css" />

<h4>Рассылка <button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#modal_add">Новая рассылка</button> <button type="button" class="btn btn-success pull-right mr-2" data-toggle="modal" data-target="#modal_add_m">Новая массовая рассылка</button></h4>
<br />

<div id="modal_add_m" class="modal fade">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title">Новая массовая рассылка</h4>
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
</div>
<div class="modal-body">

<form>
<fieldset>

<div class="form-group">
<label for="sel_msg_m" class="form-control-label">Сообщение:</label>
<select id="sel_msg_m" name="sel_msg_m" class="form-control">
<option value="-1" hidden selected="selected">Выберите сообщение</option>
<?php
$crabs_n_q = mysqli_query($connect_db, "SELECT id,ti FROM t_messages ORDER BY id DESC");
while($row_n = mysqli_fetch_assoc($crabs_n_q)) {
?>
<option value="<?php echo $row_n['id']; ?>">#<?php echo $row_n['id']; ?> - <?php echo htmlspecialchars($row_n['ti']); ?></option>
<?php } ?>
</select>
</div>

<div class="form-group">
<label for="sel_stream_m" class="form-control-label">Поток:</label>
<select id="sel_stream_m" name="sel_stream_m" class="form-control">
<option value="-1" selected="selected">Все</option>
<?php
$crabs_n_q = mysqli_query($connect_db, "SELECT id,ti FROM t_streams ORDER BY id DESC");
while($row_n = mysqli_fetch_assoc($crabs_n_q)) {
?>
<option value="<?php echo $row_n['id']; ?>">#<?php echo $row_n['id']; ?> - <?php echo htmlspecialchars($row_n['ti']); ?></option>
<?php } ?>
</select>
</div>

<div class="form-group">
<label for="idevice_m">Устройство:</label>
<select id="idevice_m" name="idevice_m" class="form-control">
<option value="-1">Все устройства</option>
<option value="0">ПК</option>
<option value="1">Телефон</option>
<option value="2">Планшет</option>
</select>
</div>

<div class="form-group">
<label for="icou_m">Страны <span data-toggle="tooltip" data-placement="top" data-original-title="Страны, по которым будет идти рассылка. Через пробел. Оставьте поле пустым, чтобы рассылать по всем">(?)</span>:</label>
<input type="text" class="form-control" id="icou_m" name="icou_m" placeholder="Страна" />
<small class="text-muted">
<a href="javascript://" data-toggle="tooltip" data-placement="bottom" data-original-title="Россия" onclick="crabs_set_c('RU')">RU</a> 
<a href="javascript://" data-toggle="tooltip" data-placement="bottom" data-original-title="Украина" onclick="crabs_set_c('UA')">UA</a> 
<a href="javascript://" data-toggle="tooltip" data-placement="bottom" data-original-title="Казахстан" onclick="crabs_set_c('KZ')">KZ</a> 
<a href="javascript://" data-toggle="tooltip" data-placement="bottom" data-original-title="Беларусь" onclick="crabs_set_c('BY')">BY</a> 
<a href="javascript://" data-toggle="modal" data-target="#modal_list_m">Весь список</a></small>
</div>

<div class="input-daterange" id="crabsdatepicker_m" style="max-width:200px;display:inline-block;">
<span class="add-on">Подписка с:</span>
<input type="text" class="form-control" id="statgo_m" value="<?php echo date("d.m.Y", $dt_first['dt']); ?>" />
</div>
<div class="input-daterange" id="crabsdatepicker2_m" style="max-width:200px;display:inline-block;">
<span class="add-on">По:</span>
<input type="text" class="form-control" id="statend_m" value="<?php echo date("d.m.Y", $dt_last['dt']); ?>" />
</div>

<br /><br />

<div class="input-daterange" id="crabsdatepickerr_m" style="max-width:200px;display:inline-block;">
<span class="add-on">Рассылка с (дата/время):</span>
<input type="text" class="form-control" id="rstatgo_m" value="<?php echo date("d.m.Y", $dt); ?>" />
</div>
<input type="text" class="form-control" style="max-width: 189px;" id="rstatgotime" name="rstatgotime" placeholder="<?php echo date("H:i", $dt); ?>" value="<?php echo date("H:i", $dt); ?>" />
<br />
<div class="input-daterange" id="crabsdatepicker2r" style="max-width:200px;display:inline-block;">
<span class="add-on">По (дата/время):</span>
<input type="text" class="form-control" id="rstatend_m" value="<?php echo date("d.m.Y", $dt+86400); ?>" />
</div>
<input type="text" class="form-control" style="max-width: 189px;" id="rstatgotime2" name="rstatgotime2" placeholder="<?php echo date("H:i", $dt); ?>" value="<?php echo date("H:i", $dt); ?>" />
<br />
<div class="form-group">
<label for="icou">Интервал рассылки в часах:</label>
<input type="text" class="form-control" style="max-width: 189px;" id="iintrvl" name="iintrvl" placeholder="4" value="4" />
</div>


</fieldset>
</form>

<div class="modal-footer">
<button type="button" id="btn_crabs_start_m" class="btn btn-primary">Создать рассылку</button>
<button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
</div>
</div>
</div>
</div>
</div>



<div id="modal_list_m" class="modal fade">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title">Список кодов стран</h4>
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
</div>
<div class="modal-body">

<table border="0">
<tbody><tr><td>Австралия</td><td style="padding-left:10px">AU</td></tr>
<tr><td>Австрия</td><td style="padding-left:10px">AT</td></tr>
<tr><td>Азербайджан</td><td style="padding-left:10px">AZ</td></tr>
<tr><td>Аландские острова</td><td style="padding-left:10px">AX</td></tr>
<tr><td>Албания</td><td style="padding-left:10px">AL</td></tr>
<tr><td>Алжир</td><td style="padding-left:10px">DZ</td></tr>
<tr><td>Американское Самоа</td><td style="padding-left:10px">AS</td></tr>
<tr><td>Ангилья</td><td style="padding-left:10px">AI</td></tr>
<tr><td>Ангола</td><td style="padding-left:10px">AO</td></tr>
<tr><td>Андорра</td><td style="padding-left:10px">AD</td></tr>
<tr><td>Антарктида</td><td style="padding-left:10px">AQ</td></tr>
<tr><td>Антигуа и Барбуда</td><td style="padding-left:10px">AG</td></tr>
<tr><td>Аргентина</td><td style="padding-left:10px">AR</td></tr>
<tr><td>Армения</td><td style="padding-left:10px">AM</td></tr>
<tr><td>Аруба</td><td style="padding-left:10px">AW</td></tr>
<tr><td>Афганистан</td><td style="padding-left:10px">AF</td></tr>
<tr><td>Багамы</td><td style="padding-left:10px">BS</td></tr>
<tr><td>Бангладеш</td><td style="padding-left:10px">BD</td></tr>
<tr><td>Барбадос</td><td style="padding-left:10px">BB</td></tr>
<tr><td>Бахрейн</td><td style="padding-left:10px">BH</td></tr>
<tr><td>Белиз</td><td style="padding-left:10px">BZ</td></tr>
<tr><td>Белоруссия</td><td style="padding-left:10px">BY</td></tr>
<tr><td>Бельгия</td><td style="padding-left:10px">BE</td></tr>
<tr><td>Бенин</td><td style="padding-left:10px">BJ</td></tr>
<tr><td>Бермуды</td><td style="padding-left:10px">BM</td></tr>
<tr><td>Болгария</td><td style="padding-left:10px">BG</td></tr>
<tr><td>Боливия</td><td style="padding-left:10px">BO</td></tr>
<tr><td>Бонэйр, Синт-Эстатиус и Саба</td><td style="padding-left:10px">BQ</td></tr>
<tr><td>Босния и Герцеговина</td><td style="padding-left:10px">BA</td></tr>
<tr><td>Ботсвана</td><td style="padding-left:10px">BW</td></tr>
<tr><td>Бразилия</td><td style="padding-left:10px">BR</td></tr>
<tr><td>Британская территория в Индийском океане</td><td style="padding-left:10px">IO</td></tr>
<tr><td>Британские Виргинские острова</td><td style="padding-left:10px">VG</td></tr>
<tr><td>Бруней</td><td style="padding-left:10px">BN</td></tr>
<tr><td>Буркина-Фасо</td><td style="padding-left:10px">BF</td></tr>
<tr><td>Бурунди</td><td style="padding-left:10px">BI</td></tr>
<tr><td>Бутан</td><td style="padding-left:10px">BT</td></tr>
<tr><td>Вануату</td><td style="padding-left:10px">VU</td></tr>
<tr><td>Ватикан</td><td style="padding-left:10px">VA</td></tr>
<tr><td>Великобритания</td><td style="padding-left:10px">GB</td></tr>
<tr><td>Венгрия</td><td style="padding-left:10px">HU</td></tr>
<tr><td>Венесуэла</td><td style="padding-left:10px">VE</td></tr>
<tr><td>Виргинские Острова (США)</td><td style="padding-left:10px">VI</td></tr>
<tr><td>Внешние малые острова (США)</td><td style="padding-left:10px">UM</td></tr>
<tr><td>Восточный Тимор</td><td style="padding-left:10px">TL</td></tr>
<tr><td>Вьетнам</td><td style="padding-left:10px">VN</td></tr>
<tr><td>Габон</td><td style="padding-left:10px">GA</td></tr>
<tr><td>Гайана</td><td style="padding-left:10px">GY</td></tr>
<tr><td>Гаити</td><td style="padding-left:10px">HT</td></tr>
<tr><td>Гамбия</td><td style="padding-left:10px">GM</td></tr>
<tr><td>Гана</td><td style="padding-left:10px">GH</td></tr>
<tr><td>Гваделупа</td><td style="padding-left:10px">GP</td></tr>
<tr><td>Гватемала</td><td style="padding-left:10px">GT</td></tr>
<tr><td>Гвиана</td><td style="padding-left:10px">GF</td></tr>
<tr><td>Гвинея</td><td style="padding-left:10px">GN</td></tr>
<tr><td>Гвинея-Бисау</td><td style="padding-left:10px">GW</td></tr>
<tr><td>Германия</td><td style="padding-left:10px">DE</td></tr>
<tr><td>Гернси</td><td style="padding-left:10px">GG</td></tr>
<tr><td>Гибралтар</td><td style="padding-left:10px">GI</td></tr>
<tr><td>Гондурас</td><td style="padding-left:10px">HN</td></tr>
<tr><td>Гонконг</td><td style="padding-left:10px">HK</td></tr>
<tr><td>Государство Палестина</td><td style="padding-left:10px">PS</td></tr>
<tr><td>Гренада</td><td style="padding-left:10px">GD</td></tr>
<tr><td>Гренландия</td><td style="padding-left:10px">GL</td></tr>
<tr><td>Греция</td><td style="padding-left:10px">GR</td></tr>
<tr><td>Грузия</td><td style="padding-left:10px">GE</td></tr>
<tr><td>Гуам</td><td style="padding-left:10px">GU</td></tr>
<tr><td>Дания</td><td style="padding-left:10px">DK</td></tr>
<tr><td>Демократическая Республика Конго</td><td style="padding-left:10px">CD</td></tr>
<tr><td>Джерси</td><td style="padding-left:10px">JE</td></tr>
<tr><td>Джибути</td><td style="padding-left:10px">DJ</td></tr>
<tr><td>Доминика</td><td style="padding-left:10px">DM</td></tr>
<tr><td>Доминиканская Республика</td><td style="padding-left:10px">DO</td></tr>
<tr><td>Европейский союз</td><td style="padding-left:10px">EU</td></tr>
<tr><td>Египет</td><td style="padding-left:10px">EG</td></tr>
<tr><td>Замбия</td><td style="padding-left:10px">ZM</td></tr>
<tr><td>Зимбабве</td><td style="padding-left:10px">ZW</td></tr>
<tr><td>Йемен</td><td style="padding-left:10px">YE</td></tr>
<tr><td>Израиль</td><td style="padding-left:10px">IL</td></tr>
<tr><td>Индия</td><td style="padding-left:10px">IN</td></tr>
<tr><td>Индонезия</td><td style="padding-left:10px">ID</td></tr>
<tr><td>Иордания</td><td style="padding-left:10px">JO</td></tr>
<tr><td>Ирак</td><td style="padding-left:10px">IQ</td></tr>
<tr><td>Иран</td><td style="padding-left:10px">IR</td></tr>
<tr><td>Ирландия</td><td style="padding-left:10px">IE</td></tr>
<tr><td>Исландия</td><td style="padding-left:10px">IS</td></tr>
<tr><td>Испания</td><td style="padding-left:10px">ES</td></tr>
<tr><td>Италия</td><td style="padding-left:10px">IT</td></tr>
<tr><td>Кабо-Верде</td><td style="padding-left:10px">CV</td></tr>
<tr><td>Казахстан</td><td style="padding-left:10px">KZ</td></tr>
<tr><td>Камбоджа</td><td style="padding-left:10px">KH</td></tr>
<tr><td>Камерун</td><td style="padding-left:10px">CM</td></tr>
<tr><td>Канада</td><td style="padding-left:10px">CA</td></tr>
<tr><td>Катар</td><td style="padding-left:10px">QA</td></tr>
<tr><td>Кения</td><td style="padding-left:10px">KE</td></tr>
<tr><td>Кипр</td><td style="padding-left:10px">CY</td></tr>
<tr><td>Киргизия</td><td style="padding-left:10px">KG</td></tr>
<tr><td>Кирибати</td><td style="padding-left:10px">KI</td></tr>
<tr><td>Китайская Республика</td><td style="padding-left:10px">TW</td></tr>
<tr><td>КНДР</td><td style="padding-left:10px">KP</td></tr>
<tr><td>КНР</td><td style="padding-left:10px">CN</td></tr>
<tr><td>Кокосовые острова</td><td style="padding-left:10px">CC</td></tr>
<tr><td>Колумбия</td><td style="padding-left:10px">CO</td></tr>
<tr><td>Коморы</td><td style="padding-left:10px">KM</td></tr>
<tr><td>Коста-Рика</td><td style="padding-left:10px">CR</td></tr>
<tr><td>Кот-д’Ивуар</td><td style="padding-left:10px">CI</td></tr>
<tr><td>Куба</td><td style="padding-left:10px">CU</td></tr>
<tr><td>Кувейт</td><td style="padding-left:10px">KW</td></tr>
<tr><td>Кюрасао</td><td style="padding-left:10px">CW</td></tr>
<tr><td>Лаос</td><td style="padding-left:10px">LA</td></tr>
<tr><td>Латвия</td><td style="padding-left:10px">LV</td></tr>
<tr><td>Лесото</td><td style="padding-left:10px">LS</td></tr>
<tr><td>Либерия</td><td style="padding-left:10px">LR</td></tr>
<tr><td>Ливан</td><td style="padding-left:10px">LB</td></tr>
<tr><td>Ливия</td><td style="padding-left:10px">LY</td></tr>
<tr><td>Литва</td><td style="padding-left:10px">LT</td></tr>
<tr><td>Лихтенштейн</td><td style="padding-left:10px">LI</td></tr>
<tr><td>Люксембург</td><td style="padding-left:10px">LU</td></tr>
<tr><td>Маврикий</td><td style="padding-left:10px">MU</td></tr>
<tr><td>Мавритания</td><td style="padding-left:10px">MR</td></tr>
<tr><td>Мадагаскар</td><td style="padding-left:10px">MG</td></tr>
<tr><td>Майотта</td><td style="padding-left:10px">YT</td></tr>
<tr><td>Макао</td><td style="padding-left:10px">MO</td></tr>
<tr><td>Македония</td><td style="padding-left:10px">MK</td></tr>
<tr><td>Малави</td><td style="padding-left:10px">MW</td></tr>
<tr><td>Малайзия</td><td style="padding-left:10px">MY</td></tr>
<tr><td>Мали</td><td style="padding-left:10px">ML</td></tr>
<tr><td>Мальдивы</td><td style="padding-left:10px">MV</td></tr>
<tr><td>Мальта</td><td style="padding-left:10px">MT</td></tr>
<tr><td>Марокко</td><td style="padding-left:10px">MA</td></tr>
<tr><td>Мартиника</td><td style="padding-left:10px">MQ</td></tr>
<tr><td>Маршалловы Острова</td><td style="padding-left:10px">MH</td></tr>
<tr><td>Мексика</td><td style="padding-left:10px">MX</td></tr>
<tr><td>Микронезия</td><td style="padding-left:10px">FM</td></tr>
<tr><td>Мозамбик</td><td style="padding-left:10px">MZ</td></tr>
<tr><td>Молдавия</td><td style="padding-left:10px">MD</td></tr>
<tr><td>Монако</td><td style="padding-left:10px">MC</td></tr>
<tr><td>Монголия</td><td style="padding-left:10px">MN</td></tr>
<tr><td>Монтсеррат</td><td style="padding-left:10px">MS</td></tr>
<tr><td>Мьянма</td><td style="padding-left:10px">MM</td></tr>
<tr><td>Намибия</td><td style="padding-left:10px">NA</td></tr>
<tr><td>Науру</td><td style="padding-left:10px">NR</td></tr>
<tr><td>Непал</td><td style="padding-left:10px">NP</td></tr>
<tr><td>Нигер</td><td style="padding-left:10px">NE</td></tr>
<tr><td>Нигерия</td><td style="padding-left:10px">NG</td></tr>
<tr><td>Нидерланды</td><td style="padding-left:10px">NL</td></tr>
<tr><td>Никарагуа</td><td style="padding-left:10px">NI</td></tr>
<tr><td>Ниуэ</td><td style="padding-left:10px">NU</td></tr>
<tr><td>Новая Зеландия</td><td style="padding-left:10px">NZ</td></tr>
<tr><td>Новая Каледония</td><td style="padding-left:10px">NC</td></tr>
<tr><td>Норвегия</td><td style="padding-left:10px">NO</td></tr>
<tr><td>ОАЭ</td><td style="padding-left:10px">AE</td></tr>
<tr><td>Оман</td><td style="padding-left:10px">OM</td></tr>
<tr><td>Остров Буве</td><td style="padding-left:10px">BV</td></tr>
<tr><td>Остров Мэн</td><td style="padding-left:10px">IM</td></tr>
<tr><td>Остров Норфолк</td><td style="padding-left:10px">NF</td></tr>
<tr><td>Остров Рождества</td><td style="padding-left:10px">CX</td></tr>
<tr><td>Острова Кайман</td><td style="padding-left:10px">KY</td></tr>
<tr><td>Острова Кука</td><td style="padding-left:10px">CK</td></tr>
<tr><td>Острова Питкэрн</td><td style="padding-left:10px">PN</td></tr>
<tr><td>Острова Святой Елены, Вознесения и Тристан-да-Кунья</td><td style="padding-left:10px">SH</td></tr>
<tr><td>Пакистан</td><td style="padding-left:10px">PK</td></tr>
<tr><td>Палау</td><td style="padding-left:10px">PW</td></tr>
<tr><td>Панама</td><td style="padding-left:10px">PA</td></tr>
<tr><td>Папуа — Новая Гвинея</td><td style="padding-left:10px">PG</td></tr>
<tr><td>Парагвай</td><td style="padding-left:10px">PY</td></tr>
<tr><td>Перу</td><td style="padding-left:10px">PE</td></tr>
<tr><td>Польша</td><td style="padding-left:10px">PL</td></tr>
<tr><td>Португалия</td><td style="padding-left:10px">PT</td></tr>
<tr><td>Пуэрто-Рико</td><td style="padding-left:10px">PR</td></tr>
<tr><td>Республика Конго</td><td style="padding-left:10px">CG</td></tr>
<tr><td>Республика Корея</td><td style="padding-left:10px">KR</td></tr>
<tr><td>Реюньон</td><td style="padding-left:10px">RE</td></tr>
<tr><td>Россия</td><td style="padding-left:10px">RU</td></tr>
<tr><td>Руанда</td><td style="padding-left:10px">RW</td></tr>
<tr><td>Румыния</td><td style="padding-left:10px">RO</td></tr>
<tr><td>САДР</td><td style="padding-left:10px">EH</td></tr>
<tr><td>Сальвадор</td><td style="padding-left:10px">SV</td></tr>
<tr><td>Самоа</td><td style="padding-left:10px">WS</td></tr>
<tr><td>Сан-Марино</td><td style="padding-left:10px">SM</td></tr>
<tr><td>Сан-Томе и Принсипи</td><td style="padding-left:10px">ST</td></tr>
<tr><td>Саудовская Аравия</td><td style="padding-left:10px">SA</td></tr>
<tr><td>Свазиленд</td><td style="padding-left:10px">SZ</td></tr>
<tr><td>Северные Марианские острова</td><td style="padding-left:10px">MP</td></tr>
<tr><td>Сейшельские Острова</td><td style="padding-left:10px">SC</td></tr>
<tr><td>Сен-Бартелеми</td><td style="padding-left:10px">BL</td></tr>
<tr><td>Сенегал</td><td style="padding-left:10px">SN</td></tr>
<tr><td>Сен-Мартен</td><td style="padding-left:10px">MF</td></tr>
<tr><td>Сен-Пьер и Микелон</td><td style="padding-left:10px">PM</td></tr>
<tr><td>Сент-Винсент и Гренадины</td><td style="padding-left:10px">VC</td></tr>
<tr><td>Сент-Китс и Невис</td><td style="padding-left:10px">KN</td></tr>
<tr><td>Сент-Люсия</td><td style="padding-left:10px">LC</td></tr>
<tr><td>Сербия</td><td style="padding-left:10px">RS</td></tr>
<tr><td>Сингапур</td><td style="padding-left:10px">SG</td></tr>
<tr><td>Синт-Мартен</td><td style="padding-left:10px">SX</td></tr>
<tr><td>Сирия</td><td style="padding-left:10px">SY</td></tr>
<tr><td>Словакия</td><td style="padding-left:10px">SK</td></tr>
<tr><td>Словения</td><td style="padding-left:10px">SI</td></tr>
<tr><td>Соломоновы Острова</td><td style="padding-left:10px">SB</td></tr>
<tr><td>Сомали</td><td style="padding-left:10px">SO</td></tr>
<tr><td>Судан</td><td style="padding-left:10px">SD</td></tr>
<tr><td>Суринам</td><td style="padding-left:10px">SR</td></tr>
<tr><td>США</td><td style="padding-left:10px">US</td></tr>
<tr><td>Сьерра-Леоне</td><td style="padding-left:10px">SL</td></tr>
<tr><td>Таджикистан</td><td style="padding-left:10px">TJ</td></tr>
<tr><td>Таиланд</td><td style="padding-left:10px">TH</td></tr>
<tr><td>Танзания</td><td style="padding-left:10px">TZ</td></tr>
<tr><td>Тёркс и Кайкос</td><td style="padding-left:10px">TC</td></tr>
<tr><td>Того</td><td style="padding-left:10px">TG</td></tr>
<tr><td>Токелау</td><td style="padding-left:10px">TK</td></tr>
<tr><td>Тонга</td><td style="padding-left:10px">TO</td></tr>
<tr><td>Тринидад и Тобаго</td><td style="padding-left:10px">TT</td></tr>
<tr><td>Тувалу</td><td style="padding-left:10px">TV</td></tr>
<tr><td>Тунис</td><td style="padding-left:10px">TN</td></tr>
<tr><td>Туркмения</td><td style="padding-left:10px">TM</td></tr>
<tr><td>Турция</td><td style="padding-left:10px">TR</td></tr>
<tr><td>Уганда</td><td style="padding-left:10px">UG</td></tr>
<tr><td>Узбекистан</td><td style="padding-left:10px">UZ</td></tr>
<tr><td>Украина</td><td style="padding-left:10px">UA</td></tr>
<tr><td>Уоллис и Футуна</td><td style="padding-left:10px">WF</td></tr>
<tr><td>Уругвай</td><td style="padding-left:10px">UY</td></tr>
<tr><td>Фареры</td><td style="padding-left:10px">FO</td></tr>
<tr><td>Фиджи</td><td style="padding-left:10px">FJ</td></tr>
<tr><td>Филиппины</td><td style="padding-left:10px">PH</td></tr>
<tr><td>Финляндия</td><td style="padding-left:10px">FI</td></tr>
<tr><td>Фолклендские острова</td><td style="padding-left:10px">FK</td></tr>
<tr><td>Франция</td><td style="padding-left:10px">FR</td></tr>
<tr><td>Французская Полинезия</td><td style="padding-left:10px">PF</td></tr>
<tr><td>Французские Южные и Антарктические Территории</td><td style="padding-left:10px">TF</td></tr>
<tr><td>Херд и Макдональд</td><td style="padding-left:10px">HM</td></tr>
<tr><td>Хорватия</td><td style="padding-left:10px">HR</td></tr>
<tr><td>ЦАР</td><td style="padding-left:10px">CF</td></tr>
<tr><td>Чад</td><td style="padding-left:10px">TD</td></tr>
<tr><td>Черногория</td><td style="padding-left:10px">ME</td></tr>
<tr><td>Чехия</td><td style="padding-left:10px">CZ</td></tr>
<tr><td>Чили</td><td style="padding-left:10px">CL</td></tr>
<tr><td>Швейцария</td><td style="padding-left:10px">CH</td></tr>
<tr><td>Швеция</td><td style="padding-left:10px">SE</td></tr>
<tr><td>Шпицберген и Ян-Майен</td><td style="padding-left:10px">SJ</td></tr>
<tr><td>Шри-Ланка</td><td style="padding-left:10px">LK</td></tr>
<tr><td>Эквадор</td><td style="padding-left:10px">EC</td></tr>
<tr><td>Экваториальная Гвинея</td><td style="padding-left:10px">GQ</td></tr>
<tr><td>Эритрея</td><td style="padding-left:10px">ER</td></tr>
<tr><td>Эстония</td><td style="padding-left:10px">EE</td></tr>
<tr><td>Эфиопия</td><td style="padding-left:10px">ET</td></tr>
<tr><td>ЮАР</td><td style="padding-left:10px">ZA</td></tr>
<tr><td>Южная Георгия и Южные Сандвичевы острова</td><td style="padding-left:10px">GS</td></tr>
<tr><td>Южный Судан</td><td style="padding-left:10px">SS</td></tr>
<tr><td>Ямайка</td><td style="padding-left:10px">JM</td></tr>
<tr><td>Япония</td><td style="padding-left:10px">JP</td></tr>
</tbody></table>

</div>
<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
</div>
</div>
</div>
</div>
</div>

























<div id="modal_add" class="modal fade">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title">Новая рассылка</h4>
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
</div>
<div class="modal-body">

<form>
<fieldset>

<div class="form-group">
<label for="sel_msg" class="form-control-label">Сообщение:</label>
<select id="sel_msg" name="sel_msg" class="form-control">
<option value="-1" hidden selected="selected">Выберите сообщение</option>
<?php
$crabs_n_q = mysqli_query($connect_db, "SELECT id,ti FROM t_messages ORDER BY id DESC");
while($row_n = mysqli_fetch_assoc($crabs_n_q)) {
?>
<option value="<?php echo $row_n['id']; ?>">#<?php echo $row_n['id']; ?> - <?php echo htmlspecialchars($row_n['ti']); ?></option>
<?php } ?>
</select>
</div>

<div class="form-group">
<label for="sel_stream" class="form-control-label">Поток:</label>
<select id="sel_stream" name="sel_stream" class="form-control">
<option value="-1" selected="selected">Все</option>
<?php
$crabs_n_q = mysqli_query($connect_db, "SELECT id,ti FROM t_streams ORDER BY id DESC");
while($row_n = mysqli_fetch_assoc($crabs_n_q)) {
?>
<option value="<?php echo $row_n['id']; ?>">#<?php echo $row_n['id']; ?> - <?php echo htmlspecialchars($row_n['ti']); ?></option>
<?php } ?>
</select>
</div>

<div class="form-group">
<label for="idevice">Устройство:</label>
<select id="idevice" name="idevice" class="form-control">
<option value="-1">Все устройства</option>
<option value="0">ПК</option>
<option value="1">Телефон</option>
<option value="2">Планшет</option>
</select>
</div>

<div class="form-group">
<label for="icou">Страны <span data-toggle="tooltip" data-placement="top" data-original-title="Страны, по которым будет идти рассылка. Через пробел. Оставьте поле пустым, чтобы рассылать по всем">(?)</span>:</label>
<input type="text" class="form-control" id="icou" name="icou" placeholder="Страна" />
<small class="text-muted">
<a href="javascript://" data-toggle="tooltip" data-placement="bottom" data-original-title="Россия" onclick="crabs_set_c('RU')">RU</a> 
<a href="javascript://" data-toggle="tooltip" data-placement="bottom" data-original-title="Украина" onclick="crabs_set_c('UA')">UA</a> 
<a href="javascript://" data-toggle="tooltip" data-placement="bottom" data-original-title="Казахстан" onclick="crabs_set_c('KZ')">KZ</a> 
<a href="javascript://" data-toggle="tooltip" data-placement="bottom" data-original-title="Беларусь" onclick="crabs_set_c('BY')">BY</a> 
<a href="javascript://" data-toggle="modal" data-target="#modal_list">Весь список</a></small>
</div>

<div class="input-daterange" id="crabsdatepicker" style="max-width:200px;display:inline-block;">
<span class="add-on">Подписка с:</span>
<input type="text" class="form-control" id="statgo" value="<?php echo date("d.m.Y", $dt_first['dt']); ?>" />
</div>
<div class="input-daterange" id="crabsdatepicker2" style="max-width:200px;display:inline-block;">
<span class="add-on">По:</span>
<input type="text" class="form-control" id="statend" value="<?php echo date("d.m.Y", $dt_last['dt']); ?>" />
</div>

<br /><br />
<div class="form-group">
<label>Отложенная отправка:</label>
<div class="custom-control custom-checkbox">
<input type="checkbox" class="custom-control-input" name="sendafter" id="sendafter">
<label class="custom-control-label" for="sendafter">Использовать отложенную отправку</label>
</div>
</div>

<div id="isaftr" style="display:none;">
<div class="form-group" style="max-width:250px;">
<div class="input-group date" id="datetimepicker1" data-target-input="nearest">
<input type="text" class="form-control datetimepicker-input" data-target="#datetimepicker1" id="dtstart" name="dtstart" value="<?php echo date('d.m.Y H:i',$dt); ?>" />
<div class="input-group-append" data-target="#datetimepicker1" data-toggle="datetimepicker">
<div class="input-group-text"><i class="fa fa-calendar"></i></div>
</div>
</div>
</div>
</div>

</fieldset>
</form>

<div class="modal-footer">
<button type="button" id="btn_crabs_start" class="btn btn-primary">Создать рассылку</button>
<button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
</div>
</div>
</div>
</div>
</div>



<div id="modal_list" class="modal fade">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title">Список кодов стран</h4>
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
</div>
<div class="modal-body">

<table border="0">
<tbody><tr><td>Австралия</td><td style="padding-left:10px">AU</td></tr>
<tr><td>Австрия</td><td style="padding-left:10px">AT</td></tr>
<tr><td>Азербайджан</td><td style="padding-left:10px">AZ</td></tr>
<tr><td>Аландские острова</td><td style="padding-left:10px">AX</td></tr>
<tr><td>Албания</td><td style="padding-left:10px">AL</td></tr>
<tr><td>Алжир</td><td style="padding-left:10px">DZ</td></tr>
<tr><td>Американское Самоа</td><td style="padding-left:10px">AS</td></tr>
<tr><td>Ангилья</td><td style="padding-left:10px">AI</td></tr>
<tr><td>Ангола</td><td style="padding-left:10px">AO</td></tr>
<tr><td>Андорра</td><td style="padding-left:10px">AD</td></tr>
<tr><td>Антарктида</td><td style="padding-left:10px">AQ</td></tr>
<tr><td>Антигуа и Барбуда</td><td style="padding-left:10px">AG</td></tr>
<tr><td>Аргентина</td><td style="padding-left:10px">AR</td></tr>
<tr><td>Армения</td><td style="padding-left:10px">AM</td></tr>
<tr><td>Аруба</td><td style="padding-left:10px">AW</td></tr>
<tr><td>Афганистан</td><td style="padding-left:10px">AF</td></tr>
<tr><td>Багамы</td><td style="padding-left:10px">BS</td></tr>
<tr><td>Бангладеш</td><td style="padding-left:10px">BD</td></tr>
<tr><td>Барбадос</td><td style="padding-left:10px">BB</td></tr>
<tr><td>Бахрейн</td><td style="padding-left:10px">BH</td></tr>
<tr><td>Белиз</td><td style="padding-left:10px">BZ</td></tr>
<tr><td>Белоруссия</td><td style="padding-left:10px">BY</td></tr>
<tr><td>Бельгия</td><td style="padding-left:10px">BE</td></tr>
<tr><td>Бенин</td><td style="padding-left:10px">BJ</td></tr>
<tr><td>Бермуды</td><td style="padding-left:10px">BM</td></tr>
<tr><td>Болгария</td><td style="padding-left:10px">BG</td></tr>
<tr><td>Боливия</td><td style="padding-left:10px">BO</td></tr>
<tr><td>Бонэйр, Синт-Эстатиус и Саба</td><td style="padding-left:10px">BQ</td></tr>
<tr><td>Босния и Герцеговина</td><td style="padding-left:10px">BA</td></tr>
<tr><td>Ботсвана</td><td style="padding-left:10px">BW</td></tr>
<tr><td>Бразилия</td><td style="padding-left:10px">BR</td></tr>
<tr><td>Британская территория в Индийском океане</td><td style="padding-left:10px">IO</td></tr>
<tr><td>Британские Виргинские острова</td><td style="padding-left:10px">VG</td></tr>
<tr><td>Бруней</td><td style="padding-left:10px">BN</td></tr>
<tr><td>Буркина-Фасо</td><td style="padding-left:10px">BF</td></tr>
<tr><td>Бурунди</td><td style="padding-left:10px">BI</td></tr>
<tr><td>Бутан</td><td style="padding-left:10px">BT</td></tr>
<tr><td>Вануату</td><td style="padding-left:10px">VU</td></tr>
<tr><td>Ватикан</td><td style="padding-left:10px">VA</td></tr>
<tr><td>Великобритания</td><td style="padding-left:10px">GB</td></tr>
<tr><td>Венгрия</td><td style="padding-left:10px">HU</td></tr>
<tr><td>Венесуэла</td><td style="padding-left:10px">VE</td></tr>
<tr><td>Виргинские Острова (США)</td><td style="padding-left:10px">VI</td></tr>
<tr><td>Внешние малые острова (США)</td><td style="padding-left:10px">UM</td></tr>
<tr><td>Восточный Тимор</td><td style="padding-left:10px">TL</td></tr>
<tr><td>Вьетнам</td><td style="padding-left:10px">VN</td></tr>
<tr><td>Габон</td><td style="padding-left:10px">GA</td></tr>
<tr><td>Гайана</td><td style="padding-left:10px">GY</td></tr>
<tr><td>Гаити</td><td style="padding-left:10px">HT</td></tr>
<tr><td>Гамбия</td><td style="padding-left:10px">GM</td></tr>
<tr><td>Гана</td><td style="padding-left:10px">GH</td></tr>
<tr><td>Гваделупа</td><td style="padding-left:10px">GP</td></tr>
<tr><td>Гватемала</td><td style="padding-left:10px">GT</td></tr>
<tr><td>Гвиана</td><td style="padding-left:10px">GF</td></tr>
<tr><td>Гвинея</td><td style="padding-left:10px">GN</td></tr>
<tr><td>Гвинея-Бисау</td><td style="padding-left:10px">GW</td></tr>
<tr><td>Германия</td><td style="padding-left:10px">DE</td></tr>
<tr><td>Гернси</td><td style="padding-left:10px">GG</td></tr>
<tr><td>Гибралтар</td><td style="padding-left:10px">GI</td></tr>
<tr><td>Гондурас</td><td style="padding-left:10px">HN</td></tr>
<tr><td>Гонконг</td><td style="padding-left:10px">HK</td></tr>
<tr><td>Государство Палестина</td><td style="padding-left:10px">PS</td></tr>
<tr><td>Гренада</td><td style="padding-left:10px">GD</td></tr>
<tr><td>Гренландия</td><td style="padding-left:10px">GL</td></tr>
<tr><td>Греция</td><td style="padding-left:10px">GR</td></tr>
<tr><td>Грузия</td><td style="padding-left:10px">GE</td></tr>
<tr><td>Гуам</td><td style="padding-left:10px">GU</td></tr>
<tr><td>Дания</td><td style="padding-left:10px">DK</td></tr>
<tr><td>Демократическая Республика Конго</td><td style="padding-left:10px">CD</td></tr>
<tr><td>Джерси</td><td style="padding-left:10px">JE</td></tr>
<tr><td>Джибути</td><td style="padding-left:10px">DJ</td></tr>
<tr><td>Доминика</td><td style="padding-left:10px">DM</td></tr>
<tr><td>Доминиканская Республика</td><td style="padding-left:10px">DO</td></tr>
<tr><td>Европейский союз</td><td style="padding-left:10px">EU</td></tr>
<tr><td>Египет</td><td style="padding-left:10px">EG</td></tr>
<tr><td>Замбия</td><td style="padding-left:10px">ZM</td></tr>
<tr><td>Зимбабве</td><td style="padding-left:10px">ZW</td></tr>
<tr><td>Йемен</td><td style="padding-left:10px">YE</td></tr>
<tr><td>Израиль</td><td style="padding-left:10px">IL</td></tr>
<tr><td>Индия</td><td style="padding-left:10px">IN</td></tr>
<tr><td>Индонезия</td><td style="padding-left:10px">ID</td></tr>
<tr><td>Иордания</td><td style="padding-left:10px">JO</td></tr>
<tr><td>Ирак</td><td style="padding-left:10px">IQ</td></tr>
<tr><td>Иран</td><td style="padding-left:10px">IR</td></tr>
<tr><td>Ирландия</td><td style="padding-left:10px">IE</td></tr>
<tr><td>Исландия</td><td style="padding-left:10px">IS</td></tr>
<tr><td>Испания</td><td style="padding-left:10px">ES</td></tr>
<tr><td>Италия</td><td style="padding-left:10px">IT</td></tr>
<tr><td>Кабо-Верде</td><td style="padding-left:10px">CV</td></tr>
<tr><td>Казахстан</td><td style="padding-left:10px">KZ</td></tr>
<tr><td>Камбоджа</td><td style="padding-left:10px">KH</td></tr>
<tr><td>Камерун</td><td style="padding-left:10px">CM</td></tr>
<tr><td>Канада</td><td style="padding-left:10px">CA</td></tr>
<tr><td>Катар</td><td style="padding-left:10px">QA</td></tr>
<tr><td>Кения</td><td style="padding-left:10px">KE</td></tr>
<tr><td>Кипр</td><td style="padding-left:10px">CY</td></tr>
<tr><td>Киргизия</td><td style="padding-left:10px">KG</td></tr>
<tr><td>Кирибати</td><td style="padding-left:10px">KI</td></tr>
<tr><td>Китайская Республика</td><td style="padding-left:10px">TW</td></tr>
<tr><td>КНДР</td><td style="padding-left:10px">KP</td></tr>
<tr><td>КНР</td><td style="padding-left:10px">CN</td></tr>
<tr><td>Кокосовые острова</td><td style="padding-left:10px">CC</td></tr>
<tr><td>Колумбия</td><td style="padding-left:10px">CO</td></tr>
<tr><td>Коморы</td><td style="padding-left:10px">KM</td></tr>
<tr><td>Коста-Рика</td><td style="padding-left:10px">CR</td></tr>
<tr><td>Кот-д’Ивуар</td><td style="padding-left:10px">CI</td></tr>
<tr><td>Куба</td><td style="padding-left:10px">CU</td></tr>
<tr><td>Кувейт</td><td style="padding-left:10px">KW</td></tr>
<tr><td>Кюрасао</td><td style="padding-left:10px">CW</td></tr>
<tr><td>Лаос</td><td style="padding-left:10px">LA</td></tr>
<tr><td>Латвия</td><td style="padding-left:10px">LV</td></tr>
<tr><td>Лесото</td><td style="padding-left:10px">LS</td></tr>
<tr><td>Либерия</td><td style="padding-left:10px">LR</td></tr>
<tr><td>Ливан</td><td style="padding-left:10px">LB</td></tr>
<tr><td>Ливия</td><td style="padding-left:10px">LY</td></tr>
<tr><td>Литва</td><td style="padding-left:10px">LT</td></tr>
<tr><td>Лихтенштейн</td><td style="padding-left:10px">LI</td></tr>
<tr><td>Люксембург</td><td style="padding-left:10px">LU</td></tr>
<tr><td>Маврикий</td><td style="padding-left:10px">MU</td></tr>
<tr><td>Мавритания</td><td style="padding-left:10px">MR</td></tr>
<tr><td>Мадагаскар</td><td style="padding-left:10px">MG</td></tr>
<tr><td>Майотта</td><td style="padding-left:10px">YT</td></tr>
<tr><td>Макао</td><td style="padding-left:10px">MO</td></tr>
<tr><td>Македония</td><td style="padding-left:10px">MK</td></tr>
<tr><td>Малави</td><td style="padding-left:10px">MW</td></tr>
<tr><td>Малайзия</td><td style="padding-left:10px">MY</td></tr>
<tr><td>Мали</td><td style="padding-left:10px">ML</td></tr>
<tr><td>Мальдивы</td><td style="padding-left:10px">MV</td></tr>
<tr><td>Мальта</td><td style="padding-left:10px">MT</td></tr>
<tr><td>Марокко</td><td style="padding-left:10px">MA</td></tr>
<tr><td>Мартиника</td><td style="padding-left:10px">MQ</td></tr>
<tr><td>Маршалловы Острова</td><td style="padding-left:10px">MH</td></tr>
<tr><td>Мексика</td><td style="padding-left:10px">MX</td></tr>
<tr><td>Микронезия</td><td style="padding-left:10px">FM</td></tr>
<tr><td>Мозамбик</td><td style="padding-left:10px">MZ</td></tr>
<tr><td>Молдавия</td><td style="padding-left:10px">MD</td></tr>
<tr><td>Монако</td><td style="padding-left:10px">MC</td></tr>
<tr><td>Монголия</td><td style="padding-left:10px">MN</td></tr>
<tr><td>Монтсеррат</td><td style="padding-left:10px">MS</td></tr>
<tr><td>Мьянма</td><td style="padding-left:10px">MM</td></tr>
<tr><td>Намибия</td><td style="padding-left:10px">NA</td></tr>
<tr><td>Науру</td><td style="padding-left:10px">NR</td></tr>
<tr><td>Непал</td><td style="padding-left:10px">NP</td></tr>
<tr><td>Нигер</td><td style="padding-left:10px">NE</td></tr>
<tr><td>Нигерия</td><td style="padding-left:10px">NG</td></tr>
<tr><td>Нидерланды</td><td style="padding-left:10px">NL</td></tr>
<tr><td>Никарагуа</td><td style="padding-left:10px">NI</td></tr>
<tr><td>Ниуэ</td><td style="padding-left:10px">NU</td></tr>
<tr><td>Новая Зеландия</td><td style="padding-left:10px">NZ</td></tr>
<tr><td>Новая Каледония</td><td style="padding-left:10px">NC</td></tr>
<tr><td>Норвегия</td><td style="padding-left:10px">NO</td></tr>
<tr><td>ОАЭ</td><td style="padding-left:10px">AE</td></tr>
<tr><td>Оман</td><td style="padding-left:10px">OM</td></tr>
<tr><td>Остров Буве</td><td style="padding-left:10px">BV</td></tr>
<tr><td>Остров Мэн</td><td style="padding-left:10px">IM</td></tr>
<tr><td>Остров Норфолк</td><td style="padding-left:10px">NF</td></tr>
<tr><td>Остров Рождества</td><td style="padding-left:10px">CX</td></tr>
<tr><td>Острова Кайман</td><td style="padding-left:10px">KY</td></tr>
<tr><td>Острова Кука</td><td style="padding-left:10px">CK</td></tr>
<tr><td>Острова Питкэрн</td><td style="padding-left:10px">PN</td></tr>
<tr><td>Острова Святой Елены, Вознесения и Тристан-да-Кунья</td><td style="padding-left:10px">SH</td></tr>
<tr><td>Пакистан</td><td style="padding-left:10px">PK</td></tr>
<tr><td>Палау</td><td style="padding-left:10px">PW</td></tr>
<tr><td>Панама</td><td style="padding-left:10px">PA</td></tr>
<tr><td>Папуа — Новая Гвинея</td><td style="padding-left:10px">PG</td></tr>
<tr><td>Парагвай</td><td style="padding-left:10px">PY</td></tr>
<tr><td>Перу</td><td style="padding-left:10px">PE</td></tr>
<tr><td>Польша</td><td style="padding-left:10px">PL</td></tr>
<tr><td>Португалия</td><td style="padding-left:10px">PT</td></tr>
<tr><td>Пуэрто-Рико</td><td style="padding-left:10px">PR</td></tr>
<tr><td>Республика Конго</td><td style="padding-left:10px">CG</td></tr>
<tr><td>Республика Корея</td><td style="padding-left:10px">KR</td></tr>
<tr><td>Реюньон</td><td style="padding-left:10px">RE</td></tr>
<tr><td>Россия</td><td style="padding-left:10px">RU</td></tr>
<tr><td>Руанда</td><td style="padding-left:10px">RW</td></tr>
<tr><td>Румыния</td><td style="padding-left:10px">RO</td></tr>
<tr><td>САДР</td><td style="padding-left:10px">EH</td></tr>
<tr><td>Сальвадор</td><td style="padding-left:10px">SV</td></tr>
<tr><td>Самоа</td><td style="padding-left:10px">WS</td></tr>
<tr><td>Сан-Марино</td><td style="padding-left:10px">SM</td></tr>
<tr><td>Сан-Томе и Принсипи</td><td style="padding-left:10px">ST</td></tr>
<tr><td>Саудовская Аравия</td><td style="padding-left:10px">SA</td></tr>
<tr><td>Свазиленд</td><td style="padding-left:10px">SZ</td></tr>
<tr><td>Северные Марианские острова</td><td style="padding-left:10px">MP</td></tr>
<tr><td>Сейшельские Острова</td><td style="padding-left:10px">SC</td></tr>
<tr><td>Сен-Бартелеми</td><td style="padding-left:10px">BL</td></tr>
<tr><td>Сенегал</td><td style="padding-left:10px">SN</td></tr>
<tr><td>Сен-Мартен</td><td style="padding-left:10px">MF</td></tr>
<tr><td>Сен-Пьер и Микелон</td><td style="padding-left:10px">PM</td></tr>
<tr><td>Сент-Винсент и Гренадины</td><td style="padding-left:10px">VC</td></tr>
<tr><td>Сент-Китс и Невис</td><td style="padding-left:10px">KN</td></tr>
<tr><td>Сент-Люсия</td><td style="padding-left:10px">LC</td></tr>
<tr><td>Сербия</td><td style="padding-left:10px">RS</td></tr>
<tr><td>Сингапур</td><td style="padding-left:10px">SG</td></tr>
<tr><td>Синт-Мартен</td><td style="padding-left:10px">SX</td></tr>
<tr><td>Сирия</td><td style="padding-left:10px">SY</td></tr>
<tr><td>Словакия</td><td style="padding-left:10px">SK</td></tr>
<tr><td>Словения</td><td style="padding-left:10px">SI</td></tr>
<tr><td>Соломоновы Острова</td><td style="padding-left:10px">SB</td></tr>
<tr><td>Сомали</td><td style="padding-left:10px">SO</td></tr>
<tr><td>Судан</td><td style="padding-left:10px">SD</td></tr>
<tr><td>Суринам</td><td style="padding-left:10px">SR</td></tr>
<tr><td>США</td><td style="padding-left:10px">US</td></tr>
<tr><td>Сьерра-Леоне</td><td style="padding-left:10px">SL</td></tr>
<tr><td>Таджикистан</td><td style="padding-left:10px">TJ</td></tr>
<tr><td>Таиланд</td><td style="padding-left:10px">TH</td></tr>
<tr><td>Танзания</td><td style="padding-left:10px">TZ</td></tr>
<tr><td>Тёркс и Кайкос</td><td style="padding-left:10px">TC</td></tr>
<tr><td>Того</td><td style="padding-left:10px">TG</td></tr>
<tr><td>Токелау</td><td style="padding-left:10px">TK</td></tr>
<tr><td>Тонга</td><td style="padding-left:10px">TO</td></tr>
<tr><td>Тринидад и Тобаго</td><td style="padding-left:10px">TT</td></tr>
<tr><td>Тувалу</td><td style="padding-left:10px">TV</td></tr>
<tr><td>Тунис</td><td style="padding-left:10px">TN</td></tr>
<tr><td>Туркмения</td><td style="padding-left:10px">TM</td></tr>
<tr><td>Турция</td><td style="padding-left:10px">TR</td></tr>
<tr><td>Уганда</td><td style="padding-left:10px">UG</td></tr>
<tr><td>Узбекистан</td><td style="padding-left:10px">UZ</td></tr>
<tr><td>Украина</td><td style="padding-left:10px">UA</td></tr>
<tr><td>Уоллис и Футуна</td><td style="padding-left:10px">WF</td></tr>
<tr><td>Уругвай</td><td style="padding-left:10px">UY</td></tr>
<tr><td>Фареры</td><td style="padding-left:10px">FO</td></tr>
<tr><td>Фиджи</td><td style="padding-left:10px">FJ</td></tr>
<tr><td>Филиппины</td><td style="padding-left:10px">PH</td></tr>
<tr><td>Финляндия</td><td style="padding-left:10px">FI</td></tr>
<tr><td>Фолклендские острова</td><td style="padding-left:10px">FK</td></tr>
<tr><td>Франция</td><td style="padding-left:10px">FR</td></tr>
<tr><td>Французская Полинезия</td><td style="padding-left:10px">PF</td></tr>
<tr><td>Французские Южные и Антарктические Территории</td><td style="padding-left:10px">TF</td></tr>
<tr><td>Херд и Макдональд</td><td style="padding-left:10px">HM</td></tr>
<tr><td>Хорватия</td><td style="padding-left:10px">HR</td></tr>
<tr><td>ЦАР</td><td style="padding-left:10px">CF</td></tr>
<tr><td>Чад</td><td style="padding-left:10px">TD</td></tr>
<tr><td>Черногория</td><td style="padding-left:10px">ME</td></tr>
<tr><td>Чехия</td><td style="padding-left:10px">CZ</td></tr>
<tr><td>Чили</td><td style="padding-left:10px">CL</td></tr>
<tr><td>Швейцария</td><td style="padding-left:10px">CH</td></tr>
<tr><td>Швеция</td><td style="padding-left:10px">SE</td></tr>
<tr><td>Шпицберген и Ян-Майен</td><td style="padding-left:10px">SJ</td></tr>
<tr><td>Шри-Ланка</td><td style="padding-left:10px">LK</td></tr>
<tr><td>Эквадор</td><td style="padding-left:10px">EC</td></tr>
<tr><td>Экваториальная Гвинея</td><td style="padding-left:10px">GQ</td></tr>
<tr><td>Эритрея</td><td style="padding-left:10px">ER</td></tr>
<tr><td>Эстония</td><td style="padding-left:10px">EE</td></tr>
<tr><td>Эфиопия</td><td style="padding-left:10px">ET</td></tr>
<tr><td>ЮАР</td><td style="padding-left:10px">ZA</td></tr>
<tr><td>Южная Георгия и Южные Сандвичевы острова</td><td style="padding-left:10px">GS</td></tr>
<tr><td>Южный Судан</td><td style="padding-left:10px">SS</td></tr>
<tr><td>Ямайка</td><td style="padding-left:10px">JM</td></tr>
<tr><td>Япония</td><td style="padding-left:10px">JP</td></tr>
</tbody></table>

</div>
<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
</div>
</div>
</div>
</div>
</div>


<div class="table-responsive">
<table class="table table-hover">
<thead>
<tr>
<th scope="col">Дата отправки</th>
<th scope="col">Сообщение</th>
<th scope="col">Прогресс</th>
<th scope="col">Действия</th>
</tr>
</thead>
<tbody>
<?php
$num = 30;
if (isset($_GET['page'])) { $page = intval($_GET['page']); } else { $page = 0; } if ($page==0) { $page=1; }
$qu = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT count(`id`) AS `cnt` FROM `t_gosend`"));
$co = $qu['cnt'];
$total = intval(($co - 1) / $num) + 1;
$page = intval($page);
if(empty($page) or $page < 0) $page = 1;
if($page > $total) $page = $total;
$start = $page * $num - $num;
$qr = mysqli_query($connect_db, "SELECT t_gosend.id,send_total,send_progress,dtstart,ti,msg,img_sm,img_big FROM `t_gosend` INNER JOIN t_messages ON t_gosend.send_msg = t_messages.id ORDER BY id DESC LIMIT $start, $num");
if ($co > 0) {
?>
<?php
while($row = mysqli_fetch_assoc($qr)) {
?>
<tr class="table-light">
<td>
<span data-toggle="tooltip" data-placement="bottom" data-original-title="<?php echo wu_time_left($row['dtstart']-$dt); ?>">
<?php echo date('d.m.Y в H:i',$row['dtstart']); ?>
</span>
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

<form>
<fieldset>
<h6 id="progress-basic"><?php echo $row['send_progress'].'/'.$row['send_total']; ?></h6>
<div class="progress">
<div class="progress-bar" role="progressbar" style="width: <?php echo $row['send_progress']/$row['send_total']*100; ?>%;" aria-valuenow="<?php echo $row['send_progress']; ?>" aria-valuemin="0" aria-valuemax="<?php echo $row['send_total']; ?>"></div>
</div>
</fieldset>
</form>


</td>
<td>
<button type="button" onclick="idel('<?php echo $row['id']; ?>');" class="btn btn-danger" data-toggle="tooltip" data-placement="bottom" data-original-title="Отменить"><i class="fa fa-trash-o"></i></button>
</td>
</tr>
<?php
}
} else {
?>
<tr class="table-light"><td colspan="4"><center>Заданий нет</center></td></tr>
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
function crabs_set_c(c){
$('#icou').val(c);
};

$(document).ready(function() {
$('#crabsdatepicker').datepicker({
format: "dd.mm.yyyy",
orientation: "bottom auto",
language: "ru",
todayHighlight: true
});

$('#crabsdatepicker2').datepicker({
format: "dd.mm.yyyy",
orientation: "bottom auto",
language: "ru",
todayHighlight: true
});

$('#crabsdatepicker_m').datepicker({
format: "dd.mm.yyyy",
orientation: "bottom auto",
language: "ru",
todayHighlight: true
});

$('#crabsdatepickerr_m').datepicker({
format: "dd.mm.yyyy",
orientation: "bottom auto",
language: "ru",
todayHighlight: true
});

$('#crabsdatepicker2_m').datepicker({
format: "dd.mm.yyyy",
orientation: "bottom auto",
language: "ru",
todayHighlight: true
});

$('#crabsdatepicker2r').datepicker({
format: "dd.mm.yyyy",
orientation: "bottom auto",
language: "ru",
todayHighlight: true
});

<?php if (isset($_GET['msg'])) { ?>
$('#modal_add').modal('show');
$('#sel_msg').val('<?php echo intval($_GET['msg']); ?>');
<?php } ?>
<?php if (isset($_GET['msg_m'])) { ?>
$('#modal_add_m').modal('show');
$('#sel_msg_m').val('<?php echo intval($_GET['msg_m']); ?>');
<?php } ?>

});

$('#btn_crabs_start_m').click(function(){
var msg = $('#sel_msg_m').val();
var stream = $('#sel_stream_m').val();
var device = $('#idevice_m').val();
var icou = $('#icou_m').val();
var statgo = $('#statgo_m').val();
var statend = $('#statend_m').val();
var rstatgo = $('#rstatgo_m').val();
var rstatgotime = $('#rstatgotime').val();
var rstatend = $('#rstatend_m').val();
var rstatgotime2 = $('#rstatgotime2').val();
var interval = $('#iintrvl').val();
$.ajax({
type: 'POST',
url: '/actions/admin_push_start_m.php',
data: {'msg': msg, 'stream': stream, 'device': device, 'icou': icou, 'statgo': statgo, 'statend': statend, 'rstatgo': rstatgo, 'rstatgotime': rstatgotime, 'rstatend': rstatend, 'rstatgotime2': rstatgotime2, 'interval': interval, 'token': crabs_tkn},
cache: false,
success: function(result){
if (result == 'msg') {
$.jGrowl('Выберите сообщение', { theme: 'growl-error' });
}
if (result == 'dtstrt') {
$.jGrowl('Выберите дату отложенной отправки', { theme: 'growl-error' });
}
if (result == 'nodts') {
$.jGrowl('Введите валидную дату рассылки С', { theme: 'growl-error' });
}
if (result == 'notimes') {
$.jGrowl('Введите валидное время рассылки С', { theme: 'growl-error' });
}
if (result == 'nodtpo') {
$.jGrowl('Введите валидную дату рассылки ПО', { theme: 'growl-error' });
}
if (result == 'notimepo') {
$.jGrowl('Введите валидное время рассылки ПО', { theme: 'growl-error' });
}
if (result == 'nointerval') {
$.jGrowl('Введите интервал рассылки в часах', { theme: 'growl-error' });
}
if (result == '1') {
$(location).attr('href','gosend');
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


$('#btn_crabs_start').click(function(){
var msg = $('#sel_msg').val();
var stream = $('#sel_stream').val();
var device = $('#idevice').val();
var icou = $('#icou').val();
var statgo = $('#statgo').val();
var statend = $('#statend').val();
var checked = $('#sendafter').prop('checked');
var dtstart = $('#dtstart').val();
$.ajax({
type: 'POST',
url: '/actions/admin_push_start.php',
data: {'msg': msg, 'stream': stream, 'device': device, 'icou': icou, 'statgo': statgo, 'statend': statend, 'checked': checked, 'dtstart': dtstart, 'token': crabs_tkn},
cache: false,
success: function(result){
if (result == 'msg') {
$.jGrowl('Выберите сообщение', { theme: 'growl-error' });
}
if (result == 'dtstrt') {
$.jGrowl('Выберите дату отложенной отправки', { theme: 'growl-error' });
}
if (result == '1') {
$(location).attr('href','gosend');
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

//Отмена
function idel(id){
$.ajax({
type: 'POST',
url: '/actions/admin_push_stop.php',
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

$('#sendafter').bind("change keyup paste input", function(e) {
var checked = $(this).prop('checked');
if (checked == true) {
$("#isaftr").show('slow');
} else {
$("#isaftr").hide('slow');
}
});

$(function () {
$('#datetimepicker1').datetimepicker();
});
</script>

<!-- /Контент -->
<?php include('inc/bottom.php'); ?>