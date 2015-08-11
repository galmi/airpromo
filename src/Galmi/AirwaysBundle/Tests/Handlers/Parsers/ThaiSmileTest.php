<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 22.07.15
 * Time: 11:47
 */

namespace Galmi\AirwaysBundle\Tests\Handlers\Parsers;

use Galmi\AirwaysBundle\Handlers\Downloader;
use Galmi\AirwaysBundle\Handlers\Parsers\ThaiSmile;
use Galmi\AirwaysBundle\Handlers\Parsers\Params;
use Galmi\AirwaysBundle\Handlers\Parsers\Result;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ThaiSmileTest extends WebTestCase
{
    public function testGetParamsStringOneWay()
    {
        $thaismileParser = new ThaiSmile($this->getDownloader());
        $getParamsString = self::getMethod('getParamsString');
        $query = $getParamsString->invokeArgs($thaismileParser, [$this->createParamsOneWay()]);
        $this->assertEquals($query, 'o1=DMK&d1=URT&dd1=2015-08-27&ADT=1&s=true&mon=true');
    }

    public function testGetParamsStringReturn()
    {
        $thaismileParser = new ThaiSmile($this->getDownloader());
        $getParamsString = self::getMethod('getParamsString');
        $query = $getParamsString->invokeArgs($thaismileParser, [$this->createParamsReturn()]);
        $this->assertEquals($query, 'o1=DMK&d1=URT&dd1=2015-08-27&ADT=1&s=true&mon=true&dd2=2015-08-30&r=true');
    }

    public function testParseResults()
    {
        $downloader = $this->getDownloader();
        $thaismileParser = new ThaiSmile($downloader);
        $parseResults = self::getMethod('parseResults');
        /** @var Result[] $results */
        $results = $parseResults->invokeArgs($thaismileParser, [$downloader->get('123'), $this->createParamsReturn()]);

        $resultCheck = [
            '27.08.2015 07:00 DMK 08:15 URT 1189.00',
            '27.08.2015 08:10 DMK 09:25 URT 1189.00',
            '27.08.2015 12:15 DMK 13:30 URT 1189.00',
            '27.08.2015 16:45 DMK 18:00 URT 1189.00',
            '27.08.2015 19:25 DMK 20:40 URT 1189.00'
        ];

        foreach ($results as &$row)
        {
            $row = $row->__toString();
        }
        $this->assertEquals($results, $resultCheck);

        //todo Добавить проверку source
    }

    public function testGetResults()
    {
        $downloader = new Downloader();
        $thaismileParser = new ThaiSmile($downloader, 'https://booking.thaismileair.com/Flight/InternalSelect');
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
        $class = new ReflectionClass('Galmi\AirwaysBundle\Handlers\Parsers\ThaiSmile');
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
            ->will($this->returnValue(file_get_contents(__DIR__ . '/ThaiSmileResults.html')));
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