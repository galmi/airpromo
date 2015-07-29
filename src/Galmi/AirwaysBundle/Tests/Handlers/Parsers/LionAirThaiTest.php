<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 29.07.15
 * Time: 11:51
 */

namespace Galmi\AirwaysBundle\Tests\Handlers\Parsers;

use Galmi\AirwaysBundle\Handlers\Parsers\LionAirThai;
use Galmi\AirwaysBundle\Handlers\Parsers\Params;
use Galmi\AirwaysBundle\Handlers\Parsers\Result;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LionAirThaiTest extends WebTestCase
{
    public function testGetParamsAsDataOneWay()
    {
        $lionAirThaiParser = new LionAirThai($this->getDownloader());
        $getParamsString = self::getMethod('getParamsData');
        $query = $getParamsString->invokeArgs($lionAirThaiParser, [$this->createParamsOneWay()]);
        $testData = [
            'pjourney' => 2,
            'depCity' => 'DMK',
            'arrCity' => 'URT',
            'dpd1' => '20/08/2015',
            'dpd2' => '',
            'sAdult' => 1,
            'sChild' => 0,
            'sInfant' => 0,
            'currency' => 'THB',
            'cTabID' => 35
        ];
        $this->assertEquals($query, $testData);
    }

    public function testGetParamsStringReturn()
    {
        $lionAirThaiParser = new LionAirThai($this->getDownloader());
        $getParamsString = self::getMethod('getParamsData');
        $query = $getParamsString->invokeArgs($lionAirThaiParser, [$this->createParamsReturn()]);
        $testData = [
            'pjourney' => 2,
            'depCity' => 'DMK',
            'arrCity' => 'URT',
            'dpd1' => '20/08/2015',
            'dpd2' => '30/08/2015',
            'sAdult' => 1,
            'sChild' => 0,
            'sInfant' => 0,
            'currency' => 'THB',
            'cTabID' => 35
        ];
        $this->assertEquals($query, $testData);
    }

    public function testParseResults()
    {
        $downloader = $this->getDownloader();
        $lionAirThaiParser = new LionAirThai($downloader);
        $parseResults = self::getMethod('parseResults');
        /** @var Result[] $results */
        $results = $parseResults->invokeArgs($lionAirThaiParser, [$downloader->get('123'), $this->createParamsReturn()]);

        $resultCheck = [
            '20.08.2015 08:55 DMK 10:10 URT 1095.00',
            '20.08.2015 14:00 DMK 15:10 URT 995.00',
            '20.08.2015 15:05 DMK 16:15 URT 795.00',
            '20.08.2015 18:55 DMK 20:10 URT 995.00',
        ];

        foreach ($results as &$row) {
            $row = $row->__toString();
        }
        $this->assertEquals($results, $resultCheck);
    }

    /**
     * @param $name
     * @return \ReflectionMethod
     */
    protected static function getMethod($name)
    {
        $class = new ReflectionClass('Galmi\AirwaysBundle\Handlers\Parsers\LionAirThai');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * @return Params
     */
    protected function createParamsOneWay()
    {
        $params = new Params();
        $params
            ->setOrigin('DMK')
            ->setDepartDate(new \DateTime('2015-08-20'))
            ->setDestination('URT');
        return $params;
    }

    /**
     * @return Params
     */
    protected function createParamsReturn()
    {
        $params = new Params();
        $params
            ->setOrigin('DMK')
            ->setDepartDate(new \DateTime('2015-08-20'))
            ->setDestination('URT')
            ->setReturnDate(new \DateTime('2015-08-30'));
        return $params;
    }

    /**
     * @return \Galmi\AirwaysBundle\Handlers\Downloader
     */
    protected function getDownloader()
    {
        $mock = $this->getMock('Galmi\AirwaysBundle\Handlers\Downloader');
        $mock->expects($this->any())
            ->method('get')
            ->will($this->returnValue(file_get_contents(__DIR__ . '/LionAirThaiResults.html')));
        return $mock;
    }
}