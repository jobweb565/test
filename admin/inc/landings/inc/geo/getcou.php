<?php
require_once("vendor/autoload.php");
use GeoIp2\Database\Reader;
$reader = new Reader(dirname(__FILE__).'/country.mmdb');
$record = $reader->country($ip);
$crabs_country = $record->country->isoCode;