<?php


$cityArray =  getLastCity($db, $chatID);
$cityName = $cityArray['city_unique'];
if ($cityArray)
{
    replyMessage($chatId, getCurrentWeather($cityName), null, $telegram);    
}
