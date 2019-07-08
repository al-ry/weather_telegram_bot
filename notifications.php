<?php
    include('vendor/autoload.php');
    require_once('telegramapi.php');
    require_once('weatherapi.php');
    require_once('database.php');
    require_once('notifications.php');

$cityArray =  getLastCity($db, $chatId);
$cityName = $cityArray['city_unique'];
if ($cityArray)
{
    replyMessage($chatId, getCurrentWeather($cityName), null, $telegram);    
}
