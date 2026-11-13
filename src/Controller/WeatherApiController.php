<?php

namespace App\Controller;

use App\Entity\Measurement;
use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

final class WeatherApiController extends AbstractController
{
    #[Route('/api/v1/weather', name: 'app_weather_api')]
    public function index(
        #[MapQueryParameter] string $country,
        #[MapQueryParameter] string $city,
        #[MapQueryParameter] string $format = 'json',
        #[MapQueryParameter('twig')] bool $twig = false,
        WeatherUtil $util,
    ): Response
    {
        $measurements = $util->getWeatherForCountryAndCity($country, $city);

        if ($twig) {
            if ($format === 'csv') {
                return $this->render('weather_api/index.csv.twig', [
                    'city' => $city,
                    'country' => $country,
                    'measurements' => $measurements,
                ], new Response('', 200, ['Content-Type' => 'text/plain']));
            }

            return $this->render('weather_api/index.json.twig', [
                'city' => $city,
                'country' => $country,
                'measurements' => $measurements,
            ], new Response('', 200, ['Content-Type' => 'application/json']));
        }

        if ($format === 'csv') {
            $lines = ['city,country,date,temperature,fahrenheit,humidity,cloudiness,wind'];
            foreach ($measurements as $m) {
                $lines[] = sprintf(
                    '%s,%s,%s,%s,%s,%s,%s',
                    $city,
                    $country,
                    $m->getDate()->format('Y-m-d'),
                    $m->getTemperature(),
                    $m->getFahrenheit(),
                    $m->getHumidity(),
                    $m->getCloudiness(),
                    $m->getWind()
                );
            }

            return new Response(implode("\n", $lines), 200, [
                'Content-Type' => 'text/plain',
            ]);
        }

        return $this->json([
            'city' => $city,
            'country' => $country,
            'measurements' => array_map(fn(Measurement $m) => [
                'date' => $m->getDate()->format('Y-m-d'),
                'temperature' => $m->getTemperature(),
                'fahrenheit' => $m->getFahrenheit(),
                'humidity' => $m->getHumidity(),
                'cloudiness' => $m->getCloudiness(),
                'wind' => $m->getWind(),
            ], $measurements),
        ]);
    }
}
