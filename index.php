<?php
    include('vendor/autoload.php'); //Подключаем библиотеку
    use Telegram\Bot\Api; 

    $telegram = new Api('840599241:AAH6I_Rtq34caNm64rCLJz6mpF0OKHn3iTU'); //Устанавливаем токен, полученный у BotFather
    $result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
    $text = $result["message"]["text"]; //Текст сообщения
    $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
    $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
    $keyboard = [["Узнать погоду"],["Избранные города"]]; //Клавиатура
    $keyboard_forecast = [["Текущая погода"],["Прогноз"]];

//////////////tests
   $country = "Yoshkar-Ola";
   define ("link", "http://api.apixu.com/v1/current.json?key=bd8f380296394c11b8053241192806&q=$country");
    $weather_data = file_get_contents(link);
    $decode = json_decode($weather_data, true);
    var_dump($decode) ;
    function printArr($array){
        echo '<pre> ' . print_r($array, true) . ' <pre>';
    }
    printArr($decode);
    echo $decode['location']['name'];
/////////////


    if($text)
    {
        if ($text == "/start")
        {
            if (strlen($name) != 0)
            {
                $reply = "Добро пожаловать, ".$name."!";
            }
            else
            {
                $reply = "Добро пожаловать, Незнакомец";
            }
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
        }
        elseif ($text == "/help")
        {
            $reply = "С помощью этого бота вы можете узнать погоду по всему миру";
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
        }
        elseif ($text == "Узнать погоду")
        {
            $reply = "Введите название населенного пункта";
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
        }
    }
    else
    {
    	$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => "Отправьте текстовое сообщение." ]);
    }
    

    function getWeather($city){
      $api = "http://api.apixu.com/v1/current.json?key=bd8f380296394c11b8053241192806&q=$city";
      $weather_data = file_get_contents($api);
      $get_arr = json_decode($weather_data, true);
      $result = $get_arr['current']['temp_c'];
      return 'Temperarute in ' .$city. "=" .$result. " C";
    }
?>