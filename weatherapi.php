<?php

function getWeatherData(string $city): ?array
{
    if (strlen($city) != 0)
    {
        $api = "http://api.apixu.com/v1/forecast.json?key=bd8f380296394c11b8053241192806&q=$city&days=3";
        $weatherData = file_get_contents($api);
        return json_decode($weatherData, true);
    }
    else
    {
        return null;
    }
}

function getTemperature(array $data): string
{
    return $data['current']['temp_c'];
}

function getFeelTemperature(array $data): string
{
    return $data['current']['feelslike_c'];
}

function getHumidity(array $data): string
{
    return $data['current']['humidity'];
}

function getCountry(?array $data): ?string
{
    return $data['location']['country'];
}

function getClouds(array $data): string
{
    return $data['current']['cloud'];
}

function getPressure(array $data): string
{
    return $data['current']['pressure_mb'];
}

function getWeatherDescription(?array $data): ?string
{
    return $data['forecast']['forecastday']['1']['day']['condition']['text'];
}

function getCity(?array $data): ?string
{
    return $data['location']['name'];
}

function getAverageTemperature(?array $data,?int $day): ?string
{
    return $data['forecast']['forecastday'][$day]['day']['avgtemp_c'];
}

function getDateNumber(?array $data,?int $day): ?string
{
    return  $data['forecast']['forecastday'][$day]['date'];
}

function getAverageHumidity(?array $data,?int $day): ?string
{
    return $data['forecast']['forecastday'][$day]['day']['avghumidity'];
}


