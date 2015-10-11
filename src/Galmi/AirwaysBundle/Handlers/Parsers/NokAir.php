<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 27.07.15
 * Time: 12:03
 */

namespace Galmi\AirwaysBundle\Handlers\Parsers;

use Symfony\Component\DomCrawler\Crawler;

class NokAir extends ParserAbstract
{
    /**
     * @param string $html
     * @param Params $params
     * @return array
     */
    public function parse($html, Params $params)
    {
        $results = [];
        $crawler = new Crawler($html);
        $tableDeparture = $crawler->filter('#Outbound_'.$params->getDepartDate()->format('d-m-Y'));
        $tableDeparture
            ->filter('.Text_body tr')
            ->reduce(
                function (Crawler $node) use (&$results, $params) {
                    $row0 = $node->filter('td')->eq(0)->text();
                    if (preg_match("/[0-9]{2}:[0-9]{2}/", $row0)) {
                        $result = new Result();
                        $result
                            ->setOrigin($params->getOrigin())
                            ->setDestination($params->getDestination())
                            ->setDepartureTime($node->filter('td')->eq(0)->text())
                            ->setArrivalTime($node->filter('td')->eq(1)->text())
                            ->setDate($params->getDepartDate())
                            ->setSourceSubmit($this->getRedirectData($params))
                            ->setSource('nokair');
                        $pricePromo = filter_var(
                            $node->filter('td')->eq(6)->text(),
                            FILTER_SANITIZE_NUMBER_FLOAT,
                            FILTER_FLAG_ALLOW_FRACTION
                        );
                        if ($pricePromo) {
                            $result->setPrice($pricePromo);
                        } else {
                            $priceEco = filter_var(
                                $node->filter('td')->eq(7)->text(),
                                FILTER_SANITIZE_NUMBER_FLOAT,
                                FILTER_FLAG_ALLOW_FRACTION
                            );
                            if ($priceEco) {
                                $result->setPrice($priceEco);
                            } else {
                                $priceFlexi = filter_var(
                                    $node->filter('td')->eq(8)->text(),
                                    FILTER_SANITIZE_NUMBER_FLOAT,
                                    FILTER_FLAG_ALLOW_FRACTION
                                );
                                if ($priceFlexi) {
                                    $result->setPrice($priceFlexi);
                                } else {
                                    return;
                                }
                            }
                        }
                        $results[] = $result;
                    }
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
            'uri' => 'http://www.nokair.com/nokconnext/aspx/Availability.aspx',
            'method' => 'POST',
            'data' => [
                'lstChild' => 0,
                'boardDate' => $params->getDepartDate()->format('m/d/Y'), //'08/03/2015',
                'ddlCurrency' => 'THB',
                'returnDate' => $params->getDepartDate()->format('m/d/Y'),
                'lstArrival' => $params->getDestination(),
                'lstDepartureMonth' => $params->getDepartDate()->format('m/Y'), //'08/2015',
                'lstDepartureDate' => $params->getDepartDate()->format('d'),
                'lstAdult' => 1,
                'lstDeparture' => $params->getOrigin(),
                'roundtripFlag' => '0',
            ],
        ];
    }
}
