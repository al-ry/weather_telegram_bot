<?php

include('vendor/autoload.php');
require_once('telegramapi.php');
require_once('weatherapi.php');
require_once('database.php');
use Telegram\Bot\Api;

    $test = new MysqliDb('127.0.0.1', 'root', '', '');
    $db = initDB();

 
    $telegram = new Api('840599241:AAH6I_Rtq34caNm64rCLJz6mpF0OKHn3iTU'); //Устанавливаем токен, полученный у BotFather
    $result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользовател
    $text = $result["message"]["text"]; //Текст сообщения
    $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
    $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
    $keyboard = [["Узнать погоду"],["Избранные города"],["Добавить город"]]; //Клавиатура
    $keyboard_forecast = [["Текущая погода"],["Прогноз"],["Назад\xE2\x9D\x8C"]];
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
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard_city, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
        }
        elseif ($text == "Текущая погода")
        {
            removeUserCommand($db, $chat_id);
            $reply = "Введите город"; 
            $data = [
                "commands" => "currentWeather",
                "user_id" => $chat_id
            ];
            $id = addCommand($db, $data);
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
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
            $id = addCommand($db, $data);
        }
        elseif ($text == "Добавить город")
        {
            ////////db
        }
        else
        {
            if (getUserCommand($db, 'currentWeather') == 'currentWeather')
            {
                $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => getCurrentWeather($text)]);
                removeUserCommand($db, "currentWeather");
            } 
            if (getUserCommand($db, 'forecastWeather') == 'forecastWeather')
            {
                removeUserCommand($db, "forecastWeather");
            }     
        }        
    }

    $api = "http://api.apixu.com/v1/current.json?key=bd8f380296394c11b8053241192806&q=Paris";
    $weatherData = file_get_contents($api);
    $weatherData = json_decode($weatherData, true);
    echo ' <pre> ' . print_r($weatherData) . " </pre>";

    function getCurrentWeather(string $city): string {
      $data = getWeatherData($city);
      $temp = getTemperature($data);
      $feelslike_temp = $get_arr['current']['feelslike_c'];
      $humidity = $get_arr['current']['humidity'];
      $country = $get_arr['location']['country'];
      $discr = $get_arr['current']['condition']['text'];
      $cloud = $get_arr['current']['cloud'];
      $pressure = $get_arr['current']['pressure_mb'];

      if ($city = $data['location']['name'])
      {
           return "Current weather in " .$city. "(" .$country. "): \n
           -Temperature: " .$temp. " °C , feels like " .$feelsTemp . " °C
           -Weather: " .$discr. "
           -Humidity: " .$humidity. "%
           -Pressure: " .floor($pressure / 1.333). " mmHg
           -Cloudiness: " .$cloud. "%";
      }
      else
      {
            return 'Не найдено';
      }
    }



    register_shutdown_function(function () {
        http_response_code(200);
    });