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
                $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => getForecastWeather($text)]);
                removeUserCommand($db, "forecastWeather");
            }     
        }        
    }



    function getCurrentWeather(string $city): string {
      $data = getWeatherData($city);
      $temp = getTemperature($data);
      $feelsTemp =  $data['current']['feelslike_c'];
      $humidity = $data['current']['humidity'];
      $country =  $data['location']['country'];
      $discr =  $data['current']['condition']['text'];
      $cloud =  $data['current']['cloud'];
      $pressure =  $data['current']['pressure_mb'];

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

    function getForecastWeather(string $city): string
    {     
        $data = getWeatherData($city);
        if ($city = $data['location']['name'])
        {
            for ($i = 0; $i <= 2; $i++)
            {
                $country = $data['location']['country'];
                $avgTemp = $data['forecast']['forecastday'][$i]['day']['avgtemp_c'];
                $avgHumidity = $data['forecast']['forecastday'][$i]['day']['avghumidity'];
                $discr = $data['forecast']['forecastday'][$i]['day']['condition']['text'];
                $reply = "Forecast weather in " .$city. "(" .$country. "): \n
                -Average temperature: " . $avgTemp. " °C 
                -Weather: " .$discr. "
                -Humidity: " .$avgHumidity. "% \n \n";
                $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
            }
        }
        else
        {
            return "Город или прогноз не найден";
        }
    }

    getForecastWeather('Cape');

    register_shutdown_function(function () {
        http_response_code(200);
    });