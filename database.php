<?php

const DB_HOST = 'eu-cdbr-west-02.cleardb.net';
const DB_USER = 'b06b82c6cf78a6';
const DB_PASS = 'e0efc55a674f218';
const DB_NAME = 'heroku_253b17b01e157dc';
const USER_ID = 'user_id';

function initDB(): MysqliDb
{
    return new MysqliDb (DB_HOST, DB_USER, DB_PASS, DB_NAME);
}

function removeUserCommand(MysqliDb $db, string $user): void
{
    $db->where (USER_ID, $user);
    $db->delete(DB_NAME . '.bot_commands');
}

function addCommand(MysqliDb $db, array $data): void
{
    $db->insert(DB_NAME . '.bot_commands', $data);
}

function getUserCommand(MysqliDb $db, string $user): ?array
{
    $db->where (USER_ID, $user);
    return $db->getOne (DB_NAME . ".bot_commands"); 
}

function addDataCommand(MysqliDb $db, string $command, $chatId): array
{
    $data = [
        "commands" => $command,
        "user_id" => $chatId
    ];
    return $data; 
}

function addDataCity(MysqliDb $db, string $city, int $chatId): array
{
    $db->where ("user_id", $chat_id);
    $test = $db->getOne (DB_NAME . ".city");
    $data = [
        "user_id" => $chatId,
        "city_unique" => $city
    ];
    return $data; 
}


function addCity(MysqliDb $db, array $data): void
{
    $db->insert(DB_NAME . '.city', $data);
}

function refreshCity(MysqliDb $db, string $user): void
{
    $db->where (USER_ID, $user);
    $db->delete(DB_NAME . '.city');
}

function getLastCity(MysqliDb $db, string $user): ?array
{
    $db->where (USER_ID, $user);
    return $db->getOne (DB_NAME . ".");   
}

