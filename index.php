<?php

include('vendor/autoload.php');
use Telegram\Bot\Api; 
 


    $telegram = new Api('840599241:AAH6I_Rtq34caNm64rCLJz6mpF0OKHn3iTU'); //Устанавливаем токен, полученный у BotFather
    $result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
    $text = $result["message"]["text"]; //Текст сообщения
    $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
    $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
    $keyboard = [["Узнать погоду"],["Избранные города"],["Добавить город"]]; //Клавиатура


    $api = "http://api.apixu.com/v1/current.json?key=bd8f380296394c11b8053241192806&q=Moscow";
    $weatherData = file_get_contents($api);
    $weatherData = json_decode($weatheData, true);
    echo $weatherData;
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
            $reply = "Введите город"; 
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
        }
        elseif ($text == "Добавить город")
        {
            ////////db
        }
        else
        {
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' =>  getCurrentWeather($text)]); 
        }        
    }

    function getCurrentWeather(string $city): string {
        // getWeatherData()
      $api = "http://api.apixu.com/v1/current.json?key=bd8f380296394c11b8053241192806&q=$city";
      $weatherData = file_get_contents($api);
      $weatherData = json_decode($weatheData, true);
      //$data = getWeatherData();
      // getTempetrature(array $data): string
      $temp_c = $get_arr['current']['temp_c'];


     // $temp = getTempetrature($data);

      $feelslike_temp = $get_arr['current']['feelslike_c'];
      $humidity = $get_arr['current']['humidity'];
      $country = $get_arr['location']['country'];
      $discr = $get_arr['current']['condition']['text'];
      $cloud = $get_arr['current']['cloud'];
      $pressure = $get_arr['current']['pressure_mb'];

      if ($city = $get_arr['location']['name'])
      {
           return "Current weather in " .$city. "(" .$country. "): \n
           -Temperature: " .$temp_c. " °C , feels like " .$feelslike_temp. " °C
           -Weather: " .$discr. "
           -Humidity: " .$humidity. "%
           -Pressure: " .floor($pressure / 1.333). " mmHg
           -Cloudiness: " .$cloud. "%";
      }
      else
      {
            return null;
      }
    }
