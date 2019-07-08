<?php

use Telegram\Bot\Api;
const apiToken = '840599241:AAH6I_Rtq34caNm64rCLJz6mpF0OKHn3iTU';

function getTelegramApi(): void
{
    $telegram = Api(apiToken);
    $telegram -> getWebhookUpdates();
}

function getText(object $result): string
{
    return $result["message"]["text"];
}

function getUserId(array $result): int
{
    return $result["message"]["chat"]["id"];
}

function getUserName(array  $result): string
{
    return  $result["message"]["from"]["username"];
}

function replyMessage(int $chatId,int $reply, string $replyMarkup, object $telegram): object
{
    return $telegram->sendMessage(['chat_id' => $chatId, 'text' => $reply, 'reply_markup' => $replyMarkup]);
}

function getReplyMarkup($keyboard, $telegram): void 
{
    $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
}


