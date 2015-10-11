<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 11.08.15
 * Time: 12:00
 */

namespace Galmi\AirwaysBundle\Handlers\Parsers;

use Symfony\Component\DomCrawler\Crawler;

class ThaiSmile extends ParserAbstract
{
    /**
     * @param string $html
     * @param Params $params
     * @return Result[]
     */
    public function parse($html, Params $params)
    {
        $results = [];
        $crawler = new Crawler($html);
        $table = $crawler->filter('#availabilityForm');
        $departure = $table->first();
        $departure
            ->filter('.flight-list')
            ->reduce(
                function (Crawler $node) use (&$results, $params) {
                    $price = 0;
                    $node->filter('.fare')->reduce(
                        function (Crawler $node) use (&$price) {
                            if ($price > 0) {
                                return;
                            }
                            $amount = $node->filter('input[name=Amount]');
                            if ($amount->count()) {
                                $price = $amount->first()->attr('value');
                            }
                        }
                    );

                    if ($price == 0) {
                        return;
                    }
                    $price = filter_var($price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    $price = number_format($price, 2, '.', '');
                    $result = new Result();
                    $result
                        ->setDepartureTime(trim($node->filter('.flight-detail-default .flight-time')->first()->text()))
                        ->setOrigin($params->getOrigin())
                        ->setArrivalTime(trim($node->filter('.flight-detail-default .flight-time')->last()->text()))
                        ->setDestination($params->getDestination())
                        ->setPrice($price)
                        ->setDate($params->getDepartDate())
                        ->setSourceSubmit($this->getRedirectData($params))
                        ->setSource('thaismile');

                    $results[] = $result;
                }
            );

        return $results;
    }

    /**
     * @param Params $params
     * @return array
     */
    protected function getRedirectData(Params $params)
    {
        return [
            'uri' => 'https://booking.thaismileair.com/Flight/InternalSelect',
            'method' => 'GET',
            'data' => [
                'dd1' => $params->getDepartDate()->format('Y-m-d'),
                'ADT' => 1,
                'o1' => $params->getOrigin(),
                's' => 'true',
                'd1' => $params->getDestination(),
                'mon' => 'true',
            ],
        ];
    }
}
