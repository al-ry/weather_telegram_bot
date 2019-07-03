<?php

use Telegram\Bot\Api;

function getTelegramApi(): array
{
    $telegram = Api('840599241:AAH6I_Rtq34caNm64rCLJz6mpF0OKHn3iTU');
    $telegram -> getWebhookUpdates();
}

function getText(object $result)
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