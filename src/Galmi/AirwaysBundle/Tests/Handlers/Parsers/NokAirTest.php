<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 22.07.15
 * Time: 11:47
 */

namespace Galmi\AirwaysBundle\Tests\Handlers\Parsers;

use Galmi\AirwaysBundle\Handlers\Downloader;
use Galmi\AirwaysBundle\Handlers\Parsers\NokAir;
use Galmi\AirwaysBundle\Handlers\Parsers\Params;
use Galmi\AirwaysBundle\Handlers\Parsers\Result;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NokAirTest extends WebTestCase
{
    public function testGetParamsAsDataOneWay()
    {
        $nokairParser = new NokAir($this->getDownloader());
        $getParamsString = self::getMethod('getParamsData');
        $query = $getParamsString->invokeArgs($nokairParser, [$this->createParamsOneWay()]);
        $testResult = json_encode([
            "RequestType" => "NewFlight",
            "SegmentSellKey" => "",
            "Criteria" => [
                "From" => 'BKK',
                "To" => 'URT',
                "RoundTrip" => "0",
                "Adult" => "1",
                "Child" => "0",
                "Infant" => "0",
                "Departure" => '2015/08/20',
                "Arrival" => '',
                "ProductClass" => "",
                "FareClass" => "",
                "BookingNo" => "",
                "IsBulk" => "0",
                "PromotionCode" => ""
            ],
            "Currency" => "THB"
        ], JSON_UNESCAPED_SLASHES);
        $this->assertEquals($query, $testResult);
    }

    public function testGetParamsStringReturn()
    {
        $nokairParser = new NokAir($this->getDownloader());
        $getParamsString = self::getMethod('getParamsData');
        $query = $getParamsString->invokeArgs($nokairParser, [$this->createParamsReturn()]);
        $testResult = json_encode([
            "RequestType" => "NewFlight",
            "SegmentSellKey" => "",
            "Criteria" => [
                "From" => 'BKK',
                "To" => 'URT',
                "RoundTrip" => "1",
                "Adult" => "1",
                "Child" => "0",
                "Infant" => "0",
                "Departure" => '2015/08/20',
                "Arrival" => '2015/08/30',
                "ProductClass" => "",
                "FareClass" => "",
                "BookingNo" => "",
                "IsBulk" => "0",
                "PromotionCode" => ""
            ],
            "Currency" => "THB"
        ], JSON_UNESCAPED_SLASHES);
        $this->assertEquals($query, $testResult);
    }

    public function testParseResults()
    {
        $downloader = $this->getDownloader();
        $nokairParser = new NokAir($downloader);
        $parseResults = self::getMethod('parseResults');
        /** @var Result[] $results */
        $results = $parseResults->invokeArgs($nokairParser, [$downloader->get('123'), $this->createParamsReturn()]);

        $resultCheck = [
            '20.08.2015 06:10 BKK 07:20 URT 1300.93',
            '20.08.2015 09:20 BKK 10:30 URT 1300.93',
            '20.08.2015 12:40 BKK 13:50 URT 1300.93',
            '20.08.2015 16:25 BKK 17:35 URT 591.59'
        ];

        foreach ($results as &$row) {
            $row = $row->__toString();
        }
        $this->assertEquals($results, $resultCheck);
    }

    public function testGetResults()
    {
        $downloader = new Downloader();
        $nokAirParser = new NokAir($downloader, 'http://www.nokair.com/nokconnext/Services/AvailabilityServices.aspx?outbound=true');
        $params = $this->createParamsOneWayWeek();
        $results = $nokAirParser->getResults($params);
        $this->assertGreaterThan(0, count($results));
    }

    /**
     * @param $name
     * @return \ReflectionMethod
     */
    protected static function getMethod($name)
    {
        $class = new ReflectionClass('Galmi\AirwaysBundle\Handlers\Parsers\NokAir');
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
            ->setOrigin('BKK')
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
            ->setOrigin('BKK')
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
            ->will($this->returnValue(file_get_contents(__DIR__ . '/NokAirResults.html')));
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