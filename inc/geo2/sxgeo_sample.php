<?php
// Пример работы с классом SxGeo v2.2
header('Content-type: text/plain; charset=utf8');

// Подключаем SxGeo.php класс
include("SxGeo.php");
// Создаем объект
// Первый параметр - имя файла с базой (используется оригинальная бинарная база SxGeo.dat)
// Второй параметр - режим работы: 
//     SXGEO_FILE   (работа с файлом базы, режим по умолчанию); 
//     SXGEO_BATCH (пакетная обработка, увеличивает скорость при обработке множества IP за раз)
//     SXGEO_MEMORY (кэширование БД в памяти, еще увеличивает скорость пакетной обработки, но требует больше памяти)
$SxGeo = new SxGeo('SxGeoCity.dat');
//$SxGeo = new SxGeo('SxGeoCity.dat', SXGEO_BATCH | SXGEO_MEMORY); // Самый производительный режим, если нужно обработать много IP за раз

//Определение IP
$ip_client = @$_SERVER['HTTP_CLIENT_IP'];
$ip_forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
$ip_remote = $_SERVER['REMOTE_ADDR'];
if(filter_var($ip_client, FILTER_VALIDATE_IP))
{
$ip = $ip_client;
}
elseif(filter_var($ip_forward, FILTER_VALIDATE_IP))
{
$ip = $ip_forward;
}
else
{
$ip = $ip_remote;
}

print_r($SxGeo->getCityFull($ip)); // Вся информация о городе
print_r($SxGeo->get($ip));         // Краткая информация о городе или код страны (если используется база SxGeo Country)
print_r($SxGeo->about());          // Информация о базе данных
