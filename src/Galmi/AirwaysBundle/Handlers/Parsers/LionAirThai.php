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

    /**
     * @param $html
     * @param Params $params
     * @return Params[]
     */
    public function parse($html, Params $params)
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
                        ->setSourceSubmit($this->getRedirectData($params))
                        ->setSource('lionairthai');
                    $results[] = $result;
                }
            });
        return $results;
    }

    /**
     * @param Params $params
     * @return array
     */
    protected function getRedirectData(Params $params)
    {
        /**
         * https://search.lionairthai.com/default.aspx?depCity=DMK&depDate=23%2F08%2F2015&aid=207&St=fa&Jtype=1&infant1=0&currency=THB&arrCity=URT&adult1=1&child1=0
         */
        return [
            'uri' => "https://search.lionairthai.com/default.aspx",
            'method' => 'GET',
            'data' => [
                'depCity'   => $params->getOrigin(),
                'depDate'   => $params->getDepartDate()->format('d/m/Y'),
                'aid'       => 207,
                'St'        => 'fa',
                'Jtype'     => 1,
                'infant1'   => 0,
                'currency'  => 'THB',
                'arrCity'   => $params->getDestination(),
                'adult1'    => 1,
                'child1'    => 0
            ]
        ];
    }
}