<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 22.07.15
 * Time: 11:47
 */

namespace Galmi\AirwaysBundle\Tests\Handlers\Parsers;

use Galmi\AirwaysBundle\Handlers\Parsers\NokAir;
use Galmi\AirwaysBundle\Handlers\Parsers\Params;
use Galmi\AirwaysBundle\Handlers\Parsers\Result;

class NokAirTest extends \PHPUnit_Framework_TestCase
{
    public function testParseResults()
    {
        $html = file_get_contents(__DIR__ . '/../Data/NokAirResults.html');
        $nokairParser = new NokAir();
        /** @var Result[] $results */
        $results = $nokairParser->parse($html, $this->createParamsReturn());

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
}