<?php

function getWeatherData(string $city): array
{
    $api = "http://api.apixu.com/v1/forecast.json?key=bd8f380296394c11b8053241192806&q=$city&days=3";
    $weatherData = file_get_contents($api);
    return $weatherData = json_decode($weatherData, true);
}

function getTemperature(array $data): string
{
    return $data['current']['temp_c'];
}