<?php
//Охуевший Мистер Крабс
include('../inc/conf.php');
crabs_token_check($_POST['token']);
$u_id=intval($_SESSION['uid']); $nowusr = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT uid,ty FROM t_users WHERE uid='$u_id' AND pas = '$_SESSION[pass]' LIMIT 1")); $nowusr_ty = explode(' ',$nowusr['ty']);
if (empty($u_id)) { exit('error'); }

//Получение данных
$id = intval($_POST['id']);

$data = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT id,ty,landing FROM `t_streams` WHERE id='$id' LIMIT 1"));
$adata = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT id,pdomain FROM `t_data` LIMIT 1"));
?>
<?php if ($data['ty'] == 0) { ?>
<div class="alert alert-primary">
Для распределения статистики Вы можете использовать в get-запросе метку label для передачи id площадки или субаккаунта.<br />
<u>Примеры:</u><br />
https://<?php echo $adata['pdomain']; ?>/<?php echo $data['landing']; ?>/?strid=<?php echo $id; ?>&label=154<br />
https://<?php echo $adata['pdomain']; ?>/<?php echo $data['landing']; ?>/?strid=<?php echo $id; ?>&label=subaccount2<br />
https://<?php echo $adata['pdomain']; ?>/<?php echo $data['landing']; ?>/?strid=<?php echo $id; ?>&label=subaccount2_154<br />
</div>
<div class="form-group">
<label for="icode">Ваша ссылка:</label>
<input type="text" class="form-control" id="icode" placeholder="Ваша ссылка" value="https://<?php echo $adata['pdomain']; ?>/<?php echo $data['landing']; ?>/?strid=<?php echo $id; ?>" />
</div>

<?php } if ($data['ty'] == 1) { ?>

<div class="alert alert-primary">
Скачайте и загрузите <a href="/actions/admin_code_get_manifest.php" target="_blank">manifest.json</a> и <a href="/actions/admin_code_get_js.php" target="_blank">firebase-messaging-sw.js</a> в корень Вашего сайта.<br /><br />
Между тегами &lt;head&gt; вставьте следующий код:<br />
<code style="color: #cacaca;">
&lt;script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"&gt;&lt;/script&gt;<br />
&lt;script type="text/javascript" src="https://www.gstatic.com/firebasejs/5.8.2/firebase-app.js"&gt;&lt;/script&gt;<br />
&lt;script type="text/javascript" src="https://www.gstatic.com/firebasejs/5.8.2/firebase-messaging.js"&gt;&lt;/script&gt;<br />
&lt;script type="text/javascript" src="https://<?php echo $_SERVER['HTTP_HOST']; ?>/inc/crabs_push/<?php echo $id; ?>"&gt;&lt;/script&gt;
</code>
<br /><br />
Сайт должен быть с SSL сертификатом.<br />
Для распределения статистики Вы можете использовать в get-запросе метку label для передачи id площадки или субаккаунта.
</div>

<?php } ?>