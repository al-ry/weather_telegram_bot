<?php

include('vendor/autoload.php');
require_once('telegramapi.php');
require_once('weatherapi.php');
require_once('database.php');
use Telegram\Bot\Api;

    $db = initDB();
    $telegram = new Api('840599241:AAH6I_Rtq34caNm64rCLJz6mpF0OKHn3iTU'); //Устанавливаем токен, полученный у BotFather
    $result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользовател
    $text = $result["message"]["text"]; //Текст сообщения
    $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
    $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
    $keyboard = [["Узнать погоду"],["Избранные города"],["Добавить город"]]; //Клавиатура
    $keyboard_forecast = [["Текущая погода"],["Прогноз"],["Назад в главное меню"]];
    $keyboard_city = [];
    if($text)
    {
        if ($text == "/start")
        {
            if (strlen($name) != 0)
            {
                $reply = "Привет, ".$name."!";
            }
            else
            {
                $reply = "Привет, незнакомец";
            }
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => true ]);
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
        }
        elseif ($text == "/help")
        {
            $reply = "С помощью этого бота вы можете узнать погоду по всему миру";
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => true ]);
        }
        else
        {
 
        }        
    }
    register_shutdown_function(function () {
        http_response_code(200);
    });