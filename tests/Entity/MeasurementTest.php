<?php

namespace App\Tests\Entity;

use App\Entity\Measurement;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
class MeasurementTest extends TestCase
{
    public static function dataGetFahrenheit(): array
    {
        return [
            ['0', 32],
            ['-100', -148],
            ['100', 212],
            ['0.5', 32.9],
            ['-40', -40],
            ['10', 50],
            ['20', 68],
            ['-10', 14],
            ['15.5', 59.9],
            ['25', 77],
        ];
    }

    #[DataProvider('dataGetFahrenheit')]
    public function testGetFahrenheit(string $temperature, float $expectedFahrenheit): void
    {
        $measurement = new Measurement();
        $measurement->setTemperature($temperature);

        $this->assertEquals($expectedFahrenheit, $measurement->getFahrenheit());
    }
}

