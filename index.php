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
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard_city, 'resize_keyboard' => true, 'one_time_keyboard' => true ]);
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
            addCommand($db, $data);
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
            addCommand($db, $data);
        }
        elseif ($text == "Добавить город")
        {
            ////////db
        }
        elseif ($text == "Назад в главное меню")
        {
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => true ]);
        }
        else
        {
            if (!getUserCommand($db, "currentWeather"))
            {
							$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => getCurrentWeather($text)]);
                if (null)
                {
                    $reply = "Город не найден";
                } 
                removeUserCommand($db, "currentWeather");
            } 
            if (!getUserCommand($db, 'forecastWeather'))
            {   
						  	$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => getForecastWeather($text)]);
                removeUserCommand($db, "forecastWeather");
                if (null)
                {
                    $reply = "Город не найден";
                }
                $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
            }     
        }        
    }

   

    function getCurrentWeather(string $city): string {
			$data = getWeatherData($city); 
			if ($city == getCity($data))
			{
				$temp = getTemperature($data);
				$feelsTemp = getFeelTemperature($data);
				$humidity = getHumidity($data);
				$country = getCountry($data);
				$discr =  getWeatherDescription($data);
				$cloud =  getClouds($data);
				$pressure =  getPressure($data);
				$reply =  "Current weather in " .$city. "(" .$country. "): \n
		  	-Temperature: " .$temp. " °C , feels like " .$feelsTemp . " °C
		  	-Weather: " .$discr. "
		  	-Humidity: " .$humidity. "%
		  	-Pressure: " .floor($pressure / 1.333). " mmHg
		  	-Cloudiness: " .$cloud. "%";
		  	return $reply;
		  	echo $reply;
			}
			else
			{
						return "error";
			}  
    }

    function getForecastWeather(string $city): string {
			$data = getWeatherData($city);
			if ($city == getCity($data))
			{
					$country = getCountry($data);
					$location = "Forecast weather in " .$city. "(" .$country. "): \n";
					for ($day = 0; $day <= 2; $day++)
					{
							$date = getDateNumber($data, $day);
							$avgTemp = getAverageTemperature($data, $day);
							$avgHumidity = $data['forecast']['forecastday'][$day]['day']['avghumidity'];
							$discr = getWeatherDescription($data, $day);
							$message = "On " .$date. ": \n
							-Average temperature: " . $avgTemp. " °C 
							-Weather: " .$discr. "
							-Humidity: " .$avgHumidity. "% \n \n";
							$reply .= $message;          
					}
					return $location .= $reply;
			}
			else
			{
					return "error";
			}
    }

    register_shutdown_function(function () {
        http_response_code(200);
    });