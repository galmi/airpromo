<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 22.07.15
 * Time: 11:47
 */

namespace Galmi\AirwaysBundle\Tests\Handlers\Parsers;

use Galmi\AirwaysBundle\Handlers\Downloader;
use Galmi\AirwaysBundle\Handlers\Sources\AirAsia;
use Galmi\AirwaysBundle\Handlers\Parsers\Params;
use Galmi\AirwaysBundle\Handlers\Parsers\Result;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AirAsiaSourceTest extends WebTestCase
{
    public function testGetParamsStringOneWay()
    {
        $airasiaParser = new AirAsia($this->getDownloader(), new \Galmi\AirwaysBundle\Handlers\Parsers\AirAsia());
        $getParamsString = self::getMethod('getParamsString');
        $query = $getParamsString->invokeArgs($airasiaParser, [$this->createParamsOneWay()]);
        $this->assertEquals($query, 'o1=DMK&d1=URT&dd1=2015-08-27&ADT=1&CHD=0&inl=0&s=true&mon=true&cc=THB');
    }

    public function testGetParamsStringReturn()
    {
        $airasiaParser = new AirAsia($this->getDownloader(), new \Galmi\AirwaysBundle\Handlers\Parsers\AirAsia());
        $getParamsString = self::getMethod('getParamsString');
        $query = $getParamsString->invokeArgs($airasiaParser, [$this->createParamsReturn()]);
        $this->assertEquals($query, 'o1=DMK&d1=URT&dd1=2015-08-27&ADT=1&CHD=0&inl=0&s=true&mon=true&cc=THB&dd2=2015-08-30&r=true');
    }

    public function testGetResults()
    {
        $downloader = new Downloader();
        $airasiaParser = new AirAsia($downloader, new \Galmi\AirwaysBundle\Handlers\Parsers\AirAsia(), 'https://booking.airasia.com/Flight/Select');
        $params = $this->createParamsOneWayWeek();
        $results = $airasiaParser->getResults($params);
        $this->assertGreaterThan(0, count($results));
    }

    /**
     * @param $name
     * @return \ReflectionMethod
     */
    protected static function getMethod($name)
    {
        $class = new ReflectionClass('Galmi\AirwaysBundle\Handlers\Sources\AirAsia');
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
            ->setDepartDate(new \DateTime('2015-08-27'))
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
            ->setDepartDate(new \DateTime('2015-08-27'))
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
            ->will($this->returnValue(file_get_contents(__DIR__ . '/../Data/AirAsiaResults.html')));
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