<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 21.07.15
 * Time: 12:12
 */

namespace Galmi\AirwaysBundle\Tests\Handlers;

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

}