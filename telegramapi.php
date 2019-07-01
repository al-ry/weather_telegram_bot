<?php

use Telegram\Bot\Api; 
$keyboard = [["Узнать погоду"],["Избранные города"],["Добавить город"]]; //Клавиатура
$keyboard_forecast = [["Текущая погода"],["Прогноз"],["Назад\xE2\x9D\x8C"]];


function userUpdates(): void
{
    $telegram -> getWebhookUpdates();
}

function initToken(): Api
{
    return new Api('840599241:AAH6I_Rtq34caNm64rCLJz6mpF0OKHn3iTU');
}

function getText(): string
{
    return $result["message"]["text"];
}

function getUserId(): string
{
    return $result["message"]["chat"]["id"];
}

function getUserName(): string
{
    return  $result["message"]["from"]["username"];
}