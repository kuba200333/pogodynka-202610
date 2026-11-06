<?php

namespace App\Controller;

use App\Entity\Location;
use App\Repository\MeasurementRepository;
use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

class WeatherController extends AbstractController
{
    #[Route('/weather/{country}/{city}', name: 'app_weather')]
    public function city(
        #[MapEntity(mapping: ['country' => 'country', 'city' => 'city'])]
        Location $location,
        WeatherUtil $util,
    ): Response
    {
        $measurements = $util->getWeatherForLocation($location);

        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'measurements' => $measurements,
        ]);
    }
}
