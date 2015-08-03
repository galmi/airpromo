<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 29.07.15
 * Time: 0:26
 */

namespace Galmi\AirwaysBundle\Handlers\Parsers;


use Symfony\Component\DomCrawler\Crawler;

class LionAirThai extends ParserAbstract
{

    /*
     * pjourney:2
     * depCity:DMK
     * arrCity:U*RT
     * dpd1:20/08/2015
     * dpd2:30/08/2015
     * sAdult:1
     * sChild:0
     * sInfant:0
     * currency:THB
     * cTabID:35
     */
    /** @var string */
    protected $uri = 'http://search.lionairthai.com/mobile/Search/SearchFlight';
    /** @var string */
    protected $sourceName = 'lionairthai';

    /**
     * @param Params $params
     * @return Result[]
     */
    public function getResults(Params $params)
    {
        $data = $this->getParamsData($params);
        $html = $this->downloader->submit($this->uri, $data);
        return $this->parseResults($html, $params);
    }

    /**
     * @param Params $params
     * @return array
     */
    private function getParamsData(Params $params)
    {
        $data = [
            'pjourney' => 2,
            'depCity' => $params->getOrigin(),
            'arrCity' => $params->getDestination(),
            'dpd1' => $params->getDepartDate()->format('d/m/Y'),//'20/08/2015',
            'dpd2' => $params->getReturnDate() ? $params->getReturnDate()->format('d/m/Y') : '',//'30/08/2015',
            'sAdult' => 1,
            'sChild' => 0,
            'sInfant' => 0,
            'currency' => 'THB',
            'cTabID' => 35
        ];
        return $data;
    }

    /**
     * @param $html
     * @param Params $params
     * @return Params[]
     */
    protected function parseResults($html, Params $params)
    {
        $results = [];
        $crawler = new Crawler($html);
        $table = $crawler->filter('#divOBEconomy');
        $table
            ->filter('.flight_list')
            ->reduce(function (Crawler $node) use (&$results, $params) {
                $price = filter_var($node->filter('.f_price label')->text(), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                if ($price) {
                    $result = new Result();
                    $result
                        ->setPrice($price)
                        ->setOrigin(trim($node->filter('.date_time .double')->eq(0)->filter('label')->text()))
                        ->setDepartureTime($node->filter('.date_time .double')->eq(0)->filter('large')->text())
                        ->setDestination(trim($node->filter('.date_time .double')->eq(1)->filter('label')->text()))
                        ->setArrivalTime($node->filter('.date_time .double')->eq(1)->filter('large')->text())
                        ->setDate($params->getDepartDate())
                        ->setSource($this->getSourceData($params));
                    $results[] = $result;
                }
            });
        return $results;
    }

    /**
     * @param Params $params
     * @return array
     */
    protected function getSourceData(Params $params)
    {
        return [
            'uri' => $this->uri,
            'method' => 'POST',
            'data' => $this->getParamsData($params)
        ];
    }
}