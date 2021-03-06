<?php
    include('vendor/autoload.php');
    require_once('telegramapi.php');
    require_once('weatherapi.php');
    require_once('database.php');
    use Telegram\Bot\Api;

    $db = initDB();
    $telegram = new Api(apiToken); //Устанавливаем токен, полученный у BotFather
    $result = getTelegramApi($telegram); //Передаем в переменную $result полную информацию о сообщении пользовател
    $text = getText($result); //Текст сообщения
    $chatId = getUserId($result); //Уникальный идентификатор пользователя
    $name = getUserName($result); //Юзернейм пользователя
    $keyboard = [["Current weather"],["Forecast Weather"]];

    if($text) 
    {
        if ($text == "/start") 
        {
            if (strlen($name) != 0)
            {
                $reply = "Hello, ".$name."!";
            }
            else
            {
                $reply = "Hello, stranger";
            }
            $replyMarkup = getReplyMarkup($keyboard, $telegram);
            replyMessage($chatId, $reply, $replyMarkup, $telegram);
        }
        elseif ($text == "/help")
        {
            $reply = "The bot can show the weather all over the world.";
            $replyMarkup= getReplyMarkup($keyboard, $telegram);
            replyMessage($chatId, $reply, $replyMarkup, $telegram);
        }
        elseif ($text == "Current weather")
        {
            removeUserCommand($db, $chatId);
            refreshCity($db, $chatId);
            $reply = "Send me a city"; 
            replyMessage($chatId, $reply, null, $telegram);
            $comandData =  addDataCommand($db, "currentWeather", $chatId);
            addCommand($db, $comandData);
        }
        elseif ($text == "Forecast Weather")
        {
            removeUserCommand($db, $chatId);
            refreshCity($db, $chatId);
            $reply = "Send me a city"; 
            replyMessage($chatId, $reply, null, $telegram);
            $comandData = addDataCommand($db, "forecastWeather", $chatId);
            addCommand($db, $comandData);
        }
        else
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
                        $reply = "City is not found";
                        replyMessage($chatId, $reply, null, $telegram);
                    }
                    else
                    {
                        $cityData = addDataCity($db, $text, $chatId);
                        addCity($db, $cityData);
                        removeUserCommand($db, $chatId);
                        replyMessage($chatId, getCurrentWeather($text), $replyMarkup, $telegram);   
                    }
                }
                elseif ($userCommand == "forecastWeather")
                {
                    if (getForecastWeather($text) == null)
                    {
                        $reply = "City is not found";
                        replyMessage($chatId, $reply, null, $telegram);
                    }
                    else
                    {
                        $cityData = addDataCity($db, $text, $chatId);
                        addCity($db, $cityData);
                        replyMessage($chatId, getForecastWeather($text), $replyMarkup, $telegram); 
                    }
                }        
            }           
        }        
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
