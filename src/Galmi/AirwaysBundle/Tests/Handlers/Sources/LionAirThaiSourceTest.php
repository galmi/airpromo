<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 29.07.15
 * Time: 11:51
 */

namespace Galmi\AirwaysBundle\Tests\Handlers\Parsers;

use Galmi\AirwaysBundle\Handlers\Downloader;
use Galmi\AirwaysBundle\Handlers\Sources\LionAirThai;
use Galmi\AirwaysBundle\Handlers\Parsers\Params;
use Galmi\AirwaysBundle\Handlers\Parsers\Result;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LionAirThaiSourceTest extends WebTestCase
{
    public function testGetParamsAsDataOneWay()
    {
        $lionAirThaiParser = new LionAirThai($this->getDownloader(), new \Galmi\AirwaysBundle\Handlers\Parsers\LionAirThai());
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
        $lionAirThaiParser = new LionAirThai($this->getDownloader(), new \Galmi\AirwaysBundle\Handlers\Parsers\LionAirThai());
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

    public function testGetResults()
    {
        $downloader = new Downloader();
        $lionThaiAirParser = new LionAirThai($downloader, new \Galmi\AirwaysBundle\Handlers\Parsers\LionAirThai(), 'http://search.lionairthai.com/mobile/Search/SearchFlight');
        $params = $this->createParamsOneWayWeek();
        $results = $lionThaiAirParser->getResults($params);
        $this->assertGreaterThan(0, count($results));
    }

    /**
     * @param $name
     * @return \ReflectionMethod
     */
    protected static function getMethod($name)
    {
        $class = new ReflectionClass('Galmi\AirwaysBundle\Handlers\Sources\LionAirThai');
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
            ->will($this->returnValue(file_get_contents(__DIR__ . '/../Data/LionAirThaiResults.html')));
        return $mock;
    }

    /**
     * @return Params
     */
    private function createParamsOneWayWeek()
    {
        $params = new Params();
        $date = new \DateTime();
        $date->add(\DateInterval::createFromDateString('+1 week'));
        $params
            ->setOrigin('DMK')
            ->setDestination('URT')
            ->setDepartDate($date);
        return $params;
    }
}