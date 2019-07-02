<?php

use Telegram\Bot\Api;

function userUpdates($telegram)
{
   $telegram -> getWebhookUpdates();
}

function initToken()
{
    return new Api('840599241:AAH6I_Rtq34caNm64rCLJz6mpF0OKHn3iTU');
}

function getText($result)
{
    return $result["message"]["text"];
}

function getUserId($result)
{
    return $result["message"]["chat"]["id"];
}

function getUserName($result)
{
    return  $result["message"]["from"]["username"];
}