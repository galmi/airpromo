<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 27.07.15
 * Time: 12:03
 */

namespace Galmi\AirwaysBundle\Handlers\Parsers;


use Galmi\AirwaysBundle\Handlers\Downloader;
use Symfony\Component\DomCrawler\Crawler;

class NokAir extends ParserAbstract
{

    /*
     * formData
     * 
     * {
     *  "RequestType":"NewFlight",
     *  "SegmentSellKey":"",
     *  "Criteria":{
     *      "From":"DMK",
     *      "To":"URT",
     *      "RoundTrip":"1",
     *      "Adult":1,
     *      "Child":0,
     *      "Infant":null,
     *      "Departure":"2015/08/19",
     *      "Arrival":"2015/08/19",
     *      "ProductClass":"",
     *      "FareClass":"",
     *      "BookingNo":"",
     *      "IsBulk":0,
     *      "PromotionCode":""
     *  },
     *  "Currency":"THB"
     * }
     */

    /** @var string */
    protected $uri = 'http://www.nokair.com/nokconnext/Services/AvailabilityServices.aspx?outbound=true';
    /** @var string */
    protected $sourceName = 'nokair';

    /**
     * @param Params $params
     * @return Result[]
     */
    public function getResults(Params $params)
    {
        $uri = $this->uri;
        $data = $this->getParamsData($params);
        $html = $this->downloader->submit($uri, $data);
        return $this->parseResults($html, $params);
    }

    /**
     * @param $params
     * @return string
     */
    private function getParamsData(Params $params)
    {
        $data = [
            "RequestType" => "NewFlight",
            "SegmentSellKey" => "",
            "Criteria" => [
                "From" => $params->getOrigin(),
                "To" => $params->getDestination(),
                "RoundTrip" => $params->getReturnDate() ? "1" : "0",
                "Adult" => "1",
                "Child" => "0",
                "Infant" => "0",
                "Departure" => $params->getDepartDate() ? $params->getDepartDate()->format('Y/m/d') : "",
                "Arrival" => $params->getReturnDate() ? $params->getReturnDate()->format('Y/m/d') : "",
                "ProductClass" => "",
                "FareClass" => "",
                "BookingNo" => "",
                "IsBulk" => "0",
                "PromotionCode" => ""
            ],
            "Currency" => "THB"
        ];
        return json_encode($data, JSON_UNESCAPED_SLASHES);
    }

    protected function parseResults($html, Params $params)
    {
        $results = [];
        $crawler = new Crawler($html);
        $tableDeparture = $crawler->filter('#Outbound_' . $params->getDepartDate()->format('d-m-Y'));
        $tableDeparture
            ->filter('.Text_body tr')
            ->reduce(function (Crawler $node) use (&$results, $params) {
                $row0 = $node->filter('td')->eq(0)->text();
                if (preg_match("/[0-9]{2}:[0-9]{2}/", $row0)) {
                    $result = new Result();
                    $result
                        ->setOrigin($params->getOrigin())
                        ->setDestination($params->getDestination())
                        ->setDepartureTime($node->filter('td')->eq(0)->text())
                        ->setArrivalTime($node->filter('td')->eq(1)->text())
                        ->setDate($params->getDepartDate())
                        ->setSource($this->getSourceData($params));
                    $pricePromo = filter_var($node->filter('td')->eq(6)->text(), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    if ($pricePromo) {
                        $result->setPrice($pricePromo);
                    } else {
                        $priceEco = filter_var($node->filter('td')->eq(7)->text(), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                        if ($priceEco) {
                            $result->setPrice($priceEco);
                        } else {
                            $priceFlexi = filter_var($node->filter('td')->eq(8)->text(), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                            if ($priceFlexi) {
                                $result->setPrice($priceFlexi);
                            } else {
                                return;
                            }
                        }
                    }
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