<?php

use Telegram\Bot\Api;

function userUpdates(Api $telegram): void
{
   $telegram -> getWebhookUpdates();
}

function initToken(): Api
{
    return new Api('840599241:AAH6I_Rtq34caNm64rCLJz6mpF0OKHn3iTU');
}

function getText(object $result)
{
    $result["message"]["text"];
}

function getUserId(object $result)
{
    return $result["message"]["chat"]["id"];
}

function getUserName(object $result)
{
    return $result["message"]["from"]["username"];
}