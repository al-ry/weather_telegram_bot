<?php

use Telegram\Bot\Api;
const apiToken = '840599241:AAH6I_Rtq34caNm64rCLJz6mpF0OKHn3iTU';

function getTelegramApi(Api $telegram): object
{
    return $telegram -> getWebhookUpdates();
}

function getText(object $result): ?string
{
    return $result["message"]["text"];
}

function getUserId(object $result): ?int
{
    return $result["message"]["chat"]["id"];
}

function getUserName(object $result): ?string
{
    return $result["message"]["from"]["username"];
}

function replyMessage(int $chatId, string $reply, string $replyMarkup, Api $telegram): object
{
    return $telegram->sendMessage(['chat_id' => $chatId, 'text' => $reply, 'reply_markup' => $replyMarkup]);
}

function getReplyMarkup(array $keyboard, Api $telegram): string
{
    return $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => true ]);
}


