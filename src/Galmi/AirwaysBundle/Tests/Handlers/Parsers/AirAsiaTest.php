<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 22.07.15
 * Time: 11:47
 */

namespace Galmi\AirwaysBundle\Tests\Handlers\Parsers;

use Galmi\AirwaysBundle\Handlers\Downloader;
use Galmi\AirwaysBundle\Handlers\Parsers\AirAsia;
use Galmi\AirwaysBundle\Handlers\Parsers\Params;
use Galmi\AirwaysBundle\Handlers\Parsers\Result;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AirAsiaTest extends WebTestCase
{
    public function testGetParamsStringOneWay()
    {
        $airasiaParser = new AirAsia($this->getDownloader());
        $getParamsString = self::getMethod('getParamsString');
        $query = $getParamsString->invokeArgs($airasiaParser, [$this->createParamsOneWay()]);
        $this->assertEquals($query, 'o1=DMK&d1=URT&dd1=2015-08-27&ADT=1&CHD=0&inl=0&s=true&mon=true&cc=THB');
    }

    public function testGetParamsStringReturn()
    {
        $airasiaParser = new AirAsia($this->getDownloader());
        $getParamsString = self::getMethod('getParamsString');
        $query = $getParamsString->invokeArgs($airasiaParser, [$this->createParamsReturn()]);
        $this->assertEquals($query, 'o1=DMK&d1=URT&dd1=2015-08-27&ADT=1&CHD=0&inl=0&s=true&mon=true&cc=THB&dd2=2015-08-30&r=true');
    }

    public function testParseResults()
    {
        $downloader = $this->getDownloader();
        $airasiaParser = new AirAsia($downloader);
        $parseResults = self::getMethod('parseResults');
        /** @var Result[] $results */
        $results = $parseResults->invokeArgs($airasiaParser, [$downloader->get('123'), $this->createParamsReturn()]);

        $resultCheck = [
            '27.08.2015 07:00 DMK 08:10 URT 891.99',
            '27.08.2015 09:50 DMK 10:55 URT 1052.00',
            '27.08.2015 11:40 DMK 12:50 URT 891.99',
            '27.08.2015 14:30 DMK 15:40 URT 891.99',
            '27.08.2015 19:10 DMK 20:20 URT 891.99'
        ];

        foreach ($results as &$row)
        {
            $row = $row->__toString();
        }
        $this->assertEquals($results, $resultCheck);
    }

    public function testGetResults()
    {
        $downloader = new Downloader();
        $airasiaParser = new AirAsia($downloader);
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
        $class = new ReflectionClass('Galmi\AirwaysBundle\Handlers\Parsers\AirAsia');
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
            ->will($this->returnValue(file_get_contents(__DIR__ . '/AirAsiaResults.html')));
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