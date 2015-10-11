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

class LionAirThaiTest extends \PHPUnit_Framework_TestCase
{

    public function testParseResults()
    {
        $html = file_get_contents(__DIR__ . '/../Data/LionAirThaiResults.html');
        $lionAirThaiParser = new LionAirThai();
        /** @var Result[] $results */
        $results = $lionAirThaiParser->parse($html, $this->createParamsReturn());

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

}