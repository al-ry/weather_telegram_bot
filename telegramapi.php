<?php

use Telegram\Bot\Api; 

$keyboard = [["Узнать погоду"],["Избранные города"],["Добавить город"]]; //Клавиатура
$keyboard_forecast = [["Текущая погода"],["Прогноз"],["Назад\xE2\x9D\x8C"]];


function userUpdates(Api $telegram): void
{
    $telegram -> getWebhookUpdates();
}

function initToken(): Api
{
    return new Api('840599241:AAH6I_Rtq34caNm64rCLJz6mpF0OKHn3iTU');
}

function getText(array $result): string
{
    return $result["message"]["text"];
}

function getUserId(array  $result): int
{
    return $result["message"]["chat"]["id"];
}

function getUserName(array  $result): string
{
    return  $result["message"]["from"]["username"];
}