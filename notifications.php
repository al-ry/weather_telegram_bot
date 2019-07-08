<?php
require_once('database.php');
$cityArray = getLastCity($db, $chatId);
$cityName = $cityArray['city_unique'];
if ($cityArray)
{
    replyMessage($chatId, getCurrentWeather($cityName), null, $telegram);    
}
