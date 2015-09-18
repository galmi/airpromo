<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 22.07.15
 * Time: 11:47
 */

namespace Galmi\AirwaysBundle\Tests\Handlers\Parsers;

use Galmi\AirwaysBundle\Handlers\Parsers\AirAsia;
use Galmi\AirwaysBundle\Handlers\Parsers\Params;
use Galmi\AirwaysBundle\Handlers\Parsers\Result;

class AirAsiaTest extends \PHPUnit_Framework_TestCase
{
    public function testParseResults()
    {
        $html = file_get_contents(__DIR__ . '/../Data/AirAsiaResults.html');
        $airasiaParser = new AirAsia();
        /** @var Result[] $results */
        $results = $airasiaParser->parse($html, $this->createParamsReturn());

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

        //todo Добавить проверку source
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
}