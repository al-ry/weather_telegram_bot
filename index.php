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
    $keyboard = [["Текущая погода"],["Прогноз"]];
    $historyKeyboard = [["Удалить историю"]];
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
            replyMessage($chat_id, $reply, $reply_markup, $telegram);
        }
        elseif ($text == "/help")
        {
            $reply = "С помощью этого бота вы можете узнать погоду по всему миру";
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => true ]);
        }
        elseif ($text == "Текущая погода")
        {
            removeUserCommand($db, $chat_id);
            $reply = "Введите город"; 
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $historyKeyboard, 'resize_keyboard' => true, 'one_time_keyboard' => true ]);
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
            $comandData =  addDataCommand($db, "currentWeather", $chat_id);
            addCommand($db, $comandData);
        }
        elseif ($text == "Прогноз")
        {
            removeUserCommand($db, $chat_id);
            $reply = "Введите город"; 
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $historyKeyboard, 'resize_keyboard' => true, 'one_time_keyboard' => true ]);
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
            $comandData = addDataCommand($db, "forecastWeather", $chat_id);
            addCommand($db, $comandData);
        }
        elseif ($text == "Удалить историю")
        {
            refreshCity($db, $chat_id);
            $reply = "История успешно очищена";
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => true ]);
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
        }
        else
        {
            if (strlen($text) != 0)
            {
                $getUser = getUserCommand($db, $chat_id);
                if ($getUser) 
                {
                    $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => true ]);
                    $userCommand = $getUser['commands'];
                    if ($userCommand == "currentWeather")
                    {
                        if (getCurrentWeather($text) == null)
                        {
                            $reply = "Город не найден попробуйте снова";
                            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply]);
                        }
                        else
                        {
                            $data = [
                                "city_unique" => $text,
                                "user_id" => $chat_id
                            ];
                            addCity($db, $data);
                            removeUserCommand($db, $chat_id);
                            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => getCurrentWeather($text), 'reply_markup' => $reply_markup ]);
                        }
                    }
                    elseif ($userCommand == "forecastWeather")
                    {
                        if (getForecastWeather($text) == null)
                        {
                            $reply = "Город не найден попробуйте снова";
                            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply]); 
                        }
                        else
                        {
                            $data = [
                                "city_unique" => $text,
                                "user_id" => $chat_id
                            ];
                            addCity($db, $data);
                            removeUserCommand($db, $chat_id);
                            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => getForecastWeather($text), 'reply_markup' => $reply_markup ]);
                        }
                    }        
                } 
            }
        }        
    }

    $keyboard = [];
    $db->where ("user_id", 592095051);
    $test =  $db->getOne (DB_NAME . ".city");
    $keyboard[] = $test['city_unique'];
    addFavCity($keyboard, $db);

    print_r($test);

    function addFavCity(array $keyboard, $db): array 
    {
        $db->where ("user_id", 592095051);
        $test =  $db->getOne (DB_NAME . ".city");
        $keyboard[] = $test['city_unique'];
        return $keyboard;
    }

    function getCurrentWeather(string $city): ?string 
    {
        $data = getWeatherData($city); 
        if ($city == getCity($data)) {
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
        } else {
            return null;
        }  
    }

    function getForecastWeather(string $city): ?string 
    {
        $data = getWeatherData($city);
        if ($city == getCity($data)) {
            $country = getCountry($data);
            $location = "Forecast weather in " .$city. "(" .$country. "): \n";
            for ($day = 0; $day <= 2; $day++) {
                $date = getDateNumber($data, $day);
                $avgTemp = getAverageTemperature($data, $day);
                $avgHumidity = getAverageHumidity($data, $day);
                $discr = getWeatherDescription($data, $day);
                $message = "On " .$date. ": \n
                -Average temperature: " . $avgTemp. " °C 
                -Weather: " .$discr. "
                -Humidity: " .$avgHumidity. "% \n \n";
                $reply .= $message;          
            }
            return $location .= $reply;
        } else {
            return null;
        } 
    }