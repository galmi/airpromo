<?php
/**
     * Created by PhpStorm.
     * User: ildar
     * Date: 22.07.15
     * Time: 11:47
     */

namespace Galmi\AirwaysBundle\Tests\Handlers\Parsers;

use Galmi\AirwaysBundle\Handlers\Downloader;
use Galmi\AirwaysBundle\Handlers\Sources\ThaiSmile;
use Galmi\AirwaysBundle\Handlers\Parsers\Params;
use ReflectionClass;

class ThaiSmileSourceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetParamsStringOneWay()
    {
        $thaismileParser = new ThaiSmile($this->getDownloader(), new \Galmi\AirwaysBundle\Handlers\Parsers\ThaiSmile());
        $getParamsString = self::getMethod('getParamsString');
        $query = $getParamsString->invokeArgs($thaismileParser, [$this->createParamsOneWay()]);
        $this->assertEquals($query, 'o1=DMK&d1=URT&dd1=2015-08-27&ADT=1&s=true&mon=true');
    }

    public function testGetParamsStringReturn()
    {
        $thaismileParser = new ThaiSmile($this->getDownloader(), new \Galmi\AirwaysBundle\Handlers\Parsers\ThaiSmile());
        $getParamsString = self::getMethod('getParamsString');
        $query = $getParamsString->invokeArgs($thaismileParser, [$this->createParamsReturn()]);
        $this->assertEquals($query, 'o1=DMK&d1=URT&dd1=2015-08-27&ADT=1&s=true&mon=true&dd2=2015-08-30&r=true');
    }

    public function testGetResults()
    {
        $downloader = new Downloader();
        $thaismileParser = new ThaiSmile($downloader, new \Galmi\AirwaysBundle\Handlers\Parsers\ThaiSmile(), 'https://booking.thaismileair.com/Flight/InternalSelect');
        $params = $this->createParamsOneWayWeek();
        $results = $thaismileParser->getResults($params);
        $this->assertGreaterThan(0, count($results));
    }

    /**
     * @param $name
     * @return \ReflectionMethod
     */
    protected static function getMethod($name)
    {
        $class = new ReflectionClass('Galmi\AirwaysBundle\Handlers\Sources\ThaiSmile');
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
            ->will($this->returnValue(file_get_contents(__DIR__ . '/../Data/ThaiSmileResults.html')));
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
            ->setDestination('CNX')
            ->setDepartDate($date);
        return $params;
    }
}