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
        elseif ($text == "Узнать погоду")
        {
            $reply = "Выберите опцию из меню";
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard_forecast, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
        }
        elseif ($text == "Избранные города")
        {
            ////////db
        }
        elseif ($text == "Текущая погода")
        {
            removeUserCommand($db, $chat_id);
            $reply = "Введите город"; 
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
            $data = [
                "commands" => "currentWeather",
                "user_id" => $chat_id
            ];
            addCommand($db, $data);
        }
        elseif ($text == "Прогноз")
        {
            removeUserCommand($db, $chat_id);
            $reply = "Введите город"; 
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
            $data = [
                "commands" => "forecastWeather",
                "user_id" => $chat_id
            ];
            addCommand($db, $data);
        }
        elseif ($text == "Добавить город")
        {
            ////////db
            $reply = "Введите город, который желаете добавить"; 
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
            $data = [
                "commands" => "addCity",
                "user_id" => $chat_id
            ];
            addCommand($db, $data);
        }
        elseif ($text == "Назад в главное меню")
        {
            
        }
        else
        {
            if (!getUserCommand($db, "addCity"))
            {
                array_push($keyboard_city, $text);
                $reply = "Город успешно добавлен";
                $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
            }       		
            if (!getUserCommand($db, "currentWeather"))
            {
                removeUserCommand($db, "currentWeather");
            } 
            if (!getUserCommand($db, 'forecastWeather'))
            {   
                removeUserCommand($db, "forecastWeather");
            }  
        }        
    }
    register_shutdown_function(function () {
        http_response_code(200);
    });