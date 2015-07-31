<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 21.07.15
 * Time: 12:12
 */

namespace Galmi\AirwaysBundle\Tests\Handlers;

use Galmi\AirwaysBundle\Handlers\Parsers\Params;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SearcherTest extends WebTestCase
{
    public function testService()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $container = $kernel->getContainer();
        $searcher = $container->get('galmi_airways.searcher');

        $this->assertGreaterThan(0, count($searcher->getSources()));
    }

    public function testSearcher()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $container = $kernel->getContainer();
        $searcher = $container->get('galmi_airways.searcher');
        $params = $this->createParamsOneWayWeek();

        $results = $searcher->search($params);
        $this->assertGreaterThan(0, count($results));
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