<?php


require_once("database.php");
include('vendor/autoload.php');
$db = initDB();
$telegram = new Api(apiToken);
use Telegram\Bot\Api;


$cityArray = getLastCity($db, $chatId);
$cityName = $cityArray['city_unique'];
if ($cityArray)
{
    replyMessage($chatId, getCurrentWeather($cityName), null, $telegram);    
}
