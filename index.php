<?php
    include('vendor/autoload.php'); //Подключаем библиотеку
    use Telegram\Bot\Api;
    $telegram = new Api('885752742:AAF63rND57OidzVAJ3ReDp7qGkX7oVaunBY'); //Устанавливаем токен, полученный у BotFather
    $result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
    $text = $result["message"]["text"]; //Текст сообщения
    $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
    $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
    $keyboard = [["Сократить ссылку"],["Расшифровать ссылку"]]; //Клавиатура
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
            $reply = "Даннный бот прдназначен для сокращения и расшифровки сокращенныйх ссылок при помощи сервиса goo.gl. Чтобы воспользоваться ботом, используйте клавиатуру.";
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
            $telegram->sendMessagе([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
        }
        elseif ($text == "Расшифровать ссылку")
        {
            $reply = "Введите сокращенную ссылку (goo.gl)";
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
            $telegram->sendMessagе([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
        }
        elseif ($text == "Сократить ссылку")
        {
            $reply = "Введите несокращенную ссылку";
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
            $telegram->sendMessagе([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
        }
    }
    else
    {
    	$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => "Отправьте текстовое сообщение." ]);
    }
?>