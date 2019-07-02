<?php

const DB_HOST = 'eu-cdbr-west-02.cleardb.net';
const DB_USER = 'b06b82c6cf78a6';
const DB_PASS = 'e0efc55a674f218';
const DB_NAME = 'heroku_253b17b01e157dc';


function initDB(): MysqliDb
{
    return new MysqliDb (DB_HOST, DB_USER, DB_PASS, DB_NAME);
}

function removeUserCommand(MysqliDb $db, string $command): void
{
    $db->where ("commands", $command);
    $db->delete(DB_NAME . '.bot_commands');
}

function addCommand(MysqliDb $db, array $data): void
{
    $db->insert(DB_NAME . '.bot_commands', $data);
}

function updateCommand(MysqliDb $db, array $data): void
{
    removeUserCommand($db, $user);
    addCommand($db, $data);
}

function getUserCommand(MysqliDb $db, string $command): string
{
    $db->where ("commands", $command);
    $getDataDB = $db->getOne (DB_NAME . ".bot_commands"); 
    return $getDataDB['commands']; 
}
