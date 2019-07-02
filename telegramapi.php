<?php

use Telegram\Bot\Api;

$telegram = new Api('375466075:AAEARK0r2nXjB67JiB35JCXXhKEyT42Px8s'); //Устанавливаем токен, полученный у BotFather
$result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
$text = $result["message"]["text"]; //Текст сообщения
$chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
$name = $result["message"]["from"]["username"]; //Юзернейм пользователя

$keyboard = [["Узнать погоду"],["Избранные города"],["Добавить город"]]; //Клавиатура
$keyboard_forecast = [["Текущая погода"],["Прогноз"],["Назад\xE2\x9D\x8C"]];


function userUpdates(Api $telegram): array
{
   return $telegram -> getWebhookUpdates();
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