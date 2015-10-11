<?php
/**
     * Created by PhpStorm.
     * User: ildar
     * Date: 22.07.15
     * Time: 11:47
     */

namespace Galmi\AirwaysBundle\Tests\Handlers\Parsers;

use Galmi\AirwaysBundle\Handlers\Parsers\ThaiSmile;
use Galmi\AirwaysBundle\Handlers\Parsers\Params;
use Galmi\AirwaysBundle\Handlers\Parsers\Result;

class ThaiSmileTest extends \PHPUnit_Framework_TestCase
{
    public function testParseResults()
    {
        $html = file_get_contents(__DIR__ . '/../Data/ThaiSmileResults.html');
        $thaismileParser = new ThaiSmile();
        /** @var Result[] $results */
        $results = $thaismileParser->parse($html, $this->createParamsReturn());

        $resultCheck = [
            '27.08.2015 07:00 DMK 08:15 URT 1490.00',
            '27.08.2015 08:10 DMK 09:25 URT 1690.00',
            '27.08.2015 12:15 DMK 13:30 URT 1490.00',
            '27.08.2015 16:45 DMK 18:00 URT 1690.00',
            '27.08.2015 19:25 DMK 20:40 URT 1690.00'
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