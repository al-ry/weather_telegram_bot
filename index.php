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
    $chataId = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
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
            $replyMarkup = getReplyMarkup($keyboard, $telegram);
            replyMessage($chataId, $reply, $replyMarkup, $telegram);
        }
        elseif ($text == "/help")
        {
            $reply = "С помощью этого бота вы можете узнать погоду по всему миру";
            $replyMarkup= getReplyMarkup($keyboard, $telegram);
            replyMessage($chatId, $reply, $replyMarkup, $telegram);
        }
        elseif ($text == "Текущая погода")
        {
            removeUserCommand($db, $chatId);
            $reply = "Введите город"; 
            $replyMarkup= getReplyMarkup($historyKeyboard, $telegram);
            replyMessage($chatId, $reply, $replyMarkup, $telegram);
            $comandData =  addDataCommand($db, "currentWeather", $chatId);
            addCommand($db, $comandData);
        }
        elseif ($text == "Прогноз")
        {
            removeUserCommand($db, $chatId);
            $reply = "Введите город"; 
            $replyMarkup= getReplyMarkup($historyKeyboard, $telegram);
            replyMessage($chatId, $reply, $replyMarkup, $telegram);
            $comandData = addDataCommand($db, "forecastWeather", $chatId);
            addCommand($db, $comandData);
        }
        elseif ($text == "Удалить историю")
        {
            refreshCity($db, $chatId);
            $reply = "История успешно очищена";
            $replyMarkup= getReplyMarkup($keyboard, $telegram);
            replyMessage($chatId, $reply, $replyMarkup, $telegram);         
        }
        else
        {
            if (strlen($text) != 0)
            {
                $getUser = getUserCommand($db, $chatId);
                if ($getUser) 
                {
                    $replyMarkup = getReplyMarkup($keyboard, $telegram);
                    $userCommand = $getUser['commands'];
                    if ($userCommand == "currentWeather")
                    {
                        if (getCurrentWeather($text) == null)
                        {
                            $reply = "Город не найден попробуйте снова";
                            replyMessage($chatId, $reply, null, $telegram);
                        }
                        else
                        {
                            $data = [
                                "city_unique" => $text,
                                "user_id" => $chatId
                            ];
                            addCity($db, $data);
                            removeUserCommand($db, $chatId);
                            replyMessage($chatId, getCurrentWeather($text), $replyMarkup, $telegram);   
                        }
                    }
                    elseif ($userCommand == "forecastWeather")
                    {
                        if (getForecastWeather($text) == null)
                        {
                            $reply = "Город не найден попробуйте снова";
                            replyMessage($chatId, $reply, null, $telegram);
                        }
                        else
                        {
                            $data = [
                                "city_unique" => $text,
                                "user_id" => $chatId
                            ];
                            addCity($db, $data);
                            removeUserCommand($db, $chatId);
                            replyMessage($chatId, getForecastWeather($text), $replyMarkup, $telegram); 
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