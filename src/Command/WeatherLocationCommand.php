<?php

namespace App\Command;

use App\Repository\LocationRepository;
use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'weather:location')]
class WeatherLocationCommand
{
    public function __construct(
        private LocationRepository $locationRepository,
        private WeatherUtil $weatherUtil,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument('Location ID')] int $id,
    ): int {
        $location = $this->locationRepository->find($id);
        $measurements = $this->weatherUtil->getWeatherForLocation($location);

        $io->writeln(sprintf('Location: %s', $location->getCity()));
        $io->table(
            ['Date', 'Temperature', 'Humidity', 'Cloudiness', 'Wind'],
            array_map(fn($m) => [
                $m->getDate()->format('Y-m-d'),
                $m->getTemperature(),
                $m->getHumidity(),
                $m->getCloudiness(),
                $m->getWind(),
            ], $measurements)
        );

        return Command::SUCCESS;
    }
}
