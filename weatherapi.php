<?php

function getWeatherData(): array
{
    $api = "http://api.apixu.com/v1/current.json?key=bd8f380296394c11b8053241192806&q=$city";
    $weatherData = file_get_contents($api);
    $weatherData = json_decode($weatherData, true);
}

function getTemperature(array $data): string
{

}